<template>
  <!-- <div>
    <v-toolbar rounded class="elevation-0">
      <v-toolbar-title><h3>Главная</h3></v-toolbar-title>
    </v-toolbar>

    <div v-if="services_cards" class="mx-4 mb-4">
        <div class="mt-2 mb-4">
          <h3>Мои услуги</h3>
        </div>

        <v-row>
          <v-col
            v-for="(serviceData) in items_services_cards"
            :key="serviceData.id"
          >
              <v-hover v-slot="{ hover }">
                <v-card
                  class="mx-auto"
                  width="250"
                  :elevation="hover ? 10 : 3"
                  :class="{ 'on-hover': hover }"
                >
                  <v-card-text
                    style="background-color:#0D47A1; color:white; height: 100px;"
                  >
                    {{serviceData.name}}
                  </v-card-text>
                  
                  <v-card-actions>
                    <v-btn 
                      color="blue darken-3" 
                      small
                      text
                      v-on:click="openFormAppeal(serviceData.id)"
                    >
                      Выбрать
                    </v-btn>
                    <v-spacer></v-spacer>

                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="blue darken-3"
                          icon
                          v-bind="attrs"
                          v-on="on"
                          @click="deleteServFromFav(serviceData.id)"
                        >
                          <v-icon>mdi-star-minus</v-icon>
                        </v-btn>
                      </template>
                      <span>Удалить из избранного</span>
                    </v-tooltip>

                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="blue darken-3"
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

                  </v-card-actions>


                </v-card>
              </v-hover>

          </v-col>
        </v-row>
      </div>

      <div v-if="contacts_cards" class="mx-4 mb-4">
        <div class="mt-2 mb-4">
          <h3>Мои контакты</h3>
        </div>

        <v-row>
          <v-col
            v-for="(contactData) in items_contacts_cards"
            :key="contactData.id"
          >
              <v-hover v-slot="{ hover }">
                <v-card
                  class="mx-auto"
                  width="250"
                  :elevation="hover ? 10 : 3"
                  :class="{ 'on-hover': hover }"
                >
                  <v-card-text
                    style="background-color:#0D47A1; color:white; height: 100px;"
                  >
                    {{contactData.name}}
                  </v-card-text>
                  
                  <v-card-actions>
                    <v-btn 
                      color="blue darken-3" 
                      small
                      text
                      v-on:click="showContact(contactData.id)"
                    >
                      Открыть
                    </v-btn>
                    <v-spacer></v-spacer>

                    <v-tooltip bottom>
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          color="blue darken-3"
                          icon
                          v-bind="attrs"
                          v-on="on"
                          @click="deleteContFromFav(contactData.id)"
                        >
                          <v-icon>mdi-star-minus</v-icon>
                        </v-btn>
                      </template>
                      <span>Удалить из избранного</span>
                    </v-tooltip>

                  </v-card-actions>


                </v-card>
              </v-hover>

          </v-col>
        </v-row>
      </div>

  </div> -->


  
  <!-- <div class="d-flex align-center justify-center" style="height:70vh;"> -->
  <div>
    <!-- <div>
      <v-img
        :src="logo[0]"
        max-width="400"
      ></v-img>        
    </div> -->
    
    <v-carousel
      cycle
      height="85vh"
      hide-delimiter-background
      show-arrows-on-hover
      interval="60000"
    >
      <v-carousel-item
        v-for="(item,i) in items"
        :key="i"
      >
        <div class="d-flex align-center justify-center">
          <img style="max-height:85vh;" :src="item.src">
        </div>
      </v-carousel-item>
    </v-carousel>
  </div>
</template>

<script>
  export default {
    data () {
      return {
        items: [],

        add_fav_service: false,
        services_cards: true,
        items_services_cards: [
          { 'id':'1', 'name':'Избранная услуга 1' },
          { 'id':'2', 'name':'Избранная услуга 2' },
          { 'id':'3', 'name':'Избранная услуга 3' },
        ],
        contacts_cards: true,
        items_contacts_cards: [
          { 'id':'1', 'name':'ИВАНОВ ИВАН ИВАНОВИЧ' },
          { 'id':'2', 'name':'ПЕТРОВ ПЕТР ПЕТОРВИЧ' },
          { 'id':'3', 'name':'ВАСИЛЬЕВ ВАСИЛИЙ ВАСИЛЬЕВИЧ' },
        ],

        logo: [{src: require('../assets/img/logo.png')}],
        //url_carousel_data: 'https://lk.kmz.ru/carousel',
        url_carousel_data: this.$store.getters.getUrlCarouselData,
      }
    },
    methods: {
      loadItemsCarousel(){
        var oXmlHttpRequest = new XMLHttpRequest();
        const strRequest = this.url_carousel_data + '/data.json';
        oXmlHttpRequest.open("get",strRequest, false);
        oXmlHttpRequest.send(null);
        if (oXmlHttpRequest.status == 200){
          var data = JSON.parse(oXmlHttpRequest.responseText);
          //console.log(data)  
          var index
          for (index = 0; index < data.length; ++index) {
            this.items.push({ src: this.url_carousel_data + '/' + data[index] })
          }
        }
        // console.log('items: ',this.items)
      },
      openFormAppeal(id){
        alert(id)
        // this.service_id_f = id
        // this.formAppeal = true;
      },
      showInfoService(id){
        alert(id)
        // var description = this.getDescription(id)
        // if(description.length==0) description='Описание отсутствует'
        // this.textInfoService = description
        // this.viewInfoServDialog = true
      },
      showContact(id){
        alert(id)
      },
      deleteServFromFav(id){
        alert(id)
      },
      deleteContFromFav(id){
        alert(id)
      },
    },
    beforeMount(){
      this.loadItemsCarousel()
    },
  }

</script>

<style>

</style>