<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        DB::table('categories')->insert([
            ['name' => 'Laptop', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Điện thoại', 'created_at' => now(), 'updated_at' => now()],
            ['name' => 'Phụ kiện', 'created_at' => now(), 'updated_at' => now()],
        ]);
    }
}
