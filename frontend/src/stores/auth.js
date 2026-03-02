import { defineStore } from 'pinia';
import apiClient from '../services/api';

export const useAuthStore = defineStore('auth', {
    state: () => ({
        user: null,
        token: localStorage.getItem('token'),
        role: localStorage.getItem('role'),
    }),
    getters: {
        isAuthenticated: (state) => !!state.token,
        isAdmin: (state) => state.role === 'admin',
        isTeacher: (state) => state.role === 'teacher',
        isStudent: (state) => state.role === 'student',
    },
    actions: {
        async login(credentials) {
            const response = await apiClient.post('/login', credentials);
            this.setAuth(response.data);
            return response;
        },
        async register(userData) {
            const response = await apiClient.post('/register', userData);
            this.setAuth(response.data);
            return response;
        },
        async logout() {
            try {
                await apiClient.post('/logout');
            } catch (error) {
                console.error('Logout failed', error);
            } finally {
                this.purgeAuth();
            }
        },
        setAuth(data) {
            this.user = data.user;
            this.token = data.token;
            this.role = data.role;
            localStorage.setItem('token', data.token);
            localStorage.setItem('role', data.role);
            apiClient.defaults.headers.common['Authorization'] = `Bearer ${data.token}`;
        },
        purgeAuth() {
            this.user = null;
            this.token = null;
            this.role = null;
            localStorage.removeItem('token');
            localStorage.removeItem('role');
            delete apiClient.defaults.headers.common['Authorization'];
        },
        async fetchUser() {
            if (this.token) {
                try {
                    const response = await apiClient.get('/user');
                    this.user = response.data;
                } catch (error) {
                    this.purgeAuth();
                }
            }
        }
    }
});
