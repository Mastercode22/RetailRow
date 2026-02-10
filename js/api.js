/**
 * RetailRow API Service
 * Centralized API communication layer for frontend
 * Handles all backend requests with proper error handling and loading states
 */

class RetailRowAPI {
    constructor() {
        // Environment-based API URL configuration
        this.baseURL = this.getAPIBaseURL();
        this.timeout = 15000; // 15 seconds timeout
    }

    /**
     * Get API base URL based on environment
     */
    getAPIBaseURL() {
        // Check if running on production, staging, or local
        const hostname = window.location.hostname;

        if (hostname === 'localhost' || hostname === '127.0.0.1') {
            // Local development
            return window.location.origin + '/RetailRow/api';
        } else if (hostname.includes('staging') || hostname.includes('test')) {
            // Staging environment
            return window.location.origin + '/api';
        } else {
            // Production
            return window.location.origin + '/api';
        }
    }

    /**
     * Generic request handler with timeout and error handling
     */
    async request(endpoint, options = {}) {
        const controller = new AbortController();
        const timeoutId = setTimeout(() => controller.abort(), this.timeout);

        try {
            const url = `${this.baseURL}${endpoint}`;
            console.log('API Request:', url, options); // Debug log

            const defaultOptions = {
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                },
                signal: controller.signal,
            };

            const response = await fetch(url, { ...defaultOptions, ...options });
            clearTimeout(timeoutId);

            console.log('API Response Status:', response.status); // Debug log

            // Try to parse JSON response
            let data;
            try {
                data = await response.json();
                console.log('API Response Data:', data); // Debug log
            } catch (parseError) {
                console.error('JSON Parse Error:', parseError);
                throw new Error(`Server returned invalid JSON. Status: ${response.status}`);
            }

            // Check if response is OK (200-299)
            if (!response.ok) {
                // Throw error with message from server if available
                throw new Error(data.message || `HTTP error! status: ${response.status}`);
            }

            // For successful responses, return the data as-is
            // The data should already have 'success' property from PHP
            return data;

        } catch (error) {
            clearTimeout(timeoutId);

            if (error.name === 'AbortError') {
                throw new Error('Request timeout - please check your connection');
            }

            console.error('API Error:', error);
            throw error;
        }
    }

    // ===== SETTINGS API =====
    async getSettings(keys = null) {
        const params = keys ? `?keys=${Array.isArray(keys) ? keys.join(',') : keys}` : '';
        return this.request(`/settings.php${params}`);
    }

    async getSetting(key) {
        const data = await this.getSettings(key);
        return data.data[key] || null;
    }

    // ===== CATEGORIES API =====
    async getCategories() {
        return this.request('/categories.php');
    }

    async getCategory(id) {
        return this.request(`/categories.php?id=${id}`);
    }

    // ===== PRODUCTS API =====
    async getProducts(params = {}) {
        const queryString = new URLSearchParams(params).toString();
        return this.request(`/products.php${queryString ? '?' + queryString : ''}`);
    }

    async getFeaturedProducts() {
        return this.getProducts({ type: 'featured' });
    }

    async getFlashSaleProducts() {
        return this.getProducts({ type: 'flash_sale' });
    }

    async getProductsByCategory(categoryId) {
        return this.getProducts({ type: 'category', category_id: categoryId });
    }

    async getProduct(id) {
        return this.request(`/products.php?id=${id}`);
    }

    async searchProducts(query) {
        return this.request(`/products.php?search=${encodeURIComponent(query)}`);
    }

    // ===== BANNERS API =====
    async getBanners() {
        return this.request('/banners.php');
    }

    // ===== FLASH SALES API =====
    async getFlashSales() {
        return this.request('/flash-sales.php');
    }

    async getActiveFlashSale() {
        return this.request('/flash-sales.php?active=1');
    }

    // ===== PAGES API =====
    async getPages() {
        return this.request('/pages.php');
    }

    async getPage(slugOrId) {
        const param = isNaN(slugOrId) ? `slug=${slugOrId}` : `id=${slugOrId}`;
        return this.request(`/pages.php?${param}`);
    }

    async getFooterPages() {
        return this.request('/pages.php?footer=1');
    }

    // ===== NAVIGATION API =====
    async getMenus(location = null) {
        const params = location ? `?location=${location}` : '';
        return this.request(`/navigation.php${params}`);
    }

    async getMenu(id) {
        return this.request(`/navigation.php?id=${id}`);
    }

    // ===== FOOTER LINKS API =====
    async getFooterLinks() {
        return this.request('/footer-links.php');
    }

    // ===== HOMEPAGE SECTIONS API =====
    async getHomepageSections() {
        return this.request('/homepage-sections.php');
    }

    // ===== ORDERS API (Future) =====
    async createOrder(orderData) {
        return this.request('/orders.php', {
            method: 'POST',
            body: JSON.stringify(orderData),
        });
    }

    async getOrder(id) {
        return this.request(`/orders.php?id=${id}`);
    }

    async trackOrder(orderNumber) {
        return this.request(`/orders.php?track=${orderNumber}`);
    }
}

// Create singleton instance
const api = new RetailRowAPI();

// Export for use in other scripts
if (typeof module !== 'undefined' && module.exports) {
    module.exports = RetailRowAPI;
}