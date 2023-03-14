
<template>
  <div>
    <v-toolbar rounded class="elevation-0">
      <v-toolbar-title><h3>Каталог услуг</h3></v-toolbar-title>
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

      <v-switch
        v-model="switch_mode_tree"
        color="primary"
        label="Режим структуры"
        dense
      ></v-switch>

      <!-- <v-btn-toggle
        v-model="toggle_one"
        color="primary"
        mandatory
      >
        <v-btn>
          <v-icon>mdi-table-large</v-icon>
          <span class="hidden-sm-and-down">Плитка</span>
        </v-btn>

        <v-btn>
          <v-icon>mdi-file-tree</v-icon>
          <span class="hidden-sm-and-down">Структура</span>
        </v-btn>
      </v-btn-toggle> -->

    </v-toolbar>

    <div
      v-if="!switch_mode_tree && !mode_search"
    >
      
      <v-breadcrumbs
        :items="itemsBreadcrumbs"
        divider="-"
      >
        <v-breadcrumbs-item
          slot="item"
          slot-scope="{ item }"
          v-on:click="clickBreadcrumbs(item.href)"
        >
          <v-chip outlined link color="primary">
            <!-- <span style="cursor: pointer;"> -->
              {{ item.text }}
            <!-- </span> -->
          </v-chip>
        </v-breadcrumbs-item>
      </v-breadcrumbs>
      <div class="mx-4 mb-4">
          <h3>{{ current_group_title }}</h3>
        </div>
      <div v-if="groups_cards" class="mx-4 mb-4">

        <!-- <div class="mb-2">
          <h3>Подразделы</h3>
        </div> -->

        <v-row>
          <v-col
            v-for="(servicesGroupData) in items_groups_cards"
            :key="servicesGroupData.id"
          >
            <!-- <v-sheet class="pa-1 ma-1 bg-transparent"> -->
              <v-hover v-slot="{ hover }">
                <!-- style="background-color:#0D47A1;" -->
                <v-card 
                  style="background-color:#1565C0;"
                  class="mx-auto"
                  width="250"
                  height="100"
                  :elevation="hover ? 10 : 3"
                  :class="{ 'on-hover': hover }"
                  v-on:click="setItemsCards(servicesGroupData.id)"
                >
                  <!-- <v-card-text
                    style="color:white;"
                  > -->
                  <v-card-text
                    style="color:white;"
                  ><v-row>

                    <v-col cols="3">
                      <v-avatar
                        tile
                        color="blue darken-2"
                      >
                        <v-icon x-large dark>
                          {{ servicesGroupData.mdi_icon.length>0 ? 'mdi-'+servicesGroupData.mdi_icon : 'mdi-desktop-classic' }}
                        </v-icon>
                      </v-avatar>
                    </v-col>

                    <v-col cols="9">
                      <div>{{ servicesGroupData.name }}</div>
                    </v-col>

                    </v-row>
                    <!-- {{ servicesGroupData.name }} -->
                  </v-card-text>
                
                </v-card>
              </v-hover>
            <!-- </v-sheet> -->
          </v-col>
        </v-row>
      </div>
      <v-divider v-if="divider"></v-divider>

      <div v-if="services_cards" class="mx-4 mb-4 mt-4">
        <!-- <div class="mt-2 mb-4">
          <h3>Услуги</h3>
        </div> -->

        <v-row>
          <v-col
            v-for="(serviceData) in items_services_cards"
            :key="serviceData.id"
          >
            <!-- <v-sheet class="pa-1 ma-1 bg-transparent"> -->
              <v-hover v-slot="{ hover }">
                <v-card
                  class="mx-auto"
                  width="250"
                  :elevation="hover ? 10 : 3"
                  :class="{ 'on-hover': hover }"
                >

                  <!-- <v-card-text
                    style="background-color: #FFF9C4; color: rgba(0, 0, 0, 0.85);"
                  > -->
                  <v-card-text
                    style="background-color:#1976D2; color:white; height: 100px;"
                  >

                    <v-row>

                      <v-col cols="3">
                        <v-avatar
                          tile
                          color="blue darken-1"
                        >
                          <v-icon x-large dark>
                            {{ serviceData.mdi_icon.length>0 ? 'mdi-'+serviceData.mdi_icon : 'mdi-desktop-classic' }}
                          </v-icon>
                        </v-avatar>
                      </v-col>

                      <v-col cols="9">
                        <div>{{serviceData.name}}</div>
                      </v-col>

                    </v-row>
 
                  </v-card-text>
                  
                  <v-card-actions>
                    <v-btn 
                      color="primary" 
                      small
                      text
                      v-on:click="openFormAppeal(serviceData.id)"
                    >
                      Выбрать
                    </v-btn>
                    <v-spacer></v-spacer>

                    <!-- <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="blue darken-3"
                          icon
                          v-bind="attrs"
                          v-on="on"
                          @click="add_fav_service=!add_fav_service"
                        >
                          <v-icon>{{ add_fav_service ? 'mdi-star' : 'mdi-star-outline' }}</v-icon>
                        </v-btn>
                      </template>
                      <span>{{ add_fav_service ? 'Удалить из избранного' : 'Добавить в избранное' }}</span>
                    </v-tooltip> -->

                    <v-tooltip bottom color="blue darken-2">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="blue darken-2"
                          icon
                          v-bind="attrs"
                          v-on="on"
                          v-on:click="showInfoService(serviceData.id)"
                        >
                          <v-icon>mdi-information</v-icon>
                        </v-btn>
                      </template>
                      <span>Описание услуги</span>
                    </v-tooltip>

                    <!-- <v-btn
                      icon
                      color="blue-grey"
                      @click="servicesList[idx_serv][3] = !servicesList[idx_serv][3]"
                    >
                      <v-icon>{{ servicesList[idx_serv][3] ? 'mdi-chevron-up' : 'mdi-chevron-down' }}</v-icon>
                    </v-btn> -->
                  </v-card-actions>

                  <!-- <v-expand-transition>
                    <div v-show="servicesList[idx_serv][3]">
                      <v-divider></v-divider>
                      <v-card-text>
                        {{serviceData[2]}}
                      </v-card-text>
                    </div>
                  </v-expand-transition> -->

                </v-card>
              </v-hover>
            <!-- </v-sheet> -->

          </v-col>
        </v-row>
      </div>
    
    </div>

    <div
      v-if="switch_mode_tree || mode_search"
    >
      <v-row
        justify="space-between"
        class="mt-3"
      >
        <v-col v-if="!mode_search" cols="7">

          <v-treeview
            :active.sync="active"
            :items="items_three"
            :load-children="pushChilds"
            :open.sync="open"
            dense
            activatable
            open-on-click
            transition
          >
            <template
              slot="prepend"
              slot-scope="{ item }"
            >
              <v-icon v-if="item.group" color="yellow accent-4">
                {{ open.indexOf(item.id)!=-1 ? 'mdi-folder-open' : 'mdi-folder' }}
              </v-icon>
              <v-icon v-if="!item.group" color="primary">
                mdi-card-text-outline
              </v-icon>
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
            class="elevation-0 row-pointer"
            @page-count="pageCount = $event"
            @click:row="selectItemSearch"
          >
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
            <v-card
              v-if="!selected_service"
              class="mx-auto"
              style="width:100%;"
              flat
            >
              <v-card-text style=" position: sticky; top:100px;">
                <div class="text-h6 grey--text text--lighten-1 font-weight-light">
                  Выберите услугу
                </div>
              </v-card-text>
            </v-card>
            <v-card
              v-else
              :key="selected_ser_id"
              class="mx-auto"
              style="width:100%;"
              flat
            >
              <!-- <v-toolbar
                dense
                elevation="0"
              >
                <v-spacer></v-spacer>

                <v-tooltip bottom>
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      color="primary"
                      icon
                      v-bind="attrs"
                      v-on="on"
                      @click="add_fav_service=!add_fav_service"
                    >
                      <v-icon>{{ add_fav_service ? 'mdi-star' : 'mdi-star-outline' }}</v-icon>
                    </v-btn>
                  </template>
                  <span>{{ add_fav_service ? 'Удалить из избранного' : 'Добавить в избранное' }}</span>
                </v-tooltip>

              </v-toolbar> -->

              <v-card-text style=" position: sticky; top:100px;">
                <h3 class="primary--text text-h5 mb-2">
                  {{ selected_ser_name }}
                </h3>
                <div class="mb-2">
                  {{ selected_ser_description }}
                </div>
              
                <v-divider></v-divider>
                <v-btn
                  class="ma-6"
                  color="success"
                  @click="openFormAppeal(selected_ser_id)"
                >
                  Создать обращение
                </v-btn>
              </v-card-text>
            </v-card>
          </v-scroll-y-transition>
        </v-col>
      </v-row>
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

    <v-dialog
      v-model="viewInfoServDialog"
      max-width="800px"
    >
      <v-card>
        <!-- color="blue darken-4" elevation="0" dark-->
        <v-toolbar>
          <v-toolbar-title>Описание услуги</v-toolbar-title>
          <v-spacer></v-spacer>
          <v-btn icon
            @click="viewInfoServDialog = false">
            <v-icon>mdi-close</v-icon>
          </v-btn>
        </v-toolbar>
        <v-card-text class="pt-4">
          <span v-html="textInfoService"></span>
          <!-- {{ textInfoService }} -->
        </v-card-text>
      </v-card>
    </v-dialog>

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
                  disabled
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
                <div
                  v-if="service_need_attach_f"
                  class="text-caption red--text"
                >
                  Необходимо обязательно прикрепить файлы!
                </div>
                <v-card-actions>

                  <v-btn
                    color="primary"
                    @click="creatAppeal"
                    text
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

  </div>
