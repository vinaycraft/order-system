<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create admin user
        User::create([
            'name' => 'Admin User',
            'mobile' => '7263060086',
            'password' => Hash::make('password123'),
            'role' => 'admin'
        ]);

        // Create manager user
        User::create([
            'name' => 'Manager',
            'mobile' => '9876543211',
            'password' => Hash::make('manager123'),
            'role' => 'manager'
        ]);

        // Create customer user
        User::create([
            'name' => 'Customer',
            'mobile' => '9876543210',
            'password' => Hash::make('customer123'),
            'role' => 'customer'
        ]);

        // Run menu items seeder
        $this->call([
            MenuItemSeeder::class
        ]);
    }
}
