<template>
  <div>
    <h1 class="text-center display-2 bg-yellow-100">کاربر</h1>
    <div class="container">
      <h3 class=" text-center">

      </h3>
      <div class="messaging">
        <div class="inbox_msg">
          <div class="mesgs">
            <!--       #   بخش پيام ها ي بين پشتيباني و كاربران-->
            <div class="msg_history scrollable" ref="inputRef">
              <!--پيام هاي كاربر-->

              <div>

                <div v-for="(message,index) in messages" :v-key="index" class="messageForScroll">
                  <div>
                    <div v-if="message[0].is_support_user===false" >
                      <div class="incoming_msg" :id="index+1">
                        <div class="incoming_msg_img">
                          <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil">
                        </div>
                        <span>
                        شما:
                        </span>
                        <div class="received_msg">
                          <div class="received_withd_msg">
                            <p>
                              {{ message.messages.Message }}
                            </p>
                            <span class="time_date">{{ message.messages.make_at }}</span></div>
                        </div>
                      </div>
                    </div>
                    <!--                # پيام هاي كاربر-->
                    <!--                پيام هاي پشتيباني-->
                    <div v-if="message[0].is_support_user===true" dir="auto">
                      <div class="outgoing_msg">
                        <div class="sent_msg">
                          <span>پشتيباني:</span>
                          <p>
                            {{ message.messages.Message }}
                          </p>
                          <span class="time_date"> {{ message.messages.make_at }}</span></div>
                      </div>
                    </div>
                    <!--            #  پيام هاي پشتيباني-->
                  </div>


                </div>
                <!--                Rate-->
                <!--                <form @submit.prevent="sendRate()"></form>-->
                <div class="rate" v-if="ended" >
                  اين مكالمه پايان يافت لطفا امتياز خود را نسبت به پاسخ گويي كارشناس وارد كنيد:
                  <br>
                  <input type="radio" id="star5"      @click.once="sendRate(account.id,Support_id,5)"  />
                  <label for="star5" title="text">5 stars</label>
                  <input type="radio" id="star4" name="rate"  @click.once="sendRate(account.id,Support_id,4)" />
                  <label for="star4" title="text">4 stars</label>
                  <input type="radio" id="star3" name="rate"  @click.once="sendRate(account.id,Support_id,3)" />
                  <label for="star3" title="text">3 stars</label>
                  <input type="radio" id="star2" name="rate"  @click.once="sendRate(account.id,Support_id,2)" />
                  <label for="star2" title="text">2 stars</label>
                  <input type="radio" id="star1" name="rate"  @click.once="sendRate(account.id,Support_id,1)" />
                  <label for="star1" title="text">1 star</label>
                </div>
                <!--                #rate-->
                <span v-if="activeUser">{{ activeUser.user }} is typing ...</span>


              </div>
              <!--              <ul class="messages" v-chat-scroll>-->
              <!--              </ul>-->
            </div>

            <!--       #   بخش پيام ها ي بين پشتيباني و كاربران-->
            <div class="type_msg">
              <div class="input_msg_write">
                <input
                    @keydown="TypingEvent()"
                    v-model="newMessage"
                    @keyup.enter="SendMessage(for_user,Support_id)"
                    type="text"
                    class="write_msg"
                    placeholder="Type a message"
                />
                <button class="msg_send_btn" type="button"><i class="fa fa-paper-plane-o" aria-hidden="true"></i>
                </button>
              </div>
            </div>
          </div>
        </div>


      </div>
    </div>

  </div>
</template>

<script>

