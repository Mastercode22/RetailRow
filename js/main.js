// Enhanced RetailRow JavaScript - Jumia Clone
document.addEventListener('DOMContentLoaded', () => {
    // Carousel
    const slidesEl = document.querySelector('.carousel-slides');
    const slides = [...document.querySelectorAll('.carousel-slide')];
    const dotsEl = document.getElementById('carouselDots');
    let idx = 0, autoplay = 5000;

    if (slides.length > 0 && dotsEl) {
        slides.forEach((s, i) => {
            const btn = document.createElement('button');
            btn.addEventListener('click', () => goTo(i));
            if (i === 0) btn.classList.add('active');
            dotsEl.appendChild(btn);
        });

        function goTo(i) {
            idx = i;
            update();
            resetTimer();
        }

        function update() {
            if (slidesEl) {
                slidesEl.style.transform = `translateX(${-idx * 100}%)`;
            }
            Array.from(dotsEl.children).forEach((b, bi) => b.classList.toggle('active', bi === idx));
        }

        let timer = setInterval(() => { idx = (idx + 1) % slides.length; update() }, autoplay);

        function resetTimer() {
            clearInterval(timer);
            timer = setInterval(() => { idx = (idx + 1) % slides.length; update() }, autoplay);
        }

        // Pause on hover
        const carouselEl = document.querySelector('.hero-carousel');
        if (carouselEl) {
            carouselEl.addEventListener('mouseenter', () => clearInterval(timer));
            carouselEl.addEventListener('mouseleave', () => {
                timer = setInterval(() => { idx = (idx + 1) % slides.length; update() }, autoplay);
            });
        }
    }

    // Flash countdown timer (4 hours from now)
    const flashTimer = document.getElementById('flashTimer');
    if (flashTimer) {
        function updateFlash() {
            const now = new Date();
            const end = new Date();
            end.setHours(end.getHours() + 4);
            const diff = Math.max(0, end - now);
            const h = String(Math.floor(diff / 3600000)).padStart(2, '0');
            const m = String(Math.floor(diff % 3600000 / 60000)).padStart(2, '0');
            const s = String(Math.floor(diff % 60000 / 1000)).padStart(2, '0');
            flashTimer.textContent = `${h}h : ${m}m : ${s}s`;
        }
        updateFlash();
        setInterval(updateFlash, 1000);
    }

    // Flash products horizontal scroll
    const flashScroll = document.getElementById('flashScroll');
    const scrollLeft = document.querySelector('.scroll-arrow.left');
    const scrollRight = document.querySelector('.scroll-arrow.right');

    if (scrollLeft && flashScroll) {
        scrollLeft.addEventListener('click', () => {
            flashScroll.scrollBy({ left: -220, behavior: 'smooth' });
        });
    }

    if (scrollRight && flashScroll) {
        scrollRight.addEventListener('click', () => {
            flashScroll.scrollBy({ left: 220, behavior: 'smooth' });
        });
    }

    // Make flash scroll draggable (mouse)
    if (flashScroll) {
        let isDown = false, startX, scrollLeftPos, isDragging = false;

        flashScroll.addEventListener('mousedown', e => {
            isDown = true;
            isDragging = false;
            flashScroll.style.cursor = 'grabbing';
            startX = e.pageX - flashScroll.offsetLeft;
            scrollLeftPos = flashScroll.scrollLeft;
        });

        window.addEventListener('mouseup', (e) => {
            isDown = false;
            flashScroll.style.cursor = 'grab';

            if (isDragging) {
                const preventClick = (e) => {
                    e.preventDefault();
                    e.stopPropagation();
                    window.removeEventListener('click', preventClick, true);
                };
                window.addEventListener('click', preventClick, true);
            }
            isDragging = false;
        });

        flashScroll.addEventListener('mousemove', e => {
            if (!isDown) return;
            const x = e.pageX - flashScroll.offsetLeft;
            const walk = (x - startX);

            if (Math.abs(walk) > 5) {
                isDragging = true;
                e.preventDefault();
                flashScroll.scrollLeft = scrollLeftPos - walk;
            }
        });

        // Touch support
        let tStartX = 0, tScrollLeft = 0;

        flashScroll.addEventListener('touchstart', e => {
            tStartX = e.touches[0].pageX - flashScroll.offsetLeft;
            tScrollLeft = flashScroll.scrollLeft;
        });

        flashScroll.addEventListener('touchmove', e => {
            const x = e.touches[0].pageX - flashScroll.offsetLeft;
            const walk = (x - tStartX);
            flashScroll.scrollLeft = tScrollLeft - walk;
        });
    }

    /*
    // Cart panel
    const cartToggle = document.getElementById('cartToggle');
    const cartPanel = document.getElementById('cartPanel');
    const closeCart = document.getElementById('closeCart');
    const cartCount = document.getElementById('cartCount');
    const overlay = document.getElementById('overlay');

    function showOverlay(show) {
        if (!overlay) return;
        overlay.classList.toggle('show', !!show);
        overlay.setAttribute('aria-hidden', String(!show));
    }

    if (cartToggle && cartPanel) {
        cartToggle.addEventListener('click', () => {
            const open = cartPanel.classList.toggle('open');
            cartToggle.setAttribute('aria-expanded', String(open));
            document.body.style.overflow = open ? 'hidden' : '';
            cartPanel.setAttribute('aria-hidden', String(!open));
            showOverlay(open);
        });
    }

    if (closeCart && cartPanel) {
        closeCart.addEventListener('click', () => {
            cartPanel.classList.remove('open');
            if (cartToggle) cartToggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
            cartPanel.setAttribute('aria-hidden', 'true');
            showOverlay(false);
        });
    }
    */

    // Mobile drawer
    const hamburger = document.getElementById('hamburger');
    const mobileDrawer = document.getElementById('mobileDrawer');
    const drawerClose = document.getElementById('drawerClose');

    if (hamburger && mobileDrawer) {
        hamburger.addEventListener('click', () => {
            const open = mobileDrawer.classList.toggle('open');
            hamburger.setAttribute('aria-expanded', String(open));
            mobileDrawer.setAttribute('aria-hidden', String(!open));
            document.body.style.overflow = open ? 'hidden' : '';
            showOverlay(open);
        });
    }

    if (drawerClose && mobileDrawer) {
        drawerClose.addEventListener('click', () => {
            mobileDrawer.classList.remove('open');
            if (hamburger) hamburger.setAttribute('aria-expanded', 'false');
            mobileDrawer.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            showOverlay(false);
        });
    }

    // Overlay click closes both
    if (overlay) {
        overlay.addEventListener('click', () => {
            /*
            if (cartPanel) {
                cartPanel.classList.remove('open');
                cartPanel.setAttribute('aria-hidden', 'true');
            }
            */
            if (mobileDrawer) {
                mobileDrawer.classList.remove('open');
                mobileDrawer.setAttribute('aria-hidden', 'true');
            }
            if (hamburger) hamburger.setAttribute('aria-expanded', 'false');
            if (cartToggle) cartToggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
            showOverlay(false);
        });
    }

    // WhatsApp button
    const wa = document.getElementById('whatsappBtn');
    if (wa) {
        wa.addEventListener('click', (e) => {
            e.preventDefault();
            const msg = encodeURIComponent('Hello, I need support with RetailRow Ghana.');
            const url = `https://wa.me/233506552368?text=${msg}`;
            window.open(url, '_blank');
        });
    }

    /*
    // Simulate add to cart (demo)
    const cartList = document.getElementById('cartList');
    const subtotalEl = document.getElementById('subtotal');
    const checkoutTotal = document.getElementById('checkoutTotal');
    const productCards = document.querySelectorAll('.product-card');

    if (productCards.length > 0) {
        productCards.forEach((card, i) => {
            card.addEventListener('click', (e) => {
                // Don't trigger if clicking on a link inside the card
                if (e.target.tagName === 'A') return;

                let count = Number(cartCount.textContent) || 0;
                count++;
                if (cartCount) cartCount.textContent = count;

                // Add simple cart item
                if (cartList) {
                    const existingEmpty = cartList.querySelector('.cart-empty');
                    if (existingEmpty) existingEmpty.remove();

                    const item = document.createElement('div');
                    item.className = 'cart-item';
                    item.style.padding = '12px 0';
                    item.style.borderBottom = '1px solid #ededed';
                    item.innerHTML = `
                        <div style="display: flex; gap: 12px; align-items: center;">
                            <div style="width: 60px; height: 60px; background: #f5f5f5; border-radius: 4px;"></div>
                            <div style="flex: 1;">
                                <div style="font-size: 13px; margin-bottom: 4px;">Product Item</div>
                                <div style="font-weight: 700; color: #1c1d1f;">GH₵ 99.00</div>
                            </div>
                        </div>
                    `;
                    cartList.appendChild(item);
                }

                const newTotal = (99 * count).toFixed(2);
                if (subtotalEl) subtotalEl.textContent = newTotal;
                if (checkoutTotal) checkoutTotal.textContent = newTotal;
            });
        });
    }
    */

    // Close with Escape key
    window.addEventListener('keyup', e => {
        if (e.key === 'Escape') {
            /*
            if (cartPanel) {
                cartPanel.classList.remove('open');
                cartPanel.setAttribute('aria-hidden', 'true');
            }
            */
            if (mobileDrawer) {
                mobileDrawer.classList.remove('open');
                mobileDrawer.setAttribute('aria-hidden', 'true');
            }
            if (hamburger) hamburger.setAttribute('aria-expanded', 'false');
            if (cartToggle) cartToggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
            showOverlay(false);
        }
    });

    // Back to top button
    const backToTop = document.getElementById('backToTop');
    if (backToTop) {
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

    // Search Auto-suggest
    const searchInput = document.getElementById('searchInput');
    const suggestionsBox = document.getElementById('searchSuggestions');

    if (searchInput && suggestionsBox) {
        let debounceTimer;

        searchInput.addEventListener('input', function () {
            const query = this.value.trim();

            clearTimeout(debounceTimer);

            if (query.length < 2) {
                suggestionsBox.classList.remove('show');
                suggestionsBox.innerHTML = '';
                return;
            }

            // Debounce to prevent too many API calls
            debounceTimer = setTimeout(() => {
                fetch(`api/search_suggestions.php?q=${encodeURIComponent(query)}`)
                    .then(response => response.json())
                    .then(data => {
                        if (data.success && data.data.length > 0) {
                            const html = data.data.map(product => `
                                <div class="suggestion-item" onclick="window.location.href='product.php?id=${product.id}'">
                                    <img src="${product.image || 'assets/images/placeholder.jpg'}" class="suggestion-image" alt="${product.name}">
                                    <div class="suggestion-info">
                                        <div class="suggestion-name">${product.name}</div>
                                        <div class="suggestion-price">GH₵ ${parseFloat(product.price).toFixed(2)}</div>
                                    </div>
                                </div>
                            `).join('');
                            suggestionsBox.innerHTML = html;
                            suggestionsBox.classList.add('show');
                        } else {
                            suggestionsBox.classList.remove('show');
                        }
                    })
                    .catch(err => console.error('Search suggestion error:', err));
            }, 300);
        });

        // Hide suggestions when clicking outside
        document.addEventListener('click', function (e) {
            if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
                suggestionsBox.classList.remove('show');
            }
        });
    }

    // Product Image Zoom
    const zoomContainer = document.querySelector('.product-zoom-container');
    const zoomImage = document.querySelector('.product-zoom-image');

    if (zoomContainer && zoomImage) {
        zoomContainer.addEventListener('mousemove', function (e) {
            const { left, top, width, height } = this.getBoundingClientRect();
            const x = (e.clientX - left) / width * 100;
            const y = (e.clientY - top) / height * 100;

            zoomImage.style.transformOrigin = `${x}% ${y}%`;
            zoomImage.style.transform = 'scale(2)';
        });

        zoomContainer.addEventListener('mouseleave', function () {
            zoomImage.style.transformOrigin = 'center center';
            zoomImage.style.transform = 'scale(1)';
        });
    }
});