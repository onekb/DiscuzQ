(window.webpackJsonp=window.webpackJsonp||[]).push([["pages-my-walletlist"],{"048d":function(e,t,i){var a=i("3fd1");"string"==typeof a&&(a=[[e.i,a,""]]),a.locals&&(e.exports=a.locals);(0,i("4f06").default)("30ce218d",a,!0,{sourceMap:!1,shadowMode:!1})},"0b45":function(e,t,i){"use strict";var a=i("3345");i.d(t,"a",(function(){return a.a})),i.d(t,"b",(function(){return a.b})),i.d(t,"c",(function(){return a.c}))},"245f":function(e,t,i){"use strict";(function(t){var a=i("4ea4"),r=a(i("6f74")),s=i("b95e"),n=a(i("4c82"));e.exports={mixins:[r.default,n.default],methods:{getForum:function(){var e=this;this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(t){t&&(e.forum=t)}))},jump2PhoneLoginPage:function(){uni.redirectTo({url:"/pages/user/phone-login"})},jump2PhoneLoginRegisterPage:function(){uni.redirectTo({url:"/pages/user/phone-login-register"})},jump2LoginPage:function(){uni.redirectTo({url:"/pages/user/login"})},jump2RegisterPage:function(){uni.redirectTo({url:"/pages/user/register"})},jump2RegisterExtendPage:function(){uni.redirectTo({url:"/pages/user/supple-mentary"})},jump2LoginBindPage:function(){uni.redirectTo({url:"/pages/user/login-bind"})},jump2RegisterBindPage:function(){uni.redirectTo({url:"/pages/user/register-bind"})},jump2LoginBindPhonePage:function(){uni.redirectTo({url:"/pages/user/login-bind-phone"})},jump2RegisterBindPhonePage:function(){uni.redirectTo({url:"/pages/user/register-bind-phone"})},jump2findpwdPage:function(){uni.navigateTo({url:"/pages/modify/findpwd?pas=reset_pwd"})},mpLoginMode:function(){this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&this.jump2LoginPage(),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open())},h5LoginMode:function(){n.default.isWeixin().isWeixin?(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),this.$store.dispatch("session/wxh5Login"))):(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}))},refreshmpParams:function(){var e=this;uni.login({success:function(i){if("login:ok"===i.errMsg){var a=i.code;uni.getUserInfo({success:function(t){var i={data:{attributes:{js_code:a,iv:t.iv,encryptedData:t.encryptedData}}};e.$store.dispatch("session/setParams",i)},fail:function(e){t.log(e)}})}},fail:function(e){t.log(e)}})},mpLogin:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;uni.setStorageSync("register",e),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open()},wxh5Login:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;uni.setStorageSync("register",e),uni.setStorageSync("rebind",t),uni.setStorageSync("h5_wechat_login",1),this.$store.dispatch("session/wxh5Login")},getLoginParams:function(e,t){var i=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{var a=uni.getStorageSync("token");""!==a&&(i.data.attributes.token=a),this.login(i,t)}},getLoginBindParams:function(e,t){var i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;this.refreshmpParams();var a=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{1===i&&(a.data.attributes.rebind=1);var r=uni.getStorageSync("token");""!==r&&(a.data.attributes.token=r),this.login(a,t)}},login:function(e,i){var a=this;this.$store.dispatch("session/h5Login",e).then((function(e){if(e&&e.data&&e.data.data&&e.data.data.id&&(a.logind(),a.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(e){e&&e.set_site&&e.set_site.site_mode!==s.SITE_PAY&&uni.getStorage({key:"page",success:function(e){t.log("resData",e),uni.redirectTo({url:e.data})}}),e&&e.set_site&&e.set_site.site_mode===s.SITE_PAY&&a.user&&!a.user.paid&&uni.redirectTo({url:"/pages/site/info"})})),uni.showToast({title:i,duration:2e3})),e&&e.data&&e.data.errors){if("401"===e.data.errors[0].status||"402"===e.data.errors[0].status||"500"===e.data.errors[0].status){var r=a.i18n.t("core.".concat(e.data.errors[0].code));uni.showToast({icon:"none",title:r,duration:2e3})}if("403"===e.data.errors[0].status||"422"===e.data.errors[0].status){var n=a.i18n.t("core.".concat(e.data.errors[0].code))||a.i18n.t(e.data.errors[0].detail[0]);uni.showToast({icon:"none",title:n,duration:2e3})}}})).catch((function(e){return t.log(e)}))}}}}).call(this,i("5a52").default)},"24a8":function(e,t,i){"use strict";i.r(t);var a=i("0b45"),r=i("84fa");for(var s in r)["default"].indexOf(s)<0&&function(e){i.d(t,e,(function(){return r[e]}))}(s);i("c3f5");var n=i("f0c5"),o=Object(n.a)(r.default,a.b,a.c,!1,null,"5b35c84f",null,!1,a.a,void 0);t.default=o.exports},3345:function(e,t,i){"use strict";(function(e){var a;i.d(t,"b",(function(){return r})),i.d(t,"c",(function(){return s})),i.d(t,"a",(function(){return a}));try{a={quiPage:i("29c4").default,quiCellItem:i("e0ca").default,quiIcon:i("895d").default,quiFilterModal:i("1c23").default,quiLoadMore:i("51e5").default}}catch(t){if(-1===t.message.indexOf("Cannot find module")||-1===t.message.indexOf(".vue"))throw t;e.error(t.message),e.error("1. 排查组件名称拼写是否正确"),e.error("2. 排查组件是否符合 easycom 规范，文档：https://uniapp.dcloud.net.cn/collocation/pages?id=easycom"),e.error("3. 若组件不符合 easycom 规范，需手动引入，并在 components 中注册该组件")}var r=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("qui-page",{staticClass:"walletlist",attrs:{"data-qui-theme":e.theme}},[i("v-uni-view",{staticClass:"walletlist-head"},[i("qui-cell-item",{attrs:{"slot-right":!0,border:!1}},[i("v-uni-view",{on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.showFilter.apply(void 0,arguments)}}},[i("v-uni-text",[e._v(e._s(e.i18n.t("profile.status")+" ："+e.filterSelected.label))]),i("qui-icon",{staticClass:"text",attrs:{name:"icon-screen",size:"32",color:"#777"}}),i("qui-filter-modal",{ref:"filter",attrs:{"filter-list":e.filterList,"if-need-confirm":!1},on:{confirm:function(t){arguments[0]=t=e.$handleEvent(t),e.confirm.apply(void 0,arguments)},change:function(t){arguments[0]=t=e.$handleEvent(t),e.changeType.apply(void 0,arguments)}},model:{value:e.show,callback:function(t){e.show=t},expression:"show"}})],1)],1),i("v-uni-picker",{staticClass:"date-picker",attrs:{mode:"date",value:e.date,fields:"month"},on:{change:function(t){arguments[0]=t=e.$handleEvent(t),e.bindDateChange.apply(void 0,arguments)}}},[i("v-uni-view",{staticClass:"uni-input"},[e._v(e._s(e.i18n.t("profile.time")+"："+e.date))])],1)],1),i("v-uni-scroll-view",{staticClass:"scroll-y",attrs:{"scroll-y":"true","scroll-with-animation":"true","show-scrollbar":"false"},on:{scrolltolower:function(t){arguments[0]=t=e.$handleEvent(t),e.pullDown.apply(void 0,arguments)}}},[e.dataList.length>0?i("v-uni-view",{staticClass:"walletlist-items"},e._l(e.dataList,(function(t,a){return i("qui-cell-item",{key:a,attrs:{title:t.change_desc,brief:e.timeHandle(t.created_at),addon:t.change_available_amount>=0?"+￥"+t.change_available_amount:"-￥"+t.change_available_amount.substr(1),"class-item":t.change_available_amount>0?"fail":"success",border:a!=e.dataList.length-1},on:{click:function(i){arguments[0]=i=e.$handleEvent(i),e.toTopic(t)}}})})),1):e._e(),e.loadingType?i("qui-load-more",{attrs:{status:e.loadingType,"show-icon":!1}}):e._e()],1)],1)},s=[]}).call(this,i("5a52").default)},"368d":function(e,t,i){e.exports=i.p+"static/img/msg-warning.f35ce51f.svg"},"3fd1":function(e,t,i){(t=i("24fb")(!1)).push([e.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */\n/* eg:\n  .container {\n    color: --color(BG-1);\n  }\n*/.walletlist[data-v-5b35c84f] {min-height:100vh}.walletlist[data-v-5b35c84f] .cell-item{padding-right:%?40?%}.walletlist[data-v-5b35c84f] .cell-item__body{height:auto;padding:%?35?% 0}.walletlist[data-v-5b35c84f] .walletlist-items /deep/ .cell-item__body{-webkit-box-align:start;-webkit-align-items:flex-start;align-items:flex-start}.walletlist[data-v-5b35c84f] .cell-item__body__right-text{font-weight:700}.walletlist[data-v-5b35c84f] .icon-screen{margin-left:%?20?%}.walletlist[data-v-5b35c84f] .walletlist-head{position:relative;padding:%?40?% 0 0 %?40?%;margin-bottom:%?30?%;background:var(--qui-BG-2);border-bottom:%?2?% solid var(--qui-BOR-ED)}.walletlist[data-v-5b35c84f] .walletlist-head .cell-item__body{height:%?78?%}.walletlist[data-v-5b35c84f] .cell-item.fail .cell-item__body__right-text{color:var(--qui-RED)}.walletlist[data-v-5b35c84f] .cell-item.success .cell-item__body__right-text{color:var(--qui-GREEN)}.walletlist[data-v-5b35c84f] .walletlist-items{padding-left:%?40?%;margin-bottom:%?30?%;background:var(--qui-BG-2);border-bottom:%?2?% solid var(--qui-BOR-ED)}.date-picker[data-v-5b35c84f]{position:absolute;top:%?40?%;left:%?40?%;z-index:10;width:50%;height:%?78?%}.date-picker .uni-input[data-v-5b35c84f]{width:100%;height:%?78?%;font-size:%?30?%;line-height:%?78?%}.scroll-y[data-v-5b35c84f]{max-height:calc(100vh - %?190?%)}.cell-item__body__right[data-v-5b35c84f]{padding-left:%?59?%}',""]),e.exports=t},"6f01":function(e,t,i){"use strict";var a=i("4ea4");i("99af"),i("4de4"),i("4160"),i("ac1f"),i("5319"),i("1276"),i("159b"),Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var r=a(i("2909")),s=a(i("5530")),n=i("4b36"),o=i("cadb"),l={data:function(){var e=new Date,t=e.getFullYear(),i=e.getMonth()+1;i=i<10?"0".concat(i):i;var a="".concat(t,"-").concat(i);return{loadingType:"",pageSize:20,pageNum:1,show:!1,userId:this.$store.getters["session/get"]("userId"),date:a,filterSelected:{label:this.i18n.t("profile.all"),value:""},dataList:[],filterList:[{title:this.i18n.t("profile.type"),data:[{label:this.i18n.t("profile.all"),value:"",selected:!0},{label:this.i18n.t("profile.withdrawalfreeze"),value:10},{label:this.i18n.t("profile.withdrawalsucceed"),value:11},{label:this.i18n.t("profile.withdrawalunfreeze"),value:12},{label:this.i18n.t("profile.registeredincome"),value:30},{label:this.i18n.t("profile.rewardincome"),value:31},{label:this.i18n.t("profile.laborincome"),value:32},{label:this.i18n.t("profile.laborexpenditure"),value:50},{label:this.i18n.t("profile.payincome"),value:[60,62,63]},{label:this.i18n.t("profile.answerincome"),value:[35,36]},{label:this.i18n.t("profile.answerpay"),value:[81,82]},{label:this.i18n.t("profile.redpacketpay"),value:[100,101,102,103,104]},{label:this.i18n.t("profile.longredpacketpay"),value:[110,111,112,113,114]},{label:this.i18n.t("profile.offerewardpay"),value:[120,121,122,123,124]}]}]}},onLoad:function(){this.getList()},methods:{confirm:function(e){this.filterSelected=(0,s.default)({},e[0].data),this.getList("filter")},changeType:function(e){this.filterList=e},showFilter:function(){this.show=!0,this.$refs.filter.setData()},bindDateChange:function(e){this.date=e.target.value,this.getList("filter")},timeHandle:function(e){return(0,o.time2MinuteOrHour)(e)},getList:function(e){var t=this;this.loadingType="loading";var i=this.date.split("-"),a=new Date(i[0],i[1],0).getDate(),s={include:["user","order.user","order.thread","order.thread.firstPost"],"filter[user]":this.userId,"page[number]":this.pageNum,"page[limit]":this.pageSize,"filter[change_type_exclude]":[11,81],"filter[start_time]":"".concat(this.date,"-01-00-00-00"),"filter[end_time]":"".concat(this.date,"-").concat(a,"-23-59-59")};e&&"filter"===e&&(s.pageNum=1,this.dataList=[]),this.filterSelected.value&&(s["filter[change_type]"]=this.filterSelected.value),n.status.run((function(){return t.$store.dispatch("jv/get",["wallet/log",{params:s}])})).then((function(e){e._jv&&delete e._jv,e.forEach((function(i,a){var r=t.handleTitle(i);r.length>42&&(r="".concat(r.substr(0,42),"...")),e[a].change_desc=r})),t.loadingType=e.length===t.pageSize?"more":"nomore",t.dataList=[].concat((0,r.default)(t.dataList),(0,r.default)(e))}))},handleTitle:function(e){switch(e.change_type){case 31:var t=e.order.user?e.order.user.username:this.i18n.t("profile.theuserwasdeleted"),i=e.order.thread?e.order.thread.title.replace(/(<([^>]+)>)/gi,""):this.i18n.t("profile.thethemewasdeleted");return"".concat(t," ").concat(this.i18n.t("profile.givearewardforyourtheme")," ").concat(i);case 41:var a=e.order.thread?e.order.thread.title.replace(/(<([^>]+)>)/gi,""):this.i18n.t("profile.thethemewasdeleted");return"".concat(this.i18n.t("profile.givearewardforthetheme")," ").concat(a);case 60:var r=e.order.user?e.order.user.username:this.i18n.t("profile.theuserwasdeleted"),s=e.order.thread?e.order.thread.title.replace(/(<([^>]+)>)/gi,""):this.i18n.t("profile.givearewardforthetheme");return"".concat(r," ").concat(this.i18n.t("profile.paidtoseeyourtheme")," ").concat(s);case 61:var n=e.order.thread?e.order.thread.title.replace(/(<([^>]+)>)/gi,""):this.i18n.t("profile.thethemewasdeleted");return"".concat(this.i18n.t("profile.paidtoview")," ").concat(n);default:return e.change_desc}},toTopic:function(e){e.order&&e.order.thread&&uni.navigateTo({url:"/topic/index?id=".concat(e.order.thread._jv.id)})},pullDown:function(){"more"===this.loadingType&&(this.pageNum+=1,this.getList())}}};t.default=l},"6f74":function(e,t,i){"use strict";var a=i("b95e");e.exports={computed:{user:function(){var e=this.$store.getters["session/get"]("userId");return e?this.$store.getters["jv/get"]("users/".concat(e)):{}}},methods:{getUserInfo:function(){var e=arguments.length>0&&void 0!==arguments[0]&&arguments[0],t=(new Date).getTime(),i=uni.getStorageSync(a.STORGE_GET_USER_TIME);if(e||(t-i)/1e3>60){var r={include:"groups,wechat"},s=this.$store.getters["session/get"]("userId");this.$store.commit("jv/deleteRecord",{_jv:{type:"users",id:s}}),this.$store.dispatch("jv/get",["users/".concat(s),{params:r}]).then((function(){return uni.$emit("updateNotiNum")})),uni.setStorageSync(a.STORGE_GET_USER_TIME,(new Date).getTime())}},logind:function(){var e=this,t=this.$store.getters["session/get"]("userId");if(t){this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]);this.$store.dispatch("jv/get",["users/".concat(t),{params:{include:"groups,wechat"}}]).then((function(t){e.$u.event.$emit("logind",t)})),this.$store.dispatch("forum/setError",{loading:!1})}}}}},"84fa":function(e,t,i){"use strict";i.r(t);var a=i("6f01"),r=i.n(a);for(var s in a)["default"].indexOf(s)<0&&function(e){i.d(t,e,(function(){return a[e]}))}(s);t.default=r.a},b469:function(e,t){e.exports={computed:{forums:function(){return this.$store.getters["jv/get"]("forums/1")}}}},c3f5:function(e,t,i){"use strict";var a=i("048d");i.n(a).a},cadb:function(e,t,i){"use strict";i("99af"),i("e25e"),i("ac1f"),i("5319"),Object.defineProperty(t,"__esModule",{value:!0}),t.timestamp2day=t.time2DateAndHM=t.time2MinuteOrHour=void 0;t.time2MinuteOrHour=function(e){var t=new Date-new Date(e);return parseInt(parseInt(t/1e3,0)/60,0)<60?"".concat(Math.ceil(t/1e3/60),"分钟前"):parseInt(parseInt(parseInt(t/1e3,0)/60,0)/60,0)<16?"".concat(Math.ceil(t/1e3/60/60),"小时前"):e.replace(/T/," ").replace(/Z/,"").substring(0,16)};t.time2DateAndHM=function(e){var t=e.replace(/T/," ").replace(/Z/,"");return"".concat(t.substring(0,10)," ").concat(t.substring(11,16))};t.timestamp2day=function(e){var t=e-Math.round(new Date/1e3);return parseInt(t/86400,0)}},e972:function(e,t,i){e.exports=i.p+"static/img/msg-404.3ba2611f.svg"}}]);