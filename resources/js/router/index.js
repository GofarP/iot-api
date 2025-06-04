import { createRouter, createWebHistory } from 'vue-router';
import Employee from '../components/Employee.vue';

const routes = [
    { path: '/employee', name: 'employee', component: Employee }
];

const router = createRouter({
    history: createWebHistory(),
    routes,
});



export default router;