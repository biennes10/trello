import { registerVueControllerComponents } from '@symfony/ux-vue';
// import * as VueRouter from 'vue-router';
// import { createApp } from 'vue';
// import App from './vue/controllers/Welcome.vue'

// createApp(App).mount('#vue-app');

import './bootstrap.js';
/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

// createApp(app).mount('#app');

registerVueControllerComponents(require.context('./vue/controllers', true, /\.vue$/));

// document.addEventListener('vue:before-mount', (event) => {
// const router = VueRouter.createRouter({
//     history: VueRouter.createWebHistory(),
//     routes: [
//        { path: '/survey/list', component: './vue/controllers/ListSurveys.vue' },
//        { path: '/survey/create', component: CreateSurvey },
//        { path: '/survey/edit/:surveyId', component: EditSurvey },
//     ],
// });

// });
// app.use(router);

