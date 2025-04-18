<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // User data
        $user = [
            'id' => (string) Str::uuid(),
            'name' => 'super admin',
            'email' => 'admin@admin.com',
            'password' => Hash::make('admin123'),
            'created_at' => now(),
            'updated_at' => now(),
        ];
        $user2 = [
            'id' => (string) Str::uuid(),
            'name' => 'john doe',
            'email' => 'johndoe@mail.com',
            'password' => Hash::make('johndoe123'),
            'created_at' => now(),
            'updated_at' => now(),
        ];

        // Clear existing users
        if (User::count()) {
            User::truncate();
        }

        // Insert the users
        User::insert($user);
        User::insert($user2);

        // Assign roles and permissions with the correct guard
        $superAdmin = User::where('email', 'admin@admin.com')->first();
        $userAccount = User::where('email', 'johndoe@mail.com')->first();

        // Ensure roles and permissions use the sanctum guard
        $adminRole = Role::findByName('admin', 'sanctum'); // Retrieve role with sanctum guard
        $superAdmin->assignRole($adminRole); // Assign role

        $userRole = Role::findByName('user', 'sanctum'); // Retrieve role with sanctum guard
        $userAccount->assignRole($userRole); // Assign role

    }
}
