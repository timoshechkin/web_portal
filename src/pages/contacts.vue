<template>
  <div>
    <v-toolbar rounded class="elevation-0">
      <v-toolbar-title><h3>Адресная книга</h3></v-toolbar-title>
      <v-spacer></v-spacer>

      <v-tooltip
        bottom color="success"
        v-if="mode_search"
      >
        <template v-slot:activator="{ on, attrs }">
          <v-btn
            :disabled="!mode_search"
            color="success"
            icon
            v-bind="attrs"
            v-on="on"
            @click="endSearcheMode"
            class="mr-2"
          >
            <v-icon>mdi-family-tree</v-icon>
          </v-btn>
        </template>
        <span>Вернуться к структуре</span>
      </v-tooltip>

      <!-- <v-btn
        v-if="mode_search"
        text
        color="success"
        @click="endSearcheMode"
        class="mr-2"
      >
        Завершить поиск
      </v-btn> -->
      <v-text-field
        v-model="text_search"
        prepend-inner-icon="mdi-magnify"
        class="mr-5"
        style="max-width:500px"
        label="Поиск"
        clearable
        hide-details
        single-line
        @input="inputTextSearch()"
        v-on:keyup.enter="enterSearch()"
      ></v-text-field>
    </v-toolbar>

    <div>
      <v-row
        class="mt-3"
      >
        <v-col v-if="!mode_search" cols="7">
          <div class="mb-4">
            <v-card
              outlined
            >
            <v-card-title>Телефоны экстренных служб</v-card-title>
            <v-card-text>
              01, 101 — пожарная охрана<br>
              02, 102 — полиция<br> 
              03, 103 — скорая медицинская помощь<br>
              04, 104 — аварийная газовая служба<br>
              112 — Единый номер вызова экстренных оперативных служб<br>
            </v-card-text>
            </v-card>
          </div>

          <v-treeview
            ref="tree"
            :active.sync="active"
            :items="items"
            :open.sync="open"
            :load-children="pushChilds"
            activatable
            dense
            open-on-click
            transition
          >
          <!-- @update:open="updateOpenTree" -->
              <!-- <template v-slot:label="{ item }">
                <v-icon v-if="item.type=='group'" color="yellow accent-4">
                  {{ open.indexOf(item.id)!=-1 ? 'mdi-folder-open' : 'mdi-folder' }}
                </v-icon>
                <v-icon v-if="item.type=='user'" color="primary">
                  mdi-account
                </v-icon>
                  {{ item.name}} <span class="primary--text">{{ item.data.profession }}</span>
              </template> -->

              <template v-slot:label="{ item }">
                
                <v-tooltip bottom color="primary">
                  <template v-slot:activator="{ on, attrs }"> 
                    <div
                      v-bind="attrs"
                      v-on="on"
                    >

                      <v-icon v-if="item.type=='group'" color="yellow accent-4">
                        {{ open.indexOf(item.id)!=-1 ? 'mdi-folder-open' : 'mdi-folder' }}
                      </v-icon>
                      <v-icon v-if="item.type=='user'" color="primary">
                        mdi-account
                      </v-icon>
                      {{ item.name}} {{ item.type=='user' ? '(':'' }}{{ item.data.profession }}{{ item.type=='user' ? ')':'' }}

                    </div>
                  </template>
                  <span><div>{{ item.name}}</div><div>{{ item.data.profession }}</div></span>
                </v-tooltip>

              </template>

            

          </v-treeview>
        </v-col>

        <v-col v-if="mode_search" cols="7">
          <v-data-table
            v-model="selected_search"
            item-key="ID"
            :headers="headers"
            :items="items_search"
            :items-per-page="itemsPerPage"
            :page.sync="page"
            :options.sync="options"
            :loading="loading_data"
            loading-text="Поиск..."
            no-data-text="Данные отсутствуют"
            hide-default-footer
            class="elevation-0"
            @page-count="pageCount = $event"   
          >
            <template v-slot:[`item.data.photo`]="{ item }">          
              <v-avatar
                v-if="$store.getters.getUserAccess.appeals_in"
                size="40" 
              >     
                <v-img
                  style="cursor:pointer;"
                  @click="openCardPhoto(item.data.photo)"
                  :src="'data:image/jpeg;base64,'+item.data.photo"
                ></v-img>
              </v-avatar>
              <div v-else :id="item.data.photo"></div>
            </template>

            <template v-slot:[`item.name`]="{ item }">
              <div
                class="primary--text"
                style="cursor:pointer;"
                v-on:click="selectItemTableData(item.ID)"
              >
                {{ item.name }}
              </div>
            </template>

            <template v-slot:[`item.in_struct`]="{ item }">
              <v-tooltip
                bottom color="primary"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    color="primary"
                    icon
                    v-bind="attrs"
                    v-on="on"
                    @click="showInTreeView(item.ID)"
                  >
                    <v-icon>mdi-file-tree</v-icon>
                  </v-btn>
                </template>
                <span>Показать в структуре</span>
              </v-tooltip>
            </template>

          </v-data-table>
          
          <div class="text-center pt-2">
            <v-pagination
              v-model="page"
              :length="pageCount"
              :total-visible="7"
            ></v-pagination>
          </div>
        </v-col>

        <v-divider vertical></v-divider>

        <v-col
          class="d-flex text-center"
        >
          <v-scroll-y-transition mode="out-in">
            <div
              v-if="!card_visible"
              class="mx-auto"
              style="width:100%;"
              flat
            >
              <v-card-text style=" position: sticky; top:100px;">
                <div class="text-h6 grey--text text--lighten-1 font-weight-light">
                  Выберите контакт
                </div>
              </v-card-text>   
            </div>
            <v-card
              v-else
              :key="card_user_id"
              class="mx-auto"
              style="width:100%;"
              flat
            >
              <div style=" position: sticky; top:100px;">
                <!-- <v-toolbar
                  dense
                  elevation="0"
                >
                  <v-spacer></v-spacer> -->

                  <!-- <v-tooltip bottom>
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        color="primary"
                        icon
                        v-bind="attrs"
                        v-on="on"
                        @click="add_fav_contact=!add_fav_contact"
                      >
                        <v-icon>{{ add_fav_contact ? 'mdi-star' : 'mdi-star-outline' }}</v-icon>
                      </v-btn>
                    </template>
                    <span>{{ add_fav_contact ? 'Удалить из избранного' : 'Добавить в избранное' }}</span>
                  </v-tooltip> -->

                  <!-- <v-tooltip
                    bottom color="primary"
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        :disabled="!mode_search"
                        color="primary"
                        icon
                        v-bind="attrs"
                        v-on="on"
                        @click="showInTreeView(card_user_id)"
                      >
                        <v-icon>mdi-file-tree</v-icon>
                      </v-btn>
                    </template>
                    <span>Показать в структуре</span>
                  </v-tooltip>

                </v-toolbar> -->
                <!-- <v-card-text style=" position: sticky; top:100px;"> -->
                <v-card-text>

                  <v-img
                    class="ma-auto mb-6"
                    v-if="card_user_photo.length>0 && $store.getters.getUserAccess.appeals_in" 
                    :src="'data:image/jpeg;base64,'+card_user_photo"
                    width="180"
                  ></v-img>


                  <!-- <v-avatar
                    v-if="card_user_photo.length>0"
                    size="100" 
                    class="mb-6"
                  >     
                    <v-img 
                      @click="openCardPhoto(card_user_photo)" 
                      style="cursor:pointer;"
                      :src="'data:image/jpeg;base64,'+card_user_photo"
                    ></v-img>
                  </v-avatar> -->

                  <v-avatar
                    v-else
                    size="100" 
                  >  
                    <v-icon
                      x-large
                    >
                      mdi-account-box
                    </v-icon>
                  </v-avatar>

                  <h3 class="text-h5 mb-2">
                    {{ card_user_name }}
                  </h3>
                  <!-- <div class="primary--text subheading mb-5"> -->
                  <div class="subheading mb-5">
                    {{ card_user_profession }}
                  </div>

                  <v-divider></v-divider>

                  <div class="my-5">
                    <v-row
                      v-if="card_user_tabn.length>0 && $store.getters.getUserAccess.appeals_in"
                      class="text-left"
                      tag="v-card-text"
                    >
                      <v-col
                        class="text-right pa-2"
                        tag="strong"
                        cols="5"
                      >
                        Таб. №:
                      </v-col>
                      <v-col class="pa-2" cols="7">
                        {{ card_user_tabn }}
                      </v-col>
                    </v-row>

                    <v-row
                      v-if="card_user_division.length>0"
                      class="text-left"
                      tag="v-card-text"
                    >
                      <v-col
                        class="text-right pa-2"
                        tag="strong"
                        cols="5"
                      >
                        Подразделение:
                      </v-col>
                      <v-col class="pa-2" cols="7">
                        {{ card_user_division }}
                      </v-col>
                    </v-row>

                    <v-row
                      v-if="card_user_workplace.length>0"
                      class="text-left"
                      tag="v-card-text"
                    >
                      <v-col
                        class="text-right pa-2"
                        tag="strong"
                        cols="5"
                      >
                        Рабочее место:
                      </v-col>
                      <v-col class="pa-2" cols="7">
                        {{ card_user_workplace }}
                      </v-col>
                    </v-row>

                    <v-row
                      v-if="card_user_email.length>0"
                      class="text-left"
                      tag="v-card-text"
                    >
                      <v-col
                        class="text-right pa-2"
                        tag="strong"
                        cols="5"
                      >
                        E-mail:
                      </v-col>
                      <v-col class="pa-2" cols="7">
                        <div><a
                          :href="`mailto:${card_user_email.split(';')[0].trim()}`"
                          target="_blank"
                        >{{ card_user_email.split(';')[0].trim() }}</a></div>
                        <div v-if="card_user_email.split(';').length>1"><a
                          :href="`mailto:${card_user_email.split(';')[1].trim()}`"
                          target="_blank"
                        >{{ card_user_email.split(';')[1].trim() }}</a></div>
                        <div v-if="card_user_email.split(';').length>2"><a
                          :href="`mailto:${card_user_email.split(';')[2].trim()}`"
                          target="_blank"
                        >{{ card_user_email.split(';')[2].trim() }}</a></div>
                      </v-col>
                    </v-row>

                    <v-row
                      v-if="card_user_phone.length>0"
                      class="text-left"
                      tag="v-card-text"
                    >
                      <v-col
                        class="text-right pa-2"
                        tag="strong"
                        cols="5"
                      >
                      Телефон:
                      </v-col>
                      <v-col class="pa-2" cols="7">
                        {{ card_user_phone }}
                      </v-col>
                    </v-row>
                  </div>

                  <v-divider v-if="card_user_assistants.length>0"></v-divider>

                  <div class="ma-auto" style="max-width:450px;">
                    <v-list three-line>
                      <v-subheader
                        v-if="card_user_assistants.length>0"
                        style="height:24px;"
                      >
                        Помощники руководителя:
                      </v-subheader>
                      <template v-for="(item, index) in card_user_assistants">
                        <v-list-item :key="item.name">
                          <v-list-item-avatar >
                            <v-img
                              v-if="$store.getters.getUserAccess.appeals_in"
                              style="cursor:pointer;"
                              @click="openCardPhoto(item.photo)"
                              :src="'data:image/jpeg;base64,'+item.photo"
                            ></v-img>
                          </v-list-item-avatar>
                          <v-list-item-content>
                            <v-list-item-title>
                              <div
                                class="primary--text"
                                style="cursor:pointer;"
                                v-on:click="showInTreeView(item.ID)"
                              >
                                {{ item.name }}
                              </div>
                            </v-list-item-title>
                            <v-list-item-subtitle v-if="item.phone.length>0">
                              Телефон: {{ item.phone }}
                            </v-list-item-subtitle>
                          </v-list-item-content>
                        </v-list-item>
                        <v-divider :key="index"></v-divider>
                      </template>
                    </v-list>
                  </div>

                </v-card-text>
              </div>
            </v-card>
          </v-scroll-y-transition>
        </v-col>
      </v-row>

      <v-dialog
        v-model="viewPhotoDialog"
        max-width="400px"
      >
        <v-card>
          <v-img 
            :src="'data:image/jpeg;base64,'+viewPhotoData"
          ></v-img>
        </v-card>
      </v-dialog>

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
  </div>
