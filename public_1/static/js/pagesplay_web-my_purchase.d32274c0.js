(window.webpackJsonp=window.webpackJsonp||[]).push([["pagesplay_web-my_purchase"],{1858:function(t,e,n){"use strict";n.r(e);var i=n("3c2a"),a=n.n(i);for(var r in i)["default"].indexOf(r)<0&&function(t){n.d(e,t,(function(){return i[t]}))}(r);e.default=a.a},"185d":function(t,e,n){t.exports=n.p+"static/img/auth.51e40f27.svg"},1906:function(t,e,n){(e=n("24fb")(!1)).push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */\n/* eg:\n  .container {\n    color: --color(BG-1);\n  }\n*/.scroll-y[data-v-1a969844]{max-height:100vh}.favorite[data-v-1a969844] .favorite-head{padding-top:%?20?%;padding-left:%?40?%;margin-bottom:%?30?%;background:var(--qui-BG-2);border-bottom:%?2?% solid var(--qui-BOR-ED)}.favorite[data-v-1a969844] .cell-item__body{height:%?78?%}.favorite[data-v-1a969844] .themeCount .addFine{display:none}.favorite[data-v-1a969844] .themeCount .addAsk{display:none}.favorite[data-v-1a969844] .themeCount .icon-delete{position:absolute;top:%?35?%;right:%?40?%}.favorite[data-v-1a969844] /deep/ .themeItem{margin:0;border:none;border-radius:0}.favorite[data-v-1a969844] /deep/ .themeItem__header__title__essence{top:%?18?%;right:0}.favorite[data-v-1a969844] .favorite-content__item{position:relative}.favorite[data-v-1a969844] .icon-delete{position:absolute;top:%?55?%;right:%?81?%}',""]),t.exports=e},"1b03":function(t,e,n){"use strict";n.r(e);var i=n("99a1"),a=n("1858");for(var r in a)["default"].indexOf(r)<0&&function(t){n.d(e,t,(function(){return a[t]}))}(r);n("c81f");var s=n("f0c5"),o=Object(s.a)(a.default,i.b,i.c,!1,null,"1a969844",null,!1,i.a,void 0);e.default=o.exports},"1cb5":function(t,e,n){t.exports=n.p+"static/img/yihuida.894c0306.svg"},"245f":function(t,e,n){"use strict";(function(e){var i=n("4ea4").default,a=i(n("6f74")),r=n("b95e"),s=i(n("4c82"));t.exports={mixins:[a.default,s.default],methods:{getForum:function(){var t=this;this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(e){e&&(t.forum=e)}))},jump2PhoneLoginPage:function(){uni.redirectTo({url:"/pages/user/phone-login"})},jump2PhoneLoginRegisterPage:function(){uni.redirectTo({url:"/pages/user/phone-login-register"})},jump2LoginPage:function(){uni.navigateTo({url:"/pages/user/login"})},jump2RegisterPage:function(){uni.redirectTo({url:"/pages/user/register"})},jump2RegisterExtendPage:function(){uni.redirectTo({url:"/pages/user/supple-mentary"})},jump2LoginBindPage:function(){uni.redirectTo({url:"/pages/user/login-bind"})},jump2RegisterBindPage:function(){uni.redirectTo({url:"/pages/user/register-bind"})},jump2LoginBindPhonePage:function(){uni.redirectTo({url:"/pages/user/login-bind-phone"})},jump2RegisterBindPhonePage:function(){uni.redirectTo({url:"/pages/user/register-bind-phone"})},jump2findpwdPage:function(){uni.navigateTo({url:"/pages/modify/findpwd?pas=reset_pwd"})},mpLoginMode:function(){this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&this.jump2LoginPage(),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open())},h5LoginMode:function(){s.default.isWeixin().isWeixin?(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),this.$store.dispatch("session/wxh5Login"))):(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}))},refreshmpParams:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:function(){},n=this;uni.login({success:function(e){if("login:ok"===e.errMsg){var i={data:{attributes:{js_code:e.code}}};n.$store.dispatch("session/setParams",i),t()}},fail:function(t){e.log(t)}})},mpLogin:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;uni.setStorageSync("register",t),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open()},wxh5Login:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;uni.setStorageSync("register",t),uni.setStorageSync("rebind",e),uni.setStorageSync("h5_wechat_login",1),this.$store.dispatch("session/wxh5Login")},getLoginParams:function(t,e){var n=t;if(""===t.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===t.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{var i=uni.getStorageSync("token");""!==i&&(n.data.attributes.token=i),this.login(n,e)}},getLoginBindParams:function(t,e){var n=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;this.refreshmpParams();var i=t;if(""===t.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===t.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{1===n&&(i.data.attributes.rebind=1);var a=uni.getStorageSync("token");""!==a&&(i.data.attributes.token=a),this.login(i,e)}},login:function(t,n){var i=this;this.$store.dispatch("session/h5Login",t).then((function(t){if(t&&t.data&&t.data.data&&t.data.data.id&&(i.logind(),i.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(t){t&&t.set_site&&t.set_site.site_mode!==r.SITE_PAY&&uni.getStorage({key:"page",success:function(t){uni.redirectTo({url:t.data})}}),t&&t.set_site&&t.set_site.site_mode===r.SITE_PAY&&i.user&&!i.user.paid&&uni.redirectTo({url:"/pages/site/info"})})),uni.showToast({title:n,duration:2e3})),t&&t.data&&t.data.errors){if("401"===t.data.errors[0].status||"402"===t.data.errors[0].status||"500"===t.data.errors[0].status){var e=i.i18n.t("core.".concat(t.data.errors[0].code));uni.showToast({icon:"none",title:e,duration:2e3})}if("403"===t.data.errors[0].status||"422"===t.data.errors[0].status){var a=i.i18n.t("core.".concat(t.data.errors[0].code))||i.i18n.t(t.data.errors[0].detail[0]);uni.showToast({icon:"none",title:a,duration:2e3})}}})).catch((function(t){return e.log(t)}))}}}}).call(this,n("5a52").default)},"35ab":function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=function(t){for(var e=0,n=0;n<t.length;n++)e+=t.charCodeAt(n);var i=function(t,e,n){var i,a,r,s=Math.floor(6*t),o=6*t-s,u=n*(1-e),c=n*(1-o*e),d=n*(1-(1-o)*e);switch(s%6){case 0:i=n,a=d,r=u;break;case 1:i=c,a=n,r=u;break;case 2:i=u,a=n,r=d;break;case 3:i=u,a=c,r=n;break;case 4:i=d,a=u,r=n;break;case 5:i=n,a=u,r=c}return{r:Math.floor(255*i),g:Math.floor(255*a),b:Math.floor(255*r)}}(e%360/360,.3,.9);return""+i.r.toString(16)+i.g.toString(16)+i.b.toString(16)},n("d3b7"),n("25f0")},"368d":function(t,e,n){t.exports=n.p+"static/img/msg-warning.f35ce51f.svg"},"3c2a":function(t,e,n){"use strict";(function(t){var i=n("4ea4").default;Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,n("99af"),n("4160"),n("159b"),n("a434");var a=i(n("2909")),r=i(n("b469")),s={components:{uniPopupDialog:i(n("840a")).default},mixins:r.default,props:{userId:{type:String,default:""}},data:function(){return{loadingType:"",data:[],query:{},totalData:0,pageSize:20,pageNum:1,currentItem:"",editThreadId:"",nowThreadId:""}},mounted:function(){this.loadlikes()},onLoad:function(){},onShow:function(){this.uploadItem()},onShareAppMessage:function(t){if("button"===t.from){var e=this.$store.getters["jv/get"]("/threads/".concat(this.nowThreadId));return{title:1===e.type?e.title:e.firstPost.summaryText,path:"/topic/index?id=".concat(this.nowThreadId)}}},onShareTimeline:function(){return{title:this.forums.set_site.site_name,query:""}},methods:{toTopic:function(t){this.editThreadId=t},handleClickShare:function(t){this.nowThreadId=t},loadlikes:function(){var e=this;this.loadingType="loading";var n={"page[number]":this.pageNum,"page[size]":this.pageSize};this.$store.dispatch("jv/get",["threads/paid",{abc:n}]).then((function(n){n._jv&&delete n._jv,e.loadingType=n.length===e.pageSize?"more":"nomore",e.data=[].concat((0,a.default)(e.data),(0,a.default)(n)),t.log(e.data);var i=0;e.data.forEach((function(t){t.isDeleted||(i+=1)})),e.totalData=i}))},handleCancel:function(){this.$refs.popTips.close()},handleOk:function(){var t=this;this.$refs.popTips.close();var e=this.currentItem,n={_jv:{type:"threads",id:e.id},isFavorite:!0!==e.isFavorite};this.$store.dispatch("jv/patch",n).then((function(){t.totalData-=1,t.data.splice(e.index,1)}))},itemDelete:function(t,e,n){var i={id:t,isFavorite:e,index:n};this.currentItem=i,this.$refs.popTips.open()},pullDown:function(){"more"===this.loadingType&&(this.pageNum+=1,this.loadlikes())},uploadItem:function(){var t=this;if(this.editThreadId){var e=this.$store.getters["jv/get"]("threads/".concat(this.editThreadId));this.data.forEach((function(n,i){n._jv.id===t.editThreadId&&(t.editThreadId="",t.$set(t.data,i,e))}))}}}};e.default=s}).call(this,n("5a52").default)},"5b5f":function(t,e,n){"use strict";var i=n("4ea4").default;n("fb6a"),n("99af"),n("ac1f"),n("5319"),n("d3b7"),n("25f0"),n("1276");var a=i(n("53ca")),r=n("b95e"),s=n("ce40");t.exports={methods:{wxShare:function(t){var e=this.getUrl(),n=this.$store.getters["jv/get"]("forums/1");n.passport&&!n.passport.offiaccount_close||this.$store.dispatch("jv/get",["offiaccount/jssdk?url=".concat(encodeURIComponent(e)),{}]).then((function(i){var a=i.appId,r=i.nonceStr,o=i.signature,u=i.timestamp;s.config({debug:!1,appId:a,timestamp:u,nonceStr:r,signature:o,jsApiList:["updateAppMessageShareData","updateTimelineShareData","hideMenuItems","showMenuItems"]}),s.ready((function(){var i={title:t.title||"Discuz!Q",desc:t.desc||n.set_site.site_introduction,link:e,imgUrl:t.logo||n.set_site.site_favicon};s.updateAppMessageShareData(i),s.updateTimelineShareData(i)}))}))},h5Share:function(t){var e="";switch((0,a.default)(t)){case"undefined":e="Discuz!Q";break;case"string":e=t;break;default:e=t.title||"Discuz!Q"}var n=t.id?"?id=".concat(t.id):"",i="";if("pages/home/index"===t.url)i="".concat(r.DISCUZ_REQUEST_HOST);else{var s=t.url;t.url&&/^\/.*/.test(t.url)&&(s=t.url.slice(1)),i="".concat(r.DISCUZ_REQUEST_HOST).concat(s).concat(n)}var o=document.createElement("input");e=(e=(e=(e=e.toString().replace(/<img(?:.|\s)*?>/g,"")).toString().replace(/(<\/?br.*?>)/gi,"")).toString().replace(/(<\/?p.*?>)/gi,"")).toString().replace(/\s+/g,""),e="".concat(e.substring(0,17)),o.value="".concat(e,"  ").concat(i),document.body.appendChild(o),o.select(),o.readOnly=!0,o.id="copyInp",document.execCommand("Copy"),o.setAttribute("onfocus",this.copyFocus(o)),o.className="oInput",o.style.display="none",uni.showToast({icon:"none",title:"分享链接已复制成功"})},copyFocus:function(t){t.blur(),document.body.removeChild(t)},getUrl:function(){var t=/iPad|iPhone|iPod/.test(navigator.userAgent),e=window.location.href.split("#")[0];return t&&window.entryUrl&&!/wechatdevtools/.test(navigator.userAgent)&&(e=window.entryUrl),e}}}},"5df8":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAqCAMAAADs1AnaAAAARVBMVEVHcEybBAT/UFD6UFD6UVH6UFD/UFD6UVH7UFCYAAD6UFD6UVH9oaH/////5ub7ZWX7ior/8PD+19f7e3v9tbX8kpL+yMjhHSfzAAAAC3RSTlMAjiCg630QwED2YJ4QxbcAAAEbSURBVDjLzdPbkoMgDAZgWrXaJSEcAu//qJugbuuI2rvuf5Fx9NNkAI2R3Oxpxmqe1+j2vEZiLpGaD9oZM30JQVouUjlGDpaLjAeIWVEAJwmo1dEO5SDII2VJQa15/yXC5KAUm7z3BaV4aswU2QF6y0GCWqCBSNrRxeAZIBSAHCUOtUa/QyVGdDEmeIUa6+RRpyC/pjW4jajvch1ah08N5OWprB87uy5JawmKg+iIsdTEFkpIsgSOOcxTcwvFonun7agmtVCg+RQwrmm1W47K6ZfsH6pbZAHpHHntFtsnk3WzvEg9VOl//FLfQON07/pz1E165zH0x6i7myVvbIteZMPeUD9sSM1Pv0H98DCtKFvQEakPxxkdi5r7L8zHODncoK0UAAAAAElFTkSuQmCC"},"6f74":function(t,e,n){"use strict";var i=n("b95e");t.exports={computed:{user:function(){var t=this.$store.getters["session/get"]("userId");return t?this.$store.getters["jv/get"]("users/".concat(t)):{}}},methods:{getUserInfo:function(){var t=arguments.length>0&&void 0!==arguments[0]&&arguments[0],e=(new Date).getTime(),n=uni.getStorageSync(i.STORGE_GET_USER_TIME);if(t||(e-n)/1e3>60){var a={include:"groups,wechat"},r=this.$store.getters["session/get"]("userId");this.$store.commit("jv/deleteRecord",{_jv:{type:"users",id:r}}),this.$store.dispatch("jv/get",["users/".concat(r),{params:a}]).then((function(){return uni.$emit("updateNotiNum")})),uni.setStorageSync(i.STORGE_GET_USER_TIME,(new Date).getTime())}},logind:function(){var t=this,e=this.$store.getters["session/get"]("userId");if(e){this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]);this.$store.dispatch("jv/get",["users/".concat(e),{params:{include:"groups,wechat"}}]).then((function(e){t.$u.event.$emit("logind",e)})),this.$store.dispatch("forum/setError",{loading:!1})}}}}},"864e":function(t,e,n){"use strict";var i=n("4ea4").default;Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,n("ac1f"),n("5319"),n("99af");var a=i(n("9558")),r={topic:function(t){var e=(0,a.default)(/<[s\u017F]pan[\t-\r \xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000\uFEFF]*id="topic"[\t-\r \xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000\uFEFF]*value="([0-9A-Z_a-z\u017F\u212A]+)"[\t-\r \xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000\uFEFF]*>((?:(?!<)[\s\S])+)<\/[s\u017F]pan>/gim,{value:1,string:2});return t&&t.replace(e,(function(t){return t.replace(e,(function(t,e,n){var i="/pages/topic/content?id=".concat(e);return'<a href="'.concat(i,'" class="content-topic">').concat(n,"</a> ")}))}))},usermention:function(t){var e=(0,a.default)(/<[s\u017F]pan[\t-\r \xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000\uFEFF]*id="member"[\t-\r \xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000\uFEFF]*value="([0-9A-Z_a-z\u017F\u212A]+)"[\t-\r \xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000\uFEFF]*>((?:(?!<)[\s\S])+)<\/[s\u017F]pan>/gim,{value:1,string:2});return t&&t.replace(e,(function(t){return t.replace(e,(function(t,e,n){var i="/pages/profile/index?userId=".concat(e);return'<a href="'.concat(i,'" class="content-member">').concat(n,"</a> ")}))}))},attachment:function(t,e){return t&&t.replace(/\[attach\](.*?)\[\/attach\]/g,(function(t,n){var i=e.$store.getters["jv/get"]("attachments/".concat(n));return i.url&&(t='<a href="'.concat(i.url,'" class="content-attachment">').concat(i.attachment,"</a>")),t}))}};var s={parse:function(t,e){if(t){for(var n in r)t=r[n](t,e);return t}}};e.default=s},"99a1":function(t,e,n){"use strict";var i=n("d13d");n.d(e,"a",(function(){return i.a})),n.d(e,"b",(function(){return i.b})),n.d(e,"c",(function(){return i.c}))},b31b:function(t,e,n){var i=n("1906");"string"==typeof i&&(i=[[t.i,i,""]]),i.locals&&(t.exports=i.locals);(0,n("4f06").default)("72d80db8",i,!0,{sourceMap:!1,shadowMode:!1})},b469:function(t,e){t.exports={computed:{forums:function(){return this.$store.getters["jv/get"]("forums/1")}}}},c81f:function(t,e,n){"use strict";var i=n("b31b");n.n(i).a},cadb:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.timestamp2day=e.time2DateAndHM=e.time2MinuteOrHour=void 0,n("e25e"),n("ac1f"),n("5319"),n("99af");e.time2MinuteOrHour=function(t){var e=new Date-new Date(t);return parseInt(parseInt(e/1e3,0)/60,0)<60?"".concat(Math.ceil(e/1e3/60),"分钟前"):parseInt(parseInt(parseInt(e/1e3,0)/60,0)/60,0)<16?"".concat(Math.ceil(e/1e3/60/60),"小时前"):t.replace(/T/," ").replace(/Z/,"").substring(0,16)};e.time2DateAndHM=function(t){var e=t.replace(/T/," ").replace(/Z/,"");return"".concat(e.substring(0,10)," ").concat(e.substring(11,16))};e.timestamp2day=function(t){var e=t-Math.round(new Date/1e3);return parseInt(e/86400,0)}},d13d:function(t,e,n){"use strict";(function(t){var i;n.d(e,"b",(function(){return a})),n.d(e,"c",(function(){return r})),n.d(e,"a",(function(){return i}));try{i={quiPage:n("29c4").default,quiCellItem:n("e0ca").default,quiThreadItem:n("369a").default,quiLoadMore:n("51e5").default,uniPopup:n("1c89").default}}catch(e){if(-1===e.message.indexOf("Cannot find module")||-1===e.message.indexOf(".vue"))throw e;t.error(e.message),t.error("1. 排查组件名称拼写是否正确"),t.error("2. 排查组件是否符合 easycom 规范，文档：https://uniapp.dcloud.net.cn/collocation/pages?id=easycom"),t.error("3. 若组件不符合 easycom 规范，需手动引入，并在 components 中注册该组件")}var a=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("qui-page",{staticClass:"favorite",attrs:{"data-qui-theme":t.theme}},[n("v-uni-scroll-view",{staticClass:"scroll-y",attrs:{"scroll-y":"true","scroll-with-animation":"true","show-scrollbar":"false"},on:{scrolltolower:function(e){arguments[0]=e=t.$handleEvent(e),t.pullDown.apply(void 0,arguments)}}},[n("v-uni-view",{staticClass:"favorite-head"},[n("qui-cell-item",{attrs:{title:""+t.totalData+t.i18n.t("profile.item")+"购买",border:!1}})],1),n("v-uni-view",{staticClass:"favorite-content"},[t._l(t.data,(function(e,i){return n("v-uni-view",{key:i,staticClass:"favorite-content__item"},[n("qui-thread-item",{attrs:{currentindex:i,thread:e},on:{toTopic:function(e){arguments[0]=e=t.$handleEvent(e),t.toTopic.apply(void 0,arguments)},handleClickShare:function(e){arguments[0]=e=t.$handleEvent(e),t.handleClickShare.apply(void 0,arguments)}}})],1)})),t.loadingType?n("qui-load-more",{attrs:{status:t.loadingType,"show-icon":!1}}):t._e()],2)],1),n("uni-popup",{ref:"popTips",attrs:{type:"center"}},[n("uni-popup-dialog",{attrs:{type:"warn","before-close":!0,content:t.i18n.t("core.deleteFavoriteSure")},on:{close:function(e){arguments[0]=e=t.$handleEvent(e),t.handleCancel.apply(void 0,arguments)},confirm:function(e){arguments[0]=e=t.$handleEvent(e),t.handleOk.apply(void 0,arguments)}}})],1)],1)},r=[]}).call(this,n("5a52").default)},e972:function(t,e,n){t.exports=n.p+"static/img/msg-404.3ba2611f.svg"}}]);