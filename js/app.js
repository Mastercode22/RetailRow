/**
 * RetailRow Frontend Integration
 * Connects frontend UI with backend API
 * Handles dynamic content loading, state management, and user interactions
 */

document.addEventListener('DOMContentLoaded', async () => {
    // Initialize app
    await initializeApp();

    // Initialize UI components
    initializeCarousel();
    initializeFlashTimer();
    initializeScrollControls();
    // initializeCart(); // Handled by cart.js
    initializeMobileMenu();
    initializeSearch();
    initializeBackToTop();
});

/**
 * Initialize the application
 */
async function initializeApp() {
    try {
        showGlobalLoader(true);

        // Load all initial data in parallel
        await Promise.all([
            loadSettings(),
            loadCategories(),
            loadBanners(),
            loadFlashSales(),
            loadFeaturedProducts(),
            loadFooterLinks(),
            loadNavigationMenus()
        ]);

        showGlobalLoader(false);
    } catch (error) {
        console.error('App initialization error:', error);
        showGlobalLoader(false);
        showErrorMessage('Failed to load page content. Please refresh the page.');
    }
}

/**
 * Load site settings
 */
async function loadSettings() {
    try {
        const response = await api.getSettings();

        if (response.success && response.data) {
            const settings = response.data;

            // Update announcement bar
            updateElement('.announce-left', settings.announcement_text);

            // Update phone number
            const phoneLink = document.querySelector('.phone-number');
            if (phoneLink && settings.phone_number) {
                phoneLink.textContent = settings.phone_number;
                phoneLink.href = `tel:${settings.phone_number.replace(/\s/g, '')}`;
            }

            // Update page title if needed
            if (settings.site_title) {
                document.title = settings.site_title;
            }

            // Update WhatsApp number
            if (settings.whatsapp_number) {
                updateWhatsAppButton(settings.whatsapp_number);
            }

            // Store settings globally for other components
            window.retailRowSettings = settings;
        }
    } catch (error) {
        console.error('Failed to load settings:', error);
    }
}

/**
 * Load categories
 */
async function loadCategories() {
    try {
        const response = await api.getCategories();

        if (response.success && response.data) {
            renderCategories(response.data);
            renderMobileCategories(response.data);
        }
    } catch (error) {
        console.error('Failed to load categories:', error);
    }
}

/**
 * Render categories
 */
function renderCategories(categories) {
    const container = document.getElementById('categoriesGrid');
    if (!container) return;

    if (categories.length === 0) {
        container.innerHTML = '<p class="text-center">No categories available</p>';
        return;
    }

    container.innerHTML = categories.map(category => `
        <a href="/category/${category.slug || category.id}" class="category-tile">
            <div class="category-icon">
                ${category.icon ?
            `<img src="${category.icon}" alt="${escapeHtml(category.name)}" loading="lazy">` :
            `<div class="category-icon-placeholder">${category.name.charAt(0)}</div>`
        }
            </div>
            <div class="category-name">${escapeHtml(category.name)}</div>
        </a>
    `).join('');
}

/**
 * Render mobile categories
 */
function renderMobileCategories(categories) {
    const container = document.getElementById('mobileCategories');
    if (!container) return;

    container.innerHTML = categories.map(category => `
        <a href="/category/${category.slug || category.id}" class="mobile-category-item">
            ${escapeHtml(category.name)}
        </a>
    `).join('');
}

/**
 * Load and render banners
 */
async function loadBanners() {
    try {
        const response = await api.getBanners();

        if (response.success && response.data) {
            renderBanners(response.data);
        }
    } catch (error) {
        console.error('Failed to load banners:', error);
    }
}

/**
 * Render banners/carousel
 */
function renderBanners(banners) {
    const slidesContainer = document.querySelector('.carousel-slides');
    const dotsContainer = document.getElementById('carouselDots');

    if (!slidesContainer) return;

    if (banners.length === 0) {
        slidesContainer.innerHTML = `
            <div class="carousel-slide active">
                <div class="slide-content">
                    <h2>Welcome to RetailRow</h2>
                    <p>Your one-stop shop for quality products</p>
                </div>
            </div>
        `;
        return;
    }

    slidesContainer.innerHTML = banners.map((banner, index) => `
        <div class="carousel-slide ${index === 0 ? 'active' : ''}">
            ${banner.image ?
            `<img src="${banner.image}" alt="${escapeHtml(banner.title || 'Banner')}" loading="${index === 0 ? 'eager' : 'lazy'}">` :
            `<div class="slide-placeholder">
                    <h2>${escapeHtml(banner.title || 'Featured Banner')}</h2>
                    ${banner.subtitle ? `<p>${escapeHtml(banner.subtitle)}</p>` : ''}
                </div>`
        }
            ${banner.link && banner.link !== '#' ?
            `<a href="${banner.link}" class="slide-overlay"></a>` :
            ''
        }
        </div>
    `).join('');

    // Clear and rebuild dots
    if (dotsContainer) {
        dotsContainer.innerHTML = '';
    }
}

