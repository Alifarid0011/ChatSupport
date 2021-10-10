<template>

  <div class="container">
    <br><br><br>
    <div class="card text-center">

      <div class="card-header">
        ابزار ایجاد نقش
      </div>

      <div class="card-body">
        <div class="row">
          <!--          جدول مجوز ها -->
          <div class="col-md-6">
            <table class="table table-success table-striped">
              <thead>
              <tr>
                <th scope="col">#</th>
                <th scope="col">مجوز ها</th>
                <th scope="col">حذف</th>
              </tr>
              </thead>
              <tbody>

              <tr v-for="(permission,index) in permissions" :key="index">
                <th scope="row">
                  {{ index + 1 }}
                </th>
                <td>
                  {{ permission.name }}
                </td>
                <td>
                  <button class="btn btn-danger" @click="RemovePermission(permission.name)">حذف</button>
                </td>
              </tr>
              </tbody>
            </table>
          </div>
          <!--          پايان جدول مجوز ها -->
          <!--     فرم ارسال      -->
          <div class="col-md-6" >
            <br><br><br><br>
            <br><br><br><br>
            <form @submit.prevent="submitPermission()">
              <input type="text" class="form-control" placeholder="مجوز جديد" name="Permission" v-model="newPermission">
              <br><br><br>
              <button type="submit" class="btn btn-success">
                ساخت مجوز
              </button>
            </form>
          <br>
          </div>
          <!--    #      -->
          <hr>
          <br>
        </div>
        <table class="table table-success table-striped">
          <thead>
          <tr>
            <th scope="col">#</th>
            <th scope="col">نقش</th>
            <th scope="col">مجوز ها</th>
            <th scope="col">ویرایش</th>
          </tr>
          </thead>
          <tbody>
          <!--        @foreach ( as $key=>$roll)-->
          <tr v-for="(roll,index) in rolls_permissions" :key="index">
            <th scope="row">
              <!--            {{$key+1}}-->
              {{ index + 1 }}
            </th>
            <td>
              <!--            {{$roll->name}}-->
              {{ roll.name }}
            </td>
            <td>
              <!--            @foreach($roll->permissions as $key=>$permission)-->
              <!--            {{$permission->name}} ;-->
              <span v-for="(permission , i) in roll.permissions" :key="i">
            {{ permission.name }} ;
            </span>
              <!--            @endforeach-->
            </td>
            <td>
              <button class="btn btn-danger" @click="Remove(roll.name)">حذف
              </button>
            </td>
          </tr>

          </tbody>
        </table>
        <div class="row container">
          <form @submit.prevent="SubmitRollAndPermission" class="justify-content-center" >
            <div class="col-sm-11 align-self-center">
              <input type="text" class="form-control" placeholder="نقش" v-model="Roll" name="roll">
              <br>
              <lable>مجوز ها </lable>
              <Multiselect
                           v-model="value"
                           mode="tags"
                           :searchable="true"
                           :createTag="false"
                           :options="Options"
                           class="multiselect-blue"
              />

              <!--              [-->
              <!--              { value: 'batman', label: 'Batman' },-->
              <!--              { value: 'robin', label: 'Robin' },-->
              <!--              { value: 'joker', label: 'Joker' },-->
              <!--              ]-->
            </div>
            <button type="submit" class="btn btn-primary">
              اضافه کردن
            </button>
          </form>

        </div>

        <br>
        <hr>
        <br>
        <div class="row">
<!--        جدول يوزر-->
        <div class="col-md-6">
          <table class="table table-success table-striped">
            <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">نام </th>
              <th scope="col">ايميل </th>
              <th scope="col">نقش</th>
              <th scope="col">ويرايش</th>
            </tr>
            </thead>
            <tbody>
            <tr v-for="(UserWithRole,index) in UsersWithRole" :key="index">
<!--              {{UserWithRole.id}}-->
<!--              {{UserWithRole.roles[0].name}}-->
              <th scope="row">
                {{ index + 1 }}
              </th>
              <td>
                {{ UserWithRole.name }}
              </td>
              <td>
                {{ UserWithRole.email }}
              </td>
              <td>
                {{ UserWithRole.roles[0].name}}
              </td>
              <td>
                <button class="btn btn-danger" @click="RemoveUsersRole(UserWithRole.id,UserWithRole.roles[0].name)">حذف</button>
              </td>
            </tr>
            </tbody>
          </table>
        </div>
        <div class="col-md-6">
          {{valueUser}}
          {{valueRoll}}
          <form @submit.prevent="SubmitUsersRoll()">
            <Multiselect
                v-model="valueUser"
                :searchable="true"
                placeholder="كاربر"
                :options="OptionsUsers"
            />
            <Multiselect
                v-model="valueRoll"
                :searchable="true"
                placeholder="نقش"
                :options="OptionsRoll"
            />
            <button type="submit" class="btn btn-primary">
              ارسال
            </button>
          </form>

        </div>
