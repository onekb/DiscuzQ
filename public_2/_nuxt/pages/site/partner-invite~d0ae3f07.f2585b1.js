(window.webpackJsonp=window.webpackJsonp||[]).push([[31],{1e3:function(t,e,o){},1132:function(t,e,o){"use strict";o(1e3)},1227:function(t,e,o){"use strict";o.r(e);var r=o(85),n=(o(39),o(36),o(13),o(5)),c=o(157),l=o.n(c),d=o(713),f=o.n(d),head=o(714),h=o.n(head),_={name:"PartnerInvite",mixins:[l.a,f.a,h.a],data:function(){return{isLogin:this.$store.getters["session/get"]("isLogin"),pageSize:7,pageNum:1,userList:[],threadsData:[],searchText:"",permission:[],inviteData:{},dialogVisible:!1,codeTips:"",codeTitle:"",inviteCode:"",normal:!1,loading:!0,canDetail:!1,currentAudioId:"",title:this.$t("site.inviteJoin")}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},mounted:function(){var code=this.$route.query.code;this.inviteCode=code,this.getInviteInfo(this.inviteCode),this.loadThreads(),(this.forums&&this.forums.set_site&&this.forums.set_site.site_mode||!this.userId)&&(this.canDetail=!0)},methods:{handleClose:function(t){this.$confirm("确认关闭？").then((function(){t()})).catch((function(){}))},getInviteInfo:function(code){var t=this;n.status.run((function(){return t.$store.dispatch("jv/get","invite/".concat(code)).then((function(e){t.inviteData=e,t.check2();var o=e.group.permission.filter((function(t){return"createThread"!==t.permission}));t.permission=o.slice(0,3)})).catch((function(e){t.check2()}))}))},check:function(){if(this.inviteCode&&32!==this.inviteCode.length)this.inviteData.id?this.submit():(this.codeTips=this.$t("site.codenotfound"),this.dialogVisible=!0);else switch(this.inviteData.status||0===this.inviteData.status?this.inviteData.status:"error"){case 0:this.codeTips=this.$t("site.codeinvalid"),this.dialogVisible=!0;break;case 1:this.submit();break;case 2:this.codeTips=this.$t("site.codeused"),this.dialogVisible=!0;break;case 3:this.codeTips=this.$t("site.codeexpired"),this.dialogVisible=!0;break;case"error":this.codeTips=this.$t("site.codenotfound"),this.dialogVisible=!0;break;default:return""}},check2:function(){if(this.inviteCode&&32!==this.inviteCode.length)this.inviteData.id?(this.codeTitle=this.$t("site.join")+this.forums.set_site.site_name||"Discuz! Q",this.codeTips=this.$t("manage.inviteInfoTitle"),this.normal=!0):(this.codeTitle=this.$t("site.codenotfound2"),this.codeTips=this.$t("site.codenotfound"));else switch(this.inviteData.status||0===this.inviteData.status?this.inviteData.status:"error"){case 0:this.codeTitle=this.$t("site.codeinvalid2"),this.codeTips=this.$t("site.codeinvalid");break;case 1:this.codeTitle=this.$t("site.join")+this.forums.set_site.site_name||"Discuz! Q",this.codeTips=this.$t("manage.inviteInfoTitle"),this.normal=!0;break;case 2:this.codeTitle=this.$t("site.codeused2"),this.codeTips=this.$t("site.codeused");break;case 3:this.codeTitle=this.$t("site.codeexpired2"),this.codeTips=this.$t("site.codeexpired");break;case"error":this.codeTitle=this.$t("site.codenotfound2"),this.codeTips=this.$t("site.codenotfound");break;default:return""}},submit:function(){this.dialogVisible=!1,this.$store.getters["session/get"]("isLogin")?this.$router.push("/"):this.handleLogin("/",this.inviteCode)},loadThreads:function(){var t=this;this.loading=!0;var e={"filter[isDeleted]":"no",sort:"-createdAt",include:"user,user.groups,firstPost,firstPost.images,firstPost.postGoods,category,threadVideo,threadAudio,question,question.beUser,question.beUser.groups","page[number]":1,"page[limit]":10,"filter[isApproved]":1,"filter[isSite]":"yes"};n.status.run((function(){return t.$store.dispatch("jv/get",["threads",{params:e}])})).then((function(e){t.loading=!1,t.threadsData=[].concat(Object(r.a)(t.threadsData),Object(r.a)(e))}),(function(e){t.handleError(e)})).finally((function(){t.loading=!1}))},audioPlay:function(t){this.currentAudioId&&this.currentAudioId!==t&&this.$refs["audio".concat(this.currentAudioId)][0].pause(),this.currentAudioId=t}}},v=(o(1132),o(11)),component=Object(v.a)(_,(function(){var t=this,e=t.$createElement,o=t._self._c||e;return o("div",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],staticClass:"infocontainer"},[t.forums&&t.forums.users?o("div",{staticClass:"info"},[o("h2",{class:t.normal?"info-title":"info-title tcolor"},[t._v("\n      "+t._s(t.codeTitle)+"\n    ")]),t._v(" "),o("div",{staticClass:"content-info abs"},[o("p",{staticClass:"payinfo-title"},[t._v(t._s(t.codeTips))]),t._v(" "),o("p",[o("span",{staticClass:"color"},[t._v(t._s(t.$t("site.creationtime")))]),t._v(" "),o("span",{staticClass:"workdate"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_install))])]),t._v(" "),o("p",[o("span",{staticClass:"date color"},[t._v(t._s(t.$t("site.circlemaster")))]),t._v(" "),t.forums.set_site&&t.forums.set_site.site_author?o("span",{staticClass:"img"},[o("Avatar",{staticClass:"avatar",attrs:{user:{username:t.forums.set_site&&t.forums.set_site.site_author&&t.forums.set_site.site_author.username||"",avatarUrl:t.forums.set_site&&t.forums.set_site.site_author&&t.forums.set_site.site_author.avatar||""},size:30,round:!0}})],1):o("span",[o("avatar",{attrs:{user:{id:0,username:"无",avatarUrl:""},"prevent-jump":!0,size:30,round:!0}})],1),t._v(" "),o("span",{staticClass:"workdate"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_author&&t.forums.set_site.site_author.username||""))])]),t._v(" "),o("p",[o("span",{staticClass:"date color"},[t._v(t._s(t.$t("home.theme")))]),t._v(" "),o("span",{staticClass:"workdate bold"},[t._v(t._s(t.forums.other&&t.forums.other.count_users))])]),t._v(" "),o("p",{staticClass:"member-img"},t._l(t.forums.users,(function(t,e){return o("span",{key:e,staticClass:"img"},[o("Avatar",{staticClass:"avatar",attrs:{user:t,size:30,round:!0}})],1)})),0),t._v(" "),o("p",[o("span",{staticClass:"date color"},[t._v(t._s(t.$t("manage.contents")))]),t._v(" "),o("span",{staticClass:"workdate bold"},[t._v(t._s(t.forums.other&&t.forums.other.count_threads))])]),t._v(" "),o("p",[o("span",{staticClass:"date color "},[t._v(t._s(t.$t("site.circlemode")))]),t._v(" "),o("span",{staticClass:"workdate"},[t._v("\n          "+t._s(t.forums.set_site&&"pay"===t.forums.set_site.site_mode?t.$t("site.paymentmode")+"， ¥"+(t.forums.set_site&&t.forums.set_site.site_price||0)+t.$t("post.yuan")+"， "+t.$t("site.periodvalidity")+(t.forums.set_site&&t.forums.set_site.site_expire||0)+t.$t("site.day"):t.$t("site.publicmode"))+"\n        ")])]),t._v(" "),o("div",{staticClass:"myauthority"},[o("div",{staticClass:"myauth-t "},[t._v(t._s(t.$t("site.myauthority")))]),t._v(" "),o("div",{staticClass:"myauth-c"},t._l(t.permission,(function(e,r){return o("span",{key:r},[t._v(t._s(t.$t("permission."+e.permission.replace(/\./g,"_"))))])})),0)]),t._v(" "),o("p",[o("span",{staticClass:"date color rel"},[t._v(t._s(t.$t("manage.siteintroduction")))]),t._v(" "),o("span",{staticClass:"workdate2"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_introduction))])])]),t._v(" "),o("p",[o("span",{staticClass:"bold"},[t._v(t._s(t.inviteData.user&&t.inviteData.user.username))]),t._v("\n      邀请您，作为\n      "),o("span",{staticClass:"bold"},[t._v(t._s("[ "+(t.inviteData.group&&t.inviteData.group.name?t.inviteData.group.name:"")+" ]"))]),t._v("加入\n      "),o("span",{staticClass:"bold"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_name))]),t._v("\n      "+t._s(t.$t("site.site"))+"\n    ")]),t._v(" "),o("div",[o("el-button",{staticClass:"r-button",attrs:{type:"primary"},on:{click:t.check}},[t._v("\n        "+t._s(t.$t("site.accepttheinvitationandbecome"))+"\n        "+t._s(t.inviteData.group&&t.inviteData.group.name)+"\n      ")])],1),t._v(" "),o("el-dialog",{attrs:{title:"提示",visible:t.dialogVisible,width:"30%","before-close":t.handleClose},on:{"update:visible":function(e){t.dialogVisible=e}}},[o("span",[t._v(t._s(t.codeTips))]),t._v(" "),o("span",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[o("el-button",{on:{click:function(e){t.dialogVisible=!1}}},[t._v("取 消")]),t._v(" "),o("el-button",{attrs:{type:"primary"},on:{click:t.submit}},[t._v("确 定")])],1)])],1):t._e(),t._v(" "),t.threadsData.length>0?o("div",{staticClass:"thread"},[o("div",{staticClass:"threadtitle"},[t._v("部分内容预览")]),t._v(" "),t._l(t.threadsData,(function(e,r){return[4===e.type?o("post-item",{key:r,ref:"audio"+(e&&e.threadAudio&&e.threadAudio._jv&&e.threadAudio._jv.id),refInFor:!0,attrs:{item:e,infoimage:!0,"can-detail":t.canDetail},on:{audioPlay:t.audioPlay}}):o("post-item",{key:r,attrs:{item:e,infoimage:!0,"can-detail":t.canDetail}})]}))],2):t._e()])}),[],!1,null,"49a42874",null);e.default=component.exports;installComponents(component,{Avatar:o(254).default,Avatar:o(254).default,PostItem:o(788).default})},714:function(t,e){t.exports={data:function(){return{title:"‎"}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},head:function(){return{title:"‎"!==this.title&&this.forums&&this.forums.set_site&&this.forums.set_site.site_name?"".concat(this.title," - ").concat(this.forums.set_site.site_name):this.title}}}},715:function(t,e){function o(e,p){return t.exports=o=Object.setPrototypeOf||function(t,p){return t.__proto__=p,t},t.exports.default=t.exports,t.exports.__esModule=!0,o(e,p)}t.exports=o,t.exports.default=t.exports,t.exports.__esModule=!0},736:function(t,e){t.exports=function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t},t.exports.default=t.exports,t.exports.__esModule=!0},738:function(t,e){t.exports=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(t){return!1}},t.exports.default=t.exports,t.exports.__esModule=!0},792:function(t,e){function o(){return t.exports=o=Object.assign||function(t){for(var i=1;i<arguments.length;i++){var source=arguments[i];for(var e in source)Object.prototype.hasOwnProperty.call(source,e)&&(t[e]=source[e])}return t},t.exports.default=t.exports,t.exports.__esModule=!0,o.apply(this,arguments)}t.exports=o,t.exports.default=t.exports,t.exports.__esModule=!0},797:function(t,e,o){var r=o(728).default,n=o(736);t.exports=function(t,e){return!e||"object"!==r(e)&&"function"!=typeof e?n(t):e},t.exports.default=t.exports,t.exports.__esModule=!0},798:function(t,e){function o(e){return t.exports=o=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)},t.exports.default=t.exports,t.exports.__esModule=!0,o(e)}t.exports=o,t.exports.default=t.exports,t.exports.__esModule=!0},799:function(t,e,o){var r=o(715);t.exports=function(t,e){t.prototype=Object.create(e.prototype),t.prototype.constructor=t,r(t,e)},t.exports.default=t.exports,t.exports.__esModule=!0},800:function(t,e,o){var r=o(715),n=o(738);function c(e,o,l){return n()?(t.exports=c=Reflect.construct,t.exports.default=t.exports,t.exports.__esModule=!0):(t.exports=c=function(t,e,o){var a=[null];a.push.apply(a,e);var n=new(Function.bind.apply(t,a));return o&&r(n,o.prototype),n},t.exports.default=t.exports,t.exports.__esModule=!0),c.apply(null,arguments)}t.exports=c,t.exports.default=t.exports,t.exports.__esModule=!0},801:function(t,e,o){var r=o(715);t.exports=function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&r(t,e)},t.exports.default=t.exports,t.exports.__esModule=!0}}]);