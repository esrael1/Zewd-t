import { createRouter, createWebHistory } from 'vue-router';
import { useAuthStore } from '../stores/auth';

const routes = [
    { path: '/', name: 'Home', component: () => import('../views/Home.vue') },
    { path: '/login', name: 'Login', component: () => import('../views/Login.vue') },
    { path: '/register', name: 'Register', component: () => import('../views/Register.vue') },

    // Admin
    {
        path: '/admin',
        name: 'AdminDashboard',
        component: () => import('../views/admin/Dashboard.vue'),
        meta: { requiresAuth: true, role: 'admin' }
    },

    // Teacher
    {
        path: '/teacher',
        name: 'TeacherDashboard',
        component: () => import('../views/teacher/Dashboard.vue'),
        meta: { requiresAuth: true, role: 'teacher' }
    },

    // Student
    {
        path: '/student',
        name: 'StudentDashboard',
        component: () => import('../views/student/Dashboard.vue'),
        meta: { requiresAuth: true, role: 'student' }
    },
    {
        path: '/live-class/:meetingId',
        name: 'LiveClass',
        component: () => import('../views/LiveClass.vue'),
        meta: { requiresAuth: true }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});

router.beforeEach((to, from, next) => {
    const authStore = useAuthStore();

    if (to.meta.requiresAuth) {
        if (!authStore.isAuthenticated) {
            next('/login');
        } else if (to.meta.role && authStore.role !== to.meta.role) {
            // Redirect to correct dashboard based on role
            if (authStore.isAdmin) next('/admin');
            else if (authStore.isTeacher) next('/teacher');
            else if (authStore.isStudent) next('/student');
            else next('/');
        } else {
            next();
        }
    } else {
        next();
    }
});

export default router;
