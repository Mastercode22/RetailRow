/* ============================================
   CART RENDERING REFERENCE
   HTML Structure for Premium Cart Styles
   ============================================ */

// ==========================================
// CART SIDEBAR (Drawer) - Minimal Updates
// ==========================================

/**
 * Your existing cart.js likely renders cart items.
 * Update the HTML structure to match these class names:
 */

// EXAMPLE: Current cart rendering in cart.js
function renderCartItem(item, product) {
    return `
        <div class="cart-item">
            <img src="${product.image}" class="cart-item-image" alt="${product.name}">
            <div class="cart-item-details">
                <div class="cart-item-name">${product.name}</div>
                <div class="cart-item-price">GH₵${product.price}</div>
                <div class="cart-quantity-controls">
                    <button class="cart-quantity-btn" onclick="decreaseQty(${item.id})">−</button>
                    <span class="cart-quantity-display">${item.quantity}</span>
                    <button class="cart-quantity-btn" onclick="increaseQty(${item.id})">+</button>
                </div>
            </div>
            <button class="cart-item-remove" onclick="removeFromCart(${item.id})">✕</button>
        </div>
    `;
}

// ==========================================
// CART PAGE - Item Rendering
// ==========================================

/**
 * For cart.php inline JavaScript, update the renderCart() function:
 */

function renderCart() {
    const container = document.getElementById('cartContainer');

    if (cart.length === 0) {
        container.innerHTML = `
            <div class="empty-cart">
                <div class="empty-cart-icon">🛒</div>
                <h2>Your cart is empty</h2>
                <p>Add some products to get started!</p>
                <a href="index.php" class="shop-now-btn">Start Shopping</a>
            </div>
        `;
        return;
    }

    let cartItemsHtml = `
        <div class="cart-items">
            <div class="cart-header">Shopping Cart (${cart.length} items)</div>
    `;

    let subtotal = 0;

    cart.forEach((item, index) => {
        const product = products.find(p => p.id == item.id);
        if (!product) return;

        const itemTotal = product.price * item.quantity;
        subtotal += itemTotal;

        cartItemsHtml += `
            <div class="cart-item">
                <img src="${product.image || 'assets/images/placeholder.jpg'}" 
                     alt="${product.name}" 
                     class="item-image">
                
                <div class="item-details">
                    <div class="item-name">${product.name}</div>
                    <div class="item-price">GH₵${product.price.toFixed(2)}</div>
                </div>
                
                <div class="item-controls">
                    <div class="quantity-controls">
                        <button class="quantity-btn" onclick="changeQuantity(${index}, -1)">−</button>
                        <input type="number" 
                               class="quantity-input" 
                               value="${item.quantity}" 
                               min="1" 
                               onchange="updateQuantity(${index}, this.value)">
                        <button class="quantity-btn" onclick="changeQuantity(${index}, 1)">+</button>
                    </div>
                    
                    <div class="item-total-price">GH₵${itemTotal.toFixed(2)}</div>
                    
                    <button class="remove-btn" 
                            onclick="removeItem(${index})" 
                            title="Remove item">✕</button>
                </div>
            </div>
        `;
    });

    cartItemsHtml += '</div>';

    const shipping = subtotal >= 50 ? 0 : 9.99;
    const total = subtotal + shipping;

    const summaryHtml = `
        <div class="cart-summary">
            <div class="summary-title">Order Summary</div>
            
            <div class="summary-row">
                <span>Subtotal (${cart.length} items)</span>
                <span>GH₵${subtotal.toFixed(2)}</span>
            </div>
            
            <div class="summary-row">
                <span>Shipping</span>
                <span>${shipping === 0 ? 'FREE' : 'GH₵' + shipping.toFixed(2)}</span>
            </div>
            
            <div class="summary-divider"></div>
            
            <div class="summary-row summary-total">
                <span>Total</span>
                <span>GH₵${total.toFixed(2)}</span>
            </div>
            
            <button class="checkout-btn" onclick="proceedToCheckout()">
                Proceed to Checkout
            </button>
            
            <a href="index.php" class="continue-shopping">Continue Shopping</a>
        </div>
    `;

    container.innerHTML = cartItemsHtml + summaryHtml;
}

