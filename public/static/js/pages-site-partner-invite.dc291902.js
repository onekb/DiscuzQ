(window.webpackJsonp=window.webpackJsonp||[]).push([["pages-site-partner-invite"],{"082f":function(t,e,i){var s=i("0e66");"string"==typeof s&&(s=[[t.i,s,""]]),s.locals&&(t.exports=s.locals);(0,i("4f06").default)("5ef39c25",s,!0,{sourceMap:!1,shadowMode:!1})},"0e66":function(t,e,i){(e=i("24fb")(!1)).push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */\n/* eg:\n  .container {\n    color: --color(BG-1);\n  }\n*/.site[data-v-f2558456] {padding-bottom:%?130?%}.site[data-v-f2558456] .header{height:auto;margin-bottom:%?30?%;background:var(--qui-BG-2);border-bottom:%?2?% solid var(--qui-BOR-ED)}.site[data-v-f2558456] .header .circleDet{padding:%?60?% %?30?%;opacity:1}.site[data-v-f2558456] .header .circleDet-txt{color:var(--qui-FC-34);opacity:.49}.site[data-v-f2558456] .header .logo{height:%?75?%;padding-top:%?71?%}.site[data-v-f2558456] .header .circleDet-num,\n.site[data-v-f2558456] .header .circleDet-share{color:var(--qui-FC-333)}.site[data-v-f2558456] .themeCount .themeItem__footer{display:none}.site[data-v-f2558456] .themeCount .addAsk{top:0}.site[data-v-f2558456] .themeCount .themeItem{padding-left:0;margin:0;border-top:none}.site[data-v-f2558456] .site-theme__last .themeItem{border-bottom:none}.site[data-v-f2558456] .site-submit .qui-button--button{position:absolute;top:%?20?%;right:%?24?%}.site-invite[data-v-f2558456]{padding:%?30?% %?60?%;text-align:center}.site-invite__detail__bold[data-v-f2558456]{margin:0 %?5?%;font-weight:700}.site-invite__detail[data-v-f2558456]{font-size:%?30?%}.site-submit[data-v-f2558456]{position:fixed;bottom:0;z-index:100;width:100%;height:%?130?%;padding:%?20?% %?24?%;background:var(--qui-BG-2);box-shadow:%?0?% %?-3?% %?6?% rgba(0,0,0,.05);box-sizing:border-box}.site-submit__price[data-v-f2558456]{margin-top:%?10?%;font-size:%?36?%;color:var(--qui-FC-AAA);text-decoration:line-through}.site-submit__price__pay[data-v-f2558456]{margin-top:%?10?%;font-size:%?36?%;color:var(--qui-RED)}.site-submit__expire[data-v-f2558456]{font-size:%?26?%;color:var(--qui-FC-333)}',""]),t.exports=e},"185d":function(t,e,i){t.exports=i.p+"static/img/auth.51e40f27.svg"},"1cb5":function(t,e,i){t.exports=i.p+"static/img/yihuida.894c0306.svg"},"245f":function(t,e,i){"use strict";(function(e){var s=i("4ea4").default,n=s(i("6f74")),a=i("b95e"),r=s(i("4c82"));t.exports={mixins:[n.default,r.default],methods:{getForum:function(){var t=this;this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(e){e&&(t.forum=e)}))},jump2PhoneLoginPage:function(){uni.redirectTo({url:"/pages/user/phone-login"})},jump2PhoneLoginRegisterPage:function(){uni.redirectTo({url:"/pages/user/phone-login-register"})},jump2LoginPage:function(){uni.navigateTo({url:"/pages/user/login"})},jump2RegisterPage:function(){uni.redirectTo({url:"/pages/user/register"})},jump2RegisterExtendPage:function(){uni.redirectTo({url:"/pages/user/supple-mentary"})},jump2LoginBindPage:function(){uni.redirectTo({url:"/pages/user/login-bind"})},jump2RegisterBindPage:function(){uni.redirectTo({url:"/pages/user/register-bind"})},jump2LoginBindPhonePage:function(){uni.redirectTo({url:"/pages/user/login-bind-phone"})},jump2RegisterBindPhonePage:function(){uni.redirectTo({url:"/pages/user/register-bind-phone"})},jump2findpwdPage:function(){uni.navigateTo({url:"/pages/modify/findpwd?pas=reset_pwd"})},mpLoginMode:function(){this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&this.jump2LoginPage(),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open())},h5LoginMode:function(){r.default.isWeixin().isWeixin?(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),this.$store.dispatch("session/wxh5Login"))):(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}))},refreshmpParams:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:function(){};t()},mpLogin:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;uni.setStorageSync("register",t),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open()},wxh5Login:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;uni.setStorageSync("register",t),uni.setStorageSync("rebind",e),uni.setStorageSync("h5_wechat_login",1),this.$store.dispatch("session/wxh5Login")},getLoginParams:function(t,e){var i=t;if(""===t.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===t.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{var s=uni.getStorageSync("token");""!==s&&(i.data.attributes.token=s),this.login(i,e)}},getLoginBindParams:function(t,e){var i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;this.refreshmpParams();var s=t;if(""===t.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===t.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{1===i&&(s.data.attributes.rebind=1);var n=uni.getStorageSync("token");""!==n&&(s.data.attributes.token=n),this.login(s,e)}},login:function(t,i){var s=this;this.$store.dispatch("session/h5Login",t).then((function(t){if(t&&t.data&&t.data.data&&t.data.data.id&&(s.logind(),s.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(t){t&&t.set_site&&t.set_site.site_mode!==a.SITE_PAY&&uni.getStorage({key:"page",success:function(t){uni.redirectTo({url:t.data})}}),t&&t.set_site&&t.set_site.site_mode===a.SITE_PAY&&s.user&&!s.user.paid&&uni.redirectTo({url:"/pages/site/info"})})),uni.showToast({title:i,duration:2e3})),t&&t.data&&t.data.errors){if("401"===t.data.errors[0].status||"402"===t.data.errors[0].status||"500"===t.data.errors[0].status){var e=s.i18n.t("core.".concat(t.data.errors[0].code));uni.showToast({icon:"none",title:e,duration:2e3})}if("403"===t.data.errors[0].status||"422"===t.data.errors[0].status){var n=s.i18n.t("core.".concat(t.data.errors[0].code))||s.i18n.t(t.data.errors[0].detail[0]);uni.showToast({icon:"none",title:n,duration:2e3})}}})).catch((function(t){return e.log(t)}))}}}}).call(this,i("5a52").default)},"2c05":function(t,e,i){"use strict";var s=i("082f");i.n(s).a},"35ab":function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.default=function(t){for(var e=0,i=0;i<t.length;i++)e+=t.charCodeAt(i);var s=function(t,e,i){var s,n,a,r=Math.floor(6*t),o=6*t-r,u=i*(1-e),c=i*(1-o*e),d=i*(1-(1-o)*e);switch(r%6){case 0:s=i,n=d,a=u;break;case 1:s=c,n=i,a=u;break;case 2:s=u,n=i,a=d;break;case 3:s=u,n=c,a=i;break;case 4:s=d,n=u,a=i;break;case 5:s=i,n=u,a=c}return{r:Math.floor(255*s),g:Math.floor(255*n),b:Math.floor(255*a)}}(e%360/360,.3,.9);return""+s.r.toString(16)+s.g.toString(16)+s.b.toString(16)},i("d3b7"),i("25f0")},"368d":function(t,e,i){t.exports=i.p+"static/img/msg-warning.f35ce51f.svg"},"5b5f":function(t,e,i){"use strict";var s=i("4ea4").default;i("fb6a"),i("99af"),i("ac1f"),i("5319"),i("d3b7"),i("25f0"),i("1276");var n=s(i("53ca")),a=i("b95e"),r=i("ce40");t.exports={methods:{wxShare:function(t){var e=this.getUrl(),i=this.$store.getters["jv/get"]("forums/1");i.passport&&!i.passport.offiaccount_close||this.$store.dispatch("jv/get",["offiaccount/jssdk?url=".concat(encodeURIComponent(e)),{}]).then((function(s){var n=s.appId,a=s.nonceStr,o=s.signature,u=s.timestamp;r.config({debug:!1,appId:n,timestamp:u,nonceStr:a,signature:o,jsApiList:["updateAppMessageShareData","updateTimelineShareData","hideMenuItems","showMenuItems"]}),r.ready((function(){var s={title:t.title||"Discuz!Q",desc:t.desc||i.set_site.site_introduction,link:e,imgUrl:t.logo||i.set_site.site_favicon};r.updateAppMessageShareData(s),r.updateTimelineShareData(s)}))}))},h5Share:function(t){var e="";switch((0,n.default)(t)){case"undefined":e="Discuz!Q";break;case"string":e=t;break;default:e=t.title||"Discuz!Q"}var i=t.id?"?id=".concat(t.id):"",s="";if("pages/home/index"===t.url)s="".concat(a.DISCUZ_REQUEST_HOST);else{var r=t.url;t.url&&/^\/.*/.test(t.url)&&(r=t.url.slice(1)),s="".concat(a.DISCUZ_REQUEST_HOST).concat(r).concat(i)}var o=document.createElement("input");e=(e=(e=(e=e.toString().replace(/<img(?:.|\s)*?>/g,"")).toString().replace(/(<\/?br.*?>)/gi,"")).toString().replace(/(<\/?p.*?>)/gi,"")).toString().replace(/\s+/g,""),e="".concat(e.substring(0,17)),o.value="".concat(e,"  ").concat(s),document.body.appendChild(o),o.select(),o.readOnly=!0,o.id="copyInp",document.execCommand("Copy"),o.setAttribute("onfocus",this.copyFocus(o)),o.className="oInput",o.style.display="none",uni.showToast({icon:"none",title:"分享链接已复制成功"})},copyFocus:function(t){t.blur(),document.body.removeChild(t)},getUrl:function(){var t=/iPad|iPhone|iPod/.test(navigator.userAgent),e=window.location.href.split("#")[0];return t&&window.entryUrl&&!/wechatdevtools/.test(navigator.userAgent)&&(e=window.entryUrl),e}}}},"5df8":function(t,e){t.exports="data:image/png;base64,iVBORw0KGgoAAAANSUhEUgAAACQAAAAqCAMAAADs1AnaAAAARVBMVEVHcEybBAT/UFD6UFD6UVH6UFD/UFD6UVH7UFCYAAD6UFD6UVH9oaH/////5ub7ZWX7ior/8PD+19f7e3v9tbX8kpL+yMjhHSfzAAAAC3RSTlMAjiCg630QwED2YJ4QxbcAAAEbSURBVDjLzdPbkoMgDAZgWrXaJSEcAu//qJugbuuI2rvuf5Fx9NNkAI2R3Oxpxmqe1+j2vEZiLpGaD9oZM30JQVouUjlGDpaLjAeIWVEAJwmo1dEO5SDII2VJQa15/yXC5KAUm7z3BaV4aswU2QF6y0GCWqCBSNrRxeAZIBSAHCUOtUa/QyVGdDEmeIUa6+RRpyC/pjW4jajvch1ah08N5OWprB87uy5JawmKg+iIsdTEFkpIsgSOOcxTcwvFonun7agmtVCg+RQwrmm1W47K6ZfsH6pbZAHpHHntFtsnk3WzvEg9VOl//FLfQON07/pz1E165zH0x6i7myVvbIteZMPeUD9sSM1Pv0H98DCtKFvQEakPxxkdi5r7L8zHODncoK0UAAAAAElFTkSuQmCC"},"6d8f":function(t,e,i){"use strict";var s=i("b816");i.d(e,"a",(function(){return s.a})),i.d(e,"b",(function(){return s.b})),i.d(e,"c",(function(){return s.c}))},"6f74":function(t,e,i){"use strict";var s=i("b95e");t.exports={computed:{user:function(){var t=this.$store.getters["session/get"]("userId");return t?this.$store.getters["jv/get"]("users/".concat(t)):{}}},methods:{getUserInfo:function(){var t=arguments.length>0&&void 0!==arguments[0]&&arguments[0],e=(new Date).getTime(),i=uni.getStorageSync(s.STORGE_GET_USER_TIME);if(t||(e-i)/1e3>60){var n={include:"groups,wechat"},a=this.$store.getters["session/get"]("userId");this.$store.commit("jv/deleteRecord",{_jv:{type:"users",id:a}}),this.$store.dispatch("jv/get",["users/".concat(a),{params:n}]).then((function(){return uni.$emit("updateNotiNum")})),uni.setStorageSync(s.STORGE_GET_USER_TIME,(new Date).getTime())}},logind:function(){var t=this,e=this.$store.getters["session/get"]("userId");if(e){this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]);this.$store.dispatch("jv/get",["users/".concat(e),{params:{include:"groups,wechat"}}]).then((function(e){t.$u.event.$emit("logind",e)})),this.$store.dispatch("forum/setError",{loading:!1})}}}}},"864e":function(t,e,i){"use strict";var s=i("4ea4").default;Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0,i("ac1f"),i("5319"),i("99af");var n=s(i("9558")),a={topic:function(t){var e=(0,n.default)(/<[s\u017F]pan[\t-\r \xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000\uFEFF]*id="topic"[\t-\r \xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000\uFEFF]*value="([0-9A-Z_a-z\u017F\u212A]+)"[\t-\r \xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000\uFEFF]*>((?:(?!<)[\s\S])+)<\/[s\u017F]pan>/gim,{value:1,string:2});return t&&t.replace(e,(function(t){return t.replace(e,(function(t,e,i){var s="/pages/topic/content?id=".concat(e);return'<a href="'.concat(s,'" class="content-topic">').concat(i,"</a> ")}))}))},usermention:function(t){var e=(0,n.default)(/<[s\u017F]pan[\t-\r \xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000\uFEFF]*id="member"[\t-\r \xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000\uFEFF]*value="([0-9A-Z_a-z\u017F\u212A]+)"[\t-\r \xA0\u1680\u2000-\u200A\u2028\u2029\u202F\u205F\u3000\uFEFF]*>((?:(?!<)[\s\S])+)<\/[s\u017F]pan>/gim,{value:1,string:2});return t&&t.replace(e,(function(t){return t.replace(e,(function(t,e,i){var s="/pages/profile/index?userId=".concat(e);return'<a href="'.concat(s,'" class="content-member">').concat(i,"</a> ")}))}))},attachment:function(t,e){return t&&t.replace(/\[attach\](.*?)\[\/attach\]/g,(function(t,i){var s=e.$store.getters["jv/get"]("attachments/".concat(i));return s.url&&(t='<a href="'.concat(s.url,'" class="content-attachment">').concat(s.attachment,"</a>")),t}))}};var r={parse:function(t,e){if(t){for(var i in a)t=a[i](t,e);return t}}};e.default=r},a223:function(t,e,i){"use strict";i.r(e);var s=i("a2d3"),n=i.n(s);for(var a in s)["default"].indexOf(a)<0&&function(t){i.d(e,t,(function(){return s[t]}))}(a);e.default=n.a},a2d3:function(t,e,i){"use strict";var s=i("4ea4").default;Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=i("4b36"),a=s(i("b469")),r=s(i("840a")),o=s(i("245f")),u={components:{uniPopupDialog:r.default},mixins:[a.default,o.default],data:function(){return{code:"",inviteData:{},codeTips:""}},onLoad:function(t){this.code=t.code,this.getInviteInfo(t.code)},onShareAppMessage:function(t){return t.from,{title:this.forums.set_site.site_name}},onShareTimeline:function(){return{title:this.forums.set_site.site_name,query:"code=".concat(this.code)}},methods:{check:function(){if(this.code&&32!==this.code.length)this.inviteData.id?this.submit():(this.codeTips=this.i18n.t("site.codenotfound"),this.$refs.popCode.open());else switch(this.inviteData.status||0===this.inviteData.status?this.inviteData.status:"error"){case 0:this.codeTips=this.i18n.t("site.codeinvalid"),this.$refs.popCode.open();break;case 1:this.submit();break;case 2:this.codeTips=this.i18n.t("site.codeused"),this.$refs.popCode.open();break;case 3:this.codeTips=this.i18n.t("site.codeexpired"),this.$refs.popCode.open();break;case"error":this.codeTips=this.i18n.t("site.codenotfound"),this.$refs.popCode.open();break;default:return""}},getInviteInfo:function(t){var e=this;n.status.run((function(){return e.$store.dispatch("jv/get","invite/".concat(t))})).then((function(t){e.inviteData=t}))},handleInviteCancel:function(){this.$refs.popCode.close()},handleInviteOk:function(){this.$refs.popCode.close(),this.submit()},close:function(){this.$refs.auth.close()},submit:function(){this.$store.getters["session/get"]("isLogin")?2===this.forums.set_reg.register_type&&!0===this.isWeixin?uni.navigateTo({url:"/pages/home/index"}):this.$refs.toast.show({message:this.i18n.t("site.codeforbid")}):(uni.setStorage({key:"page",data:"/pages/home/index"}),uni.setStorageSync("inviteCode",this.code),this.h5LoginMode())}}};e.default=u},b11f:function(t,e,i){"use strict";i.r(e);var s=i("6d8f"),n=i("a223");for(var a in n)["default"].indexOf(a)<0&&function(t){i.d(e,t,(function(){return n[t]}))}(a);i("2c05");var r=i("f0c5"),o=Object(r.a)(n.default,s.b,s.c,!1,null,"f2558456",null,!1,s.a,void 0);e.default=o.exports},b469:function(t,e){t.exports={computed:{forums:function(){return this.$store.getters["jv/get"]("forums/1")}}}},b816:function(t,e,i){"use strict";(function(t){var s;i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return a})),i.d(e,"a",(function(){return s}));try{s={quiPage:i("29c4").default,quiSiteThread:i("d6c1").default,quiButton:i("8397").default,uniPopup:i("1c89").default,quiToast:i("2039").default}}catch(e){if(-1===e.message.indexOf("Cannot find module")||-1===e.message.indexOf(".vue"))throw e;t.error(e.message),t.error("1. 排查组件名称拼写是否正确"),t.error("2. 排查组件是否符合 easycom 规范，文档：https://uniapp.dcloud.net.cn/collocation/pages?id=easycom"),t.error("3. 若组件不符合 easycom 规范，需手动引入，并在 components 中注册该组件")}var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("qui-page",{staticClass:"site",attrs:{"data-qui-theme":t.theme,header:!1}},[i("qui-site-thread",{attrs:{"share-url":"/pages/site/partner-invite?code="+t.code}}),i("v-uni-view",{staticClass:"site-invite"},[i("v-uni-view",{staticClass:"site-invite__detail"},[32!==t.code.length?i("v-uni-text",{staticClass:"site-invite__detail__bold"},[t._v(t._s(t.inviteData.username||""))]):i("v-uni-text",{staticClass:"site-invite__detail__bold"},[t._v(t._s(t.inviteData.user?t.inviteData.user.username:""))]),i("v-uni-text",[t._v(t._s(t.i18n.t("site.inviteyouas")))]),i("v-uni-text",{staticClass:"site-invite__detail__bold"},[t._v(t._s("[ "+(t.inviteData.group&&t.inviteData.group.name)+" ]"))]),i("v-uni-text",[t._v(t._s(t.i18n.t("site.join")))]),i("v-uni-text",{staticClass:"site-invite__detail__bold"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_name))]),i("v-uni-text",[t._v(t._s(t.i18n.t("site.site")))])],1)],1),i("v-uni-view",{staticClass:"site-submit"},[i("v-uni-view",[i("v-uni-view",{class:32!==t.code.length?"site-submit__price__pay":"site-submit__price"},[t._v(t._s("¥"+(t.forums.set_site&&t.forums.set_site.site_price||0)))]),i("v-uni-view",{staticClass:"site-submit__expire"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_expire?t.i18n.t("site.periodvalidity")+" "+(t.forums.set_site&&t.forums.set_site.site_expire)+" "+t.i18n.t("site.day"):t.i18n.t("site.permanent")))])],1),i("qui-button",{attrs:{type:"primary",size:"small"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.check.apply(void 0,arguments)}}},[t._v(t._s(t.i18n.t("site.accepttheinvitation")))])],1),i("uni-popup",{ref:"popCode",attrs:{type:"center"}},[i("uni-popup-dialog",{attrs:{type:"warn","before-close":!0,content:t.codeTips},on:{close:function(e){arguments[0]=e=t.$handleEvent(e),t.handleInviteCancel.apply(void 0,arguments)},confirm:function(e){arguments[0]=e=t.$handleEvent(e),t.handleInviteOk.apply(void 0,arguments)}}})],1),i("qui-toast",{ref:"toast"})],1)},a=[]}).call(this,i("5a52").default)},cadb:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e.timestamp2day=e.time2DateAndHM=e.time2MinuteOrHour=void 0,i("e25e"),i("ac1f"),i("5319"),i("99af");e.time2MinuteOrHour=function(t){var e=new Date-new Date(t);return parseInt(parseInt(e/1e3,0)/60,0)<60?"".concat(Math.ceil(e/1e3/60),"分钟前"):parseInt(parseInt(parseInt(e/1e3,0)/60,0)/60,0)<16?"".concat(Math.ceil(e/1e3/60/60),"小时前"):t.replace(/T/," ").replace(/Z/,"").substring(0,16)};e.time2DateAndHM=function(t){var e=t.replace(/T/," ").replace(/Z/,"");return"".concat(e.substring(0,10)," ").concat(e.substring(11,16))};e.timestamp2day=function(t){var e=t-Math.round(new Date/1e3);return parseInt(e/86400,0)}},e972:function(t,e,i){t.exports=i.p+"static/img/msg-404.3ba2611f.svg"}}]);