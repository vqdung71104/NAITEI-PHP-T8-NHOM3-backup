document.addEventListener('DOMContentLoaded', () => {
    // Nút +
    document.querySelectorAll('.quantity-btn.plus').forEach(btn => {
        btn.addEventListener('click', () => {
            const productId = btn.dataset.productId;
            const input = document.getElementById('quantity-' + productId);
            let value = parseInt(input.value);
            const max = parseInt(input.max);
            if (value < max) value++;
            input.value = value;

            const hiddenInput = document.getElementById('quantity-input-' + productId);
            if (hiddenInput) hiddenInput.value = value;
        });
    });

    // Nút −
    document.querySelectorAll('.quantity-btn.minus').forEach(btn => {
        btn.addEventListener('click', () => {
            const productId = btn.dataset.productId;
            const input = document.getElementById('quantity-' + productId);
            let value = parseInt(input.value);
            if (value > 1) value--;
            input.value = value;

            const hiddenInput = document.getElementById('quantity-input-' + productId);
            if (hiddenInput) hiddenInput.value = value;
        });
    });
});


function addToCart() {
    const btn = document.querySelector('.add-cart');
    const original = btn.textContent;
    
    btn.textContent = 'Đang thêm...';
    btn.disabled = true;
    
    setTimeout(() => {
        btn.textContent = 'Đã thêm';
        btn.style.background = '#2d5a2d';
        
        setTimeout(() => {
            btn.textContent = original;
            btn.style.background = '#000';
            btn.disabled = false;
        }, 1500);
    }, 600);
}

function submitReview(event) {
    event.preventDefault();
    
    const name = document.getElementById('name').value;
    const content = document.getElementById('content').value;
    
    const reviews = document.getElementById('reviews');
    const review = document.createElement('div');
    review.className = 'review';
    review.innerHTML = `
        <div class="review-header">
            <span class="review-name">${name}</span>
            <span class="review-date">Vừa xong</span>
        </div>
        <div class="review-content">${content}</div>
    `;
    
    reviews.insertBefore(review, reviews.firstChild);
    
    document.getElementById('success').style.display = 'block';
    document.getElementById('name').value = '';
    document.getElementById('content').value = '';
    
    setTimeout(() => {
        document.getElementById('success').style.display = 'none';
    }, 3000);
}

function goBack() {
    window.history.back();
}