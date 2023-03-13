import Vue from 'vue'
require('../bootstrap');


import { BButton, BImg, BModal,VBModal  } from 'bootstrap-vue'
Vue.component('b-modal', BModal)
Vue.component('b-button', BButton)
Vue.component('b-img', BImg)



import Swal from 'sweetalert2'
window.Swal = Swal;

import VueRouter from 'vue-router'
Vue.use(VueRouter)
import {routes} from './routes';

import store from '../store'

import tablecom from '../table.vue'
Vue.component('TableComponent', tablecom);


import Prodibedondakhil from './components/sonod/prodibedon_dakhil.vue'
Vue.component('Prodibedondakhil', Prodibedondakhil);




Vue.prototype.$localStorage = localStorage;

import loader from "vue-ui-preloader";

Vue.use(loader);

import Multiselect from 'vue-multiselect'


// register globally
Vue.component('multiselect', Multiselect)

window.Reload = new Vue();
const router = new VueRouter({
  routes,
  mode: 'history'
})
const app = new Vue({
    el: '#app',
    router,
    store
});


