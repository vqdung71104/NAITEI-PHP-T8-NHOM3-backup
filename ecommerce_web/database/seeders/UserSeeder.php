<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Tạo admin user
        User::create([
            'name' => 'Admin',
            'email' => 'admin@bookstore.com',
            'password' => Hash::make('123456789'),
            'role' => 'admin',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'user',
            'email' => 'user@example.com',
            'password' => Hash::make('123456789'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        // Tạo một số customer users
        User::create([
            'name' => 'Lê Trọng Khánh',
            'email' => 'khanh@example.com',
            'password' => Hash::make('123456789'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Do Minh Tam',
            'email' => 'tam@example.com',
            'password' => Hash::make('123456789'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Vu Quang Dung',
            'email' => 'dung@example.com',
            'password' => Hash::make('123456789'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        User::create([
            'name' => 'Nguyen Khanh Toan',
            'email' => 'toan@example.com',
            'password' => Hash::make('123456789'),
            'role' => 'customer',
            'email_verified_at' => now(),
        ]);

        // Tạo thêm random customers sử dụng factory
        User::factory(20)->create([
            'role' => 'customer'
        ]);
    }
}
