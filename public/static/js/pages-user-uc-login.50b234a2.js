(window.webpackJsonp=window.webpackJsonp||[]).push([["pages-user-uc-login"],{"093c":function(e,t,i){"use strict";i.r(t);var s=i("e2e6"),n=i.n(s);for(var r in s)["default"].indexOf(r)<0&&function(e){i.d(t,e,(function(){return s[e]}))}(r);t.default=n.a},"1e39":function(e,t,i){"use strict";var s=i("c46f");i.n(s).a},"245f":function(e,t,i){"use strict";(function(t){var s=i("4ea4"),n=s(i("6f74")),r=i("b95e"),o=s(i("4c82"));e.exports={mixins:[n.default,o.default],methods:{getForum:function(){var e=this;this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(t){t&&(e.forum=t)}))},jump2PhoneLoginPage:function(){uni.redirectTo({url:"/pages/user/phone-login"})},jump2PhoneLoginRegisterPage:function(){uni.redirectTo({url:"/pages/user/phone-login-register"})},jump2LoginPage:function(){uni.redirectTo({url:"/pages/user/login"})},jump2RegisterPage:function(){uni.redirectTo({url:"/pages/user/register"})},jump2LoginBindPage:function(){uni.redirectTo({url:"/pages/user/login-bind"})},jump2RegisterBindPage:function(){uni.redirectTo({url:"/pages/user/register-bind"})},jump2LoginBindPhonePage:function(){uni.redirectTo({url:"/pages/user/login-bind-phone"})},jump2RegisterBindPhonePage:function(){uni.redirectTo({url:"/pages/user/register-bind-phone"})},jump2findpwdPage:function(){uni.navigateTo({url:"/pages/modify/findpwd?pas=reset_pwd"})},mpLoginMode:function(){this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&this.jump2LoginPage(),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open())},h5LoginMode:function(){o.default.isWeixin().isWeixin?(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),this.$store.dispatch("session/wxh5Login"))):(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}))},refreshmpParams:function(){var e=this;uni.login({success:function(i){if("login:ok"===i.errMsg){var s=i.code;uni.getUserInfo({success:function(t){var i={data:{attributes:{js_code:s,iv:t.iv,encryptedData:t.encryptedData}}};e.$store.dispatch("session/setParams",i)},fail:function(e){t.log(e)}})}},fail:function(e){t.log(e)}})},mpLogin:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;uni.setStorageSync("register",e),uni.setStorageSync("isSend",!0),uni.setStorageSync("isBind",!1),this.$store.getters["session/get"]("auth").open()},wxh5Login:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;uni.setStorageSync("register",e),uni.setStorageSync("rebind",t),this.$store.dispatch("session/wxh5Login")},getLoginParams:function(e,t){var i=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{var s=uni.getStorageSync("token");""!==s&&(i.data.attributes.token=s),this.login(i,t)}},getLoginBindParams:function(e,t){var i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;this.refreshmpParams();var s=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{1===i&&(s.data.attributes.rebind=1);var n=uni.getStorageSync("token");""!==n&&(s.data.attributes.token=n),this.login(s,t)}},login:function(e,i){var s=this;this.$store.dispatch("session/h5Login",e).then((function(e){if(e&&e.data&&e.data.data&&e.data.data.id&&(s.logind(),s.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(e){e&&e.set_site&&e.set_site.site_mode!==r.SITE_PAY&&uni.getStorage({key:"page",success:function(e){t.log("resData",e),uni.redirectTo({url:e.data})}}),e&&e.set_site&&e.set_site.site_mode===r.SITE_PAY&&s.user&&!s.user.paid&&uni.redirectTo({url:"/pages/site/info"})})),uni.showToast({title:i,duration:2e3})),e&&e.data&&e.data.errors){if("401"===e.data.errors[0].status||"402"===e.data.errors[0].status||"500"===e.data.errors[0].status){var n=s.i18n.t("core.".concat(e.data.errors[0].code));uni.showToast({icon:"none",title:n,duration:2e3})}if("403"===e.data.errors[0].status||"422"===e.data.errors[0].status){var o=s.i18n.t(e.data.errors[0].detail[0]);uni.showToast({icon:"none",title:o,duration:2e3})}}})).catch((function(e){return t.log(e)}))}}}}).call(this,i("5a52").default)},"259e":function(e,t,i){"use strict";(function(e){var s;i.d(t,"b",(function(){return n})),i.d(t,"c",(function(){return r})),i.d(t,"a",(function(){return s}));try{s={quiPage:i("29c4").default,quiIcon:i("895d").default}}catch(t){if(-1===t.message.indexOf("Cannot find module")||-1===t.message.indexOf(".vue"))throw t;e.error(t.message),e.error("1. 排查组件名称拼写是否正确"),e.error("2. 排查组件是否符合 easycom 规范，文档：https://uniapp.dcloud.net.cn/collocation/pages?id=easycom"),e.error("3. 若组件不符合 easycom 规范，需手动引入，并在 components 中注册该组件")}var n=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("qui-page",{staticClass:"uc-login",attrs:{"data-qui-theme":e.theme}},[i("v-uni-view",[i("v-uni-view",{staticClass:"uc-login-h"},[e._v(e._s(e.i18n.t("user.ucenterlogin")))]),i("v-uni-view",{staticClass:"uc-login-con"},[i("v-uni-input",{staticClass:"input",attrs:{maxlength:"15",placeholder:e.i18n.t("user.username"),"placeholder-style":"color: #ddd"},model:{value:e.username,callback:function(t){e.username=t},expression:"username"}}),i("v-uni-input",{staticClass:"input",attrs:{type:"password",maxlength:"50",placeholder:e.i18n.t("user.password"),"placeholder-style":"color: #ddd"},model:{value:e.password,callback:function(t){e.password=t},expression:"password"}}),i("v-uni-view",{staticClass:"box"},[e._l(e.problem,(function(t,s){return i("v-uni-view",{directives:[{name:"show",rawName:"v-show",value:e.sun===s||e.num,expression:"sun === index || num"}],key:s,staticClass:"box-sun",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.loader(e.problem[s].key)}}},[e._v(e._s(e.problem[s].value))])})),i("v-uni-view",{staticClass:"box-min",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.minBtn.apply(void 0,arguments)}}},[i("qui-icon",{attrs:{name:"icon-unfold",size:"18",color:"#ddd"}})],1)],2),e.displays?i("v-uni-input",{staticClass:"input",attrs:{type:"text",maxlength:"50",placeholder:e.i18n.t("user.answers"),"placeholder-style":"color: #ddd"},model:{value:e.answer,callback:function(t){e.answer=t},expression:"answer"}}):e._e()],1),i("v-uni-view",{staticClass:"uc-login-btn",on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.handleLogin.apply(void 0,arguments)}}},[e._v(e._s(e.i18n.t("user.login")))])],1)],1)},r=[]}).call(this,i("5a52").default)},"368d":function(e,t,i){e.exports=i.p+"static/img/msg-warning.0c78a551.svg"},"416b":function(e,t,i){(t=i("24fb")(!1)).push([e.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */\n/* eg:\n  .container {\n    color: --color(BG-1);\n  }\n*/.uc-login[data-v-5bfd9984]{padding-bottom:40px;font-size:%?30?%;background-color:var(--qui-BG-2)}.uc-login-h[data-v-5bfd9984]{padding:%?60?% %?0?% %?80?% %?40?%;font-size:%?50?%;font-weight:700;color:var(--qui-FC-333)}.uc-login-con[data-v-5bfd9984]{margin:%?0?% %?40?%}.uc-login-con .input[data-v-5bfd9984]{width:100%;height:%?100?%;font-size:%?36?%;line-height:%?100?%;text-align:left;border-bottom:%?2?% solid var(--qui-BOR-ED)}.uc-login-btn[data-v-5bfd9984]{width:%?670?%;height:%?90?%;margin:%?50?% auto %?0?%;line-height:%?90?%;color:var(--qui-FC-FFF);text-align:center;background-color:var(--qui-MAIN);border-radius:%?5?%}.uc-login-ft[data-v-5bfd9984]{margin:%?160?% 0 %?50?%;text-align:center}.uc-login-ft-title[data-v-5bfd9984]{color:#ddd}.uc-login-ft-con[data-v-5bfd9984]{margin:%?30?% 0 %?100?%}.uc-login-ft-con-image[data-v-5bfd9984]{width:%?100?%;height:%?100?%}.uc-login-ft-btn[data-v-5bfd9984]{color:#1878f3}.uc-login-ft-text[data-v-5bfd9984]{color:#aaa}.uc-login-ft-line[data-v-5bfd9984]{width:%?0?%;height:%?32?%;margin:0 %?50?%;border:%?2?% solid #ddd}.uc-login .phong-img[data-v-5bfd9984]{margin:0 %?40?%}.uc-login .questionid[data-v-5bfd9984]{border:0;border-bottom:%?2?% solid var(--qui-BOR-ED)}.uc-login .box[data-v-5bfd9984]{position:relative;z-index:2;width:100%;height:%?100?%;font-size:%?36?%;line-height:%?100?%;text-align:left;background:brown;border-bottom:%?2?% solid var(--qui-BOR-ED);box-sizing:border-box}.uc-login .box-sun[data-v-5bfd9984]{width:100%;height:%?100?%;font-size:%?36?%;font-weight:400;line-height:%?100?%;text-align:left;background:#fff;border-bottom:%?2?% solid var(--qui-BOR-ED);box-sizing:border-box}.uc-login .box-min[data-v-5bfd9984]{position:absolute;top:0;right:0;width:%?100?%;height:100%;line-height:50px;text-align:center;background:#fff}',""]),e.exports=t},"6f74":function(e,t,i){"use strict";var s=i("b95e");e.exports={computed:{user:function(){var e=this.$store.getters["session/get"]("userId");return e?this.$store.getters["jv/get"]("users/".concat(e)):{}}},methods:{getUserInfo:function(){var e=arguments.length>0&&void 0!==arguments[0]&&arguments[0],t=(new Date).getTime(),i=uni.getStorageSync(s.STORGE_GET_USER_TIME);if(e||(t-i)/1e3>60){var n={include:"groups,wechat"},r=this.$store.getters["session/get"]("userId");this.$store.commit("jv/deleteRecord",{_jv:{type:"users",id:r}}),this.$store.dispatch("jv/get",["users/".concat(r),{params:n}]).then((function(){return uni.$emit("updateNotiNum")})),uni.setStorageSync(s.STORGE_GET_USER_TIME,(new Date).getTime())}},logind:function(){var e=this,t=this.$store.getters["session/get"]("userId");if(t){this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]);this.$store.dispatch("jv/get",["users/".concat(t),{params:{include:"groups,wechat"}}]).then((function(t){e.$u.event.$emit("logind",t)})),this.$store.dispatch("forum/setError",{loading:!1})}}}}},9019:function(e,t,i){"use strict";var s=i("259e");i.d(t,"a",(function(){return s.a})),i.d(t,"b",(function(){return s.b})),i.d(t,"c",(function(){return s.c}))},b469:function(e,t){e.exports={computed:{forums:function(){return this.$store.getters["jv/get"]("forums/1")}}}},c46f:function(e,t,i){var s=i("416b");"string"==typeof s&&(s=[[e.i,s,""]]),s.locals&&(e.exports=s.locals);(0,i("4f06").default)("10df8cbf",s,!0,{sourceMap:!1,shadowMode:!1})},e25c:function(e,t,i){"use strict";i.r(t);var s=i("9019"),n=i("093c");for(var r in n)["default"].indexOf(r)<0&&function(e){i.d(t,e,(function(){return n[e]}))}(r);i("1e39");var o=i("f0c5"),a=Object(o.a)(n.default,s.b,s.c,!1,null,"5bfd9984",null,!1,s.a,void 0);t.default=a.exports},e2e6:function(e,t,i){"use strict";var s=i("4ea4");Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var n=s(i("6f74")),r=s(i("245f")),o={mixins:[n.default,r.default],data:function(){return{username:"",password:"",url:"",site_mode:"",isPaid:!1,forum:{},answer:"",value:"",sun:0,num:"",displays:!1,problem:[{key:0,value:this.i18n.t("user.safetyProblem")},{key:1,value:this.i18n.t("user.safetyProblem1")},{key:2,value:this.i18n.t("user.safetyProblem2")},{key:3,value:this.i18n.t("user.safetyProblem3")},{key:4,value:this.i18n.t("user.safetyProblem4")},{key:5,value:this.i18n.t("user.safetyProblem5")},{key:6,value:this.i18n.t("user.safetyProblem6")},{key:7,value:this.i18n.t("user.safetyProblem7")}]}},onLoad:function(){this.$store.dispatch("forum/setError",{code:"user_login",status:200})},methods:{loader:function(e){this.displays=0!==e,this.sun=e,this.num=!1},minBtn:function(){this.num=!this.num},handleLogin:function(){var e=this;if(this.username)if(this.password){var t={data:{attributes:{username:this.username,password:this.password,questionid:this.sun,answer:this.answer}}};this.$store.dispatch("session/ucLogin",t).then((function(t){if(t&&t.data&&t.data.errors&&"no_bind_user"===t.data.errors[0].code){var i={headimgurl:t.data.errors[0].user.headimgurl,username:t.data.errors[0].user.username||t.data.errors[0].user.nickname};uni.setStorageSync("token",t.data.errors[0].token),uni.setStorageSync("userInfo",i),uni.navigateTo({url:"/pages/user/register-bind"})}t&&t.data&&t.data.data&&t.data.data.attributes.access_token&&(e.logind(),uni.navigateTo({url:"/pages/home/index"}))})).catch((function(e){if(e&&e.data&&e.data.errors&&"no_bind_user"===e.data.errors[0].code){var t={headimgurl:e.data.errors[0].user.headimgurl,username:e.data.errors[0].user.username||e.data.errors[0].user.nickname};uni.setStorageSync("token",e.data.errors[0].token),uni.setStorageSync("userInfo",t),uni.navigateTo({url:"/pages/user/register-bind"})}}))}else uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3})}}};t.default=o},e972:function(e,t,i){e.exports=i.p+"static/img/msg-404.e11dc2d7.svg"}}]);