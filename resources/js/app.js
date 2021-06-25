// Vue
window.Vue = require('vue').default;

// Bootstrap and jQuery
window.$ = window.jQuery = require('jquery');
window.Popper = require('popper.js').default;
require('bootstrap');

// Axios
window.axios = require('axios');
window.axios.defaults.headers.common['Accept'] = 'application/json';

// Vue auto components
const files = require.context('./components/', true, /\.vue$/i);
files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue single components
// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

// Start
const app = new Vue().$mount('#app');
