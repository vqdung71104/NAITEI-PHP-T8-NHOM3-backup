<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Category;
use App\Models\Product;
use App\Models\Order;

class AdminController extends Controller
{
    /**
     * Display the admin dashboard.
     */
    public function dashboard()
    {
        try {
            // Get data for dashboard
            $categories = Category::all();
            $products = Product::with('category')->get();
            $orders = Order::with(['user', 'items.product'])->get();
            
            // Pagination data
            $pagination = [
                'current_page' => 1,
                'per_page' => 10,
                'total' => $products->count()
            ];

            return inertia('AdminDashboard', [
                'categories' => $categories,
                'products' => $products,
                'orders' => $orders,
                'pagination' => $pagination,
            ]);
        } catch (\Exception $e) {
            // Return empty data if there's an error (e.g., tables don't exist yet)
            return inertia('AdminDashboard', [
                'categories' => [],
                'products' => [],
                'orders' => [],
                'pagination' => [
                    'current_page' => 1,
                    'per_page' => 10,
                    'total' => 0
                ],
            ]);
        }
    }

    public function index()
    {
        return redirect()->route('admin.dashboard');
    }


    /**
     * Display admin statistics or overview
     */
    public function categories()
    {
        $categories = Category::all();
        return redirect()->back()->with('categories', $categories);
    }
    
    public function storeCategory(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $category = Category::create($validated);
        
        return redirect()->back()->with('success', 'Category created successfully.');
    }
    
    public function updateCategory(Request $request, Category $category)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);
        
        $category->update($validated);
        
        return redirect()->back()->with('success', 'Category updated successfully.');
    }
    
    public function destroyCategory(Category $category)
    {
        $category->delete();
        return redirect()->back()->with('success', 'Category deleted successfully.');
    }

    // Product CRUD methods
    public function products()
    {
        $products = Product::all();
        return redirect()->back()->with('products', $products);
    }

    public function storeProduct(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|string|max:255',
        ]);
        
        $product = Product::create($validated);
        
        return redirect()->back()->with('success', 'Product created successfully.');
    }
    
    public function updateProduct(Request $request, Product $product)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'stock' => 'required|integer|min:0',
            'category_id' => 'required|exists:categories,id',
            'image_url' => 'nullable|string|max:255',
        ]);
        
        $product->update($validated);
        
        return redirect()->back()->with('success', 'Product updated successfully.');
    }
    
    public function destroyProduct(Product $product)
    {
        $product->delete();
        return redirect()->back()->with('success', 'Product deleted successfully.');
    }
}
