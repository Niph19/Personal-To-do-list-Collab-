/**
 * API Helper - Handle all API calls with Bearer token
 * FE: Automatically attach token to every request
 */

const API = {
    baseUrl: '/api',
    useMock: false,

    mockKey() {
        const user = AuthGuard.getUser();
        const userId = user?.id ?? user?.email?.toLowerCase() ?? 'guest';
        return `tempo.mock.tasks.${encodeURIComponent(userId)}`;
    },

    /**
     * Make API request with token
     */
    async request(path, options = {}) {
        // Mock mode for development
        if (this.useMock) {
            return this._mockRequest(path, options);
        }

        // Real API call with Bearer token
        try {
            const response = await fetch(this.baseUrl + path, {
                ...options,
                headers: {
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                    ...AuthGuard.getAuthHeader(), // Attach Bearer token
                    ...(options.headers || {})
                },
                ...(options.body ? { body: JSON.stringify(options.body) } : {})
            });

            const data = await response.json().catch(() => ({}));

            if (!response.ok) {
                // Handle 401 Unauthorized - token invalid/expired
                if (response.status === 401 && AuthGuard.getToken()) {
                    AuthGuard.clearAuth();
                    window.location.href = '/login';
                    throw new Error('Sesi telah berakhir. Silakan login kembali.');
                }
                
                const validationMessages = Object.values(data.errors || {}).flat();
                throw new Error(validationMessages.join(' ') || data.message || 'Permintaan gagal.');
            }

            // Return data wrapper or full response
            return data.data !== undefined ? data.data : data;
        } catch (error) {
            console.error('API Error:', error);
            throw error;
        }
    },

    /**
     * Mock request handler (for development without BE)
     */
    async _mockRequest(path, opts = {}) {
        // Load tasks
        if (path === '/tugas' && (!opts.method || opts.method === 'GET')) {
            const mockKey = this.mockKey();
            const cached = localStorage.getItem(mockKey);
            if (cached) return JSON.parse(cached);
            
            const response = await fetch('/mock/tasks.json');
            if (!response.ok) throw new Error('Mock tidak dapat dimuat.');
            const data = await response.json();
            localStorage.setItem(mockKey, JSON.stringify(data));
            return data;
        }

        // Login
        if (path === '/login' && opts.method === 'POST') {
            return {
                user: {
                    id: 'mock_' + opts.body.email.trim().toLowerCase(),
                    name: opts.body.email.split('@')[0],
                    email: opts.body.email
                },
                token: 'mock_token_' + opts.body.email.trim().toLowerCase()
            };
        }

        // Register
        if (path === '/register' && opts.method === 'POST') {
            return {
                user: {
                    id: 'mock_' + opts.body.email.trim().toLowerCase(),
                    name: opts.body.name,
                    email: opts.body.email
                }
            };
        }

        // Create task
        if (path === '/tugas' && opts.method === 'POST') {
            const mockKey = this.mockKey();
            const tasks = JSON.parse(localStorage.getItem(mockKey) || '[]');
            const newTask = { id: 'task_' + Date.now(), ...opts.body };
            tasks.unshift(newTask);
            localStorage.setItem(mockKey, JSON.stringify(tasks));
            return newTask;
        }

        // Update task
        if (path.startsWith('/tugas/') && opts.method === 'PUT') {
            const id = path.split('/')[2];
            const mockKey = this.mockKey();
            const tasks = JSON.parse(localStorage.getItem(mockKey) || '[]');
            const task = tasks.find(t => t.id === id);
            if (task) {
                Object.assign(task, opts.body);
                localStorage.setItem(mockKey, JSON.stringify(tasks));
                return task;
            }
            throw new Error('Tugas tidak ditemukan.');
        }

        // Delete task
        if (path.startsWith('/tugas/') && opts.method === 'DELETE') {
            const id = path.split('/')[2];
            const mockKey = this.mockKey();
            let tasks = JSON.parse(localStorage.getItem(mockKey) || '[]');
            tasks = tasks.filter(t => t.id !== id);
            localStorage.setItem(mockKey, JSON.stringify(tasks));
            return { success: true };
        }

        // Logout
        if (path === '/logout' && opts.method === 'POST') {
            return { success: true };
        }

        return null;
    },

    /**
     * Shorthand methods
     */
    get(path) {
        return this.request(path);
    },

    post(path, body) {
        return this.request(path, { method: 'POST', body });
    },

    put(path, body) {
        return this.request(path, { method: 'PUT', body });
    },

    delete(path) {
        return this.request(path, { method: 'DELETE' });
    }
};

window.API = API;
