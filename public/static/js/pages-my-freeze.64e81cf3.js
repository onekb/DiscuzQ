(window.webpackJsonp=window.webpackJsonp||[]).push([["pages-my-freeze"],{"12c0":function(e,t,i){"use strict";(function(e){var s;i.d(t,"b",(function(){return n})),i.d(t,"c",(function(){return r})),i.d(t,"a",(function(){return s}));try{s={quiPage:i("29c4").default,quiCellItem:i("e0ca").default,quiLoadMore:i("51e5").default}}catch(t){if(-1===t.message.indexOf("Cannot find module")||-1===t.message.indexOf(".vue"))throw t;e.error(t.message),e.error("1. 排查组件名称拼写是否正确"),e.error("2. 排查组件是否符合 easycom 规范，文档：https://uniapp.dcloud.net.cn/collocation/pages?id=easycom"),e.error("3. 若组件不符合 easycom 规范，需手动引入，并在 components 中注册该组件")}var n=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("qui-page",{staticClass:"freeze",attrs:{"data-qui-theme":e.theme}},[i("v-uni-scroll-view",{staticClass:"scroll-y",attrs:{"scroll-y":"true","scroll-with-animation":"true","show-scrollbar":"false"},on:{scrolltolower:function(t){arguments[0]=t=e.$handleEvent(t),e.pullDown.apply(void 0,arguments)}}},[i("v-uni-view",{staticClass:"freeze-head"},[i("v-uni-view",{staticClass:"freeze-head__num"},[i("v-uni-text",[e._v(e._s(e.i18n.t("profile.total")))]),i("v-uni-text",{staticClass:"freeze-head__num__detail"},[e._v(e._s(e.totalData))]),i("v-uni-text",[e._v(e._s(""+e.i18n.t("profile.item")+e.i18n.t("profile.records")))])],1),i("v-uni-view",{staticClass:"freeze-head__money"},[i("v-uni-text",[e._v(e._s(e.i18n.t("profile.amountinvolved")))]),i("v-uni-text",{staticClass:"freeze-head__money__detail"},[e._v("¥"+e._s(e.userInfo.walletFreeze))])],1)],1),e.freezelist.length>0?i("v-uni-view",{staticClass:"freeze-items"},e._l(e.freezelist,(function(t,s){return i("qui-cell-item",{key:s,attrs:{title:e.i18n.t("profile.freezingreason")+" : "+t.change_desc,brief:"ID:"+t.id,addon:t.change_freeze_amount>0?"￥"+t.change_freeze_amount:"-￥"+t.change_freeze_amount.substr(1),"brief-right":e.timeHandle(t.created_at),border:s!=e.freezelist.length-1}})})),1):e._e(),e.loadingType?i("qui-load-more",{attrs:{status:e.loadingType,"show-icon":!1}}):e._e()],1)],1)},r=[]}).call(this,i("5a52").default)},1341:function(e,t,i){(t=i("24fb")(!1)).push([e.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */\n/* eg:\n  .container {\n    color: --color(BG-1);\n  }\n*/.freeze[data-v-79e714d8] {min-height:100vh}.freeze[data-v-79e714d8] .cell-item{padding-right:%?40?%}.freeze[data-v-79e714d8] .cell-item__body{height:%?150?%}.freeze[data-v-79e714d8] .freeze-head__num__detail,\n.freeze[data-v-79e714d8] .freeze-head__money__detail,\n.freeze[data-v-79e714d8] .cell-item__body__right-text{font-weight:700}.freeze[data-v-79e714d8] .freeze-head{display:-webkit-box;display:-webkit-flex;display:flex;height:%?78?%;-webkit-box-align:center;-webkit-align-items:center;align-items:center;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;padding:%?40?% %?40?% 0;margin-bottom:%?30?%;font-size:%?26?%;background:var(--qui-BG-2);border-bottom:%?2?% solid var(--qui-BOR-ED)}.freeze[data-v-79e714d8] .freeze-head__num{-webkit-box-pack:start;-webkit-justify-content:flex-start;justify-content:flex-start}.freeze[data-v-79e714d8] .freeze-head__money{-webkit-box-pack:end;-webkit-justify-content:flex-end;justify-content:flex-end}.freeze[data-v-79e714d8] .freeze-head__num__detail{margin:0 %?5?%}.freeze[data-v-79e714d8] .freeze-head__money__detail{margin-left:%?10?%}.freeze[data-v-79e714d8] .freeze-items{padding-left:%?40?%;margin-bottom:%?30?%;background:var(--qui-BG-2);border-bottom:%?2?% solid var(--qui-BOR-ED)}.scroll-y[data-v-79e714d8]{max-height:100vh}',""]),e.exports=t},1578:function(e,t,i){var s=i("1341");"string"==typeof s&&(s=[[e.i,s,""]]),s.locals&&(e.exports=s.locals);(0,i("4f06").default)("0417b87c",s,!0,{sourceMap:!1,shadowMode:!1})},"1c03":function(e,t,i){"use strict";var s=i("4ea4");i("99af"),Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var n=s(i("2909")),r=i("cadb"),a={onLoad:function(){this.getFreezelist()},data:function(){return{loadingType:"",totalData:0,pageSize:20,pageNum:1,freezelist:[],userId:this.$store.getters["session/get"]("userId")}},computed:{userInfo:function(){return this.$store.getters["jv/get"]("/users/".concat(this.userId))}},methods:{getFreezelist:function(){var e=this;this.loadingType="loading";var t={"filter[user]":this.userId,"filter[change_type]":[10,11,12,81,9,8],"page[number]":this.pageNum,"page[limit]":this.pageSize};this.$store.dispatch("jv/get",["wallet/log",{params:t}]).then((function(t){t._jv&&(e.totalData=t._jv.json.meta.total,delete t._jv),e.loadingType=t.length===e.pageSize?"more":"nomore",e.freezelist=[].concat((0,n.default)(e.freezelist),(0,n.default)(t))}))},timeHandle:function(e){return(0,r.time2MinuteOrHour)(e)},pullDown:function(){"more"===this.loadingType&&(this.pageNum+=1,this.getFreezelist())}}};t.default=a},"245f":function(e,t,i){"use strict";(function(t){var s=i("4ea4"),n=s(i("6f74")),r=i("b95e"),a=s(i("4c82"));e.exports={mixins:[n.default,a.default],methods:{getForum:function(){var e=this;this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(i){i&&(t.log("forum",i),e.forum=i)}))},jump2PhoneLoginPage:function(){uni.redirectTo({url:"/pages/user/phone-login"})},jump2PhoneLoginRegisterPage:function(){uni.redirectTo({url:"/pages/user/phone-login-register"})},jump2LoginPage:function(){uni.redirectTo({url:"/pages/user/login"})},jump2RegisterPage:function(){uni.redirectTo({url:"/pages/user/register"})},jump2RegisterExtendPage:function(){uni.redirectTo({url:"/pages/user/supple-mentary"})},jump2LoginBindPage:function(){uni.redirectTo({url:"/pages/user/login-bind"})},jump2RegisterBindPage:function(){uni.redirectTo({url:"/pages/user/register-bind"})},jump2LoginBindPhonePage:function(){uni.redirectTo({url:"/pages/user/login-bind-phone"})},jump2RegisterBindPhonePage:function(){uni.redirectTo({url:"/pages/user/register-bind-phone"})},jump2findpwdPage:function(){uni.navigateTo({url:"/pages/modify/findpwd?pas=reset_pwd"})},mpLoginMode:function(){this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&this.jump2LoginPage(),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open())},h5LoginMode:function(){a.default.isWeixin().isWeixin?(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),this.$store.dispatch("session/wxh5Login"))):(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}))},refreshmpParams:function(){var e=this;uni.login({success:function(i){if("login:ok"===i.errMsg){var s=i.code;uni.getUserInfo({success:function(t){var i={data:{attributes:{js_code:s,iv:t.iv,encryptedData:t.encryptedData}}};e.$store.dispatch("session/setParams",i)},fail:function(e){t.log(e)}})}},fail:function(e){t.log(e)}})},mpLogin:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;uni.setStorageSync("register",e),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open()},wxh5Login:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;uni.setStorageSync("register",e),uni.setStorageSync("rebind",t),this.$store.dispatch("session/wxh5Login")},getLoginParams:function(e,t){var i=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{var s=uni.getStorageSync("token");""!==s&&(i.data.attributes.token=s),this.login(i,t)}},getLoginBindParams:function(e,t){var i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;this.refreshmpParams();var s=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{1===i&&(s.data.attributes.rebind=1);var n=uni.getStorageSync("token");""!==n&&(s.data.attributes.token=n),this.login(s,t)}},login:function(e,i){var s=this;this.$store.dispatch("session/h5Login",e).then((function(e){if(e&&e.data&&e.data.data&&e.data.data.id&&(s.logind(),s.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(e){e&&e.set_site&&e.set_site.site_mode!==r.SITE_PAY&&uni.getStorage({key:"page",success:function(e){t.log("resData",e),uni.redirectTo({url:e.data})}}),e&&e.set_site&&e.set_site.site_mode===r.SITE_PAY&&s.user&&!s.user.paid&&uni.redirectTo({url:"/pages/site/info"})})),uni.showToast({title:i,duration:2e3})),e&&e.data&&e.data.errors){if("401"===e.data.errors[0].status||"402"===e.data.errors[0].status||"500"===e.data.errors[0].status){var n=s.i18n.t("core.".concat(e.data.errors[0].code));uni.showToast({icon:"none",title:n,duration:2e3})}if("403"===e.data.errors[0].status||"422"===e.data.errors[0].status){var a=s.i18n.t(e.data.errors[0].detail[0]);uni.showToast({icon:"none",title:a,duration:2e3})}}})).catch((function(e){return t.log(e)}))}}}}).call(this,i("5a52").default)},"2e70":function(e,t,i){"use strict";var s=i("1578");i.n(s).a},3150:function(e,t,i){"use strict";i.r(t);var s=i("eaf3"),n=i("6315");for(var r in n)["default"].indexOf(r)<0&&function(e){i.d(t,e,(function(){return n[e]}))}(r);i("2e70");var a=i("f0c5"),o=Object(a.a)(n.default,s.b,s.c,!1,null,"79e714d8",null,!1,s.a,void 0);t.default=o.exports},"368d":function(e,t,i){e.exports=i.p+"static/img/msg-warning.0c78a551.svg"},6315:function(e,t,i){"use strict";i.r(t);var s=i("1c03"),n=i.n(s);for(var r in s)["default"].indexOf(r)<0&&function(e){i.d(t,e,(function(){return s[e]}))}(r);t.default=n.a},"6f74":function(e,t,i){"use strict";var s=i("b95e");e.exports={computed:{user:function(){var e=this.$store.getters["session/get"]("userId");return e?this.$store.getters["jv/get"]("users/".concat(e)):{}}},methods:{getUserInfo:function(){var e=arguments.length>0&&void 0!==arguments[0]&&arguments[0],t=(new Date).getTime(),i=uni.getStorageSync(s.STORGE_GET_USER_TIME);if(e||(t-i)/1e3>60){var n={include:"groups,wechat"},r=this.$store.getters["session/get"]("userId");this.$store.commit("jv/deleteRecord",{_jv:{type:"users",id:r}}),this.$store.dispatch("jv/get",["users/".concat(r),{params:n}]).then((function(){return uni.$emit("updateNotiNum")})),uni.setStorageSync(s.STORGE_GET_USER_TIME,(new Date).getTime())}},logind:function(){var e=this,t=this.$store.getters["session/get"]("userId");if(t){this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]);this.$store.dispatch("jv/get",["users/".concat(t),{params:{include:"groups,wechat"}}]).then((function(t){e.$u.event.$emit("logind",t)})),this.$store.dispatch("forum/setError",{loading:!1})}}}}},b469:function(e,t){e.exports={computed:{forums:function(){return this.$store.getters["jv/get"]("forums/1")}}}},cadb:function(e,t,i){"use strict";i("99af"),i("e25e"),i("ac1f"),i("5319"),Object.defineProperty(t,"__esModule",{value:!0}),t.timestamp2day=t.time2DateAndHM=t.time2MinuteOrHour=void 0;t.time2MinuteOrHour=function(e){var t=new Date-new Date(e);return parseInt(parseInt(t/1e3,0)/60,0)<60?"".concat(Math.ceil(t/1e3/60),"分钟前"):parseInt(parseInt(parseInt(t/1e3,0)/60,0)/60,0)<16?"".concat(Math.ceil(t/1e3/60/60),"小时前"):e.replace(/T/," ").replace(/Z/,"").substring(0,16)};t.time2DateAndHM=function(e){var t=e.replace(/T/," ").replace(/Z/,"");return"".concat(t.substring(0,10)," ").concat(t.substring(11,16))};t.timestamp2day=function(e){var t=e-Math.round(new Date/1e3);return parseInt(t/86400,0)}},e972:function(e,t,i){e.exports=i.p+"static/img/msg-404.e11dc2d7.svg"},eaf3:function(e,t,i){"use strict";var s=i("12c0");i.d(t,"a",(function(){return s.a})),i.d(t,"b",(function(){return s.b})),i.d(t,"c",(function(){return s.c}))}}]);