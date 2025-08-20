# NAITEI-PHP-T8-NHOM3
# 📚 BookStore Website

Một dự án xây dựng website bán sách trực tuyến, hỗ trợ người dùng tìm kiếm, mua sách, và quản lý giỏ hàng một cách tiện lợi.  

---

## 🚀 Giới thiệu

Trang web BookStore được phát triển nhằm mục tiêu:  
- Cung cấp nền tảng bán sách trực tuyến thân thiện và dễ sử dụng.  
- Hỗ trợ khách hàng tìm kiếm, lọc và đặt mua sách nhanh chóng.  
- Quản lý kho sách và đơn hàng hiệu quả cho quản trị viên.  

---

## ✨ Tính năng chính

- 🔍 *Tìm kiếm sách* theo tên, tác giả, thể loại.  
- 🛒 *Giỏ hàng*: thêm, xóa, cập nhật số lượng sách.  
- 👤 *Tài khoản người dùng*: đăng ký, đăng nhập, cập nhật thông tin cá nhân.  
- 📦 *Quản trị viên*: quản lý sách, quản lý đơn hàng, quản lý người dùng, thống kê số liệu.   


---

## 🛠️ Công nghệ sử dụng

- *Frontend*: (React)  
- *Backend*: (Laravel)  
- *Cơ sở dữ liệu*: (MySQL)  
- *Authentication*: ()   

---

## Yêu cầu hệ thống

Để chạy được dự án này, cần đảm bảo môi trường có các thành phần sau:

- **PHP** >= 8.1  
- **Laravel** >= 10.x  
- **Composer** >= 2.x  
- **MySQL** >= 8.0  
- **Node.js & NPM** (khuyến nghị: Node.js >= 18.x, NPM >= 9.x)

---

## Cài đặt và chạy dự án

Thực hiện các bước sau để cài đặt và chạy dự án trên máy của bạn:

1. Clone repository

    ```bash
    git clone https://github.com/awesome-academy/NAITEI-PHP-T8-NHOM3.git
    cd ecommerce_web
    ```

2. Cài đặt dependencies
    ```bash
    composer install
    npm install
    ```

3. Cấu hình môi trường
    ```bash
    cp .env.example .env
    php artisan key:generate
    ```

    ```bash
    # Cập nhật file .env với thông tin database:
    DB_CONNECTION=mysql
    DB_HOST=127.0.0.1
    DB_PORT=3306
    DB_DATABASE=bookstore
    DB_USERNAME=your-username
    DB_PASSWORD=your-password
    ```

4. Thiết lập database
    ```bash
    php artisan migrate
    php artisan db:seed
    ```

5. Compile assets
    ```bash
    npm run build
    ```

6. Chạy ứng dụng
    ```bash
    php artisan serve
    ```

Ứng dụng sẽ chạy tại: http://localhost:8000


--

## Tài khoản demo
- Admin
    - Email: admin@bookstore.com
    - Password: 123456789
- User
    - Email: user@example.com
    - Password: 123456789

## 📂 Cấu trúc thư mục

Dự án được tổ chức theo cấu trúc chính như sau:

```plaintext
ecommerce_web/
├── app/            # Code chính của Laravel (Models, Controllers, Middleware, ...)
├── database/       # Chứa Migrations & Seeders
├── public/         # Entry point, file index.php, assets public
├── resources/      # Views (Blade), JavaScript, CSS, Components
├── routes/         # Định nghĩa route (web.php, api.php, ...)
├── package.json    # Cấu hình dependencies cho Node.js/NPM
└── composer.json   # Cấu hình dependencies cho PHP/Laravel