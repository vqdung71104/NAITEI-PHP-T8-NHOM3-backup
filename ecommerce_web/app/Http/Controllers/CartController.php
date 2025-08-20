<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use App\Models\CartItem;

class CartController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để xem giỏ hàng.');
        }

        // Lấy tất cả sản phẩm trong giỏ hàng của user, kèm thông tin product
        $cartItems = CartItem::with('product')->where('user_id', $user->id)->get();

        // Tính subtotal
        $subtotal = $cartItems->sum(function($item) {
            return ($item->product->price ?? 0) * $item->quantity;
        });

        $shipping = 30000; // phí vận chuyển mặc định

        return view('cart.index', compact('cartItems', 'subtotal', 'shipping'));
    }

    public function add(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            // Nếu chưa đăng nhập, chuyển hướng tới login
            return redirect()->route('login')->with('error', 'Bạn cần đăng nhập để thêm vào giỏ hàng.');
        }

        // Kiểm tra xem sản phẩm đã có trong giỏ hàng chưa
        $cartItem = CartItem::where('user_id', $user->id)
                            ->where('product_id', $request->product_id)
                            ->first();

        if ($cartItem) {
            // Nếu đã có, tăng số lượng
            $cartItem->quantity += $request->quantity;
            $cartItem->save();
        } else {
            // Nếu chưa có, tạo mới
            CartItem::create([
                'user_id' => $user->id,
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
        }

        return redirect()->route('cart.index')->with('success', 'Đã thêm vào giỏ hàng!');
    }

    public function update(Request $request, CartItem $cartItem)
    {
        $request->validate(['quantity' => 'required|integer|min:1']);
        $cartItem->update(['quantity' => $request->quantity]);
        return response()->json(['success' => true]);
    }

    public function remove(CartItem $cartItem)
    {
        $cartItem->delete();
        return response()->json(['success' => true]);
    }
    
    public function checkout()
{
    // Lấy giỏ hàng từ session
    $cart = session()->get('cart', []);

    // Tính tổng tiền
    $total = 0;
    foreach ($cart as $item) {
        $total += $item['price'] * $item['quantity'];
    }

    // Tạm thời dùng dữ liệu giả cho địa chỉ và SĐT
    // Sau này có thể lấy từ DB nếu có bảng địa chỉ
    $addresses = [
        '123 Đường ABC, Quận 1, TP. HCM',
        '456 Đường DEF, Quận 3, TP. HCM',
    ];

    $phones = [
        '0901234567',
        '0987654321',
    ];

    return view('cart.checkout', compact('cart', 'total', 'addresses', 'phones'));
}


    public function processCheckout(Request $request)
    {
        $cart = session('cart', []);

        if (empty($cart)) {
            return redirect()->route('cart.index')->with('error', 'Giỏ hàng trống.');
        }

        // Lấy địa chỉ & số điện thoại
        $address = $request->input('selected_address') === 'new'
            ? $request->input('new_address')
            : $request->input('selected_address');

        $phone = $request->input('selected_phone') === 'new'
            ? $request->input('new_phone')
            : $request->input('selected_phone');

        $order = [
            'id' => uniqid('order_'),
            'items' => $cart,
            'total' => array_sum(array_map(fn($item) => $item['price'] * $item['quantity'], $cart)),
            'address' => $address,
            'phone' => $phone,
            'status' => 'Đang xử lý',
            'created_at' => now()->format('Y-m-d H:i:s')
        ];

        $orders = session('orders', []);
        $orders[] = $order;
        session(['orders' => $orders]);

        session()->forget('cart');

        return redirect()->route('orders.track')->with('success', 'Đặt hàng thành công! Mã đơn: ' . $order['id']);
    }
}
