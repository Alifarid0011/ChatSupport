<template>
  <div>
    <div class="container">
      <h1 class="text-center display-2 bg-yellow-100">پشتیبانی</h1>
      <h3 class=" text-center">
      </h3>
      <div class="messaging">
        <div class="inbox_msg">
          <div class="inbox_people">
            <div class="headind_srch">
              <div class="recent_heading">
                <h4>Recent</h4>
              </div>
              <div class="srch_bar">
                <div class="stylish-input-group">
                  <input type="text" class="search-bar" placeholder="Search">
                  <span class="input-group-addon">
                <button type="button"> <i class="fa fa-search" aria-hidden="true"></i> </button>
                </span></div>
              </div>
            </div>
            <div class="inbox_chat">
              <div v-for="(user, index) in users" :key="index">
                <div class="chat_list active_chat" v-if="user.role!=='Support'">
                  <div class="chat_people">
                    <div class="chat_img">
                      <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil">
                    </div>
                    <div class="chat_ib">
                      <h5>
                        {{ user.name }}
                        <span class="chat_date" v-if="user.online">
                            <span class="Online"></span><p style="color: darkgreen">انلاین</p>
                        </span>
                        <span class="chat_date" v-else>
                            <span class="Offline"></span><p style="color: darkgray">افلاین</p>
                        </span>
                      </h5>
                      <div v-if="user.id===activeUser.id&&activeUser.user===user.name" style="color: darkgray">
                        <span v-if="activeUser">{{ activeUser.user }} is typing ...</span>
                      </div>
                      <!--                      <button class="btn btn-primary" style="margin-right: 10px" @click.prevent="fetchMessages(user.id,Support_id)">پاسخ دهي-->
                      <div>
                        <button class="btn btn-primary" style="margin-right: 10px"
                                @click.prevent="
                              SelectingEvent(user.id,Support_id,user.type,user.name)
                             ">پاسخ دهي
                        </button>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <!--          v-if="selected" false-->
          <div v-if="false" class="d-grid gap-2 col-6 mx-auto">
            <button class="btn btn-warning btn-block">بلاك</button>
            <button class="btn btn-danger btn-block" @click="end(for_user,Support_id)">خاتمه</button>
          </div>
          <div class="mesgs">
            <!--       #   بخش پيام ها ي بين پشتيباني و كاربران-->
            <div class="msg_history scrollable" ref="inputRef">
              <!--پيام هاي كاربر-->
              <div>
                <div v-for="(message,index) in messages" :v-key="index" class="messageForscroll">
                  <!--                  {{ Object.values(Object.values(message)[0])[0] }}-->
                  <div>
                    <div v-if="Object.values(Object.values(message)[0])[0]===false">
                      <div class="incoming_msg" :id="index+1">
                        <div class="incoming_msg_img">
                          <img src="https://ptetutorials.com/images/user-profile.png" alt="sunil">
                        </div>
                        <span>
                          {{ nameOfSelectedUser }}
                        </span>
                        <div class="received_msg">
                          <div class="received_withd_msg">
                            <p>
                              {{ message.messages.Message }}
                            </p>
                            <span class="time_date">
                                    {{ message.messages.make_at }}
                            </span></div>
                        </div>
                      </div>
                    </div>
                    <!--                # پيام هاي كاربر-->
                    <!--                پيام هاي پشتيباني-->
                    <div v-if="Object.values(Object.values(message)[0])[0]===true">
                      <div class="outgoing_msg">
                        <div class="sent_msg">
                          <span>پشتيباني:</span>
                          <p>
                            {{ message.messages.Message }}
                          </p>
                          <span class="time_date">
                            {{ message.messages.make_at }}
                          </span></div>
                      </div>
                    </div>
                    <!--            #  پيام هاي پشتيباني-->
                  </div>
                </div>
              </div>
            </div>
            <!--       #   بخش پيام ها ي بين پشتيباني و كاربران-->
            <div class="type_msg">
              <div class="input_msg_write">
                <input
                    @keydown="TypingEvent()"
                    v-model="newMessage"
                    @keyup.enter="SendMessage(for_user,Support_id,newMessage,role_receiver)"
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

  name: 'Chat',

  data() {
    return {
      messages: [],
      users: [],
      role_receiver: null,
      guests: [],
      account: [],
      Support_id: null,
      for_user: null,
      nameOfSelectedUser: null,
      supportable: null,
      newMessage: '',
      activeUser: false,
      typingTimer: false,
      fourResentMessage: [],
      selected: false
    };
  },
  async created() {
    await this.showUser();
  },
  mounted() {
    window.Echo.channel('Chat').listen('SendMessage', (event) => {
      this.fetchMessages(this.for_user, this.Support_id);
    });
    setTimeout(async () => {
      await this.Joining();
    }, 1000);
  },
  methods: {
    Joining() {
      Echo.join('ChatGuest')
          .here(async guest => {
// انلاین یا افلاین بودن مهمانان
            axios.post('Supports', {support_id: this.Support_id}).then((res) => {
              this.users = res.data[0].filter(async UserData => {
                await guest.forEach((element) => {
                  if (UserData.id === element.id && UserData.type === 'Guest') {
                    $.extend(true, UserData, {
                      online: true
                    });
                  }
                });
              });
            });
            // انلاین یا افلان بودن مهمانان که توسط پشتیبان انتخاب نشده اند و کربر آزاد اند
            setTimeout(async () => {
                  await guest.filter(async GuestData => {
                    if (GuestData.isGuest)
                      await axios.post('supportable', {user_id: GuestData.id, role: 'Guest'}).then(async res => {
                        if (!res.data[0]) {
                          let data = {
                            id: GuestData.id,
                            name: GuestData.name,
                            type: 'Guest',
                            online: true
                          };
                          this.users.push(data);
                        }
                      });
                  });
                }
                , 1020);
          }).joining(async guest => {

        if (guest.isGuest !== false) {
         this.users.filter(async GuestData => {
            if (GuestData.id === guest.id && GuestData.type === 'Guest') {
              $.extend(true, GuestData, {
                online: true
              });
            }
          });
          // this.users = guest.filter(async UserData => {
          //   await axios.post('supportable', {user_id: guest.id, role: 'Guest'}).then(async res => {
          //     if (res.data[0])
          //       $.extend(true, UserData, {
          //         online: true
          //       });
          //   });
          // });
          await axios.post('supportable', {user_id: guest.id, role: 'Guest'}).then(async res => {
            if (!res.data[0]) {
              let data = {
                id: guest.id,
                name: guest.name,
                type: 'Guest',
                online: true
              };
              this.users.push(data);
            }
          });
        }

      }).leaving(async guest => {
        if (guest.isGuest !== false) {
          this.users = this.users.filter(async GuestData => {
            axios.post('Supports', {support_id: this.Support_id}).then((res) => {
              if (res.data[0])
                if (GuestData.id === guest.id && GuestData.type === 'Guest') {
                  $.extend(true, GuestData, {
                    online: false
                  });
                }
            });
          });
          // حذف کاربر از لیست موجود
          await axios.post('supportable', {user_id: guest.id, role: 'Guest'}).then(async res => {
            // console.log(res.data[0]);
            if (!res.data[0]) {
              console.log(guest);
              this.users = this.users.filter(U => U.name !== guest.name);
            }
          });
        }
      });
      //برای کاربران
      Echo.join('Chat')
          .here(async user => {
            // let obj = [];
            // for (let key in user) {
            //   if (!user.hasOwnProperty(key)) continue;
            //   obj.push(user[key]);
            // }
            this.users = this.users.filter(async UserData => {
              if (UserData.role === 'Normal')
                await user.forEach((element) => {
                  if (UserData.id === element.id && UserData.type === 'User') {
                    $.extend(true, UserData, {
                      online: true
                    });
                  }
                });
            });
            setTimeout(async () => {
                  await user.filter(async userData => {
                    // console.log();
                    if (userData.role === 'Normal')
                      await axios.post('supportable', {user_id: userData.id, role: 'User'}).then(async res => {
                        if (!res.data[0]) {
                          let data = {
                            id: userData.id,
                            name: userData.name,
                            type: 'User',
                            online: true
                          };
                          this.users.push(data);
                        }
                      });
                  });
                }
                , 1010);
          }).joining(user => {
        // console.log(`user: ${user}`);

        this.users = this.users.filter(async UserData => {
          if (UserData.role === 'Normal') {
            // console.log(user);
            if (UserData.id === user.id && UserData.type === 'User') {
              $.extend(true, UserData, {
                online: true
              });
            }
          }

        });
        setTimeout(async () => {
              if (user.role === 'Normal') {
                await axios.post('supportable', {user_id: user.id, role: 'User'}).then(async res => {
                  if (!res.data[0]) {
                    let data = {
                      id: user.id,
                      name: user.name,
                      type: 'User',
                      online: true
                    };
                    this.users.push(data);
                  }
                });
              }
            }
            , 1030);
      }).leaving(async user => {
        this.users = this.users.filter(async UserData => {
          if (UserData.id === user.id && UserData.type === 'User') {
            $.extend(true, UserData, {
              online: false
            });
          }
        });
        if (user.role === 'Normal')
          await axios.post('supportable', {user_id: user.id, role: 'User'}).then(async res => {
            if (!res.data[0]) {
              this.users = this.users.filter(U => U.id !== user.id);
            }
          });
      }).listenForWhisper('typing', user => {
        this.activeUser = user;
        // console.log(user);
        if (this.typingTimer) {
          clearTimeout(this.typingTimer);
        }
        this.typingTimer = setTimeout(() => {
          this.activeUser = false;
        }, 3000);
      }).listenForWhisper('selecting', user => {
        // console.log(user);
        if (user.Support_id !== this.account.id) {
          this.users = this.users.filter(u => u.id != user.userSelected);
        }
      });
    },
    TypingEvent() {
      let data = {
        forUser: this.for_user,
        user: this.account.user
      };
      Echo.join('Chat')
          .whisper('typing', data);
    }
    ,
    SelectingEvent(user_id, Support_id, role, name) {
      this.selected = true;
      this.role_receiver = role;
      let data = {
        userSelected: user_id,
        Support_id: Support_id
      };
      this.selectUser(user_id, Support_id, role, name);
      Echo.join('Chat')
          .whisper('selecting', data);
    },
    async selectUser(user, support, role, nameOfSelectedUser) {
      let data = {
        user_id: user,
        support_id: support,
        role: role
      };
      this.nameOfSelectedUser = nameOfSelectedUser;
      this.for_user = user;
      this.Support_id = support;
      this.role_receiver = role;
      await axios.post('selectUser', data);
      // console.log(data);
      this.fetchMessages(user, support);
    },
    async fetchMessages(user, support) {
      await axios.get(`messages?user_id=${user}&support_id=${support}&role=${this.role_receiver}`).then(response => {
        this.messages = response.data;

      }).catch(reason => {
        // console.log(reason);
      });
      // if (this.role_receiver === 'Guest') {
      //   await axios.get(`messages?receive_id=${user}&support_id=${support}&role=${this.role_receiver}`).then(response => {
      //     this.messages = response.data;
      //   });
      // } else {
      //   await axios.get(`messages?receive_id=${user}&support_id=${support}`).then(response => {
      //     this.messages = response.data;
      //   });
      // }

      // if (this.fourResentMessage.length != 2) {
      //   this.fourResentMessage.push(this.messages[this.messages.length - 1].created_at);
      // } else {
      //   this.fourResentMessage.shift();
      // }
      // if (this.fourResentMessage[0] === this.fourResentMessage[1]) {
      //   this.Ban();
      // }
      //براي اسكرول چت ( كلاس scrilable هم نياز است )
      let container = this.$refs.inputRef;
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
    },
    async Ban() {
      await axios.post('ban').then(response => {
        // console.log(response);
      });
    },

//اينجا بايد درست بشه
    async SendMessage(user_id, support, Message, receiver_role) {
      let date = {
        support_id: support,
        message: Message,
        receiver_id: user_id,
        receiver_role: receiver_role,
        role: 'support'
      };
      // console.log(date);
      if (this.newMessage !== '')
        await axios.post('messages', date);
      this.newMessage = '';
    },
    async end(user, support) {
      let endData = {
        user_id: user,
        support_id: support,
        end: true
      };
      // await axios.post('messages', date);
      Echo.join('Chat')
          .whisper('ending', endData);
      setTimeout(() => {
        this.selected = false;
        this.users = this.users.filter(u => u.id !== user);
        axios.post('endChat', date);
      }, 30000);
      //  سي ثانيه ميگذره و پاك ميشه
    },
    async getUsers(user) {
      let obj = [];
      for (let key in user) {
        if (!user.hasOwnProperty(key)) continue;
        obj.push(user[key]);
      }
      const data = [];
      await obj.forEach((element) => {
        let datatemp = {
          element, 'online': true
        };
        data.push(datatemp);
        // console.log(datatemp);
        axios.post('supportable', {user_id: element.id}).then(res => {
          if (res.data[0]) {
            if (res.data[1] === null || res.data[1] === this.account.id)
              this.users.push(datatemp);
          } else if (element.role !== 'Support' && res.data[0] === false) {
            this.users.push(datatemp);
          }
        });
      });
    }
  }
}
;

</script>

<style scoped>
.Online {
  width: 10px;
  height: 10px;
  background-color: darkgreen;
  border-radius: 50%
}

.Offline {
  width: 10px;
  height: 10px;
  background-color: darkgray;
  border-radius: 50%
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
  width: 60%;
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