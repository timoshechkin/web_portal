<template>
    <div>
      <v-toolbar rounded class="elevation-0">
          <v-toolbar-title><h3>Новости</h3></v-toolbar-title>
          <v-spacer></v-spacer>
          <v-menu
            v-model="menuPeriodFilter"
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
                v-model="period_post_filter"
                label="За период:"
                hide-details
                readonly
                v-bind="attrs"
                v-on="on"
              ></v-text-field>
            </template>
            <v-date-picker
              v-model="period_post_filter"
              type="month"
              locale="ru-ru"
              :first-day-of-week="1"
              @input="menuPeriodFilter = false; getPostPeriod()"
            ></v-date-picker>
          </v-menu>

          <!-- <v-btn
            color="success"
            @click="openFormAddPost()"
          >
            Добавить новость
          </v-btn> -->
      </v-toolbar>

      <!-- Блок добавления новой новости -->
      <div>

        <!-- Блок кнопки добавления новости -->
        <v-card
          rounded="lg"
          elevation="0"
          outlined
          class="mx-auto mt-6"
          max-width="960"
          v-if="button_add && $store.getters.getUserAccess.news_edit"
        >
          <v-row
            align="center"
            justify="center"
            class="py-5"
          >
            <v-icon
              @click="openFormAddPost()"
            >
              mdi-plus-circle-outline
            </v-icon>
          </v-row>
        </v-card>

        <!-- Блок добавления новости -->
        <v-card
          rounded="lg"
          elevation="0"
          outlined
          class="mx-auto mt-6"
          max-width="960"
          v-if="formPost"
        >
          <v-toolbar
            elevation="0"
          >
            <v-toolbar-title>Добавление новости</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon
              @click="closeFormAddPost()">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-toolbar>
          <v-divider></v-divider>
          <div
            class="pt-0"
          >
            <v-card-text>

              <!-- <v-text-field
                type="text"
                label="Заголовок" 
                v-model="title_post"
                dense
                class="text-h6"
              ></v-text-field> -->

              <div
                class="px-2 py-1 mb-4"
                style="position:relative; width:100%; min-height:40px; border-radius:4px; border: 1px solid rgb(192, 192, 192);"
              >
                <!-- Поле заголовка -->
                <div
                  id="ta_title_post"
                  contenteditable="true"
                  aria-multiline="true"
                  class="pr-8 text-h6 text_post"
                  v-on:keyup="titleChanged()"
                  style="width:100%; display:inline-block"
                  @paste="onPasteText"
                >
                </div>
                <!-- Подсказка поля заголовка -->
                <div
                  id="label_title_post"
                  style="position:absolute; top:4px; left:8px; display:block;"
                  class="text-h6 font-weight-light placeholder"
                >
                  Заголовок
                </div>
                <!-- Кнопка выбора эмоджи поля заголовка -->
                <div
                  style="position:absolute; top:1px; right:1px;"
                >
                  <v-menu
                    open-on-hover
                    :close-on-content-click="false"
                    top
                    offset-y
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        text
                        icon
                        v-bind="attrs"
                        v-on="on"
                      >
                        <v-icon>mdi-emoticon-happy-outline</v-icon>
                      </v-btn>
                    </template>
                    <v-card>
                      <div style="overflow-y:auto; max-height:550px;">
                        <div v-for="group in emojiTable()" :key="group.id">
                            <v-card-subtitle>
                              {{ group.name }}
                            </v-card-subtitle>
                            <v-card-text>
                              <table style="border: none; border-spacing: 2px;">
                                <tbody>
                                  <tr v-for="row in group.rows" :key="row">
                                    <td v-for="col in row" :key="col.html" >
                                      <img @mousedown="insertEmoji('ta_title_post',col.html); titleChanged();" class='emoji' style="cursor:pointer;" :src='`${$store.getters.getUrlEmojiData}/${col.img}`' :alt='col.html'>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </v-card-text>
                        </div>  
                      </div>
                    </v-card>
                  </v-menu>
                </div>
              </div>
              
              <!-- Поле текста -->
              <div
                class="pa-2"
                style="position:relative; width:100%; min-height:40px; border-radius:4px; border: 1px solid rgb(192, 192, 192);"
              >
              <div
                  style="position:absolute; top:1px; left:1px;"
                >
                  
                  <v-btn elevation="0" small @click="formatHTML('ta_text_post','bold')">
                    <v-icon>mdi-format-bold</v-icon>
                  </v-btn>
                  <v-btn elevation="0" small @click="formatHTML('ta_text_post','italic')">
                    <v-icon>mdi-format-italic</v-icon>
                  </v-btn>
                  <v-btn elevation="0" small @click="formatHTML('ta_text_post','paragraph')">
                    <v-icon>mdi-format-paragraph</v-icon>
                  </v-btn>
                  <v-btn elevation="0" small @click="formatHTML('ta_text_post','br')">
                    <v-icon>mdi-format-text-wrapping-wrap</v-icon>
                  </v-btn>
                  <v-btn elevation="0" small @click="formatHTML('ta_text_post','removeFormat')">
                    <v-icon>mdi-broom</v-icon>
                  </v-btn>   
                  <v-btn elevation="0" small @click="formatHTML('ta_text_post','undo')">
                    <v-icon>mdi-undo</v-icon>
                  </v-btn> 
                  <v-btn elevation="0" small @click="formatHTML('ta_text_post','redo')">
                    <v-icon>mdi-redo</v-icon>
                  </v-btn>  
                  <v-btn elevation="0" small @click="formatHTML('ta_text_post','delete')">
                    <v-icon>mdi-close</v-icon>
                  </v-btn> 
                  <v-btn elevation="0" small @click="openDialogEditHTML('ta_text_post')">
                    <v-icon>mdi-code-tags</v-icon>
                  </v-btn>

                </div>
                <div
                  id="ta_text_post"
                  contenteditable="true"
                  aria-multiline="true"
                  class="pr-8 pt-8 text-body-2 text_post"
                  v-on:keyup="postChanged()"
                  style="width:100%; display:inline-block"
                  @paste="onPasteText"
                >
                </div>
                <!-- Подсказка поля текста -->
                <div
                  id="label_text_post"
                  style="position:absolute; top:8px; left:8px; display:block;"
                  class="text-body-2 pt-8 font-weight-light placeholder"
                >
                  Текст новости
                </div> 
                <!-- Кнопка выбора эмоджи поля текста -->
                <div
                  style="position:absolute; top:1px; right:1px;"
                >
                  <v-menu
                    open-on-hover
                    :close-on-content-click="false"
                    top
                    offset-y
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        text
                        icon
                        v-bind="attrs"
                        v-on="on"
                      >
                        <v-icon>mdi-emoticon-happy-outline</v-icon>
                      </v-btn>
                    </template>
                    <v-card>
                      <div style="overflow-y:auto; max-height:550px;">
                        <div v-for="group in emojiTable()" :key="group.id">
                            <v-card-subtitle>
                              {{ group.name }}
                            </v-card-subtitle>
                            <v-card-text>
                              <table style="border: none; border-spacing: 2px;">
                                <tbody>
                                  <tr v-for="row in group.rows" :key="row">
                                    <td v-for="col in row" :key="col.html" >
                                      <img @mousedown="insertEmoji('ta_text_post',col.html); postChanged();" class='emoji' style="cursor:pointer;" :src='`${$store.getters.getUrlEmojiData}/${col.img}`' :alt='col.html'>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </v-card-text>
                        </div>  
                      </div>
                    </v-card>
                  </v-menu>
                </div>
              </div>

              <!-- <v-textarea
                ref="ta_text_post"
                rows="1"
                label="Текст"
                v-model="text_post"
                auto-grow
                class="v-text-small"
              >
                <template v-slot:append>
                  <div>
                    <v-menu
                      open-on-hover
                      top
                      offset-y
                    >
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn
                          text
                          icon
                          v-bind="attrs"
                          v-on="on"
                        >
                          <v-icon>mdi-emoticon-happy-outline</v-icon>
                        </v-btn>
                      </template>
                      <v-card>
                        <div style="overflow-y:auto; max-height:550px;">
                          <div v-for="group in emojiTable()" :key="group.id">
                              <v-card-subtitle>
                                {{ group.name }}
                              </v-card-subtitle>
                              <v-card-text>
                                <table style="border: none; border-spacing: 2px;">
                                  <tbody>
                                    <tr v-for="row in group.rows" :key="row">
                                      <td v-for="col in row" :key="col.html">
                                        <img @mousedown="insertEmoji('ta_text_post',col.html)" class='emoji' style="cursor:pointer;" :src='`${$store.getters.getUrlEmojiData}/${col.img}`' :alt='col.html'>
                                      </td>
                                    </tr>
                                  </tbody>
                                </table>
                              </v-card-text>
                          </div>  
                        </div>
                      </v-card>
                    </v-menu>
                  </div>
                </template>
              </v-textarea> -->

            </v-card-text>
            <!-- Блок прикрепленных картинок -->
            <v-row class="mx-3">
              <div
                v-for="(image,index) in images_post"
                :key="image.file_obj.name"
                :index=index
              >
                <v-col>
                  <div class="mx-auto" style="position:relative; max-width:285px;">
                    
                    <!-- {{ image.file_obj.name }} -->
                    <v-img
                      :src="image.data"
                      @click="viewImageInPostAdd(image.data)"
                      style="cursor:pointer;"
                    ></v-img>
                    <div style="background: rgba(255,255,255,0.5); position:absolute; top:0px; right:0px;">
                      <v-btn
                        color="primary"
                        x-small
                        icon
                        @click="delImageInPostAdd(index)"
                      >
                        <v-icon dark>mdi-close-box</v-icon>
                      </v-btn>
                    </div>
                  </div>
                </v-col>
              </div>
            </v-row>
            <!-- Блок прикрепленных файлов -->
            <v-list
              two-line
              dense
            >
              <v-list-item
                v-for="(file,index) in files_post"
                :key="file.name"
                style="min-height:60px;"
                :index=index
              >
                <v-list-item-avatar class="my-0">
                  <v-icon
                    large
                    color="blue"
                  >mdi-text-box-outline</v-icon>
                </v-list-item-avatar>

                <v-list-item-content>
                  <v-list-item-title v-text="file.name" style="color:#2196f3; cursor:pointer;" @click="openFileAddPost(index)"></v-list-item-title>

                  <v-list-item-subtitle v-text="getFileSizeString(file.size)"></v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-action>
                  <v-btn icon @click="delFileInPostAdd(index)">
                    <v-icon>mdi-trash-can-outline</v-icon>
                  </v-btn>
                </v-list-item-action>
              </v-list-item>
            </v-list>

            <v-divider></v-divider>

            <v-card-actions class="pa-4">
              <v-icon class="mx-2"
                @click="addImageInPost()"
              >
                mdi-camera-outline
              </v-icon>

              <v-icon class="mx-2"
                @click="addFileInPost()"
              >
                mdi-file-outline
              </v-icon>
              <v-divider
                class="mx-4"
                vertical
              ></v-divider>

              <div class="d-flex flex-row">

                <v-menu
                  v-model="menuDate"
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
                      style="max-width:120px; padding-top:2px;"
                      type="text"
                      v-model="date_post"
                      hide-details
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="date_post"
                    locale="ru-ru"
                    :first-day-of-week="1"
                    @input="menuDate = false"
                  ></v-date-picker>
                </v-menu>

                <v-menu
                  ref="menu"
                  v-model="menuTime"
                  :close-on-content-click="false"
                  :nudge-right="40"
                  :return-value.sync="time_post"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="290px"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      prepend-icon="mdi-clock-time-four-outline"
                      class="px-2 text-body-2"
                      style="max-width:85px; padding-top:2px;"
                      v-model="time_post"
                      hide-details
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>

                  </template>
                  <v-time-picker
                    v-if="menuTime"
                    v-model="time_post"
                    full-width
                    @click:minute="$refs.menu.save(time_post)"
                  ></v-time-picker>
                </v-menu>
                  
                <v-checkbox
                  v-model="visible_post"
                  hide-details
                  label="Видимость"
                  class="px-2"
                  style="margin-top:6px;"
                ></v-checkbox>
              </div>

              <v-spacer></v-spacer>
              
              <v-btn
                small
                color="primary"
                elevation="0"
                @click="addPost()"
              >
                Опубликовать
              </v-btn>
              <v-btn
                small
                color="error"
                elevation="0"
                @click="closeFormAddPost()"
              >
                Отменить
              </v-btn>

            </v-card-actions>

          </div>

        </v-card>
      </div>

      <!-- Список новостей -->
      <div
        v-for="(post,index) in posts"
        :key="post.id"
        :index="index"
      >
        <v-card
          rounded="lg"
          elevation="0"
          outlined
          class="mx-auto mt-6"
          max-width="960"
          style="position:relative;"
        >

          <!-- Блок для редактирования новости -->
          <div
            :id="'edit_post_' + post.id"
            style="display:none;"
          >
            <!-- Поле заголовка -->
            <div class="pa-4">
              <div
                class="px-2 py-1"
                style="position:relative; width:100%; border-radius:4px; border: 1px solid rgb(192, 192, 192);"
              >  
                <div
                  :id="'edit_post_title_' + post.id"
                  contenteditable="true"
                  aria-multiline="true"
                  class="pr-8 text-h6 text_post"
                  style="width:100%; display:inline-block"
                  @paste="onPasteText"
                >
                </div>
                <!-- Кнопка выбора эмоджи поля заголовка -->
                <div
                  style="position:absolute; top:1px; right:1px;"
                >
                  <v-menu
                    open-on-hover
                    :close-on-content-click="false"
                    top
                    offset-y
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        text
                        icon
                        v-bind="attrs"
                        v-on="on"
                      >
                        <v-icon>mdi-emoticon-happy-outline</v-icon>
                      </v-btn>
                    </template>
                    <v-card>
                      <div style="overflow-y:auto; max-height:550px;">
                        <div v-for="group in emojiTable()" :key="group.id">
                            <v-card-subtitle>
                              {{ group.name }}
                            </v-card-subtitle>
                            <v-card-text>
                              <table style="border: none; border-spacing: 2px;">
                                <tbody>
                                  <tr v-for="row in group.rows" :key="row">
                                    <td v-for="col in row" :key="col.html" >
                                      <img @mousedown="insertEmoji('edit_post_title_'+post.id, col.html);" class='emoji' style="cursor:pointer;" :src='`${$store.getters.getUrlEmojiData}/${col.img}`' :alt='col.html'>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </v-card-text>
                        </div>  
                      </div>
                    </v-card>
                  </v-menu>
                </div>

              </div>
            </div>
            <!-- Поле текста -->
            <div class="px-4 pb-4">
              <div
                class="pa-2"
                style="position:relative; width:100%; min-height:40px; border-radius:4px; border: 1px solid rgb(192, 192, 192);"
              >
                <div
                  style="position:absolute; top:1px; left:1px;"
                >
                  
                  <v-btn elevation="0" small @click="formatHTML('edit_post_text_' + post.id,'bold')">
                    <v-icon>mdi-format-bold</v-icon>
                  </v-btn>
                  <v-btn elevation="0" small @click="formatHTML('edit_post_text_' + post.id,'italic')">
                    <v-icon>mdi-format-italic</v-icon>
                  </v-btn>
                  <v-btn elevation="0" small @click="formatHTML('edit_post_text_' + post.id,'paragraph')">
                    <v-icon>mdi-format-paragraph</v-icon>
                  </v-btn>
                  <v-btn elevation="0" small @click="formatHTML('edit_post_text_' + post.id,'br')">
                    <v-icon>mdi-format-text-wrapping-wrap</v-icon>
                  </v-btn>
                  <v-btn elevation="0" small @click="formatHTML('edit_post_text_' + post.id,'removeFormat')">
                    <v-icon>mdi-broom</v-icon>
                  </v-btn>  
                  <v-btn elevation="0" small @click="formatHTML('edit_post_text_' + post.id,'undo')">
                    <v-icon>mdi-undo</v-icon>
                  </v-btn> 
                  <v-btn elevation="0" small @click="formatHTML('edit_post_text_' + post.id,'redo')">
                    <v-icon>mdi-redo</v-icon> 
                  </v-btn> 
                  <v-btn elevation="0" small @click="formatHTML('edit_post_text_' + post.id,'delete')">
                    <v-icon>mdi-close</v-icon>
                  </v-btn> 
                  <v-btn elevation="0" small @click="openDialogEditHTML('edit_post_text_' + post.id)">
                    <v-icon>mdi-code-tags</v-icon>
                  </v-btn>

                </div>
                <div
                  :id="'edit_post_text_' + post.id"
                  contenteditable="true"
                  aria-multiline="true"
                  class="pr-8 pt-8 text-body-2 text_post"
                  style="width:100%; display:inline-block"
                  @paste="onPasteText"
                >
                </div>
                <!-- Кнопка выбора эмоджи поля заголовка -->
                <div
                  style="position:absolute; top:1px; right:1px;"
                >
                  <v-menu
                    open-on-hover
                    :close-on-content-click="false"
                    top
                    offset-y
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        text
                        icon
                        v-bind="attrs"
                        v-on="on"
                      >
                        <v-icon>mdi-emoticon-happy-outline</v-icon>
                      </v-btn>
                    </template>
                    <v-card>
                      <div style="overflow-y:auto; max-height:550px;">
                        <div v-for="group in emojiTable()" :key="group.id">
                            <v-card-subtitle>
                              {{ group.name }}
                            </v-card-subtitle>
                            <v-card-text>
                              <table style="border: none; border-spacing: 2px;">
                                <tbody>
                                  <tr v-for="row in group.rows" :key="row">
                                    <td v-for="col in row" :key="col.html" >
                                      <img @mousedown="insertEmoji('edit_post_text_'+post.id, col.html);" class='emoji' style="cursor:pointer;" :src='`${$store.getters.getUrlEmojiData}/${col.img}`' :alt='col.html'>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </v-card-text>
                        </div>  
                      </div>
                    </v-card>
                  </v-menu>
                </div>
              </div>
            </div>
            <!-- Блок прикрепленных картинок -->
            <v-row class="mx-3">
              <div
                v-for="(image,index_file) in post.images_edit"
                :key="image.name"
                :index=index_file
              >
                <v-col      
                  v-if="image.state!='delete'"
                >
                  <div  
                    class="mx-auto"
                    style="position:relative; max-width:285px;"
                  >
                    
                    <v-img
                      :src="image.data"
                      @click="viewImageInPostAdd(image.data)"
                      style="cursor:pointer;"
                    ></v-img>
                    <div style="background: rgba(255,255,255,0.5); position:absolute; top:0px; right:0px;">
                      <v-btn
                        x-small
                        color="primary"
                        icon
                        @click="delImageInPostEdit(index,index_file)"
                      >
                        <v-icon dark>mdi-close-box</v-icon>
                      </v-btn>
                    </div>
                  </div>
                </v-col>
              </div>
            </v-row>
            <!-- Блок прикрепленных файлов -->
            <v-list
              two-line
              dense
            >
              <div
                v-for="(file,index_file) in post.files_edit"
                :key="file.id"
                :index="index_file"
              >
                <v-list-item
                  v-if="file.state!='delete'"
                  style="min-height:60px;"
                >
                  <v-list-item-avatar class="my-0">
                    <v-icon
                      large
                      color="blue"
                    >mdi-text-box-outline</v-icon>
                  </v-list-item-avatar>

                  <v-list-item-content>
                    <v-list-item-title v-text="file.name" style="color:#2196f3; cursor:pointer;" @click="openFile(file)"></v-list-item-title>

                    <v-list-item-subtitle v-text="getFileSizeString(file.size)"></v-list-item-subtitle>
                  </v-list-item-content>

                  <v-list-item-action>
                    <v-btn icon @click="delFileInPostEdit(index,index_file)">
                      <v-icon>mdi-trash-can-outline</v-icon>
                    </v-btn>
                  </v-list-item-action>
                </v-list-item>
              </div>
            </v-list>

            <v-card-actions class="pa-4">
              <v-icon class="mx-2"
                @click="addImageInPostEdit(index)"
              >
                mdi-camera-outline
              </v-icon>
              <v-icon class="mx-2"
                @click="addFileInPostEdit(index)"
              >
                mdi-file-outline
              </v-icon>
              <v-divider
                class="mx-4"
                vertical
              ></v-divider>

              <div class="d-flex flex-row">
                <!-- v-model="post.date" -->
                <v-menu
                  ref="menu_post_date"
                  :return-value.sync="post.date_edit"  
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
                      style="max-width:120px; padding-top:2px;"
                      type="text"
                      v-model="post.date_edit"
                      hide-details
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>
                  </template>
                  <v-date-picker
                    v-model="post.date_edit"
                    locale="ru-ru"
                    :first-day-of-week="1"
                    @input="$refs.menu_post_date[index].save(post.date_edit)"
                  ></v-date-picker>
                </v-menu>
                <!-- @input="menuDate = false" -->
                <v-menu
                  ref="menu_post_time"
                  :close-on-content-click="false"
                  :return-value.sync="post.time_edit"
                  :nudge-right="40"
                  transition="scale-transition"
                  offset-y
                  max-width="290px"
                  min-width="290px"
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-text-field
                      :ref="'edit_post_time_'+post.id"
                      v-model="post.time_edit"
                      prepend-icon="mdi-clock-time-four-outline"
                      class="px-2 text-body-2"
                      style="max-width:85px; padding-top:2px;"
                      hide-details
                      readonly
                      v-bind="attrs"
                      v-on="on"
                    ></v-text-field>

                  </template>
                  <v-time-picker
                    v-model="post.time_edit"
                    full-width
                    @click:minute="$refs.menu_post_time[index].save(post.time_edit)"
                  ></v-time-picker>
                </v-menu>
                  
                <v-checkbox
                  hide-details
                  label="Видимость"
                  class="px-2"
                  style="margin-top:6px;"
                  v-model=post.visible_edit
                ></v-checkbox>
              </div>

              <v-spacer></v-spacer>
              
              <v-btn
                small
                color="primary"
                elevation="0"
                @click="saveEditPost(post.id)"
              >
                Сохранить
              </v-btn>
              <v-btn
                small
                color="error"
                elevation="0"
                @click="cancelEditPost(post.id)"
              >
                Отменить
              </v-btn>
            </v-card-actions>
            <v-divider></v-divider>

          </div>
          <!-- Блок для просмотра новости -->
          <div
            :id="'view_post_' + post.id"
          >
            <!-- Блок функций -->
            <div
              style="position:absolute; top:16px; right:16px;"
              v-if="$store.getters.getUserAccess.news_edit"
            >
              <!-- Иконка видимости новости -->
              <v-icon class="mx-2">{{ post.visible ? 'mdi-eye' : 'mdi-eye-off' }}</v-icon>
              <!-- Кнопка меню функций для работы с новостью -->
              <v-menu
                offset-y
                open-on-hover
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-btn
                    text
                    icon
                    v-bind="attrs"
                    v-on="on"
                  >
                    <v-icon>mdi-dots-horizontal</v-icon>
                  </v-btn>
                </template>
                <v-list>
                  <v-list-item
                    link
                  >
                    <v-list-item-title @click="editPost(post.id)">Редактировать</v-list-item-title>
                  </v-list-item>
                  <v-list-item
                    link
                  >
                    <v-list-item-title @click="delPost(post.id)">Удалить</v-list-item-title>
                  </v-list-item>
                </v-list>
              </v-menu>
            </div>
            <!-- Блок заголовка -->
            <v-card-title
              :id="'view_post_title_'+post.id"
              class="mr-12 pr-12"
            >
              <span v-html="post.title"></span>
            </v-card-title>
            <!-- Блок даты -->
            <v-card-subtitle
              :id="'view_post_date_'+post.id"
            >
              {{ post.date }} {{ post.time }}
            </v-card-subtitle>
            <!-- Блок текста -->
            <v-card-text
              :id="'view_post_text_'+post.id"
              class="text_post"
            >
              <span v-html="post.text"></span>
            </v-card-text>
            <!-- Блок прикрепленных картинок -->
            <v-row class="mx-3">
              <div
                v-for="(image,index) in post.images"
                :key="image.name"
                :index=index
              >
                <v-col>
                  <div class="mx-auto" style="position:relative; max-width:285px;">
                    
                    <v-img
                      :src="image.data"
                      @click="viewImageInPostAdd(image.data)"
                      style="cursor:pointer;"
                    ></v-img>

                  </div>
                </v-col>
              </div>
            </v-row>
            <!-- Блок прикрепленных файлов -->
            <v-list
              two-line
              dense
            >
              <v-list-item
                v-for="(file,index) in post.files"
                :key="file.name"
                style="min-height:60px;"
                :index=index
              >
                <v-list-item-avatar class="my-0">
                  <v-icon
                    large
                    color="blue"
                  >mdi-text-box-outline</v-icon>
                </v-list-item-avatar>

                <v-list-item-content>
                  <v-list-item-title v-text="file.name" @click="openFile(file)" style="color:#2196f3; cursor:pointer;"></v-list-item-title>

                  <v-list-item-subtitle v-text="getFileSizeString(file.size)"></v-list-item-subtitle>
                </v-list-item-content>

              </v-list-item>
            </v-list>
          </div>
          
          <!-- Блок информации количества лайков, комментариев, просмотров -->
          <v-card-actions>
            <v-chip 
              class="ma-2"
              outlined
              link
              @click="setLike(post.id)"
            >
              <v-icon
                v-if="post.liked==1"
                left
                color="red"
              >
                mdi-heart
              </v-icon>
              <v-icon
                v-if="post.liked==0"
                left
              >
                mdi-heart-outline
              </v-icon>
              {{ post.likes }}
            </v-chip>
            <v-chip
              class="ma-2"
              outlined
              link
              @click="getComments(post.id)"
            >
              <v-icon left>mdi-comment-outline</v-icon>
              {{ post.comments }}
            </v-chip>
            <v-spacer></v-spacer>
            <!-- <v-chip
              class="ma-2"
              outlined
            >
              <v-icon left>mdi-eye-outline</v-icon>
              {{ post.views }}
            </v-chip> -->
          </v-card-actions>
          <v-divider></v-divider>
            <!-- Блок комментариев -->
            <v-list two-line>
              <div
                v-for="comment in post.comments_list"
                :key="comment.id"
              >
                <v-list-item>
                  <v-list-item-content>
                    <div style="position:relative;">
                      <v-avatar
                        size="40"
                        class="mr-2"
                        style="float:left;" 
                      >     
                        <v-img v-if="comment.user_photo.length>0 && $store.getters.getUserAccess.appeals_in"
                          :src="'data:image/jpeg;base64,' + comment.user_photo"
                        ></v-img>
                        <v-icon v-else>
                          mdi-account-circle-outline
                        </v-icon>
                      </v-avatar>
                      <div>
                        <v-list-item-title style="font-size: 14px;" v-text="comment.user_name"></v-list-item-title>
                        <v-list-item-action-text v-text="comment.date"></v-list-item-action-text>
                      </div>
                      <div
                        v-if="$store.getters.getUserId==comment.user_id"
                        style="position:absolute; top:1px; right:1px;"
                      >
                        <v-btn
                          text
                          icon
                          @click="delComment(post.id,comment.id)"
                        >
                          <v-icon>mdi-trash-can-outline</v-icon>
                        </v-btn>
                      </div>
                    </div>
                    
                    <v-card-text class="py-0 pl-12 pr-0" v-html="comment.text"></v-card-text>
                  </v-list-item-content>

                  <!-- <v-list-item-avatar >
                    <v-img :src="'data:image/jpeg;base64,' + comment.user_photo"></v-img>
                  </v-list-item-avatar>

                  <v-list-item-content>
                    <v-list-item-title v-html="comment.user_name"></v-list-item-title>
                    <v-list-item-action-text v-text="comment.date"></v-list-item-action-text>
                    <v-list-item-subtitle v-html="comment.text"></v-list-item-subtitle>
                  </v-list-item-content> -->
                </v-list-item>
                <!-- <v-divider></v-divider> -->
              </div>
            </v-list>

          <v-card-actions class="mb-2" style="align-items: start;">

            <v-avatar color="success" size="40" class="mx-2" style="float:left; font-size: 14px;">
              <v-img
                v-if="$store.getters.getUserPhoto.length>0 && $store.getters.getUserAccess.appeals_in"
                :src="'data:image/jpeg;base64,' + $store.getters.getUserPhoto"
              ></v-img>
              <v-icon v-else>
                mdi-account-circle-outline
              </v-icon>
            </v-avatar>

            <!-- <v-textarea
              ref="ta_comments"
              v-model="text_comment"
              placeholder="Написать комментарий..."
              auto-grow
              outlined
              rows="1"
              hide-details="true"
              row-height="10"
              class="v-text-small"
            >
              <template v-slot:append>
                <div style="margin-top:-14px">
                  <v-menu
                    open-on-hover
                    top
                    offset-y
                  >
                    <template v-slot:activator="{ on, attrs }">
                      <v-btn
                        text
                        icon
                        v-bind="attrs"
                        v-on="on"
                      >
                        <v-icon>mdi-emoticon-happy-outline</v-icon>
                      </v-btn>
                    </template>
                    <v-card>
                      <div style="overflow-y:auto; max-height:550px;">
                        <div v-for="group in emojiTable()" :key="group.id">
                            <v-card-subtitle>
                              {{ group.name }}
                            </v-card-subtitle>
                            <v-card-text>
                              <table style="border: none; border-spacing: 2px;">
                                <tbody>
                                  <tr v-for="row in group.rows" :key="row">
                                    <td v-for="col in row" :key="col.html" >
                                      <img @mousedown="insertEmoji('ta_comments',col.html)" class='emoji' style="cursor:pointer;" :src='`${$store.getters.getUrlEmojiData}/${col.img}`' :alt='col.html'>
                                    </td>
                                  </tr>
                                </tbody>
                              </table>
                            </v-card-text>
                        </div>  
                      </div>
                    </v-card>
                  </v-menu>
                </div>
              </template>

            </v-textarea> -->

            <div
              class="pa-2"
              style="position:relative; width:calc(100% - 108px); min-height:40px; border-radius:4px; border: 1px solid rgb(192, 192, 192);"
            >
              <div
                :id="'ta_comments_'+post.id"
                contenteditable="true"
                aria-multiline="true"
                class="pr-8 text-body-2 text_post"
                style="width:100%; display:inline-block"
                @paste="onPasteText"
              >
              </div>
              <div
                style="position:absolute; top:1px; right:1px;"
              >
                <v-menu
                  open-on-hover
                  :close-on-content-click="false"
                  top
                  offset-y
                >
                  <template v-slot:activator="{ on, attrs }">
                    <v-btn
                      text
                      icon
                      v-bind="attrs"
                      v-on="on"
                    >
                      <v-icon>mdi-emoticon-happy-outline</v-icon>
                    </v-btn>
                  </template>
                  <v-card>
                    <div style="overflow-y:auto; max-height:550px;">
                      <div v-for="group in emojiTable()" :key="group.id">
                          <v-card-subtitle>
                            {{ group.name }}
                          </v-card-subtitle>
                          <v-card-text>
                            <table style="border: none; border-spacing: 2px;">
                              <tbody>
                                <tr v-for="row in group.rows" :key="row">
                                  <td v-for="col in row" :key="col.html" >
                                    <img @mousedown="insertEmoji('ta_comments_'+post.id, col.html)" class='emoji' style="cursor:pointer;" :src='`${$store.getters.getUrlEmojiData}/${col.img}`' :alt='col.html'>
                                  </td>
                                </tr>
                              </tbody>
                            </table>
                          </v-card-text>
                      </div>  
                    </div>
                  </v-card>
                </v-menu>
              </div>
            </div>

            <v-btn
              class="mx-2"
              text
              icon
              @click="addComment(post.id, 'ta_comments_'+post.id)"
            >
              <v-icon>mdi-send</v-icon>
            </v-btn>
            
            
          </v-card-actions>
        </v-card>
        
      </div>
      <!-- Кнопка подгрузки новостей -->
      <v-card
          rounded="lg"
          elevation="0"
          outlined
          class="mx-auto mt-6"
          max-width="960"
          v-if="button_next_posts"
        >
          <v-row
            align="center"
            justify="center"
            class="py-5"
          >
            <div class="text-body-2 blue--text" @click="getPosts()" style="cursor:pointer;">
              Показать еще...
            </div>
          </v-row>
        </v-card>
           
      <v-btn
        v-scroll="onScroll"
        v-show="fab"
        fab
        dark
        fixed
        bottom
        right
        color="primary"
        @click="toTop"
      >
        <v-icon>mdi-chevron-up</v-icon>
      </v-btn>

      <!-- <v-dialog
        v-model="formPost2"
        max-width="800px"
        persistent
      >
        <v-card>
          <v-toolbar
            color="blue darken-4"
            dark
          >
            <v-toolbar-title>Добавление новости</v-toolbar-title>
            <v-spacer></v-spacer>
            <v-btn icon
              @click="formPost = false">
              <v-icon>mdi-close</v-icon>
            </v-btn>
          </v-toolbar>

          <v-form>
            <v-container class="pa-7">
              <v-text-field
                label="Prepend"
                prepend-icon="mdi-map-marker"
              ></v-text-field>
              <v-menu
                v-model="menuDate"
                :close-on-content-click="false"
                :nudge-right="40"
                transition="scale-transition"
                offset-y
                min-width="auto"
              >
                <template v-slot:activator="{ on, attrs }">
                  <v-text-field
                    style="max-width:120px;"
                    type="text"
                    label="Дата" 
                    variant="outlined"
                    v-model="date_post"
                    filled
                    dense
                    readonly
                    v-bind="attrs"
                    v-on="on"
                  ></v-text-field>
                </template>
                <v-date-picker
                  v-model="date_post"
                  locale="ru-ru"
                  :first-day-of-week="1"
                  @input="menuDate = false"
                ></v-date-picker>
              </v-menu>

              <v-checkbox
                v-model="visible_post"
                label="Видимость"
              ></v-checkbox>

              <v-text-field
                type="text"
                label="Тема" 
                variant="outlined"
                v-model="title_post"
                filled
                dense
              ></v-text-field>
  
              <v-textarea
                clearable
                label="Текст"
                variant="outlined"
                v-model="text_post"
                no-resize
                filled
              ></v-textarea>

              <v-file-input
                v-model="files_post"
                multiple
                small-chips
                truncate-length="20"
                label="Файлы"
                filled
                dense
              ></v-file-input>

              <v-card-actions>

                <v-btn
                  color="primary"
                  @click="creatAppeal"
                  text
                >
                    Добавить
                </v-btn>

              </v-card-actions>
                
            </v-container>
          </v-form>

        </v-card>
      </v-dialog> -->
      <v-dialog
        v-model="viewImageDialog"
        max-width="800px"
      >
        <v-card>
          <v-img
            :src="viewImageData"
          ></v-img>
        </v-card>
      </v-dialog>

      <v-dialog
        v-model="viewEditHTMLPostDialog"
        max-width="800px"
      >
        <v-card>
          <v-container class="pa-7">
            <v-textarea
              class="text-body-2"
              style="line-height:1.3rem"
              outlined
              v-model="textEditHTML"
              rows="15"
            ></v-textarea>
            <v-card-actions>
              
              <v-btn
                small
                color="primary"
                @click="saveEditHTML()"
              >
                  Применить
              </v-btn>
              <v-spacer></v-spacer>
              <v-btn
                small
                color="error"
                @click="cancelEditHTML()"
              >
                  Отменить
              </v-btn>
            </v-card-actions>
          </v-container> 
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

      <!-- <v-overlay
        absolute="true"
        :value="overlay"
      >
        <v-progress-circular
          :size="70"
          color="white"
          indeterminate
        ></v-progress-circular>
      </v-overlay> -->

    </div>
