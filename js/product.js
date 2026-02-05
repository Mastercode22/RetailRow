document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    if (productId) {
        loadProduct(productId);
    } else {
        // Handle case where no product ID is provided
        const container = document.getElementById('product-details-container');
        container.innerHTML = '<p>Product not found.</p>';
    }
});

async function loadProduct(productId) {
    const container = document.getElementById('product-details-container');
    try {
        const response = await fetch(`api/products.php?id=${productId}`);
        const result = await response.json();

        if (result.success && result.data) {
            renderProduct(result.data);
        } else {
            container.innerHTML = '<p>Product not found.</p>';
        }
    } catch (error) {
        console.error('Error loading product:', error);
        container.innerHTML = '<p>Error loading product.</p>';
    }
}

function renderProduct(product) {
    const container = document.getElementById('product-details-container');

    const discountedPrice = product.discount > 0 ? (product.price * (1 - product.discount / 100)).toFixed(2) : product.price;

    container.innerHTML = `
        <div class="product-gallery">
            <div class="main-image">
                <img src="${product.image}" alt="${product.name}">
            </div>
            <!-- Thumbnails will be added here -->
        </div>
        <div class="product-info">
            <h1>${product.name}</h1>
            <div class="category">${product.category_name}</div>
            <div class="price-section">
                <span class="price">GH₵${discountedPrice}</span>
                ${product.old_price ? `<span class="old-price">GH₵${product.old_price}</span>` : ''}
            </div>
            <div class="stock">${product.stock > 0 ? 'In Stock' : 'Out of Stock'}</div>
            <div class="description">${product.description}</div>
            
            <div class="quantity-selector">
                <button id="decrease-quantity">-</button>
                <input type="text" id="quantity" value="1" min="1" max="${product.stock}">
                <button id="increase-quantity">+</button>
            </div>
            
            <button id="add-to-cart" data-product-id="${product.id}">Add to Cart</button>
        </div>
    `;

    setupEventListeners(product);
}

function setupEventListeners(product) {
    const decreaseBtn = document.getElementById('decrease-quantity');
    const increaseBtn = document.getElementById('increase-quantity');
    const quantityInput = document.getElementById('quantity');
    const addToCartBtn = document.getElementById('add-to-cart');

    decreaseBtn.addEventListener('click', () => {
        let quantity = parseInt(quantityInput.value);
        if (quantity > 1) {
            quantityInput.value = quantity - 1;
        }
    });

    increaseBtn.addEventListener('click', () => {
        let quantity = parseInt(quantityInput.value);
        if (quantity < product.stock) {
            quantityInput.value = quantity + 1;
        }
    });

    addToCartBtn.addEventListener('click', async () => {
        const quantity = parseInt(quantityInput.value);
        const productId = addToCartBtn.dataset.productId;

        try {
            const response = await fetch('api/cart/add.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ product_id: productId, quantity: quantity }),
            });

            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }

            const responseText = await response.text();
            let result;

            try {
                result = JSON.parse(responseText);
            } catch (e) {
                throw new Error(`Invalid JSON response: ${responseText}`);
            }

            if (result.success) {
                alert('Product added to cart!');
                updateCartCount();
            } else {
                alert('Error: ' + (result.message || 'Unknown error'));
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            alert('Error adding to cart: ' + error.message);
        }
    });
}