// ==========================================
// CART.JS INTEGRATION EXAMPLE
// ==========================================

/**
 * If you're using the existing cart.js from js/cart.js,
 * ensure it renders items with these exact class names.
 * 
 * The premium CSS will automatically style them!
 */

// Example of updating existing cart item rendering:

// BEFORE (Old structure):
/*
<div class="cart-product">
    <img src="...">
    <div class="details">
        <p class="name">Product Name</p>
        <span class="cost">$99</span>
    </div>
    <div class="qty-box">
        <button>-</button>
        <span>1</span>
        <button>+</button>
    </div>
    <button class="delete">X</button>
</div>
*/

// AFTER (Premium structure):
/*
<div class="cart-item">
    <img src="..." class="cart-item-image">
    <div class="cart-item-details">
        <div class="cart-item-name">Product Name</div>
        <div class="cart-item-price">GH₵99</div>
        <div class="cart-quantity-controls">
            <button class="cart-quantity-btn">−</button>
            <span class="cart-quantity-display">1</span>
            <button class="cart-quantity-btn">+</button>
        </div>
    </div>
    <button class="cart-item-remove">✕</button>
</div>
*/

// ==========================================
// CLASS NAME MAPPING REFERENCE
// ==========================================

/**
 * Old Class Name → New Class Name
 * 
 * CART SIDEBAR:
 * .cart-product        → .cart-item
 * .product-image       → .cart-item-image
 * .product-details     → .cart-item-details
 * .product-name        → .cart-item-name
 * .product-price       → .cart-item-price
 * .qty-controls        → .cart-quantity-controls
 * .qty-btn             → .cart-quantity-btn
 * .qty-number          → .cart-quantity-display
 * .delete-btn          → .cart-item-remove
 * 
 * CART PAGE:
 * .product-row         → .cart-item
 * .product-img         → .item-image
 * .product-info        → .item-details
 * .product-title       → .item-name
 * .price               → .item-price
 * .controls            → .item-controls
 * .quantity-box        → .quantity-controls
 * .qty-button          → .quantity-btn
 * .qty-input           → .quantity-input
 * .total-price         → .item-total-price
 * .remove              → .remove-btn
 */

// ==========================================
// COMPLETE CART.JS INTEGRATION
// ==========================================

/**
 * Here's a complete example of cart.js functions
 * that work with the premium styles:
 */

class Cart {
    constructor() {
        this.items = JSON.parse(localStorage.getItem('retailrow_cart') || '[]');
        this.init();
    }

    init() {
        this.updateCartCount();
        this.setupEventListeners();
    }

    setupEventListeners() {
        // Cart toggle
        document.getElementById('cartToggle')?.addEventListener('click', () => {
            this.openCart();
        });

        document.getElementById('closeCart')?.addEventListener('click', () => {
            this.closeCart();
        });

        // Overlay click
        document.getElementById('overlay')?.addEventListener('click', () => {
            this.closeCart();
        });
    }

    openCart() {
        const panel = document.getElementById('cartPanel');
        const overlay = document.getElementById('overlay');

        panel.classList.add('open');
        overlay.classList.add('show');
        panel.setAttribute('aria-hidden', 'false');

        this.renderCartItems();
    }

    closeCart() {
        const panel = document.getElementById('cartPanel');
        const overlay = document.getElementById('overlay');

        panel.classList.remove('open');
        overlay.classList.remove('show');
        panel.setAttribute('aria-hidden', 'true');
    }

