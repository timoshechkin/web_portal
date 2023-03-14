<template>
  <!-- App.vue -->
    <v-app>
      <v-app-bar
          color="blue darken-4"
          dark
          clipped-left
          app
        >
          <v-app-bar-nav-icon
            @click.stop="setDispDraw"
            v-if="logged"
          ></v-app-bar-nav-icon>
          <div class="d-none d-sm-flex d-md-flex d-lg-flex d-xl-flex" style="font-size:1.25rem;">Личный кабинет работника</div>

          <v-spacer></v-spacer>
          
            <v-badge
              :value="alerts.length>0"
              left
              bottom
              overlap
              color="green"
            >
              <template #badge>

                <v-menu
                  v-model="menu_alerts"
                  offset-y
                  :close-on-content-click="false"
                  transition="scale-transition"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <div
                      style="cursor: pointer"
                      v-bind="attrs"
                      v-on="on"
                    >
                      {{ String(alerts.length) }}
                    </div>
                  </template>
                  <v-card>
                    <v-toolbar
                      dense
                      elevation="2"
                    >
                      <v-icon large color="primary">
                        mdi-bell-circle-outline
                      </v-icon>                   
                      <v-spacer></v-spacer>
                      <v-btn
                        small
                        text
                        color="primary"
                        @click="hideAlerts()"
                      >
                        <v-icon left small color="primary">
                          mdi-broom
                        </v-icon>
                        Очистить список 
                      </v-btn>
                    </v-toolbar>
                    <!-- <v-divider></v-divider> -->
                    <div style="max-width:350px; overflow-y:auto; max-height:550px;">
                      <v-list>
                        <v-list-item-group>
                          <div
                            v-for="(item, index) in alerts"
                            :key="index"
                          >                       
                            <v-list-item>

                              <v-list-item-icon>
                                <v-icon v-if="item.type=='info'" large color="green">mdi-information-outline</v-icon>
                                <v-icon v-if="item.type=='warning'" large color="warning">mdi-alert-outline</v-icon>
                                <v-icon v-if="item.type=='error'" large color="error">mdi-alert-octagon-outline</v-icon>
                              </v-list-item-icon>

                              <v-list-item-content style="align-self:start;">
                                <div class="text-caption"><span v-html="item.description"></span></div>                             
                              </v-list-item-content>
                              
                              <v-list-item-action style="align-self:start;">
                                <v-tooltip bottom>
                                  <template v-slot:activator="{ on, attrs }">
                                    <v-btn
                                      icon
                                      small
                                      @click="hideAlerts(item.id)"
                                      v-bind="attrs"
                                      v-on="on"
                                    >
                                      <v-icon small>mdi-close</v-icon>
                                    </v-btn>
                                  </template>
                                  <span>Скрыть уведомление</span>
                                </v-tooltip>

                                <v-tooltip bottom>
                                  <template v-slot:activator="{ on, attrs }">
                                    <!-- @click="openObject(item.object_id, item.object_type)" -->
                                    <v-btn
                                      @click="openObject(item.object_id, item.section)"
                                      v-if="item.object_id!=''"
                                      class="mt-2"
                                      icon
                                      small                                                                    
                                      v-bind="attrs"
                                      v-on="on"
                                    >
                                      <v-icon small color="blue">mdi-open-in-new</v-icon>
                                    </v-btn>
                                  </template>
                                  <span>Открыть документ</span>
                                </v-tooltip>

                              </v-list-item-action>

                            </v-list-item>
                            <v-divider
                              v-if="index < alerts.length - 1"
                              :key="index"
                            ></v-divider>
                          
                          </div>
                        </v-list-item-group>
                      </v-list>                  
                    </div>
                  </v-card>
                </v-menu>

              </template>
              <v-avatar
                v-if="logged"
                size="35"
                class="mx-2"
              >
                <v-img
                  v-if="user_photo.length>0" 
                  :src="'data:image/jpeg;base64,' + user_photo"
                ></v-img>
                <v-icon  v-else>
                  mdi-account-circle-outline
                </v-icon>
              </v-avatar>
            </v-badge>

          <v-chip
            v-if="logged"
            class="d-none d-md-flex d-lg-flex d-xl-flex"
            label
            outlined
          >
            {{ user_name }}
          </v-chip>

            <!-- <v-btn 
              class="d-none d-md-flex d-lg-flex d-xl-flex"
              text
              v-if="logged">
              <v-icon :color="color_acc">
                mdi-account
              </v-icon>
              {{ user_name }}
            </v-btn> -->

          <v-btn text small
            v-if="!logged"
            @click="dialogLogin = true"
          >
            <v-icon>
              mdi-login-variant
            </v-icon>
            Войти
          </v-btn>

          <v-btn text small
          v-if="logged && basic_auth"
          @click="logout">
            <v-icon>
              mdi-logout-variant
            </v-icon>
            Выйти
          </v-btn>

          <v-btn icon
          @click="toggleTheme"
          >
            <v-icon>
              mdi-theme-light-dark
            </v-icon>
          </v-btn>

        </v-app-bar>
        <!-- <v-navigation-drawer
          v-if="isDrawDisp"
          permanent
          bottom
          clipped
          app
        > -->
        <v-navigation-drawer
          v-if="isDrawDisp"
          permanent
          bottom
          clipped
          app
        >
          <v-list dense nav>
            <v-list-item value="home" to="/" @click="collapse_nav">
              <v-list-item-icon><v-icon>mdi-home</v-icon></v-list-item-icon>
              <v-list-item-content><v-list-item-title>Главная</v-list-item-title></v-list-item-content>
            </v-list-item>

            <v-list-item value="news" to="/news" @click="collapse_nav">
              <v-list-item-icon><v-icon>mdi-newspaper-variant-multiple</v-icon></v-list-item-icon>
              <v-list-item-content><v-list-item-title>Новости</v-list-item-title></v-list-item-content>
            </v-list-item>

            <v-list-item value="services" to="/services" @click="collapse_nav">
              <v-list-item-icon><v-icon>mdi-medical-bag</v-icon></v-list-item-icon>
              <v-list-item-content><v-list-item-title>Каталог услуг</v-list-item-title></v-list-item-content>
            </v-list-item>

            <v-list-group
              v-if="$store.getters.getUserAccess.appeals_in"
              v-model="group_nav_appeals_expanded"
              :value="group_nav_appeals_expanded"
              no-action="true"
              prepend-icon="mdi-text-box-multiple"
            >
              <template v-slot:activator>
                <v-list-item-title>Обращения</v-list-item-title>
              </template>

              <v-list-item value="appeals_out" to="/appeals_out">
                <!-- <v-list-item-icon><v-icon>mdi-comment-arrow-left</v-icon></v-list-item-icon> -->
                <v-list-item-content><v-list-item-title>Исходящие</v-list-item-title></v-list-item-content>
                <v-list-item-icon><v-icon>mdi-arrow-top-right-bold-box-outline</v-icon></v-list-item-icon>
              </v-list-item>

              <v-list-item value="appeals_in" to="/appeals_in">
                <!-- <v-list-item-icon><v-icon>mdi-comment-arrow-right</v-icon></v-list-item-icon> -->
                <v-list-item-content><v-list-item-title>Входящие</v-list-item-title></v-list-item-content>
                <v-list-item-icon><v-icon>mdi-arrow-bottom-left-bold-box-outline</v-icon></v-list-item-icon>
              </v-list-item>

            </v-list-group>

            <v-list-item v-else value="appeals_out" to="/appeals_out">
              <v-list-item-icon><v-icon>mdi-text-box-multiple</v-icon></v-list-item-icon>
              <v-list-item-content><v-list-item-title>Обращения</v-list-item-title></v-list-item-content>   
            </v-list-item>

            <v-list-item value="contacts" to="/contacts" @click="collapse_nav">
              <v-list-item-icon><v-icon>mdi-book-open</v-icon></v-list-item-icon>
              <v-list-item-content><v-list-item-title>Адресная книга</v-list-item-title></v-list-item-content>
            </v-list-item>

            <!-- <v-list-item value="messanger" to="/messanger" @click="collapse_nav">
              <v-list-item-icon><v-icon>mdi-chat</v-icon></v-list-item-icon>
              <v-list-item-content><v-list-item-title>Мессенджер</v-list-item-title></v-list-item-content>
            </v-list-item> -->

            <!-- <v-list-item value="settings" to="/settings" @click="collapse_nav">
              <v-list-item-icon><v-icon>mdi-cog</v-icon></v-list-item-icon>
              <v-list-item-content><v-list-item-title>Настройки</v-list-item-title></v-list-item-content>
            </v-list-item> -->

            <v-list-group
              v-model="group_nav_services_expanded"
              :value="group_nav_services_expanded"
              no-action="true"
              prepend-icon="mdi-store-cog"
            >
              <template v-slot:activator>
                <v-list-item-title>Сервисы</v-list-item-title>
              </template>

              <v-list-item  value="newspapers" to="/newspapers">               
                <v-list-item-content><v-list-item-title>Газета</v-list-item-title></v-list-item-content> 
                <v-list-item-icon><v-icon>mdi-newspaper-variant</v-icon></v-list-item-icon>              
              </v-list-item>

              <v-list-item  value="videogallery" to="/videogallery">               
                <v-list-item-content><v-list-item-title>Видеогалерея</v-list-item-title></v-list-item-content>
                <v-list-item-icon><v-icon>mdi-filmstrip-box-multiple</v-icon></v-list-item-icon> 
              </v-list-item>

              <v-list-item  value="rates" to="/rates">               
                <v-list-item-content><v-list-item-title>Курсы валют</v-list-item-title></v-list-item-content>
                <v-list-item-icon><v-icon>mdi-chart-box-outline</v-icon></v-list-item-icon> 
              </v-list-item>

              <v-list-item  value="weather" to="/weather">               
                <v-list-item-content><v-list-item-title>Погода</v-list-item-title></v-list-item-content>
                <v-list-item-icon><v-icon>mdi-weather-partly-cloudy</v-icon></v-list-item-icon> 
              </v-list-item>

              <!-- <v-list-item  value="videochat" to="/videochat">               
                <v-list-item-content><v-list-item-title>Видеочат</v-list-item-title></v-list-item-content>
                <v-list-item-icon><v-icon>mdi-video-box</v-icon></v-list-item-icon> 
              </v-list-item> -->

              <!-- <v-list-item  value="home" to="/home">               
                <v-list-item-content><v-list-item-title>Видеочат</v-list-item-title></v-list-item-content>
                <v-list-item-icon><v-icon>mdi-video-box</v-icon></v-list-item-icon> 
              </v-list-item> -->

              <v-list-item @click="openFormMailToDir()">
                <v-list-item-content><v-list-item-title>Написать директору</v-list-item-title></v-list-item-content> 
                <v-list-item-icon><v-icon>mdi-email-variant</v-icon></v-list-item-icon>               
              </v-list-item>

            </v-list-group>

            <v-list-item href="https://wiki.kmz.ru/pages/viewpage.action?pageId=14877206" target="_blank" @click="collapse_nav">
              <v-list-item-icon><v-icon>mdi-help-box</v-icon></v-list-item-icon>
              <v-list-item-content><v-list-item-title>Помощь</v-list-item-title></v-list-item-content>
            </v-list-item>

          </v-list>
          <template v-slot:append>
            <v-chip
              class="ma-2"
              color="teal"
              label
              text-color="white"
              to="/anticorruption"
              @click="collapse_nav"
            >
              <v-icon left>
                mdi-security
              </v-icon>
              Противодействие коррупции
            </v-chip>
            <!-- <v-btn block to="/anticorruption" @click="collapse_nav">
              Противодействие коррупции
            </v-btn> -->
          </template>
        </v-navigation-drawer>
      <!-- Sizes your content based upon application components -->
      <!-- <LoginDialog /> -->
      <v-main>
  
        <!-- Provides the application the proper gutter -->
        <v-container fluid>
  
          <!-- If using vue-router -->
          <router-view ref="router_view"></router-view>
        </v-container>
      </v-main>
  
        <v-dialog
          v-model="dialogLogin"
          max-width="450px"
          persistent
        >
          <v-card>
            <v-toolbar
              color="blue darken-4"
              dark
            >
              <v-toolbar-title>Вход в личный кабинет</v-toolbar-title>
              <v-spacer></v-spacer>
              <v-btn icon
                @click="dialogLogin = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>

            <v-form>
            <v-container class="pb-8">

              <v-card-text>

                <v-autocomplete
                  prepend-inner-icon="mdi-account"
                  ref="country"
                  v-model="loginAuth"
                  :items="usersSelect"
                  label="Пользователь"
                  placeholder="Пользователь"
                  filled
                ></v-autocomplete>

                <v-text-field
                  :append-icon="visiblePass ? 'mdi-eye' : 'mdi-eye-off'"
                  :type="visiblePass ? 'text' : 'password'"
                  label="Пароль"
                  prepend-inner-icon="mdi-lock-outline"
                  variant="outlined"
                  @click:append="visiblePass = !visiblePass"
                  v-on:keyup.enter="login_in_form"
                  v-model="passAuth"
                  filled
                ></v-text-field>

              </v-card-text>

              <v-card-actions>
            
                <v-btn
                  block
                  color="success"
                  @click="login_in_form"
                  large
                >
                  Войти
                </v-btn>
              
              </v-card-actions>

            </v-container>
        </v-form>

          </v-card>
        </v-dialog>


        <v-dialog
          v-model="formMailDir"
          max-width="800px"
          persistent
        >
          <v-card>
            <v-toolbar
              color="blue darken-4"
              dark
            >
              <v-toolbar-title>Сообщение исполнительному директору</v-toolbar-title>

              <v-spacer></v-spacer>

              <v-btn icon
                @click="formMailDir = false">
                <v-icon>mdi-close</v-icon>
              </v-btn>
            </v-toolbar>

            <v-form>
              <v-container class="pa-7">

                <v-text-field
                  type="text"
                  label="Тема" 
                  variant="outlined"
                  v-model="theme_mail_dir"
                  filled
                  dense
                ></v-text-field>

                <v-textarea
                  clearable
                  label="Текст сообщения"
                  variant="outlined"
                  v-model="description_mail_dir"
                  no-resize
                  filled
                ></v-textarea>

                <div
                  v-if="checkbox_anonym_mail_dir"
                  class="text-subtitle-2"
                  style="color:#F44336;"
                >
                  При анонимной отправке Вы не сможете получить информацию о результате рассмотрения обращения!
                </div>

                <v-checkbox
                  v-model="checkbox_anonym_mail_dir"
                  label="Анонимно"
                ></v-checkbox>

                <v-text-field
                  v-if="!checkbox_anonym_mail_dir"
                  type="text"
                  disabled
                  label="ФИО работника" 
                  variant="outlined"
                  v-model="user_name_mail_dir"
                  filled
                  dense
                ></v-text-field>

                <v-text-field
                  v-if="!checkbox_anonym_mail_dir"
                  type="text"
                  disabled
                  label="Должность/профессия" 
                  variant="outlined"
                  v-model="user_prof_mail_dir"
                  filled
                  dense
                ></v-text-field>

                <v-text-field
                  v-if="!checkbox_anonym_mail_dir"
                  type="text"
                  disabled
                  label="Подразделение" 
                  variant="outlined"
                  v-model="user_division_mail_dir"
                  filled
                  dense
                ></v-text-field>

                <v-card-actions>                 
                  <v-btn
                    color="primary"
                    @click="sendMailDir"
                    text
                  >
                      Отправить
                  </v-btn>
                  <v-spacer></v-spacer>
                  <v-btn
                    color="error"
                    @click="formMailDir = false"
                    text
                  >
                      Отменить
                  </v-btn>
                </v-card-actions>
                  
              </v-container>
            </v-form>

          </v-card>
        </v-dialog>

        <v-snackbar
          v-model="snackbar"
          :color="snackColor"
        >
          {{ snackText }}

          <template v-slot:action="{ attrs }">
            <v-btn icon
              @click="snackbar = false"
              v-bind="attrs">
                <v-icon>mdi-close</v-icon>
            </v-btn>

          </template>
        </v-snackbar>

        <!-- <v-footer
          padless
          app
          color="blue darken-4"
          dark
        >
          <v-col
            class="text-center"
            cols="12"
          >
            {{ new Date().getFullYear() }} — ПАО "Курганмашзавод"
          </v-col>
        </v-footer> -->
      <v-overlay :value="$store.getters.getProgressMain">
        <v-progress-circular
          :size="70"
          color="white"
          indeterminate
        ></v-progress-circular>
      </v-overlay>
    </v-app>
