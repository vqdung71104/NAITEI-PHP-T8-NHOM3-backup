<?php

namespace Database\Seeders;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use App\Models\Product;
use App\Models\Address;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class OrderSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy danh sách users (customer) và products
        $users = User::where('role', 'customer')->with('addresses')->get();
        $products = Product::all();
        $statuses = Order::getStatuses();

        // Tạo 100 orders trong vòng 6 tháng 
        for ($i = 0; $i < 100; $i++) {
            $user = $users->random();
            $userAddress = $user->addresses->random();
            
            // Random thời gian trong 6 tháng 
            $orderDate = Carbon::now()->subDays(rand(1, 180));
            
            // Tạo order
            $order = Order::create([
                'user_id' => $user->id,
                'address_id' => $userAddress->id,
                'status' => $statuses[array_rand($statuses)],
                'total_price' => 0, 
                'created_at' => $orderDate,
                'updated_at' => $orderDate,
            ]);

            // Tạo 1-5 order items cho mỗi order
            $itemCount = rand(1, 5);
            $totalPrice = 0;
            $selectedProducts = $products->random($itemCount);

            foreach ($selectedProducts as $product) {
                $quantity = rand(1, 3);
                $price = $product->price * $quantity;
                $totalPrice += $price;

                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'quantity' => $quantity,
                    'created_at' => $orderDate,
                    'updated_at' => $orderDate,
                ]);
            }

            // Cập nhật total_price cho order
            $order->update(['total_price' => $totalPrice]);
        }
    }
}
