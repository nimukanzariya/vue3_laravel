import AuthLayout from '@/components/layouts/AuthLayout.vue';
import DashboardLayout from '@/components/layouts/DashboardLayout.vue';
import BulkStock from '@/components/pages/bulkStock.vue';
import CreateStock from '@/components/pages/CreateStock.vue';
import LoginPage from '@/components/pages/LoginPage.vue';
import StockPage from '@/components/pages/StockPage.vue';
import { createRouter, createWebHistory } from 'vue-router';


const router = createRouter({
    history: createWebHistory(),
    routes: [
        {
            path: "/",
            component: DashboardLayout,
            children: [
                {
                    path: "/",
                    component: StockPage
                },
                {
                    path: "/create-stock",
                    component: CreateStock
                },
                {
                    path: "/bulk-stock",
                    component: BulkStock
                },
            ]
        },
        {
            path: "/",
            component: AuthLayout,
            children: [
                {
                    path: "/login",
                    component: LoginPage
                },
            ]
        },
    ]
});


router.beforeEach((to, from, next) => {
    const isAuthenticated = !!localStorage.getItem("active-user");

    if (!isAuthenticated && to.path !== "/login") {
        next("/login");
    } else if (isAuthenticated && to.path === "/login") {
        next("/");
    } else {
        next();
    }
});

export default router;