</template>

<script>
  // document.execCommand('defaultParagraphSeparator', false, 'p')
  export default {
    data () {
      return {
        viewImageDialog: false,

        viewEditHTMLPostDialog: false,
        idFieldEditHTML: '',
        textEditHTML: '',

        viewImageData: '',
        button_add: true,
        formPost: false,
        formPost2: false,
        visible_post: true,
        // date_post: (new Date(Date.now() - (new Date()).getTimezoneOffset() * 60000)).toISOString().substr(0, 10),
        date_post: new Date().toISOString().substr(0, 10),
        menuDate: false,
        time_post: new Date().getHours() + ':' + new Date().getMinutes(),
        menuTime: false,
        menuPeriodFilter: false,
        period_post_filter: new Date().toISOString().substr(0, 7),

        title_post: '',
        text_post: '',
        files_post: [],
        files_post: [],
        images_post: [],

        emojiData: [],
        publicPath: window.location.origin,
        fab: false,
        show: false,
        posts: [],
        count_posts: 0,
        posts_in_part: 10,
        button_next_posts: false,
        text_new_post: '',
        text_comment: '',

        snackbar: false,
        snackColor:'blue-grey',
        snackText: '',

        // overlay: false,
      }
    },
    methods: {
      cancelEditHTML(){
        this.idFieldEditHTML = ''
        this.textEditHTML = ''
        this.viewEditHTMLPostDialog = false
      },
      saveEditHTML(){
        document.getElementById(this.idFieldEditHTML).innerHTML = this.textEditHTML
        if(this.idFieldEditHTML=='ta_text_post')this.postChanged()
        this.idFieldEditHTML = ''
        this.textEditHTML = ''
        this.viewEditHTMLPostDialog = false      
      },
      openDialogEditHTML(id_field){
        this.idFieldEditHTML = id_field
        const textHTML = document.getElementById(id_field).innerHTML
        this.textEditHTML = textHTML
        this.viewEditHTMLPostDialog = true
      },
      onPasteText(e){
        console.log('e: ',e)
        e.preventDefault()
        var text = e.clipboardData.getData('text/plain')
        console.log('text: ',text)
        window.document.execCommand('insertText', false, text)
        return false
      },
      formatHTML(ref,type){     
        // var sel = window.getSelection()
        // console.log('sel: ',sel)
        // if (sel.focusNode != null && (sel.focusNode.id == ref || sel.focusNode.parentElement.id == ref || sel.focusNode.parentElement.parentElement.id == ref)) {
          if(type=='bold'){
            document.execCommand('bold', false, null)
          }
          if(type=='italic'){
            document.execCommand('italic', false, null)
          } 
          if(type=='paragraph'){
            document.execCommand('formatBlock', false, 'p');
          }   
          if(type=='br'){
            document.execCommand('insertHTML', false, '<br>')
          }
          if(type=='removeFormat'){
            document.execCommand('removeFormat', false, null)
          }
          if(type=='delete'){
            // console.log('ref: ',ref)
            document.getElementById(ref).innerHTML = ''
            // document.execCommand('delete', false, null)
            if(ref=='ta_text_post')this.postChanged()
          }
          if(type=='undo'){
            document.execCommand('undo', false, null)
          } 
          if(type=='redo'){
            document.execCommand('redo', false, null)
          } 
        // }
      },
      editHTML(id){

      },
      getDataBase64FromFile(fileObj){
        let reader = new FileReader()
        reader.readAsDataURL(fileObj)
        reader.onload = function(){
          return reader.result
        }
        reader.onerror = function(){
          return reader.error
        }
      },
      openFileAddPost(index){
        var file = this.files_post[index]
        let reader = new FileReader()
        reader.readAsDataURL(file)
        reader.onload = function(){
          // console.log(reader.result)
          const name_file = file.name
          var a = document.createElement("a")
          a.href = reader.result
          a.download = name_file
          a.click()
        }
        reader.onerror = function(){
          console.log(reader.error)
        }
      },
      openFile(item_file){
        const name_file = item_file.name
        var a = document.createElement("a")
        a.href = "data:;base64," + item_file.data
        a.download = name_file
        a.click()
      },
      getFileSizeString(bytes){
        // console.log('bytes: ',bytes)
        const kBytes = Math.round(bytes / 1024)
        return kBytes.toString() + ' КБ'
      },
      saveEditPost(id){
        const object_post = this.posts.find(item => item.id===id)
        const date_post = object_post.date_edit
        const time_post = object_post.time_edit
        const visible_post = object_post.visible_edit
        const title_post = this.getTextForSave(document.getElementById('edit_post_title_'+id))
        const text_post = this.getTextForSave(document.getElementById('edit_post_text_'+id))
        console.log('text_post: ',text_post)

        // Формируем массивы файлов для добавления и удаления
        var arrNewFiles = new Array()
        var delFilesStr = ""
        var index
        for (index = 0; index < object_post.files_edit.length; ++index){        
          var typeObj = Object.prototype.toString.call(object_post.files_edit[index])
          if(typeObj==="[object File]"){
            arrNewFiles.push(object_post.files_edit[index])
          }else if(object_post.files_edit[index].state == 'delete'){
            // arrDelFiles.push(object_post.files_edit[index])
            delFilesStr = delFilesStr + (delFilesStr.length>0 ? ';' : '') + object_post.files_edit[index].id.toString()
          }
        }
        // Формируем массивы картинок для добавления и удаления
        var arrNewImages = new Array()
        var delImagesStr = ""
        var index
        for (index = 0; index < object_post.images_edit.length; ++index){        
          if(object_post.images_edit[index].hasOwnProperty('state') && object_post.images_edit[index].state == 'delete'){
            delImagesStr = delImagesStr + (delImagesStr.length>0 ? ';' : '') + object_post.images_edit[index].id.toString()    
          }else if(object_post.images_edit[index].hasOwnProperty('file_obj') && Object.prototype.toString.call(object_post.images_edit[index].file_obj)==="[object File]"){
            arrNewImages.push(object_post.images_edit[index].file_obj)
          }
        }

        console.log('delFilesStr: ',delFilesStr)
        // console.log('arrDelFiles: ',arrDelFiles)
        
        if(title_post!='' && text_post!=''){
          this.$store.dispatch('setProgressMain', true)
          // this.overlay = true
          var formData = new FormData()
          // Добавление полей
          var data = {
            id_post: id,
            text_post: text_post,
            title_post: title_post,
            date_post: date_post,
            time_post: time_post,
            visible_post: visible_post,
            // arrDelFiles: arrDelFiles,
            delFiles: delFilesStr,
            delImages: delImagesStr,
          };
          for (var key in data) {
            formData.append(key, data[key])
          }
          for (var key in arrNewFiles) {
            formData.append(key+'_attach', arrNewFiles[key])
          }
          for (var key in arrNewImages) {
            formData.append(key+'_image', arrNewImages[key])
          }
          var xhr = new XMLHttpRequest()
          xhr.onreadystatechange = function(vm){
            if(xhr.readyState==4){
              // this.overlay = false
              vm.$store.dispatch('setProgressMain', true)
              //console.log('responseText: ',xhr.responseText)
              var resJsonObj = JSON.parse(xhr.responseText)
              console.log('resJsonObj: ',resJsonObj)
              if(resJsonObj.error==""){
                // vm.getPosts()
                const title = vm.getTextForView(resJsonObj.title)
                const text = vm.getTextForView(resJsonObj.text)
                const date = resJsonObj.date.substr(6,4) + '-' + resJsonObj.date.substr(3,2) + '-' + resJsonObj.date.substr(0,2)
                const time = resJsonObj.date.substr(11,5)

                object_post.date_edit = date
                object_post.date = date
                object_post.time_edit = time
                object_post.time = time
                object_post.visible_edit = resJsonObj.visible
                object_post.visible = resJsonObj.visible
                object_post.title = title
                object_post.text = text
                object_post.files = JSON.parse(JSON.stringify(resJsonObj.files));
                object_post.files_edit = JSON.parse(JSON.stringify(resJsonObj.files));
                object_post.images = JSON.parse(JSON.stringify(resJsonObj.images));
                object_post.images_edit = JSON.parse(JSON.stringify(resJsonObj.images));

                document.getElementById('edit_post_'+id).style.display = "none"
                document.getElementById('view_post_'+id).style.display = "block"
                // vm.posts.unshift({ 'id':resJsonObj.id, 'title':title, 'date_edit':date, 'time_edit':time, 'date':date, 'time':time, 'text':text,'visible_edit': visible, 'visible':resJsonObj.visible, 'user_name': resJsonObj.user_name, 'user_photo': resJsonObj.user_photo, 'likes': resJsonObj.likes, 'comments': resJsonObj.comments, 'views': resJsonObj.views})

                vm.snackText = 'Изменения сохранены!'
                // vm.overlay = false
                vm.$store.dispatch('setProgressMain', false)
                vm.snackColor = "success"
                vm.snackbar = true
              }else{
                vm.snackText = 'Ошибка сохранения изменений!'
                // vm.overlay = false
                vm.$store.dispatch('setProgressMain', false)
                vm.snackColor = "red accent-2"
                vm.snackbar = true
              }
              vm.$store.dispatch('setProgressMain', false)
            }
          }.bind(xhr, this)
          xhr.open("post", this.$store.getters.getUrlTracker + "?action=edit_post", true);
          xhr.send(formData);
        }else{
          this.snackText = 'Заполните все поля!';
          this.snackColor = 'red accent-2';
          this.snackbar = true;
        }
      },
      cancelEditPost(id){
        const object_post = this.posts.find(item => item.id===id)

        object_post.date_edit = object_post.date
        object_post.time_edit = object_post.time
        object_post.visible_edit = object_post.visible
        object_post.files_edit = JSON.parse(JSON.stringify(object_post.files));
        object_post.images_edit = JSON.parse(JSON.stringify(object_post.images));
        // object_post.files_edit = object_post.files

        document.getElementById('edit_post_'+id).style.display = "none"
        document.getElementById('view_post_'+id).style.display = "block"

        var edit_post_title = document.getElementById('edit_post_title_'+id)
        var edit_post_text = document.getElementById('edit_post_text_'+id)

        edit_post_title.innerHTML = ''
        edit_post_text.innerHTML = ''
      },
      editPost(id){
        document.getElementById('edit_post_'+id).style.display = "block"
        document.getElementById('view_post_'+id).style.display = "none"

        var edit_post_title = document.getElementById('edit_post_title_'+id)
        var view_post_title = document.getElementById('view_post_title_'+id)

        var edit_post_text = document.getElementById('edit_post_text_'+id)
        var view_post_text = document.getElementById('view_post_text_'+id)

        // console.log('view_post_title',view_post_title)
        // console.log('view_post_title',view_post_title)

        edit_post_title.innerHTML = view_post_title.firstChild.innerHTML
        edit_post_text.innerHTML = view_post_text.firstChild.innerHTML
      },
      delPost(id){
        // this.overlay = true
        this.$store.dispatch('setProgressMain', true)
        //console.log('delPost',id)
        var oXmlHttpRequest = new XMLHttpRequest()
        const strRequest = this.$store.getters.getUrlTracker + '?action=del_post&id=' + id
        oXmlHttpRequest.open("get",strRequest,true)
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            // this.overlay = false
            vm.$store.dispatch('setProgressMain', false)
            //console.log('responseText: ', oXmlHttpRequest.responseText)
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
            if(resJsonObj.error == ""){
              const index_post = vm.posts.findIndex(item => item.id===resJsonObj.id)
              //console.log('index_post: ', index_post)
              if(index_post!=-1) {
                vm.posts.splice(index_post, 1)
                vm.count_posts--
              }
              vm.snackText = 'Новость удалена!'
              // vm.overlay = false
              vm.$store.dispatch('setProgressMain', false)
              vm.snackColor = "success"
              vm.snackbar = true
            }else{
              vm.snackText = 'Ошибка удаления новости!'
              // vm.overlay = false
              vm.$store.dispatch('setProgressMain', false)
              vm.snackColor = "red accent-2"
              vm.snackbar = true
            }   
          }
        }.bind(oXmlHttpRequest, this)
        oXmlHttpRequest.send(null)
      },
      delComment(id_post,id_comment){
        this.$store.dispatch('setProgressMain', true)
        var oXmlHttpRequest = new XMLHttpRequest()
          const strRequest = this.$store.getters.getUrlTracker + '?action=del_comment&id_comment='+id_comment
          oXmlHttpRequest.open("get",strRequest,true)
          oXmlHttpRequest.onreadystatechange = function(vm){
            if(oXmlHttpRequest.readyState==4){
              var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
              // console.log('resJsonObj: ', resJsonObj)
              if(resJsonObj.error == ""){
                const object_post = vm.posts.find(item => item.id===id_post)
                const index_comment = object_post.comments_list.findIndex(item => item.id===id_comment)
                // console.log('index_comment: ', index_comment)
                if(index_comment!=-1) {
                  object_post.comments_list.splice(index_comment, 1)
                  object_post.comments--
                }
                vm.snackText = 'Комментарий удален!'
                vm.$store.dispatch('setProgressMain', false)
                vm.snackColor = "success"
                vm.snackbar = true
              }else{
                vm.snackText = 'Ошибка удаления комментария!'
                vm.$store.dispatch('setProgressMain', false)
                vm.snackColor = "red accent-2"
                vm.snackbar = true
              }
            }
          }.bind(oXmlHttpRequest, this)
          oXmlHttpRequest.send(null)
      },
      getComments(id_post){
        const object_post = this.posts.find(item => item.id===id_post)
        if(object_post.comments_list.length>0){
          object_post.comments_list = []
        }else{    
          // console.log('object_post: ', object_post)
          var oXmlHttpRequest = new XMLHttpRequest()
          const strRequest = this.$store.getters.getUrlTracker + '?action=get_comments&id_post='+id_post
          oXmlHttpRequest.open("get",strRequest,true)
          oXmlHttpRequest.onreadystatechange = function(vm){
            if(oXmlHttpRequest.readyState==4){
              var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
              // console.log('getUserId: ', vm.$store.getters.getUserId)
              // console.log('resJsonObj: ', resJsonObj)            
              // console.log('object_post.comments_list: ', object_post.comments_list)
              for (var indx in resJsonObj) {
                object_post.comments_list.push({ 'id':resJsonObj[indx].id, 'date':resJsonObj[indx].date, 'text':vm.getTextForView(resJsonObj[indx].text), 'user_id':resJsonObj[indx].user_id, 'user_name':resJsonObj[indx].user_name, 'user_photo':resJsonObj[indx].user_photo })
              }
            }
          }.bind(oXmlHttpRequest, this)
          oXmlHttpRequest.send(null)
        }
      },
      addComment(id_post, id_el){
        const object_post = this.posts.find(item => item.id===id_post)
        const object_field_text = document.getElementById(id_el)
        const text = this.getTextForSave(object_field_text)
        var formData = new FormData()
        // Добавление полей
        var data = {
            text: text,
            id_post: id_post,
          };
        for (var key in data) {
          formData.append(key, data[key])
        }
        var oXmlHttpRequest = new XMLHttpRequest()
        const strRequest = this.$store.getters.getUrlTracker + '?action=add_comment'
        console.log('id_post: ', id_post)
        console.log('text: ', text)
        oXmlHttpRequest.open("post",strRequest,true)
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
            console.log('resJsonObj: ', resJsonObj)
            object_post.comments_list.unshift({ 'id':resJsonObj.id, 'date':resJsonObj.date, 'text':vm.getTextForView(resJsonObj.text), 'user_name':resJsonObj.user_name, 'user_photo':resJsonObj.user_photo })
            object_field_text.innerHTML = ''
            object_post.comments++
          }
        }.bind(oXmlHttpRequest, this)
        oXmlHttpRequest.send(formData)
      },
      setLike(id_post){
        const object_post = this.posts.find(item => item.id===id_post)
        var formData = new FormData()
        // Добавление полей
        var data = {
            id_post: id_post,
          };
        for (var key in data) {
          formData.append(key, data[key])
        }
        var oXmlHttpRequest = new XMLHttpRequest()
        const strRequest = this.$store.getters.getUrlTracker + '?action=set_like'
        oXmlHttpRequest.open("post",strRequest,true)
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
            // console.log('resJsonObj: ', resJsonObj)
            if(resJsonObj.state_delete==1){
              object_post.likes--
              object_post.liked=0
            }else{
              object_post.likes++
              object_post.liked=1
            }
          }
        }.bind(oXmlHttpRequest, this)
        oXmlHttpRequest.send(formData)
      },
      titleChanged(){
        if(document.getElementById('ta_title_post').innerHTML.length>0){
          document.getElementById('label_title_post').style.display = "none"
        }else{
          document.getElementById('label_title_post').style.display = "block"
        }
      },
      postChanged(){
        if(document.getElementById('ta_text_post').innerHTML.length>0){
          document.getElementById('label_text_post').style.display = "none"
        }else{
          document.getElementById('label_text_post').style.display = "block"
        }
      },
      insertEmoji(ref,html){
        const img = this.getEmojiImg(html)
        const emoji = img.length>0 ? "<img class='emoji' src='" + this.$store.getters.getUrlEmojiData + "/" + img + "' alt='" + html + "'>" : "<span id='emoji'>" + html + "</span>"
        // console.log('emoji: ',emoji)
        if (!emoji.length) return
        this.insertTextAtCaret(ref,emoji)
        // var sel = window.getSelection();
        // if (sel.focusNode != null && (sel.focusNode.id == ref || sel.focusNode.parentElement.id == ref || sel.focusNode.parentElement.parentElement.id == ref)) {
        //   document.execCommand('insertHTML', false, emoji)
        // }
      },
      createElementFromHTML(htmlString){
        var div = document.createElement('div');
        div.innerHTML = htmlString.trim();
        return div;
      },
      insertTextAtCaret(ref,text) {
        var sel, range;
        sel = window.getSelection();

        // console.log('getSelection: ',window.getSelection())
        // console.log('ref: ',ref)
        // console.log('sel.focusNode: ',sel.focusNode)
        // console.log('focusNode.id: ',sel.focusNode.id)
        // console.log('focusNode.parentElement.id: ',sel.focusNode.parentElement.id)
        // console.log('focusNode.parentElement.parentElement.id: ',sel.focusNode.parentElement.parentElement.id)
        
        if (sel.focusNode != null && (sel.focusNode.id == ref || sel.focusNode.parentElement.id == ref || sel.focusNode.parentElement.parentElement.id == ref)) {
          if (sel.getRangeAt && sel.rangeCount) {
            range = sel.getRangeAt(0);
            range.deleteContents();
            // range.insertNode( document.createTextNode(text) );
            range.insertNode(this.createElementFromHTML(text).firstChild);
            console.log('range: ',range)
            range.setStart(range.endContainer, range.endOffset);
            //sel.removeAllRanges()
          }
        }else{
          document.getElementById(ref).innerHTML += text
        }
        // else if (document.selection && document.selection.createRange) {
        //   document.selection.createRange().text = text;
        // }
      },
      addImageInPost(){
        var input = document.createElement('input')
        input.type = 'file'
        input.accept = 'image/*'
        input.onchange = e => {
          var file = e.target.files[0]
          if(file.type.toString().indexOf('image')!=-1){
            let reader = new FileReader()
            reader.readAsDataURL(file)
            reader.onload = function(vm){
              // console.log('result: ', reader.result)
              vm.images_post.push({ 'file_obj':file, 'data':reader.result })
            }.bind(reader,this)
            reader.onerror = function(){
              console.log('error: ', reader.error)
            }  
          }
          console.log('images_post: ', this.images_post)
        }
        input.click();
      },
      delFileInPostAdd(index){
        this.files_post.splice(index,1)
      },
      delImageInPostAdd(index){
        this.images_post.splice(index,1)
      },
      viewImageInPostAdd(data){
        // this.viewImageData = this.images_post[index].data
        this.viewImageData = data
        this.viewImageDialog = true
      },
      delFileInPostEdit(index,index_file){
        var file_item = this.posts[index].files_edit[index_file]
        var typeObj = Object.prototype.toString.call(file_item)
        if(typeObj==="[object File]"){
          // console.log('index_file: ',index_file)
          this.posts[index].files_edit.splice(index_file,1)
        }else{
          file_item.state = 'delete'
        }
        // console.log('typeObj: ',typeObj)
        // this.posts[index].files_edit[index_file].state = 'delete'

        // var filesArrRes = new Array()
        // var indx
        // var files_edit = this.posts[index].files_edit
        // for (indx = 0; indx < files_edit.length; ++indx) { 
        //   filesArrRes.push(files_edit[indx])    
        //   if(indx==index_file)filesArrRes[indx].state = 'delete'
        //   // if(indx!=index_file)filesArrRes.push(files_edit[indx])
        // }
        // this.posts[index].files_edit = []
        // this.posts[index].files_edit = filesArrRes
        console.log('files_edit',this.posts[index].files_edit)
        console.log('files',this.posts[index].files)
      },
      delImageInPostEdit(index,index_file){
        var file_item = this.posts[index].images_edit[index_file]
        // console.log('file_item: ',file_item)
        if(file_item.hasOwnProperty('state')){
          file_item.state = 'delete'
        }else{
          this.posts[index].images_edit.splice(index_file,1)
        }
      },
      addImageInPostEdit(index){
        var input = document.createElement('input')
        input.type = 'file'
        input.accept = 'image/*'
        input.onchange = e => {
          var file = e.target.files[0]
          if(file.type.toString().indexOf('image')!=-1){
            let reader = new FileReader()
            reader.readAsDataURL(file)
            reader.onload = function(vm){
              // console.log('result: ', reader.result)
              // vm.images_post.push({ 'file_obj':file, 'data':reader.result })
              vm.posts[index].images_edit.push({ 'file_obj':file, 'data':reader.result })
            }.bind(reader,this)
            reader.onerror = function(){
              console.log('error: ', reader.error)
            }  
          }
          console.log('images_edit: ', this.posts[index].images_edit)
        }
        input.click();
      },
      addFileInPostEdit(index){
        var input = document.createElement('input')
        input.type = 'file'
        input.onchange = e => {
          var file = e.target.files[0]
          // console.log('file: ', file)
          this.posts[index].files_edit.push(file)
          // console.log('files_edit: ', this.posts[index].files_edit)
        }
        input.click();
      },
      addFileInPost(){
        var input = document.createElement('input')
        input.type = 'file'
        input.onchange = e => {
          var file = e.target.files[0]
          // console.log('file: ', file)
          this.files_post.push(file)
          console.log('files_post: ', this.files_post)
        }
        input.click();
      },
      closeFormAddPost(){
        this.formPost = false
        this.button_add = true      
        this.files_post = []
        this.images_post = []
        this.visible_post = true
        this.date_post = new Date().toISOString().substr(0, 10)
        this.time_post = new Date().getHours() + ':' + new Date().getMinutes()
      },
      openFormAddPost(){
        this.formPost = true
        this.button_add = false
      },
      addPost(){
        // var text_post = this.getTextForSave('ta_text_post')
        var text_post = this.getTextForSave(document.getElementById('ta_text_post'))
        // console.log('text_post: ', text_post)
        // var title_post = this.getTextForSave('ta_title_post')
        var title_post = this.getTextForSave(document.getElementById('ta_title_post'))
        // console.log('title_post: ', title_post)

        if(text_post!='' && title_post!=''){
          // this.overlay = true
          this.$store.dispatch('setProgressMain', true)
          var formData = new FormData()
          // Добавление полей
          var data = {
            text_post: text_post,
            title_post: title_post,
            date_post: this.date_post,
            time_post: this.time_post,
            visible_post: this.visible_post,
          };
          for (var key in data) {
            formData.append(key, data[key])
          }
          for (var key in this.files_post) {
            formData.append(key + '_attach', this.files_post[key])
          }
          for (var key in this.images_post) {
            formData.append(key + '_image', this.images_post[key].file_obj)
          }
          var xhr = new XMLHttpRequest()
          xhr.onreadystatechange = function(vm){
            if(xhr.readyState==4){
              // this.overlay = false
              vm.$store.dispatch('setProgressMain', false)
              console.log('responseText: ',xhr.responseText)
              var resJsonObj = JSON.parse(xhr.responseText)
              console.log('resJsonObj: ',resJsonObj)
              if(resJsonObj.error==""){
                // vm.getPosts()
                const title = vm.getTextForView(resJsonObj.title)
                const text = vm.getTextForView(resJsonObj.text)
                const date = resJsonObj.date.substr(6,4) + '-' + resJsonObj.date.substr(3,2) + '-' + resJsonObj.date.substr(0,2)
                const time = resJsonObj.date.substr(11,5)
                var files = JSON.parse(JSON.stringify(resJsonObj.files));
                var files_edit = JSON.parse(JSON.stringify(resJsonObj.files));
                var images = JSON.parse(JSON.stringify(resJsonObj.images));
                var images_edit = JSON.parse(JSON.stringify(resJsonObj.images));
                vm.posts.unshift({ 'id':resJsonObj.id, 'title':title, 'date_edit':date, 'time_edit':time, 'date':date, 'time':time, 'text':text,'files_edit': files_edit, 'files':files,'images_edit': images_edit, 'images':images,'visible_edit': resJsonObj.visible, 'visible':resJsonObj.visible, 'user_name': resJsonObj.user_name, 'user_photo': resJsonObj.user_photo, 'likes': resJsonObj.likes, 'liked': resJsonObj.liked, 'comments': resJsonObj.comments, 'views': resJsonObj.views, 'comments_list': [] })
                vm.count_posts++
                vm.button_add = true
                vm.formPost = false
                vm.date_post = new Date().toISOString().substr(0, 10)
                vm.time_post = new Date().getHours() + ':' + new Date().getMinutes()
                vm.visible_post = true
                vm.files_post = []
                vm.images_post = []
                vm.snackText = 'Новость добавлена!'
                // vm.overlay = false
                vm.$store.dispatch('setProgressMain', false)
                vm.snackColor = "success"
                vm.snackbar = true
              }else{
                vm.snackText = 'Ошибка добавления новости!'
                // vm.overlay = false
                vm.$store.dispatch('setProgressMain', false)
                vm.snackColor = "red accent-2"
                vm.snackbar = true
              }
            }
          }.bind(xhr, this)
          xhr.open("post", this.$store.getters.getUrlTracker + "?action=add_post", true);
          xhr.send(formData);
        }else{
          this.snackText = 'Заполните все поля!';
          this.snackColor = 'red accent-2';
          this.snackbar = true;
        }

      },
      getEmojiImg(html){
        var index
        for (index = 0; index < this.emojiData.length; ++index) {
          const item_res = this.emojiData[index].items.find(item => item.html===html)
          if(item_res) return item_res.img
        }
        return ''
      },
      emojiTable(){
        const max_cols = 15
        var arr_res = new Array();
        var index
        for (index = 0; index < this.emojiData.length; ++index) {
          var rows = new Array();
          var itemsArr = this.emojiData[index].items
          var counter = 0
          var indexRow
          for (indexRow = 0; indexRow < Math.ceil(itemsArr.length / max_cols); ++indexRow) {
            var cols = new Array();
            var indexCol
            for (indexCol = 0; indexCol < max_cols; ++indexCol) {
              if(counter > itemsArr.length - 1) break
              cols.push({ "img":itemsArr[counter].img, "html":itemsArr[counter].html })
              counter ++
            }
            rows.push(cols)
          }
          arr_res.push({ "id": this.emojiData[index].id, "name":this.emojiData[index].name, "rows":rows })
        }
        return arr_res
      },
      // getTextForSave_(curNode){
      //   var text_res = ''
      //   var curNodeArr = curNode.childNodes
      //   // console.log('curNodeArr: ',curNodeArr)
      //   for (let i = 0; i < curNodeArr.length; i++) {
      //     // console.log('curNode: ',curNodeArr[i])
      //     if(curNodeArr[i].nodeType == 1 && curNodeArr[i].nodeName == "IMG" && curNodeArr[i].className == "emoji"){
      //       text_res += "<span id='emoji'>" + curNodeArr[i].alt + "</span>"
      //     }else if(curNodeArr[i].nodeType == 1 && (curNodeArr[i].nodeName == "DIV" || curNodeArr[i].nodeName == "P" || curNodeArr[i].nodeName == "STRONG" || curNodeArr[i].nodeName == "B" || curNodeArr[i].nodeName == "EM")){
      //       const teg_start = "<"+ curNodeArr[i].nodeName.toString() +">"
      //       const teg_end = "</"+ curNodeArr[i].nodeName.toString() +">"
      //       text_res += teg_start
      //       var childNodeArr = curNodeArr[i].childNodes
      //       for (let j = 0; j < childNodeArr.length; j++) {
      //         if(childNodeArr[j].nodeType == 1 && childNodeArr[j].nodeName == "IMG" && childNodeArr[j].className == "emoji"){
      //           text_res += "<span id='emoji'>" + childNodeArr[j].alt + "</span>"
      //         }else if(childNodeArr[j].nodeType == 3 && childNodeArr[j].nodeName == "#text"){
      //           text_res += childNodeArr[j].nodeValue
      //         }
      //       }
      //       text_res += teg_end
      //     }else if(curNodeArr[i].nodeType == 1 && curNodeArr[i].nodeName == "BR"){
      //       text_res += curNodeArr[i].outerHTML
      //     }else if(curNodeArr[i].nodeType == 3 && curNodeArr[i].nodeName == "#text"){
      //       text_res += curNodeArr[i].nodeValue
      //     }
      //   }
      //   return text_res
      //   // let range = new Range()
      //   // range.setStart(id_el, 0)
      //   // range.setEnd(id_el, 0)
      // },
      getTextForSave(curNode){
        const text = curNode.innerHTML
        const tegStart = "<img class='emoji'"
        const tegEnd = ">"
        var text_res = text
        var text_teg = ''
        var curNode
        var paste_teg = ''
        let IndexTegStart
        let IndexTegEnd
        let lastIndex = -1
        while ((lastIndex = text.indexOf(tegStart, lastIndex + 1)) !== -1) {
          IndexTegStart = lastIndex
          IndexTegEnd = text.indexOf(tegEnd, lastIndex + 1)
          text_teg = text.substr(IndexTegStart, IndexTegEnd-IndexTegStart+tegEnd.length)
          // console.log('text_teg: ',text_teg)
          curNode = this.createElementFromHTML(text_teg).firstChild
          paste_teg = "<span id='emoji'>" + curNode.alt + "</span>"
          // console.log('img_teg: ',img_teg)
          // text_res.replace(text_teg,img_teg)
          text_res = text_res.replaceAll(text_teg, paste_teg)
          // console.log('indexOf: ',text_res.indexOf(text_teg))
        }
        // console.log('text_res: ',text_res)
        return text_res
      },
      getTextForView(text){
        const tegStart = "<span id='emoji'>"
        const tegEnd = "</span>"
        var text_res = text
        var text_teg = ''
        var curNode
        var img_teg = ''
        let IndexTegStart
        let IndexTegEnd
        let lastIndex = -1
        while ((lastIndex = text.indexOf(tegStart, lastIndex + 1)) !== -1) {
          IndexTegStart = lastIndex
          IndexTegEnd = text.indexOf(tegEnd, lastIndex + 1)
          text_teg = text.substr(IndexTegStart, IndexTegEnd-IndexTegStart+tegEnd.length)
          // console.log('text_teg: ',text_teg)
          curNode = this.createElementFromHTML(text_teg).firstChild
          img_teg = "<img class='emoji' src='" + this.$store.getters.getUrlEmojiData + "/" + this.getEmojiImg(curNode.innerText) + "' alt='" + curNode.innerText + "'>"
          // console.log('img_teg: ',img_teg)
          // text_res.replace(text_teg,img_teg)
          text_res = text_res.replaceAll(text_teg,img_teg)
          // console.log('indexOf: ',text_res.indexOf(text_teg))
        }
        // console.log('text_res: ',text_res)
        return text_res
      },
      // getTextForView_(text){       
      //   var text_res = ''
      //   var curNodeArr = this.createElementFromHTML(text).childNodes
      //   // console.log('curNodeArr: ',curNodeArr)
      //   for (let i = 0; i < curNodeArr.length; i++) {
      //     //console.log('curNode: ',curNodeArr[i])
      //     if(curNodeArr[i].nodeType == 1 && curNodeArr[i].id == "emoji"){
      //       text_res += "<img class='emoji' src='" + this.$store.getters.getUrlEmojiData + "/" + this.getEmojiImg(curNodeArr[i].innerText) + "' alt='" + curNodeArr[i].innerText + "'>"
      //     }else if(curNodeArr[i].nodeType == 1 && (curNodeArr[i].nodeName == "DIV" || curNodeArr[i].nodeName == "P" || curNodeArr[i].nodeName == "STRONG" || curNodeArr[i].nodeName == "B" || curNodeArr[i].nodeName == "EM")){
      //       const teg_start = "<"+ curNodeArr[i].nodeName.toString() +">"
      //       const teg_end = "</"+ curNodeArr[i].nodeName.toString() +">"
      //       text_res += teg_start
      //       var childNodeArr = curNodeArr[i].childNodes
      //       for (let j = 0; j < childNodeArr.length; j++) {
      //         if(childNodeArr[j].nodeType == 1 && childNodeArr[j].id == "emoji"){
      //           text_res += "<img class='emoji' src='" + this.$store.getters.getUrlEmojiData + "/" + this.getEmojiImg(childNodeArr[j].innerText) + "' alt='" + childNodeArr[j].innerText + "'>"
      //         }else if(childNodeArr[j].nodeType == 3 && childNodeArr[j].nodeName == "#text"){
      //           text_res += childNodeArr[j].nodeValue
      //         }
      //       }
      //       text_res += teg_end
      //     }else if(curNodeArr[i].nodeType == 1 && curNodeArr[i].nodeName == "BR"){
      //       text_res += curNodeArr[i].outerHTML
      //     }else if(curNodeArr[i].nodeType == 3 && curNodeArr[i].nodeName == "#text"){
      //       text_res += curNodeArr[i].nodeValue
      //     }
      //   }
      //   return text_res
      // },
      getPostPeriod(){
        this.count_posts = 0
        this.posts = []
        this.getPosts()
      },
      firstGetPost(){
        var oXmlHttpRequest = new XMLHttpRequest()
        const strRequest = this.$store.getters.getUrlTracker + '?action=get_date_last_post'
        oXmlHttpRequest.open("get",strRequest,true)
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            console.log('responseText: ', oXmlHttpRequest.responseText)
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
            vm.period_post_filter = resJsonObj.data_last_post.substr(6,4) + '-' + resJsonObj.data_last_post.substr(3,2)
            vm.getPosts()
          }
        }.bind(oXmlHttpRequest, this)
        oXmlHttpRequest.send(null)
      },
      getPosts(){
        // this.count_posts = 0
        // this.posts = []
        this.$store.dispatch('setProgressMain', true)
        const item_start = this.posts.length
        const item_end = item_start + this.posts_in_part - 1
        var oXmlHttpRequest = new XMLHttpRequest()
        // const strRequest = window.location.origin + '/posts.json'
        const strRequest = this.$store.getters.getUrlTracker + '?action=get_posts&item_start=' + item_start + '&item_end=' + item_end + '&period_post_filter=' + this.period_post_filter
        oXmlHttpRequest.open("get",strRequest,true)
        oXmlHttpRequest.onreadystatechange = function(vm){
          if(oXmlHttpRequest.readyState==4){
            // console.log('responseText: ', oXmlHttpRequest.responseText)
            var resJsonObj = JSON.parse(oXmlHttpRequest.responseText)
            vm.count_posts = resJsonObj.pagination.count_posts         
            // console.log('resJsonObj: ', resJsonObj)
            var index;
            for (index = 0; index < resJsonObj.data.length; ++index) {
              const id = resJsonObj.data[index].id;
              // const title = resJsonObj[index].title;
              const title = vm.getTextForView(resJsonObj.data[index].title)
              const date = resJsonObj.data[index].date.substr(6,4) + '-' + resJsonObj.data[index].date.substr(3,2) + '-' + resJsonObj.data[index].date.substr(0,2)
              const time = resJsonObj.data[index].date.substr(11,5)
              // const text = resJsonObj[index].text;
              const text = vm.getTextForView(resJsonObj.data[index].text)
              // vm.getTextForSave_(text)
              // vm.getTextForView_(resJsonObj.data[index].text)
              const user_name = resJsonObj.data[index].user_name
              const user_photo = resJsonObj.data[index].user_photo
              const visible = resJsonObj.data[index].visible
              const likes = resJsonObj.data[index].likes
              const liked = resJsonObj.data[index].liked
              const comments = resJsonObj.data[index].comments
              const views = resJsonObj.data[index].views
              // const files = resJsonObj[index].files
              var files = JSON.parse(JSON.stringify(resJsonObj.data[index].files));
              var files_edit = JSON.parse(JSON.stringify(resJsonObj.data[index].files));
              var images = JSON.parse(JSON.stringify(resJsonObj.data[index].images));
              var images_edit = JSON.parse(JSON.stringify(resJsonObj.data[index].images));
              vm.posts.push({'id':id, 'title':title, 'date_edit':date, 'time_edit':time, 'date':date, 'time':time, 'text':text,'files_edit':files_edit, 'files':files,'images_edit':images_edit, 'images':images, 'user_name':user_name, 'user_photo':user_photo,'visible_edit':visible,'visible':visible, 'likes':likes, 'liked':liked, 'comments':comments, 'views':views, 'comments_list': [] })
            } 
            // console.log('count_posts: ', vm.count_posts)
            // console.log('posts.length: ', vm.posts.length)
            if(vm.count_posts>vm.posts.length) vm.button_next_posts = true
            else vm.button_next_posts = false
            vm.$store.dispatch('setProgressMain', false)
            // vm.posts = resJsonObj      
          }
        }.bind(oXmlHttpRequest, this)
        oXmlHttpRequest.send(null)
      },
      onScroll(e){
        if (typeof window === 'undefined') return
        const top = window.pageYOffset ||   e.target.scrollTop || 0
        this.fab = top > 20
      },
      toTop(){
        this.$vuetify.goTo(0)
      },
    },
    beforeMount(){
      this.emojiData = this.$store.getters.getEmojiData
      // if(!this.$store.getters.getLogged)this.$router.push('/')
    },
    mounted(){
      this.firstGetPost()
      // this.getPosts()
      //console.log('emojiTable: ', this.emojiTable())
    },
  }
</script>

<style>

</style>