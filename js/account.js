/**
 * Account Dashboard Logic
 */
const account = {
    init: async () => {
        // Check auth status
        const user = await auth.checkUser();

        const loader = document.getElementById('page-loader');
        if (loader) loader.classList.add('hidden');

        if (user) {
            // Show Dashboard
            document.getElementById('dashboard-section').classList.remove('hidden');
            account.setupAvatarUpload();
            account.renderSidebar(user);

            // Load initial section based on hash or default to profile
            const hash = window.location.hash.replace('#', '') || 'profile';
            account.loadSection(hash);
        } else {
            // Show Login
            document.getElementById('auth-section').classList.remove('hidden');

            // Check if user clicked "Register" from dropdown
            if (window.location.hash === '#register') {
                if (typeof app !== 'undefined' && app.toggleAuth) {
                    app.toggleAuth('register');
                }
            }
        }
    },

    renderSidebar: (user) => {
        const nameEl = document.getElementById('sidebar-name');
        const emailEl = document.getElementById('sidebar-email');
        const avatarEl = document.getElementById('sidebar-avatar');

        if (nameEl) nameEl.textContent = user.name;
        if (emailEl) emailEl.textContent = user.email;
        if (avatarEl && user.avatar) {
            avatarEl.src = 'assets/uploads/' + user.avatar;
        }
    },

    loadSection: async (section) => {
        // Update active link
        document.querySelectorAll('.sidebar-menu a').forEach(el => el.classList.remove('active'));
        const activeLink = document.querySelector(`.sidebar-menu a[href="#${section}"]`);
        if (activeLink) activeLink.classList.add('active');

        const view = document.getElementById('account-view');
        if (!view) return;

        view.innerHTML = '<div class="text-center"><i class="fas fa-spinner fa-spin fa-2x"></i></div>';

        switch (section) {
            case 'profile':
                await account.renderProfile(view);
                break;
            case 'orders':
                await account.renderOrders(view);
                break;
            case 'addresses':
                view.innerHTML = '<h3>Address Book</h3><p>Address management coming soon.</p>';
                break;
            case 'wishlist':
                view.innerHTML = '<h3>My Wishlist</h3><p>Your wishlist is empty.</p>';
                break;
            case 'security':
                view.innerHTML = '<h3>Security Settings</h3><p>Password change coming soon.</p>';
                break;
            default:
                account.renderProfile(view);
        }
    },

    renderProfile: async (container) => {
        try {
            // Fetch fresh profile data
            const response = await api.request('/user/profile.php');

            if (!response.success) {
                throw new Error(response.message || 'Failed to load profile');
            }

            const data = response.data;

            container.innerHTML = `
                <h3 class="mb-4">My Profile</h3>
                <form id="profile-form" onsubmit="account.updateProfile(event)">
                    <div class="row" style="display:grid; grid-template-columns: 1fr 1fr; gap: 15px;">
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="name" class="form-control" value="${data.name || ''}" required>
                        </div>
                        <div class="form-group">
                            <label>Email</label>
                            <input type="email" class="form-control" value="${data.email || ''}" disabled>
                            <small class="text-muted">Email cannot be changed</small>
                        </div>
                        <div class="form-group">
                            <label>Phone Number</label>
                            <input type="tel" name="phone" class="form-control" value="${data.phone || ''}">
                        </div>
                        <div class="form-group">
                            <label>Date of Birth</label>
                            <input type="date" name="dob" class="form-control" value="${data.dob || ''}">
                        </div>
                    </div>
                    <div class="profile-form-actions">
                        <button type="submit" class="profile-save-btn">
                            <i class="fas fa-save"></i> Save Changes
                        </button>
                    </div>
                </form>
            `;
        } catch (error) {
            console.error('Profile error:', error);
            container.innerHTML = `<div class="alert alert-danger">Error loading profile: ${error.message}. Please login again.</div>`;
        }
    },

    updateProfile: async (e) => {
        e.preventDefault();
        const formData = new FormData(e.target);
        const data = Object.fromEntries(formData.entries());
        const submitBtn = e.target.querySelector('button[type="submit"]');
        const originalBtnText = submitBtn ? submitBtn.textContent : '';

        try {
            if (submitBtn) {
                submitBtn.disabled = true;
                submitBtn.textContent = 'Saving...';
            }

            const response = await api.request('/user/profile.php', {
                method: 'PUT',
                body: JSON.stringify(data)
            });

            if (response.success) {
                alert('Profile updated successfully!');
            } else {
                throw new Error(response.message || 'Update failed');
            }
        } catch (error) {
            alert('Failed to update profile: ' + error.message);
        } finally {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.textContent = originalBtnText;
            }
        }
    },

    setupAvatarUpload: () => {
        const avatarInput = document.getElementById('avatar-input');
        if (!avatarInput) return;

        avatarInput.addEventListener('change', async (e) => {
            const file = e.target.files[0];
            if (!file) return;

            // 1. Preview image
            const reader = new FileReader();
            const avatarImg = document.getElementById('sidebar-avatar');
            const originalSrc = avatarImg.src; // Store original source to revert on failure

            reader.onload = (event) => {
                avatarImg.src = event.target.result;
            };
            reader.readAsDataURL(file);

            // 2. Upload image
            const formData = new FormData();
            formData.append('avatar', file);

            try {
                const response = await fetch('api/user/upload_avatar.php', {
                    method: 'POST',
                    body: formData,
                });

                const result = await response.json();

                if (response.ok && result.success) {
                    alert('Profile picture updated!');
                    if (auth.user) {
                        auth.user.avatar = result.data.filename;
                    }
                } else {
                    throw new Error(result.message || 'Upload failed');
                }
            } catch (error) {
                alert('Error uploading image: ' + error.message);
                avatarImg.src = originalSrc;
            }
        });
    },
};

account.renderOrders = async (container) => {
    container.innerHTML = '<h3>My Orders</h3>';
    try {
        const response = await api.request('/orders.php');
        if (!response.success || !response.data) {
            throw new Error(response.message || 'Could not fetch orders.');
        }

        if (response.data.length === 0) {
            container.innerHTML += '<p>You have not placed any orders yet.</p>';
            return;
        }

        const ordersHtml = response.data.map(order => {
            const statusClass = (order.status || 'pending').toLowerCase().replace(/ /g, '-');
            const statusText = (order.status || 'pending').replace(/_/g, ' ');

            return `
            <div class="order-card">
                <div class="order-header">
                    <span>Order #${String(order.id).padStart(6, '0')}</span>
                    <span class="order-status status-${statusClass}">${statusText}</span>
                </div>
                <div class="order-body">
                    <p><strong>Date:</strong> ${new Date(order.created_at).toLocaleDateString()}</p>
                    <p><strong>Total:</strong> GH₵ ${parseFloat(order.total_amount).toFixed(2)}</p>
                    <p><strong>Items:</strong> ${order.item_count}</p>
                </div>
                <div class="order-footer">
                    <a href="track-order.php?id=${order.id}" class="btn btn-secondary">View Details</a>
                </div>
            </div>
        `}).join('');

        container.innerHTML += `<div class="orders-list">${ordersHtml}</div>`;

    } catch (error) {
        console.error('Error loading orders:', error);
        container.innerHTML += `<div class="alert alert-danger">Could not load your orders. Please try again later.</div>`;
    }
};

// Initialize on load
document.addEventListener('DOMContentLoaded', account.init);