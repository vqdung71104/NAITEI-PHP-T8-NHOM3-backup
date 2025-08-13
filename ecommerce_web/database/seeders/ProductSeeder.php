<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        DB::table('products')->insert([
            [
                'name' => 'Laptop Dell Inspiron 15',
                'description' => 'Laptop hiệu năng tốt, màn hình 15.6 inch Full HD.',
                'price' => 15000000,
                'stock' => 10,
                'category_id' => 1, 
                'image_url' => 'https://bizweb.dktcdn.net/thumb/1024x1024/100/306/444/products/dell-latitude-7490-dellpc-7.jpg?v=1711344583853',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Điện thoại iPhone 14 Pro',
                'description' => 'Smartphone cao cấp, camera 48MP, chip A16 Bionic.',
                'price' => 28000000,
                'stock' => 5,
                'category_id' => 2,
                'image_url' => 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcTiaVAgOULRyNJSGmeca3gkNTFBRFgdTD4JlA&s',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Tai nghe Bluetooth Sony WH-1000XM5',
                'description' => 'Tai nghe chống ồn hàng đầu, pin lên đến 30 giờ.',
                'price' => 8500000,
                'stock' => 15,
                'category_id' => 3,
                'image_url' => 'https://www.sony.com.vn/image/6145c1d32e6ac8e63a46c912dc33c5bb?fmt=pjpeg&wid=220&bgcolor=FFFFFF&bgc=FFFFFF',
                'created_at' => now(),
                'updated_at' => now(),
            ]
        ]);
    }
}
