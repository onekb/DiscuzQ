(window.webpackJsonp=window.webpackJsonp||[]).push([["pages-my-profile"],{"185d":function(e,t,i){e.exports=i.p+"static/img/auth.51e40f27.svg"},"245f":function(e,t,i){"use strict";(function(t){var s=i("4ea4").default,n=s(i("6f74")),o=i("b95e"),r=s(i("4c82"));e.exports={mixins:[n.default,r.default],methods:{getForum:function(){var e=this;this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(t){t&&(e.forum=t)}))},jump2PhoneLoginPage:function(){uni.redirectTo({url:"/pages/user/phone-login"})},jump2PhoneLoginRegisterPage:function(){uni.redirectTo({url:"/pages/user/phone-login-register"})},jump2LoginPage:function(){uni.navigateTo({url:"/pages/user/login"})},jump2RegisterPage:function(){uni.redirectTo({url:"/pages/user/register"})},jump2RegisterExtendPage:function(){uni.redirectTo({url:"/pages/user/supple-mentary"})},jump2LoginBindPage:function(){uni.redirectTo({url:"/pages/user/login-bind"})},jump2RegisterBindPage:function(){uni.redirectTo({url:"/pages/user/register-bind"})},jump2LoginBindPhonePage:function(){uni.redirectTo({url:"/pages/user/login-bind-phone"})},jump2RegisterBindPhonePage:function(){uni.redirectTo({url:"/pages/user/register-bind-phone"})},jump2findpwdPage:function(){uni.navigateTo({url:"/pages/modify/findpwd?pas=reset_pwd"})},mpLoginMode:function(){this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&this.jump2LoginPage(),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open())},h5LoginMode:function(){r.default.isWeixin().isWeixin?(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),this.$store.dispatch("session/wxh5Login"))):(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}))},refreshmpParams:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:function(){},i=this;uni.login({success:function(t){if("login:ok"===t.errMsg){var s={data:{attributes:{js_code:t.code}}};i.$store.dispatch("session/setParams",s),e()}},fail:function(e){t.log(e)}})},mpLogin:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;uni.setStorageSync("register",e),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open()},wxh5Login:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;uni.setStorageSync("register",e),uni.setStorageSync("rebind",t),uni.setStorageSync("h5_wechat_login",1),this.$store.dispatch("session/wxh5Login")},getLoginParams:function(e,t){var i=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{var s=uni.getStorageSync("token");""!==s&&(i.data.attributes.token=s),this.login(i,t)}},getLoginBindParams:function(e,t){var i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;this.refreshmpParams();var s=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{1===i&&(s.data.attributes.rebind=1);var n=uni.getStorageSync("token");""!==n&&(s.data.attributes.token=n),this.login(s,t)}},login:function(e,i){var s=this;this.$store.dispatch("session/h5Login",e).then((function(e){if(e&&e.data&&e.data.data&&e.data.data.id&&(s.logind(),s.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(e){e&&e.set_site&&e.set_site.site_mode!==o.SITE_PAY&&uni.getStorage({key:"page",success:function(e){uni.redirectTo({url:e.data})}}),e&&e.set_site&&e.set_site.site_mode===o.SITE_PAY&&s.user&&!s.user.paid&&uni.redirectTo({url:"/pages/site/info"})})),uni.showToast({title:i,duration:2e3})),e&&e.data&&e.data.errors){if("401"===e.data.errors[0].status||"402"===e.data.errors[0].status||"500"===e.data.errors[0].status){var t=s.i18n.t("core.".concat(e.data.errors[0].code));uni.showToast({icon:"none",title:t,duration:2e3})}if("403"===e.data.errors[0].status||"422"===e.data.errors[0].status){var n=s.i18n.t("core.".concat(e.data.errors[0].code))||s.i18n.t(e.data.errors[0].detail[0]);uni.showToast({icon:"none",title:n,duration:2e3})}}})).catch((function(e){return t.log(e)}))}}}}).call(this,i("5a52").default)},"35ab":function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=function(e){for(var t=0,i=0;i<e.length;i++)t+=e.charCodeAt(i);var s=function(e,t,i){var s,n,o,r=Math.floor(6*e),a=6*e-r,u=i*(1-t),c=i*(1-a*t),d=i*(1-(1-a)*t);switch(r%6){case 0:s=i,n=d,o=u;break;case 1:s=c,n=i,o=u;break;case 2:s=u,n=i,o=d;break;case 3:s=u,n=c,o=i;break;case 4:s=d,n=u,o=i;break;case 5:s=i,n=u,o=c}return{r:Math.floor(255*s),g:Math.floor(255*n),b:Math.floor(255*o)}}(t%360/360,.3,.9);return""+s.r.toString(16)+s.g.toString(16)+s.b.toString(16)},i("d3b7"),i("25f0")},3678:function(e,t,i){(t=i("24fb")(!1)).push([e.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */\n/* eg:\n  .container {\n    color: --color(BG-1);\n  }\n*/.profile[data-v-48e3af7b] {overflow:hidden}.profile[data-v-48e3af7b] .my-profile{position:relative;padding-top:%?20?%;padding-left:%?40?%;background:var(--qui-BG-2);border-bottom:%?2?% solid var(--qui-BOR-ED);box-sizing:border-box}.profile[data-v-48e3af7b] .cell-item{padding-right:%?40?%}.profile[data-v-48e3af7b] .cell-item__body__content-title{color:var(--qui-FC-777)}.profile[data-v-48e3af7b] .cell-item__body__right{color:var(--qui-FC-333)}.profile[data-v-48e3af7b] .qui-uploader-box{display:none}.profile[data-v-48e3af7b] .no-arrow .arrow{visibility:hidden}.my-profile__avatar[data-v-48e3af7b]{position:absolute;top:%?13?%;top:%?20?%;right:%?44?%;right:%?30?%;width:%?75?%;height:%?75?%}[data-v-48e3af7b] .cell-item__body__right-text{font-family:PingFang SC;font-size:%?26?%;font-weight:400}[data-v-48e3af7b] .cell-item__body__content-title{font-family:PingFang SC;font-size:%?26?%;font-weight:400;color:#666}',""]),e.exports=t},"368d":function(e,t,i){e.exports=i.p+"static/img/msg-warning.f35ce51f.svg"},4592:function(e,t,i){"use strict";i.r(t);var s=i("ff7f"),n=i.n(s);for(var o in s)["default"].indexOf(o)<0&&function(e){i.d(t,e,(function(){return s[e]}))}(o);t.default=n.a},"6f74":function(e,t,i){"use strict";var s=i("b95e");e.exports={computed:{user:function(){var e=this.$store.getters["session/get"]("userId");return e?this.$store.getters["jv/get"]("users/".concat(e)):{}}},methods:{getUserInfo:function(){var e=arguments.length>0&&void 0!==arguments[0]&&arguments[0],t=(new Date).getTime(),i=uni.getStorageSync(s.STORGE_GET_USER_TIME);if(e||(t-i)/1e3>60){var n={include:"groups,wechat"},o=this.$store.getters["session/get"]("userId");this.$store.commit("jv/deleteRecord",{_jv:{type:"users",id:o}}),this.$store.dispatch("jv/get",["users/".concat(o),{params:n}]).then((function(){return uni.$emit("updateNotiNum")})),uni.setStorageSync(s.STORGE_GET_USER_TIME,(new Date).getTime())}},logind:function(){var e=this,t=this.$store.getters["session/get"]("userId");if(t){this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]);this.$store.dispatch("jv/get",["users/".concat(t),{params:{include:"groups,wechat"}}]).then((function(t){e.$u.event.$emit("logind",t)})),this.$store.dispatch("forum/setError",{loading:!1})}}}}},b469:function(e,t){e.exports={computed:{forums:function(){return this.$store.getters["jv/get"]("forums/1")}}}},b690:function(e,t,i){"use strict";i.r(t);var s=i("fe74"),n=i("4592");for(var o in n)["default"].indexOf(o)<0&&function(e){i.d(t,e,(function(){return n[e]}))}(o);i("dbaf");var r=i("f0c5"),a=Object(r.a)(n.default,s.b,s.c,!1,null,"48e3af7b",null,!1,s.a,void 0);t.default=a.exports},bdae:function(e,t,i){var s=i("3678");"string"==typeof s&&(s=[[e.i,s,""]]),s.locals&&(e.exports=s.locals);(0,i("4f06").default)("d131c8d2",s,!0,{sourceMap:!1,shadowMode:!1})},dbaf:function(e,t,i){"use strict";var s=i("bdae");i.n(s).a},e6ed:function(e,t,i){"use strict";(function(e){var s;i.d(t,"b",(function(){return n})),i.d(t,"c",(function(){return o})),i.d(t,"a",(function(){return s}));try{s={quiPage:i("29c4").default,quiCellItem:i("e0ca").default,quiAvatar:i("da98").default,quiUploader:i("c012").default,quiToast:i("2039").default,uniPopup:i("1c89").default}}catch(t){if(-1===t.message.indexOf("Cannot find module")||-1===t.message.indexOf(".vue"))throw t;e.error(t.message),e.error("1. 排查组件名称拼写是否正确"),e.error("2. 排查组件是否符合 easycom 规范，文档：https://uniapp.dcloud.net.cn/collocation/pages?id=easycom"),e.error("3. 若组件不符合 easycom 规范，需手动引入，并在 components 中注册该组件")}var n=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("qui-page",{staticClass:"profile",attrs:{"data-qui-theme":e.theme}},[i("v-uni-view",{staticClass:"my-profile"},[e.profile.canEditUsername?i("v-uni-navigator",{attrs:{url:"/pages/modify/editusername?id="+e.userId,"hover-class":"none"}},[i("qui-cell-item",{attrs:{title:e.i18n.t("profile.username"),arrow:!0,addon:e.profile.username}})],1):e._e(),e.profile.canEditUsername?e._e():i("qui-cell-item",{staticClass:"no-arrow",attrs:{title:e.i18n.t("profile.username"),arrow:!0,addon:e.profile.username}}),i("qui-cell-item",{attrs:{title:e.i18n.t("profile.avatar"),"slot-right":!0,arrow:!0},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.changeAvatar.apply(void 0,arguments)}}},[i("qui-avatar",{staticClass:"my-profile__avatar",attrs:{user:e.profile,"is-real":e.profile.isReal,size:60}})],1),e.forums.qcloud&&e.forums.qcloud.qcloud_sms?i("qui-cell-item",{attrs:{title:e.i18n.t("profile.mobile"),arrow:!0,addon:e.profile.mobile?e.profile.mobile:e.i18n.t("profile.bindingmobile")},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.bindPhone.apply(void 0,arguments)}}}):e._e(),i("v-uni-navigator",{attrs:{url:e.profile.hasPassword?"/pages/modify/editpwd?id="+e.userId:"/pages/modify/newpwd?id="+e.userId,"hover-class":"none"}},[i("qui-cell-item",{attrs:{title:e.i18n.t("profile.password"),arrow:!0,addon:e.profile.hasPassword?e.i18n.t("profile.modify"):e.i18n.t("profile.setpassword")}})],1),e.forums.passport&&e.forums.passport.offiaccount_close?i("qui-cell-item",{attrs:{title:e.i18n.t("profile.wechat"),addon:e.name,arrow:!0},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.bindWechat.apply(void 0,arguments)}}}):e._e(),e.profile.realname&&e.forums.qcloud&&e.forums.qcloud.qcloud_faceid?i("qui-cell-item",{staticClass:"no-arrow",attrs:{title:e.i18n.t("profile.certification"),addon:e.profile.realname,arrow:!0}}):e._e(),!e.profile.realname&&e.forums.qcloud&&e.forums.qcloud.qcloud_faceid?i("v-uni-navigator",{attrs:{url:"/pages/modify/realname?id="+e.userId,"hover-class":"none"}},[i("qui-cell-item",{attrs:{title:e.i18n.t("profile.certification"),arrow:!0,addon:e.i18n.t("profile.tocertification")}})],1):e._e(),i("v-uni-navigator",{attrs:{url:"/pages/modify/signature?id="+e.userId,"hover-class":"none"}},[i("qui-cell-item",{attrs:{title:e.i18n.t("profile.signature"),arrow:!0,addon:e.i18n.t("profile.modify"),border:!1}})],1),i("qui-uploader",{ref:"upload",attrs:{url:e.host+"api/users/"+e.userId+"/avatar",header:e.header,"form-data":e.formData,"async-clear":!0,count:"1",name:"avatar"},on:{uploadSuccess:function(t){arguments[0]=t=e.$handleEvent(t),e.uploadSuccess.apply(void 0,arguments)},uploadFail:function(t){arguments[0]=t=e.$handleEvent(t),e.uploadFail.apply(void 0,arguments)},chooseSuccess:function(t){arguments[0]=t=e.$handleEvent(t),e.chooseSuccess.apply(void 0,arguments)}}}),i("qui-toast",{ref:"toast"})],1),i("uni-popup",{ref:"noBind",attrs:{type:"center"}},[i("uni-popup-dialog",{attrs:{type:"warn",content:e.i18n.t("user.noBindTips"),"before-close":!0},on:{close:function(t){arguments[0]=t=e.$handleEvent(t),e.closeNoBind.apply(void 0,arguments)},confirm:function(t){arguments[0]=t=e.$handleEvent(t),e.clickNoBind.apply(void 0,arguments)}}})],1),i("uni-popup",{ref:"changeBind",attrs:{type:"center"}},[i("uni-popup-dialog",{attrs:{type:"warn",content:e.i18n.t("user.changeBindTips"),"before-close":!0},on:{close:function(t){arguments[0]=t=e.$handleEvent(t),e.closeChangeBind.apply(void 0,arguments)},confirm:function(t){arguments[0]=t=e.$handleEvent(t),e.clickChangeBind.apply(void 0,arguments)}}})],1)],1)},o=[]}).call(this,i("5a52").default)},e972:function(e,t,i){e.exports=i.p+"static/img/msg-404.3ba2611f.svg"},fe74:function(e,t,i){"use strict";var s=i("e6ed");i.d(t,"a",(function(){return s.a})),i.d(t,"b",(function(){return s.b})),i.d(t,"c",(function(){return s.c}))},ff7f:function(e,t,i){"use strict";var s=i("4ea4").default;Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0,i("c975");var n=i("b95e"),o=s(i("b469")),r=s(i("245f")),a=s(i("840a")),u=i("06a1"),c=s(i("4c82")),d={components:{uniPopupDialog:a.default},mixins:[o.default,r.default,c.default],data:function(){return{hasPassword:!1,header:{},formData:{},profile:{},name:"",show:!1,host:n.DISCUZ_REQUEST_HOST,userId:this.$store.getters["session/get"]("userId"),isWeixin:!1}},onShow:function(){this.getUserInfo()},onLoad:function(){var e=uni.getStorageSync("access_token");this.header={authorization:"Bearer ".concat(e)},this.formData={type:1};var t=c.default.isWeixin().isWeixin;this.isWeixin=t},methods:{bindPhone:function(){this.profile&&""!==this.profile.mobile?uni.navigateTo({url:"/pages/modify/mobile?id=".concat(this.userId)}):uni.navigateTo({url:"/pages/modify/setphon?id=".concat(this.userId)})},bindWechat:function(){if("绑定"===this.name)return uni.setStorage({key:"page",data:(0,u.getCurUrl)()}),void(this.isWeixin?this.wxh5Login(0,0):uni.showToast({icon:"none",title:this.i18n.t("profile.wechatTip"),duration:2e3}));"绑定"!==this.name&&this.name.indexOf("解绑")>-1&&this.$refs.noBind.open(),"绑定"!==this.name&&this.name.indexOf("换绑")>-1&&this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorage({key:"page",data:(0,u.getCurUrl)()}),uni.setStorageSync("isSend",!1),this.$refs.changeBind.open())},clickNoBind:function(){var e=this;this.$store.dispatch("jv/delete","users/".concat(this.userId,"/wechat")).then((function(t){t&&t._jv&&t._jv.id&&(e.getUserInfo(),e.closeNoBind(),uni.showToast({title:"解绑成功",duration:2e3}))}))},closeNoBind:function(){this.$refs.noBind.close()},clickChangeBind:function(){uni.setStorageSync("isBind",!1),this.wxh5Login(0,1)},closeChangeBind:function(){this.$refs.changeBind.close()},getUserInfo:function(){var e=this;this.$store.dispatch("jv/get",["users/".concat(this.userId),{params:{include:"groups,wechat"}}]).then((function(t){if(t&&t._jv&&t._jv.id){e.profile=t,t&&t.wechat&&""!==t.wechat.mp_openid?e.forums&&e.forums.set_reg&&2===e.forums.set_reg.register_type?e.isWeixin?e.name="".concat(t.wechat.nickname," (换绑)"):e.name="".concat(t.wechat.nickname):e.name="".concat(t.wechat.nickname," (解绑)"):e.name="绑定";var i={headimgurl:t.avatarUrl,username:t.username};uni.setStorageSync("userInfo",i)}}))},uploadSuccess:function(e){if(uni.hideLoading(),e.statusCode>=200&&e.statusCode<300){this.$refs.toast.show({message:this.i18n.t("profile.successfullyuploadedtheavatar")});var t=JSON.parse(e.data).data.attributes.avatarUrl;this.profile.avatarUrl=t,this.$set(this.$store.getters["jv/get"]("users/".concat(this.userId)),"avatarUrl",t)}else{var i=JSON.parse(e.data).errors[0].code;"upload_time_not_up"===i?this.$refs.toast.show({message:this.i18n.t("profile.uploadtimenotup")}):"validation_error"===i?this.$refs.toast.show({message:this.i18n.t("profile.validationerror")}):this.$refs.toast.show({message:i})}},changeAvatar:function(){this.$refs.upload.uploadClick()},chooseSuccess:function(){uni.showLoading()}}};t.default=d}}]);