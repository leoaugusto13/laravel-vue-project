import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import ForgotPassword from '../components/ForgotPassword.vue';
import Users from '../components/Users.vue';
import Inscricoes from '../components/Inscricoes.vue';
import AdminCourses from '../components/AdminCourses.vue';
import DashboardStats from '../components/DashboardStats.vue';
import SystemLogs from '../components/SystemLogs.vue';
import Reports from '../components/Reports.vue';
import Home from '../components/Home.vue';

const routes = [
    {
        path: '/',
        redirect: '/login'
    },
    {
        path: '/login',
        name: 'login',
        component: Login
    },
    {
        path: '/register',
        name: 'register',
        component: Register
    },
    {
        path: '/forgot-password',
        name: 'forgot-password',
        component: ForgotPassword
    },
    {
        path: '/admin',
        component: Home,
        children: [
            {
                path: 'dashboard',
                alias: '',
                name: 'dashboard',
                component: DashboardStats
            },
            {
                path: 'users',
                name: 'users',
                component: Users,
                beforeEnter: (to, from, next) => {
                    const user = JSON.parse(localStorage.getItem('user'));
                    if (user && user.role === 'admin') {
                        next();
                    } else {
                        next('/admin/dashboard');
                    }
                }
            },
            {
                path: 'inscricoes',
                name: 'inscricoes',
                component: Inscricoes
            },
            {
                path: 'courses',
                name: 'admin-courses',
                component: AdminCourses
            },
            {
                path: 'logs',
                name: 'admin-logs',
                component: SystemLogs
            },
            {
                path: 'reports',
                name: 'admin-reports',
                component: Reports
            }
        ]
    },
    {
        path: '/portal',
        component: Home,
        children: [
            {
                path: 'inscricoes',
                name: 'portal-inscricoes',
                component: Inscricoes
            }
        ],
        beforeEnter: (to, from, next) => {
            const user = JSON.parse(localStorage.getItem('user'));
            if (user) { // Any logged user can access portal, or restrict to non-admins if preferred.
                next();
            } else {
                next('/login');
            }
        }
    },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