/**
 * Load flash sales
 */
async function loadFlashSales() {
    try {
        const response = await api.getFlashSaleProducts();

        if (response.success && response.data) {
            renderFlashSales(response.data);
        }
    } catch (error) {
        console.error('Failed to load flash sales:', error);
    }
}

/**
 * Render flash sale products
 */
function renderFlashSales(products) {
    const container = document.getElementById('flashScroll');
    if (!container) return;

    if (products.length === 0) {
        container.innerHTML = '<p class="text-center">No flash sales available right now</p>';
        return;
    }

    container.innerHTML = products.map(product => createProductCard(product, true)).join('');
}

/**
 * Load featured products
 */
async function loadFeaturedProducts() {
    try {
        const response = await api.getFeaturedProducts();

        if (response.success && response.data) {
            renderFeaturedProducts(response.data);
        }
    } catch (error) {
        console.error('Failed to load featured products:', error);
    }
}

/**
 * Render featured products
 */
function renderFeaturedProducts(products) {
    const container = document.getElementById('featuredProducts');
    if (!container) return;

    if (products.length === 0) {
        container.innerHTML = '<p class="text-center">No featured products available</p>';
        return;
    }

    container.innerHTML = products.map(product => createProductCard(product)).join('');
}

/**
 * Create product card HTML
 */
function createProductCard(product, isFlashSale = false) {
    const currencySymbol = window.retailRowSettings?.currency_symbol || 'GH₵';
    const discount = product.flash_discount || product.discount || 0;
    const finalPrice = discount > 0 ?
        (product.price * (1 - discount / 100)).toFixed(2) :
        product.price.toFixed(2);

    return `
        <div class="product-card" data-product-id="${product.id}">
            ${discount > 0 ? `<div class="discount-badge">-${discount}%</div>` : ''}
            <a href="/product/${product.slug || product.id}" class="product-link">
                <div class="product-image">
                    ${product.image ?
            `<img src="${product.image}" alt="${escapeHtml(product.name)}" loading="lazy">` :
            `<div class="product-image-placeholder">${product.name.charAt(0)}</div>`
        }
                </div>
                <div class="product-info">
                    <h3 class="product-name">${escapeHtml(product.name)}</h3>
                    <div class="product-price">
                        <span class="current-price">${currencySymbol} ${finalPrice}</span>
                        ${product.old_price || discount > 0 ?
            `<span class="old-price">${currencySymbol} ${product.price.toFixed(2)}</span>` :
            ''
        }
                    </div>
                    ${product.stock !== undefined ?
            `<div class="product-stock ${product.stock > 0 ? 'in-stock' : 'out-of-stock'}">
                            ${product.stock > 0 ? `${product.stock} in stock` : 'Out of stock'}
                        </div>` :
            ''
        }
                </div>
            </a>
            <button class="add-to-cart-btn" data-product-id="${product.id}" ${product.stock === 0 ? 'disabled' : ''}>
                ${product.stock === 0 ? 'Out of Stock' : 'Add to Cart'}
            </button>
        </div>
    `;
}

/**
 * Load footer links
 */
async function loadFooterLinks() {
    try {
        const response = await api.getFooterLinks();

        if (response.success && response.data) {
            renderFooterLinks(response.data);
        }
    } catch (error) {
        console.error('Failed to load footer links:', error);
    }
}

/**
 * Render footer links
 */
function renderFooterLinks(groups) {
    const container = document.getElementById('footerLinks');
    if (!container) return;

    container.innerHTML = groups.map(group => `
        <div class="footer-column">
            <h4>${escapeHtml(group.title)}</h4>
            <ul>
                ${(group.links || []).map(link => `
                    <li><a href="${link.url}">${escapeHtml(link.label)}</a></li>
                `).join('')}
            </ul>
        </div>
    `).join('');
}

/**
 * Load navigation menus
 */
async function loadNavigationMenus() {
    try {
        // Load utility menu
        const utilityResponse = await api.getMenus('utility');
        if (utilityResponse.success && utilityResponse.data) {
            renderUtilityMenu(utilityResponse.data);
        }
    } catch (error) {
        console.error('Failed to load navigation menus:', error);
    }
}

/**
 * Render utility menu
 */
