<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;

class AddressSeeder extends Seeder
{
    public function run(): void
    {
        // Lấy user Test User
        $user = User::where('email', 'test@example.com')->first();

        if ($user) {
            DB::table('addresses')->insert([
                [
                    'user_id' => $user->id,
                    'full_name' => $user->name,
                    'phone_number' => '0901234567',
                    'details' => 'Căn hộ 101, Tòa A',
                    'ward' => 'Phường Trung Hòa',
                    'district' => 'Cầu Giấy',
                    'city' => 'Hà Nội',
                    'postal_code' => '100000',
                    'country' => 'Vietnam',
                    'is_default' => true,
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'user_id' => $user->id,
                    'full_name' => $user->name,
                    'phone_number' => '0912345678',
                    'details' => 'Nhà số 20, Ngõ 50',
                    'ward' => 'Phường Tân Phong',
                    'district' => 'Quận 7',
                    'city' => 'TP. Hồ Chí Minh',
                    'postal_code' => '700000',
                    'country' => 'Vietnam',
                    'is_default' => false,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            ]);
        }
    }
}
