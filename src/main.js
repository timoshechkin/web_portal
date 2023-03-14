import Vue from 'vue'
import App from './App.vue'
import router from './router/index'
import store from './store'
import vuetify from './plugins/vuetify'
// import VueSocketIO from 'vue-socket.io'
// import VueResource from 'vue-resource'

// import { url } from './utils/config'

// /* import the fontawesome core */
// import { library } from '@fortawesome/fontawesome-svg-core'
// /* import font awesome icon component */
// import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
// /* import specific icons */
// import { faUserSecret } from '@fortawesome/free-solid-svg-icons'
// /* add icons to the library */
// library.add(faUserSecret)
// /* add font awesome icon component */
// Vue.component('font-awesome-icon', FontAwesomeIcon)

//Vue.config.productionTip = false

// Socket config
// Vue.use(new VueSocketIO({
//   debug: true,
//   connection: 'https://lk-test.kmz.ru:3000',
//   vuex: {
//     store, // Attach the store
//     actionPrefix: 'SOCKET_',
//     mutationPrefix: 'SOCKET_'
//   },
// }))

// // Vue resource for http
// Vue.use(VueResource)

Vue.use(router)

new Vue({
  router,
  store,
  vuetify,
  render: h => h(App)
}).$mount('#app')
