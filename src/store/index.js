import Vue from 'vue'
import Vuex from 'vuex'
// import { STATUS_OPTIONS } from './utils/config'

Vue.use(Vuex)

const store = new Vuex.Store({
    state: {
        user_name_store: '',
        user_login_store: '',
        user_id_store: '',
        user_photo_store: '',
        user_access_store: {},
        user_prof_store: '',
        user_division_store: '',

        logged_store: false,
        tracker_url_store: '',
        url_tracker: '',
        url_carousel_data: '',
        url_emoji_data: '',
        url_anticorruption_data: '',
        url_newspapers_data: '',
        url_videogallery_data: '',

        emoji_data: [],
        progress_main: false,

        target_page: '',
        target_query: {},

        // room: undefined, // Current room 
        // username: undefined, // Username
        // status: STATUS_OPTIONS.available, // User status
        // rooms: [] // Available rooms in the whole chat

    },
    mutations: {
        setUserName: (state, payload) => {
            state.user_name_store = payload;
        },
        setUserLogin: (state, payload) => {
            state.user_login_store = payload;
        },
        setUserId: (state, payload) => {
            state.user_id_store = payload;
        },
        setUserPhoto: (state, payload) => {
            state.user_photo_store = payload;
        },
        setLogged: (state, payload) => {
            state.logged_store = payload;
        },
        setUrlStore: (state, payload) => {
            state.tracker_url_store = payload;
        },
        setUrlTracker: (state, payload) => {
            state.url_tracker = payload;
        },
        setUrlCarouselData: (state, payload) => {
            state.url_carousel_data = payload;
        },
        setUrlEmojiData: (state, payload) => {
            state.url_emoji_data = payload;
        },
        setUrlAnticorruptionData: (state, payload) => {
            state.url_anticorruption_data = payload;
        },
        setUrlVideogalleryData: (state, payload) => {
            state.url_videogallery_data = payload;
        },
        setUrlNewspapersData: (state, payload) => {
            state.url_newspapers_data = payload;
        },
        setEmojiData: (state, payload) => {
            state.emoji_data = payload;
        },
        setUserAccess: (state, payload) => {
            state.user_access_store = payload;
        },
        setProgressMain: (state, payload) => {
            state.progress_main = payload;
        },
        setUserProf: (state, payload) => {
            state.user_prof_store = payload;
        },
        setUserDivision: (state, payload) => {
            state.user_division_store = payload;
        },
        setTargetPage: (state, payload) => {
            state.target_page = payload;
        },
        setTargetQuery: (state, payload) => {
            state.target_query = payload;
        },
        // joinRoom(state, { room, username }) {
        //     state.room = room
        //     state.username = username
        // },
        // changeRoom(state, room) {
        //     state.room = room
        // },
        // setRooms(state, rooms) {
        //     state.rooms = rooms
        // },
        // leaveChat(state) {
        //     state.room = undefined
        //     state.username = undefined
        // },
        // changeStatus(state) {
        //     let nextStatus
        //     if (state.status === STATUS_OPTIONS.available) nextStatus = STATUS_OPTIONS.absent
        //     if (state.status === STATUS_OPTIONS.absent) nextStatus = STATUS_OPTIONS.unavailable
        //     if (state.status === STATUS_OPTIONS.unavailable) nextStatus = STATUS_OPTIONS.available

        //     state.status = nextStatus
        // }
    },
    actions: {
        setUserName: (context, payload) => {
            context.commit('setUserName', payload);
        },
        setUserLogin: (context, payload) => {
            context.commit('setUserLogin', payload);
        },
        setUserId: (context, payload) => {
            context.commit('setUserId', payload);
        },
        setUserPhoto: (context, payload) => {
            context.commit('setUserPhoto', payload);
        },
        setLogged: (context, payload) => {
            context.commit('setLogged', payload);
        },
        setUrlStore: (context, payload) => {
            context.commit('setUrlStore', payload);
        },
        setUrlTracker: (context, payload) => {
            context.commit('setUrlTracker', payload);
        },
        setUrlCarouselData: (context, payload) => {
            context.commit('setUrlCarouselData', payload);
        },
        setUrlEmojiData: (context, payload) => {
            context.commit('setUrlEmojiData', payload);
        },
        setUrlAnticorruptionData: (context, payload) => {
            context.commit('setUrlAnticorruptionData', payload);
        },
        setUrlNewspapersData: (context, payload) => {
            context.commit('setUrlNewspapersData', payload);
        },
        setUrlVideogalleryData: (context, payload) => {
            context.commit('setUrlVideogalleryData', payload);
        },
        setEmojiData: (context, payload) => {
            context.commit('setEmojiData', payload);
        },
        setUserAccess: (context, payload) => {
            context.commit('setUserAccess', payload);
        },
        setProgressMain: (context, payload) => {
            context.commit('setProgressMain', payload);
        },
        setUserProf: (context, payload) => {
            context.commit('setUserProf', payload);
        },
        setUserDivision: (context, payload) => {
            context.commit('setUserDivision', payload);
        },
        setTargetPage: (context, payload) => {
            context.commit('setTargetPage', payload);
        },
        setTargetQuery: (context, payload) => {
            context.commit('setTargetQuery', payload);
        },
        // joinRoom({ commit }, information) {
        //     commit('joinRoom', information)
        // },
        // changeRoom({ commit }, room) {
        //     commit('changeRoom', room)
        // },
        // setRooms({ commit }, rooms) {
        //     commit('setRooms', rooms)
        // },
        // leaveChat({ commit }) {
        //     commit('leaveChat')
        // },
        // changeStatus({ commit }) {
        //     commit('changeStatus')
        // }
    },
    modules: {},
    getters: {
        getUserName: state => {
            return state.user_name_store
        },
        getUserLogin: state => {
            return state.user_login_store
        },
        getUserId: state => {
            return state.user_id_store
        },
        getUserPhoto: state => {
            return state.user_photo_store
        },
        getLogged: state => {
            return state.logged_store
        },
        getUrlStore: state => {
            return state.tracker_url_store
        },
        getUrlTracker: state => {
            return state.url_tracker
        },
        getUrlCarouselData: state => {
            return state.url_carousel_data
        },
        getUrlEmojiData: state => {
            return state.url_emoji_data
        },
        getUrlAnticorruptionData: state => {
            return state.url_anticorruption_data
        },
        getUrlNewspapersData: state => {
            return state.url_newspapers_data
        },
        getUrlVideogalleryData: state => {
            return state.url_videogallery_data
        },
        getEmojiData: state => {
            return state.emoji_data
        },
        getUserAccess: state => {
            return state.user_access_store
        },
        getProgressMain: state => {
            return state.progress_main
        },
        getUserProf: state => {
            return state.user_prof_store
        },
        getUserDivision: state => {
            return state.user_division_store
        },
        getTargetPage: state => {
            return state.target_page
        },
        getTargetQuery: state => {
            return state.target_query
        },
    },
})

export default store