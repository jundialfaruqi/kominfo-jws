<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Drop existing triggers that attempt to update the same table (laporans)
        DB::unprepared('DROP TRIGGER IF EXISTS trg_laporans_ai_balance');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_laporans_au_balance');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_laporans_ad_balance');

        // Recreate simplified triggers that ONLY maintain tb_balance and DO NOT update laporans

        // AFTER INSERT: maintain tb_balance aggregates
        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_laporans_ai_balance
            AFTER INSERT ON laporans
            FOR EACH ROW
            BEGIN
                DECLARE delta DECIMAL(15,2);
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
            END
        SQL);

        // AFTER UPDATE: adjust tb_balance aggregates for old and new values/groups
        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_laporans_au_balance
            AFTER UPDATE ON laporans
            FOR EACH ROW
            BEGIN
                DECLARE oldDelta DECIMAL(15,2);
                DECLARE newDelta DECIMAL(15,2);
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
                ELSE
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
                END IF;
            END
        SQL);

        // AFTER DELETE: subtract from tb_balance aggregates
        DB::unprepared(<<<SQL
            CREATE TRIGGER trg_laporans_ad_balance
            AFTER DELETE ON laporans
            FOR EACH ROW
            BEGIN
                DECLARE oldDelta DECIMAL(15,2);
                SET oldDelta = CASE WHEN OLD.is_opening = 1 OR OLD.jenis = 'masuk' THEN OLD.saldo ELSE -OLD.saldo END;

                UPDATE tb_balance SET
                    total_masuk = total_masuk - CASE WHEN OLD.is_opening = 1 OR OLD.jenis = 'masuk' THEN OLD.saldo ELSE 0 END,
                    total_keluar = total_keluar - CASE WHEN OLD.jenis = 'keluar' THEN OLD.saldo ELSE 0 END,
                    ending_balance = ending_balance - oldDelta,
                    updated_at = NOW()
                WHERE id_masjid = OLD.id_masjid AND id_group_category = OLD.id_group_category;
            END
        SQL);
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        DB::unprepared('DROP TRIGGER IF EXISTS trg_laporans_ai_balance');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_laporans_au_balance');
        DB::unprepared('DROP TRIGGER IF EXISTS trg_laporans_ad_balance');
    }
};