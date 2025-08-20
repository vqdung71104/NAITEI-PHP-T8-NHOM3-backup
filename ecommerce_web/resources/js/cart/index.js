document.addEventListener("DOMContentLoaded", function () {

    // Lấy CSRF token từ meta
    const tokenMeta = document.querySelector('meta[name="csrf-token"]');
    const csrfToken = tokenMeta ? tokenMeta.getAttribute('content') : '';

    if (!csrfToken) console.warn('CSRF token not found!');

    // Hàm update số lượng trong DB
    async function updateDatabase(cartItemId, quantity) {
        try {
            const res = await fetch(`/cart/update/${cartItemId}`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken
                },
                body: JSON.stringify({ quantity })
            });
            const data = await res.json();
            if (!data.success) console.error('Update DB failed', data);
        } catch (err) {
            console.error('AJAX error', err);
        }
    }

    // Hàm tính subtotal/total
    function updateCartSummary() {
        let subtotal = 0;

        document.querySelectorAll('.cart-item').forEach(item => {
            const input = item.querySelector('.quantity-input');
            const price = parseFloat(input.dataset.price) || 0;
            const qty = parseInt(input.value) || 1;

            subtotal += price * qty;
            // cập nhật giá từng sản phẩm
            item.querySelector('.price').textContent = (price * qty).toLocaleString('vi-VN') + 'đ';
        });

        const shipping = 30000;
        const total = subtotal + shipping;

        const subtotalEl = document.getElementById('subtotal');
        const shippingEl = document.getElementById('shipping');
        const totalEl = document.getElementById('total');

        if (subtotalEl) subtotalEl.textContent = subtotal.toLocaleString('vi-VN') + 'đ';
        if (shippingEl) shippingEl.textContent = shipping.toLocaleString('vi-VN') + 'đ';
        if (totalEl) totalEl.textContent = total.toLocaleString('vi-VN') + 'đ';
    }

    // Xử lý số lượng +/- và input
    document.querySelectorAll('.cart-item').forEach(item => {
        const cartItemId = item.dataset.id;
        const input = item.querySelector('.quantity-input');
        const minusBtn = item.querySelector('.quantity-btn.minus');
        const plusBtn = item.querySelector('.quantity-btn.plus');

        // Nút trừ
        minusBtn.addEventListener('click', () => {
            let value = parseInt(input.value) || 1;
            if (value > 1) value--;
            input.value = value;

            updateCartSummary();
            updateDatabase(cartItemId, value);
        });

        // Nút cộng
        plusBtn.addEventListener('click', () => {
            let value = parseInt(input.value) || 1;
            const max = parseInt(input.max) || Infinity;
            if (value < max) value++;
            input.value = value;

            updateCartSummary();
            updateDatabase(cartItemId, value);
        });

        // Nhập tay
        input.addEventListener('input', () => {
            let value = parseInt(input.value) || 1;
            const max = parseInt(input.max) || Infinity;
            if (value < 1) value = 1;
            if (value > max) value = max;
            input.value = value;

            updateCartSummary();
            updateDatabase(cartItemId, value);
        });
    });

    // Xử lý xóa sản phẩm
    document.querySelectorAll('.remove-btn').forEach(button => {
        button.addEventListener('click', async () => {
            const cartItemDiv = button.closest('.cart-item');
            const cartItemId = cartItemDiv.dataset.id;

            if (!confirm('Bạn có chắc muốn xóa sản phẩm này khỏi giỏ hàng?')) return;

            try {
                const res = await fetch(`/cart/remove/${cartItemId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    }
                });
                const data = await res.json();
                if (data.success) {
                    cartItemDiv.remove(); // xóa khỏi DOM
                    updateCartSummary();  // cập nhật subtotal/total
                } else {
                    console.error('Xóa thất bại', data);
                }
            } catch (err) {
                console.error('AJAX error', err);
            }
        });
    });

    // Chạy lần đầu
    updateCartSummary();
});
