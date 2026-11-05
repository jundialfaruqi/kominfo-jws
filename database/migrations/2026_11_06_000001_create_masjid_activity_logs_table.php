<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        if (!Schema::hasTable('masjid_activity_logs')) {
            Schema::create('masjid_activity_logs', function (Blueprint $table) {
                $table->id();
                $table->foreignId('profil_id')
                    ->nullable()
                    ->constrained('profils')
                    ->nullOnDelete();
                $table->foreignId('user_id')
                    ->nullable()
                    ->constrained('users')
                    ->nullOnDelete();
                $table->string('masjid_name');
                $table->string('module', 50);
                $table->string('action', 30);
                $table->text('description');
                $table->timestamps();

                $table->index('created_at', 'idx_masjid_activity_logs_created_at');
                $table->index(['profil_id', 'created_at'], 'idx_masjid_activity_logs_profil_created');
                $table->index('module', 'idx_masjid_activity_logs_module');
            });
        }

        // Pastikan permission 'statistik-masjid-lihat' dan role 'Admin OPD' tersedia
        if (Schema::hasTable('permissions') && Schema::hasTable('roles')) {
            app()[PermissionRegistrar::class]->forgetCachedPermissions();

            $permData = ['name' => 'statistik-masjid-lihat', 'guard_name' => 'web'];
            $permission = Permission::firstOrCreate($permData);

            if (Schema::hasColumn('permissions', 'group') && empty($permission->group)) {
                $permission->group = 'Statistik Masjid';
                $permission->save();
            }

            // Pastikan role Admin OPD tersedia untuk klasifikasi Masjid vs OPD
            $adminOpdRole = Role::firstOrCreate([
                'name' => 'Admin OPD',
                'guard_name' => 'web',
            ]);

            // Salin permission dasar dari Admin Masjid ke Admin OPD jika Admin OPD baru dibuat dan belum punya permission
            $adminMasjidRole = Role::where('name', 'Admin Masjid')->first();
            if ($adminMasjidRole && $adminOpdRole->permissions()->count() === 0) {
                $adminOpdRole->syncPermissions($adminMasjidRole->permissions);
            }

            // Berikan akses 'statistik-masjid-lihat' ke Super Admin dan Admin
            foreach (['Super Admin', 'Admin'] as $roleName) {
                $role = Role::where('name', $roleName)->first();
                if ($role && !$role->hasPermissionTo($permission)) {
                    $role->givePermissionTo($permission);
                }
            }

            app()[PermissionRegistrar::class]->forgetCachedPermissions();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('masjid_activity_logs');
    }
};