    renderCartItems() {
        const container = document.getElementById('cartList');

        if (this.items.length === 0) {
            container.innerHTML = `
                <div class="cart-empty">
                    <div class="cart-empty-icon">🛒</div>
                    <h3>Your cart is empty</h3>
                    <p>Add items to get started</p>
                    <a href="index.php">Browse Products</a>
                </div>
            `;
            this.updateFooter(0);
            return;
        }

        let html = '';
        let total = 0;

        this.items.forEach((item, index) => {
            const itemTotal = item.price * item.quantity;
            total += itemTotal;

            html += `
                <div class="cart-item">
                    <img src="${item.image}" class="cart-item-image" alt="${item.name}">
                    <div class="cart-item-details">
                        <div class="cart-item-name">${item.name}</div>
                        <div class="cart-item-price">GH₵${item.price.toFixed(2)}</div>
                        <div class="cart-quantity-controls">
                            <button class="cart-quantity-btn" onclick="cart.changeQuantity(${index}, -1)">−</button>
                            <span class="cart-quantity-display">${item.quantity}</span>
                            <button class="cart-quantity-btn" onclick="cart.changeQuantity(${index}, 1)">+</button>
                        </div>
                    </div>
                    <button class="cart-item-remove" onclick="cart.removeItem(${index})">✕</button>
                </div>
            `;
        });

        container.innerHTML = html;
        this.updateFooter(total);
    }

    updateFooter(total) {
        document.getElementById('subtotal').textContent = total.toFixed(2);
        document.getElementById('checkoutTotal').textContent = total.toFixed(2);
    }

    updateCartCount() {
        const count = this.items.reduce((sum, item) => sum + item.quantity, 0);
        const badge = document.getElementById('cartCount');

        if (badge) {
            badge.textContent = count;
            // Add bounce animation
            badge.classList.add('cart-count-bounce');
            setTimeout(() => badge.classList.remove('cart-count-bounce'), 400);
        }
    }

    addItem(product, quantity = 1) {
        const existingIndex = this.items.findIndex(item => item.id === product.id);

        if (existingIndex > -1) {
            this.items[existingIndex].quantity += quantity;
        } else {
            this.items.push({
                id: product.id,
                name: product.name,
                price: product.price,
                image: product.image,
                quantity: quantity
            });
        }

        this.save();
        this.updateCartCount();
        this.renderCartItems();
    }

    changeQuantity(index, delta) {
        const newQty = this.items[index].quantity + delta;

        if (newQty < 1) {
            this.removeItem(index);
        } else {
            this.items[index].quantity = newQty;
            this.save();
            this.renderCartItems();
            this.updateCartCount();
        }
    }

    removeItem(index) {
        this.items.splice(index, 1);
        this.save();
        this.renderCartItems();
        this.updateCartCount();
    }

    save() {
        localStorage.setItem('retailrow_cart', JSON.stringify(this.items));
    }
}

// Initialize cart
window.cart = new Cart();

// ==========================================
// API INTEGRATION REFERENCE
// ==========================================

/**
 * If using API-based cart (api/cart/add.php, etc.),
 * ensure your renderCart() function uses the same
 * class names as shown above.
 * 
 * The premium CSS doesn't care whether cart data
 * comes from localStorage or API - it only styles
 * the HTML structure!
 */

// Example API-based cart rendering:
async function loadCartFromAPI() {
    try {
        const response = await fetch('api/cart/get.php');
        const result = await response.json();

        if (result.success) {
            renderCartItems(result.data);
        }
    } catch (error) {
        console.error('Cart load error:', error);
    }
}

function renderCartItems(items) {
    // Use same HTML structure as shown above
    // Premium CSS will automatically apply!
}

// ==========================================
// NOTES FOR DEVELOPERS
// ==========================================

/**
 * 1. The premium styles are CLASS-BASED, not ID-based
 *    Use the exact class names shown above
 * 
 * 2. All existing JavaScript logic can stay the same
 *    Only update the HTML structure/class names
 * 
 * 3. Cart operations (add/remove/update) don't need changes
 *    Only the rendering HTML needs updating
 * 
 * 4. Icons: Use − and + for buttons (not - and +)
 *    Use ✕ for remove (not X)
 * 
 * 5. Test thoroughly after updating class names
 *    Ensure all click handlers still work
 */