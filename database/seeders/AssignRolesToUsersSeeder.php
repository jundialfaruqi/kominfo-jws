<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Spatie\Permission\Models\Role;

class AssignRolesToUsersSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all users
        $users = User::all();

        foreach ($users as $user) {
            // Assign Spatie role based on legacy role field
            switch ($user->role) {
                case 'Super Admin':
                    $role = Role::where('name', 'Super Admin')->first();
                    if ($role) {
                        $user->assignRole($role);
                        $this->command->info("Assigned 'Super Admin' role to user: {$user->name}");
                    }
                    break;

                case 'Admin':
                    $role = Role::where('name', 'Admin')->first();
                    if ($role) {
                        $user->assignRole($role);
                        $this->command->info("Assigned 'Admin' role to user: {$user->name}");
                    }
                    break;

                case 'User':
                    $role = Role::where('name', 'User')->first();
                    if ($role) {
                        $user->assignRole($role);
                        $this->command->info("Assigned 'User' role to user: {$user->name}");
                    }
                    break;

                case 'Admin Masjid':
                    $role = Role::where('name', 'Admin Masjid')->first();
                    if ($role) {
                        $user->assignRole($role);
                        $this->command->info("Assigned 'Admin Masjid' role to user: {$user->name}");
                    }
                    break;

                default:
                    // If no matching role, assign User role as default
                    $role = Role::where('name', 'User')->first();
                    if ($role) {
                        $user->assignRole($role);
                        $this->command->info("Assigned default 'User' role to user: {$user->name}");
                    }
                    break;
            }
        }

        $this->command->info('All users have been assigned Spatie roles based on their legacy roles!');
    }
}
