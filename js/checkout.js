document.addEventListener('DOMContentLoaded', () => {

    const checkoutContainer = document.getElementById('checkout-container');
    if (!checkoutContainer) {
        // Not on the checkout page, do nothing.
        return;
    }
    
    let cartData = {};
    let currentStep = 1;
    const totalSteps = 3;

    const checkoutState = {
        customer: {},
        shipping: {},
        payment: {}
    };

    // --- INITIALIZATION ---
    async function initializeCheckout() {
        try {
            const response = await fetch('api/cart/get.php');
            if (!response.ok) throw new Error('Failed to load cart.');
            cartData = await response.json();

            if (!cartData.items || cartData.items.length === 0) {
                checkoutContainer.innerHTML = '<div class="cart-empty"><h2>Your Cart is Empty</h2><p>Please add items to your cart before proceeding to checkout.</p><a href="/" class="step-btn step-btn-primary">Continue Shopping</a></div>';
                return;
            }
            
            renderCheckoutLayout();
            renderOrderSummary();
            setupEventListeners();
            showStep(currentStep);

        } catch (error) {
            console.error('Checkout initialization failed:', error);
            checkoutContainer.innerHTML = '<div class="cart-empty"><h2>Error</h2><p>Could not load checkout. Please try again later.</p></div>';
        }
    }

    // --- RENDER FUNCTIONS ---
    function renderCheckoutLayout() {
        checkoutContainer.innerHTML = `
            <div class="checkout-main">
                <!-- Progress Bar -->
                <div class="checkout-progress">
                    <div class="progress-step" data-step="1">
                        <div class="step-number">1</div>
                        Customer
                    </div>
                    <div class="progress-step" data-step="2">
                        <div class="step-number">2</div>
                        Shipping
                    </div>
                    <div class="progress-step" data-step="3">
                        <div class="step-number">3</div>
                        Payment
                    </div>
                </div>

                <form id="checkout-form">
                    <!-- Step 1: Customer Info -->
                    <div class="checkout-step" data-step="1">
                        <h2 class="step-title">Customer Information</h2>
                        <div class="form-group">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" id="name" name="name" class="form-input" required>
                        </div>
                        <div class="form-grid">
                            <div class="form-group">
                                <label for="email" class="form-label">Email</label>
                                <input type="email" id="email" name="email" class="form-input" required>
                            </div>
                            <div class="form-group">
                                <label for="phone" class="form-label">Phone Number</label>
                                <input type="tel" id="phone" name="phone" class="form-input" required>
                            </div>
                        </div>
                        <div class="step-actions">
                            <button type="button" class="step-btn step-btn-primary" data-action="next">Continue to Shipping</button>
                        </div>
                    </div>

                    <!-- Step 2: Shipping -->
                    <div class="checkout-step" data-step="2">
                        <h2 class="step-title">Shipping Details</h2>
                        <div class="form-group">
                            <label for="address" class="form-label">Address</label>
                            <input type="text" id="address" name="address" class="form-input" required>
                        </div>
                        <div class="form-grid">
                             <div class="form-group">
                                <label for="city" class="form-label">City</label>
                                <input type="text" id="city" name="city" class="form-input" required>
                            </div>
                             <div class="form-group">
                                <label for="region" class="form-label">Region</label>
                                <input type="text" id="region" name="region" class="form-input" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="delivery_notes" class="form-label">Delivery Notes (Optional)</label>
                            <textarea id="delivery_notes" name="delivery_notes" class="form-input"></textarea>
                        </div>
                        <div class="step-actions">
                            <button type="button" class="step-btn step-btn-secondary" data-action="prev">Back to Customer</button>
                            <button type="button" class="step-btn step-btn-primary" data-action="next">Continue to Payment</button>
                        </div>
                    </div>

                    <!-- Step 3: Payment -->
                    <div class="checkout-step" data-step="3">
                        <h2 class="step-title">Payment Method</h2>
                        <div class="payment-options">
                            <label class="payment-option selected">
                                <input type="radio" name="payment_method" value="momo" checked>
                                <strong>Mobile Money (MTN, Telecel, AT)</strong>
                            </label>
                            <label class="payment-option">
                                <input type="radio" name="payment_method" value="cod">
                                <strong>Cash on Delivery</strong>
                            </label>
                        </div>
                        <div class="step-actions">
                             <button type="button" class="step-btn step-btn-secondary" data-action="prev">Back to Shipping</button>
                             <button type="submit" class="step-btn step-btn-primary">Place Order</button>
                        </div>
                    </div>
                </form>
            </div>
            <aside class="checkout-summary">
                <h2 class="summary-title">Order Summary</h2>
                <div id="summary-items"></div>
                <div class="summary-divider"></div>
                <div id="summary-totals"></div>
            </aside>
        `;
    }

    function renderOrderSummary() {
        const itemsContainer = document.getElementById('summary-items');
        const totalsContainer = document.getElementById('summary-totals');
        
        itemsContainer.innerHTML = cartData.items.map(item => `
            <div class="summary-item">
                <img src="${item.image || 'assets/images/placeholder.jpg'}" alt="${item.name}" class="summary-item-img">
                <div class="summary-item-info">
                    <span class="summary-item-title">${item.name} (x${item.quantity})</span>
                </div>
                <span class="summary-item-total">GH₵ ${item.line_total.toFixed(2)}</span>
            </div>
        `).join('');

        totalsContainer.innerHTML = `
            <div class="summary-total-row">
                <span>Subtotal</span>
                <span>GH₵ ${cartData.subtotal.toFixed(2)}</span>
            </div>
            <div class="summary-total-row">
                <span>Tax</span>
                <span>GH₵ ${cartData.tax.toFixed(2)}</span>
            </div>
            <div class="summary-total-row">
                <span>Shipping</span>
                <span>GH₵ ${cartData.shipping.toFixed(2)}</span>
            </div>
            <div class="summary-divider"></div>
            <div class="summary-total-row summary-grand-total">
                <span>Total</span>
                <span>GH₵ ${cartData.total.toFixed(2)}</span>
            </div>
        `;
    }

    // --- STEP NAVIGATION & VALIDATION ---
    function showStep(stepNumber) {
        document.querySelectorAll('.checkout-step').forEach(step => step.classList.remove('active'));
        document.querySelector(`.checkout-step[data-step="${stepNumber}"]`).classList.add('active');

        document.querySelectorAll('.progress-step').forEach(step => {
            const stepNum = parseInt(step.dataset.step);
            step.classList.remove('active', 'completed');
            if (stepNum < stepNumber) {
                step.classList.add('completed');
            } else if (stepNum === stepNumber) {
                step.classList.add('active');
            }
        });
        currentStep = stepNumber;
    }
    
    function validateStep(stepNumber) {
        const step = document.querySelector(`.checkout-step[data-step="${stepNumber}"]`);
        const inputs = step.querySelectorAll('input[required], select[required]');
        let isValid = true;
        inputs.forEach(input => {
            if (!input.checkValidity()) {
                // You can add more detailed validation feedback here
                input.classList.add('is-invalid');
                isValid = false;
            } else {
                input.classList.remove('is-invalid');
            }
        });
        return isValid;
    }


    // --- EVENT LISTENERS ---
    function setupEventListeners() {
        const form = document.getElementById('checkout-form');
        form.addEventListener('submit', handleFormSubmit);

        document.querySelectorAll('[data-action="next"]').forEach(btn => {
            btn.addEventListener('click', () => {
                if (validateStep(currentStep)) {
                    showStep(currentStep + 1);
                }
            });
        });
        
        document.querySelectorAll('[data-action="prev"]').forEach(btn => {
            btn.addEventListener('click', () => showStep(currentStep - 1));
        });

        document.querySelectorAll('.payment-option').forEach(option => {
            option.addEventListener('click', () => {
                document.querySelectorAll('.payment-option').forEach(o => o.classList.remove('selected'));
                option.classList.add('selected');
                option.querySelector('input[type="radio"]').checked = true;
            });
        });
    }

    // --- FORM SUBMISSION ---
    async function handleFormSubmit(e) {
        e.preventDefault();
        if (!validateStep(currentStep)) {
            return;
        }

        const formData = new FormData(e.target);
        const orderData = Object.fromEntries(formData.entries());

        try {
            const response = await fetch('api/checkout/create_order.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(orderData)
            });

            const result = await response.json();

            if (!response.ok) {
                throw new Error(result.message || 'An unknown error occurred.');
            }
            
            // Redirect to a confirmation page
            window.location.href = `order_confirmation.php?order_id=${result.order_id}`;

        } catch (error) {
            console.error('Order submission failed:', error);
            alert(`Error: ${error.message}`);
        }
    }

    // --- START ---
    initializeCheckout();
});
