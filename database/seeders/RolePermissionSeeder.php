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
            'view-profiles',
            'create-profiles',
            'edit-profiles',
            'delete-profiles',

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
            'view-profiles',
            'create-profiles',
            'edit-profiles',
            'delete-profiles',
            'view-jumbotron',
            'create-jumbotron',
            'edit-jumbotron',
            'delete-jumbotron',
        ]);

        // User - basic permissions for their own content
        $userRole = Role::create(['name' => 'User']);
        $userRole->givePermissionTo([
            'view-dashboard',
        ]);

        // Admin Masjid - similar to User but with more content management permissions
        $adminMasjidRole = Role::create(['name' => 'Admin Masjid']);
        $adminMasjidRole->givePermissionTo([
            'view-jumbotron',
            'create-jumbotron',
            'edit-jumbotron',
            'delete-jumbotron',
        ]);

        $this->command->info('Roles and permissions created successfully!');
    }
}
