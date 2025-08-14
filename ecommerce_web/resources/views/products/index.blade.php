@extends('layouts.user')

@section('title', 'Danh sách sản phẩm')

@section('content')
    <h2>Sản phẩm nổi bật</h2>
    <div class="product-list">
        @foreach ($products as $product)
            <div class="product-card">
                <a href="{{ url('/products/' . $product->id) }}">
                    {{-- Ảnh từ DB (link đầy đủ) --}}
                    <img src="{{ $product->image_url }}" 
                         width="150" height="200" 
                         alt="{{ $product->name }}">
                </a>

                <h3>
                    <a href="{{ url('/products/' . $product->id) }}">
                        {{ $product->name }}
                    </a>
                </h3>

                <p>{{ number_format($product->price, 0, ',', '.') }} VNĐ</p>

                {{-- Form thêm vào giỏ --}}
                <form action="{{ route('cart.add') }}" method="POST">
                    @csrf
                    <input type="hidden" name="name" value="{{ $product->name }}">
                    <input type="hidden" name="price" value="{{ $product->price }}">
                    <input type="hidden" name="image" value="{{ $product->image_url }}">
                    <input type="hidden" name="quantity" value="1">
                    <button type="submit">Thêm vào giỏ</button>
                </form>

                <p><a href="{{ url('/products/' . $product->id) }}">Xem chi tiết</a></p>
            </div>
        @endforeach
    </div>
@endsection
