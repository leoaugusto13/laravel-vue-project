import { createRouter, createWebHistory } from 'vue-router';
import Login from '../components/Login.vue';
import Register from '../components/Register.vue';
import ForgotPassword from '../components/ForgotPassword.vue';
import Users from '../components/admin/Users.vue';
import Inscricoes from '../components/Inscricoes.vue';
import AdminCourses from '../components/admin/AdminCourses.vue';
import DashboardStats from '../components/admin/DashboardStats.vue';
import SystemLogs from '../components/admin/SystemLogs.vue';
import Reports from '../components/admin/Reports.vue';
import Home from '../components/Home.vue';
import AdminTrainings from '../components/admin/AdminTrainings.vue';
import AdminDirectorates from '../components/admin/AdminDirectorates.vue';
import AdminModalities from '../components/admin/AdminModalities.vue';
import AdminTargetAudiences from '../components/admin/AdminTargetAudiences.vue';

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
            },
            {
                path: 'trainings',
                name: 'admin-trainings',
                component: AdminTrainings
            },
            {
                path: 'directorates',
                name: 'admin-directorates',
                component: AdminDirectorates
            },
            {
                path: 'modalities',
                name: 'admin-modalities',
                component: AdminModalities
            },
            {
                path: 'target-audiences',
                name: 'admin-target-audiences',
                component: AdminTargetAudiences
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