function renderUtilityMenu(menu) {
    const container = document.querySelector('.utility-inner');
    if (!container || !menu || !menu.items) return;

    const items = menu.items.map((item, index) => `
        ${index > 0 ? '<div class="utility-divider"></div>' : ''}
        <a href="${item.url}" class="utility-link" ${item.target === '_blank' ? 'target="_blank" rel="noopener noreferrer"' : ''}>
            ${escapeHtml(item.label)}
        </a>
    `).join('');

    container.innerHTML = items;
}

/**
 * Initialize search functionality
 */
function initializeSearch() {
    const searchForm = document.querySelector('.search-form');
    const searchInput = searchForm?.querySelector('input[type="search"]');

    if (!searchForm || !searchInput) return;

    searchForm.addEventListener('submit', async (e) => {
        e.preventDefault();

        const query = searchInput.value.trim();
        if (!query) return;

        // Redirect to search results page
        window.location.href = `/search?q=${encodeURIComponent(query)}`;
    });
}

/**
 * Initialize carousel
 */
function initializeCarousel() {
    const slidesEl = document.querySelector('.carousel-slides');
    const dotsEl = document.getElementById('carouselDots');

    if (!slidesEl) return;

    const slides = [...slidesEl.children];
    if (slides.length === 0) return;

    let idx = 0;
    const autoplayInterval = 5000;

    // Create dots if they don't exist
    if (dotsEl && dotsEl.children.length !== slides.length) {
        dotsEl.innerHTML = '';
        slides.forEach((_, i) => {
            const btn = document.createElement('button');
            btn.setAttribute('aria-label', `Go to slide ${i + 1}`);
            btn.addEventListener('click', () => goTo(i));
            if (i === 0) btn.classList.add('active');
            dotsEl.appendChild(btn);
        });
    }

    const dots = dotsEl ? [...dotsEl.children] : [];

    function goTo(i) {
        idx = i;
        update();
        resetTimer();
    }

    function update() {
        if (slidesEl) {
            slidesEl.style.transform = `translateX(${-idx * 100}%)`;
        }
        if (dots.length > 0) {
            dots.forEach((dot, i) => {
                dot.classList.toggle('active', i === idx);
            });
        }
    }

    let timer = setInterval(() => {
        idx = (idx + 1) % slides.length;
        update();
    }, autoplayInterval);

    function resetTimer() {
        clearInterval(timer);
        timer = setInterval(() => {
            idx = (idx + 1) % slides.length;
            update();
        }, autoplayInterval);
    }

    // Pause on hover
    const carouselEl = document.querySelector('.hero-carousel');
    if (carouselEl) {
        carouselEl.addEventListener('mouseenter', () => clearInterval(timer));
        carouselEl.addEventListener('mouseleave', resetTimer);
    }

    // Initial update
    update();
}

/**
 * Initialize flash sale countdown timer
 */
function initializeFlashTimer() {
    const flashTimer = document.getElementById('flashTimer');
    if (!flashTimer) return;

    function updateFlash() {
        const now = new Date();
        const end = new Date();
        end.setHours(end.getHours() + 4); // 4 hours from now

        const diff = Math.max(0, end - now);
        const h = String(Math.floor(diff / 3600000)).padStart(2, '0');
        const m = String(Math.floor((diff % 3600000) / 60000)).padStart(2, '0');
        const s = String(Math.floor((diff % 60000) / 1000)).padStart(2, '0');

        flashTimer.textContent = `${h}h : ${m}m : ${s}s`;
    }

    updateFlash();
    setInterval(updateFlash, 1000);
}

/**
 * Initialize scroll controls for flash sales
 */
function initializeScrollControls() {
    const flashScroll = document.getElementById('flashScroll');
    const scrollLeft = document.querySelector('.scroll-arrow.left');
    const scrollRight = document.querySelector('.scroll-arrow.right');

    if (!flashScroll) return;

    if (scrollLeft) {
        scrollLeft.addEventListener('click', () => {
            flashScroll.scrollBy({ left: -220, behavior: 'smooth' });
        });
    }

    if (scrollRight) {
        scrollRight.addEventListener('click', () => {
            flashScroll.scrollBy({ left: 220, behavior: 'smooth' });
        });
    }

    // Make draggable
    let isDown = false;
    let startX;
    let scrollLeftPos;

    flashScroll.addEventListener('mousedown', (e) => {
        isDown = true;
        flashScroll.style.cursor = 'grabbing';
        startX = e.pageX - flashScroll.offsetLeft;
        scrollLeftPos = flashScroll.scrollLeft;
    });

    window.addEventListener('mouseup', () => {
        isDown = false;
        flashScroll.style.cursor = 'grab';
    });

    flashScroll.addEventListener('mousemove', (e) => {
        if (!isDown) return;
        e.preventDefault();
        const x = e.pageX - flashScroll.offsetLeft;
        const walk = (x - startX);
        flashScroll.scrollLeft = scrollLeftPos - walk;
    });
}

