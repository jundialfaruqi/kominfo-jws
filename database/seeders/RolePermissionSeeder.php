<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create permissions
        $permissions = [
            'view-dashboard',

            // User Management
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',

            // Role Management
            'view-roles',
            'create-roles',
            'edit-roles',
            'delete-roles',

            // Permission Management
            'view-permissions',
            'create-permissions',
            'edit-permissions',
            'delete-permissions',

            // Profile Management
            'view-profil-masjid',
            'create-profil-masjid',
            'edit-profil-masjid',
            'delete-profil-masjid',

            // Jumbotron Management
            'view-jumbotron',
            'create-jumbotron',
            'edit-jumbotron',
            'delete-jumbotron',

            // User Role Assignment Management
            'view-user-role-assignment',
            'create-user-role-assignment',
            'edit-user-role-assignment',
            'delete-user-role-assignment',

            // Group Category
            'view-group-category',
            'create-group-category',
            'edit-group-category',
            'delete-group-category',

            // Keuangan
            'view-laporan-keuangan',
            'create-laporan-keuangan',
            'edit-laporan-keuangan',
            'delete-laporan-keuangan',

        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Create roles and assign permissions

        // Super Admin - has all permissions
        $superAdminRole = Role::create(['name' => 'Super Admin']);
        $superAdminRole->givePermissionTo(Permission::all());

        // Admin - has most permissions except user management of super admins
        $adminRole = Role::create(['name' => 'Admin']);
        $adminRole->givePermissionTo([
            'view-dashboard',
            'view-users',
            'create-users',
            'edit-users',
            'delete-users',
            'view-profil-masjid',
            'create-profil-masjid',
            'edit-profil-masjid',
            'delete-profil-masjid',
            'view-jumbotron',
            'create-jumbotron',
            'edit-jumbotron',
            'delete-jumbotron',
            'view-group-category',
            'create-group-category',
            'edit-group-category',
            'delete-group-category',
            'view-laporan-keuangan',
            'create-laporan-keuangan',
            'edit-laporan-keuangan',
            'delete-laporan-keuangan',
        ]);

        // Admin Masjid - basic permissions for their own content
        $userRole = Role::create(['name' => 'Admin Masjid']);
        $userRole->givePermissionTo([
            'view-dashboard',
            'view-group-category',
            'create-group-category',
            'edit-group-category',
            'delete-group-category',
            'view-laporan-keuangan',
            'create-laporan-keuangan',
            'edit-laporan-keuangan',
            'delete-laporan-keuangan',
        ]);

        // Admin Masjid - similar to User but with more content management permissions
        $adminMasjidRole = Role::create(['name' => 'Admin Jumbotron']);
        $adminMasjidRole->givePermissionTo([
            'view-jumbotron',
            'create-jumbotron',
            'edit-jumbotron',
            'delete-jumbotron',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
