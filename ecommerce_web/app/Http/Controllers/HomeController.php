<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Product; // import model Product

class HomeController extends Controller
{
    public function index()
    {
        // Lấy tất cả sản phẩm từ bảng products
        $products = Product::all();

        return view('home', compact('products'));
    }
}
