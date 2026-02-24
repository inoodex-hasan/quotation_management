<?php

namespace Database\Seeders;

use App\Models\Admin\Category;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class CategoriesSeeder extends Seeder
{
    public function run()
    {
        $categories = [
            'Combo',
            'Pizza',
            'Snacks',
            'Desserts',
            'Coffee',
            'Drinks',
            'Sauces',
            'Other goods',
        ];

        foreach ($categories as $category) {
            Category::create(['name' => $category]);
        }
    }
}