/**
 * Initialize cart functionality
 */
/*
function initializeCart() {
    const cartToggle = document.getElementById('cartToggle');
    const cartPanel = document.getElementById('cartPanel');
    const closeCart = document.getElementById('closeCart');
    const overlay = document.getElementById('overlay');

    if (cartToggle && cartPanel) {
        cartToggle.addEventListener('click', () => {
            const open = cartPanel.classList.toggle('open');
            cartToggle.setAttribute('aria-expanded', String(open));
            document.body.style.overflow = open ? 'hidden' : '';
            showOverlay(open);
        });
    }

    if (closeCart && cartPanel) {
        closeCart.addEventListener('click', () => {
            cartPanel.classList.remove('open');
            cartToggle?.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
            showOverlay(false);
        });
    }

    if (overlay) {
        overlay.addEventListener('click', () => {
            closeAllPanels();
        });
    }

    // Handle add to cart clicks
    document.addEventListener('click', (e) => {
        const addToCartBtn = e.target.closest('.add-to-cart-btn');
        if (addToCartBtn) {
            e.preventDefault();
            const productId = addToCartBtn.dataset.productId;
            addToCart(productId);
        }
    });
}
*/

/**
 * Add product to cart
 */
function addToCart(productId) {
    console.log('Adding product to cart:', productId);

    // Update cart count
    const cartCount = document.getElementById('cartCount');
    if (cartCount) {
        const currentCount = parseInt(cartCount.textContent) || 0;
        cartCount.textContent = currentCount + 1;
    }

    // Show notification
    showNotification('Product added to cart!');
}

/**
 * Initialize mobile menu
 */
function initializeMobileMenu() {
    const hamburger = document.getElementById('hamburger');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const drawerClose = document.getElementById('drawerClose');

    if (hamburger && mobileDrawer) {
        hamburger.addEventListener('click', () => {
            const open = mobileDrawer.classList.toggle('open');
            hamburger.setAttribute('aria-expanded', String(open));
            document.body.style.overflow = open ? 'hidden' : '';
            showOverlay(open);
        });
    }

    if (drawerClose && mobileDrawer) {
        drawerClose.addEventListener('click', () => {
            mobileDrawer.classList.remove('open');
            hamburger?.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
            showOverlay(false);
        });
    }
}

/**
 * Initialize back to top button
 */
function initializeBackToTop() {
    const backToTop = document.getElementById('backToTop');
    if (!backToTop) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 400) {
            backToTop.classList.add('show');
        } else {
            backToTop.classList.remove('show');
        }
    });

    backToTop.addEventListener('click', () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    });
}

/**
 * Utility Functions
 */

function showOverlay(show) {
    const overlay = document.getElementById('overlay');
    if (!overlay) return;
    overlay.classList.toggle('show', !!show);
}

function closeAllPanels() {
    const cartPanel = document.getElementById('cartPanel');
    const mobileDrawer = document.getElementById('mobileDrawer');

    if (cartPanel) cartPanel.classList.remove('open');
    if (mobileDrawer) mobileDrawer.classList.remove('open');

    document.body.style.overflow = '';
    showOverlay(false);
}

function showGlobalLoader(show) {
    let loader = document.getElementById('globalLoader');

    if (show && !loader) {
        loader = document.createElement('div');
        loader.id = 'globalLoader';
        loader.className = 'global-loader';
        loader.innerHTML = '<div class="loader-spinner"></div>';
        document.body.appendChild(loader);
    } else if (!show && loader) {
        loader.remove();
    }
}

function showNotification(message, type = 'success') {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.textContent = message;
    document.body.appendChild(notification);

    setTimeout(() => {
        notification.classList.add('show');
    }, 10);

    setTimeout(() => {
        notification.classList.remove('show');
        setTimeout(() => notification.remove(), 300);
    }, 3000);
}

function showErrorMessage(message) {
    showNotification(message, 'error');
}

function updateElement(selector, content) {
    const element = document.querySelector(selector);
    if (element && content) {
        element.textContent = content;
    }
}

function updateWhatsAppButton(number) {
    const whatsappBtn = document.getElementById('whatsappBtn');
    if (whatsappBtn) {
        whatsappBtn.addEventListener('click', (e) => {
            e.preventDefault();
            const msg = encodeURIComponent('Hello, I need support with RetailRow.');
            const url = `https://wa.me/${number.replace(/\D/g, '')}?text=${msg}`;
            window.open(url, '_blank');
        });
    }
}

function escapeHtml(text) {
    const div = document.createElement('div');
    div.textContent = text;
    return div.innerHTML;
}

// Handle ESC key to close panels
window.addEventListener('keyup', (e) => {
    if (e.key === 'Escape') {
        closeAllPanels();
    }
});