export default {
  name: 'ChatUser',
  data() {
    return {
      rate: null,
      messages: [],
      users: [],
      IsOpen: null,
      account: [],
      Support_id: null,
      for_user: null,
      newMessage: '',
      activeUser: false,
      typingTimer: false,
      fourResentMessage: [],
      ended: false
    };
  },
  mounted() {
    window.Echo.channel('Chat').listen('SendMessage', (event) => {
      // if (event.message.receive_id === this.account.id) {
      //   this.Support_id = event.message.support_id;
      //   this.fetchMessages(this.for_user,event.message.support_id);
      // }
      this.fetchMessages(this.for_user, this.Support_id,"User");
    });
    // if (rate!==null){
    //   console.log('ok');
    // }
    this.Joining();
    this.IsOpen();
  },
  created() {
    this.fetchMessages(this.for_user, this.Support_id,"User");
    this.showUser();
    Echo.join('Chat')
        .here(user => {
          this.users = user;
        }).joining(user => {
      this.users.push(user);
    }).leaving(user => {
      this.users = this.users.filter(u => u.id != user.id);
    }).listenForWhisper('typing', response => {
      this.activeUser=this.account.name;
      console.log(response);
    });

  },
  methods: {
 async   sendRate(user_id,support_id,rate) {
   let data={
     user_id : user_id,
     support_id:support_id,
     rate:rate
   }
   let dataForEnd = {
     receive_id: user_id,
     support_id: support_id,
   };

      await axios.post('rateToSupport',data).then(res=>{
        if (res.data.message==="rate were recorded"){
          axios.post('endChat', dataForEnd);
        }
      })
    },
    Joining() {
      Echo.join('Chat')
          .here(user => {
            // this.getUsers(user);
          }).joining(user => {
        axios.post('supportable', {user_id: user.id}).then(res => {
          if (res.data[0]) {
            if (res.data[1] === null || res.data[1] === this.account.id)
              this.users.push(user);
          }
        });
      }).leaving(user => {
        this.users = this.users.filter(u => u.id != user.id);
      }).listenForWhisper('typing', user => {
        // console.log(user);
        if (this.account.id === user.forUser) {
          this.activeUser = user;
          if (this.typingTimer) {
            clearTimeout(this.typingTimer);
          }
          this.typingTimer = setTimeout(() => {
            this.activeUser = false;
          }, 3000);
        }
      }).listenForWhisper('ending', user => {
        if (user.user_id === this.account.id) {
          this.ended = user.end;

          setTimeout(()=>{
            let dataForEnd = {
              receive_id: this.for_user,
              support_id: this.Support_id,
            };
            axios.post('endChat', dataForEnd);
            this.ended=false;
          },3000)
        }
      });
    },
    async TypingEvent() {
      if (this.IsOpen[1] == null)
        await this.isOpen(this.account.id);
      if (this.IsOpen[0] && this.IsOpen[1] != null) {
        Echo.join('Chat')
            .whisper('typing', this.account);
      }
    },
    selectUser(user, support) {
      this.for_user = user;
      this.Support_id = support;
      this.fetchMessages(user, support,"User");
    },
    async fetchMessages(user, support,role) {
      await axios.get(`messages?user_id=${user}&support_id=${support}&role=${role}`).then(response => {
        this.messages = response.data;
      });
      // if (this.fourResentMessage.length !== 2) {
      //   this.fourResentMessage.push(this.messages[this.messages.length - 1].created_at);
      // } else {
      //   this.fourResentMessage.shift();
      // }
      // if (this.fourResentMessage[0] === this.fourResentMessage[1]) {
      //   this.Ban();
      // }
      var container = this.$refs.inputRef;
      container.scrollTop = container.scrollHeight + 120;

    },
    async showUser() {
      await axios.post('showUser').then(response => {
        this.account = response.data;
        if (this.account.Role == 'Support') {
          this.Support_id = this.account.id;
        } else {
          this.for_user = this.account.id;
        }
      });
      this.isOpen(this.account.id);
    },
    async Ban() {
      await axios.post('ban').then(response => {
        console.log(response);
      });
    },
    async SendMessage(user, support) {
      let data = {
       user_id: user,
        support_id:  support,
        message: this.newMessage,
        role: 'user'
      };
      if (this.newMessage !== '')
        await axios.post('messages', data);
      this.newMessage = '';
    },
    async isOpen(user) {
      this.IsOpen = false;
      let data = {
        user_id: user,
        role:"User"
      };
      await axios.post('supportable', data).then(res => {
        this.IsOpen = res.data;
        console.log(res.data);
      });
      if (this.IsOpen[0] == true) {
        this.Support_id = this.IsOpen[1];
        this.selectUser(this.account.id, this.Support_id);
      }
      return this.IsOpen;
    }
  }

};
</script>

<style scoped>
.rate {
  float: left;
  height: 46px;
  padding: 0 10px;
}

.rate:not(:checked) > input {
  position: absolute;
  top: -9999px;
}

.rate:not(:checked) > label {
  float: right;
  width: 1em;
  overflow: hidden;
  white-space: nowrap;
  cursor: pointer;
  font-size: 30px;
  color: #ccc;
}

.rate:not(:checked) > label:before {
  content: '★ ';
}

.rate > input:checked ~ label {
  color: #ffc700;
}

