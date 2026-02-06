/* ============================================
   PREMIUM PRODUCT PAGE - ENHANCED JAVASCRIPT
   Minimal changes to preserve existing logic
   ============================================ */

document.addEventListener('DOMContentLoaded', function () {
    const urlParams = new URLSearchParams(window.location.search);
    const productId = urlParams.get('id');

    if (productId) {
        loadProduct(productId);
    } else {
        const container = document.getElementById('product-details-container');
        container.innerHTML = '<p>Product not found.</p>';
    }

    // Setup sticky add to cart for mobile
    setupStickyCart();
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

    // Calculate prices
    const discountedPrice = product.discount > 0
        ? (product.price * (1 - product.discount / 100)).toFixed(2)
        : product.price;
    const savings = product.discount > 0
        ? (product.price - discountedPrice).toFixed(2)
        : 0;
    const savingsPercent = product.discount || 0;

    // Determine stock status
    let stockClass = 'in-stock';
    let stockText = 'In Stock';
    if (product.stock === 0) {
        stockClass = 'out-of-stock';
        stockText = 'Out of Stock';
    } else if (product.stock < 10) {
        stockClass = 'low-stock';
        stockText = `Only ${product.stock} left in stock`;
    }

    // Generate star rating (example - you can modify based on actual rating data)
    const rating = product.rating || 4.5;
    const reviewCount = product.review_count || 0;
    const starsHtml = generateStars(rating);

    container.innerHTML = `
        <div class="product-gallery">
            <div class="main-image">
                ${product.discount > 0 ? `<div class="discount-badge">-${product.discount}%</div>` : ''}
                <img src="${product.image}" alt="${product.name}" id="mainProductImage">
            </div>
            <div class="thumbnails">
                <div class="thumbnail active" data-image="${product.image}">
                    <img src="${product.image}" alt="${product.name}">
                </div>
                ${product.image_2 ? `
                <div class="thumbnail" data-image="${product.image_2}">
                    <img src="${product.image_2}" alt="${product.name}">
                </div>` : ''}
                ${product.image_3 ? `
                <div class="thumbnail" data-image="${product.image_3}">
                    <img src="${product.image_3}" alt="${product.name}">
                </div>` : ''}
            </div>
        </div>
        
        <div class="product-info">
            <h1>${product.name}</h1>
            <div class="category">${product.category_name || 'Uncategorized'}</div>
            
            <div class="rating-row">
                <div class="star-rating">${starsHtml}</div>
                <span class="rating-text">
                    <span class="rating-count">${rating}</span> ${reviewCount > 0 ? `(${reviewCount} reviews)` : ''}
                </span>
            </div>
            
            <div class="price-section">
                <span class="price">GH₵${discountedPrice}</span>
                ${product.old_price || product.discount > 0 ? `
                    <div>
                        <span class="old-price">GH₵${product.old_price || product.price}</span>
                        ${savings > 0 ? `<span class="savings-text">Save GH₵${savings}</span>` : ''}
                    </div>
                ` : ''}
            </div>
            
            <div class="stock ${stockClass}">${stockText}</div>
            
            <div class="description">${product.description || ''}</div>
            
            <div class="quantity-cta-section">
                <label class="quantity-label">Quantity</label>
                <div class="quantity-selector">
                    <button id="decrease-quantity" ${product.stock === 0 ? 'disabled' : ''}>−</button>
                    <input type="number" id="quantity" value="1" min="1" max="${product.stock}" ${product.stock === 0 ? 'disabled' : ''}>
                    <button id="increase-quantity" ${product.stock === 0 ? 'disabled' : ''}>+</button>
                </div>
                
                <button id="add-to-cart" data-product-id="${product.id}" ${product.stock === 0 ? 'disabled' : ''}>
                    ${product.stock === 0 ? 'Out of Stock' : 'Add to Cart'}
                </button>
            </div>
            
            <div class="trust-badges">
                <div class="trust-badge">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    <span>Genuine Products</span>
                </div>
                <div class="trust-badge">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    <span>Secure Payment</span>
                </div>
                <div class="trust-badge">
                    <svg viewBox="0 0 24 24" fill="currentColor">
                        <path d="M9 16.17L4.83 12l-1.42 1.41L9 19 21 7l-1.41-1.41z"/>
                    </svg>
                    <span>Easy Returns</span>
                </div>
            </div>
        </div>
        
        <!-- Product Details Tabs -->
        <div class="product-details-tabs" style="grid-column: 1 / -1;">
            <div class="tabs-nav">
                <button class="tab-btn active" data-tab="description">Description</button>
                <button class="tab-btn" data-tab="specifications">Specifications</button>
                <button class="tab-btn" data-tab="reviews">Reviews</button>
            </div>
            
            <div class="tab-content active" id="description-tab">
                <div class="detail-card">
                    <h3>Product Description</h3>
                    <p>${product.description || 'No description available.'}</p>
                </div>
            </div>
            
            <div class="tab-content" id="specifications-tab">
                <div class="detail-card">
                    <h3>Specifications</h3>
                    <table class="specs-table">
                        <tr>
                            <td>Brand</td>
                            <td>${product.brand || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td>Category</td>
                            <td>${product.category_name || 'N/A'}</td>
                        </tr>
                        <tr>
                            <td>Stock Status</td>
                            <td>${stockText}</td>
                        </tr>
                        <tr>
                            <td>SKU</td>
                            <td>${product.sku || product.id}</td>
                        </tr>
                    </table>
                </div>
            </div>
            
            <div class="tab-content" id="reviews-tab">
                <div class="detail-card">
                    <h3>Customer Reviews</h3>
                    <p>No reviews yet. Be the first to review this product!</p>
                </div>
            </div>
        </div>
    `;

    setupEventListeners(product);
    setupThumbnailGallery();
    setupProductTabs();
}

// Generate star rating HTML
function generateStars(rating) {
    const fullStars = Math.floor(rating);
    const hasHalfStar = rating % 1 >= 0.5;
    let html = '';

    for (let i = 0; i < 5; i++) {
        if (i < fullStars) {
            html += '<span class="star">★</span>';
        } else if (i === fullStars && hasHalfStar) {
            html += '<span class="star">★</span>';
        } else {
            html += '<span class="star" style="color: #DDD;">★</span>';
        }
    }

    return html;
}

// Setup thumbnail gallery
function setupThumbnailGallery() {
    const thumbnails = document.querySelectorAll('.thumbnail');
    const mainImage = document.getElementById('mainProductImage');

    if (!mainImage || thumbnails.length === 0) return;

    thumbnails.forEach(thumb => {
        thumb.addEventListener('click', function () {
            // Remove active class from all
            thumbnails.forEach(t => t.classList.remove('active'));
            // Add active to clicked
            this.classList.add('active');
            // Update main image
            const newImage = this.dataset.image;
            mainImage.src = newImage;
        });
    });
}

// Setup product tabs
function setupProductTabs() {
    const tabButtons = document.querySelectorAll('.tab-btn');
    const tabContents = document.querySelectorAll('.tab-content');

    tabButtons.forEach(btn => {
        btn.addEventListener('click', function () {
            const targetTab = this.dataset.tab;

            // Remove active from all buttons and contents
            tabButtons.forEach(b => b.classList.remove('active'));
            tabContents.forEach(c => c.classList.remove('active'));

            // Add active to clicked button and corresponding content
            this.classList.add('active');
            document.getElementById(`${targetTab}-tab`).classList.add('active');
        });
    });
}

// Setup event listeners (existing logic)
function setupEventListeners(product) {
    const decreaseBtn = document.getElementById('decrease-quantity');
    const increaseBtn = document.getElementById('increase-quantity');
    const quantityInput = document.getElementById('quantity');
    const addToCartBtn = document.getElementById('add-to-cart');

    if (decreaseBtn) {
        decreaseBtn.addEventListener('click', () => {
            let quantity = parseInt(quantityInput.value);
            if (quantity > 1) {
                quantityInput.value = quantity - 1;
            }
        });
    }

    if (increaseBtn) {
        increaseBtn.addEventListener('click', () => {
            let quantity = parseInt(quantityInput.value);
            if (quantity < product.stock) {
                quantityInput.value = quantity + 1;
            }
        });
    }

    if (addToCartBtn) {
        addToCartBtn.addEventListener('click', async () => {
            const quantity = parseInt(quantityInput.value);
            const productId = addToCartBtn.dataset.productId;

            // Add loading state
            addToCartBtn.disabled = true;
            const originalText = addToCartBtn.textContent;
            addToCartBtn.textContent = 'Adding...';

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
                    // Success feedback
                    // Add to UI Cart (cart.js)
                    if (window.cart) {
                        window.cart.addItem({
                            id: product.id,
                            name: product.name,
                            price: product.price,
                            image: product.image
                        }, quantity);
                    }

                    addToCartBtn.textContent = '✓ Added!';
                    addToCartBtn.classList.add('cart-success-animation');

                    // Cart count is updated automatically by window.cart.addItem()

                    // Reset button after 2 seconds
                    setTimeout(() => {
                        addToCartBtn.textContent = originalText;
                        addToCartBtn.disabled = false;
                        addToCartBtn.classList.remove('cart-success-animation');
                    }, 2000);
                } else {
                    alert('Error: ' + (result.message || 'Unknown error'));
                    addToCartBtn.textContent = originalText;
                    addToCartBtn.disabled = false;
                }
            } catch (error) {
                console.error('Error adding to cart:', error);
                alert('Error adding to cart: ' + error.message);
                addToCartBtn.textContent = originalText;
                addToCartBtn.disabled = false;
            }
        });
    }

    // Connect sticky add-to-cart button (mobile) to main button
    const stickyAddToCartBtn = document.querySelector('.sticky-add-to-cart button');
    if (stickyAddToCartBtn && addToCartBtn) {
        stickyAddToCartBtn.addEventListener('click', () => {
            addToCartBtn.click();
        });
    }
}

// Setup sticky add to cart for mobile
function setupStickyCart() {
    const stickyCart = document.querySelector('.sticky-add-to-cart');
    if (!stickyCart) return;

    let lastScroll = 0;
    const threshold = 300;

    window.addEventListener('scroll', () => {
        const currentScroll = window.pageYOffset;

        if (currentScroll > threshold) {
            stickyCart.classList.add('visible');
        } else {
            stickyCart.classList.remove('visible');
        }

        lastScroll = currentScroll;
    });
}