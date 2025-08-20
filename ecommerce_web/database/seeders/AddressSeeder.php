<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\User;
use App\Models\Address;

class AddressSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách users (trừ admin)
        $users = User::where('role', 'customer')->get();
        
        $vietnameseCities = [
            'Hà Nội', 'TP Hồ Chí Minh', 'Đà Nẵng', 'Hải Phòng', 'Cần Thơ', 
            'An Giang', 'Bà Rịa - Vũng Tàu', 'Bắc Giang', 'Bắc Kạn', 'Bạc Liêu'
        ];

        $districts = [
            'Quận 1', 'Quận 2', 'Quận 3', 'Quận Thanh Xuân', 'Quận Hai Bà Trưng',
            'Quận Hoàn Kiếm', 'Quận Ba Đình', 'Quận Cầu Giấy', 'Quận Đống Đa',
            'Quận Tây Hồ', 'Quận Long Biên', 'Huyện Gia Lâm'
        ];

        $wards = [
            'Phường Láng Thượng', 'Phường Đống Đa', 'Phường Cát Linh', 
            'Phường Quang Trung', 'Phường Trung Liệt', 'Phường Thổ Quan',
            'Phường Láng Hạ', 'Phường Khâm Thiên', 'Phường Phương Liệt',
            'Phường Đại Kim', 'Phường Định Công', 'Phường Giáp Bát'
        ];

        $streetDetails = [
            '123 Nguyễn Trái', '456 Lê Lợi', '789 Trần Hưng Đạo', 
            '321 Hai Bà Trưng', '654 Lý Thường Kiệt', '987 Nguyễn Huệ',
            '147 Phan Chu Trinh', '258 Đinh Tiên Hoàng', '369 Lê Thánh Tông',
            '741 Nguyễn Du', '852 Trần Phú', '963 Hoàng Diệu'
        ];

        foreach ($users as $user) {
            // Mỗi user có 1-3 địa chỉ
            $addressCount = rand(1, 3);
            
            for ($i = 0; $i < $addressCount; $i++) {
                Address::create([
                    'user_id' => $user->id,
                    'full_name' => $user->name,
                    'phone_number' => '0' . rand(900000000, 999999999),
                    'details' => $streetDetails[array_rand($streetDetails)],
                    'ward' => $wards[array_rand($wards)],
                    'district' => $districts[array_rand($districts)],
                    'city' => $vietnameseCities[array_rand($vietnameseCities)],
                    'postal_code' => rand(100000, 999999),
                    'country' => 'Việt Nam',
                    'is_default' => $i === 0, // Địa chỉ đầu tiên là mặc định
                ]);
            }
        }
    }
}
