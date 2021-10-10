require('./bootstrap');
require('alpinejs');
import * as _vue from 'vue';
import _Chat from './components/Chat';
import _ChatUser from './components/ChatUser';
import _ChatGuest from './components/chatGuest';

const chat = _vue.createApp({});
chat.component("Chat", _Chat);
chat.component("ChatUser", _ChatUser);
chat.component("ChatGuest", _ChatGuest);
chat.mount('#chat');
