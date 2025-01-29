import { createRouter, createWebHistory } from 'vue-router';
import Dashboard from '@/views/Dashboard.vue';
import Categories from '@/views/Categories.vue';
import CafeOwners from '@/views/CafeOwners.vue';
import OwnerCategories from '@/views/OwnerCategories.vue';
import Products from '@/views/Products.vue';

const routes = [
    { path: '/', component: Dashboard },
    { path: '/admin/categories', component: Categories },
    { path: '/admin/cafe-owners', component: CafeOwners },
    { path: '/admin/products', component: Products },
    { path: '/owners/:id', component: OwnerCategories, props: true },
];

const router = createRouter({
    history: createWebHistory(),
    routes
});

export default router;
