(window.webpackJsonp=window.webpackJsonp||[]).push([["pages-user-wechat"],{"1c4f":function(e,t,r){"use strict";(function(e){var s;r.d(t,"b",(function(){return i})),r.d(t,"c",(function(){return n})),r.d(t,"a",(function(){return s}));try{s={quiPage:r("29c4").default}}catch(t){if(-1===t.message.indexOf("Cannot find module")||-1===t.message.indexOf(".vue"))throw t;e.error(t.message),e.error("1. 排查组件名称拼写是否正确"),e.error("2. 排查组件是否符合 easycom 规范，文档：https://uniapp.dcloud.net.cn/collocation/pages?id=easycom"),e.error("3. 若组件不符合 easycom 规范，需手动引入，并在 components 中注册该组件")}var i=function(){var e=this.$createElement;return(this._self._c||e)("qui-page",{attrs:{"data-qui-theme":this.theme,header:!1}})},n=[]}).call(this,r("5a52").default)},"245f":function(e,t,r){"use strict";(function(t){var s=r("4ea4"),i=s(r("6f74")),n=r("b95e"),o=s(r("4c82"));e.exports={mixins:[i.default,o.default],methods:{getForum:function(){var e=this;this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(t){t&&(e.forum=t)}))},jump2PhoneLoginPage:function(){uni.redirectTo({url:"/pages/user/phone-login"})},jump2PhoneLoginRegisterPage:function(){uni.redirectTo({url:"/pages/user/phone-login-register"})},jump2LoginPage:function(){uni.redirectTo({url:"/pages/user/login"})},jump2RegisterPage:function(){uni.redirectTo({url:"/pages/user/register"})},jump2RegisterExtendPage:function(){uni.redirectTo({url:"/pages/user/supple-mentary"})},jump2LoginBindPage:function(){uni.redirectTo({url:"/pages/user/login-bind"})},jump2RegisterBindPage:function(){uni.redirectTo({url:"/pages/user/register-bind"})},jump2LoginBindPhonePage:function(){uni.redirectTo({url:"/pages/user/login-bind-phone"})},jump2RegisterBindPhonePage:function(){uni.redirectTo({url:"/pages/user/register-bind-phone"})},jump2findpwdPage:function(){uni.navigateTo({url:"/pages/modify/findpwd?pas=reset_pwd"})},mpLoginMode:function(){this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&this.jump2LoginPage(),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open())},h5LoginMode:function(){o.default.isWeixin().isWeixin?(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),this.$store.dispatch("session/wxh5Login"))):(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}))},refreshmpParams:function(){var e=this;uni.login({success:function(r){if("login:ok"===r.errMsg){var s=r.code;uni.getUserInfo({success:function(t){var r={data:{attributes:{js_code:s,iv:t.iv,encryptedData:t.encryptedData}}};e.$store.dispatch("session/setParams",r)},fail:function(e){t.log(e)}})}},fail:function(e){t.log(e)}})},mpLogin:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;uni.setStorageSync("register",e),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open()},wxh5Login:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;uni.setStorageSync("register",e),uni.setStorageSync("rebind",t),uni.setStorageSync("h5_wechat_login",1),this.$store.dispatch("session/wxh5Login")},getLoginParams:function(e,t){var r=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{var s=uni.getStorageSync("token");""!==s&&(r.data.attributes.token=s),this.login(r,t)}},getLoginBindParams:function(e,t){var r=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;this.refreshmpParams();var s=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{1===r&&(s.data.attributes.rebind=1);var i=uni.getStorageSync("token");""!==i&&(s.data.attributes.token=i),this.login(s,t)}},login:function(e,r){var s=this;this.$store.dispatch("session/h5Login",e).then((function(e){if(e&&e.data&&e.data.data&&e.data.data.id&&(s.logind(),s.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(e){e&&e.set_site&&e.set_site.site_mode!==n.SITE_PAY&&uni.getStorage({key:"page",success:function(e){t.log("resData",e),uni.redirectTo({url:e.data})}}),e&&e.set_site&&e.set_site.site_mode===n.SITE_PAY&&s.user&&!s.user.paid&&uni.redirectTo({url:"/pages/site/info"})})),uni.showToast({title:r,duration:2e3})),e&&e.data&&e.data.errors){if("401"===e.data.errors[0].status||"402"===e.data.errors[0].status||"500"===e.data.errors[0].status){var i=s.i18n.t("core.".concat(e.data.errors[0].code));uni.showToast({icon:"none",title:i,duration:2e3})}if("403"===e.data.errors[0].status||"422"===e.data.errors[0].status){var o=s.i18n.t("core.".concat(e.data.errors[0].code))||s.i18n.t(e.data.errors[0].detail[0]);uni.showToast({icon:"none",title:o,duration:2e3})}}})).catch((function(e){return t.log(e)}))}}}}).call(this,r("5a52").default)},"368d":function(e,t,r){e.exports=r.p+"static/img/msg-warning.f35ce51f.svg"},5980:function(e,t,r){"use strict";r.r(t);var s=r("a74b"),i=r.n(s);for(var n in s)["default"].indexOf(n)<0&&function(e){r.d(t,e,(function(){return s[e]}))}(n);t.default=i.a},"6f74":function(e,t,r){"use strict";var s=r("b95e");e.exports={computed:{user:function(){var e=this.$store.getters["session/get"]("userId");return e?this.$store.getters["jv/get"]("users/".concat(e)):{}}},methods:{getUserInfo:function(){var e=arguments.length>0&&void 0!==arguments[0]&&arguments[0],t=(new Date).getTime(),r=uni.getStorageSync(s.STORGE_GET_USER_TIME);if(e||(t-r)/1e3>60){var i={include:"groups,wechat"},n=this.$store.getters["session/get"]("userId");this.$store.commit("jv/deleteRecord",{_jv:{type:"users",id:n}}),this.$store.dispatch("jv/get",["users/".concat(n),{params:i}]).then((function(){return uni.$emit("updateNotiNum")})),uni.setStorageSync(s.STORGE_GET_USER_TIME,(new Date).getTime())}},logind:function(){var e=this,t=this.$store.getters["session/get"]("userId");if(t){this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]);this.$store.dispatch("jv/get",["users/".concat(t),{params:{include:"groups,wechat"}}]).then((function(t){e.$u.event.$emit("logind",t)})),this.$store.dispatch("forum/setError",{loading:!1})}}}}},a74b:function(e,t,r){"use strict";var s=r("4ea4");Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0;var i=s(r("3835")),n=s(r("6f74")),o=s(r("b469")),a=s(r("4c82")),u=s(r("245f")),d=r("b95e"),c={mixins:[n.default,o.default,a.default,u.default],data:function(){return{state:!0}},onLoad:function(e){this.wxLogin(e)},methods:{wxLogin:function(e){var t=this;this.$store.dispatch("session/noSenseh5Login",e).then((function(e){if(t.forums&&t.forums.set_site&&"1"===t.forums.set_site.open_ext_fields&&e&&e.new_user)t.jump2RegisterExtendPage();else if(e&&e.data&&e.data.data&&e.data.data.id){if(t.logind(),t.forums&&t.forums.set_site&&t.forums.set_site.site_mode!==d.SITE_PAY)uni.getStorage({key:"page",success:function(e){uni.redirectTo({url:e.data})},fail:function(){uni.redirectTo({url:"/pages/home/index"})}}),uni.getStorageSync("isBind")?uni.showToast({title:"绑定成功",duration:2e3}):uni.showToast({title:"登录成功",duration:2e3});else if(t.forums&&t.forums.set_site&&t.forums.set_site.site_mode===d.SITE_PAY&&t.user&&!t.user.paid){uni.redirectTo({url:"/pages/site/info"}),uni.getStorageSync("isBind")?uni.showToast({title:"绑定成功",duration:2e3}):uni.showToast({title:"登录成功",duration:2e3})}}else{if(e&&e.data&&e.data.errors){var r=(0,i.default)(e.data.errors,1)[0];if("no_bind_user"===e.data.errors[0].code){var s={headimgurl:e.data.errors[0].user.headimgurl,username:e.data.errors[0].user.nickname};return uni.setStorageSync("token",e.data.errors[0].token),uni.setStorageSync("userInfo",s),uni.setStorageSync("isBind",!0),t.forums&&t.forums.set_reg&&0===t.forums.set_reg.register_type&&t.jump2RegisterBindPage(),void(t.forums&&t.forums.set_reg&&1===t.forums.set_reg.register_type&&t.jump2RegisterBindPhonePage())}if("permission_denied"===e.data.errors[0].code)return void t.login();if("register_validate"===e.data.errors[0].code)return uni.showToast({icon:"none",title:t.i18n.t("core.register_validate"),duration:2e3}),t.$store.commit("session/SET_AUDIT_INFO",{errorCode:"register_validate",username:r.data.userName}),void uni.redirectTo({url:"/pages/user/warning"});if("validate_reject"===e.data.errors[0].code)return uni.showToast({icon:"none",title:t.i18n.t("core.validate_reject"),duration:2e3}),t.$store.commit("session/SET_AUDIT_INFO",{errorCode:"validate_reject",reason:r.data.rejectReason,username:r.data.userName}),void uni.redirectTo({url:"/pages/user/warning"});if("register_close"===e.data.errors[0].code)return uni.showToast({icon:"none",title:t.i18n.t("core.register_close"),duration:2e3}),void uni.redirectTo({url:"/pages/site/info"});if("ban_user"===e.data.errors[0].code)return uni.showToast({icon:"none",title:t.i18n.t("core.ban_user"),duration:2e3}),void t.$store.dispatch("forum/setError",{loading:!1,code:"ban_user"});if("account_has_been_bound"===e.data.errors[0].code)return void uni.getStorage({key:"page",success:function(e){uni.redirectTo({url:e.data}),setTimeout((function(){uni.showToast({icon:"none",title:"用户或微信已绑定其它，无法进行绑定",duration:2e3})}),1e3)}});if("rebind_mp_wechat"===e.data.errors[0].code)return uni.setStorageSync("token",e.data.errors[0].token),void uni.redirectTo({url:"/pages/user/login-bind"})}uni.redirectTo({url:"/"}),setTimeout((function(){uni.showToast({icon:"none",title:"未知错误".concat(e.data.errors[0].code),duration:2e3})}),500)}})).catch((function(e){uni.redirectTo({url:"/"}),setTimeout((function(){uni.showToast({icon:"none",title:e.message,duration:2e3})}),500)}))}}};t.default=c},b469:function(e,t){e.exports={computed:{forums:function(){return this.$store.getters["jv/get"]("forums/1")}}}},bf2e:function(e,t,r){"use strict";r.r(t);var s=r("c636"),i=r("5980");for(var n in i)["default"].indexOf(n)<0&&function(e){r.d(t,e,(function(){return i[e]}))}(n);var o=r("f0c5"),a=Object(o.a)(i.default,s.b,s.c,!1,null,null,null,!1,s.a,void 0);t.default=a.exports},c636:function(e,t,r){"use strict";var s=r("1c4f");r.d(t,"a",(function(){return s.a})),r.d(t,"b",(function(){return s.b})),r.d(t,"c",(function(){return s.c}))},e972:function(e,t,r){e.exports=r.p+"static/img/msg-404.3ba2611f.svg"}}]);