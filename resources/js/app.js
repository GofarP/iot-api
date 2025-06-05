import { createApp } from "vue";
import App from './components/App.vue';
import router from './router';


const app = createApp(App);
app.use();
app.use(router);
app.mount("#app");