</template> 
  
<script>

  const pause = ms => new Promise(resolve => setTimeout(resolve, ms))

  export default {
    data: () => ({
      add_fav_service: false,
      mode_search: false,
      headers: [
          { text: 'Наименование', value: 'name', },
          { text: 'Раздел', value: 'path', },
      ],
      items_search: [],
      itemsPerPage: 10,
      itemsPerPage: 10,
      page: 1,
      pageCount: 0,
      options: {},
      loading_data: false,

      selected_service: false,
      selected_ser_name: '',
      selected_ser_description: '',
      selected_ser_id: '',

      itemsBreadcrumbs: [],
      current_group_title: '',
      viewInfoServDialog: false,
      textInfoService: '',
      toggle_one: 0,
      text_search: '',
      text_search_last: '',
      selected_search: [],

      switch_mode_tree: false,
      active: [],
      formAppeal: false,
      service_id_f: '',
      service_need_attach_f: '',
      theme_f: '',
      description_f: '',
      files_f: [],
      open: [],
      items: [],
      items_for_select: [],
      items_three: [],
      items_groups_cards: [],
      items_services_cards: [],
      groups_cards: false,
      services_cards: false,
      divider: false,
      snackbar: false,
      snackColor:'blue-grey',
      snackText: '',
      overlay: false,
    }),

    computed: {
      selected () { 
        //console.log('active: ',this.active)     
        if (!this.active.length){
          this.selected_service = false
          this.selected_ser_name = ''
          this.selected_ser_description = ''
          this.selected_ser_id = ''
        }else{
          const id = this.active[0]
          //Поиск выбранного элемента в массиве
          const findItemNested = (arr, itemId, nestingKey) => (
            arr.reduce((a, item) => {
              if (a) return a;
              if (item.id === itemId) return item;
              if (item[nestingKey]) return findItemNested(item[nestingKey], itemId, nestingKey)
            }, null)
          );
          const res = findItemNested(this.items_three, id, "children");

          //Добавляем полное описание
          const description = this.getDescription(id)
          res['description'] = description
          var resJsonObj = JSON.parse(JSON.stringify(res));
          //console.log('resJsonObj: ',resJsonObj)
          this.selected_service = true
          this.selected_ser_name = resJsonObj.name
          this.selected_ser_description = resJsonObj.description
          this.selected_ser_id = resJsonObj.id
        }
        // if (!this.active.length) return undefined
        // console.log('active: ',this.active)
        // const id = this.active[0]

        // //Поиск выбранного элемента в массиве
        // const findItemNested = (arr, itemId, nestingKey) => (
        //   arr.reduce((a, item) => {
        //     if (a) return a;
        //     if (item.id === itemId) return item;
        //     if (item[nestingKey]) return findItemNested(item[nestingKey], itemId, nestingKey)
        //   }, null)
        // );
        // const res = findItemNested(this.items_three, id, "children");

        // //Добавляем полное описание
        // const description = this.getDescription(id)
        // res['description'] = description
        // var resJsonObj = JSON.parse(JSON.stringify(res));

        // this.selected_service = true
        // this.selected_ser_name = resJsonObj.name
        // this.selected_ser_description = resJsonObj.description
        // this.selected_ser_id = resJsonObj.id
      
        // return resJsonObj
      },
    },
    watch: {
      selected: 'selectedTreeView',
    },
    methods: {
      selectedTreeView () { 
      },
      async inputTextSearch(){
        if(this.text_search){
          var initText = this.text_search
          await pause(2000)
          if(initText==this.text_search && this.text_search!=this.text_search_last) this.searchServices()
        }else{
          this.mode_search = false
          this.items_search = []
          this.text_search_last = '' 
          this.selected_service = false
        }
      },
      enterSearch(){
        if(this.text_search!=this.text_search_last)this.searchServices();
      },
      searchServices(){
        this.text_search_last = this.text_search;
        this.active = [];
        this.mode_search = true;
        // this.active = [];
        this.loading_data = true;
        var oXmlHttpRequest = new XMLHttpRequest();
        const strRequest = this.$store.getters.getUrlTracker + '?action=search_services&text_search=' + this.text_search;
        oXmlHttpRequest.open("get",strRequest,true);
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText);
            console.log('resJsonObj: ', resJsonObj);
            vm.items_search = resJsonObj;
            vm.loading_data = false;
          }
        }.bind(oXmlHttpRequest, this)  
        oXmlHttpRequest.send(null);
      },
      selectItemSearch(item_serv){
        this.selected_search = []
        this.selected_search.push(item_serv)
        // alert(item.ID);
        // this.active = [];
        // this.active.push(item.ID);
        //console.log('items: ',this.items)
        //console.log('id: ',item_serv.ID)
        const item_data = this.items.find(item => item.ID===item_serv.ID);
        //console.log('item_data: ',item_data)
        this.selected_ser_id = item_serv.ID
        this.selected_ser_name = item_data.name
        var descr = this.getDescription(item_serv.ID)
        //console.log('descr: ',descr)
        this.selected_ser_description = descr
        this.selected_service = true
      },
      clickBreadcrumbs(id){
        //alert(id);
        this.setItemsCards(id);
      },
      selectGroup(id) {
        alert(id);
      },
      goToServiceForm(id) {
        alert(id);
        // this.$router.push('/formService')
      },
      creatAppeal(){
        // console.log('service_need_attach_f',this.service_need_attach_f)
        // console.log('files_f.length',this.files_f.length)
        if(this.theme_f=='' || this.description_f=='' || this.service_id_f=='' || (this.service_need_attach_f==true && this.files_f.length==0)){
          this.snackText = 'Заполните обязательные поля формы!';
          this.snackColor = 'red accent-2';
          this.snackbar = true;
        }else{         
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
                vm.service_need_attach_f = []
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
        }
      },
      showInfoService(id){
        var description = this.getDescription(id)
        // if(description.length>0){
        //   this.textInfoService = description
        //   this.viewInfoServDialog = true
        // } 
        if(description.length==0) description='Описание отсутствует'
        this.textInfoService = description
        this.viewInfoServDialog = true
      },
      openFormAppeal(id){
        const item_data = this.items.find(item => item.ID===id)
        // console.log('item_data: ',item_data)
        this.service_need_attach_f = item_data.need_attach
        this.service_id_f = id
        this.theme_f = ''
        this.description_f = ''
        this.files_f = []
        this.formAppeal = true
      },
      getDescription(id_service){
        var oXmlHttpRequest = new XMLHttpRequest();
        const strRequest = this.$store.getters.getUrlTracker + '?action=get_description&id=' + id_service + '&link_object=service';
        oXmlHttpRequest.open("get",strRequest, false);
        oXmlHttpRequest.send(null);
        //console.log('responseText: ',oXmlHttpRequest.responseText)
        var resJsonObj = JSON.parse(oXmlHttpRequest.responseText);
        var res = resJsonObj.payload[0].description
        if (oXmlHttpRequest.status == 200) return res; else return '';
      },
      addChild(item) {
        const name = `${item.name} (${item.children.length})`;
        const id = this.nextId++;
        item.children.push({
          id,
          name,
          children:[]
        });
      },
      pushChilds(item){
        //console.log('item.id: ', item.id)
        var childJSON = this.getChilds(item.id)
        //console.log('childJSON: ', childJSON)
        
        childJSON.forEach(function(item_i, i, childJSON) {
          const id = item_i.id;
          const name = item_i.name;
          const group = item_i.group;

          item.children.push({ 'id':id, 'name':name, 'group':group, 'children':( item_i.group ? [] : null )});
        });
      },
      getChilds(root_id){
        var items_list = this.items.filter(item => item.parentID === root_id)
        var arr_res = new Array();
        items_list.forEach(function(item, i, items_list) {

          arr_res.push({ 'id': item.ID, 'parentID':item.parentID, 'name':item.name, 'shortname':item.shortname, 'group':item.group, 'children': [] });
          
        });
        var resJsonObj = JSON.parse(JSON.stringify(arr_res));
        //console.log( "JSON: " + resJsonObj );
        return resJsonObj
      },
      setBreadcrumbs(id){
        // console.log('id: ',id)
        // console.log('itemsBreadcrumbs: ',this.itemsBreadcrumbs)
        const item_data = this.items.find(item => item.ID===id);
        var res_search = false
        var arr_res = new Array();
        var index;
        for (index = 0; index < this.itemsBreadcrumbs.length; ++index) {
          arr_res.push({ 'text': this.itemsBreadcrumbs[index].text, 'disabled':this.itemsBreadcrumbs[index].disabled, 'href':this.itemsBreadcrumbs[index].href });
          if(this.itemsBreadcrumbs[index].href==id) {res_search=true; break;}
        }
        
        if(res_search == false) arr_res.push({ 'text': item_data.name, 'disabled':false, 'href':id });
        this.itemsBreadcrumbs = arr_res;
        // if(arr_res.length>0) this.itemsBreadcrumbs = arr_res;
        // else this.itemsBreadcrumbs.push({ 'text': item_data.name, 'disabled':false, 'href':id });
        this.current_group_title = item_data.name;
      },
      setItemsCards(root_id){
        // alert(root_id)
        this.setBreadcrumbs(root_id); 
        this.items_groups_cards = []
        this.items_services_cards = []
        var items_list = this.items.filter(item => item.parentID === root_id)
        var index;
        for (index = 0; index < items_list.length; ++index) {
          if(items_list[index].group) this.items_groups_cards.push({ 'id': items_list[index].ID, 'name':items_list[index].name, 'mdi_icon':items_list[index].mdi_icon }); 
          if(!items_list[index].group) this.items_services_cards.push({ 'id': items_list[index].ID, 'name':items_list[index].name, 'mdi_icon':items_list[index].mdi_icon });   
        };
        if(this.items_groups_cards.length>0) this.groups_cards=true; else this.groups_cards=false;
        if(this.items_services_cards.length>0) this.services_cards=true; else this.services_cards=false;
        if(this.items_groups_cards.length>0 && this.items_services_cards.length>0) this.divider=true; else this.divider=false;
      },
      getItemsStart(){
        //console.log('strRequest: ', this.$store.getters.getUrlTracker + '?action=get_services&group_id=' + '&search=');
        this.overlay = true
        var oXmlHttpRequest = new XMLHttpRequest();
        const strRequest = this.$store.getters.getUrlTracker + '?action=get_services&group_id=' + '&search=';
        oXmlHttpRequest.open("get",strRequest,true);
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            console.log('responseText: ', oXmlHttpRequest.responseText);
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText);
            //console.log('responseText: ', oXmlHttpRequest.responseText);
            // console.log('resJsonObj: ', resJsonObj);
            vm.items = resJsonObj.payload;
            const main_item_id = vm.items.find(item => item.parentID === '');
            //console.log('find_item_three: ', main_item_id) 
            //onsole.log('find_items_three: ', vm.items.filter(item => item.parentID === main_item_id.ID))
            vm.items_three = vm.getChilds(main_item_id.ID);
            // Заполняем список услу без групп для выбора
            const items_all = vm.items;
            items_all.forEach(function(item, i, items_all) {
              if(!item.group) vm.items_for_select.push({ 'id': item.ID, 'name':item.name })
            });
            //console.log('items_for_select: ', vm.items_for_select)
            // Заполняем список групп для режима плитки
            vm.setItemsCards(main_item_id.ID);
            vm.overlay = false
          }
        }.bind(oXmlHttpRequest, this)
        oXmlHttpRequest.send(null);
      },
    },
    beforeMount(){
      // if(!this.$store.getters.getLogged)this.$router.push('/')
    },
    mounted(){
      this.getItemsStart();
    },
  }
</script>

<style lang="css" scoped>
.row-pointer >>> tbody tr :hover {
  cursor: pointer;
}
</style>