(window.webpackJsonp=window.webpackJsonp||[]).push([["pages-modify-newpwd"],{"0e6a":function(e,t,s){"use strict";var i=s("4ea4").default;Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var n=i(s("3835")),r=s("4b36"),o={data:function(){return{userid:"",fun:!0,valueused:"",valuenew:"",judge:!1,judge2:!1,test:""}},onLoad:function(){this.userid=this.usersid},computed:{usersid:function(){return this.$store.getters["session/get"]("userId")},users:function(){return this.$store.getters["jv/get"]("users/".concat(this.usersid))}},methods:{submission:function(){this.valueused&&this.valuenew?this.valueused===this.valuenew?this.users.hasPassword?uni.showToast({icon:"none",title:this.i18n.t("modify.logoinpaswd"),duration:2e3}):this.setpassword():(this.judge2=!0,this.test=this.i18n.t("modify.masstext")):this.valueused?this.valuenew||uni.showToast({icon:"none",title:this.i18n.t("modify.confrimpasword"),duration:2e3}):uni.showToast({icon:"none",title:this.i18n.t("modify.newpassword"),duration:2e3})},setpassword:function(){var e=this,t=this,s={_jv:{type:"users",id:this.userid},newPassword:this.valueused,password_confirmation:this.valuenew};r.status.run((function(){return e.$store.dispatch("jv/patch",s)})).then((function(s){var i={_jv:{type:"users",id:e.userid}};t.$store.dispatch("jv/get",i).then((function(){})),s&&uni.showToast({title:e.i18n.t("modify.passwordsetsucc"),duration:2e3}),uni.redirectTo({url:"/pages/my/profile"})})).catch((function(t){if(422===t.statusCode){e.judge2=!0;var s=(0,n.default)(t.data.errors,1),i=(0,n.default)(s[0].detail,1)[0];e.test=i}else if(500===t.statusCode){e.judge2=!0;var r=(0,n.default)(t.data.errors,1),o=(0,n.default)(r[0].detail,1)[0];e.test=o}}))}}};t.default=o},1058:function(e,t,s){"use strict";s.r(t);var i=s("0e6a"),n=s.n(i);for(var r in i)["default"].indexOf(r)<0&&function(e){s.d(t,e,(function(){return i[e]}))}(r);t.default=n.a},1235:function(e,t,s){"use strict";s.r(t);var i=s("a8f9"),n=s("1058");for(var r in n)["default"].indexOf(r)<0&&function(e){s.d(t,e,(function(){return n[e]}))}(r);s("5f6c");var o=s("f0c5"),a=Object(o.a)(n.default,i.b,i.c,!1,null,"0dfde591",null,!1,i.a,void 0);t.default=a.exports},"245f":function(e,t,s){"use strict";(function(t){var i=s("4ea4").default,n=i(s("6f74")),r=s("b95e"),o=i(s("4c82"));e.exports={mixins:[n.default,o.default],methods:{getForum:function(){var e=this;this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(t){t&&(e.forum=t)}))},jump2PhoneLoginPage:function(){uni.redirectTo({url:"/pages/user/phone-login"})},jump2PhoneLoginRegisterPage:function(){uni.redirectTo({url:"/pages/user/phone-login-register"})},jump2LoginPage:function(){uni.navigateTo({url:"/pages/user/login"})},jump2RegisterPage:function(){uni.redirectTo({url:"/pages/user/register"})},jump2RegisterExtendPage:function(){uni.redirectTo({url:"/pages/user/supple-mentary"})},jump2LoginBindPage:function(){uni.redirectTo({url:"/pages/user/login-bind"})},jump2RegisterBindPage:function(){uni.redirectTo({url:"/pages/user/register-bind"})},jump2LoginBindPhonePage:function(){uni.redirectTo({url:"/pages/user/login-bind-phone"})},jump2RegisterBindPhonePage:function(){uni.redirectTo({url:"/pages/user/register-bind-phone"})},jump2findpwdPage:function(){uni.navigateTo({url:"/pages/modify/findpwd?pas=reset_pwd"})},mpLoginMode:function(){this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&this.jump2LoginPage(),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open())},h5LoginMode:function(){o.default.isWeixin().isWeixin?(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),this.$store.dispatch("session/wxh5Login"))):(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}))},refreshmpParams:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:function(){},s=this;uni.login({success:function(t){if("login:ok"===t.errMsg){var i={data:{attributes:{js_code:t.code}}};s.$store.dispatch("session/setParams",i),e()}},fail:function(e){t.log(e)}})},mpLogin:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;uni.setStorageSync("register",e),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open()},wxh5Login:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;uni.setStorageSync("register",e),uni.setStorageSync("rebind",t),uni.setStorageSync("h5_wechat_login",1),this.$store.dispatch("session/wxh5Login")},getLoginParams:function(e,t){var s=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{var i=uni.getStorageSync("token");""!==i&&(s.data.attributes.token=i),this.login(s,t)}},getLoginBindParams:function(e,t){var s=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;this.refreshmpParams();var i=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{1===s&&(i.data.attributes.rebind=1);var n=uni.getStorageSync("token");""!==n&&(i.data.attributes.token=n),this.login(i,t)}},login:function(e,s){var i=this;this.$store.dispatch("session/h5Login",e).then((function(e){if(e&&e.data&&e.data.data&&e.data.data.id&&(i.logind(),i.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(e){e&&e.set_site&&e.set_site.site_mode!==r.SITE_PAY&&uni.getStorage({key:"page",success:function(e){uni.redirectTo({url:e.data})}}),e&&e.set_site&&e.set_site.site_mode===r.SITE_PAY&&i.user&&!i.user.paid&&uni.redirectTo({url:"/pages/site/info"})})),uni.showToast({title:s,duration:2e3})),e&&e.data&&e.data.errors){if("401"===e.data.errors[0].status||"402"===e.data.errors[0].status||"500"===e.data.errors[0].status){var t=i.i18n.t("core.".concat(e.data.errors[0].code));uni.showToast({icon:"none",title:t,duration:2e3})}if("403"===e.data.errors[0].status||"422"===e.data.errors[0].status){var n=i.i18n.t("core.".concat(e.data.errors[0].code))||i.i18n.t(e.data.errors[0].detail[0]);uni.showToast({icon:"none",title:n,duration:2e3})}}})).catch((function(e){return t.log(e)}))}}}}).call(this,s("5a52").default)},"368d":function(e,t,s){e.exports=s.p+"static/img/msg-warning.f35ce51f.svg"},"5f6c":function(e,t,s){"use strict";var i=s("b63f");s.n(i).a},"6f74":function(e,t,s){"use strict";var i=s("b95e");e.exports={computed:{user:function(){var e=this.$store.getters["session/get"]("userId");return e?this.$store.getters["jv/get"]("users/".concat(e)):{}}},methods:{getUserInfo:function(){var e=arguments.length>0&&void 0!==arguments[0]&&arguments[0],t=(new Date).getTime(),s=uni.getStorageSync(i.STORGE_GET_USER_TIME);if(e||(t-s)/1e3>60){var n={include:"groups,wechat"},r=this.$store.getters["session/get"]("userId");this.$store.commit("jv/deleteRecord",{_jv:{type:"users",id:r}}),this.$store.dispatch("jv/get",["users/".concat(r),{params:n}]).then((function(){return uni.$emit("updateNotiNum")})),uni.setStorageSync(i.STORGE_GET_USER_TIME,(new Date).getTime())}},logind:function(){var e=this,t=this.$store.getters["session/get"]("userId");if(t){this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]);this.$store.dispatch("jv/get",["users/".concat(t),{params:{include:"groups,wechat"}}]).then((function(t){e.$u.event.$emit("logind",t)})),this.$store.dispatch("forum/setError",{loading:!1})}}}}},a4dc:function(e,t,s){(t=s("24fb")(!1)).push([e.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */\n/* eg:\n  .container {\n    color: --color(BG-1);\n  }\n*/.page-newpwd[data-v-0dfde591] {background-color:var(--qui-BG-2);box-sizing:border-box}.page-newpwd[data-v-0dfde591] .setuppas{width:100vw;background-color:var(--qui-BG-2);box-sizing:border-box}.page-newpwd[data-v-0dfde591] .setuppas-pas{width:100%;padding:%?31?% 0 0 %?40?%;box-sizing:border-box}.page-newpwd[data-v-0dfde591] .setuppas-pas-inpa{width:100%;height:%?100?%;font-size:%?36?%;font-weight:400;line-height:%?100?%;text-align:left;border-bottom:%?2?% solid var(--qui-BOR-ED);opacity:1}.page-newpwd[data-v-0dfde591] .setuppas-pas-inpa2{width:100%;height:%?100?%;font-size:%?36?%;font-weight:400;line-height:%?100?%;color:var(--qui-RED);text-align:left;border-bottom:%?2?% solid var(--qui-RED);opacity:1}.page-newpwd[data-v-0dfde591] .setuppas-pas-btn{margin:%?50?% 0 0}.page-newpwd[data-v-0dfde591] .setuppas-erro-messag1{margin-top:%?20?%;font-size:%?26?%;font-weight:400;color:var(--qui-RED)}.setuppas-pas-btn[data-v-0dfde591] .qui-button--button[size="large"]{font-size:%?30?%;color:var(--qui-FC-FFF);border-radius:%?7?%}',""]),e.exports=t},a88b:function(e,t,s){"use strict";(function(e){var i;s.d(t,"b",(function(){return n})),s.d(t,"c",(function(){return r})),s.d(t,"a",(function(){return i}));try{i={quiPage:s("29c4").default,quiButton:s("8397").default}}catch(t){if(-1===t.message.indexOf("Cannot find module")||-1===t.message.indexOf(".vue"))throw t;e.error(t.message),e.error("1. 排查组件名称拼写是否正确"),e.error("2. 排查组件是否符合 easycom 规范，文档：https://uniapp.dcloud.net.cn/collocation/pages?id=easycom"),e.error("3. 若组件不符合 easycom 规范，需手动引入，并在 components 中注册该组件")}var n=function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("qui-page",{staticClass:"page-newpwd",attrs:{"data-qui-theme":e.theme}},[s("v-uni-view",{staticClass:"setuppas"},[s("v-uni-view",{staticClass:"setuppas-pas"},[s("v-uni-input",{staticClass:"setuppas-pas-inpa",attrs:{type:"password",placeholder:e.i18n.t("modify.enternew"),"placeholder-style":"color:rgba(221,221,221,1)"},model:{value:e.valueused,callback:function(t){e.valueused=t},expression:"valueused"}}),s("v-uni-input",{staticClass:"setuppas-pas-inpa",attrs:{type:"password",placeholder:e.i18n.t("modify.enterreplace"),"placeholder-style":"color:rgba(221,221,221,1)"},model:{value:e.valuenew,callback:function(t){e.valuenew=t},expression:"valuenew"}}),e.judge2?s("v-uni-view",{staticClass:"setuppas-erro-messag1"},[e._v(e._s(e.test))]):e._e(),s("v-uni-view",{staticClass:"setuppas-pas-btn"},[s("qui-button",{attrs:{type:"primary",size:"large"},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.submission.apply(void 0,arguments)}}},[e._v(e._s(e.i18n.t("modify.submission")))])],1)],1)],1)],1)},r=[]}).call(this,s("5a52").default)},a8f9:function(e,t,s){"use strict";var i=s("a88b");s.d(t,"a",(function(){return i.a})),s.d(t,"b",(function(){return i.b})),s.d(t,"c",(function(){return i.c}))},b469:function(e,t){e.exports={computed:{forums:function(){return this.$store.getters["jv/get"]("forums/1")}}}},b63f:function(e,t,s){var i=s("a4dc");"string"==typeof i&&(i=[[e.i,i,""]]),i.locals&&(e.exports=i.locals);(0,s("4f06").default)("24de143a",i,!0,{sourceMap:!1,shadowMode:!1})},e972:function(e,t,s){e.exports=s.p+"static/img/msg-404.3ba2611f.svg"}}]);