(window.webpackJsonp=window.webpackJsonp||[]).push([[56],{1082:function(e,t,r){"use strict";var n=r(931);r.n(n).a},1166:function(e,t,r){"use strict";r.r(t);r(32);var n=r(8),o=(r(51),r(5)),head=r(733),c=r.n(head),l=r(732),d=r.n(l),h=r(793),m=r.n(h),f=r(845),v=r.n(f),_=r(178),k=r.n(_),C={name:"PhoneLoginRegister",mixins:[c.a,d.a,m.a,v.a,k.a],data:function(){return{title:"手机号登录/注册",phoneNumber:"",content:this.$t("modify.sendVerifyCode"),activeName:"0",verifyCode:"",code:"",site_mode:"",isPaid:!1,canClick:!0,ischeck:!0,loading:!1,preurl:"/"}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},mounted:function(){var e=this.$route.query,code=e.code,t=e.preurl;t&&(this.preurl=t),"undefined"!==code&&(this.code=code),this.forums&&this.forums.set_site&&this.forums.set_site.site_mode&&(this.site_mode=this.forums.set_site.site_mode)},methods:{check:function(e){this.ischeck=e},changeinput:function(){var e=this;setTimeout((function(){e.phoneNumber=e.phoneNumber.replace(/[^\d]/g,"")}),30),11===this.phoneNumber.length?this.canClick=!0:this.canClick=!1},sendVerifyCode:function(){var e=this;return Object(n.a)(regeneratorRuntime.mark((function t(){var r;return regeneratorRuntime.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:return r={_jv:{type:"sms/send"},mobile:e.phoneNumber,type:"login"},t.next=3,e.checkCaptcha(r);case 3:r=t.sent,o.status.run((function(){return e.$store.dispatch("jv/post",r)})).then((function(t){t.interval&&e.countDown(t.interval)}),(function(t){return e.handleError(t)}));case 5:case"end":return t.stop()}}),t)})))()},PhoneLogin:function(){var e=this;if(this.loading=!0,""===this.phoneNumber)this.$message.error("手机号不能为空"),this.loading=!1;else if(""===this.verifyCode)this.$message.error("验证码不能为空"),this.loading=!1;else if(this.ischeck){var t={data:{attributes:{mobile:this.phoneNumber,code:this.verifyCode,type:"login",register:1}}};this.code&&"undefined"!==this.code&&(t.data.attributes.inviteCode=this.code),this.$store.dispatch("session/verificationCodeh5Login",t).then((function(t){if(e.loading=!1,t&&t.data&&t.data.data&&t.data.data.id&&e.logind(t),t&&t.data&&t.data.errors&&"no_bind_user"===t.data.errors[0].code){var r=t.data.errors[0].token;return localStorage.setItem("mobileToken",r),void e.logind(t)}if(t&&t.data&&t.data.errors&&"register_validate"===t.data.errors[0].code)return e.$store.commit("session/SET_AUDIT_INFO",{errorCode:"register_validate",username:e.phoneNumber}),void e.$router.push("/user/warning");if(t&&t.data&&t.data.errors&&t.data.errors[0]){var n=t.data.errors[0].detail?t.data.errors[0].detail[0]:t.data.errors[0].code,o=t.data.errors[0].detail?t.data.errors[0].detail[0]:e.$t("core.".concat(n));e.$message.error(o)}})).catch((function(t){e.loading=!1,e.handleError(t)}))}else this.$message.error("请同意协议"),this.loading=!1},toWechat:function(){this.$router.push("/user/wechat?code=".concat(this.code,"&preurl=").concat(this.preurl))},toUserlogin:function(){this.$router.push("/user/login?code=".concat(this.code,"&preurl=").concat(this.preurl))}}},y=(r(1082),r(12)),component=Object(y.a)(C,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return e.forums?r("div",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],staticClass:"register"},[r("el-tabs",{staticClass:"register-select",attrs:{type:"border-card"},model:{value:e.activeName,callback:function(t){e.activeName=t},expression:"activeName"}},[e.forums&&e.forums.qcloud&&e.forums.qcloud.qcloud_sms?r("el-tab-pane",{attrs:{label:e.$t("user.phonelogin")+"/注册",name:"0"}},[r("span",{staticClass:"title2"},[e._v(e._s(e.$t("profile.mobile")))]),e._v(" "),r("el-input",{staticClass:"phone-input",attrs:{placeholder:e.$t("user.phoneNumber"),maxlength:"11"},model:{value:e.phoneNumber,callback:function(t){e.phoneNumber=t},expression:"phoneNumber"}}),e._v(" "),r("el-button",{staticClass:"count-b",class:{disabled:!e.canClick},attrs:{size:"middle"},on:{click:e.sendVerifyCode}},[e._v(e._s(e.content))]),e._v(" "),r("span",{staticClass:"title3"},[e._v(e._s(e.$t("user.verification")))]),e._v(" "),r("el-input",{staticClass:"reg-input",attrs:{placeholder:e.$t("user.verificationCode")},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.PhoneLogin(t)}},model:{value:e.verifyCode,callback:function(t){e.verifyCode=t},expression:"verifyCode"}}),e._v(" "),r("div",{staticClass:"agreement"},[r("reg-agreement",{on:{check:e.check}})],1),e._v(" "),r("el-button",{staticClass:"r-button",attrs:{type:"primary"},on:{click:e.PhoneLogin}},[e._v(e._s(e.$t("user.login")))]),e._v(" "),r("div",{staticClass:"otherlogin"},[e.forums&&e.forums.passport&&e.forums.passport.oplatform_close&&e.forums.passport.offiaccount_close?r("svg-icon",{staticClass:"wechat-icon",attrs:{type:"wechatlogin"},on:{click:e.toWechat}}):e._e(),e._v(" "),r("svg-icon",{staticClass:"wechat-icon",attrs:{type:"userlogin"},on:{click:e.toUserlogin}})],1)],1):e._e()],1)],1):e._e()}),[],!1,null,"7ddd9636",null);t.default=component.exports;installComponents(component,{RegAgreement:r(800).default,SvgIcon:r(60).default})},733:function(e,t){e.exports={data:function(){return{title:"‎"}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},head:function(){return{title:"‎"!==this.title&&this.forums&&this.forums.set_site&&this.forums.set_site.site_name?"".concat(this.title," - ").concat(this.forums.set_site.site_name):this.title}}}},746:function(e,t,r){},793:function(e,t,r){r(11);var n=r(797);e.exports={mixins:[n],computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},methods:{checkCaptcha:function(e){var t=this;return new Promise((function(r,n){if(t.forums&&t.forums.qcloud&&t.forums.qcloud.qcloud_captcha)return new TencentCaptcha(t.forums.qcloud.qcloud_captcha_app_id,(function(t){0===t.ret?(e.captcha_rand_str=t.randstr,e.captcha_ticket=t.ticket,r(e)):n(t)})).show();r(e)}))}}}},798:function(e,t,r){"use strict";var n=r(746);r.n(n).a},800:function(e,t,r){"use strict";r.r(t);var n={name:"RegAgreement",props:{check:{type:Boolean,default:!0}},data:function(){return{forums:"",popTitle:"",popDetail:"",showagree:!1,checked:!0}},mounted:function(){this.getAttachMent()},methods:{getAttachMent:function(){var e=this;this.$store.dispatch("jv/get",["forum",{params:{include:"users","filter[tag]":"agreement"}}]).then((function(t){e.forums=t}))},open:function(e){this.$router.push("/user/agreement?type=".concat(e))},rcheck:function(){this.$emit("check",this.checked)}}},o=(r(798),r(12)),component=Object(o.a)(n,(function(){var e=this,t=e.$createElement,r=e._self._c||t;return e.forums.agreement&&e.forums.agreement.register||e.forums.agreement&&e.forums.agreement.privacy?r("div",{staticClass:"reg-agreement"},[e.check?r("el-checkbox",{on:{change:e.rcheck},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}}):e._e(),e._v(" "),r("span",{staticClass:"agree"},[e._v(e._s(e.$t("permission.user.agreement")))]),e._v(" "),e.forums.agreement&&e.forums.agreement.register?r("span",{staticClass:"regagree",on:{click:function(t){return e.open("register")}}},[e._v(e._s("《"+this.$t("permission.user.agreementRegister")+"》")+"\n  ")]):e._e(),e._v(" "),e.forums.agreement&&e.forums.agreement.privacy?r("span",{staticClass:"regagree",on:{click:function(t){return e.open("privacy")}}},[e._v(e._s("《"+this.$t("permission.user.agreementPrivacy")+"》")+"\n  ")]):e._e()],1):e._e()}),[],!1,null,"1f4f9d70",null);t.default=component.exports},845:function(e,t){e.exports={methods:{countDown:function(e){var t=this;if(this.canClick){var r=e;this.canClick=!1,this.content=r+this.$t("modify.retransmission");var n=setInterval((function(){r-=1,t.content=r+t.$t("modify.retransmission"),r<0&&(clearInterval(n),t.content=t.$t("modify.sendVerifyCode"),t.canClick=!0)}),1e3)}}}}},931:function(e,t,r){}}]);