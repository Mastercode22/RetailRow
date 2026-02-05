document.addEventListener('DOMContentLoaded', () => {
    const cartPanel = document.getElementById('cartPanel');
    const closeCartBtn = document.getElementById('closeCart');
    const cartToggle = document.getElementById('cartToggle');
    const overlay = document.getElementById('overlay');

    const openCart = () => {
        cartPanel.classList.add('open');
        overlay.classList.add('show');
        document.body.style.overflow = 'hidden';
        updateCart();
    };

    const closeCart = () => {
        cartPanel.classList.remove('open');
        overlay.classList.remove('show');
        document.body.style.overflow = '';
    };

    if (cartToggle) {
        cartToggle.addEventListener('click', openCart);
    }
    if (closeCartBtn) {
        closeCartBtn.addEventListener('click', closeCart);
    }
    if (overlay) {
        overlay.addEventListener('click', closeCart);
    }

    updateCartCount();
});

async function updateCart() {
    const cartListEl = document.getElementById('cartList');
    const subtotalEl = document.getElementById('subtotal');
    const checkoutTotalEl = document.getElementById('checkoutTotal');

    try {
        const response = await fetch('api/cart/get.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }
        const result = await response.json();

        if (result.success && result.data && result.data.items) {
            if (result.data.items.length === 0) {
                if (cartListEl) cartListEl.innerHTML = '<p class="cart-empty">Your cart is empty</p>';
                if (subtotalEl) subtotalEl.textContent = '0.00';
                if (checkoutTotalEl) checkoutTotalEl.textContent = '0.00';
                return;
            }

            if (cartListEl) {
                cartListEl.innerHTML = result.data.items.map(item => `
                <div class="cart-item" data-id="${item.id}">
                    <img src="${item.image || 'assets/images/placeholder.jpg'}" alt="${item.name}" class="cart-item-image">
                    <div class="cart-item-details">
                        <a href="product.php?id=${item.id}" class="cart-item-title">${item.name}</a>
                        <div class="cart-item-price">GH₵ ${item.price.toFixed(2)}</div>
                        ${item.description ? `<div class="cart-item-description">${item.description}</div>` : ''}
                        <div class="cart-item-actions">
                            <div class="quantity-selector">
                                <button class="quantity-btn" data-action="decrease" data-item-id="${item.id}">-</button>
                                <span class="item-quantity">${item.quantity}</span>
                                <button class="quantity-btn" data-action="increase" data-item-id="${item.id}">+</button>
                            </div>
                            <button class="remove-item-btn" data-item-id="${item.id}">Remove</button>
                        </div>
                    </div>
                </div>
            `).join('');
            }

            // Add event listeners for quantity and remove buttons
            attachCartEventListeners();

            if (subtotalEl) subtotalEl.textContent = result.data.subtotal.toFixed(2);
            if (checkoutTotalEl) checkoutTotalEl.textContent = result.data.subtotal.toFixed(2);
        } else {
            if (cartListEl) cartListEl.innerHTML = '<p class="cart-empty">Your cart is empty</p>';
            if (subtotalEl) subtotalEl.textContent = '0.00';
            if (checkoutTotalEl) checkoutTotalEl.textContent = '0.00';
        }
    } catch (error) {
        console.error('Error fetching cart:', error);
        if (cartListEl) cartListEl.innerHTML = '<p class="cart-empty">Error loading cart. Please try again.</p>';
        if (subtotalEl) subtotalEl.textContent = '0.00';
        if (checkoutTotalEl) checkoutTotalEl.textContent = '0.00';
    }
}

async function updateCartCount() {
    const cartCountEl = document.getElementById('cartCount');
    try {
        const response = await fetch('api/cart/get.php');
        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        if (result.success && result.data && result.data.items) {
            const totalItems = result.data.items.reduce((sum, item) => sum + item.quantity, 0);
            cartCountEl.textContent = totalItems;
            cartCountEl.style.display = totalItems > 0 ? 'flex' : 'none';
        } else {
            cartCountEl.textContent = 0;
            cartCountEl.style.display = 'none';
        }
    } catch (error) {
        console.error('Error fetching cart count:', error);
        cartCountEl.textContent = 0;
        cartCountEl.style.display = 'none';
    }
}

function attachCartEventListeners() {
    // Increase quantity buttons
    document.querySelectorAll('.quantity-btn[data-action="increase"]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const itemId = btn.dataset.itemId;
            const quantitySpan = btn.previousElementSibling;
            const newQuantity = parseInt(quantitySpan.textContent) + 1;

            try {
                const response = await fetch('api/cart/update.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({
                        product_id: itemId,
                        quantity: newQuantity
                    })
                });

                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }

                const result = await response.json();
                if (result.success) {
                    updateCart();
                    updateCartCount();
                } else {
                    alert('Failed to update quantity: ' + result.message);
                }
            } catch (error) {
                console.error('Error updating quantity:', error);
                alert('Error updating quantity');
            }
        });
    });

    // Decrease quantity buttons
    document.querySelectorAll('.quantity-btn[data-action="decrease"]').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const itemId = btn.dataset.itemId;
            const quantitySpan = btn.nextElementSibling;
            const currentQuantity = parseInt(quantitySpan.textContent);

            if (currentQuantity <= 1) {
                // Remove item if quantity would be 0
                if (confirm('Remove this item from cart?')) {
                    removeFromCart(itemId);
                }
            } else {
                const newQuantity = currentQuantity - 1;
                try {
                    const response = await fetch('api/cart/update.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                        },
                        body: JSON.stringify({
                            product_id: itemId,
                            quantity: newQuantity
                        })
                    });

                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }

                    const result = await response.json();
                    if (result.success) {
                        updateCart();
                        updateCartCount();
                    } else {
                        alert('Failed to update quantity: ' + result.message);
                    }
                } catch (error) {
                    console.error('Error updating quantity:', error);
                    alert('Error updating quantity');
                }
            }
        });
    });

    // Remove buttons
    document.querySelectorAll('.remove-item-btn').forEach(btn => {
        btn.addEventListener('click', async (e) => {
            e.preventDefault();
            const itemId = btn.dataset.itemId;
            if (confirm('Remove this item from cart?')) {
                removeFromCart(itemId);
            }
        });
    });
}

async function removeFromCart(productId) {
    try {
        const response = await fetch('api/cart/remove.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({
                product_id: productId
            })
        });

        if (!response.ok) {
            throw new Error(`HTTP error! status: ${response.status}`);
        }

        const result = await response.json();
        if (result.success) {
            updateCart();
            updateCartCount();
        } else {
            alert('Failed to remove item: ' + result.message);
        }
    } catch (error) {
        console.error('Error removing item:', error);
        alert('Error removing item from cart');
    }
}
