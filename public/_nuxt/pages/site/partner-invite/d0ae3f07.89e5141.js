(window.webpackJsonp=window.webpackJsonp||[]).push([[42],{1068:function(t,e,r){"use strict";var o=r(908);r.n(o).a},1154:function(t,e,r){"use strict";r.r(e);var o=r(85),n=(r(11),r(5)),c=r(178),d=r.n(c),l=r(732),h=r.n(l),head=r(733),f=r.n(head),_={name:"PartnerInvite",mixins:[d.a,h.a,f.a],data:function(){return{isLogin:this.$store.getters["session/get"]("isLogin"),pageSize:7,pageNum:1,userList:[],threadsData:[],searchText:"",permission:[],inviteData:{},dialogVisible:!1,codeTips:"",codeTitle:"",inviteCode:"",normal:!1,loading:!0,canDetail:!1,currentAudioId:"",title:this.$t("site.inviteJoin")}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},mounted:function(){var code=this.$route.query.code;this.inviteCode=code,this.getInviteInfo(this.inviteCode),this.loadThreads(),(this.forums&&this.forums.set_site&&this.forums.set_site.site_mode||!this.userId)&&(this.canDetail=!0)},methods:{handleClose:function(t){this.$confirm("确认关闭？").then((function(){t()})).catch((function(){}))},getInviteInfo:function(code){var t=this;n.status.run((function(){return t.$store.dispatch("jv/get","invite/".concat(code)).then((function(e){t.inviteData=e,t.check2();var r=e.group.permission.filter((function(t){return"createThread"!==t.permission}));t.permission=r.slice(0,3)})).catch((function(e){t.check2()}))}))},check:function(){if(this.inviteCode&&32!==this.inviteCode.length)this.inviteData.id?this.submit():(this.codeTips=this.$t("site.codenotfound"),this.dialogVisible=!0);else switch(this.inviteData.status||0===this.inviteData.status?this.inviteData.status:"error"){case 0:this.codeTips=this.$t("site.codeinvalid"),this.dialogVisible=!0;break;case 1:this.submit();break;case 2:this.codeTips=this.$t("site.codeused"),this.dialogVisible=!0;break;case 3:this.codeTips=this.$t("site.codeexpired"),this.dialogVisible=!0;break;case"error":this.codeTips=this.$t("site.codenotfound"),this.dialogVisible=!0;break;default:return""}},check2:function(){if(this.inviteCode&&32!==this.inviteCode.length)this.inviteData.id?(this.codeTitle=this.$t("manage.payJoin"),this.codeTips=this.$t("manage.inviteInfoTitle"),this.normal=!0):(this.codeTitle=this.$t("site.codenotfound2"),this.codeTips=this.$t("site.codenotfound"));else switch(this.inviteData.status||0===this.inviteData.status?this.inviteData.status:"error"){case 0:this.codeTitle=this.$t("site.codeinvalid2"),this.codeTips=this.$t("site.codeinvalid");break;case 1:this.codeTitle=this.$t("manage.payJoin"),this.codeTips=this.$t("manage.inviteInfoTitle"),this.normal=!0;break;case 2:this.codeTitle=this.$t("site.codeused2"),this.codeTips=this.$t("site.codeused");break;case 3:this.codeTitle=this.$t("site.codeexpired2"),this.codeTips=this.$t("site.codeexpired");break;case"error":this.codeTitle=this.$t("site.codenotfound2"),this.codeTips=this.$t("site.codenotfound");break;default:return""}},submit:function(){this.dialogVisible=!1,this.$store.getters["session/get"]("isLogin")?this.$router.push("/"):this.handleLogin("/",this.inviteCode)},loadThreads:function(){var t=this;this.loading=!0;var e={"filter[isDeleted]":"no",sort:"-createdAt",include:"user,user.groups,firstPost,firstPost.images,firstPost.postGoods,category,threadVideo,threadAudio,question,question.beUser,question.beUser.groups","page[number]":1,"page[limit]":10,"filter[isApproved]":1,"filter[isSite]":"yes"};n.status.run((function(){return t.$store.dispatch("jv/get",["threads",{params:e}])})).then((function(e){t.loading=!1,t.threadsData=[].concat(Object(o.a)(t.threadsData),Object(o.a)(e))}),(function(e){t.handleError(e)})).finally((function(){t.loading=!1}))},audioPlay:function(t){this.currentAudioId&&this.currentAudioId!==t&&this.$refs["audio".concat(this.currentAudioId)][0].pause(),this.currentAudioId=t}}},v=(r(1068),r(12)),component=Object(v.a)(_,(function(){var t=this,e=t.$createElement,r=t._self._c||e;return r("div",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],staticClass:"infocontainer"},[t.forums&&t.forums.users?r("div",{staticClass:"info"},[r("h2",{class:t.normal?"info-title":"info-title tcolor"},[t._v("\n      "+t._s(t.codeTitle)+"\n    ")]),t._v(" "),r("div",{staticClass:"content-info abs"},[r("p",{staticClass:"payinfo-title"},[t._v(t._s(t.codeTips))]),t._v(" "),r("p",[r("span",{staticClass:"color"},[t._v(t._s(t.$t("site.creationtime")))]),t._v(" "),r("span",{staticClass:"workdate"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_install))])]),t._v(" "),r("p",[r("span",{staticClass:"date color"},[t._v(t._s(t.$t("site.circlemaster")))]),t._v(" "),t.forums.set_site&&t.forums.set_site.site_author?r("span",{staticClass:"img"},[r("Avatar",{staticClass:"avatar",attrs:{user:{username:t.forums.set_site&&t.forums.set_site.site_author&&t.forums.set_site.site_author.username||"",avatarUrl:t.forums.set_site&&t.forums.set_site.site_author&&t.forums.set_site.site_author.avatar||""},size:30,round:!0}})],1):r("span",[r("avatar",{attrs:{user:{id:0,username:"无",avatarUrl:""},"prevent-jump":!0,size:30,round:!0}})],1),t._v(" "),r("span",{staticClass:"workdate"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_author&&t.forums.set_site.site_author.username||""))])]),t._v(" "),r("p",[r("span",{staticClass:"date color"},[t._v(t._s(t.$t("home.theme")))]),t._v(" "),r("span",{staticClass:"workdate bold"},[t._v(t._s(t.forums.other&&t.forums.other.count_users))])]),t._v(" "),r("p",{staticClass:"member-img"},t._l(t.forums.users,(function(t,e){return r("span",{key:e,staticClass:"img"},[r("Avatar",{staticClass:"avatar",attrs:{user:t,size:30,round:!0}})],1)})),0),t._v(" "),r("p",[r("span",{staticClass:"date color"},[t._v(t._s(t.$t("manage.contents")))]),t._v(" "),r("span",{staticClass:"workdate bold"},[t._v(t._s(t.forums.other&&t.forums.other.count_threads))])]),t._v(" "),r("p",[r("span",{staticClass:"date color "},[t._v(t._s(t.$t("site.circlemode")))]),t._v(" "),r("span",{staticClass:"workdate"},[t._v("\n          "+t._s(t.forums.set_site&&"pay"===t.forums.set_site.site_mode?t.$t("site.paymentmode")+"， ¥"+(t.forums.set_site&&t.forums.set_site.site_price||0)+t.$t("post.yuan")+"， "+t.$t("site.periodvalidity")+(t.forums.set_site&&t.forums.set_site.site_expire||0)+t.$t("site.day"):t.$t("site.publicmode"))+"\n        ")])]),t._v(" "),r("div",{staticClass:"myauthority"},[r("div",{staticClass:"myauth-t "},[t._v(t._s(t.$t("site.myauthority")))]),t._v(" "),r("div",{staticClass:"myauth-c"},t._l(t.permission,(function(e,o){return r("span",{key:o},[t._v(t._s(t.$t("permission."+e.permission)))])})),0)]),t._v(" "),r("p",[r("span",{staticClass:"date color rel"},[t._v(t._s(t.$t("manage.siteintroduction")))]),t._v(" "),r("span",{staticClass:"workdate2"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_introduction))])])]),t._v(" "),r("p",[r("span",{staticClass:"bold"},[t._v(t._s(t.inviteData.user&&t.inviteData.user.username))]),t._v("\n      邀请您，作为\n      "),r("span",{staticClass:"bold"},[t._v(t._s("[ "+(t.inviteData.group&&t.inviteData.group.name?t.inviteData.group.name:"")+" ]"))]),t._v("加入\n      "),r("span",{staticClass:"bold"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_name))]),t._v("\n      "+t._s(t.$t("site.site"))+"\n    ")]),t._v(" "),r("div",[r("el-button",{staticClass:"r-button",attrs:{type:"primary"},on:{click:t.check}},[t._v("\n        "+t._s(t.$t("site.accepttheinvitationandbecome"))+"\n        "+t._s(t.inviteData.group&&t.inviteData.group.name)+"\n      ")])],1),t._v(" "),r("el-dialog",{attrs:{title:"提示",visible:t.dialogVisible,width:"30%","before-close":t.handleClose},on:{"update:visible":function(e){t.dialogVisible=e}}},[r("span",[t._v(t._s(t.codeTips))]),t._v(" "),r("span",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[r("el-button",{on:{click:function(e){t.dialogVisible=!1}}},[t._v("取 消")]),t._v(" "),r("el-button",{attrs:{type:"primary"},on:{click:t.submit}},[t._v("确 定")])],1)])],1):t._e(),t._v(" "),t.threadsData.length>0?r("div",{staticClass:"thread"},[r("div",{staticClass:"threadtitle"},[t._v("部分内容预览")]),t._v(" "),t._l(t.threadsData,(function(e,o){return[4===e.type?r("post-item",{key:o,ref:"audio"+(e&&e.threadAudio&&e.threadAudio._jv&&e.threadAudio._jv.id),refInFor:!0,attrs:{item:e,infoimage:!0,"can-detail":t.canDetail},on:{audioPlay:t.audioPlay}}):r("post-item",{key:o,attrs:{item:e,infoimage:!0,"can-detail":t.canDetail}})]}))],2):t._e()])}),[],!1,null,"4975d777",null);e.default=component.exports;installComponents(component,{Avatar:r(268).default,Avatar:r(268).default,PostItem:r(776).default})},732:function(t,e,r){r(32);var o=r(714);r(51),t.exports={data:function(){var t=this;return{errorCodeHandler:{default:{model_not_found:function(){return t.$router.replace("/error")},not_authenticated:function(){return t.$router.push("/user/login")}},thread:{permission_denied:function(){return t.$router.replace("/error")}}}}},methods:{handleError:function(t){var e=arguments,r=this;return o(regeneratorRuntime.mark((function o(){var n,c,d,l,h,f;return regeneratorRuntime.wrap((function(o){for(;;)switch(o.prev=o.next){case 0:if(n=e.length>1&&void 0!==e[1]?e[1]:"",c=t.response.data.errors,!(Array.isArray(c)&&c.length>0)){o.next=17;break}if(d=c[0].code,l=c[0].detail&&c[0].detail.length>0?c[0].detail[0]:c[0].code,h=c[0].detail&&c[0].detail.length>0?c[0].detail[0]:r.$t("core.".concat(l)),"site_closed"!==c[0].code){o.next=10;break}return o.next=9,r.siteClose(c);case 9:return o.abrupt("return",o.sent);case 10:if("need_ext_fields"!==c[0].code){o.next=14;break}return f=r.$store.getters["session/get"]("userId"),r.$router.push("/user/supple-mentary?id=".concat(f)),o.abrupt("return");case 14:"Permission Denied"===d?r.$message.error(r.$t("core.permission_denied2")):r.$message.error(h),r.errorCodeHandler.default[d]&&r.errorCodeHandler.default[d](),n&&r.errorCodeHandler[n][d]&&r.errorCodeHandler[n][d]();case 17:case"end":return o.stop()}}),o)})))()},siteClose:function(t){var e=this;return o(regeneratorRuntime.mark((function r(){return regeneratorRuntime.wrap((function(r){for(;;)switch(r.prev=r.next){case 0:return r.prev=0,r.next=3,e.$store.dispatch("forum/setError",{code:t[0].code,detail:t[0].detail&&t[0].detail.length>0&&t[0].detail[0]});case 3:return r.next=5,e.$router.push("/site/close");case 5:r.next=9;break;case 7:r.prev=7,r.t0=r.catch(0);case 9:case"end":return r.stop()}}),r,null,[[0,7]])})))()}}}},733:function(t,e){t.exports={data:function(){return{title:"‎"}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},head:function(){return{title:"‎"!==this.title&&this.forums&&this.forums.set_site&&this.forums.set_site.site_name?"".concat(this.title," - ").concat(this.forums.set_site.site_name):this.title}}}},908:function(t,e,r){}}]);