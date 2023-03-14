<template>
    <div>

      <v-toolbar rounded class="elevation-0">
          <v-toolbar-title><h3>Прогноз погоды</h3></v-toolbar-title>
          <v-spacer></v-spacer>
      </v-toolbar>

      <v-card
        class="mx-auto mt-3"
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
          class="elevation-0 row-pointer"
        >
          <template v-slot:[`item.icon`]="{ item }"> 
            <v-icon
              color="blue"
              large
            >
              {{ item.icon }}
            </v-icon>         
          </template>
        </v-data-table>

      </v-card>

    </div>
</template>

<script>
  export default {
    data () {
      return {
        loading_data: false,
        items: [],
        headers: [
          { text: 'День', value: 'day', sortable: false, },
          { text: '', value: 'icon', sortable: false, },
          { text: '', value: 'cloud', sortable: false, },
          { text: 'Темп. возд. мин., C', value: 'temp_min', sortable: false, },
          { text: 'Темп. возд. макс., C', value: 'temp_max', sortable: false, },         
          { text: 'Скорость ветра, м/с', value: 'wind_speed', sortable: false, },
          { text: 'Осадки, мм', value: 'precip', sortable: false, }
        ],
        clouds: [
          { value:'Ясно', mdi:'weather-sunny' },
          { value:'Малооблачно', mdi:'weather-partly-cloudy' },
          { value:'Малооблачно, небольшой  снег', mdi:'weather-partly-snowy' },
          { value:'Малооблачно,  снег', mdi:'weather-partly-snowy' },         
          { value:'Малооблачно, небольшой  мокрый снег', mdi:'weather-partly-snowy-rainy' },          
          { value:'Облачно', mdi:'weather-partly-cloudy' },  
          { value:'Облачно, небольшой  снег', mdi:'weather-partly-snowy' },       
          { value:'Облачно,  снег', mdi:'weather-partly-snowy' },
          { value:'Облачно,  мокрый снег', mdi:'weather-partly-snowy-rainy' },
          { value:'Облачно, небольшой  мокрый снег', mdi:'weather-partly-snowy-rainy' },
          { value:'Облачно, небольшой  снег с дождём', mdi:'weather-partly-snowy-rainy' },         
          { value:'Пасмурно', mdi:'weather-cloudy' },
          { value:'Пасмурно,  снег', mdi:'weather-snowy' },
          { value:'Пасмурно, небольшой  снег', mdi:'weather-snowy' },
          { value:'Пасмурно, сильный  снег', mdi:'weather-snowy-heavy' },
          { value:'Пасмурно, небольшой  мокрый снег', mdi:'weather-snowy-rainy' },
          { value:'Пасмурно,  снег с дождём', mdi:'weather-snowy-rainy' }, 
          { value:'Пасмурно,  мокрый снег', mdi:'weather-snowy-rainy' },                 
      ],
      }
    },
    methods: {
      getCloudIcon(value){
        var item = this.clouds.find(item => item.value===value)
        // console.log('mdi: ',item)
        var mdi = 'mdi-weather-cloudy-alert'
        if (item!=undefined) mdi = 'mdi-'+item.mdi
        return mdi
      },
      createElementFromHTML(htmlString){
        var div = document.createElement('div');
        div.innerHTML = htmlString.trim();
        return div;
      },
      getWeather(){
        this.items = []
        this.loading_data = true
        // this.$store.dispatch('setProgressMain', true)
        var oXmlHttpRequest = new XMLHttpRequest()
        const strRequest = this.$store.getters.getUrlTracker + '?action=get_weather'
        oXmlHttpRequest.open("get",strRequest,true)      
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            // var text = oXmlHttpRequest.responseText
            // console.log('text: ',text)
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)           
            // console.log('resJsonObj: ',resJsonObj)
            var contentObj = vm.createElementFromHTML(resJsonObj.html)
            // console.log('contentObj: ',contentObj)

            var arr_days_html = new Array()
            arr_days_html = contentObj.getElementsByClassName('widget-row widget-row-days-date')[0].children
            // console.log('arr_days: ',arr_days_html)

            var arr_clouds_html = new Array()
            arr_clouds_html = contentObj.getElementsByClassName('widget-row widget-row-icon')[0].children
            // console.log('arr_clouds_html: ',arr_clouds_html)

            var arr_temperature_html = new Array()
            arr_temperature_html = contentObj.getElementsByClassName('chart ten-days')[0].children[1].children
            // console.log('arr_temperature_html: ',arr_temperature_html)

            var arr_wind_speed_html = new Array()
            arr_wind_speed_html = contentObj.getElementsByClassName('widget-row widget-row-wind-gust row-with-caption')[0].getElementsByClassName('row-item')
            // console.log('arr_wind_speed_html: ',arr_wind_speed_html)

            var arr_precipitation_bars_html = new Array()
            arr_precipitation_bars_html = contentObj.getElementsByClassName('widget-row widget-row-precipitation-bars row-with-caption')[0].getElementsByClassName('row-item')
            // console.log('arr_precipitation_bars_html: ',arr_precipitation_bars_html)

            var day, date, cloud, temp_min, temp_max, wind_speed, precip, icon
            var index
            for (index = 0; index < 10; ++index) {
              day = arr_days_html[index].children[0].innerHTML
              date = arr_days_html[index].children[1].innerHTML
              cloud = arr_clouds_html[index].children[0].dataset.text
              temp_min = arr_temperature_html[index].children[1].children[0].innerHTML
              temp_max = arr_temperature_html[index].children[0].children[0].innerHTML
              wind_speed = arr_wind_speed_html[index].children[1].innerHTML
              precip = arr_precipitation_bars_html[index].children[0].innerHTML
              icon = vm.getCloudIcon(cloud)
              vm.items.push({ day:day + ' ' + date, icon:icon, cloud:cloud, temp_min:temp_min, temp_max:temp_max, wind_speed:wind_speed, precip:precip })
            }
            // vm.$store.dispatch('setProgressMain', false)
            vm.loading_data = false
            // console.log('items: ',vm.items)
          }
        }.bind(oXmlHttpRequest, this)
        oXmlHttpRequest.send(null)
      },
    },
    beforeMount(){
      // if(!this.$store.getters.getLogged)this.$router.push('/')
    },
    mounted(){
      this.getWeather()
    },
  }
</script>

<style lang="css" scoped>
.row-pointer >>> tbody tr :hover {
  cursor: pointer;
}
</style>