</template>


<script>

export default {

  data() {
    return{
      alerts: [],
      menu_alerts: false,
      // access_appeals_in: false,
      title_main: 'Личный кабинет работника',
      group_nav_services_expanded: false,
      group_nav_appeals_expanded: false,

      formMailDir: false,
      from_mail_dir: 'portal@kmz.ru',
      to_mail_dir: 'otimoshechkin@kmz.ru',
      to_full_copy_mail_dir: 'portal@kmz.ru',
      user_name_mail_dir: '',
      user_prof_mail_dir: '',
      user_division_mail_dir: '',
      checkbox_anonym_mail_dir: false,
      theme_mail_dir: '',
      description_mail_dir: '',

      dialogLogin: false,
      usersSelect: [],
      passAuth: '',
      visiblePass: false,
      loginAuth: '',
      drawer: true,
      logged: false,
      basic_auth: false,
      user_name:'',
      user_login:'',
      user_id:'',
      user_photo:'',
      snackbar: false,
      snackText: '',
      timeout: 2000,
      snackColor:'blue-grey',
      // progress_main: false,
      color_acc: 'white',
    }
  },
  computed: {
    isDrawDisp(){
      if(!this.logged){
        return false
      }
      else if(this.drawer){
        return true
      }
    },
  },
  methods:{
    openObject(object_id, page){
      // console.log('page: ',page)
      if(this.$route.name!=page){
        // this.$router.push({ path:'/'+page, query:{ id:object_id } })
        this.$store.dispatch('setTargetQuery', { id:object_id })
        this.$router.push({ path:'/'+page })
      }else{
        this.$refs.router_view.openFormViewAppeal(object_id)
      }  
      this.menu_alerts = false   
    },
    getAlerts(){
      var user_id = this.$store.getters.getUserId
      var oXmlHttpRequest = new XMLHttpRequest()
      const strRequest = this.$store.getters.getUrlTracker + '?action=get_alerts&user_id=' + user_id
      oXmlHttpRequest.open("get", strRequest, true)
      oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            // console.log('responseText: ',oXmlHttpRequest.responseText)
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
            // console.log('resJsonObj: ',resJsonObj)
            if(resJsonObj.error==""){
              vm.alerts = []
              var data = resJsonObj.data
              data.forEach(function(item, i, data) {
                vm.alerts.push({ id:item.id, type:item.type, description:item.description, object_id:item.object_id, object_type:item.object_type, section:item.section })
              })
            } 
            if(vm.alerts.length==0) vm.menu_alerts = false          
          }
        }.bind(oXmlHttpRequest, this)
      oXmlHttpRequest.send(null)
    },
    hideAlerts(id=''){
      var oXmlHttpRequest = new XMLHttpRequest()
      const strRequest = this.$store.getters.getUrlTracker + '?action=hide_alerts&id=' + id
      oXmlHttpRequest.open("get", strRequest, true)
      oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            // console.log('responseText: ',oXmlHttpRequest.responseText)
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
            // console.log('resJsonObj: ',resJsonObj)
            if(resJsonObj.error==""){ 
              vm.getAlerts()         
              // const index_alert = vm.alerts.findIndex(item => item.id===resJsonObj.id)
              // if(index_alert!=-1) {
              //   vm.alerts.splice(index_alert, 1)
              // }
              // if(vm.alerts.length==0) vm.menu_alerts = false
            }
          }
        }.bind(oXmlHttpRequest, this)
      oXmlHttpRequest.send(null)
    },
    openFormMailToDir(){
      this.user_name_mail_dir = this.$store.getters.getUserName
      this.user_prof_mail_dir = this.$store.getters.getUserProf
      this.user_division_mail_dir = this.$store.getters.getUserDivision    
      this.theme_mail_dir = ''
      this.description_mail_dir = ''
      this.checkbox_anonym_mail_dir = false
      this.formMailDir = true
    },
    sendMailDir(){
      var formData = new FormData()
      // Добавление полей
      var data = {
          user_id: this.$store.getters.getUserId,
          from: this.from_mail_dir,
          to: this.to_mail_dir,
          to_full_copy: this.to_full_copy_mail_dir,
          user_name: this.$store.getters.getUserName,
          user_prof: this.$store.getters.getUserProf,
          user_division: this.$store.getters.getUserDivision,
          checkbox_anonym: this.checkbox_anonym_mail_dir,
          theme_mail_dir: this.theme_mail_dir,
          description_mail_dir: this.description_mail_dir 
        };
      for (var key in data) {
        formData.append(key, data[key])
      }
      if(this.theme_mail_dir=='' || this.description_mail_dir==''){
          this.snackText = 'Заполните поля формы!'
          this.snackColor = 'red accent-2'
          this.snackbar = true
        }else{
          this.$store.dispatch('setProgressMain', true)
          var oXmlHttpRequest = new XMLHttpRequest()
          const strRequest = this.$store.getters.getUrlTracker + '?action=send_mail_dir'
          oXmlHttpRequest.open("post",strRequest,true)
          oXmlHttpRequest.onreadystatechange = function(vm){
            if(oXmlHttpRequest.readyState==4){
              vm.$store.dispatch('setProgressMain', false)
              console.log('oXmlHttpRequest.responseText: ', oXmlHttpRequest.responseText)
              var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
              console.log('resJsonObj: ', resJsonObj)
              vm.formMailDir = false
              if(resJsonObj.statsend=='success'){
                vm.snackText = 'Сообщение отправлено!';                
                vm.snackColor = "success";
                vm.snackbar = true;
              }else{
                vm.snackText = 'Ошибка отправки сообщения ('+ resJsonObj.errormess +')!';                
                vm.snackColor = "error";
                vm.snackbar = true;
              }
            }
          }.bind(oXmlHttpRequest, this)
          oXmlHttpRequest.send(formData)
        }
    },
    collapse_nav(){
      this.group_nav_appeals_expanded = false
      this.group_nav_services_expanded = false
    },
    test_Store (){
      alert(this.$store.getters.getUserId + ' | ' + this.$store.getters.getUserLogin + ' | ' + this.$store.getters.getUserName + ' | ' + this.$store.getters.getLogged);
    },
    logout(){
      // this.progress_main = true,
      this.$store.dispatch('setProgressMain', true)
      this.getAjax('logout');
    },
    login_in_form(){
      if(this.loginAuth!=''){
        const authorization = this.loginAuth + ':' + this.passAuth;
        this.getAjax('login_in_form', authorization);
      }else{
        this.snackText = 'Заполните поля формы!';
        this.snackColor = 'red accent-2';
        this.snackbar = true;
      }
    },
    setDispDraw(){
      this.drawer = !this.drawer;
    }, 
    toggleTheme(){
      this.$vuetify.theme.dark = !this.$vuetify.theme.dark;
    },
    getAjax: function(action, authorization='') {  
      // this.progress_main = true; 
      this.$store.dispatch('setProgressMain', true)
      var oXmlHttpRequest = new XMLHttpRequest();
      const strRequest = this.$store.getters.getUrlTracker + '?action=' + action + (action=='login_in_form' ? '&authorization=' + authorization : '') ;
      oXmlHttpRequest.open("get",strRequest,true);
      //oXmlHttpRequest.setRequestHeader("Content-Security-Policy", "upgrade-insecure-requests");
      oXmlHttpRequest.onreadystatechange = function(vm){
                          if(oXmlHttpRequest.readyState==4){
                            // console.log('responseText: ',oXmlHttpRequest.responseText)
                            // vm.progress_main = false; 
                            vm.$store.dispatch('setProgressMain', false)
                            // Авторизация в личном кабинете ОС
                              if(action=='login_start' && oXmlHttpRequest.responseText!=''){
                                var resJsonObj = JSON.parse(oXmlHttpRequest.responseText);
                                // console.log('resJsonObj: ',resJsonObj)
                                if(resJsonObj.statlogged==1){
                                  // console.log('process.env',process.env)
                                  //console.log('statlogged_1: ',resJsonObj.statlogged)
                                  vm.logged = true;
                                  vm.user_name = resJsonObj.data.user_name;
                                  vm.user_login = resJsonObj.data.user_login;
                                  vm.user_id = resJsonObj.data.user_id;
                                  vm.user_photo = resJsonObj.data.user_photo;
                                  // vm.access_appeals_in = resJsonObj.data.access.appeals_in;
                                  vm.$store.dispatch('setUserName', resJsonObj.data.user_name);
                                  vm.$store.dispatch('setUserLogin', resJsonObj.data.user_login);
                                  vm.$store.dispatch('setUserId', resJsonObj.data.user_id);
                                  vm.$store.dispatch('setUserPhoto', resJsonObj.data.user_photo);
                                  vm.$store.dispatch('setUserAccess', resJsonObj.data.access);
                                  vm.$store.dispatch('setUserProf', resJsonObj.data.user_prof);
                                  vm.$store.dispatch('setUserDivision', resJsonObj.data.user_division);
                                  // vm.$store.dispatch('setUserAccess', { 'appeals_in':false, 'news_edit':false });
                                  vm.$store.dispatch('setLogged', true);
                                  if(resJsonObj.data.user_login=='BasicAuthorization'){
                                    vm.color_acc = 'success';
                                    vm.basic_auth = true;
                                  }else{
                                    vm.color_acc = 'warning';
                                    vm.basic_auth = false;
                                  }
                                  if(vm.$store.getters.getTargetPage!='' && vm.$store.getters.getTargetQuery.id!=undefined){
                                    vm.$router.push({ path: '/'+vm.$store.getters.getTargetPage, query: { id: vm.$store.getters.getTargetQuery.id } })
                                    // vm.$store.dispatch('setTargetPage', '')
                                    // vm.$store.dispatch('setTargetQuery', '')
                                  }
                                  // Получаем уведомления пользователя
                                  vm.getAlerts()
                                  setInterval(vm.getAlerts, 60000)
                                }else{
                                  //console.log('statlogged_2: ',resJsonObj.statlogged)
                                  vm.usersSelect = resJsonObj.data;
                                  vm.dialogLogin = true;
                                }
                              }
                              // Авторизация в личном кабинете базовая
                              if(action=='login_in_form' && oXmlHttpRequest.responseText!=''){
                                var resJsonObj = JSON.parse(oXmlHttpRequest.responseText);
                                // console.log('resJsonObj: ',resJsonObj)
                                if(resJsonObj.statlogged==1){
                                  // console.log('process.env',process.env)
                                  vm.logged = true;
                                  vm.user_name = resJsonObj.data.user_name;
                                  vm.user_login = resJsonObj.data.user_login;
                                  vm.user_id = resJsonObj.data.user_id;
                                  vm.user_photo = resJsonObj.data.user_photo;
                                  // vm.access_appeals_in = resJsonObj.data.access.appeals_in;
                                  vm.$store.dispatch('setUserName', resJsonObj.data.user_name);
                                  vm.$store.dispatch('setUserLogin', resJsonObj.data.user_login);
                                  vm.$store.dispatch('setUserId', resJsonObj.data.user_id);
                                  vm.$store.dispatch('setUserPhoto', resJsonObj.data.user_photo);
                                  vm.$store.dispatch('setUserAccess', resJsonObj.data.access);
                                  vm.$store.dispatch('setUserProf', resJsonObj.data.user_prof);
                                  vm.$store.dispatch('setUserDivision', resJsonObj.data.user_division);
                                  vm.$store.dispatch('setLogged', true);
                                  vm.dialogLogin = false;
                                  if(resJsonObj.data.user_login=='BasicAuthorization'){
                                    vm.color_acc = 'success';
                                    vm.basic_auth = true;
                                  }else{
                                    vm.color_acc = 'warning';
                                    vm.basic_auth = false;
                                  }
                                  if(vm.$store.getters.getTargetPage!='' && vm.$store.getters.getTargetQuery.id!=undefined){
                                    vm.$router.push({ path: '/'+vm.$store.getters.getTargetPage, query: { id: vm.$store.getters.getTargetQuery.id } })
                                    // vm.$store.dispatch('setTargetPage', '')
                                    // vm.$store.dispatch('setTargetQuery', '')
                                  }
                                  // Получаем уведомления пользователя
                                  vm.getAlerts()
                                  setInterval(vm.getAlerts, 10000)

                                }else{
                                  vm.snackText = 'Ошибка авторизации!';
                                  vm.snackColor = 'red accent-2';
                                  vm.snackbar = true;
                                }
                              }
                              // Выход из личного кабинета
                              if(action=='logout' && oXmlHttpRequest.responseText!=''){
                                var resJsonObj = JSON.parse(oXmlHttpRequest.responseText);
                                if(resJsonObj.statlogged==2){
                                  vm.logged = false;
                                  vm.$router.push('/');
                                  vm.user_name = '';
                                  vm.user_login = '';
                                  vm.user_id = '';
                                  vm.user_photo = '';
                                  vm.access_appeals_in = false;
                                  vm.$store.dispatch('setUserName', '');
                                  vm.$store.dispatch('setUserLogin', '');
                                  vm.$store.dispatch('setUserId', '');
                                  vm.$store.dispatch('setUserPhoto', '');
                                  vm.$store.dispatch('setUserAccess', {});
                                  vm.$store.dispatch('setLogged', false);
                                  vm.$store.dispatch('setUserProf', '');
                                  vm.$store.dispatch('setUserDivision', '');
                                  vm.loginAuth = '';
                                  vm.passAuth = '';
                                  vm.basic_auth = false;
                                  vm.usersSelect = resJsonObj.data;
                                  vm.alerts = []
                                  vm.$store.dispatch('setProgressMain', false)
                                  vm.snackText = 'Выход выполнен!';
                                  vm.snackColor = "success";
                                  vm.snackbar = true;
                                }
                              }
                            }
                          }.bind(oXmlHttpRequest, this)
      oXmlHttpRequest.send(null);
    },
  },
  beforeMount(){ 
    // const userData = require("./assets/json/config.json")
    // this.$store.dispatch('setUrlTracker', userData.url_tracker)
    // this.$store.dispatch('setUrlCarouselData', userData.url_carousel_data)

    this.$store.dispatch('setUrlTracker', window.location.origin + '/tracker_json.php')
    this.$store.dispatch('setUrlCarouselData', window.location.origin + '/carousel')
    this.$store.dispatch('setUrlEmojiData', window.location.origin + '/sources/emoji') 
    this.$store.dispatch('setUrlAnticorruptionData', window.location.origin + '/sources/anticorruption')  
    this.$store.dispatch('setUrlNewspapersData', window.location.origin + '/sources/newspapers')  
    this.$store.dispatch('setUrlVideogalleryData', window.location.origin + '/sources/videogallery')

    var oXmlHttpRequest = new XMLHttpRequest();
    const strRequest = this.$store.getters.getUrlEmojiData + '/data.json';
    oXmlHttpRequest.open("get",strRequest,false);
    oXmlHttpRequest.send(null);
    var resJsonObj = JSON.parse(oXmlHttpRequest.responseText);
    this.$store.dispatch('setEmojiData', resJsonObj)
    
    document.title = this.title_main
    this.getAjax('login_start')
  },
  mounted(){

  },
}

