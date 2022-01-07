require('./bootstrap');

window.Vue = require('vue');
window.moment = require('moment');
Vue.prototype.moment = moment;

Vue.component('web-app', require('./views/WebApp').default);

const app = new Vue({
    el: '#app',
});
