(window.webpackJsonp=window.webpackJsonp||[]).push([["pages-user-at-member"],{"12e5":function(e,t,i){"use strict";(function(e){var s;i.d(t,"b",(function(){return a})),i.d(t,"c",(function(){return n})),i.d(t,"a",(function(){return s}));try{s={quiPage:i("29c4").default,quiIcon:i("895d").default,quiAvatarCell:i("1d60").default,quiButton:i("8397").default}}catch(t){if(-1===t.message.indexOf("Cannot find module")||-1===t.message.indexOf(".vue"))throw t;e.error(t.message),e.error("1. 排查组件名称拼写是否正确"),e.error("2. 排查组件是否符合 easycom 规范，文档：https://uniapp.dcloud.net.cn/collocation/pages?id=easycom"),e.error("3. 若组件不符合 easycom 规范，需手动引入，并在 components 中注册该组件")}var a=function(){var e=this,t=e.$createElement,i=e._self._c||t;return i("qui-page",{staticClass:"qui-at-member-page-box",attrs:{"data-qui-theme":e.theme}},[i("v-uni-view",{staticClass:"qui-at-member-page-box__hd"},[i("v-uni-view",{staticClass:"qui-at-member-page-box__hd__sc"},[i("qui-icon",{staticClass:"icon-search",attrs:{name:"icon-search",size:"30"}}),i("v-uni-input",{attrs:{type:"text","placeholder-class":"input-placeholder","confirm-type":"search",placeholder:"select"===e.select?e.i18n.t("discuzq.atMember.selectUser"):e.i18n.t("discuzq.atMember.selectedMember")},on:{input:function(t){arguments[0]=t=e.$handleEvent(t),e.searchInput.apply(void 0,arguments)}}})],1)],1),i("v-uni-view",{staticClass:"qui-at-member-page-box__lst"},[i("v-uni-scroll-view",{staticClass:"scroll-Y",style:"select"!==e.select?"height:calc(100vh - 292rpx)":"height:calc(100vh - 2rpx)",attrs:{"scroll-y":"true","scroll-with-animation":"true"},on:{scrolltolower:function(t){arguments[0]=t=e.$handleEvent(t),e.lower.apply(void 0,arguments)}}},[e.followStatus&&"select"!==e.select?i("v-uni-checkbox-group",{on:{change:function(t){arguments[0]=t=e.$handleEvent(t),e.changeCheck.apply(void 0,arguments)}}},e._l(e.allFollow,(function(t){return i("v-uni-label",{key:t.id},[t.toUser?i("qui-avatar-cell",{attrs:{mark:t.toUser.id,title:t.toUser.username,icon:t.toUser.avatarUrl?t.toUser.avatarUrl:"/static/noavatar.gif",value:t.toUser.groups.length>0?t.toUser.groups[0].name:"",label:t.toUser.label,"is-real":t.toUser.isReal}},[i("v-uni-checkbox",{attrs:{slot:"rightIcon",value:JSON.stringify(t)},slot:"rightIcon"})],1):e._e()],1)})),1):e._e(),e.followStatus||"select"===e.select?e._e():i("v-uni-checkbox-group",{on:{change:function(t){arguments[0]=t=e.$handleEvent(t),e.changeCheck.apply(void 0,arguments)}}},e._l(e.allSiteUser,(function(e){return i("v-uni-label",{key:e.id},[i("qui-avatar-cell",{attrs:{mark:e.id,title:e.username,icon:e.avatarUrl?e.avatarUrl:"/static/noavatar.gif",value:e.groups.length>0?e.groups[0].name:"",label:e.label,"is-real":e.isReal}},[i("v-uni-checkbox",{attrs:{slot:"rightIcon",value:JSON.stringify(e)},slot:"rightIcon"})],1)],1)})),1),"select"===e.select?i("v-uni-view",e._l(e.allSiteUser,(function(t){return i("v-uni-view",{key:t.id},[i("qui-avatar-cell",{attrs:{mark:t.id,title:t.username,icon:t.avatarUrl?t.avatarUrl:"/static/noavatar.gif",value:t.groups[0].name,label:t.label,"is-real":t.isReal},on:{click:function(i){arguments[0]=i=e.$handleEvent(i),e.radioChange(t)}}})],1)})),1):e._e(),i("v-uni-view",{staticClass:"loading-text"},["search.norelatedusersfound"===e.loadingText||"search.noFollowers"===e.loadingText?i("qui-icon",{attrs:{name:"icon-noData"}}):e._e(),i("v-uni-text",{staticClass:"loading-text__cont"},[e._v(e._s(e.i18n.t(e.loadingText)))])],1)],1)],1),"select"!==e.select?i("v-uni-view",{staticClass:"qui-at-member-page-box__ft"},["select"!==e.select?i("qui-button",{attrs:{size:"large",type:Boolean(e.checkAvatar.length<1)?"default":"primary",disabled:Boolean(e.checkAvatar.length<1)},on:{click:function(t){arguments[0]=t=e.$handleEvent(t),e.getCheckMember.apply(void 0,arguments)}}},[e._v(e._s(e.checkAvatar.length<1?e.i18n.t("discuzq.atMember.notSelected"):e.i18n.t("discuzq.atMember.selected")+"("+e.checkAvatar.length+")"))]):e._e()],1):e._e()],1)},n=[]}).call(this,i("5a52").default)},"185d":function(e,t,i){e.exports=i.p+"static/img/auth.51e40f27.svg"},"2376c":function(e,t,i){"use strict";var s=i("12e5");i.d(t,"a",(function(){return s.a})),i.d(t,"b",(function(){return s.b})),i.d(t,"c",(function(){return s.c}))},"245f":function(e,t,i){"use strict";(function(t){var s=i("4ea4").default,a=s(i("6f74")),n=i("b95e"),r=s(i("4c82"));e.exports={mixins:[a.default,r.default],methods:{getForum:function(){var e=this;this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(t){t&&(e.forum=t)}))},jump2PhoneLoginPage:function(){uni.redirectTo({url:"/pages/user/phone-login"})},jump2PhoneLoginRegisterPage:function(){uni.redirectTo({url:"/pages/user/phone-login-register"})},jump2LoginPage:function(){uni.redirectTo({url:"/pages/user/login"})},jump2RegisterPage:function(){uni.redirectTo({url:"/pages/user/register"})},jump2RegisterExtendPage:function(){uni.redirectTo({url:"/pages/user/supple-mentary"})},jump2LoginBindPage:function(){uni.redirectTo({url:"/pages/user/login-bind"})},jump2RegisterBindPage:function(){uni.redirectTo({url:"/pages/user/register-bind"})},jump2LoginBindPhonePage:function(){uni.redirectTo({url:"/pages/user/login-bind-phone"})},jump2RegisterBindPhonePage:function(){uni.redirectTo({url:"/pages/user/register-bind-phone"})},jump2findpwdPage:function(){uni.navigateTo({url:"/pages/modify/findpwd?pas=reset_pwd"})},mpLoginMode:function(){this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&this.jump2LoginPage(),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open())},h5LoginMode:function(){r.default.isWeixin().isWeixin?(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),this.$store.dispatch("session/wxh5Login"))):(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}))},refreshmpParams:function(){var e=this;uni.login({success:function(i){if("login:ok"===i.errMsg){var s=i.code;uni.getUserInfo({success:function(t){var i={data:{attributes:{js_code:s,iv:t.iv,encryptedData:t.encryptedData}}};e.$store.dispatch("session/setParams",i)},fail:function(e){t.log(e)}})}},fail:function(e){t.log(e)}})},mpLogin:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;uni.setStorageSync("register",e),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open()},wxh5Login:function(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;uni.setStorageSync("register",e),uni.setStorageSync("rebind",t),uni.setStorageSync("h5_wechat_login",1),this.$store.dispatch("session/wxh5Login")},getLoginParams:function(e,t){var i=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{var s=uni.getStorageSync("token");""!==s&&(i.data.attributes.token=s),this.login(i,t)}},getLoginBindParams:function(e,t){var i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;this.refreshmpParams();var s=e;if(""===e.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===e.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{1===i&&(s.data.attributes.rebind=1);var a=uni.getStorageSync("token");""!==a&&(s.data.attributes.token=a),this.login(s,t)}},login:function(e,i){var s=this;this.$store.dispatch("session/h5Login",e).then((function(e){if(e&&e.data&&e.data.data&&e.data.data.id&&(s.logind(),s.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(e){e&&e.set_site&&e.set_site.site_mode!==n.SITE_PAY&&uni.getStorage({key:"page",success:function(e){uni.redirectTo({url:e.data})}}),e&&e.set_site&&e.set_site.site_mode===n.SITE_PAY&&s.user&&!s.user.paid&&uni.redirectTo({url:"/pages/site/info"})})),uni.showToast({title:i,duration:2e3})),e&&e.data&&e.data.errors){if("401"===e.data.errors[0].status||"402"===e.data.errors[0].status||"500"===e.data.errors[0].status){var t=s.i18n.t("core.".concat(e.data.errors[0].code));uni.showToast({icon:"none",title:t,duration:2e3})}if("403"===e.data.errors[0].status||"422"===e.data.errors[0].status){var a=s.i18n.t("core.".concat(e.data.errors[0].code))||s.i18n.t(e.data.errors[0].detail[0]);uni.showToast({icon:"none",title:a,duration:2e3})}}})).catch((function(e){return t.log(e)}))}}}}).call(this,i("5a52").default)},"35ab":function(e,t,i){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default=function(e){for(var t=0,i=0;i<e.length;i++)t+=e.charCodeAt(i);var s=function(e,t,i){var s,a,n,r=Math.floor(6*e),o=6*e-r,u=i*(1-t),c=i*(1-o*t),l=i*(1-(1-o)*t);switch(r%6){case 0:s=i,a=l,n=u;break;case 1:s=c,a=i,n=u;break;case 2:s=u,a=i,n=l;break;case 3:s=u,a=c,n=i;break;case 4:s=l,a=u,n=i;break;case 5:s=i,a=u,n=c}return{r:Math.floor(255*s),g:Math.floor(255*a),b:Math.floor(255*n)}}(t%360/360,.3,.9);return""+s.r.toString(16)+s.g.toString(16)+s.b.toString(16)},i("d3b7"),i("25f0")},"368d":function(e,t,i){e.exports=i.p+"static/img/msg-warning.f35ce51f.svg"},"36e5":function(e,t,i){var s=i("895f");"string"==typeof s&&(s=[[e.i,s,""]]),s.locals&&(e.exports=s.locals);(0,i("4f06").default)("cb1f0f4e",s,!0,{sourceMap:!1,shadowMode:!1})},"472a":function(e,t,i){"use strict";var s=i("36e5");i.n(s).a},"6f74":function(e,t,i){"use strict";var s=i("b95e");e.exports={computed:{user:function(){var e=this.$store.getters["session/get"]("userId");return e?this.$store.getters["jv/get"]("users/".concat(e)):{}}},methods:{getUserInfo:function(){var e=arguments.length>0&&void 0!==arguments[0]&&arguments[0],t=(new Date).getTime(),i=uni.getStorageSync(s.STORGE_GET_USER_TIME);if(e||(t-i)/1e3>60){var a={include:"groups,wechat"},n=this.$store.getters["session/get"]("userId");this.$store.commit("jv/deleteRecord",{_jv:{type:"users",id:n}}),this.$store.dispatch("jv/get",["users/".concat(n),{params:a}]).then((function(){return uni.$emit("updateNotiNum")})),uni.setStorageSync(s.STORGE_GET_USER_TIME,(new Date).getTime())}},logind:function(){var e=this,t=this.$store.getters["session/get"]("userId");if(t){this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]);this.$store.dispatch("jv/get",["users/".concat(t),{params:{include:"groups,wechat"}}]).then((function(t){e.$u.event.$emit("logind",t)})),this.$store.dispatch("forum/setError",{loading:!1})}}}}},"895f":function(e,t,i){(t=i("24fb")(!1)).push([e.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */\n/* eg:\n  .container {\n    color: --color(BG-1);\n  }\n*/.qui-at-member-page-box[data-v-f96fbc4e]{width:100%;height:100%;background-color:var(--qui-BG-2)}.qui-at-member-page-box__hd[data-v-f96fbc4e]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;height:%?80?%;padding:%?20?% %?40?%}.qui-at-member-page-box__hd__sc[data-v-f96fbc4e]{display:-webkit-box;display:-webkit-flex;display:flex;-webkit-box-align:center;-webkit-align-items:center;align-items:center;width:100%;height:100%;padding:0 %?10?%;background-color:var(--qui-BG-IT);border-radius:%?7?%;border-radius:%?80?%}.qui-at-member-page-box__hd__sc .icon-search[data-v-f96fbc4e]{margin:0 %?10?%;color:#bbb}.qui-at-member-page-box__hd__sc uni-input[data-v-f96fbc4e]{width:100%;height:100%}.qui-at-member-page-box__hd__sc[data-v-f96fbc4e] uni-input .input-placeholder{font-size:%?30?%;color:var(--qui-FC-C6)}.qui-at-member-page-box__lst .scroll-Y .loading-text[data-v-f96fbc4e]{height:%?100?%;font-size:%?28?%;line-height:%?100?%;color:var(--qui-FC-AAA);text-align:center}.qui-at-member-page-box__lst .scroll-Y .loading-text__cont[data-v-f96fbc4e]{margin-left:%?20?%}.qui-at-member-page-box__ft[data-v-f96fbc4e]{position:absolute;bottom:0;width:100%;padding:%?40?%;background-color:var(--qui-BG-2);box-sizing:border-box}.qui-at-member-page-box__ft[data-v-f96fbc4e] .qui-button--button[size="large"]{border-radius:%?5?%}.qui-at-member-page-box__ft[data-v-f96fbc4e] .qui-button--button[disabled]{color:#7d7979;background-color:#fff}',""]),e.exports=t},"928b":function(e,t,i){"use strict";i.r(t);var s=i("ec0f"),a=i.n(s);for(var n in s)["default"].indexOf(n)<0&&function(e){i.d(t,e,(function(){return s[e]}))}(n);t.default=a.a},b469:function(e,t){e.exports={computed:{forums:function(){return this.$store.getters["jv/get"]("forums/1")}}}},bf3c:function(e,t,i){"use strict";i.r(t);var s=i("2376c"),a=i("928b");for(var n in a)["default"].indexOf(n)<0&&function(e){i.d(t,e,(function(){return a[e]}))}(n);i("472a");var r=i("f0c5"),o=Object(r.a)(a.default,s.b,s.c,!1,null,"f96fbc4e",null,!1,s.a,void 0);t.default=o.exports},e972:function(e,t,i){e.exports=i.p+"static/img/msg-404.3ba2611f.svg"},ec0f:function(e,t,i){"use strict";var s=i("4ea4").default;Object.defineProperty(t,"__esModule",{value:!0}),t.default=void 0,i("4de4"),i("4160"),i("159b"),i("99af"),i("b64b");var a=s(i("2909")),n=s(i("5530")),r=i("2f62"),o={name:"QuiAtMemberPage",data:function(){return{allSiteUser:[],allFollow:[],followStatus:!0,checkAvatar:[],loadingText:"discuzq.list.loading",searchValue:"",pageNum:1,meta:{},select:!0,current:0,categoryId:0,categoryIndex:0}},methods:(0,n.default)((0,n.default)({},(0,r.mapMutations)({setAtMember:"atMember/SET_ATMEMBER"})),{},{handleGroups:function(e){var t=[];return e.groups&&e.groups.length>0&&(t=e.groups.filter((function(e){return e.isDisplay}))),t.length>0?t[0].name:""},changeCheck:function(e){var t=this;this.checkAvatar=[],e.detail.value.forEach((function(e){t.followStatus?t.checkAvatar.push(JSON.parse(e).toUser):t.checkAvatar.push(JSON.parse(e))}))},radioChange:function(e){uni.navigateTo({url:"/topic/post?type=5&categoryId=".concat(this.categoryId,"&categoryIndex=").concat(this.categoryIndex)}),setTimeout((function(){e.toUser?uni.$emit("radioChange",e.toUser):uni.$emit("radioChange",e)}),1e3)},getCheckMember:function(){this.setAtMember(this.checkAvatar),uni.navigateBack({delta:1})},searchInput:function(e){var t=this;this.followStatus=!1,this.searchValue=e.detail.value,this.checkAvatar=[],1!==this.pageNum&&(this.pageNum=1),this.loadingText="discuzq.list.loading",this.timeout&&clearTimeout(this.timeout),this.timeout=setTimeout((function(){t.allSiteUser=[],t.getSiteMember(1)}),250)},lower:function(){this.followStatus?this.meta.total>this.allFollow.length?(this.pageNum+=1,this.getFollowMember(this.pageNum)):this.loadingText="discuzq.list.noMoreData":this.meta.total>this.allSiteUser.length?(this.pageNum+=1,this.getSiteMember(this.pageNum)):this.loadingText="discuzq.list.noMoreData"},getFollowMember:function(e){var t=this,i={include:["toUser","toUser.groups"],"page[size]":20,"page[number]":e};"select"===this.select&&(i["filter[canBeAsked]"]="yes"),this.$store.dispatch("jv/get",["follow",{params:i}]).then((function(e){t.meta=e._jv.json.meta,t.allFollow=[].concat((0,a.default)(t.allFollow),(0,a.default)(e)),Object.keys(e).nv_length-1==0?t.loadingText="search.noFollowers":e._jv.json.meta.total<=20&&Object.keys(e).nv_length-1!=0&&(t.loadingText="discuzq.list.noMoreData")}))},getSiteMember:function(e){var t=this,i={"filter[username]":"*".concat(this.searchValue,"*"),"filter[status]":"normal","page[size]":20,"page[number]":e};"select"===this.select&&(i["filter[canBeAsked]"]="yes"),this.$store.dispatch("jv/get",["users",{params:i}]).then((function(e){t.meta=e._jv.json.meta,t.allSiteUser=[].concat((0,a.default)(t.allSiteUser),(0,a.default)(e)),Object.keys(e).nv_length-1==0?t.loadingText="search.norelatedusersfound":e._jv.json.meta.total<=20&&Object.keys(e).nv_length-1!=0&&(t.loadingText="discuzq.list.noMoreData")}))}}),onLoad:function(e){this.select=e.name,this.categoryId=e.categoryId,this.categoryIndex=e.categoryIndex,"select"===e.name?(this.followStatus=!1,uni.setNavigationBarTitle({title:this.i18n.t("discuzq.atMember.selectUser")})):uni.setNavigationBarTitle({title:this.i18n.t("discuzq.atMember.atTitle")}),"select"!==e.name?this.getFollowMember(1):"select"===e.name&&this.getSiteMember(1),this.setAtMember([])}};t.default=o}}]);