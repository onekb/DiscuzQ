(window.webpackJsonp=window.webpackJsonp||[]).push([[12],{719:function(t,e,o){},720:function(t,e,o){},726:function(t,e,o){"use strict";o.r(e);o(253);var n={name:"ListLoadMore",mixins:[{data:function(){return{scrollTop:0}},mounted:function(){this.addEventListener(window,"scroll",this.handleScroll)},destroyed:function(){this.removeEventListener(window,"scroll",this.handleScroll)},methods:{handleScroll:function(){var t=document.documentElement.scrollTop||document.body.scrollTop,e=document.documentElement.clientHeight||document.body.clientHeight,o=document.documentElement.scrollHeight||document.body.scrollHeight;this.scrollData(),Math.ceil(o-e)>=20&&this.scrollPost(),Math.ceil(t+e+10)>=o&&this.scrollLoadMore(),this.scrollTop=t},addEventListener:function(t,e,o,n){t.addEventListener?t.addEventListener(e,o,n):t.attachEvent&&t.attachEvent("on".concat(e),o)},removeEventListener:function(t,e,o,n){t.removeEventListener?t.removeEventListener(e,o,n):t.detachEvent&&t.detachEvent("on".concat(e),o)}}}],props:{loading:{type:Boolean,default:!1},hasMore:{type:Boolean,default:!1},pageNum:{type:[Number,String],default:1},length:{type:[Number,String],default:0},surplus:{type:[Number,String],default:0},loadMoreText:{type:String,default:function(){return this.$t("topic.showMore")}}},methods:{scrollLoadMore:function(){(this.pageNum-1)%5>0&&!this.loading&&this.hasMore&&this.loadMore()},scrollData:function(){this.$emit("scrollDdata")},scrollPost:function(){this.$emit("scrollPost")},loadMore:function(){this.$emit("loadMore")}}},r=(o(738),o(11)),component=Object(r.a)(n,(function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticClass:"load-more-container"},[t.loading?o("loading"):[t.hasMore&&(t.pageNum-1)%5==0?o("div",{staticClass:"load-more",on:{click:t.loadMore}},[t._v("\n      "+t._s(t.surplus>0?t.$t("notice.checkMore",{surplus:t.surplus}):t.loadMoreText)+"\n    ")]):t.hasMore&&(t.pageNum-1)%5>0?o("loading"):o("div",{staticClass:"no-more"},[0===t.length?o("svg-icon",{staticClass:"empty-icon",attrs:{type:"empty"}}):t._e(),t._v("\n      "+t._s(t.length>0?t.$t("discuzq.list.noMoreData"):t.$t("discuzq.list.noData"))+"\n    ")],1)]],2)}),[],!1,null,"e9521ef0",null);e.default=component.exports;installComponents(component,{Loading:o(737).default,SvgIcon:o(62).default})},735:function(t,e,o){},736:function(t,e,o){},737:function(t,e,o){"use strict";o.r(e);o(253);var n={name:"Loading",props:{loading:{type:Boolean,default:!1},fontSize:{type:Number,default:32}}},r=(o(739),o(11)),component=Object(r.a)(n,(function(){var t=this.$createElement;return(this._self._c||t)("div",{staticClass:"loading-container"},[this._v("\n  "+this._s(this.$t("discuzq.list.loading"))+"...\n")])}),[],!1,null,"00ec5480",null);e.default=component.exports},738:function(t,e,o){"use strict";o(719)},739:function(t,e,o){"use strict";o(720)},758:function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAAB0AAAAdBAMAAACkvXo8AAAAMFBMVEX0Nz9HcEz0Nz/0Nz/0Nz/0Nz/0Nz/0Nz/0Nz/0Nz/0Nz/0Nz/0Nz/0Nz/0Nz/0Nz+8Gen2AAAAD3RSTlMhAPHhCaE+0RdgkL2ugm+A/zHcAAAAy0lEQVQY02MQCVKCA+UrAgxi9v8R4KcAg4dnPZz7lVGAgWmF2H4oV7cxtYGB/3+A2Hkw184x+z8DkP//mMAiILdSZPp/CP//NolL/7eJPP4P4wPlJkiA1MD4/y0d4v8j8//r/0fl/0fhF/3//8keic+m/98gH4lfkvSZA1n+u1sBM7L+0IeCc9cj+N8FgaAJSV5JVRjFPiVVaWN9BP+zI1B9P5J81ErZVeeR3fsHVT8G/zca/389Lv/6o/AdGESRuV8FGESCENxPIQIAaU83v1QfDM0AAAAASUVORK5CYII="},759:function(t,e,o){"use strict";o.r(e);var n=o(801),r={name:"Advertising",data:function(){return{qrcode:null,siteName:""}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},watch:{forums:{handler:function(t){t&&t.set_site&&(this.siteName=t.set_site.site_name?t.set_site.site_name:"Discuz! Q")},deep:!0}},mounted:function(){this.siteName=this.forums&&this.forums.set_site&&this.forums.set_site.site_name?this.forums.set_site.site_name:"Discuz! Q",this.createQrcode(window.location.href)},destroyed:function(){this.qrcode=null},methods:{createQrcode:function(link){var t=this;this.qrcode=null,this.$nextTick((function(){t.qrcode=new n(t.$refs.qrcode,{width:70,height:70,text:link})}))}}},c=(o(783),o(11)),component=Object(c.a)(r,(function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"adv-container"},[e("div",{ref:"qrcode",staticClass:"qrcode"}),this._v(" "),e("div",{staticClass:"adv-info"},[e("div",{staticClass:"adv-title"},[this._v("扫一扫，访问移动端")]),this._v(" "),e("img",{staticClass:"adv-logo2",attrs:{src:o(758)}}),this._v(" "),e("span",{staticClass:"adv-title2"},[this._v(this._s(this.siteName))])])])}),[],!1,null,"fa151192",null);e.default=component.exports},760:function(t,e,o){"use strict";o.r(e);o(52);var n={name:"Copyright",data:function(){return{year:"2019"}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}},code:function(){return this.$store.state.site.info.attributes.set_site.site_record_code.replace(/[^0-9]+/g,"")||""}},mounted:function(){var t=window.currentTime||new Date;this.year=t.getFullYear()}},r=(o(784),o(11)),component=Object(r.a)(n,(function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticClass:"copyright"},[o("div",{staticClass:"info"},[o("span",[t._v("Powered By")]),t._v(" "),o("a",{staticClass:"site",attrs:{href:"https://discuz.com",target:"_blank"}},[t._v("Discuz! Q")]),t._v(" "),o("span",{staticClass:"block"},[t._v("© "+t._s(t.year))])]),t._v(" "),t.forums&&t.forums.set_site&&(t.forums.set_site.site_record||t.forums.set_site.site_record_code)?o("div",{staticClass:"id"},[o("div",{staticClass:"site-record-code"},[o("a",{attrs:{href:"https://beian.miit.gov.cn/",target:"_blank"}},[t._v(t._s(t.forums.set_site?t.forums.set_site.site_record:""))])]),t._v(" "),t.forums&&t.forums.set_site&&t.forums.set_site.site_record_code?o("div",{staticClass:"site-record-code"},[o("a",{attrs:{href:"http://www.beian.gov.cn/portal/registerSystemInfo?recordcode="+t.code,target:"_blank"}},[t._v(t._s(t.forums&&t.forums.set_site&&t.forums.set_site.site_record_code))])]):t._e()]):t._e()])}),[],!1,null,"0b80548e",null);e.default=component.exports},783:function(t,e,o){"use strict";o(735)},784:function(t,e,o){"use strict";o(736)},785:function(t,e,o){},804:function(t,e,o){"use strict";o.r(e);var n={name:"UserItem",props:{item:{type:Object,default:function(){}},show:{type:String,default:"all"},isFollow:{type:Boolean,default:!1}},data:function(){return{}},methods:{toUser:function(){this.$router.push("/user/".concat(this.item.id))}}},r=(o(858),o(11)),component=Object(r.a)(n,(function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{staticClass:"user-item-container",class:{simple:"simple"===t.show}},[o("div",{staticClass:"flex user-item",on:{click:function(e){return e.stopPropagation(),t.toUser(e)}}},[o("avatar",{attrs:{user:t.item,size:"simple"===t.show?40:45,round:!0}}),t._v(" "),o("div",{staticClass:"info"},[o("div",{staticClass:"flex"},[o("span",{staticClass:"name text-hidden"},[t._v(t._s(t.item.username))]),t._v(" "),"all"===t.show&&t.item.groupName?o("span",{staticClass:"role"},[t._v(t._s(t.item.groupName))]):t._e()]),t._v(" "),o("div",{staticClass:"flex count"},["all"===t.show?o("div",{staticClass:"count-item"},[t._v(t._s(t.$t("profile.topic"))+" "+t._s(t.item.threadCount||0))]):t._e(),t._v(" "),"all"===t.show?o("div",{staticClass:"count-item"},[t._v(t._s(t.$t("profile.following"))+" "+t._s(t.item.followCount||0))]):t._e(),t._v(" "),o("div",{staticClass:"count-item"},[t._v(t._s(t.$t("profile.followers"))+" "+t._s(t.item.fansCount||0))])])])],1),t._v(" "),t.isFollow?o("el-button",{staticClass:"follow",attrs:{type:"text"}},[t._v("\n    "+t._s(t.$t("profile.following"))+"\n  ")]):t._e()],1)}),[],!1,null,"ee325c6e",null);e.default=component.exports;installComponents(component,{Avatar:o(254).default})},858:function(t,e,o){"use strict";o(785)}}]);