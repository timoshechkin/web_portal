<template>
    <div>

      <v-toolbar rounded class="elevation-0">
          <v-toolbar-title><h3>Корпоративная газета "Машиностроитель"</h3></v-toolbar-title>
          <v-spacer></v-spacer>
      </v-toolbar>

      <!-- <v-card
        class="mt-3"
        outlined
        elevation="0"
      >

      </v-card> -->

      <v-expansion-panels focusable class="mt-3">
        <v-expansion-panel
          v-for="(group,i) in this.items"
          :key="i"
        >
          <v-expansion-panel-header>{{ group.name }}</v-expansion-panel-header>
          <v-expansion-panel-content>
            <v-row class="mt-3">
              <v-col
                v-for="(item,j) in group.items"
                :key="j"
              >

                  <v-card
                    outlined
                    elevation="0"
                    class="mx-auto"
                    width="200"
                    :href="$store.getters.getUrlNewspapersData+'/'+group.id+'/pdf/'+item.url"
                    target="_blank"
                  >
                    <v-img
                      height="283"
                      :src="$store.getters.getUrlNewspapersData+'/'+group.id+'/img/'+item.img"
                    ></v-img>
                    <div class="pa-4 text-subtitle-2">{{ item.name }}</div>
                  </v-card>

              </v-col>
            </v-row>
          </v-expansion-panel-content>
        </v-expansion-panel>
      </v-expansion-panels>

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
      const strRequest = this.$store.getters.getUrlNewspapersData + '/data.json'
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