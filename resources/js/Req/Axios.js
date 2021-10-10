import axios from 'axios';
const Axios=axios.create({
  baseURL:'http://chat.local/'
});
Axios.defaults.withCredentials = true;

export default Axios;