import * as _vue from 'vue';
import _test from './components/test';
import _app from './components/App';
const app = _vue.createApp({});
app.component('test', _test);
app.component('App', _app);
app.mount('#app');
