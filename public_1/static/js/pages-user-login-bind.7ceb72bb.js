(window.webpackJsonp=window.webpackJsonp||[]).push([["pages-user-login-bind"],{"12b6":function(t,e,i){"use strict";i.r(e);var n=i("9332"),s=i("63c9");for(var o in s)["default"].indexOf(o)<0&&function(t){i.d(e,t,(function(){return s[t]}))}(o);i("b635");var r=i("f0c5"),a=Object(r.a)(s.default,n.b,n.c,!1,null,"3e604c0a",null,!1,n.a,void 0);e.default=a.exports},"205c":function(t,e,i){(e=i("24fb")(!1)).push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */\n/* eg:\n  .container {\n    color: --color(BG-1);\n  }\n*/.login-bind-box[data-v-3e604c0a]{padding-bottom:40px;font-size:%?30?%;background-color:var(--qui-BG-2)}.login-bind-box-h[data-v-3e604c0a]{padding:%?60?% %?40?% %?80?%;font-size:%?50?%;font-weight:700;color:var(--qui-FC-333)}.login-bind-box-info[data-v-3e604c0a]{padding:%?0?% %?40?% %?50?%;font-size:%?40?%}.login-bind-box-info-h[data-v-3e604c0a]{margin-bottom:%?20?%}.login-bind-box-info-image[data-v-3e604c0a]{width:%?50?%;height:%?50?%;margin-right:%?20?%;vertical-align:middle;border-radius:%?100?%}.login-bind-box-info-bold[data-v-3e604c0a]{font-weight:700}.login-bind-box-info-ft[data-v-3e604c0a]{font-size:%?34?%}.login-bind-box-con[data-v-3e604c0a]{margin:%?0?% %?40?%}.login-bind-box-con .input[data-v-3e604c0a]{width:100%;height:%?100?%;padding:%?0?% %?0?% %?0?% %?20?%;font-size:%?36?%;line-height:%?100?%;text-align:left;border-bottom:%?2?% solid var(--qui-BOR-ED)}.login-bind-box-btn[data-v-3e604c0a]{width:%?670?%;height:%?90?%;margin:%?50?% auto %?0?%;line-height:%?90?%;color:var(--qui-FC-FFF);text-align:center;background-color:var(--qui-MAIN);border-radius:%?5?%}.login-bind-box-ft[data-v-3e604c0a]{margin:%?160?% 0 %?50?%;text-align:center}.login-bind-box-ft-btn[data-v-3e604c0a]{color:#1878f3}.login-bind-box-ft-text[data-v-3e604c0a]{color:#aaa}.login-bind-box-ft-line[data-v-3e604c0a]{width:%?0?%;height:%?32?%;margin:0 %?50?%;border:%?2?% solid #ddd}',""]),t.exports=e},"245f":function(t,e,i){"use strict";(function(e){var n=i("4ea4").default,s=n(i("6f74")),o=i("b95e"),r=n(i("4c82"));t.exports={mixins:[s.default,r.default],methods:{getForum:function(){var t=this;this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(e){e&&(t.forum=e)}))},jump2PhoneLoginPage:function(){uni.redirectTo({url:"/pages/user/phone-login"})},jump2PhoneLoginRegisterPage:function(){uni.redirectTo({url:"/pages/user/phone-login-register"})},jump2LoginPage:function(){uni.navigateTo({url:"/pages/user/login"})},jump2RegisterPage:function(){uni.redirectTo({url:"/pages/user/register"})},jump2RegisterExtendPage:function(){uni.redirectTo({url:"/pages/user/supple-mentary"})},jump2LoginBindPage:function(){uni.redirectTo({url:"/pages/user/login-bind"})},jump2RegisterBindPage:function(){uni.redirectTo({url:"/pages/user/register-bind"})},jump2LoginBindPhonePage:function(){uni.redirectTo({url:"/pages/user/login-bind-phone"})},jump2RegisterBindPhonePage:function(){uni.redirectTo({url:"/pages/user/register-bind-phone"})},jump2findpwdPage:function(){uni.navigateTo({url:"/pages/modify/findpwd?pas=reset_pwd"})},mpLoginMode:function(){this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&this.jump2LoginPage(),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open())},h5LoginMode:function(){r.default.isWeixin().isWeixin?(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),this.$store.dispatch("session/wxh5Login"))):(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}))},refreshmpParams:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:function(){},i=this;uni.login({success:function(e){if("login:ok"===e.errMsg){var n={data:{attributes:{js_code:e.code}}};i.$store.dispatch("session/setParams",n),t()}},fail:function(t){e.log(t)}})},mpLogin:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;uni.setStorageSync("register",t),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open()},wxh5Login:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;uni.setStorageSync("register",t),uni.setStorageSync("rebind",e),uni.setStorageSync("h5_wechat_login",1),this.$store.dispatch("session/wxh5Login")},getLoginParams:function(t,e){var i=t;if(""===t.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===t.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{var n=uni.getStorageSync("token");""!==n&&(i.data.attributes.token=n),this.login(i,e)}},getLoginBindParams:function(t,e){var i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;this.refreshmpParams();var n=t;if(""===t.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===t.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{1===i&&(n.data.attributes.rebind=1);var s=uni.getStorageSync("token");""!==s&&(n.data.attributes.token=s),this.login(n,e)}},login:function(t,i){var n=this;this.$store.dispatch("session/h5Login",t).then((function(t){if(t&&t.data&&t.data.data&&t.data.data.id&&(n.logind(),n.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(t){t&&t.set_site&&t.set_site.site_mode!==o.SITE_PAY&&uni.getStorage({key:"page",success:function(t){uni.redirectTo({url:t.data})}}),t&&t.set_site&&t.set_site.site_mode===o.SITE_PAY&&n.user&&!n.user.paid&&uni.redirectTo({url:"/pages/site/info"})})),uni.showToast({title:i,duration:2e3})),t&&t.data&&t.data.errors){if("401"===t.data.errors[0].status||"402"===t.data.errors[0].status||"500"===t.data.errors[0].status){var e=n.i18n.t("core.".concat(t.data.errors[0].code));uni.showToast({icon:"none",title:e,duration:2e3})}if("403"===t.data.errors[0].status||"422"===t.data.errors[0].status){var s=n.i18n.t("core.".concat(t.data.errors[0].code))||n.i18n.t(t.data.errors[0].detail[0]);uni.showToast({icon:"none",title:s,duration:2e3})}}})).catch((function(t){return e.log(t)}))}}}}).call(this,i("5a52").default)},"368d":function(t,e,i){t.exports=i.p+"static/img/msg-warning.f35ce51f.svg"},"63c9":function(t,e,i){"use strict";i.r(e);var n=i("a6bb"),s=i.n(n);for(var o in n)["default"].indexOf(o)<0&&function(t){i.d(e,t,(function(){return n[t]}))}(o);e.default=s.a},"6f74":function(t,e,i){"use strict";var n=i("b95e");t.exports={computed:{user:function(){var t=this.$store.getters["session/get"]("userId");return t?this.$store.getters["jv/get"]("users/".concat(t)):{}}},methods:{getUserInfo:function(){var t=arguments.length>0&&void 0!==arguments[0]&&arguments[0],e=(new Date).getTime(),i=uni.getStorageSync(n.STORGE_GET_USER_TIME);if(t||(e-i)/1e3>60){var s={include:"groups,wechat"},o=this.$store.getters["session/get"]("userId");this.$store.commit("jv/deleteRecord",{_jv:{type:"users",id:o}}),this.$store.dispatch("jv/get",["users/".concat(o),{params:s}]).then((function(){return uni.$emit("updateNotiNum")})),uni.setStorageSync(n.STORGE_GET_USER_TIME,(new Date).getTime())}},logind:function(){var t=this,e=this.$store.getters["session/get"]("userId");if(e){this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]);this.$store.dispatch("jv/get",["users/".concat(e),{params:{include:"groups,wechat"}}]).then((function(e){t.$u.event.$emit("logind",e)})),this.$store.dispatch("forum/setError",{loading:!1})}}}}},"87b2":function(t,e,i){"use strict";(function(t){var n;i.d(e,"b",(function(){return s})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return n}));try{n={quiPage:i("29c4").default,quiRegistrationAgreement:i("aabe").default}}catch(e){if(-1===e.message.indexOf("Cannot find module")||-1===e.message.indexOf(".vue"))throw e;t.error(e.message),t.error("1. 排查组件名称拼写是否正确"),t.error("2. 排查组件是否符合 easycom 规范，文档：https://uniapp.dcloud.net.cn/collocation/pages?id=easycom"),t.error("3. 若组件不符合 easycom 规范，需手动引入，并在 components 中注册该组件")}var s=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("qui-page",{staticClass:"login-bind-box",attrs:{"data-qui-theme":t.theme}},[i("v-uni-view",[i("v-uni-view",{staticClass:"login-bind-box-h"},[t._v(t._s(t.i18n.t("user.loginBindId")))]),i("v-uni-view",{staticClass:"login-bind-box-info"},[i("v-uni-view",{staticClass:"login-bind-box-info-h"},[i("v-uni-text",[t._v(t._s(t.i18n.t("user.dear")))]),i("img",{staticClass:"login-bind-box-info-image",attrs:{src:t.userInfo.headimgurl}}),i("v-uni-text",{staticClass:"login-bind-box-info-bold"},[t._v(t._s(t.userInfo.username))])],1),i("v-uni-view",{staticClass:"login-bind-box-info-ft"},[t._v(t._s(t.isBind?t.i18n.t("user.loginBindText"):t.i18n.t("user.changeLoginBindText")))])],1),i("v-uni-view",{staticClass:"login-bind-box-con"},[i("v-uni-input",{staticClass:"input",attrs:{maxlength:"15",placeholder:t.i18n.t("user.username"),"placeholder-style":"color: #ddd"},model:{value:t.username,callback:function(e){t.username=e},expression:"username"}}),i("v-uni-input",{staticClass:"input",attrs:{type:"password",maxlength:"50",placeholder:t.i18n.t("user.password"),"placeholder-style":"color: #ddd"},model:{value:t.password,callback:function(e){t.password=e},expression:"password"}})],1),i("v-uni-view",{staticClass:"login-bind-box-btn",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.handleLogin.apply(void 0,arguments)}}},[t._v(t._s(t.i18n.t("user.loginBind")))]),i("v-uni-view",{staticClass:"login-bind-box-ft"},[t.isBind?i("v-uni-view",[t.forum&&t.forum.set_reg&&t.forum.set_reg.register_close?i("v-uni-text",{staticClass:"login-bind-box-ft-btn",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.jump2Register.apply(void 0,arguments)}}},[t._v(t._s(t.i18n.t("user.registerUser")))]):t._e()],1):i("v-uni-view",[t.forum&&t.forum.set_reg&&t.forum.set_reg.register_close?i("v-uni-text",{staticClass:"login-bind-box-ft-btn",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.jump2Register.apply(void 0,arguments)}}},[t._v(t._s(t.i18n.t("user.registerUser")))]):t._e(),t.forum&&t.forum.set_reg&&t.forum.set_reg.register_close&&t.forum.qcloud&&t.forum.qcloud.qcloud_sms?i("v-uni-text",{staticClass:"login-bind-box-ft-line"}):t._e(),t.forum&&t.forum.qcloud&&t.forum.qcloud.qcloud_sms?i("v-uni-text",{staticClass:"login-bind-box-ft-text",on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.jump2findpwd.apply(void 0,arguments)}}},[t._v(t._s(t.i18n.t("user.forgetPassword")))]):t._e()],1)],1)],1),i("qui-registration-agreement")],1)},o=[]}).call(this,i("5a52").default)},9332:function(t,e,i){"use strict";var n=i("87b2");i.d(e,"a",(function(){return n.a})),i.d(e,"b",(function(){return n.b})),i.d(e,"c",(function(){return n.c}))},a6bb:function(t,e,i){"use strict";var n=i("4ea4").default;Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var s=n(i("6f74")),o=n(i("245f")),r={mixins:[s.default,o.default],data:function(){return{username:"",password:"",site_mode:"",forum:{},isPaid:!1}},onLoad:function(){this.getForum()},computed:{userInfo:function(){return uni.getStorageSync("userInfo")},isBind:function(){return uni.getStorageSync("isBind")}},methods:{handleLogin:function(){var t={data:{attributes:{username:this.username,password:this.password}}};this.isBind?this.getLoginBindParams(t,"绑定成功"):this.getLoginBindParams(t,"绑定成功",1)},jump2Register:function(){this.jump2RegisterBindPage()},jump2findpwd:function(){this.jump2findpwdPage()}}};e.default=r},b469:function(t,e){t.exports={computed:{forums:function(){return this.$store.getters["jv/get"]("forums/1")}}}},b635:function(t,e,i){"use strict";var n=i("f48f");i.n(n).a},e972:function(t,e,i){t.exports=i.p+"static/img/msg-404.3ba2611f.svg"},f48f:function(t,e,i){var n=i("205c");"string"==typeof n&&(n=[[t.i,n,""]]),n.locals&&(t.exports=n.locals);(0,i("4f06").default)("0df96464",n,!0,{sourceMap:!1,shadowMode:!1})}}]);