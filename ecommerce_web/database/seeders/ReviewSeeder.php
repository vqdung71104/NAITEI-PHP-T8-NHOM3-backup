<?php

namespace Database\Seeders;

use App\Models\Review;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ReviewSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Lấy các orders đã hoàn thành
        $completedOrders = Order::where('status', 'completed')
            ->with(['user', 'orderItems.product'])
            ->get();

        // Các comment mẫu theo rating
        $comments = [
            5 => [
                'Sách rất hay, nội dung phong phú và bổ ích!',
                'Chất lượng tuyệt vời, đóng gói cẩn thận. Rất hài lòng!',
                'Cuốn sách tuyệt vời, đáng đọc và sưu tầm.',
                'Nội dung sách hay, giá cả hợp lý. Sẽ mua thêm!',
                'Sách chất lượng cao, giao hàng nhanh. 5 sao!',
                'Rất ấn tượng với cuốn sách này, nội dung sâu sắc.',
                'Sách hay, đọc mãi không chán. Recommend!',
            ],
            4 => [
                'Sách khá hay, nội dung bổ ích nhưng hơi dài.',
                'Chất lượng tốt, giá hợp lý. Đáng mua.',
                'Nội dung hay nhưng bìa hơi bị nhăn một chút.',
                'Sách tốt, giao hàng đúng hẹn. 4 sao!',
                'Khá hài lòng với chất lượng sách.',
                'Sách hay nhưng font chữ hơi nhỏ.',
            ],
            3 => [
                'Sách bình thường, nội dung không quá đặc sắc.',
                'Chất lượng ổn, giá cả phù hợp.',
                'Nội dung tạm được, có thể đọc thử.',
                'Sách không tệ nhưng cũng không xuất sắc.',
                'Bình thường, phù hợp để đọc giải trí.',
            ],
            2 => [
                'Sách không như mong đợi, nội dung hơi khô khan.',
                'Chất lượng giấy không tốt lắm.',
                'Giao hàng chậm, sách hơi cũ.',
                'Nội dung không phù hợp với sở thích.',
            ],
            1 => [
                'Sách không hay, nội dung nhàm chán.',
                'Chất lượng kém, không đáng tiền.',
                'Giao hàng chậm, sách bị hỏng.',
                'Rất thất vọng với cuốn sách này.',
            ]
        ];

        // Tạo review cho khoảng 60% sản phẩm trong các đơn hàng completed
        foreach ($completedOrders as $order) {
            foreach ($order->orderItems as $orderItem) {
                // Chỉ tạo review cho 60% sản phẩm
                if (rand(1, 100) <= 60) {
                    $rating = $this->generateWeightedRating();
                    $comment = $comments[$rating][array_rand($comments[$rating])];
                    
                    // Random thời gian review (sau ngày order 1-30 ngày)
                    $reviewDate = $order->created_at->addDays(rand(1, 30));
                    
                    // Kiểm tra xem đã có review cho sản phẩm này từ user này chưa
                    $existingReview = Review::where('user_id', $order->user_id)
                        ->where('product_id', $orderItem->product_id)
                        ->first();
                    
                    if (!$existingReview) {
                        Review::create([
                            'user_id' => $order->user_id,
                            'product_id' => $orderItem->product_id,
                            'rating' => $rating,
                            'content' => $comment,
                            'image_url' => null, // Có thể thêm ảnh sau
                            'created_at' => $reviewDate,
                            'updated_at' => $reviewDate,
                        ]);
                    }
                }
            }
        }

        // Tạo thêm một số review random cho các sản phẩm khác
        $users = User::where('role', 'customer')->get();
        $products = Product::all();
        
        for ($i = 0; $i < 50; $i++) {
            $user = $users->random();
            $product = $products->random();
            
            // Kiểm tra trùng lặp
            $existingReview = Review::where('user_id', $user->id)
                ->where('product_id', $product->id)
                ->first();
            
            if (!$existingReview) {
                $rating = $this->generateWeightedRating();
                $comment = $comments[$rating][array_rand($comments[$rating])];
                
                Review::create([
                    'user_id' => $user->id,
                    'product_id' => $product->id,
                    'rating' => $rating,
                    'content' => $comment,
                    'image_url' => null,
                    'created_at' => Carbon::now()->subDays(rand(1, 90)),
                    'updated_at' => Carbon::now()->subDays(rand(1, 90)),
                ]);
            }
        }
    }

    /**
     * Generate weighted rating (higher ratings more likely)
     */
    private function generateWeightedRating(): int
    {
        $weights = [
            1 => 5,   // 5%
            2 => 10,  // 10% 
            3 => 20,  // 20%
            4 => 35,  // 35%
            5 => 30   // 30%
        ];
        
        $random = rand(1, 100);
        $cumulative = 0;
        
        foreach ($weights as $rating => $weight) {
            $cumulative += $weight;
            if ($random <= $cumulative) {
                return $rating;
            }
        }
        
        return 5; // fallback
    }
}
