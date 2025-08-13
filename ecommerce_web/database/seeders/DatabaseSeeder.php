<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Chạy các seeder theo thứ tự
        $this->call([
            UserSeeder::class,
            CategorySeeder::class,
            ProductSeeder::class,
        ]);

        // Tạo products sau khi đã có categories
        // Lấy tất cả category IDs
        $categoryIds = Category::pluck('id')->toArray();

        // Tạo 50 sản phẩm ngẫu nhiên
        Product::factory(50)->create([
            'category_id' => fake()->randomElement($categoryIds)
        ]);

        // Tạo một số sản phẩm hết hàng
        Product::factory(10)->outOfStock()->create([
            'category_id' => fake()->randomElement($categoryIds)
        ]);

        // Tạo sản phẩm cho từng category cụ thể
        foreach ($categoryIds as $categoryId) {
            Product::factory(5)->forCategory($categoryId)->create();
        }
    }
}
