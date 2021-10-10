require('./bootstrap');
require('alpinejs');
import { createApp } from 'vue';
// import App from './components/App'
import sel from './components/sel'
// import { MultiSelectPlugin } from "@syncfusion/ej2-vue-dropdowns";
// import VueNextSelect from 'vue-next-select'
import SelectVue from './components/SelectVue'
const app = createApp({});
// app.component('App', App);
// app.component('ejs-multiselect', MultiSelectPlugin);
// app.component('vue-select', VueNextSelect)
app.mount('#App');
// Vue.createApp({
//   data() {
//     return {
//       selected: ''
//     }
//   }
// }).component('sel',sel).mount('#sel')

