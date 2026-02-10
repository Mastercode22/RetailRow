/**
 * Authentication Service
 */
const auth = {
    user: null,

    checkUser: async () => {
        try {
            const response = await api.request('/auth/me.php');
            if (response.success && response.authenticated) {
                auth.user = response.user;
                return response.user;
            }
        } catch (e) {
            console.log('User not logged in:', e.message);
        }
        return null;
    },

    login: async (email, password) => {
        try {
            const response = await api.request('/auth/login.php', {
                method: 'POST',
                body: JSON.stringify({ email, password })
            });

            if (response.success) {
                // Login successful, reload page
                window.location.reload();
            } else {
                throw new Error(response.message || 'Login failed');
            }
        } catch (error) {
            console.error('Login error:', error);
            throw error; // Re-throw to be handled by form
        }
    },

    register: async (name, email, password) => {
        try {
            const response = await api.request('/auth/register.php', {
                method: 'POST',
                body: JSON.stringify({ name, email, password })
            });

            if (response.success) {
                return response;
            } else {
                throw new Error(response.message || 'Registration failed');
            }
        } catch (error) {
            console.error('Registration error:', error);
            throw error; // Re-throw to be handled by form
        }
    },

    logout: async () => {
        try {
            await api.request('/auth/logout.php');
        } catch (e) {
            console.error('Logout error:', e);
        } finally {
            window.location.href = 'index.php';
        }
    }
};

// Event Listeners for Forms
document.addEventListener('DOMContentLoaded', () => {
    const loginForm = document.getElementById('login-form');
    if (loginForm) {
        loginForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn ? submitBtn.textContent : '';

            try {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Logging in...';
                }

                await auth.login(formData.get('email'), formData.get('password'));
            } catch (error) {
                alert(error.message || 'Login failed. Please check your credentials.');

                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                }
            }
        });
    }

    const registerForm = document.getElementById('register-form');
    if (registerForm) {
        registerForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const formData = new FormData(e.target);
            const submitBtn = e.target.querySelector('button[type="submit"]');
            const originalBtnText = submitBtn ? submitBtn.textContent : '';

            try {
                if (submitBtn) {
                    submitBtn.disabled = true;
                    submitBtn.textContent = 'Creating account...';
                }

                await auth.register(
                    formData.get('name'),
                    formData.get('email'),
                    formData.get('password')
                );

                alert('Registration successful! Please login.');

                // Switch to login form if app.toggleAuth exists
                if (typeof app !== 'undefined' && app.toggleAuth) {
                    app.toggleAuth('login');
                }
            } catch (error) {
                alert(error.message || 'Registration failed. Please try again.');

                if (submitBtn) {
                    submitBtn.disabled = false;
                    submitBtn.textContent = originalBtnText;
                }
            }
        });
    }
});