<!--        پايان جدول يوزر-->
        </div>
      </div>
      <div class="card-footer text-muted">
        *هر ; جدا کننده ی مجوز است
      </div>
    </div>
    <br><br><br><br><br><br><br>
  </div>

</template>

<script>

import Axios from '../Req/Axios';
import Multiselect from '@vueform/multiselect';

export default {
  components: {
    Multiselect
  },
  data() {
    return {
      Roll: '',
      UsersWithRole:[],
      UsersWithOutRole:[],
      removeItem: [],
      value: [],
      valueRoll: null,
      valueUser: null,
      OptionsRoll: [],
      rolls_permissions: [],
      permissions: [],
      newPermission: '',
      Options: [],
      OptionsUsers: [],
    };
  },
  created() {
    this.fetchRolls();
    this.fetchPermissions();
    this.fetchUsersWithRole();
    this.fetchUsersWithOutRole();
  },
  methods: {

    async fetchRolls() {
     await Axios.post('ShowRoll').then(res => {
        console.log(res.data);
        this.rolls_permissions = res.data[0];
      });
      this.rolls_permissions.forEach((element) => {
        this.OptionsRoll.push(element.name);
      });
    },
    fetchUsersWithRole() {
      Axios.post('ShowUsersWithRole').then(res => {
        this.UsersWithRole = res.data[0];
      });
    },
    async fetchUsersWithOutRole() {
      while (this.OptionsUsers.length) {
        this.OptionsUsers.pop();
      }
     await Axios.post('ShowUsersWithOutRole').then(res => {
        this.UsersWithOutRole = res.data[0];
      });
      this.UsersWithOutRole.forEach((element) => {
        this.OptionsUsers.push({value: element.id, label: element.name});
      });
    },
    async fetchPermissions() {
      while (this.Options.length) {
        this.Options.pop();
      }
      await Axios.post('ShowPermission').then(res => {
        this.permissions = res.data[0];
      });
      this.permissions.forEach((element, index) => {
        this.Options.push({value: element.name, label: element.name});
      });

    },
    SubmitRollAndPermission() {
      let data={
        roll: this.Roll,
        permissions:this.value
      }
      Axios.post('CreateRoll',data).then(res => {
        console.log(res.data);
        this.Roll = '';
        while (this.value.length) {
          this.value.pop();
        }
        this.fetchRolls();
      }).catch(res => {
        console.log(res);
      });
    },
    submitPermission() {
      Axios.post('CreatePermission', {
        permission: this.newPermission
      }).then(() => {
        this.newPermission = '';
        this.fetchPermissions();
      }).catch(res => {
        console.log(res);
      });
    },
    SubmitUsersRoll() {
      let data={
        user_id:this.valueUser,
        role:this.valueRoll,
      }
      Axios.post('CreateUsersRole', {
        data
      }).then(() => {
        this.valueUser=''
        this.valueRoll=''
        this.fetchUsersWithRole();
        this.fetchUsersWithOutRole();
      }).catch(res => {

      });
    },
    Remove(n) {
      Axios.get(`RemoveRoll?name=${n}`).then(res => {
        this.fetchRolls();
      }).catch(e => {
        console.log(e.message);
      });
    },
    RemoveUsersRole(UserId,Role){
      let data={
        user_id:UserId,
        role:Role,
      }
      Axios.post('RemoveUsersRole',data).then(res => {
        this.fetchUsersWithRole();
        this.fetchUsersWithOutRole();
      }).catch(e => {
        console.log(e.message);
      });
    },
    RemovePermission(n) {
      Axios.get(`RemovePermission?name=${n}`).then(() => {
        this.fetchPermissions();
      }).catch(e => {
        console.log(e.message);
      });
    }
  }

};

</script>

<style src="@vueform/multiselect/themes/default.css">

</style>
<style >
.output {
  font-family: Courier, Courier New, Lucida Console, Monaco, Consolas;
  background: #000000;
  color: #ffffff;
  padding: 20px;
  margin-bottom: 20px;
  display: inline-block;
  width: 100%;
  box-sizing: border-box;
  font-size: 13px;
}

.multiselect-tag.is-user {
  padding: 5px 8px;
  border-radius: 22px;
  background: #35495e;
  margin: 3px 3px 8px;
}

.multiselect-tag.is-user img {
  width: 18px;
  border-radius: 50%;
  height: 18px;
  margin-right: 8px;
  border: 2px solid #ffffffbf;
}

.multiselect-tag.is-user i:before {
  color: #ffffff;
  border-radius: 50%;;
}

.user-image {
  margin: 0 6px 0 0;
  border-radius: 50%;
  height: 22px;
}

.character-option-icon {
  margin: 0 6px 0 0;
  height: 22px;
}

.character-label-icon {
  margin: 0 6px 0 0;
  height: 26px;
}
</style>