</template> 
  
<script>

  const pause = ms => new Promise(resolve => setTimeout(resolve, ms))

  export default {
    data: () => ({
      selected_search: [],
      no_load_children: false,
      add_fav_contact: false,
      butt_in_struct: false,
      viewPhotoDialog: false,
      viewPhotoData: '',
      active: [],
      avatar: null,
      open: [],
      loaded: [],
      items: [],
      text_search: '',
      mode_search: false,
      text_search_last: '',
      headers: [
          { text: '', sortable: false, value: 'data.photo', },
          { text: 'ФИО', value: 'name', },
          { text: 'Должность', value: 'data.profession', },
          { text: 'Подразделение', value: 'data.division', },
          // { text: 'Таб.', value: 'data.tabn', },
          { text: 'Тел.', value: 'data.phone', },
          { text: '', value: 'in_struct', },
      ],
      items_search: [],
      itemsPerPage: 10,
      page: 1,
      pageCount: 0,
      options: {},
      loading_data: false,

      card_visible: false,
      card_user_id: '',
      card_user_name: '',
      card_user_profession: '',
      card_user_division: '',
      card_user_email: '',
      card_user_phone: '',
      card_user_workplace: '',
      card_user_photo: '',
      card_user_tabn: '',
      card_user_assistants: [],

      overlay: false,

    }),

    computed: {
      selected () {
          if (!this.active.length){
            this.card_visible = false
            this.card_user_id = ''
            this.card_user_name = ''
            this.card_user_profession = ''
            this.card_user_division = ''
            this.card_user_email = ''
            this.card_user_phone = ''
            this.card_user_workplace = ''
            this.card_user_photo = ''
            this.card_user_tabn = ''
            this.card_user_assistants = []
            this.butt_in_struct = false
            this.add_fav_contact = false
            
          }else{
            const id = this.active[0]
            //Поиск выбранного элемента в массиве
            var resJsonObj = this.getNodeJSONById(id)
            // const findItemNested = (arr, itemId, nestingKey) => (
            //   arr.reduce((a, item) => {
            //     if (a) return a;
            //     if (item.id === itemId) return item;
            //     if (item[nestingKey]) return findItemNested(item[nestingKey], itemId, nestingKey)
            //   }, null)
            // );
            // const res = findItemNested(this.items, id, "children");
            // var resJsonObj = JSON.parse(JSON.stringify(res));
            console.log('findItemNested: ',resJsonObj);
            //console.log('findItemNested_name: ',resJsonObj.name);
            this.card_user_photo = resJsonObj.data.photo
            this.card_user_id = resJsonObj.id
            this.card_user_name = resJsonObj.name
            this.card_user_profession = resJsonObj.data.profession
            this.card_user_division = resJsonObj.data.division
            this.card_user_email = resJsonObj.data.email
            this.card_user_phone = resJsonObj.data.phone
            this.card_user_workplace = resJsonObj.data.workplace       
            this.card_user_tabn = resJsonObj.data.tabn
            this.card_user_assistants = resJsonObj.data.assistants
            this.card_visible = true
            this.add_fav_contact = false
            console.log('card_user_assistants: ',this.card_user_assistants);
          }
      },
    },
    watch: {
      selected: 'selectedTreeView',
    },
    methods: {
      loadChilds(){

      },
      getNodeJSONById(id){
        const findItemNested = (arr, itemId, nestingKey) => (
          arr.reduce((a, item) => {
            if (a) return a;
            if (item.id === itemId) return item;
            if (item[nestingKey]) return findItemNested(item[nestingKey], itemId, nestingKey)
          }, null)
        )
        const res = findItemNested(this.items, id, "children")
        var resJsonObj = JSON.parse(JSON.stringify(res))
        return resJsonObj
      },
      updateOpenTree(openArr){
        // console.log('updateOpenTree: ',openArr)
        // var index;
        // for (index = 0; index < openArr.length; ++index) {
        //   console.log('openObj_id: ',this.getNodeJSONById(openArr[index]))
        //   var itemObj = this.getNodeJSONById(openArr[index])
        //   if(itemObj.children.length==0) this.pushChilds(itemObj)
        // }
      },
      showInTreeView(id){
        //alert(this.card_user_id)
        this.overlay = true
        var oXmlHttpRequest = new XMLHttpRequest()
        const strRequest = this.$store.getters.getUrlTracker + '?action=show_in_treeview&id=' + id
        oXmlHttpRequest.open("get",strRequest,true)
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            vm.no_load_children = true
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
            //console.log('resJsonObj: ', resJsonObj)
            vm.items = []
            vm.open = []
            var arr_item = new Array(); 
            arr_item = resJsonObj.payload[0].children
            var index;
            for (index = 0; index < arr_item.length; ++index) {
              const id = arr_item[index].id;
              const name = arr_item[index].name;
              const parrent_id = arr_item[index].parrent_id;
              const type = arr_item[index].type;
              const data = arr_item[index].data;
              const children = arr_item[index].children;
              vm.items.push({ 'id':id, 'name':name, 'parrent_id':parrent_id, 'type':type, 'data': data, 'children': children})
            } 
            //console.log('items: ', vm.items)
            //vm.$refs.tree.updateAll(true)
            var index;
            for (index = 0; index < resJsonObj.nodes.length; ++index) {
              vm.open.push(resJsonObj.nodes[index])
            }
            vm.active = []
            vm.active.push(id)
            //vm.items = resJsonObj  
            //console.log('open: ', vm.open)
            vm.endSearcheMode()
            vm.no_load_children = false
            vm.overlay = false
          }
        }.bind(oXmlHttpRequest, this)  
        oXmlHttpRequest.send(null);
      },
      endSearcheMode(){
        this.mode_search = false
        this.items_search = []
        this.text_search_last = ''
        this.card_visible = false 
        this.text_search =''
      },
      openCardPhoto(data_photo){
        //console.log('data_photo: ',data_photo.length)
        this.viewPhotoData = data_photo
        this.viewPhotoDialog = true
      },
      selectedTreeView () {
        
      },
      async pushChilds(item){
        //console.log('item: ',item)
        if(!this.no_load_children){
          await pause(500)
          var resJsonObj = JSON.parse(this.getItems(item.id));
          console.log('resJsonObj: ',resJsonObj)
          var index;
          for (index = 0; index < resJsonObj.length; ++index) {
            const id = resJsonObj[index].ID;
            const name = resJsonObj[index].name;
            const parrent_id = resJsonObj[index].parrent_id;
            const type = resJsonObj[index].type;
            const data = resJsonObj[index].data;
            item.children.push({ 'id':id, 'name':name, 'parrent_id':parrent_id, 'type':type, 'data': data, 'children':( type=='group' ? [] : null )});
          }
          return true
        }
        console.log('open: ',this.open)
      },
      itemsStart(){
        this.overlay = true
        var responseText = this.getItems()
        //console.log('responseText: ',responseText)
        var resJsonObj = JSON.parse(responseText);
        var index;
        for (index = 0; index < resJsonObj.length; ++index) {
          const id = resJsonObj[index].ID;
          const name = resJsonObj[index].name;
          const parrent_id = resJsonObj[index].parrent_id;
          const type = resJsonObj[index].type;
          const data = resJsonObj[index].data;
          this.items.push({ 'id':id, 'name':name, 'parrent_id':parrent_id, 'type':type, 'data': data, 'children':( type=='group' ? [] : null )});
        }
        this.overlay = false
        //console.log('items: ',this.items)
      },
      getItems(guidNode=''){
        var oXmlHttpRequest = new XMLHttpRequest();
        //console.log('req: ', this.$store.getters.getUrlTracker + '?action=get_address_book&guidNode=' + guidNode);
        const strRequest = this.$store.getters.getUrlTracker + '?action=get_address_book&guidNode=' + guidNode;
        oXmlHttpRequest.open("get",strRequest,false);
        oXmlHttpRequest.send(null);
        return oXmlHttpRequest.responseText;
      },
      async inputTextSearch(){
        if(this.text_search){
          var initText = this.text_search
          await pause(2000)
          if(initText==this.text_search && this.text_search!=this.text_search_last) this.searchContacts()
        }else{
          this.endSearcheMode()
          // this.mode_search = false
          // this.items_search = []
          // this.text_search_last = ''
          // this.card_visible = false  
        } 

        // var initText = this.text_search
        // await pause(2000)
        // if(this.text_search && initText==this.text_search && this.text_search!=this.text_search_last) {
        //   this.searchContacts()
        // }else if(!this.text_search){
        //   this.mode_search = false;
        //   this.items_search = []; 
        //   this.text_search_last = '';   
        // } 
      },
      enterSearch(){
        if(this.text_search!=this.text_search_last)this.searchContacts();
      },
      searchContacts(){ 
        this.text_search_last = this.text_search;   
        this.mode_search = true;
        this.active = [];
        this.loading_data = true;
        var oXmlHttpRequest = new XMLHttpRequest();
        //console.log('req: ', this.$store.getters.getUrlTracker + '?action=search_contacts&text_search=' + this.text_search);
        const strRequest = this.$store.getters.getUrlTracker + '?action=search_contacts&text_search=' + this.text_search;
        oXmlHttpRequest.open("get",strRequest,true);
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
            console.log('resJsonObj: ', resJsonObj)
            // vm.items_search = resJsonObj;
            vm.items_search = []
            var index
            for (index = 0; index < resJsonObj.length; ++index) {
              const ID = resJsonObj[index].ID
              const name = resJsonObj[index].name
              const parrent_id = resJsonObj[index].parrent_id
              const type = resJsonObj[index].type
              const data = resJsonObj[index].data
              vm.items_search.push({ 'ID':ID, 'name':name, 'parrent_id':parrent_id, 'type':type, 'data': data, 'in_struct':'in_struct', 'view_info':'view_info' })
            }


            vm.loading_data = false;
          }
        }.bind(oXmlHttpRequest, this)  
        oXmlHttpRequest.send(null);
      },
      selectItemTableData(id){
        //console.log('selected_search: ',this.selected_search)
        const item_res = this.items_search.find(item => item.ID===id);
        this.selected_search = []
        this.selected_search.push(item_res)

        this.card_visible = true
        this.card_user_id = item_res.ID
        this.card_user_name = item_res.name
        this.card_user_profession = item_res.data.profession
        this.card_user_division = item_res.data.division
        this.card_user_email = item_res.data.email
        this.card_user_phone = item_res.data.phone
        this.card_user_workplace = item_res.data.workplace
        this.card_user_photo = item_res.data.photo
        this.card_user_tabn = item_res.data.tabn
        this.card_user_assistants = item_res.data.assistants
        this.butt_in_struct = true
        this.add_fav_contact = false
      },
    },
    beforeMount(){
      // if(!this.$store.getters.getLogged)this.$router.push('/')
    },
    mounted(){
      this.itemsStart()
    },
  }
</script>

<style lang="css" scoped>
.row-pointer >>> tbody tr :hover {
  cursor: pointer;
}
.v-expansion-panel::before {
  box-shadow: none;
}
</style>
