
<template>
  <div>
    <v-toolbar rounded class="elevation-0">
      <v-toolbar-title><h3>Исходящие обращения</h3></v-toolbar-title>
      <v-spacer></v-spacer>

      <v-text-field
        v-model="text_search"
        prepend-inner-icon="mdi-magnify"
        class="mr-5"
        style="max-width:300px"
        label="Поиск"
        clearable
        hide-details
        single-line
        @input="inputTextSearch()"
        v-on:keyup.enter="enterSearch()"
      ></v-text-field>

      <v-select
        v-model="status_filter"
        class="mr-5"
        style="max-width:150px"
        clearable
        :items="items_status"
        label="Статус"
        hide-details
        single-line
        @input="setFilter()"
      ></v-select>
      <!-- <v-btn
        color="success"
        @click="openFormAppeal()"
      >
        Создать обращение
      </v-btn> -->
    </v-toolbar>

    <div class="mt-3">
    <v-data-table
      :headers="headers"
      :items="appeals"
      :items-per-page="itemsPerPage"
      :page.sync="page"
      :loading="loading_data"
      loading-text="Поиск..."
      no-data-text="Данные отсутствуют"
      :custom-sort="customSort"
      hide-default-footer
      class="elevation-0 row-pointer"
      @update:options="updateOptions"
      @click:row="onClickRow"
    >
    </v-data-table>

    <v-row align="center">
      <v-col
        class="d-flex"
        cols="12"
        sm="6"
      >
        <v-pagination
          v-model="page"
          :length="pageCount"
          :total-visible="5"
          @input="getAppeals()"
        ></v-pagination>
      </v-col>
      <v-col
        class="d-flex"
        cols="12"
        sm="6"
      >
        <div class="pt-2" style="margin-left:auto; margin-right: 0;">
          <div style="float:left;">
            <v-subheader>
              На странице:
            </v-subheader>
          </div>
          <div style="margin-top:4px; float:left; width:85px;">
            <v-select
              v-model="itemsPerPage"
              :items="itemsCountPerPage"
              @input="setItemsPerPage()"
              solo
              dense
            ></v-select>
          </div>
        </div>
      </v-col>
    </v-row>
    </div> 

    <v-dialog
      v-model="formAppeal"
      max-width="800px"
      persistent
    >
      <v-card>
        <v-toolbar
          color="blue darken-4"
          dark
        >
          <v-toolbar-title>Создание обращения</v-toolbar-title>

          <v-spacer></v-spacer>

          <v-btn icon
            @click="formAppeal = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-toolbar>

        <v-form>
          <v-container class="pa-7">

            <v-autocomplete
              v-model="service_id_f"
              :items="items_for_select"
              item-text="name"
              item-value="id"
              label="Услуга"
              placeholder="Услуга"
              filled
              dense
            ></v-autocomplete>

            <v-text-field
              type="text"
              label="Тема" 
              variant="outlined"
              v-model="theme_f"
              filled
              dense
            ></v-text-field>

            <v-textarea
              clearable
              label="Содержание"
              variant="outlined"
              v-model="description_f"
              no-resize
              filled
            ></v-textarea>

            <v-file-input
              v-model="files_f"
              multiple
              small-chips
              truncate-length="20"
              label="Файлы"
              filled
              dense
            ></v-file-input>

            <v-card-actions>

              <v-btn
                color="primary"
                @click="creatAppeal"
              >
                  Отправить
              </v-btn>

            </v-card-actions>
              

          </v-container>
        </v-form>

      </v-card>
    </v-dialog>

    <v-dialog
      v-model="formViewAppeal"
      max-width="800px"
      persistent
    >
      <v-card>

        <v-toolbar
          color="blue darken-4"
          dark
        >
          <v-toolbar-title>Обращение №:{{ reg_num_app_fv }}</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon
            @click="closeFormViewAppeal()">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-toolbar>

        <v-container style="position:relative; color:white; background-color: #1E88E5;">
          <div style="position:absolute; top:0px; right:0px;" class="pr-1 pt-2">

            <!-- <v-tooltip
              v-if="$store.getters.getUserId==user_ib_fv && (grade_fv==1 || grade_fv==2) && sub_apps_fv.length==0"
              bottom
              color="warning"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn 
                  class="mr-2"
                  elevation="0"
                  fab
                  dark
                  x-small
                  color="warning"
                  @click="returnAppDialog = true"
                  v-bind="attrs"
                  v-on="on"
                >
                  <v-icon dark>mdi-plus-box-multiple-outline</v-icon>
                </v-btn>
              </template>
              <span>Создать обращение повторно</span>
            </v-tooltip> -->

            <v-tooltip
              v-if="sub_apps_fv.length>0"
              bottom
              color="blue darken-3"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn 
                  class="mr-2"
                  elevation="0"
                  fab
                  dark
                  x-small
                  color="primary"
                  @click="openSubAppeal()"
                  v-bind="attrs"
                  v-on="on"
                >
                  <v-icon dark>mdi-arrange-send-backward</v-icon>
                </v-btn>
              </template>
              <span>Открыть подчиненное обращение</span>
            </v-tooltip>

            <v-tooltip
              v-if="parent_app_fv!=''"
              bottom
              color="blue darken-3"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn 
                  class="mr-2"
                  elevation="0"
                  fab
                  dark
                  x-small
                  color="primary"
                  @click="openParentAppeal()"
                  v-bind="attrs"
                  v-on="on"
                >
                  <v-icon dark>mdi-arrange-bring-forward</v-icon>
                </v-btn>
              </template>
              <span>Открыть родительское обращение</span>
            </v-tooltip>

            <v-tooltip
              v-if="$store.getters.getUserId==user_ib_fv && status_app_fv!='Завершен'"
              bottom
              color="error"
            >
              <template v-slot:activator="{ on, attrs }">
                <v-btn 
                  class="mr-2"
                  elevation="0"
                  fab
                  dark
                  x-small
                  color="error"
                  @click="delAppeal()"
                  v-bind="attrs"
                  v-on="on"
                >
                  <v-icon dark>mdi-trash-can-outline</v-icon>
                </v-btn>
              </template>
              <span>Отменить обращение</span>
            </v-tooltip>
          </div>
          <v-chip class="ml-1" label small color="primary">{{ service_name_fv }}</v-chip>
          <v-card-title>{{ theme_app_fv }}</v-card-title>
          <v-card-text>
            <table style="border: none; border-spacing: 0;">
              <tbody>
                <tr>
                  <td class="pr-2">Дата создания:</td><td class="pr-5" style="color:rgba(0,0,0,1)">{{ date_reg_app_fv }}</td>
                  <td class="pl-5 pr-2">Текущий этап:</td><td class="pr-5" style="color:rgba(0,0,0,1)">{{ stage_app_fv }}</td>
                  <td class="pl-5" colspan="2">
                    <!-- <div style="min-height:36px;">
                      <v-tooltip
                        v-if="(grade_fv==1 || grade_fv==2) && sub_apps_fv.length==0"
                        bottom
                        color="blue darken-3"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn 
                            fab
                            dark
                            small
                            color="primary"
                            @click="returnAppDialog = true"
                            v-bind="attrs"
                            v-on="on"
                          >
                            <v-icon dark>mdi-plus-box-multiple-outline</v-icon>
                          </v-btn>
                        </template>
                        <span>Создать обращение повторно</span>
                      </v-tooltip>

                      <v-tooltip
                        v-if="sub_apps_fv.length>0"
                        bottom
                        color="blue darken-3"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn 
                            fab
                            dark
                            small
                            color="primary"
                            @click="openSubAppeal()"
                            v-bind="attrs"
                            v-on="on"
                          >
                            <v-icon dark>mdi-arrange-send-backward</v-icon>
                          </v-btn>
                        </template>
                        <span>Открыть подчиненное обращение</span>
                      </v-tooltip>

                      <v-tooltip
                        v-if="parent_app_fv!=''"
                        bottom
                        color="blue darken-3"
                      >
                        <template v-slot:activator="{ on, attrs }">
                          <v-btn 
                            fab
                            dark
                            small
                            color="primary"
                            @click="openParentAppeal()"
                            v-bind="attrs"
                            v-on="on"
                          >
                            <v-icon dark>mdi-arrange-bring-forward</v-icon>
                          </v-btn>
                        </template>
                        <span>Открыть родительское обращение</span>
                      </v-tooltip>
                    </div> -->
                  </td>
                  <!-- <td></td> -->                 
                </tr>
                <tr>
                  <td class="pr-2">Дата завершения:</td><td class="pr-5" style="color:rgba(0,0,0,1)">{{ date_close_app_fv }}</td>
                  <td class="pl-5 pr-2">Статус:</td><td class="pr-5" style="color:rgba(0,0,0,1)">{{ status_app_fv }}</td>
                  <td class="pl-5 pr-2 pt-1"><div v-if="vis_grade_fv">Оценка:</div></td>
                  <td>
                    <div v-if="vis_grade_fv && status_app_fv=='Завершен'">                     
                      <div v-if="$store.getters.getUserId==user_ib_fv && sub_apps_fv.length==0">
                        <span v-for="n in 5" :key="n">  
                          <v-icon v-if="n<=grade_fv" style="color:rgba(0,0,0,0.54)" @click="setGrade(n, 1)">mdi-star</v-icon>
                          <v-icon v-else style="color:rgba(0,0,0,0.54)" @click="setGrade(n, 0)">mdi-star-outline</v-icon>
                        </span> 
                      </div>                                          
                      <div v-else>
                        <span v-for="n in 5" :key="n">  
                          <v-icon v-if="n<=grade_fv" style="color:rgba(0,0,0,0.54)">mdi-star</v-icon>
                          <v-icon v-else style="color:rgba(0,0,0,0.54)">mdi-star-outline</v-icon>
                        </span> 
                      </div> 
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </v-card-text>
        </v-container>

        <v-divider></v-divider>

        <v-container style="overflow-y: auto; max-height:300px;">
          <v-list two-line>
              <template>
                <div
                  v-for="(item_mess, i) in this.messages_fv"
                  :key="i"
                >
                  <v-list-item>
                    <v-list-item-content>
                      <div>

                        <v-avatar
                          v-if="item_mess.user_photo.length>0 && $store.getters.getUserAccess.appeals_in"
                          size="40"
                          class="mr-2"
                          style="float:left;" 
                        >     
                          <v-img
                            :src="'data:image/jpeg;base64,'+item_mess.user_photo"
                          ></v-img>
                        </v-avatar>
                        <v-avatar
                          v-else
                          :color="getColorAvatar(item_mess.user_name)"
                          size="40"
                          class="mr-2"
                          style="float:left; font-size: 14px;"
                        >
                          <span class="white--text" v-text="getTextAvatar(item_mess.user_name)"></span>
                        </v-avatar>

                        <div>
                          <v-list-item-title style="font-size: 14px;" v-text="item_mess.user_name"></v-list-item-title>
                          <v-list-item-action-text v-text="formatDateTime(item_mess.message_date)"></v-list-item-action-text>
                        </div>
                      </div>
                      <v-card-text class="pt-2 px-0 pb-0">{{item_mess.message_text}}
                        <div>
                          <v-chip
                            small
                            @click="openFile(item_file)"
                            v-for="(item_file, j) in item_mess.message_attachment"
                            :key="j"
                          >
                              {{ item_file.att_name }}.{{ item_file.att_ext }}
                          </v-chip>
                        </div>
                      </v-card-text>
                    </v-list-item-content>  
                  </v-list-item>
                  <v-divider v-if="i<messages_fv.length-1"></v-divider>
                </div>
              </template>
          </v-list>
        </v-container>

        <v-form v-if="vis_mess_fv">
          <v-container class="px-7 pb-7">
            <v-textarea
              clearable
              prepend-icon="mdi-comment"
              label="Текст сообщения"
              hide-details="auto"
              v-model="message_text_fv"
              no-resize
              rows="1"
            ></v-textarea>

            <v-file-input
              class="my-5"
              v-model="message_files_fv"
              multiple
              small-chips
              hide-details="auto"
              truncate-length="20"
              label="Файлы"
              dense
            ></v-file-input>

            <v-card-actions>
              <v-btn
                text
                color="primary"
                @click="addMessageAppeal"
              >
                  Отправить сообщение
              </v-btn>
              <v-spacer></v-spacer>
              <v-btn
              text
              color="error"
              @click="closeFormViewAppeal()"
            >
              Закрыть
            </v-btn>
            </v-card-actions>
          </v-container>
        </v-form>
        <div v-else>
          <v-divider></v-divider>
          <v-card-actions class="pa-5">
            <v-btn 
              v-if="$store.getters.getUserId==user_ib_fv && sub_apps_fv.length==0"
              text
              color="primary"
              @click="cause_repeat_fv=''; returnAppDialog = true;"
            >
              Создать повторно
            </v-btn>
            <v-spacer></v-spacer>
            <v-btn
              text
              color="error"
              @click="closeFormViewAppeal()"
            >
              Закрыть
            </v-btn>
          </v-card-actions>
        </div>
        
        <v-overlay :value="progressAppDialog">
          <v-progress-circular
            :size="70"
            color="white"
            indeterminate
          ></v-progress-circular>
        </v-overlay>
      </v-card>
    </v-dialog>

    <v-dialog
      persistent
      v-model="addCommentDialog"
      max-width="500px"
    >
      <v-card>
        <v-form>
          <v-container class="px-7 pt-7">
            <v-textarea
              auto-grow
              outlined
              clearable
              hide-details="true"
              label="Комментарий к оценке"
              v-model="comment_fv"
            ></v-textarea>
            <div
              v-if="legend_vis_fv"
              class="mt-2 text-caption red--text"
            >* Комментарий обязателен!
            </div>
            <v-card-actions>
              <v-btn
                text
                color="error"
                @click="cancelComment()"
              >
                Отменить
              </v-btn>
              <v-spacer></v-spacer>
              <v-btn
                text
                color="primary"
                @click="applyComment()"
              >
                Установить оценку
              </v-btn>
            </v-card-actions>
          </v-container>
        </v-form>
      </v-card>
    </v-dialog>

    <v-dialog
      persistent
      v-model="returnAppDialog"
      max-width="450px"
    >
      <v-card>
        <v-form>
          <v-container class="px-4 pt-4">
            <!-- <v-card-text>Будет создано новое обращение на основании текущего!</v-card-text> -->
            <v-textarea
              auto-grow
              outlined
              clearable
              hide-details="true"
              label="Причина повторного создания обращения"
              v-model="cause_repeat_fv"
            ></v-textarea>
            <div
              class="mt-2 text-caption red--text"
            >* Поле обязательно для заполнения!
            </div>
            <v-card-actions>
              <v-btn
                text
                color="error"
                @click="returnAppDialog=false"
              >
                Отменить
              </v-btn>
              <v-spacer></v-spacer>
              <v-btn
                text
                color="primary"
                @click="returnAppeal()"
              >
                Создать
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
    <v-overlay
      absolute="true"
      :value="overlay"
    >
      <v-progress-circular
        :size="70"
        color="white"
        indeterminate
      ></v-progress-circular>
    </v-overlay>
  </div>
