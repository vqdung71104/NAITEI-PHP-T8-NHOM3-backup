<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $bookPrefixes = [
            'Tuyển tập',
            'Cẩm nang',
            'Hướng dẫn',
            'Bí quyết',
            'Nghệ thuật',
            'Khám phá',
            'Lịch sử',
            'Cuộc đời',
            'Triết lý',
            'Phương pháp',
            'Kỹ năng',
            'Bí mật',
            'Thế giới',
            'Con đường',
            'Hành trình',
            'Câu chuyện',
            'Truyện',
            'Tiểu thuyết',
            'Tự truyện',
            'Hồi ký'
        ];

        $bookTopics = [
            'thành công',
            'hạnh phúc',
            'tình yêu',
            'cuộc sống',
            'kinh doanh',
            'lãnh đạo',
            'sáng tạo',
            'học tập',
            'phát triển bản thân',
            'tâm lý học',
            'triết học',
            'lịch sử',
            'khoa học',
            'công nghệ',
            'nghệ thuật',
            'âm nhạc',
            'văn học',
            'thơ ca',
            'truyền thống',
            'hiện đại',
            'tương lai',
            'gia đình',
            'trẻ em',
            'tuổi trẻ',
            'người già',
            'phụ nữ',
            'đàn ông',
            'xã hội',
            'chính trị',
            'kinh tế',
            'giáo dục',
            'y học',
            'sức khỏe',
            'thể thao',
            'du lịch',
            'ẩm thực',
            'nấu ăn',
            'làm đẹp',
            'thời trang'
        ];

        // Tạo tên sách độc đáo
        $bookName = fake()->randomElement($bookPrefixes) . ' ' . 
                   fake()->randomElement($bookTopics) . 
                   (fake()->boolean(30) ? ' - Tập ' . fake()->numberBetween(1, 5) : '') .
                   (fake()->boolean(20) ? ' (' . fake()->year(1990, 2024) . ')' : '');

        return [
            'name' => trim($bookName),
            'description' => fake()->paragraph(3),
            'price' => fake()->randomFloat(0, 50000, 500000),
            'category_id' => Category::factory(),
            'stock' => fake()->numberBetween(0, 100),
            'image_url' => fake()->imageUrl(400, 600, 'books', true, 'book'),
            'author' => fake()->name(),
        ];
    }

    /**
     * Indicate that the product is out of stock.
     */
    public function outOfStock(): static
    {
        return $this->state(fn (array $attributes) => [
            'stock' => 0,
        ]);
    }

    /**
     * Create product for specific category
     */
    public function forCategory(int $categoryId): static
    {
        return $this->state(fn (array $attributes) => [
            'category_id' => $categoryId,
        ]);
    }
}
