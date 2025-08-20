@vite(['resources/css/partials/user/header.css', 'resources/js/partials/user/header.js'])
<meta name="user-logged-in" content="{{ auth()->check() ? '1' : '0' }}">
<body>
    <header class="page-header">
    <nav>
            <ul id="navMenu">
                <li><a href="{{ route('home') }}" class="{{ request()->routeIs('home') ? 'active' : '' }}">Trang chủ</a></li>
                <li><a href="{{ route('products.viewall') }}" class="{{ request()->routeIs('products.*') ? 'active' : '' }}">Sản phẩm</a></li>
                <li><a href="{{ route('cart.index') }}" class="{{ request()->routeIs('cart.*') ? 'active' : '' }}">Giỏ hàng</a></li>
                <li><a href="{{ route('orders.track') }}" class="{{ request()->routeIs('orders.track') ? 'active' : '' }}">Theo dõi đơn hàng</a></li>
                <li class="user-menu">
                    @auth
                        <div id="authUser">
                            <a href="#" class="btn btn-primary" onclick="toggleDropdown(event)">Xem Profile</a>
                            <div class="user-dropdown" id="userDropdown">
                                <a href="{{ route('home') }}">Profile</a>
                                <a href="{{ route('orders.track') }}">Đơn hàng</a>
                                <a href="{{ route('logout') }}"
                                onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                Đăng xuất
                                </a>
                            </div>

                            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                                @csrf
                            </form>
                        </div>
                    @endauth

                    <!-- Người dùng chưa đăng nhập -->
                    @guest
                        <div id="guestUser">
                            <a href="{{ route('login') }}" class="btn btn-secondary login-btn">Đăng nhập</a>
                        </div>
                    @endguest
                </li>
        </ul>
            <button class="mobile-toggle" onclick="toggleMenu()">☰</button>
    </nav>
    <meta name="csrf-token" content="{{ csrf_token() }}">
</header>
</body>
