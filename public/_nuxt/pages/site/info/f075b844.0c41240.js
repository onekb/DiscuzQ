(window.webpackJsonp=window.webpackJsonp||[]).push([[41],{1067:function(t,e,r){"use strict";var o=r(907);r.n(o).a},1153:function(t,e,r){"use strict";r.r(e);var o=r(85),n=(r(11),r(5)),c=r(732),l=r.n(c),head=r(733),d=null,_={name:"SiteInfo",mixins:[r.n(head).a,l.a],data:function(){return{title:this.$t("profile.circleinfo"),isLogin:this.$store.getters["session/get"]("isLogin"),qrcodeShow:!1,payStatus:0,orderSn:"",codeUrl:"",site_price:0,threadsData:[],loading:!0,canDetail:!1,currentAudioId:"",preurl:"/"}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}},userId:function(){return this.$store.getters["session/get"]("userId")}},watch:{userId:function(t){t&&this.userinfo()},forums:{handler:function(t){t.set_site&&t.set_site.site_price&&(this.site_price=(1*t.set_site.site_price).toFixed(2))}}},mounted:function(){this.preurl=this.$route.fullPath,this.userId&&this.userinfo(),this.loadThreads(),this.site_price=this.forums&&this.forums.set_site&&this.forums.set_site.site_price?(1*this.forums.set_site.site_price).toFixed(2):0,(this.forums.set_site&&this.forums.set_site.site_mode||!this.userId)&&(this.canDetail=!0)},methods:{time2YearMonthDay:function(t){var e=new Date(t);return["".concat(e.getFullYear(),"年"),e.getMonth()<10?"0".concat(e.getMonth()+1,"月"):"".concat(e.getMonth()+1,"月"),e.getDate()<10?"0".concat(e.getDate(),"日"):"".concat(e.getDate(),"日")].join("")},userinfo:function(){var t=this;this.$store.dispatch("jv/get",["users/".concat(this.userId),{params:{include:"groups,wechat"}}]).then((function(e){e.paid&&t.$router.push("/")}))},tologin:function(){this.$router.push("/user/login?preurl=".concat(this.preurl))},paysureShow:function(){this.creatOrder(this.forums.set_site.site_price,1,this.value)},creatOrder:function(t,e,r){var o=this,n={_jv:{type:"orders"},type:e,amount:t};this.$store.dispatch("jv/post",n).then((function(t){o.orderSn=t.order_sn,o.orderPay(10,r,o.orderSn,"3")}),(function(t){return o.handleError(t)}))},orderPay:function(t,e,r,o){var n,c=this;n={_jv:{type:"trade/pay/order/".concat(r)},payment_type:t},this.$store.dispatch("jv/post",n).then((function(t){c.wxRes=t,"3"===o&&t&&(c.codeUrl=t.wechat_qrcode,c.payShowStatus=!1,c.qrcodeShow=!0,d=setInterval((function(){1!==c.payStatus?c.getOrderStatus(c.orderSn,o):clearInterval(d)}),3e3))}),(function(t){return c.handleError(t)}))},getOrderStatus:function(t,e){var r=this;this.$store.dispatch("jv/get","orders/".concat(t)).then((function(t){r.payStatus=t.status,1===r.payStatus&&(r.payShowStatus=!1,"3"===e&&(r.qrcodeShow=!1),window.location.href="/",r.$message.success(r.$t("pay.paySuccess")))})).catch((function(){r.$message.success(r.$t("pay.payFail"))}))},loadThreads:function(){var t=this;this.loading=!0;var e={"filter[isDeleted]":"no",sort:"-createdAt",include:"user,user.groups,firstPost,firstPost.images,firstPost.postGoods,category,threadVideo,threadAudio,question,question.beUser,question.beUser.groups","page[number]":1,"page[limit]":10,"filter[isApproved]":1,"filter[isSite]":"yes"};n.status.run((function(){return t.$store.dispatch("jv/get",["threads",{params:e}])})).then((function(e){t.loading=!1,t.threadsData=[].concat(Object(o.a)(t.threadsData),Object(o.a)(e))}),(function(e){t.handleError(e)})).finally((function(){t.loading=!1}))},audioPlay:function(t){this.currentAudioId&&this.currentAudioId!==t&&this.$refs["audio".concat(this.currentAudioId)][0].pause(),this.currentAudioId=t}}},f=(r(1067),r(12)),component=Object(f.a)(_,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],staticClass:"infocontainer"},[t.forums&&t.forums.users?r("div",{staticClass:"info"},[r("h2",{staticClass:"info-title"},[t._v(t._s(t.$t("manage.payJoin")))]),t._v(" "),r("div",{staticClass:"payinfo"},[r("p",{staticClass:"payinfo-title"},[t._v(t._s(t.$t("manage.payInfoTitle")))]),t._v(" "),r("p",[r("span",{staticClass:"color"},[t._v(t._s(t.$t("post.paymentAmount")))]),t._v(" "),r("span",{staticClass:"paymoney"},[t._v(t._s("¥ "+t.site_price+" ")+"元")])]),t._v(" "),r("p",[r("span",{staticClass:"date color"},[t._v(t._s(t.$t("site.periodvalidity")))]),t._v(" "),r("span",{staticClass:"workdate"},[t._v("自加入起\n          "+t._s(t.forums.set_site&&t.forums.set_site.site_expire?(t.forums.set_site&&t.forums.set_site.site_expire)+" "+t.$t("site.day"):t.$t("site.permanent")))])])]),t._v(" "),r("div",{staticClass:"content-info abs"},[r("p",[r("span",{staticClass:"color"},[t._v(t._s(t.$t("site.creationtime")))]),t._v(" "),r("span",{staticClass:"workdate"},[t._v(t._s(t.forums.set_site&&t.time2YearMonthDay(t.forums.set_site.site_install)))])]),t._v(" "),r("p",[r("span",{staticClass:"date color"},[t._v(t._s(t.$t("site.circlemaster")))]),t._v(" "),t.forums.set_site&&t.forums.set_site.site_author?r("span",{staticClass:"img"},[r("Avatar",{staticClass:"avatar",attrs:{user:{username:t.forums.set_site&&t.forums.set_site.site_author&&t.forums.set_site.site_author.username||"",avatarUrl:t.forums.set_site&&t.forums.set_site.site_author&&t.forums.set_site.site_author.avatar||""},size:30,round:!0}})],1):r("span",[r("avatar",{attrs:{user:{id:0,username:"无",avatarUrl:""},"prevent-jump":!0,size:30,round:!0}})],1),t._v(" "),r("span",{staticClass:"workdate3"},[t._v("\n          "+t._s(t.forums.set_site&&t.forums.set_site.site_author&&t.forums.set_site.site_author.username||"")+"\n        ")])]),t._v(" "),r("p",[r("span",{staticClass:"date color"},[t._v(t._s(t.$t("home.theme")))]),t._v(" "),r("span",{staticClass:"workdate bold"},[t._v(t._s(t.forums.other&&t.forums.other.count_users))])]),t._v(" "),r("p",{staticClass:"member-img"},t._l(t.forums.users,(function(t,e){return r("span",{key:e,staticClass:"img"},[r("Avatar",{staticClass:"avatar",attrs:{user:t,size:30,round:!0}})],1)})),0),t._v(" "),r("p",[r("span",{staticClass:"date color "},[t._v(t._s(t.$t("manage.contents")))]),t._v(" "),r("span",{staticClass:"workdate bold"},[t._v(t._s(t.forums.other&&t.forums.other.count_threads))])]),t._v(" "),r("p",[r("span",{staticClass:"date color rel"},[t._v(t._s(t.$t("manage.siteintroduction")))]),t._v(" "),r("span",{staticClass:"workdate2"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_introduction))])])]),t._v(" "),t.isLogin?r("p",{staticClass:"joinnow"},[r("span",[t._v(t._s(t.$t("site.justonelaststepjoinnow")))]),t._v(" "),r("span",{staticClass:"bold"},[t._v("\n        "+t._s(t.forums.set_site&&t.forums.set_site.site_name)+"\n      ")]),t._v(" "),r("span",[t._v(t._s(t.$t("site.site")))]),t._v(" "),r("el-button",{class:t.isLogin?"r-button islogin":"r-button",attrs:{type:"primary"},on:{click:t.paysureShow}},[t._v("\n        "+t._s(t.$t("site.paynow"))+"，¥"+t._s(" "+t.site_price+" "||!1)+"\n        "+t._s(t.forums.set_site&&t.forums.set_site.site_expire?"  / "+t.$t("site.periodvalidity")+(t.forums.set_site&&t.forums.set_site.site_expire)+t.$t("site.day"):" / "+t.$t("site.permanent"))+"\n      ")])],1):t._e(),t._v(" "),t.isLogin?t._e():r("div",[r("el-button",{staticClass:"r-button",attrs:{type:"primary"},on:{click:t.tologin}},[t._v(t._s(t.$t("site.joinnow")))])],1),t._v(" "),t.qrcodeShow?r("topic-wx-pay",{attrs:{"qr-code":t.codeUrl},on:{close:function(e){t.qrcodeShow=!1}}}):t._e()],1):t._e(),t._v(" "),t.threadsData.length>0?r("div",{staticClass:"thread"},[r("div",{staticClass:"threadtitle"},[t._v("部分内容预览")]),t._v(" "),t._l(t.threadsData,(function(e,o){return[4===e.type?r("post-item",{key:o,ref:"audio"+(e&&e.threadAudio&&e.threadAudio._jv&&e.threadAudio._jv.id),refInFor:!0,attrs:{item:e,infoimage:!0,"can-detail":t.canDetail},on:{audioPlay:t.audioPlay}}):r("post-item",{key:o,attrs:{item:e,infoimage:!0,"can-detail":t.canDetail}})]}))],2):t._e()])}),[],!1,null,"c2879d32",null);e.default=component.exports;installComponents(component,{Avatar:r(268).default,Avatar:r(268).default,TopicWxPay:r(873).default,PostItem:r(776).default})},732:function(t,e,r){r(32);var o=r(714);r(51),t.exports={data:function(){var t=this;return{errorCodeHandler:{default:{model_not_found:function(){return t.$router.replace("/error")},not_authenticated:function(){return t.$router.push("/user/login")}},thread:{permission_denied:function(){return t.$router.replace("/error")}}}}},methods:{handleError:function(t){var e=arguments,r=this;return o(regeneratorRuntime.mark((function o(){var n,c,l,d,_,f;return regeneratorRuntime.wrap((function(o){for(;;)switch(o.prev=o.next){case 0:if(n=e.length>1&&void 0!==e[1]?e[1]:"",c=t.response.data.errors,!(Array.isArray(c)&&c.length>0)){o.next=17;break}if(l=c[0].code,d=c[0].detail&&c[0].detail.length>0?c[0].detail[0]:c[0].code,_=c[0].detail&&c[0].detail.length>0?c[0].detail[0]:r.$t("core.".concat(d)),"site_closed"!==c[0].code){o.next=10;break}return o.next=9,r.siteClose(c);case 9:return o.abrupt("return",o.sent);case 10:if("need_ext_fields"!==c[0].code){o.next=14;break}return f=r.$store.getters["session/get"]("userId"),r.$router.push("/user/supple-mentary?id=".concat(f)),o.abrupt("return");case 14:"Permission Denied"===l?r.$message.error(r.$t("core.permission_denied2")):r.$message.error(_),r.errorCodeHandler.default[l]&&r.errorCodeHandler.default[l](),n&&r.errorCodeHandler[n][l]&&r.errorCodeHandler[n][l]();case 17:case"end":return o.stop()}}),o)})))()},siteClose:function(t){var e=this;return o(regeneratorRuntime.mark((function r(){return regeneratorRuntime.wrap((function(r){for(;;)switch(r.prev=r.next){case 0:return r.prev=0,r.next=3,e.$store.dispatch("forum/setError",{code:t[0].code,detail:t[0].detail&&t[0].detail.length>0&&t[0].detail[0]});case 3:return r.next=5,e.$router.push("/site/close");case 5:r.next=9;break;case 7:r.prev=7,r.t0=r.catch(0);case 9:case"end":return r.stop()}}),r,null,[[0,7]])})))()}}}},733:function(t,e){t.exports={data:function(){return{title:"‎"}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},head:function(){return{title:"‎"!==this.title&&this.forums&&this.forums.set_site&&this.forums.set_site.site_name?"".concat(this.title," - ").concat(this.forums.set_site.site_name):this.title}}}},747:function(t,e,r){"use strict";r.r(e);var o={name:"MessageBox",props:{width:{type:String,default:"820px"},title:{type:String,default:""},overflow:{type:String,default:"auto"}}},n=(r(804),r(12)),component=Object(n.a)(o,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",[r("Cover"),t._v(" "),r("div",{staticClass:"message-box",style:{overflow:t.overflow,width:t.width}},[r("div",{staticClass:"container-title"},[r("div",{staticClass:"title"},[t._v(t._s(t.title))]),t._v(" "),r("svg-icon",{staticStyle:{"font-size":"14px",cursor:"pointer",fill:"#6d6d6d"},attrs:{type:"close"},on:{click:function(e){return t.$emit("close")}}})],1),t._v(" "),t._t("default")],2)],1)}),[],!1,null,"05e0d157",null);e.default=component.exports;installComponents(component,{Cover:r(737).default,SvgIcon:r(60).default})},751:function(t,e,r){},804:function(t,e,r){"use strict";var o=r(751);r.n(o).a},811:function(t,e,r){},868:function(t,e,r){"use strict";var o=r(811);r.n(o).a},873:function(t,e,r){"use strict";r.r(e);var o={name:"TopicWxPay",props:{qrCode:{type:String,default:""}}},n=(r(868),r(12)),component=Object(n.a)(o,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("message-box",{attrs:{title:t.$t("pay.pay")},on:{close:function(e){return t.$emit("close")}}},[r("div",{staticClass:"container"},[r("div",{staticClass:"title"},[r("svg-icon",{staticStyle:{fill:"#09BB07","font-size":"30px"},attrs:{type:"wechat"}}),t._v(" "),r("span",[t._v(t._s(t.$t("pay.wxPay")))])],1),t._v(" "),r("div",{staticClass:"tip"},[t._v(t._s(t.$t("pay.wechatTimeLimit")))]),t._v(" "),r("div",{staticClass:"container-qr-code"},[r("img",{staticStyle:{width:"138px",height:"138px"},attrs:{src:t.qrCode,alt:"qr-code"}})]),t._v(" "),r("div",{staticClass:"scan-tip"},[r("svg-icon",{staticStyle:{display:"block","font-size":"32px"},attrs:{type:"wx-scan"}}),t._v(" "),r("div",{staticClass:"tip"},[r("span",[t._v(t._s(t.$t("pay.wechatScan")))]),t._v(" "),r("span",[t._v(t._s(t.$t("pay.wechatScanPay")))])])],1)])])}),[],!1,null,"028323c1",null);e.default=component.exports;installComponents(component,{SvgIcon:r(60).default,MessageBox:r(747).default})},907:function(t,e,r){}}]);