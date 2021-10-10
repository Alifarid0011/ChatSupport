const chatButton = document.querySelector('.chatbox__button');
const chatContent = document.querySelector('.chatbox__support');
const icons = {
    isClicked: '<img width="40px" src="{{../images/icons/chatbox-icon.svg}}" />',
    isNotClicked: `<img width="40px" src="{{asset('assets/Chat/images/icons/chat.svg')}} />`
}
const chatbox = new InteractiveChatbox(chatButton, chatContent, icons);
chatbox.display();
chatbox.toggleIcon(true, chatButton);