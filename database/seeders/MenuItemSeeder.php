<?php

namespace Database\Seeders;

use App\Models\MenuItem;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MenuItemSeeder extends Seeder
{
    public function run(): void
    {
        // Disable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        // Clear existing menu items
        MenuItem::truncate();

        // Add menu items
        $menuItems = [
            [
                'name' => 'Aloo Paratha',
                'description' => 'Classic potato stuffed paratha with Indian spices',
                'price' => 50.00,
                'is_available' => true,
                'image_url' => '/images/aloo-paratha.jpg'
            ],
            [
                'name' => 'Paneer Paratha',
                'description' => 'Fresh cottage cheese stuffed paratha with herbs',
                'price' => 70.00,
                'is_available' => true,
                'image_url' => '/images/paneer-paratha.jpg'
            ],
            [
                'name' => 'Gobi Paratha',
                'description' => 'Spiced cauliflower stuffed paratha',
                'price' => 55.00,
                'is_available' => true,
                'image_url' => '/images/gobi-paratha.jpg'
            ],
            [
                'name' => 'Mixed Veg Paratha',
                'description' => 'Mixed vegetables stuffed paratha',
                'price' => 60.00,
                'is_available' => true,
                'image_url' => '/images/mixed-veg-paratha.jpg'
            ],
            [
                'name' => 'Plain Paratha',
                'description' => 'Simple layered paratha with butter',
                'price' => 40.00,
                'is_available' => true,
                'image_url' => '/images/plain-paratha.jpg'
            ],
            [
                'name' => 'Methi Paratha',
                'description' => 'Fenugreek leaves stuffed paratha',
                'price' => 55.00,
                'is_available' => true,
                'image_url' => '/images/methi-paratha.jpg'
            ]
        ];

        foreach ($menuItems as $item) {
            MenuItem::create($item);
        }

        // Re-enable foreign key checks
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
