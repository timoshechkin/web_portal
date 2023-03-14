<template>
    <div>

      <v-toolbar rounded class="elevation-0">
          <v-toolbar-title><h3>Курсы валют (по данным ЦБ)</h3></v-toolbar-title>
          <v-spacer></v-spacer>
          <v-menu
            v-model="menuDateFilter"
            :close-on-content-click="false"
            :nudge-right="40"
            transition="scale-transition"
            offset-y
            min-width="auto"
          >
            <template v-slot:activator="{ on, attrs }">
              <v-text-field
                prepend-icon="mdi-calendar"
                class="px-2 text-body-2"
                style="max-width:300px; padding-top:2px;"
                type="text"
                v-model="date_filter"
                label="На дату:"
                hide-details
                readonly
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="date_filter"
              locale="ru-ru"
              :first-day-of-week="1"
              @input="menuDateFilter = false; getRates()"
            ></v-date-picker>
          </v-menu>
      </v-toolbar>

      <v-card
        class="mt-3"
        outlined
        elevation="0"
      >
        <v-data-table
          :headers="headers"
          :items="items"
          :loading="loading_data"
          items-per-page="-1"
          loading-text="Загрузка..."
          no-data-text="Данные отсутствуют"
          hide-default-footer
          sort-by="Name"
          class="elevation-0 row-pointer"
          @click:row="selectItem"
        >
        </v-data-table>

      </v-card>

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
  export default {
    data () {
      return {
        snackbar: false,
        snackColor:'blue-grey',
        snackText: '',

        loading_data: false,
        menuDateFilter: false,
        date_filter: new Date().toISOString().substr(0, 10),
        items: [],
        headers: [
          { text: 'Валюта', value: 'Name', },
          { text: 'Цифр. код', value: 'NumCode', },
          { text: 'Букв. код', value: 'CharCode', },
          { text: 'Единиц', value: 'Nominal', },         
          { text: 'Курс', value: 'Value', },
        ],
      }
    },
    methods: {
      selectItem(item){
        navigator.clipboard.writeText(item.Value).then(function(vm) {
          vm.snackText = 'Значение скопировано в буфер обмена!'
          vm.snackColor = "success"
          vm.snackbar = true
        }.bind(navigator, this), function(err) {
          console.error('Ошибка копирования: ', err);
        });
      },
      getRates(){
        this.items = []
        this.loading_data = true
        var oXmlHttpRequest = new XMLHttpRequest()
        const strRequest = this.$store.getters.getUrlTracker + '?action=get_rates&date=' + this.date_filter
        oXmlHttpRequest.open("get",strRequest,true)      
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            var text = oXmlHttpRequest.responseText
            // console.log('text: ',text)
            var parser = new DOMParser()
            var xmlDoc = parser.parseFromString(text,'text/xml')
            // console.log('xmlDoc: ',xmlDoc)
            var arr_tegs_valute = new Array()
            arr_tegs_valute = xmlDoc.getElementsByTagName('Valute')
            // console.log('arr_tegs_valute: ',arr_tegs_valute)
            var index;
            for (index = 0; index < arr_tegs_valute.length; ++index) {
              vm.items.push({ NumCode:arr_tegs_valute[index].childNodes[0].innerHTML, CharCode:arr_tegs_valute[index].childNodes[1].innerHTML, Nominal:arr_tegs_valute[index].childNodes[2].innerHTML, Name:arr_tegs_valute[index].childNodes[3].innerHTML, Value:arr_tegs_valute[index].childNodes[4].innerHTML })
            }
            // xmlDoc.getElementsByTagName("title")[0].childNodes[0].nodeValue;
            vm.loading_data = false
          }
        }.bind(oXmlHttpRequest, this)
        oXmlHttpRequest.send(null)
      },
    },
    beforeMount(){
      // if(!this.$store.getters.getLogged)this.$router.push('/')
    },
    mounted(){
      this.getRates()
    },
  }
</script>

<style lang="css" scoped>
.row-pointer >>> tbody tr :hover {
  cursor: pointer;
}
</style>