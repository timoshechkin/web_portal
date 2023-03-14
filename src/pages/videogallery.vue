<template>
    <div>

      <v-toolbar rounded class="elevation-0">
          <v-toolbar-title><h3>Видеогалерея</h3></v-toolbar-title>
          <v-spacer></v-spacer>
      </v-toolbar>

        <v-row class="mt-3">
              <v-col
                v-for="(item,i) in items"
                :key="i" 
              >
                <v-card  
                     
                  outlined
                  elevation="0"
                  class="mx-auto"
                  width="300"
                  :href="$store.getters.getUrlVideogalleryData+'/video/'+item.url"
                  target="_blank"
                >
                  <v-img
                    height="170"
                    :src="$store.getters.getUrlVideogalleryData+'/img/'+item.img"
                  ></v-img>
                  <div class="text-h6 px-4 pt-4">{{ item.title }}</div>
                  <v-card-subtitle>{{ item.date }}</v-card-subtitle>
                  <v-card-text>{{ item.description }}</v-card-text>
                </v-card>
              </v-col>
        </v-row>
      
    </div>
</template>

<script>
  export default {
    data () {
      return {
        items: [],
      }
    },
    methods: {

    },
    beforeMount(){
      var oXmlHttpRequest = new XMLHttpRequest()
      const strRequest = this.$store.getters.getUrlVideogalleryData + '/data.json'
      oXmlHttpRequest.open("get",strRequest,false)
      oXmlHttpRequest.send(null)
      var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
      console.log('resJsonObj: ',resJsonObj)
      this.items = resJsonObj
      // if(!this.$store.getters.getLogged)this.$router.push('/')
    },
  }
</script>

<style>

</style>