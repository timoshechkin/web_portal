//import { createRouter, createWebHistory } from 'vue-router'
//import HomeView from '../views/HomeView.vue'
import Vue from 'vue'
import VueRouter from 'vue-router'
import store from '@/store/index.js'
// import VueMaterial from 'vue-material'
// import 'vue-material/dist/vue-material.min.css'
// import 'vue-material/dist/theme/default.css' // This line here

Vue.use(VueRouter)
// Vue.use(VueMaterial)

import index from '@/pages/index'
import services from '@/pages/services'
import appeals_out from '@/pages/appeals_out'
import appeals_in from '@/pages/appeals_in'
import contacts from '@/pages/contacts'
import settings from '@/pages/settings'
import news from '@/pages/news'
import messanger from '@/pages/messanger'
import anticorruption from '@/pages/anticorruption'
import newspapers from '@/pages/newspapers'
import videogallery from '@/pages/videogallery'
import rates from '@/pages/rates'
import weather from '@/pages/weather'
import videochat from '@/pages/videochat'
// import Home from '@/views/Home.vue'


const routes = [
  {
    path: '/',
    name: 'index',
    component: index
  },
  {
    path: '/services',
    name: 'services',
    component: services
  },
  {
    path: '/appeals_out',
    name: 'appeals_out',
    component: appeals_out
  },
  {
    path: '/appeals_in',
    name: 'appeals_in',
    component: appeals_in
  },
  {
    path: '/contacts',
    name: 'contacts',
    component: contacts
  },
  {
    path: '/settings',
    name: 'settings',
    component: settings
  },
  {
    path: '/news',
    name: 'news',
    component: news
  },
  {
    path: '/messanger',
    name: 'messanger',
    component: messanger
  },
  {
    path: '/anticorruption',
    name: 'anticorruption',
    component: anticorruption
  },
  {
    path: '/newspapers',
    name: 'newspapers',
    component: newspapers
  },
  {
    path: '/videogallery',
    name: 'videogallery',
    component: videogallery
  },
  {
    path: '/rates',
    name: 'rates',
    component: rates
  },
  {
    path: '/weather',
    name: 'weather',
    component: weather
  },
  {
    path: '/videochat',
    name: 'videochat',
    component: videochat
  },
  // {
  //   path: '/home',
  //   name: 'home',
  //   component: Home,
  //   beforeEnter: (to, from, next) => {
  //     store.state.room && store.state.username ? next('/chat') : next()
  //   }
  // },
  // {
  //   path: '/chat',
  //   name: 'chat',
  //   // route level code-splitting
  //   // this generates a separate chunk (about.[hash].js) for this route
  //   // which is lazy-loaded when the route is visited.
  //   component: () => import(/* webpackChunkName: "about" */ '@/views/Chat.vue'),
  //   beforeEnter: (to, from, next) => {
  //     !store.state.room && !store.state.username ? next('/') : next()
  //   }
  // }

]

// const router = createRouter({
//   history: createWebHistory(process.env.BASE_URL),
//   routes
// })

const router = new VueRouter({
  routes
})

router.beforeEach((to, from, next) => {
  router.getRoutes()
  // console.log('to.name: ', to.name)
  // console.log('to.query: ', to.query)
  if (to.name !== 'index') {
    store.dispatch('setTargetPage', to.name)
    if (Object.entries(to.query).length !== 0) store.dispatch('setTargetQuery', to.query)
  }
  // console.log('target_page in store: ', store.state.target_page)
  // console.log('query: ', to.query)
  var stateLogged = store.state.logged_store;
  // console.log('stateLogged: ', stateLogged)
  if (!stateLogged && to.name !== 'index') next('/')
  else next()
})

export default router
