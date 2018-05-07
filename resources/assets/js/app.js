
/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');
window.moment = require('moment');


/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */
import VueResource from 'vue-resource';
Vue.use(VueResource);
Vue.http.headers.common['X-CSRF-TOKEN'] = document.querySelector('#token').getAttribute('content');

import vSelect from 'vue-select';

Vue.component('v-select', vSelect);
Vue.component('example-component', require('./components/ExampleComponent.vue'));
Vue.component('app-user', require('./components/user.component.vue'));
Vue.component('app-entity', require('./components/entity.component.vue'));
Vue.component('app-versions', require('./components/versions.component.vue'));


Vue.filter('dateFormat', (value, onlyDate) => { 
   if (value) {
        if(onlyDate)
            return moment(String(value)).format('DD.MM.YYYY');
        else
            return moment(String(value)).format('DD.MM.YYYY HH:mm');
   }
});

Vue.filter('timeFormat', (value) => {
   if (value) {
        return moment(String(value)).format('HH:mm');
   }
});