</template> 
  
<script>

  const pause = ms => new Promise(resolve => setTimeout(resolve, ms))

  export default {
    data: () => ({
      overlay: false,
      loading_data: false,

      sortBy: undefined,
      sortDesc: false,

      options: {},

      sort_reg_date: null,
      sort_close_date: null,
      sort_name: null,
      sort_status: null,
      sort_num: null,
      sort_stage: null,

      page: 1,
      pageCount: 0,
      itemsPerPage: 10,
      itemsCountPerPage:[ 10,20,50,100 ],
      headers: [
          { text: 'Тема', value: 'name', },
          { text: 'Номер', value: 'number', },
          { text: 'Дата регистрации', value: 'registration_date', },
          { text: 'Дата закрытия', value: 'closing_date', },
          { text: 'Текущий этап', value: 'stage', },
          { text: 'Статус', value: 'status', },
        ],
      appeals: [],
      formAppeal: false,
      formViewAppeal: false,
      service_id_f: '',
      theme_f: '',
      description_f: '',
      files_f: [],
      
      returnAppDialog: false,

      items: [],
      items_for_select: [],
      snackbar: false,
      snackColor:'blue-grey',
      snackText: '',
      text_search: '',
      text_search_last: '',
      status_filter: '',
      items_status: [ 'Черновик','Зарегистрирован','Принят к выполнению','Эскалация','Завершен','Все незакрытые' ],
      colors: [ 'teal darken-2', 'deep-orange darken-2', 'purple darken-2', 'green darken-2' ],
      colors_users: [],

      addCommentDialog: false,
      messages_fv: [],
      status_app_fv: '',
      stage_app_fv: '',
      date_reg_app_fv: null,
      date_close_app_fv: null,
      theme_app_fv: '',
      reg_num_app_fv: '',
      id_app_fv: '',
      process_fv: '',
      grade_fv: 0,
      grade_old_fv: 0,
      comment_fv: '',
      message_text_fv: '',
      message_files_fv: [],
      service_name_fv: '',
      vis_mess_fv: false,
      user_ib_fv: '',
      vis_grade_fv: false,
      sub_apps_fv: [],
      parent_app_fv: '',
      legend_vis_fv: false,
      cause_repeat_fv: '',
      progressAppDialog: false,
    }),
    methods:{
      test(){
        alert('test')
      },
      openParentAppeal(){
        if(this.parent_app_fv!=''){
          const id = this.parent_app_fv
          this.closeFormViewAppeal()
          this.openFormViewAppeal(id)
        }
      },
      openSubAppeal(){
        if(this.sub_apps_fv.length>0){
          const id = this.sub_apps_fv[0].id
          this.closeFormViewAppeal()
          this.openFormViewAppeal(id)
        }
      },
      returnAppeal(){
        if(this.cause_repeat_fv.length!=0){
          this.returnAppDialog = false
          this.progressAppDialog = true
          var formData = new FormData()
          // Добавление полей
          var data = {
            id_app: this.id_app_fv,
            cause_repeat: this.cause_repeat_fv,
          }
          for (var key in data) {
            formData.append(key, data[key])
          }

          var oXmlHttpRequest = new XMLHttpRequest()
          const strRequest = this.$store.getters.getUrlTracker + '?action=return_appeal'
          // const strRequest = this.$store.getters.getUrlTracker + '?action=return_appeal&id_app=' + this.id_app_fv + '&cause_repeat=' + this.cause_repeat_fv
          // console.log('strRequest: ',strRequest)
          oXmlHttpRequest.open("post",strRequest,true)
          oXmlHttpRequest.onreadystatechange = function(vm){
            if(oXmlHttpRequest.readyState==4){
              vm.progressAppDialog = false             
              var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
              if(resJsonObj.error==""){
                // vm.appeals.unshift({ ID:resJsonObj.id, closing_date:'', name:resJsonObj.name, number:resJsonObj.num, process:resJsonObj.process, registration_date:resJsonObj.registration_date==''?'':resJsonObj.registration_date, stage:resJsonObj.stage, status:resJsonObj.status })
                vm.getAppeals()
                vm.sub_apps_fv.push({ id:resJsonObj.id, num:resJsonObj.num, name:resJsonObj.name })
                vm.snackText = 'Повторное обращение создано!'
                vm.snackColor = "success"
                vm.snackbar = true
              }else{
                vm.snackText = 'Ошибка: ' + resJsonObj.error
                vm.snackColor = 'red accent-2'
                vm.snackbar = true
              }
            }
          }.bind(oXmlHttpRequest, this)
          oXmlHttpRequest.send(formData)
        }else{
          this.snackText = 'Укажите причину повторного создания обращения!'
          this.snackColor = "red accent-2"
          this.snackbar = true
        }
      },
      delAppeal(){
        this.progressAppDialog = true
        var oXmlHttpRequest = new XMLHttpRequest()
        const strRequest = this.$store.getters.getUrlTracker + '?action=del_appeal&id=' + this.id_app_fv
        // console.log('strRequest: ',strRequest)
        oXmlHttpRequest.open("get",strRequest,true)
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            vm.progressAppDialog = false
            vm.returnAppDialog = false
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
            if(resJsonObj.error==""){
              vm.getAppeals()
              vm.closeFormViewAppeal()
              vm.sub_apps_fv.push({ id:resJsonObj.id, num:resJsonObj.num, name:resJsonObj.name })
              vm.snackText = 'Обращение удалено!'
              vm.snackColor = "success"
              vm.snackbar = true
            }else{
              vm.snackText = 'Ошибка: ' + resJsonObj.error
              vm.snackColor = 'red accent-2'
              vm.snackbar = true
            }
          }
        }.bind(oXmlHttpRequest, this)
        oXmlHttpRequest.send(null)
      },
      cancelComment(){
        this.grade_fv = this.grade_old_fv
        this.grade_old_fv = 0
        this.comment_fv = ''
        this.addCommentDialog = false
      },
      applyComment(){
        if(this.comment_fv.length==0 && this.grade_fv<4){
          this.snackText = 'Заполните комментарий к оценке!'
          this.snackColor = "red accent-2"
          this.snackbar = true
        }else{
          this.sendGrade()
          this.comment_fv = ''
          this.addCommentDialog = false
        }       
      },
      sendGrade(){
        var oXmlHttpRequest = new XMLHttpRequest()
        const strRequest = this.$store.getters.getUrlTracker + '?action=set_grade&id_app=' + this.id_app_fv + '&grade=' + this.grade_fv + '&comment=' + this.comment_fv
        oXmlHttpRequest.open("get",strRequest,true)
        oXmlHttpRequest.onreadystatechange = function(vm){
            if(oXmlHttpRequest.readyState==4){
              // console.log('responseText: ', oXmlHttpRequest.responseText)
              var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
              if(resJsonObj.error_flag==0){
                if(vm.grade_fv==0) vm.snackText = 'Оценка удалена!'
                else vm.snackText = 'Оценка установлена!'
                vm.snackColor = "success"
                vm.snackbar = true
              }else{
                vm.snackText = 'Ошибка: '+ resJsonObj.message
                vm.snackColor = "red accent-2"
                vm.snackbar = true
                vm.grade_fv = 0
                vm.comment_fv = ''
              }
            } 
          }.bind(oXmlHttpRequest, this)
          oXmlHttpRequest.send(null);
      },
      setGrade(n, state){
        // console.log('getUserId: ',this.$store.getters.getUserId)
        if(n==1 && state==1 && this.grade_fv==1){
          this.grade_fv = 0
          this.comment_fv = ''
          this.sendGrade()
        }else{         
          this.grade_old_fv = this.grade_fv
          this.grade_fv = n
          if(this.grade_fv!=this.grade_old_fv){
            if(this.grade_fv<5){
              if(this.grade_fv<4) this.legend_vis_fv = true
              else this.legend_vis_fv = false
              this.addCommentDialog = true
            }else{
              this.sendGrade()
            }
          }         
        }       
        // console.log('n: ',n)
        // console.log('state: ',state)
      },
      setVisMessage(){
        if(this.status_app_fv=='Завершен') this.vis_mess_fv = false; else this.vis_mess_fv = true;
      },
      openFile(item_file){
        const name_file = item_file.att_name + '.' +  item_file.att_ext;
        var a = document.createElement("a");
        a.href = "data:;base64," + item_file.att_data;
        a.download = name_file;
        a.click();
      },
      addMessageAppeal(){
        if(this.message_text_fv!=''){
          var formData = new FormData();
          // Добавление полей
          var data = {
            id_appeal: this.id_app_fv,
            content: this.message_text_fv,
            process: this.process_fv
          };
          for (var key in data) {
            formData.append(key, data[key]);
          }
          for (var key in this.message_files_fv) {
            formData.append(key, this.message_files_fv[key]);
          }
          //console.log('data: ',data)
          //console.log('formData: ',formData)
          var xhr = new XMLHttpRequest();
          xhr.onreadystatechange = function(vm){
            if(xhr.readyState==4){
              //console.log('response_add_message:', xhr.responseText) 
              var resJsonObj = JSON.parse(xhr.responseText);
              if(resJsonObj.error_flag==0){
                vm.message_text_fv = '';
                vm.message_files_fv = [];
                vm.updateListMessage(vm.id_app_fv, vm.process_fv);
                vm.snackText = 'Сообщение отправлено!';
                vm.snackColor = "success";
                vm.snackbar = true;
              }else{
                vm.snackText = 'Ошибка отправки сообщения!';
                vm.snackColor = "red accent-2";
                vm.snackbar = true;
              }
            }
          }.bind(xhr, this)
          xhr.open("post", this.$store.getters.getUrlTracker + "?action=add_message_appeal", true);
          xhr.send(formData);
        }else{
          this.snackText = 'Напишите текст сообщения!';
          this.snackColor = 'red accent-2';
          this.snackbar = true;
        }
      },
      updateListMessage(id, process){
        var oXmlHttpRequest = this.getDataAppeal(id);
        if (oXmlHttpRequest.status == 200){
          var resJsonObj = JSON.parse(oXmlHttpRequest.responseText);

          const countMess = this.messages_fv.length;
          const messagesNew = resJsonObj.payload[0].messages.slice(countMess);

          var index;
          for (index = 0; index < messagesNew.length; ++index) {
            this.messages_fv.push(messagesNew[index])
          }
        }
      },
      getColorAvatar(name_user){
        var item_color_user = this.colors_users.find(item => item.name_user == name_user)
        return item_color_user.color
      },
      getTextAvatar(text_in){
        if(text_in!=''){
          var arrayOfStrings = text_in.split(' ');
          var letter_1 = arrayOfStrings[0][0].toUpperCase();
          var letter_2 = arrayOfStrings.length>1 ? arrayOfStrings[1][0].toUpperCase() : '';
          return letter_1 + letter_2;
        }else{
          return '';
        }
        
      },
      closeFormViewAppeal(){
        this.formViewAppeal = false
        this.messages_fv = []
        this.status_app_fv = ''
        this.stage_app_fv = ''
        this.date_reg_app_fv = null
        this.date_close_app_fv = null
        this.theme_app_fv = ''
        this.reg_num_app_fv =''
        this.grade_fv = 0
        this.comment_fv = ''
        this.id_app_fv = ''
        this.message_text_fv = ''
        this.message_files_fv = []
        this.process_fv = ''
        this.colors_users = []
        this.service_name_fv = ''
        this.vis_mess_fv = false
        this.user_ib_fv = ''
        this.vis_grade_fv = false
        this.sub_apps_fv = []
        this.parent_app_fv = ''
      },
      getDataAppeal(id){
        var oXmlHttpRequest = new XMLHttpRequest()
        const strRequest = this.$store.getters.getUrlTracker + '?action=get_appeal_item&id=' + id + '&process=Запрос'
        oXmlHttpRequest.open("get",strRequest, false)
        oXmlHttpRequest.send(null)
        return oXmlHttpRequest
      },
      openFormViewAppeal(id){
        this.$store.dispatch('setProgressMain', true)
        // this.progressAppDialog = true
        // var oXmlHttpRequest = this.getDataAppeal(id)
        var oXmlHttpRequest = new XMLHttpRequest()
        const strRequest = this.$store.getters.getUrlTracker + '?action=get_appeal_item&id=' + id + '&process=Запрос'
        oXmlHttpRequest.open("get",strRequest, true)
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            vm.$store.dispatch('setProgressMain', false)
            // vm.progressAppDialog = false
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
            // console.log('get_appeal_item_resJsonObj:', resJsonObj)       
            if (oXmlHttpRequest.status == 200){
              // Назначаем цвета аватаров для переписки и заполняем массив colors_users
              var messagesPayload = resJsonObj.payload[0].messages
              var index_color = 0
              var index
              for (index = 0; index < messagesPayload.length; ++index) {
                const name_user = messagesPayload[index].user_name
                if(vm.colors_users.length>0){
                  if(vm.colors_users.find(item => item.name_user == name_user) == undefined){
                    vm.colors_users.push({ 'name_user': name_user, 'color': vm.colors[index_color] })
                    index_color++
                  }
                }else{
                  vm.colors_users.push({ 'name_user': name_user, 'color': vm.colors[index_color] })
                  index_color++
                } 
              }
              //console.log('colors_users: ', vm.colors_users);   
              vm.messages_fv = resJsonObj.payload[0].messages
              vm.status_app_fv = resJsonObj.payload[1].default_value        
              vm.service_name_fv = resJsonObj.payload[2].default_value.name
              vm.date_reg_app_fv = resJsonObj.payload[4].default_value==''?'':vm.formatDateTime(resJsonObj.payload[4].default_value)
              vm.date_close_app_fv = resJsonObj.payload[5].default_value==''?'':vm.formatDateTime(resJsonObj.payload[5].default_value)
              vm.reg_num_app_fv = resJsonObj.payload[6].default_value
              vm.comment_fv = ''
              vm.stage_app_fv = typeof resJsonObj.payload[9].default_value=="undefined"?'':resJsonObj.payload[9].default_value.name
              vm.theme_app_fv = resJsonObj.payload[10].default_value
              vm.grade_fv = resJsonObj.payload[15].vote.mark
              vm.vis_grade_fv = resJsonObj.payload[15].vote.visible
              vm.user_ib_fv = resJsonObj.payload[16].default_value  
              vm.parent_app_fv = resJsonObj.payload[17].default_value
              vm.sub_apps_fv = resJsonObj.payload[18].default_value      
              vm.id_app_fv = id
              vm.process_fv = "Запрос"
              vm.setVisMessage()
              vm.formViewAppeal = true
            }else{
              vm.snackText = 'Ошибка получения данных!'
              vm.snackColor = 'red accent-2'
              vm.snackbar = true
            }
          }
        }.bind(oXmlHttpRequest, this)
        oXmlHttpRequest.send(null)       
      },
      onClickRow(item){
        this.openFormViewAppeal(item.ID)
      },
      customSort: function(items, index, isDesc) {
        return items;
      },
      updateOptions(options){
        // Сортировка
        //console.log('SortBy: ',options.sortBy)
        //console.log('SortDesc: ',options.sortDesc)

        var sortByNew = options.sortBy.length==0? undefined : options.sortBy[0]
        var sortDescNew = options.sortDesc.length==0? undefined : options.sortDesc[0]
        if(sortByNew!==this.sortBy || sortDescNew!==this.sortDesc){
          this.sortBy = sortByNew
          this.sortDesc = sortDescNew

          this.sort_name = null
          this.sort_num = null
          this.sort_stage = null
          this.sort_reg_date = null
          this.sort_close_date = null
          this.sort_status = null

          if(this.sortBy=='name')this.sort_name = !this.sortDesc
          if(this.sortBy=='number')this.sort_num = !this.sortDesc
          if(this.sortBy=='stage')this.sort_stage = !this.sortDesc
          if(this.sortBy=='registration_date')this.sort_reg_date = !this.sortDesc
          if(this.sortBy=='closing_date')this.sort_close_date = !this.sortDesc
          if(this.sortBy=='status')this.sort_status = !this.sortDesc

          this.getAppeals()
        }

      },
      async inputTextSearch(){
        if(this.text_search){
          var initText = this.text_search
          await pause(2000)
          if(initText==this.text_search && this.text_search!=this.text_search_last) this.searchAppeals()
        }else{
          this.text_search_last = ''
          this.searchAppeals() 
        }
      },
      enterSearch(){
        if(this.text_search!=this.text_search_last)this.searchAppeals();
      },
      searchAppeals(){
        this.text_search_last = this.text_search;
        this.getAppeals();
      },
      setItemsPerPage(){
        this.page = 1;
        this.getAppeals();
      },
      setFilter(){
        this.getAppeals();
      },
      openFormAppeal(){
        this.formAppeal = true;
      },
      getItemsServices(){     
        var oXmlHttpRequest = new XMLHttpRequest();
        const strRequest = this.$store.getters.getUrlTracker + '?action=get_services&group_id=' + '&search=';
        oXmlHttpRequest.open("get",strRequest,true);
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText);
            vm.items = resJsonObj.payload;

            // Заполняем список услу без групп для выбора
            const items_all = vm.items;
            items_all.forEach(function(item, i, items_all) {
              if(!item.group) vm.items_for_select.push({ 'id': item.ID, 'name':item.name })
            });       
          }
        }.bind(oXmlHttpRequest, this)
        oXmlHttpRequest.send(null);
      },
      formatDate(date_str) {
        let date = new Date(date_str);
        var dd = date.getDate();
        if (dd < 10) dd = '0' + dd;
        var mm = date.getMonth() + 1;
        if (mm < 10) mm = '0' + mm;
        var YY = date.getFullYear();
        return dd + '.' + mm + '.' + YY;
      },
      formatDateTime(date_str) {
        let date = new Date(date_str);
        var dd = date.getDate();
        if (dd < 10) dd = '0' + dd;
        var mm = date.getMonth() + 1;
        if (mm < 10) mm = '0' + mm;
        var YY = date.getFullYear();
        var HH = date.getHours();
        if (HH < 10) HH = '0' + HH;
        var min = date.getMinutes();
        if (min < 10) min = '0' + min;
        return dd + '.' + mm + '.' + YY + ' ' + HH + ':' + min;
      },
      getAppeals(){
        var status_filter_='';
        var text_search_ = '';
        var sort_reg_date_ = '';
        var sort_close_date_ = '';
        var sort_name_ = '';
        var sort_status_ = '';
        var sort_num_ = '';
        var sort_stage_ = '';
        if(this.status_filter === null) {status_filter_ = '';} else {status_filter_ = this.status_filter;}
        if(this.text_search === null) {text_search_ = '';} else {text_search_ = this.text_search;}
        if(this.sort_reg_date === null) {sort_reg_date_ = '';} else {sort_reg_date_ = this.sort_reg_date;}
        if(this.sort_close_date === null) {sort_close_date_ = '';} else {sort_close_date_ = this.sort_close_date;}
        if(this.sort_name === null) {sort_name_ = '';} else {sort_name_ = this.sort_name;}
        if(this.sort_status === null) {sort_status_ = '';} else {sort_status_ = this.sort_status;}
        if(this.sort_num === null) {sort_num_ = '';} else {sort_num_ = this.sort_num;}
        if(this.sort_stage === null) {sort_stage_ = '';} else {sort_stage_ = this.sort_stage;}

        this.loading_data = true;
        var oXmlHttpRequest = new XMLHttpRequest();
        const strRequest = this.$store.getters.getUrlTracker + '?action=get_appeals_out&page=' + this.page + '&per_page=' + this.itemsPerPage + '&status_value=' + status_filter_ + '&search=' + text_search_ + '&sort_reg_date=' + sort_reg_date_ + '&sort_close_date=' + sort_close_date_ + '&sort_name=' + sort_name_ + '&sort_status=' + sort_status_ + '&sort_num=' + sort_num_ + '&sort_stage=' + sort_stage_;
        // console.log('strRequest: ',strRequest);
        oXmlHttpRequest.open("get",strRequest,true);
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            // console.log('responseText: ', oXmlHttpRequest.responseText);
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText);
            // console.log('resJsonObj: ', resJsonObj);
            // vm.pageCount = resJsonObj.payload.pagination.pages;
            vm.pageCount = resJsonObj.pagination.pages;
            const data_table = resJsonObj.data;
            //console.log('length_appeals', data_table.length);
            // Меняем формат представления дат для таблицы
            //const data_table_res = [];
            vm.appeals = []
            data_table.forEach(function(item, i, data_table) {
              //data_table_res[i] = { ID: item.ID, closing_date: item.closing_date==''?'':vm.formatDate(item.closing_date), name: item.name, number: item.number, process: item.process, registration_date: item.registration_date==''?'':vm.formatDate(item.registration_date), stage: item.stage, status: item.status }
              vm.appeals.push({ ID: item.ID, closing_date: item.closing_date==''?'':item.closing_date, name: item.name, number: item.number, process: item.process, registration_date: item.registration_date==''?'':item.registration_date, stage: item.stage, status: item.status })
            });
            vm.loading_data = false;
          }
        }.bind(oXmlHttpRequest, this)
        oXmlHttpRequest.send(null);
      },
      creatAppeal(){
        if(this.theme_f!='' && this.description_f!='' && this.service_id_f!=''){
          
          var formData = new FormData();
          // Добавление полей
          var data = {
            user_id: this.$store.getters.getUserId,
            service_id: this.service_id_f,
            theme: this.theme_f,
            description: this.description_f,
          };
          for (var key in data) {
            formData.append(key, data[key])
          }
          for (var key in this.files_f) {
            formData.append(key, this.files_f[key])
          }
          var xhr = new XMLHttpRequest()
          xhr.onreadystatechange = function(vm){
            if(xhr.readyState==4){
              var resJsonObj = JSON.parse(xhr.responseText)
              if(resJsonObj.error_flag==0){
                vm.formAppeal = false
                vm.service_id_f = ''
                vm.theme_f = ''
                vm.description_f = ''
                vm.files_f = []
                vm.snackText = 'Обращение создано!'
                vm.overlay = false
                vm.snackColor = "success"
                vm.snackbar = true
              }else{
                vm.snackText = 'Ошибка создания обращения!'
                vm.overlay = false
                vm.snackColor = "red accent-2"
                vm.snackbar = true
              }
            }
          }.bind(xhr, this)
          xhr.open("post", this.$store.getters.getUrlTracker + "?action=create_appeal", true)
          xhr.send(formData)
        }else{
          this.snackText = 'Заполните поля формы!'
          this.snackColor = 'red accent-2'
          this.snackbar = true
        }
      },
    },
    beforeMount(){
      // if(!this.$store.getters.getLogged)this.$router.push('/')
    },
    mounted(){
      this.getAppeals()
      this.getItemsServices()
      // console.log('getTargetQuery:', this.$store.getters.getTargetQuery)
      if(this.$store.getters.getTargetQuery.id!=undefined){
        const id = this.$store.getters.getTargetQuery.id
        this.openFormViewAppeal(id)
        this.$store.dispatch('setTargetPage', '')
        this.$store.dispatch('setTargetQuery', '')
      }
    },
  }
</script>

<style lang="css" scoped>
.row-pointer >>> tbody tr :hover {
  cursor: pointer;
}
</style>