</script>


<style>
  /* @import "@mdi/font/css/materialdesignicons.min.css"; */
  /*@import "dhx-suite/codebase/suite.min.css";*/
  /* @import "./index.css"; */

  .emoji{
    width: 16px;
    height: 16px;
    border: 0;
    vertical-align: -3px;
    margin: 0 2px;
    display: inline-block;
    overflow: hidden;
  }
  .v-text-small textarea{
    line-height: 1.375rem;
    font-size: .875rem;
    font-weight: 400;
    letter-spacing: .0071428571em;
  }
  .placeholder{
    -webkit-user-select: none;
    -moz-user-select: none;
    -ms-user-select: none;
    user-select: none;
    position: relative;
    z-index: 3;
    cursor: text;
    pointer-events: none;
  }
  .noselect{
    -webkit-touch-callout: none; /* iOS Safari */
      -webkit-user-select: none; /* Safari */
      -khtml-user-select: none; /* Konqueror HTML */
        -moz-user-select: none; /* Old versions of Firefox */
          -ms-user-select: none; /* Internet Explorer/Edge */
              user-select: none; /* Non-prefixed version, currently
                                    supported by Chrome, Edge, Opera and Firefox */
  }
  .text_post div{
    padding-top: 4px;
  }
  .text_post p{
    margin: 12px 0;
  }

  [contenteditable]:focus{
    outline: 0px solid transparent;
  }
  
</style>