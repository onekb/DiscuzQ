(window.webpackJsonp=window.webpackJsonp||[]).push([[32],{1019:function(t,e,n){},1151:function(t,e,n){"use strict";n(1019)},1246:function(t,e,n){"use strict";n.r(e);var o=n(85),r=(n(39),n(35),n(13),n(5)),c=n(158),l=n.n(c),d=n(715),m=n.n(d),head=n(716),h=n.n(head),f={name:"PartnerInvite",mixins:[l.a,m.a,h.a],data:function(){return{isLogin:this.$store.getters["session/get"]("isLogin"),pageSize:7,pageNum:1,userList:[],threadsData:[],searchText:"",permission:[],inviteData:{},dialogVisible:!1,codeTips:"",codeTitle:"",inviteCode:"",normal:!1,loading:!0,canDetail:!1,currentAudioId:"",title:this.$t("site.inviteJoin")}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},mounted:function(){var code=this.$route.query.code;this.inviteCode=code,this.getInviteInfo(this.inviteCode),this.loadThreads(),(this.forums&&this.forums.set_site&&this.forums.set_site.site_mode||!this.userId)&&(this.canDetail=!0)},methods:{handleClose:function(t){this.$confirm("确认关闭？").then((function(){t()})).catch((function(){}))},getInviteInfo:function(code){var t=this;r.status.run((function(){return t.$store.dispatch("jv/get","invite/".concat(code)).then((function(e){t.inviteData=e,t.check2();var n=e.group.permission.filter((function(t){return"createThread"!==t.permission}));t.permission=n.slice(0,3)})).catch((function(e){t.check2()}))}))},check:function(){if(this.inviteCode&&32!==this.inviteCode.length)this.inviteData.id?this.submit():(this.codeTips=this.$t("site.codenotfound"),this.dialogVisible=!0);else switch(this.inviteData.status||0===this.inviteData.status?this.inviteData.status:"error"){case 0:this.codeTips=this.$t("site.codeinvalid"),this.dialogVisible=!0;break;case 1:this.submit();break;case 2:this.codeTips=this.$t("site.codeused"),this.dialogVisible=!0;break;case 3:this.codeTips=this.$t("site.codeexpired"),this.dialogVisible=!0;break;case"error":this.codeTips=this.$t("site.codenotfound"),this.dialogVisible=!0;break;default:return""}},check2:function(){if(this.inviteCode&&32!==this.inviteCode.length)this.inviteData.id?(this.codeTitle=this.$t("site.join")+this.forums.set_site.site_name||"Discuz! Q",this.codeTips=this.$t("manage.inviteInfoTitle"),this.normal=!0):(this.codeTitle=this.$t("site.codenotfound2"),this.codeTips=this.$t("site.codenotfound"));else switch(this.inviteData.status||0===this.inviteData.status?this.inviteData.status:"error"){case 0:this.codeTitle=this.$t("site.codeinvalid2"),this.codeTips=this.$t("site.codeinvalid");break;case 1:this.codeTitle=this.$t("site.join")+this.forums.set_site.site_name||"Discuz! Q",this.codeTips=this.$t("manage.inviteInfoTitle"),this.normal=!0;break;case 2:this.codeTitle=this.$t("site.codeused2"),this.codeTips=this.$t("site.codeused");break;case 3:this.codeTitle=this.$t("site.codeexpired2"),this.codeTips=this.$t("site.codeexpired");break;case"error":this.codeTitle=this.$t("site.codenotfound2"),this.codeTips=this.$t("site.codenotfound");break;default:return""}},submit:function(){this.dialogVisible=!1,this.$store.getters["session/get"]("isLogin")?this.$router.push("/"):this.handleLogin("/",this.inviteCode)},loadThreads:function(){var t=this;this.loading=!0;var e={"filter[isDeleted]":"no",sort:"-createdAt",include:"user,user.groups,firstPost,firstPost.images,firstPost.postGoods,category,threadVideo,threadAudio,question,question.beUser,question.beUser.groups","page[number]":1,"page[limit]":10,"filter[isApproved]":1,"filter[isSite]":"yes"};r.status.run((function(){return t.$store.dispatch("jv/get",["threads",{params:e}])})).then((function(e){t.loading=!1,t.threadsData=[].concat(Object(o.a)(t.threadsData),Object(o.a)(e))}),(function(e){t.handleError(e)})).finally((function(){t.loading=!1}))},audioPlay:function(t){this.currentAudioId&&this.currentAudioId!==t&&this.$refs["audio".concat(this.currentAudioId)][0].pause(),this.currentAudioId=t}}},v=(n(1151),n(11)),component=Object(v.a)(f,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],staticClass:"infocontainer"},[t.forums&&t.forums.users?n("div",{staticClass:"info"},[n("h2",{class:t.normal?"info-title":"info-title tcolor"},[t._v("\n      "+t._s(t.codeTitle)+"\n    ")]),t._v(" "),n("div",{staticClass:"content-info abs"},[n("p",{staticClass:"payinfo-title"},[t._v(t._s(t.codeTips))]),t._v(" "),n("p",[n("span",{staticClass:"color"},[t._v(t._s(t.$t("site.creationtime")))]),t._v(" "),n("span",{staticClass:"workdate"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_install))])]),t._v(" "),n("p",[n("span",{staticClass:"date color"},[t._v(t._s(t.$t("site.circlemaster")))]),t._v(" "),t.forums.set_site&&t.forums.set_site.site_author?n("span",{staticClass:"img"},[n("Avatar",{staticClass:"avatar",attrs:{user:{username:t.forums.set_site&&t.forums.set_site.site_author&&t.forums.set_site.site_author.username||"",avatarUrl:t.forums.set_site&&t.forums.set_site.site_author&&t.forums.set_site.site_author.avatar||""},size:30,round:!0}})],1):n("span",[n("avatar",{attrs:{user:{id:0,username:"无",avatarUrl:""},"prevent-jump":!0,size:30,round:!0}})],1),t._v(" "),n("span",{staticClass:"workdate"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_author&&t.forums.set_site.site_author.username||""))])]),t._v(" "),n("p",[n("span",{staticClass:"date color"},[t._v(t._s(t.$t("home.theme")))]),t._v(" "),n("span",{staticClass:"workdate bold"},[t._v(t._s(t.forums.other&&t.forums.other.count_users))])]),t._v(" "),n("p",{staticClass:"member-img"},t._l(t.forums.users,(function(t,e){return n("span",{key:e,staticClass:"img"},[n("Avatar",{staticClass:"avatar",attrs:{user:t,size:30,round:!0}})],1)})),0),t._v(" "),n("p",[n("span",{staticClass:"date color"},[t._v(t._s(t.$t("manage.contents")))]),t._v(" "),n("span",{staticClass:"workdate bold"},[t._v(t._s(t.forums.other&&t.forums.other.count_threads))])]),t._v(" "),n("p",[n("span",{staticClass:"date color "},[t._v(t._s(t.$t("site.circlemode")))]),t._v(" "),n("span",{staticClass:"workdate"},[t._v("\n          "+t._s(t.forums.set_site&&"pay"===t.forums.set_site.site_mode?t.$t("site.paymentmode")+"， ¥"+(t.forums.set_site&&t.forums.set_site.site_price||0)+t.$t("post.yuan")+"， "+t.$t("site.periodvalidity")+(t.forums.set_site&&t.forums.set_site.site_expire||0)+t.$t("site.day"):t.$t("site.publicmode"))+"\n        ")])]),t._v(" "),n("div",{staticClass:"myauthority"},[n("div",{staticClass:"myauth-t "},[t._v(t._s(t.$t("site.myauthority")))]),t._v(" "),n("div",{staticClass:"myauth-c"},t._l(t.permission,(function(e,o){return n("span",{key:o},[t._v(t._s(t.$t("permission."+e.permission.replace(/\./g,"_"))))])})),0)]),t._v(" "),n("p",[n("span",{staticClass:"date color rel"},[t._v(t._s(t.$t("manage.siteintroduction")))]),t._v(" "),n("span",{staticClass:"workdate2"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_introduction))])])]),t._v(" "),n("p",[n("span",{staticClass:"bold"},[t._v(t._s(t.inviteData.user&&t.inviteData.user.username))]),t._v("\n      邀请您，作为\n      "),n("span",{staticClass:"bold"},[t._v(t._s("[ "+(t.inviteData.group&&t.inviteData.group.name?t.inviteData.group.name:"")+" ]"))]),t._v("加入\n      "),n("span",{staticClass:"bold"},[t._v(t._s(t.forums.set_site&&t.forums.set_site.site_name))]),t._v("\n      "+t._s(t.$t("site.site"))+"\n    ")]),t._v(" "),n("div",[n("el-button",{staticClass:"r-button",attrs:{type:"primary"},on:{click:t.check}},[t._v("\n        "+t._s(t.$t("site.accepttheinvitationandbecome"))+"\n        "+t._s(t.inviteData.group&&t.inviteData.group.name)+"\n      ")])],1),t._v(" "),n("el-dialog",{attrs:{title:"提示",visible:t.dialogVisible,width:"30%","before-close":t.handleClose},on:{"update:visible":function(e){t.dialogVisible=e}}},[n("span",[t._v(t._s(t.codeTips))]),t._v(" "),n("span",{staticClass:"dialog-footer",attrs:{slot:"footer"},slot:"footer"},[n("el-button",{on:{click:function(e){t.dialogVisible=!1}}},[t._v("取 消")]),t._v(" "),n("el-button",{attrs:{type:"primary"},on:{click:t.submit}},[t._v("确 定")])],1)])],1):t._e(),t._v(" "),t.threadsData.length>0?n("div",{staticClass:"thread"},[n("div",{staticClass:"threadtitle"},[t._v("部分内容预览")]),t._v(" "),t._l(t.threadsData,(function(e,o){return[4===e.type?n("post-item",{key:o,ref:"audio"+(e&&e.threadAudio&&e.threadAudio._jv&&e.threadAudio._jv.id),refInFor:!0,attrs:{item:e,infoimage:!0,"can-detail":t.canDetail},on:{audioPlay:t.audioPlay}}):n("post-item",{key:o,attrs:{item:e,infoimage:!0,"can-detail":t.canDetail}})]}))],2):t._e()])}),[],!1,null,"49a42874",null);e.default=component.exports;installComponents(component,{Avatar:n(254).default,Avatar:n(254).default,PostItem:n(756).default})},716:function(t,e){t.exports={data:function(){return{title:"‎"}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},head:function(){return{title:"‎"!==this.title&&this.forums&&this.forums.set_site&&this.forums.set_site.site_name?"".concat(this.title," - ").concat(this.forums.set_site.site_name):this.title}}}},717:function(t,e,n){"use strict";n.d(e,"b",(function(){return o})),n.d(e,"c",(function(){return r})),n.d(e,"a",(function(){return c}));n(52),n(159);var o=function(time){var t=((window.currentTime||new Date)-new Date(time))/1e3;return 0===parseInt(t)?"刚刚发布":parseInt(t)<60?"".concat(parseInt(t),"秒前"):parseInt(t/60)<60?"".concat(parseInt(t/60),"分钟前"):parseInt(t/60/60)<16?"".concat(parseInt(t/60/60),"小时前"):time.replace(/T/," ").replace(/Z/,"").substring(0,16)},r=function(t){var e=t-Math.round(new Date/1e3);return parseInt(e/86400,0)},c=function(t){var e=Math.round(new Date(t)/1e3),n=Math.round(new Date/1e3)-e,o=parseInt(n/86400,0);if(o>365){var r=parseInt(n/86400/365,0);return"".concat(r,"年")}return"".concat(o,"天")}},718:function(t,e){function n(e,p){return t.exports=n=Object.setPrototypeOf||function(t,p){return t.__proto__=p,t},t.exports.default=t.exports,t.exports.__esModule=!0,n(e,p)}t.exports=n,t.exports.default=t.exports,t.exports.__esModule=!0},722:function(t,e,n){},723:function(t,e,n){},729:function(t,e,n){"use strict";n.r(e);var o={name:"ProductItem",props:{item:{type:Object,default:function(){}}},data:function(){return{}}},r=(n(747),n(11)),component=Object(r.a)(o,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return t.item?n("a",{staticClass:"product-container",attrs:{href:t.item.detailContent,target:"_blank",el:"nofollow"}},[n("el-image",{staticClass:"image",attrs:{src:t.item.imagePath,alt:t.item.title,fit:"cover"}},[n("div",{staticClass:"image-slot",attrs:{slot:"placeholder"},slot:"placeholder"},[n("i",{staticClass:"el-icon-loading"})])]),t._v(" "),n("div",{staticClass:"info"},[n("div",{staticClass:"info-header"},[n("div",{staticClass:"title"},[t._v(t._s(t.item.title))]),t._v(" "),t.item.price>0?n("div",{staticClass:"price"},[t._v(t._s(t.$t("post.yuanItem")+t.item.price+t.$t("post.yuan")))]):t._e()]),t._v(" "),n("div",{staticClass:"btn"},[n("svg-icon",{staticClass:"icon",attrs:{type:"product-icon"}}),t._v(t._s(t.$t("post.buyProudct")))],1)])],1):t._e()}),[],!1,null,"85619e88",null);e.default=component.exports;installComponents(component,{SvgIcon:n(62).default})},730:function(t,e,n){"use strict";n.r(e);n(13),n(108);var o={name:"AudioPlayer",props:{file:{type:Object,default:function(){}},currentAudio:{type:Object,default:function(){}}},data:function(){return{onDragging:!1,delta:0}},computed:{currentFile:function(){return this.file&&this.file.id?this.currentAudio.id===this.file.id:this.currentAudio.id===this.file._jv.id},playing:function(){return this.currentAudio.currentTime&&this.currentAudio.duration}},methods:{formatDuration:function(t){var e=Math.floor(t/60),n=(t-60*e).toString().substr(0,2);return"."===n[1]&&(n="0".concat(n[0])),"".concat(e,":").concat(n)},formatCurrentTime:function(t){var e=parseInt(t/60)%60,n=(t%60).toFixed(),o=e<10?"0".concat(e):e,s=n<10?"0".concat(n):n;return"".concat(o,":").concat(s)},convertToPercentage:function(){return this.currentAudio.currentTime/this.currentAudio.duration*100},onmousedown:function(){window.document.addEventListener("mousemove",this.dragging),window.document.addEventListener("mouseup",this.onMouseUp)},dragging:function(t){this.delta=t.clientX-this.$refs.progress.getBoundingClientRect().x,this.delta<0&&(this.delta=0),this.delta>290&&(this.delta=290);var time=this.delta/290*this.currentAudio.duration;this.$emit("seeking",time)},onMouseUp:function(){var time=this.delta/290*this.currentAudio.duration;this.$emit("seek",time),window.document.removeEventListener("mousemove",this.dragging),window.document.removeEventListener("mouseup",this.onMouseUp)}}},r=(n(748),n(11)),component=Object(r.a)(o,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"audio"},[t.currentFile&&t.currentAudio.isLoading?n("div",{staticClass:"control"},[n("svg-icon",{staticStyle:{"font-size":"32px"},attrs:{type:"loading",rotate:""}})],1):t.currentFile&&t.currentAudio.isPlay?n("div",{staticClass:"control",on:{click:function(e){return t.$emit("pause")}}},[n("svg-icon",{staticStyle:{"font-size":"32px"},attrs:{type:"audio-pause"}})],1):n("div",{staticClass:"control",on:{click:function(e){return t.$emit("play",t.file)}}},[n("svg-icon",{staticStyle:{"font-size":"32px"},attrs:{type:"audio-play"}})],1),t._v(" "),n("div",{staticClass:"info"},[n("div",{staticClass:"title"},[n("span",{staticClass:"title-audio"},[t._v(t._s(t.file.fileName||t.file.file_name))]),t._v(" "),t.currentFile&&t.playing?n("span",{staticClass:"duration-audio"},[t._v("\n        "+t._s(t.formatCurrentTime(t.currentAudio.currentTime))+" /\n        "+t._s(t.formatDuration(t.currentAudio.duration))+"\n      ")]):n("span",{staticClass:"duration-audio"},[t._v("--:--")])]),t._v(" "),t.currentFile&&t.playing?n("div",{ref:"progress",staticClass:"progress",attrs:{id:"progress"}},[n("div",{staticClass:"progress-item",style:{width:t.convertToPercentage()+"%"}}),t._v(" "),n("div",{staticClass:"control-ball",style:{left:t.convertToPercentage()+"%"},attrs:{id:"control-ball"},on:{mousedown:t.onmousedown}})]):t._e()])])}),[],!1,null,"7fe73b95",null);e.default=component.exports;installComponents(component,{SvgIcon:n(62).default})},733:function(t,e,n){},746:function(t,e){t.exports=function(t){if(void 0===t)throw new ReferenceError("this hasn't been initialised - super() hasn't been called");return t},t.exports.default=t.exports,t.exports.__esModule=!0},747:function(t,e,n){"use strict";n(722)},748:function(t,e,n){"use strict";n(723)},749:function(t,e){t.exports=function(){if("undefined"==typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"==typeof Proxy)return!0;try{return Boolean.prototype.valueOf.call(Reflect.construct(Boolean,[],(function(){}))),!0}catch(t){return!1}},t.exports.default=t.exports,t.exports.__esModule=!0},756:function(t,e,n){"use strict";n.r(e);n(13),n(24);var o=n(724),time=n(717),r=n(731),c=n(715),l=n.n(c),d=n(158),m=n.n(d),h={name:"PostItem",filters:{formatDate:function(t){return Object(time.b)(t)}},mixins:[l.a,m.a],props:{item:{type:Object,default:function(){}},showLike:{type:Boolean,default:!0},showComment:{type:Boolean,default:!0},showShare:{type:Boolean,default:!0},lazy:{type:Boolean,default:!0},infoimage:{type:Boolean,default:!1},canDetail:{type:Boolean,default:!1},padding:{type:String,default:"20.5px 20px 30px"}},data:function(){return{loading:!1,showVideoPop:!1,isLiked:!1,currentAudio:{id:"",url:"",currentTime:"",duration:"",audio:"",seeking:!1,isPlay:!1,isLoading:!1}}},computed:{unpaid:function(){return!(this.item.paid||0===parseFloat(this.item.price))},forums:function(){return this.$store.state.site.info.attributes||{}}},watch:{item:{handler:function(t){this.isLiked=t.firstPost&&t.firstPost.isLiked,this.likeCount=t.firstPost&&t.firstPost.likeCount},deep:!0,immediate:!0}},mounted:function(){this.currentAudio.audio=document.getElementById("audio-player".concat(this.item&&this.item._jv&&this.item._jv.id))},methods:{handleLike:function(){var t=this;if(this.$store.getters["session/get"]("isLogin")){if(this.loading)return;if(!this.item.firstPost.canLike)return void this.$message.error(this.$t("topic.noThreadLikePermission"));this.loading=!0;var e=!this.isLiked,n={_jv:{type:"posts",id:this.item.firstPost&&this.item.firstPost._jv&&this.item.firstPost._jv.id},isLiked:e};return this.$store.dispatch("jv/patch",n).then((function(){t.$message.success(e?t.$t("discuzq.msgBox.likeSuccess"):t.$t("discuzq.msgBox.cancelLikeSuccess")),e?t.likeCount+=1:t.likeCount-=1,t.isLiked=e,t.$emit("change")}),(function(e){t.handleError(e)})).finally((function(){t.loading=!1}))}this.$message.warning("请登录"),window.setTimeout((function(){t.headerTologin()}),1e3)},toDetail:function(){this.item.isDraft||this.canViewPostsFn()&&this.routerLink()},onClickImage:function(){this.unpaid&&this.canViewPostsFn()&&this.routerLink()},openVideo:function(){this.canViewPostsFn()&&(this.unpaid?this.routerLink():"/"===this.$route.path||"/site/search"===this.$route.path||"category-id"===this.$route.name?this.$emit("showVideoFn",this.item.threadVideo):this.showVideoPop=!0)},routerLink:function(){window.open("/thread/".concat(this.item._jv&&this.item._jv.id),"_blank")},onClickContent:function(t){if(!this.item.isDraft){var e=t||window.event;"a"!==(e.target||e.srcElement).nodeName.toLocaleLowerCase()&&this.toDetail()}},canViewPostsFn:function(){var t=this;return this.item.canViewPosts?!this.canDetail||(this.$message.warning(this.$t("topic.joinAfterView")),!1):(this.$store.getters["session/get"]("isLogin")?this.$message.warning(this.$t("home.noPostingTopic")):(this.$message.warning(this.$t("core.not_authenticated")),window.setTimeout((function(){t.headerTologin()}),1e3)),!1)},formatTopicHTML:function(html){return o.a.parse(html)},extensionValidate:function(t){return r.extensionList.indexOf(t.toUpperCase())>0?t.toUpperCase():"UNKNOWN"},play:function(t){var e=this;this.unpaid?this.routerLink():this.canViewPostsFn()&&this.currentAudio.audio&&(this.currentAudio.id!==t._jv.id&&(this.resetAudio(this.currentAudio.audio),this.currentAudio.url=t.url||t.media_url,this.currentAudio.id=t._jv.id,this.currentAudio.audio.src=this.currentAudio.url,this.currentAudio.isLoading=!0,this.currentAudio.audio.load()),window.setTimeout((function(){e.currentAudio.audio.play(),e.currentAudio.isPlay=!0,e.currentAudio.audio.addEventListener("timeupdate",e.onProgressing),e.currentAudio.audio.addEventListener("ended",e.onEnded),e.$emit("audioPlay",e.currentAudio.id)}),0))},onProgressing:function(){this.currentAudio.seeking||(this.currentAudio.isLoading=!1,this.currentAudio.duration=this.currentAudio.audio.duration,this.currentAudio.currentTime=this.currentAudio.audio.currentTime)},onEnded:function(){this.resetAudio(this.currentAudio.audio)},resetAudio:function(audio){audio&&(audio.removeEventListener("timeupdate",this.onProgressing),audio.removeEventListener("ended",this.onEnded),this.currentAudio.isPlay=!1,this.currentAudio.duration="",this.currentAudio.currentTime="")},pause:function(){this.currentAudio.audio&&(this.currentAudio.isLoading=!1,this.currentAudio.isPlay=!1,this.currentAudio.audio.pause())},seek:function(time){this.currentAudio.audio&&(this.currentAudio.seeking=!1,this.currentAudio.currentTime=time,this.currentAudio.audio.currentTime=time)},seeking:function(time){this.currentAudio.seeking=!0,this.currentAudio.currentTime=time}}},f=(n(762),n(11)),component=Object(f.a)(h,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"post-container",style:{padding:t.padding}},[t.item.isEssence?n("div",{staticClass:"essence"},[n("svg-icon",{attrs:{type:"index-essence"}})],1):t._e(),t._v(" "),5===t.item.type&&t.item.question&&1===t.item.question.is_answer&&t.item.question.beUser?n("avatar",{staticClass:"avatar",attrs:{user:{id:t.item.question.beUser.id,username:t.item.question.beUser.username,avatarUrl:t.item.question.beUser.avatarUrl,isReal:t.item.question.beUser.isReal},"prevent-jump":t.canDetail}}):t.item.user?n("avatar",{staticClass:"avatar",attrs:{user:{id:t.item.user.id,username:t.item.user.username,avatarUrl:t.item.user.avatarUrl,isReal:t.item.user.isReal},"prevent-jump":t.canDetail}}):t._e(),t._v(" "),n("div",{staticClass:"main-cont"},[n("div",{staticClass:"top-flex"},[n("div",{staticClass:"top-user-info"},[5===t.item.type&&t.item.question&&1===t.item.question.is_answer&&t.item.question.beUser?n("nuxt-link",{staticClass:"user-info",attrs:{to:t.item.user.id>0?"/user/"+t.item.question.beUser.id:""}},[n("span",{staticClass:"user-name"},[t._v(t._s(t.item.question.beUser.username))]),t._v(" "),t.item.question.beUser&&t.item.question.beUser.groups&&t.item.question.beUser.groups.length>0&&t.item.question.beUser.groups[0].isDisplay?n("span",{staticClass:"admin"},[t._v("\n            ("+t._s(t.item.question.beUser.groups[0].name)+")\n          ")]):t._e()]):t.item.user?n("nuxt-link",{staticClass:"user-info",attrs:{to:t.item.user.id>0?"/user/"+t.item.user.id:""}},[n("span",{staticClass:"user-name"},[t._v(t._s(t.item.user.username))]),t._v(" "),t.item.user&&t.item.user.groups&&t.item.user.groups.length>0&&t.item.user.groups[0].isDisplay?n("span",{staticClass:"admin"},[t._v("\n            ("+t._s(t.item.user.groups[0].name)+")\n          ")]):t._e()]):t._e(),t._v(" "),5===t.item.type&&t.item.question&&1===t.item.question.is_answer?n("div",{staticClass:"answered"},[t._v("\n          "+t._s(t.$t("topic.answered"))+"\n        ")]):t._e()],1),t._v(" "),n("div",{staticClass:"time"},[t.item.createdAt?[t._v(t._s(t.$t("topic.publishAt"))+"\n          "+t._s(t._f("formatDate")(t.item.createdAt)))]:t._e()],2)]),t._v(" "),5===t.item.type?n("div",{staticClass:"question-user"},[t.item.question?t._e():n("div",[t._v("\n        "+t._s(t.$t("topic.questionsAndAnswers"))+"\n        "),n("div",{staticClass:"inline blue"},[t._v("\n          ￥"+t._s(t.item&&t.item.questionTypeAndMoney?t.item.questionTypeAndMoney.money:0)+"\n        ")])]),t._v(" "),t.item.question&&0===t.item.question.is_answer?n("div",{staticClass:"inline"},[n("div",{staticClass:"inline blue"},[t._v("\n          ￥"+t._s(t.item&&t.item.questionTypeAndMoney?t.item.questionTypeAndMoney.money:0)+"\n        ")]),t._v("\n        "+t._s(t.$t("topic.be"))+"\n        "),t.item.question&&t.item.question.beUser?n("nuxt-link",{staticClass:"grey",attrs:{to:t.item.question.beUser.id>0?"/user/"+t.item.question.beUser.id:""}},[t._v("@"+t._s(t.item.question.beUser.username))]):t._e(),t._v("\n        "+t._s(t.$t("topic.question"))+"\n      ")],1):t._e(),t._v(" "),t.item.question&&1===t.item.question.is_answer?[t._v("\n        "+t._s(t.$t("topic.answer"))+"\n        "),t.item.user?n("nuxt-link",{staticClass:"grey",attrs:{to:t.item.user.id>0?"/user/"+t.item.user.id:""}},[t._v("@"+t._s(t.item.user.username))]):t._e(),t._v("\n        "+t._s(t.$t("topic.of"))+t._s(t.$t("topic.question"))+"\n      ")]:t._e()],2):t._e(),t._v(" "),t.item.firstPost?[n("div",{staticClass:"first-post",on:{click:function(e){return e.target!==e.currentTarget?null:t.toDetail(e)}}},[n("a",{attrs:{href:"/thread/"+(t.item._jv&&t.item._jv.id)},on:{click:function(e){return e.preventDefault(),t.onClickContent(e)}}},[1===t.item.type?n("div",{staticClass:"title"},[t._v("\n            "+t._s(t.$t("home.released"))+"\n            "),n("svg-icon",{directives:[{name:"show",rawName:"v-show",value:parseFloat(t.item.price)>0||parseFloat(t.item.attachmentPrice)>0,expression:"\n                parseFloat(item.price) > 0 ||\n                  parseFloat(item.attachmentPrice) > 0\n              "}],staticClass:"blue icon-pay-yuan-inline",attrs:{type:"pay-yuan"}}),t._v(" "),n("span",{staticClass:"blue"},[t._v(t._s(t.item.title))])],1):n("div",{staticClass:"content"},[5===t.item.type?n("svg-icon",{staticClass:"icon-pay-yuan blue",attrs:{type:"question-icon"}}):6===t.item.type?n("svg-icon",{staticClass:"icon-pay-yuan blue",attrs:{type:"product-icon"}}):parseFloat(t.item.price)>0||parseFloat(t.item.attachmentPrice)>0?n("svg-icon",{staticClass:"icon-pay-yuan grey",attrs:{type:"pay-yuan"}}):t._e(),t._v(" "),n("div",{class:{"content-block":5===t.item.type||6===t.item.type||parseFloat(t.item.price)>0,blue:5===t.item.type},domProps:{innerHTML:t._s(t.$xss(t.formatTopicHTML(t.item.firstPost.summary)))}})],1)]),t._v(" "),t.item.firstPost.images&&t.item.firstPost.images.length>0?n("div",{directives:[{name:"viewer",rawName:"v-viewer",value:{url:"data-source"},expression:"{ url: 'data-source' }"}],staticClass:"images",on:{click:function(e){return e.target!==e.currentTarget?null:t.toDetail(e)}}},t._l(t.item.firstPost.images.slice(0,3),(function(image,e){return n("el-image",{key:e,class:{image:!0,infoimage:t.infoimage},attrs:{src:image.thumbUrl,"data-source":t.unpaid?"":image.url,alt:image.filename,fit:"cover",lazy:t.lazy},on:{click:function(e){return e.target!==e.currentTarget?null:t.onClickImage(e)}}},[n("div",{staticClass:"image-slot",attrs:{slot:"placeholder"},slot:"placeholder"},[n("i",{staticClass:"el-icon-loading"})])])})),1):t._e(),t._v(" "),t.item.firstPost.images&&t.item.firstPost.images.length>3?n("div",{staticClass:"image-count",on:{click:t.toDetail}},[t._v("\n          "+t._s(t.$t("home.total"))+" "+t._s(t.item.firstPost.images.length)+"\n          "+t._s(t.$t("home.seeAllImage"))+"\n        ")]):t._e(),t._v(" "),2===t.item.type&&t.item.threadVideo?n("div",{staticClass:"video-main",on:{click:function(e){return e.stopPropagation(),t.openVideo(e)}}},[t.item.threadVideo.cover_url?n("el-image",{staticClass:"video-img-cover",attrs:{src:t.item.threadVideo.cover_url,alt:t.item.threadVideo.file_name,fit:"cover",lazy:t.lazy}}):n("div",{staticClass:"no-cover"},[t._v(t._s(t.$t("home.noPoster")))]),t._v(" "),n("svg-icon",{staticClass:"video-play",attrs:{type:"video-play"}})],1):t._e(),t._v(" "),t.item.firstPost.attachments&&t.item.firstPost.attachments.length>0?n("div",{staticClass:"attachment",on:{click:t.toDetail}},[n("svg-icon",{attrs:{type:t.extensionValidate(t.item.firstPost.attachments[0].extension)}}),t._v(" "),n("div",{staticClass:"name text-hidden"},[t._v("\n            "+t._s(t.item.firstPost.attachments[0].fileName)+"\n          ")]),t._v(" "),t.item.firstPost.attachments.length>1?n("div",{staticClass:"total"},[t._v("\n            "+t._s(t.$t("home.etc")+t.item.firstPost.attachments.length+t.$t("home.attachmentTotal"))+"\n          ")]):t._e()],1):t._e()]),t._v(" "),6===t.item.type?n("product-item",{attrs:{item:t.item&&t.item.firstPost&&t.item.firstPost.postGoods}}):t._e(),t._v(" "),4===t.item.type?n("div",{on:{click:function(e){return e.target!==e.currentTarget?null:t.toDetail(e)}}},[n("audio-player",{attrs:{file:t.item&&t.item.threadAudio,"current-audio":t.currentAudio},on:{play:t.play,pause:t.pause,seek:t.seek,seeking:t.seeking}}),t._v(" "),n("audio",{staticClass:"audio-player",staticStyle:{display:"none"},attrs:{id:"audio-player"+(t.item._jv&&t.item._jv.id),src:t.currentAudio.url}})],1):t._e(),t._v(" "),t.item.location?n("nuxt-link",{staticClass:"location",attrs:{to:"/topic/position?longitude="+t.item.longitude+"&latitude="+t.item.latitude}},[n("span",{staticClass:"flex"},[n("svg-icon",{staticClass:"icon",attrs:{type:"location"}}),t._v("\n          "+t._s(t.item.location)+"\n        ")],1)]):t._e(),t._v(" "),t.canDetail?t._e():n("div",{staticClass:"bottom-handle"},[n("div",{staticClass:"left"},[t.showLike?n("div",{staticClass:"btn like",class:{liked:t.isLiked},on:{click:t.handleLike}},[n("svg-icon",{staticClass:"icon",attrs:{type:"like"},on:{click:t.handleLike}}),t._v("\n            "+t._s(t.isLiked?t.$t("topic.liked"):t.$t("topic.like"))+"\n            "+t._s(t.likeCount>0?t.likeCount:"")+"\n          ")],1):t._e(),t._v(" "),t.showComment?n("div",{staticClass:"btn comment",on:{click:t.toDetail}},[n("svg-icon",{staticClass:"icon",attrs:{type:"post-comment"}}),t._v("\n            "+t._s(t.$t("topic.comment"))+"\n            "+t._s(t.item.postCount-1>0?t.item.postCount-1:"")+"\n          ")],1):t._e(),t._v(" "),t.item._jv&&t.item._jv.id&&t.showShare?n("share-popover",{attrs:{"threads-id":t.item._jv.id}},[n("div",{staticClass:"btn share"},[n("svg-icon",{staticClass:"icon",attrs:{type:"link"}}),t._v("\n              "+t._s(t.$t("topic.share"))+"\n            ")],1)]):t._e(),t._v(" "),t._t("btn-edit"),t._v(" "),t._t("btn-delete")],2),t._v(" "),t._t("bottom-right")],2)]:t._e()],2),t._v(" "),t.showVideoPop?n("video-pop",{attrs:{"cover-url":t.item.threadVideo.cover_url,url:t.item.threadVideo.media_url},on:{remove:function(e){t.showVideoPop=!1}}}):t._e()],1)}),[],!1,null,"1af0eecb",null);e.default=component.exports;installComponents(component,{SvgIcon:n(62).default,Avatar:n(254).default,ProductItem:n(729).default,AudioPlayer:n(730).default,SharePopover:n(740).default,VideoPop:n(725).default})},762:function(t,e,n){"use strict";n(733)},802:function(t,e){function n(){return t.exports=n=Object.assign||function(t){for(var i=1;i<arguments.length;i++){var source=arguments[i];for(var e in source)Object.prototype.hasOwnProperty.call(source,e)&&(t[e]=source[e])}return t},t.exports.default=t.exports,t.exports.__esModule=!0,n.apply(this,arguments)}t.exports=n,t.exports.default=t.exports,t.exports.__esModule=!0},810:function(t,e,n){var o=n(732).default,r=n(746);t.exports=function(t,e){return!e||"object"!==o(e)&&"function"!=typeof e?r(t):e},t.exports.default=t.exports,t.exports.__esModule=!0},811:function(t,e){function n(e){return t.exports=n=Object.setPrototypeOf?Object.getPrototypeOf:function(t){return t.__proto__||Object.getPrototypeOf(t)},t.exports.default=t.exports,t.exports.__esModule=!0,n(e)}t.exports=n,t.exports.default=t.exports,t.exports.__esModule=!0},812:function(t,e,n){var o=n(718);t.exports=function(t,e){t.prototype=Object.create(e.prototype),t.prototype.constructor=t,o(t,e)},t.exports.default=t.exports,t.exports.__esModule=!0},813:function(t,e,n){var o=n(718),r=n(749);function c(e,n,l){return r()?(t.exports=c=Reflect.construct,t.exports.default=t.exports,t.exports.__esModule=!0):(t.exports=c=function(t,e,n){var a=[null];a.push.apply(a,e);var r=new(Function.bind.apply(t,a));return n&&o(r,n.prototype),r},t.exports.default=t.exports,t.exports.__esModule=!0),c.apply(null,arguments)}t.exports=c,t.exports.default=t.exports,t.exports.__esModule=!0},814:function(t,e,n){var o=n(718);t.exports=function(t,e){if("function"!=typeof e&&null!==e)throw new TypeError("Super expression must either be null or a function");t.prototype=Object.create(e&&e.prototype,{constructor:{value:t,writable:!0,configurable:!0}}),e&&o(t,e)},t.exports.default=t.exports,t.exports.__esModule=!0}}]);