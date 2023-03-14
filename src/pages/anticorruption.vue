<template>
    <div>

      <v-toolbar rounded class="elevation-0">
          <v-toolbar-title><h3>Противодействие коррупции</h3></v-toolbar-title>
          <v-spacer></v-spacer>
      </v-toolbar>

      <v-card
        class="mt-3"
        outlined
        elevation="0"
      >
        
        <v-list
          subheader
        >
          <div
            v-for="group in this.items"
            :key="group.id"
          >
            <v-subheader inset>
              {{ group.name }}
            </v-subheader>

            <v-list-item
              v-for="item in group.items"
              :key="item.id"
              :href="$store.getters.getUrlAnticorruptionData+'/'+item.url"
              target="_blank"
            >
              <v-list-item-avatar>
                <v-icon
                  v-if="group.id=='videos'"
                  color="blue"
                  large
                >
                  mdi-file-video
                </v-icon>
                <v-icon
                  v-if="group.id=='docs'"
                  color="blue"
                  large
                >
                  mdi-file-document
                </v-icon>
              </v-list-item-avatar>

              <v-list-item-content>
                <v-list-item-title class="blue--text" v-text="item.name"></v-list-item-title>

                <!-- <v-list-item-subtitle v-text="folder.subtitle"></v-list-item-subtitle> -->
              </v-list-item-content>

              <!-- <v-list-item-action>
                <v-btn icon>
                  <v-icon color="grey lighten-1">mdi-information</v-icon>
                </v-btn>
              </v-list-item-action> -->
            </v-list-item>
          </div>
        </v-list>

        <v-card-actions class="px-6 pb-6">
          <v-btn
            small
            color="primary"
            elevation="0"
            @click="openFormMail()"
          >
            Сообщить о коррупции
          </v-btn>
        </v-card-actions>

      </v-card>

      <v-dialog
        v-model="formMail"
        max-width="800px"
        persistent
      >
        <v-card>
          <v-toolbar
            color="blue darken-4"
            dark
          >
            <v-toolbar-title>Сообщение о коррупции</v-toolbar-title>

            <v-spacer></v-spacer>

            <v-btn icon
              @click="formMail = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-toolbar>

          <v-form>
            <v-container class="pa-7">

              <v-textarea
                clearable
                label="Текст сообщения"
                variant="outlined"
                v-model="description_mail"
                no-resize
                filled
              ></v-textarea>

              <v-text-field
                type="text"
                disabled
                label="ФИО работника" 
                variant="outlined"
                v-model="user_name_mail"
                filled
                dense
              ></v-text-field>

              <v-text-field
                type="text"
                disabled
                label="Должность/профессия" 
                variant="outlined"
                v-model="user_prof_mail"
                filled
                dense
              ></v-text-field>

              <v-text-field
                type="text"
                disabled
                label="Подразделение" 
                variant="outlined"
                v-model="user_division_mail"
                filled
                dense
              ></v-text-field>

              <v-card-actions>                 
                <v-btn
                  color="primary"
                  @click="sendMail"
                  text
                >
                    Отправить
                </v-btn>
                <v-spacer></v-spacer>
                <v-btn
                  color="error"
                  @click="formMail = false"
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

      <v-overlay :value="$store.getters.getProgressMain">
        <v-progress-circular
          :size="70"
          color="white"
          indeterminate
        ></v-progress-circular>
      </v-overlay>

    </div>
</template>

<script>
  export default {
    data () {
      return {
        items: [],

        formMail: false,
        from_mail: 'portal@kmz.ru',
        to_mail: 'otimoshechkin@kmz.ru',
        to_full_copy_mail: 'portal@kmz.ru',
        user_name_mail: '',
        user_prof_mail: '',
        user_division_mail: '',
        description_mail: '',

        snackbar: false,
        snackText: '',
        snackColor:'blue-grey',
      }
    },
    methods: {
      openFormMail(){
        this.user_name_mail = this.$store.getters.getUserName
        this.user_prof_mail = this.$store.getters.getUserProf
        this.user_division_mail = this.$store.getters.getUserDivision    
        this.description_mail = ''
        this.formMail = true
      },
      sendMail(){
        var formData = new FormData()
        // Добавление полей
        var data = {
            user_id: this.$store.getters.getUserId,
            from: this.from_mail,
            to: this.to_mail,
            to_full_copy: this.to_full_copy_mail,
            user_name: this.$store.getters.getUserName,
            user_prof: this.$store.getters.getUserProf,
            user_division: this.$store.getters.getUserDivision,
            description_mail: this.description_mail
          };
        for (var key in data) {
          formData.append(key, data[key])
        }
        if(this.description_mail==''){
          this.snackText = 'Заполните поля формы!'
          this.snackColor = 'red accent-2'
          this.snackbar = true
        }else{
          this.$store.dispatch('setProgressMain', true)
          var oXmlHttpRequest = new XMLHttpRequest()
          const strRequest = this.$store.getters.getUrlTracker + '?action=send_mail_corr'
          oXmlHttpRequest.open("post",strRequest,true)
          oXmlHttpRequest.onreadystatechange = function(vm){
            if(oXmlHttpRequest.readyState==4){
              vm.$store.dispatch('setProgressMain', false)
              // console.log('oXmlHttpRequest.responseText: ', oXmlHttpRequest.responseText)
              var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
              // console.log('resJsonObj: ', resJsonObj)
              vm.formMail = false
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
    },
    beforeMount(){
      var oXmlHttpRequest = new XMLHttpRequest()
      const strRequest = this.$store.getters.getUrlAnticorruptionData + '/data.json'
      oXmlHttpRequest.open("get",strRequest,false)
      oXmlHttpRequest.send(null)
      var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
      // console.log('resJsonObj: ',resJsonObj)
      this.items = resJsonObj
    },
  }
</script>

<style>

</style>