<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\MenuItem;
use Illuminate\Database\Seeder;

class DefaultMenuItemsSeeder extends Seeder
{
    public function run(): void
    {
        // Create default category
        $category = Category::firstOrCreate(
            ['name' => 'Parathas'],
            [
                'description' => 'Delicious stuffed Indian flatbreads',
                'is_active' => true
            ]
        );

        // Clear existing menu items
        MenuItem::where('category_id', $category->id)->delete();

        // Add menu items
        $menuItems = [
            [
                'name' => 'Methi Paratha',
                'description' => 'Fresh fenugreek leaves stuffed paratha with aromatic spices',
                'price' => 60.00,
                'category_id' => $category->id,
                'is_available' => true,
                'image_url' => '/images/methi-paratha.jpg'
            ],
            [
                'name' => 'Kobbi Paratha',
                'description' => 'Minced meat stuffed paratha with special blend of spices',
                'price' => 80.00,
                'category_id' => $category->id,
                'is_available' => true,
                'image_url' => '/images/kobbi-paratha.jpg'
            ],
            [
                'name' => 'Aalu Paratha',
                'description' => 'Classic potato stuffed paratha with Indian spices',
                'price' => 50.00,
                'category_id' => $category->id,
                'is_available' => true,
                'image_url' => '/images/aalu-paratha.jpg'
            ],
            [
                'name' => 'Mix Paratha',
                'description' => 'Mixed vegetables stuffed paratha with blend of spices',
                'price' => 70.00,
                'category_id' => $category->id,
                'is_available' => true,
                'image_url' => '/images/mix-paratha.jpg'
            ]
        ];

        foreach ($menuItems as $item) {
            MenuItem::create($item);
        }
    }
}
