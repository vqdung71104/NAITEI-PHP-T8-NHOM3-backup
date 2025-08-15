<?php


namespace App\Http\Controllers;


use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;


class ProductController extends Controller{
    public function index(): JsonResponse
    {
        try {
            $products = Product::all();
            return response()->json(['success' => true, 'data' => $products, 'message' => 'Get products successfully'],200);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to retrieve products'], 500);
        }
    }


    public function store(Request $request): JsonResponse
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255|unique:products,name',
                'description' => 'nullable|string',
                'price' => 'required|numeric|min:0',
                'category_id' => 'required|exists:categories,id',
                'stock' => 'required|integer|min:0',
            ]);


            $product = Product::create($validated);


            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Create product success'
            ], 201);


        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error create category',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function show(string $id): JsonResponse
    {
        try {
            $product = Product::find($id);


            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }


            return response()->json([
                'success' => true,
                'data' => $product,
                'message' => 'Get product successfully'
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error get product',
                'error' => $e->getMessage()
            ], 500);
        }
    }




    public function update(Request $request, string $id): JsonResponse
    {
        try {
            $product = Product::find($id);


            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }


            $validated = $request->validate([
                'name' => 'sometimes|required|string|max:255|unique:products,name,' . $id,
                'description' => 'nullable|string',
                'price' => 'sometimes|required|numeric|min:0',
                'category_id' => 'sometimes|required|exists:categories,id',
                'stock' => 'sometimes|required|integer|min:0',
            ]);


            $product->update($validated);


            return response()->json([
                'success' => true,
                'data' => $product->fresh(),
                'message' => 'Update product successfully'
            ], 200);


        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Invalid data',
                'errors' => $e->errors()
            ], 422);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error update product',
                'error' => $e->getMessage()
            ], 500);
        }
    }


    public function destroy(string $id): JsonResponse
    {
        try {
            $product = Product::find($id);


            if (!$product) {
                return response()->json([
                    'success' => false,
                    'message' => 'Product not found'
                ], 404);
            }




            if ($product->orders()->exists() || $product->reviews()->exists()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete product because it has associated orders or reviews'
                ], 400);
            }


            $product->delete();


            return response()->json([
                'success' => true,
                'message' => 'Delete product successfully'
            ], 200);


        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting product',
                'error' => $e->getMessage()
            ], 500);
        }
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


