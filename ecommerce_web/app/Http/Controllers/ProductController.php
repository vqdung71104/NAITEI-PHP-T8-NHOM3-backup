<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Http\Requests\ReviewRequest;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::all();
        return view('products.index', compact('products'));
    }

    public function show($id)
    {
        $product = Product::findOrFail($id);
        $reviews = session("reviews.{$id}", []);

        return view('products.show', compact('product', 'reviews'));
    }

    public function addReview(ReviewRequest $request, $id)
    {
        // Đảm bảo sản phẩm tồn tại (phòng trường hợp post trực tiếp sai ID)
        $product = Product::findOrFail($id);

        // Chỉ lấy dữ liệu đã qua validate
        $data = $request->validated(); // ['author' => ..., 'content' => ...];

        $reviews = session("reviews.{$product->id}", []);
        $reviews[] = $data;

        session(["reviews.{$product->id}" => $reviews]);

        return redirect()
            ->route('products.show', $product->id)
            ->with('success', 'Đánh giá đã được gửi!');
    }
}
