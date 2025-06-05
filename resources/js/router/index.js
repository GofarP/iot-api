import { createRouter, createWebHistory } from 'vue-router';
import Employee from '../components/Employee.vue';
import NotFound from '../components/NotFound.vue';


const routes = [
    { path: '/', name: 'employee', component: Employee },
    {
        path: '/:pathMatch(.*)*',
        name: 'NotFound',
        component: NotFound
    },

];

const router = createRouter({
    history: createWebHistory(),
    routes,
});



export default router;