.rate:not(:checked) > label:hover,
.rate:not(:checked) > label:hover ~ label {
  color: #deb217;
}

.rate > input:checked + label:hover,
.rate > input:checked + label:hover ~ label,
.rate > input:checked ~ label:hover,
.rate > input:checked ~ label:hover ~ label,
.rate > label:hover ~ input:checked ~ label {
  color: #c59b08;
}

.scrollable {
  overflow: hidden;
  overflow-y: scroll;
  height: calc(100vh - 20px);
}

.container {
  max-width: 1170px;
  margin: auto;
}

img {
  max-width: 100%;
}

.inbox_people {
  background: #f8f8f8 none repeat scroll 0 0;
  float: left;
  overflow: hidden;
  width: 40%;
  border-right: 1px solid #c4c4c4;
}

.inbox_msg {
  border: 1px solid #c4c4c4;
  clear: both;
  overflow: hidden;
}

.top_spac {
  margin: 20px 0 0;
}


.recent_heading {
  float: left;
  width: 40%;
}

.srch_bar {
  display: inline-block;
  text-align: right;
  width: 60%;
}

.headind_srch {
  padding: 10px 29px 10px 20px;
  overflow: hidden;
  border-bottom: 1px solid #c4c4c4;
}

.recent_heading h4 {
  color: #05728f;
  font-size: 21px;
  margin: auto;
}

.srch_bar input {
  border: 1px solid #cdcdcd;
  border-width: 0 0 1px 0;
  width: 80%;
  padding: 2px 0 4px 6px;
  background: none;
}

.srch_bar .input-group-addon button {
  background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
  border: medium none;
  padding: 0;
  color: #707070;
  font-size: 18px;
}

.srch_bar .input-group-addon {
  margin: 0 0 0 -27px;
}

.chat_ib h5 {
  font-size: 15px;
  color: #464646;
  margin: 0 0 8px 0;
}

.chat_ib h5 span {
  font-size: 13px;
  float: right;
}

.chat_ib p {
  font-size: 14px;
  color: #989898;
  margin: auto
}

.chat_img {
  float: left;
  width: 11%;
}

.chat_ib {
  float: left;
  padding: 0 0 0 15px;
  width: 88%;
}

.chat_people {
  overflow: hidden;
  clear: both;
}

.chat_list {
  border-bottom: 1px solid #c4c4c4;
  margin: 0;
  padding: 18px 16px 10px;
}

.inbox_chat {
  height: 550px;
  overflow-y: scroll;
}

.active_chat {
  background: #ebebeb;
}

.incoming_msg_img {
  display: inline-block;
  width: 6%;
}

.received_msg {
  display: inline-block;
  padding: 0 0 0 10px;
  vertical-align: top;
  width: 92%;
}

.received_withd_msg p {
  background: #ebebeb none repeat scroll 0 0;
  border-radius: 3px;
  color: #646464;
  font-size: 14px;
  margin: 0;
  padding: 5px 10px 5px 12px;
  width: 100%;
}

.time_date {
  color: #747474;
  display: block;
  font-size: 12px;
  margin: 8px 0 0;
}

.received_withd_msg {
  width: 57%;
  direction: rtl !important;
}

.mesgs {
  float: left;
  padding: 30px 15px 0 25px;
  width: 100%;
}

.sent_msg p {
  background: #05728f none repeat scroll 0 0;
  border-radius: 3px;
  font-size: 14px;
  margin: 0;
  color: #fff;
  padding: 5px 10px 5px 12px;
  width: 100%;
}

.outgoing_msg {
  overflow: hidden;
  margin: 26px 0 26px;
}

.sent_msg {
  float: right;
  width: 46%;
}

.input_msg_write input {
  background: rgba(0, 0, 0, 0) none repeat scroll 0 0;
  border: medium none;
  color: #4c4c4c;
  font-size: 15px;
  min-height: 48px;
  width: 100%;
}

.type_msg {
  border-top: 1px solid #c4c4c4;
  position: relative;
  width: 1050px;
}

.msg_send_btn {
  background: #05728f none repeat scroll 0 0;
  border: medium none;
  border-radius: 50%;
  color: #fff;
  cursor: pointer;
  font-size: 17px;
  height: 33px;
  position: absolute;
  right: 0;
  top: 11px;
  width: 33px;
}

.messaging {
  padding: 0 0 50px 0;
}

.msg_history {
  height: 516px;
  overflow-y: auto;
}
</style>