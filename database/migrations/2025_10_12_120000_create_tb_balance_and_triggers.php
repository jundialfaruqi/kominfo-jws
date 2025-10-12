<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Add running_balance column to laporans
        Schema::table('laporans', function (Blueprint $table) {
            if (!Schema::hasColumn('laporans', 'running_balance')) {
                $table->bigInteger('running_balance')->default(0)->after('is_opening');
            }
        });

        // Create tb_balance table (aggregated totals per masjid and category)
        Schema::create('tb_balance', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('id_masjid');
            $table->unsignedBigInteger('id_group_category');
            $table->bigInteger('total_masuk')->default(0);
            $table->bigInteger('total_keluar')->default(0);
            $table->bigInteger('ending_balance')->default(0);
            $table->timestamps();

            $table->unique(['id_masjid', 'id_group_category'], 'uniq_tb_balance_masjid_cat');
        });

        // Initialize tb_balance from existing laporans data
        DB::unprepared(<<<SQL
            INSERT INTO tb_balance (id_masjid, id_group_category, total_masuk, total_keluar, ending_balance, created_at, updated_at)
            SELECT
                l.id_masjid,
                l.id_group_category,
                SUM(CASE WHEN l.is_opening = 1 OR l.jenis = 'masuk' THEN l.saldo ELSE 0 END) AS total_masuk,
                SUM(CASE WHEN l.jenis = 'keluar' THEN l.saldo ELSE 0 END) AS total_keluar,
                SUM(CASE WHEN l.is_opening = 1 OR l.jenis = 'masuk' THEN l.saldo ELSE -l.saldo END) AS ending_balance,
                NOW(), NOW()
            FROM laporans l
            GROUP BY l.id_masjid, l.id_group_category
        SQL);

        // Initialize running_balance using window function (MySQL 8+)
        DB::unprepared(<<<SQL
            UPDATE laporans AS t
            JOIN (
                SELECT
                    id,
                    SUM(CASE WHEN is_opening = 1 OR jenis = 'masuk' THEN saldo ELSE -saldo END)
                        OVER (PARTITION BY id_masjid, id_group_category ORDER BY tanggal, id ROWS BETWEEN UNBOUNDED PRECEDING AND CURRENT ROW) AS rb
                FROM laporans
            ) AS s ON s.id = t.id
            SET t.running_balance = CAST(s.rb AS SIGNED)
        SQL);

        // Trigger: AFTER INSERT on laporans
        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_laporans_ai_balance
            AFTER INSERT ON laporans
            FOR EACH ROW
            BEGIN
                DECLARE delta BIGINT;
                DECLARE prevSum BIGINT;

                SET delta = CASE WHEN NEW.is_opening = 1 OR NEW.jenis = 'masuk' THEN NEW.saldo ELSE -NEW.saldo END;

                INSERT INTO tb_balance (id_masjid, id_group_category, total_masuk, total_keluar, ending_balance, created_at, updated_at)
                VALUES (
                    NEW.id_masjid,
                    NEW.id_group_category,
                    CASE WHEN NEW.is_opening = 1 OR NEW.jenis = 'masuk' THEN NEW.saldo ELSE 0 END,
                    CASE WHEN NEW.jenis = 'keluar' THEN NEW.saldo ELSE 0 END,
                    delta,
                    NOW(), NOW()
                )
                ON DUPLICATE KEY UPDATE
                    total_masuk = total_masuk + CASE WHEN NEW.is_opening = 1 OR NEW.jenis = 'masuk' THEN NEW.saldo ELSE 0 END,
                    total_keluar = total_keluar + CASE WHEN NEW.jenis = 'keluar' THEN NEW.saldo ELSE 0 END,
                    ending_balance = ending_balance + delta,
                    updated_at = NOW();

                SELECT COALESCE(SUM(CASE WHEN is_opening = 1 OR jenis = 'masuk' THEN saldo ELSE -saldo END), 0) INTO prevSum
                FROM laporans
                WHERE id_masjid = NEW.id_masjid
                  AND id_group_category = NEW.id_group_category
                  AND (tanggal < NEW.tanggal OR (tanggal = NEW.tanggal AND id < NEW.id));

                UPDATE laporans SET running_balance = prevSum + delta WHERE id = NEW.id;

                UPDATE laporans
                SET running_balance = running_balance + delta
                WHERE id_masjid = NEW.id_masjid
                  AND id_group_category = NEW.id_group_category
                  AND (tanggal > NEW.tanggal OR (tanggal = NEW.tanggal AND id > NEW.id));
            END
        SQL);

        // Trigger: AFTER UPDATE on laporans
        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_laporans_au_balance
            AFTER UPDATE ON laporans
            FOR EACH ROW
            BEGIN
                DECLARE oldDelta BIGINT;
                DECLARE newDelta BIGINT;
                DECLARE prevSum BIGINT;

                SET oldDelta = CASE WHEN OLD.is_opening = 1 OR OLD.jenis = 'masuk' THEN OLD.saldo ELSE -OLD.saldo END;
                SET newDelta = CASE WHEN NEW.is_opening = 1 OR NEW.jenis = 'masuk' THEN NEW.saldo ELSE -NEW.saldo END;

                IF OLD.id_masjid = NEW.id_masjid AND OLD.id_group_category = NEW.id_group_category THEN
                    UPDATE tb_balance SET
                        total_masuk = total_masuk - CASE WHEN OLD.is_opening = 1 OR OLD.jenis = 'masuk' THEN OLD.saldo ELSE 0 END
                                                        + CASE WHEN NEW.is_opening = 1 OR NEW.jenis = 'masuk' THEN NEW.saldo ELSE 0 END,
                        total_keluar = total_keluar - CASE WHEN OLD.jenis = 'keluar' THEN OLD.saldo ELSE 0 END
                                                        + CASE WHEN NEW.jenis = 'keluar' THEN NEW.saldo ELSE 0 END,
                        ending_balance = ending_balance - oldDelta + newDelta,
                        updated_at = NOW()
                    WHERE id_masjid = NEW.id_masjid AND id_group_category = NEW.id_group_category;

                    -- Remove oldDelta effect from rows after OLD position
                    UPDATE laporans
                    SET running_balance = running_balance - oldDelta
                    WHERE id_masjid = OLD.id_masjid
                      AND id_group_category = OLD.id_group_category
                      AND (tanggal > OLD.tanggal OR (tanggal = OLD.tanggal AND id > OLD.id));

                    -- Recalculate running_balance for NEW row at new position
                    SELECT COALESCE(SUM(CASE WHEN is_opening = 1 OR jenis = 'masuk' THEN saldo ELSE -saldo END), 0) INTO prevSum
                    FROM laporans
                    WHERE id_masjid = NEW.id_masjid
                      AND id_group_category = NEW.id_group_category
                      AND (tanggal < NEW.tanggal OR (tanggal = NEW.tanggal AND id < NEW.id));

                    UPDATE laporans SET running_balance = prevSum + newDelta WHERE id = NEW.id;

                    -- Add newDelta effect to rows after NEW position
                    UPDATE laporans
                    SET running_balance = running_balance + newDelta
                    WHERE id_masjid = NEW.id_masjid
                      AND id_group_category = NEW.id_group_category
                      AND (tanggal > NEW.tanggal OR (tanggal = NEW.tanggal AND id > NEW.id));
                ELSE
                    -- Moved to different masjid/category
                    UPDATE tb_balance SET
                        total_masuk = total_masuk - CASE WHEN OLD.is_opening = 1 OR OLD.jenis = 'masuk' THEN OLD.saldo ELSE 0 END,
                        total_keluar = total_keluar - CASE WHEN OLD.jenis = 'keluar' THEN OLD.saldo ELSE 0 END,
                        ending_balance = ending_balance - oldDelta,
                        updated_at = NOW()
                    WHERE id_masjid = OLD.id_masjid AND id_group_category = OLD.id_group_category;

                    INSERT INTO tb_balance (id_masjid, id_group_category, total_masuk, total_keluar, ending_balance, created_at, updated_at)
                    VALUES (
                        NEW.id_masjid,
                        NEW.id_group_category,
                        CASE WHEN NEW.is_opening = 1 OR NEW.jenis = 'masuk' THEN NEW.saldo ELSE 0 END,
                        CASE WHEN NEW.jenis = 'keluar' THEN NEW.saldo ELSE 0 END,
                        newDelta,
                        NOW(), NOW()
                    )
                    ON DUPLICATE KEY UPDATE
                        total_masuk = total_masuk + CASE WHEN NEW.is_opening = 1 OR NEW.jenis = 'masuk' THEN NEW.saldo ELSE 0 END,
                        total_keluar = total_keluar + CASE WHEN NEW.jenis = 'keluar' THEN NEW.saldo ELSE 0 END,
                        ending_balance = ending_balance + newDelta,
                        updated_at = NOW();

                    -- Remove oldDelta effect from rows after OLD position in old group
                    UPDATE laporans
                    SET running_balance = running_balance - oldDelta
                    WHERE id_masjid = OLD.id_masjid
                      AND id_group_category = OLD.id_group_category
                      AND (tanggal > OLD.tanggal OR (tanggal = OLD.tanggal AND id > OLD.id));

                    -- Set running_balance for NEW row based on its new group position
                    SELECT COALESCE(SUM(CASE WHEN is_opening = 1 OR jenis = 'masuk' THEN saldo ELSE -saldo END), 0) INTO prevSum
                    FROM laporans
                    WHERE id_masjid = NEW.id_masjid
                      AND id_group_category = NEW.id_group_category
                      AND (tanggal < NEW.tanggal OR (tanggal = NEW.tanggal AND id < NEW.id));

                    UPDATE laporans SET running_balance = prevSum + newDelta WHERE id = NEW.id;

                    -- Add newDelta effect to rows after NEW position in new group
                    UPDATE laporans
                    SET running_balance = running_balance + newDelta
                    WHERE id_masjid = NEW.id_masjid
                      AND id_group_category = NEW.id_group_category
                      AND (tanggal > NEW.tanggal OR (tanggal = NEW.tanggal AND id > NEW.id));
                END IF;
            END
        SQL);

        // Trigger: AFTER DELETE on laporans
        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_laporans_ad_balance
            AFTER DELETE ON laporans
            FOR EACH ROW
            BEGIN
                DECLARE oldDelta BIGINT;
                SET oldDelta = CASE WHEN OLD.is_opening = 1 OR OLD.jenis = 'masuk' THEN OLD.saldo ELSE -OLD.saldo END;

                UPDATE tb_balance SET
                    total_masuk = total_masuk - CASE WHEN OLD.is_opening = 1 OR OLD.jenis = 'masuk' THEN OLD.saldo ELSE 0 END,
                    total_keluar = total_keluar - CASE WHEN OLD.jenis = 'keluar' THEN OLD.saldo ELSE 0 END,
                    ending_balance = ending_balance - oldDelta,
                    updated_at = NOW()
                WHERE id_masjid = OLD.id_masjid AND id_group_category = OLD.id_group_category;

                UPDATE laporans
                SET running_balance = running_balance - oldDelta
                WHERE id_masjid = OLD.id_masjid
                  AND id_group_category = OLD.id_group_category
                  AND (tanggal > OLD.tanggal OR (tanggal = OLD.tanggal AND id > OLD.id));
            END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        // Drop triggers
        DB::unprepared('DROP TRIGGER IF EXISTS trg_laporans_ai_balance');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_laporans_au_balance');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_laporans_ad_balance');

        // Drop tb_balance table
        Schema::dropIfExists('tb_balance');

        // Drop running_balance column
        Schema::table('laporans', function (Blueprint $table) {
            if (Schema::hasColumn('laporans', 'running_balance')) {
                $table->dropColumn('running_balance');
            }
        });
    }
};