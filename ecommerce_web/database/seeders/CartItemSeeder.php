<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\CartItem;
use App\Models\User;
use App\Models\Product;

class CartItemSeeder extends Seeder
{
    public function run()
    {
        // Lấy 1 user bất kỳ (hoặc tạo user demo)
        $user = User::first(); 

        // Lấy 2 sản phẩm bất kỳ
        $products = Product::take(2)->get();

        if ($user && $products->count() > 0) {
            foreach ($products as $product) {
                CartItem::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'quantity' => rand(1, 3), // số lượng ngẫu nhiên 1-3
                ]);
            }
        }
    }
}
