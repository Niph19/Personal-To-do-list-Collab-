/**
 * Auth Guard - Client-side token validation
 * FE: Check token, redirect if missing/invalid
 */

const AuthGuard = {
    /**
     * Check if user has valid token
     */
    hasToken() {
        const token = localStorage.getItem('tempo.token');
        const user = sessionStorage.getItem('tempo.user');
        return !!(token && user);
    },

    /**
     * Get stored token
     */
    getToken() {
        return localStorage.getItem('tempo.token');
    },

    /**
     * Get stored user
     */
    getUser() {
        const user = sessionStorage.getItem('tempo.user');
        return user ? JSON.parse(user) : null;
    },

    /**
     * Save auth data after login
     */
    setAuth(token, user) {
        localStorage.setItem('tempo.token', token);
        sessionStorage.setItem('tempo.user', JSON.stringify(user));
    },

    /**
     * Clear auth data on logout
     */
    clearAuth() {
        localStorage.removeItem('tempo.token');
        sessionStorage.removeItem('tempo.user');
    },

    /**
     * Guard: redirect to login if no token
     * Use on dashboard page
     */
    requireAuth() {
        if (!this.hasToken()) {
            window.location.href = '/login';
            return false;
        }
        return true;
    },

    /**
     * Guard: redirect to dashboard if already logged in
     * Use on login/register pages
     */
    requireGuest() {
        if (this.hasToken()) {
            window.location.href = '/dashboard';
            return false;
        }
        return true;
    },

    /**
     * Get Authorization header for API requests
     */
    getAuthHeader() {
        const token = this.getToken();
        return token ? { 'Authorization': `Bearer ${token}` } : {};
    }
};

// Export for use in other scripts
window.AuthGuard = AuthGuard;
