(window.webpackJsonp=window.webpackJsonp||[]).push([[26,9],{"0Y4v":function(e,t,s){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default={name:"table-no-list"}},"5shi":function(e,t,s){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var a=r(s("QbLZ"));s("gtKr");var i=r(s("u8Dz"));function r(e){return e&&e.__esModule?e:{default:e}}t.default=(0,a.default)({name:"cont-arrange-view"},i.default)},"7qpD":function(e,t,s){"use strict";s.r(t);var a=s("INw2"),i=s("EFx4");for(var r in i)["default"].indexOf(r)<0&&function(e){s.d(t,e,(function(){return i[e]}))}(r);s("hc7x");var n=s("KHd+"),o=Object(n.a)(i.default,a.a,a.b,!1,null,"7d149013",null);t.default=o.exports},Dt3C:function(e,t,s){"use strict";s.r(t);var a=s("qxrf"),i=s("aoOm");for(var r in i)["default"].indexOf(r)<0&&function(e){s.d(t,e,(function(){return i[e]}))}(r);var n=s("KHd+"),o=Object(n.a)(i.default,a.a,a.b,!1,null,null,null);t.default=o.exports},EFx4:function(e,t,s){"use strict";s.r(t);var a=s("0Y4v"),i=s.n(a);for(var r in a)["default"].indexOf(r)<0&&function(e){s.d(t,e,(function(){return a[e]}))}(r);t.default=i.a},FCu8:function(e,t,s){"use strict";s.r(t);var a=s("tr5V"),i=s.n(a);for(var r in a)["default"].indexOf(r)<0&&function(e){s.d(t,e,(function(){return a[e]}))}(r);t.default=i.a},INw2:function(e,t,s){"use strict";s.d(t,"a",(function(){return a})),s.d(t,"b",(function(){return i}));var a=function(){var e=this.$createElement;this._self._c;return this._m(0)},i=[function(){var e=this.$createElement,t=this._self._c||e;return t("div",{staticClass:"table-no-list"},[t("p",[this._v("暂无数据")])])}]},Kvoi:function(e,t,s){},Skey:function(e,t,s){"use strict";s.r(t);var a=s("jNVq"),i=s("FCu8");for(var r in i)["default"].indexOf(r)<0&&function(e){s.d(t,e,(function(){return i[e]}))}(r);var n=s("KHd+"),o=Object(n.a)(i.default,a.a,a.b,!1,null,null,null);t.default=o.exports},Xz3T:function(e,t,s){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var a=u(s("4gYi")),i=u(s("Dt3C")),r=u(s("rWG0")),n=u(s("7qpD")),o=u(s("VVfg")),l=u(s("CKnL")),c=u(s("6NK7"));function u(e){return e&&e.__esModule?e:{default:e}}t.default={data:function(){return{searchUserName:"",keyWords:"",showSensitiveWords:!1,pageOptions:[{value:10,label:"每页显示10条"},{value:20,label:"每页显示20条"},{value:30,label:"每页显示30条"}],pageSelect:10,searchReview:[{value:0,label:"未审核"},{value:2,label:"已忽略"}],searchReviewSelect:0,categoriesList:[{label:"所有分类",value:0}],categoriesListSelect:[0],searchTime:[{value:1,label:"全部"},{value:2,label:"最近一周"},{value:3,label:"最近一个月"},{value:4,label:"最近三个月"}],searchTimeSelect:1,relativeTime:["",""],reasonForOperation:[{value:"无",label:"无"},{value:"广告/SPAM",label:"广告/SPAM"},{value:"恶意灌水",label:"恶意灌水"},{value:"违规内容",label:"违规内容"},{value:"文不对题",label:"文不对题"},{value:"重复发帖",label:"重复发帖"},{value:"我很赞同",label:"我很赞同"},{value:"精品文章",label:"精品文章"},{value:"原创内容",label:"原创内容"},{value:"其他",label:"其他"}],reasonForOperationSelect:1,appleAll:!1,themeList:[],currentPaga:1,total:0,pageCount:1,ignoreStatus:!0,submitForm:[],showViewer:!1,url:[],subLoading:!1,btnLoading:0}},methods:{titleIcon:function(e){return c.default.titleIcon(e)},imgShowClick:function(e,t){var s=this;this.url=[];var a=[];e.forEach((function(e){a.push(e.url)})),this.url.push(a[t]),a.forEach((function(e,a){a>t&&s.url.push(e)})),a.forEach((function(e,a){a<t&&s.url.push(e)})),this.showViewer=!0},closeViewer:function(){this.showViewer=!1},handleSelectionChange:function(e){this.multipleSelection=e,this.multipleSelection.length>=1?this.deleteStatus=!1:this.deleteStatus=!0},reasonForOperationChange:function(e,t){this.submitForm[t].message=e},handleCurrentChange:function(e){document.getElementsByClassName("index-main-con__main")[0].scrollTop=0,o.default.setLItem("currentPag",e),this.currentPaga=e,this.getPostsList(e)},postSearch:function(){this.ignoreStatus=2!==this.searchReviewSelect,this.currentPaga=1,this.getPostsList()},searchTimeChange:function(e){var t=new Date,s=new Date;switch(this.relativeTime=[],e){case 1:this.relativeTime.push("","");break;case 2:s.setTime(s.getTime()-6048e5),this.relativeTime.push(this.formatDate(t),this.formatDate(s));break;case 3:s.setTime(s.getTime()-2592e6),this.relativeTime.push(this.formatDate(t),this.formatDate(s));break;case 4:s.setTime(s.getTime()-7776e6),this.relativeTime.push(this.formatDate(t),this.formatDate(s));break;default:this.$message.error("搜索日期选择错误，请重新选择！或 刷新页面（F5）")}},submitClick:function(){this.subLoading=!0;var e=[],t=[],s=[];this.submitForm.forEach((function(a,i){0===a.radio?e.push({id:a.id,isApproved:1}):1===a.radio?s.push({id:a.id,isDeleted:!0}):2===a.radio&&t.push({id:a.id,isApproved:2})})),e.length>=1&&this.patchPostsBatch(e),s.length>=1&&this.patchPostsBatch(s),t.length>=1&&this.patchPostsBatch(t)},radioChange:function(e,t){switch(e){case 0:this.submitForm[t].isApproved=1;break;case 1:this.submitForm[t].isDeleted=!0;break;case 2:this.submitForm[t].isApproved=2}},allOperationsSubmit:function(e){this.btnLoading=e;var t=[];switch(e){case 1:this.submitForm.forEach((function(e,s){t.push({id:e.id,isApproved:1})}));break;case 2:this.submitForm.forEach((function(e,s){t.push({id:e.id,isDeleted:!0})}));break;case 3:this.submitForm.forEach((function(e,s){t.push({id:e.id,isApproved:2})}))}this.patchPostsBatch(t)},singleOperationSubmit:function(e,t,s,a){var i=[{id:Number(s)}];switch(e){case 1:i[0].isApproved=1,i[0].message=this.submitForm[a].message,this.patchPosts(i,s);break;case 2:i[0].isDeleted=!0,i[0].message=this.submitForm[a].message,this.patchPosts(i,s);break;case 3:i[0].isApproved=2,i[0].message=this.submitForm[a].message,this.patchPosts(i,s)}},viewClick:function(e){var t=this.$router.resolve({path:"/thread/"+e});window.open(t.href,"_blank")},editClick:function(e,t){var s=this.$router.resolve({path:"/reply-to-topic/"+e+"/"+t,query:{edit:"reply"}});window.open(s.href,"_blank")},formatDate:function(e){return this.$dayjs(e).format("YYYY-MM-DD HH:mm")},getPostsList:function(e){var t=this;this.appFetch({url:"posts_get_v3",method:"get",data:{page:e,perPage:this.pageSelect,isDeleted:"no",nickname:this.searchUserName,q:this.keyWords,isApproved:this.searchReviewSelect,createdAtBegin:this.relativeTime[1],createdAtEnd:this.relativeTime[0],categoryId:this.categoriesListSelect[this.categoriesListSelect.length-1],highlight:this.showSensitiveWords?"yes":"no",sort:"-created_at"}}).then((function(e){if(e.errors)t.$message.error(e.errors[0].code);else{if(0!==e.Code)return void t.$message.error(e.Message);t.themeList=[],t.submitForm=[],t.themeList=e.Data.pageData,t.total=e.Data.totalCount,t.pageCount=e.Data.totalPage,t.themeList.forEach((function(e,s){t.submitForm.push({Select:"无",radio:"",type:2,id:e.postId,isApproved:0,isDeleted:!1,message:""})}))}})).catch((function(e){}))},getCategories:function(){var e=this;this.appFetch({url:"categories_get_v3",method:"get",data:{}}).then((function(t){if(t.errors)e.$message.error(t.errors[0].code);else{if(0!==t.Code)return void e.$message.error(t.Message);t.Data.forEach((function(t,s){if(t.children.length){var a=[];t.children.forEach((function(e){a.push({label:e.name,value:e.searchIds})})),e.categoriesList.push({label:t.name,value:t.searchIds,children:a})}else e.categoriesList.push({label:t.name,value:t.searchIds})}))}})).catch((function(e){}))},patchPostsBatch:function(e){var t=this;this.appFetch({url:"submit_review_post_v3",method:"post",data:{type:2,data:e}}).then((function(e){if(t.subLoading=!1,t.btnLoading=0,e.errors)t.$message.error(e.errors[0].code);else{if(0!==e.Code)return void t.$message.error(e.Message);t.getPostsList(Number(o.default.getLItem("currentPag"))||1),t.$message({message:"操作成功",type:"success"})}})).catch((function(e){}))},patchPosts:function(e,t){var s=this;this.appFetch({url:"submit_review_post_v3",method:"post",data:{type:2,data:e}}).then((function(e){if(s.subLoading=!1,s.btnLoading=0,e.errors)s.$message.error(e.errors[0].code);else{if(0!==e.Code)return void s.$message.error(e.Message);s.getPostsList(Number(o.default.getLItem("currentPag"))||1),s.$message({message:"操作成功",type:"success"})}})).catch((function(e){}))},contentIndexes:function(e,t){return c.default.dataTypeJudgment(e,t)}},created:function(){this.getCategories(),this.getPostsList(Number(o.default.getLItem("currentPag"))||1)},components:{Card:a.default,ContArrange:i.default,Page:r.default,tableNoList:n.default,ElImageViewer:l.default}}},aoOm:function(e,t,s){"use strict";s.r(t);var a=s("5shi"),i=s.n(a);for(var r in a)["default"].indexOf(r)<0&&function(e){s.d(t,e,(function(){return a[e]}))}(r);t.default=i.a},gtKr:function(e,t,s){},hc7x:function(e,t,s){"use strict";s("Kvoi")},jNVq:function(e,t,s){"use strict";s.d(t,"a",(function(){return a})),s.d(t,"b",(function(){return i}));var a=function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("div",{staticClass:"reply-review-box"},[s("div",{staticClass:"cont-review-header"},[s("div",{staticClass:"cont-review-header__lf"},[s("div",[s("span",{staticClass:"cont-review-header__lf-title"},[e._v("用户名：")]),e._v(" "),s("el-input",{attrs:{size:"medium",placeholder:"搜索用户名",clearable:""},model:{value:e.searchUserName,callback:function(t){e.searchUserName=t},expression:"searchUserName"}})],1),e._v(" "),s("div",[s("span",{staticClass:"cont-review-header__lf-title"},[e._v("每页显示：")]),e._v(" "),s("el-select",{attrs:{size:"medium",placeholder:"选择每页显示"},model:{value:e.pageSelect,callback:function(t){e.pageSelect=t},expression:"pageSelect"}},e._l(e.pageOptions,(function(e){return s("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),1)],1)]),e._v(" "),s("div",{staticClass:"cont-review-header__rt"},[s("div",[s("span",{staticClass:"cont-review-header__lf-title"},[e._v("内容包含：")]),e._v(" "),s("el-input",{staticClass:"content-contains-input",attrs:{size:"medium",clearable:"",placeholder:"搜索关键词"},model:{value:e.keyWords,callback:function(t){e.keyWords=t},expression:"keyWords"}}),e._v(" "),s("el-checkbox",{model:{value:e.showSensitiveWords,callback:function(t){e.showSensitiveWords=t},expression:"showSensitiveWords"}},[e._v("显示敏感词")])],1),e._v(" "),s("div",{staticClass:"cont-review-header__rt-search"},[s("span",{staticClass:"cont-review-header__lf-title"},[e._v("搜索范围：")]),e._v(" "),s("el-select",{attrs:{size:"medium",placeholder:"选择审核状态"},model:{value:e.searchReviewSelect,callback:function(t){e.searchReviewSelect=t},expression:"searchReviewSelect"}},e._l(e.searchReview,(function(e){return s("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),1),e._v(" "),s("el-cascader",{attrs:{clearable:"",options:e.categoriesList,props:{expandTrigger:"hover",checkStrictly:!0}},on:{change:e.handleChange},model:{value:e.categoriesListSelect,callback:function(t){e.categoriesListSelect=t},expression:"categoriesListSelect"}}),e._v(" "),s("el-select",{attrs:{size:"medium",placeholder:"选择搜索时间"},on:{change:e.searchTimeChange},model:{value:e.searchTimeSelect,callback:function(t){e.searchTimeSelect=t},expression:"searchTimeSelect"}},e._l(e.searchTime,(function(e){return s("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),1),e._v(" "),s("el-button",{attrs:{size:"small",type:"primary"},on:{click:e.postSearch}},[e._v("搜索")])],1)])]),e._v(" "),s("div",{staticClass:"cont-review-table"},[e._l(e.themeList,(function(t,a){return s("ContArrange",{key:a,attrs:{replyBy:t.nickname?t.nickname:"该用户被删除",themeName:t.title,titleIcon:e.titleIcon(t),finalPost:e.formatDate(t.updatedAt),ip:t.ip,userId:t.user?t.userId:"该用户被删除"}},[s("div",{staticClass:"cont-review-table__side",attrs:{slot:"side"},slot:"side"},[s("el-radio-group",{on:{change:function(t){return e.radioChange(t,a)}},model:{value:e.submitForm[a].radio,callback:function(t){e.$set(e.submitForm[a],"radio",t)},expression:"submitForm[index].radio"}},[s("el-radio",{attrs:{label:0}},[e._v("通过")]),e._v(" "),s("el-radio",{attrs:{label:1}},[e._v("删除")]),e._v(" "),2!==t.isApproved?s("el-radio",{attrs:{label:2,disabled:2===t.isApproved}},[e._v("忽略")]):e._e()],1)],1),e._v(" "),s("div",{staticClass:"cont-review-table__main",attrs:{slot:"main"},slot:"main"},[s("a",{staticClass:"cont-review-table__main__cont-text",attrs:{href:"/thread/comment/"+t.postId+"?threadId="+t.threadId,target:"_blank"},domProps:{innerHTML:e._s(t.content.text)}}),e._v(" "),s("div",{staticClass:"cont-review-table__main__cont-imgs"},e._l(t.content.indexes,(function(a,i){return s("p",{key:i,staticClass:"cont-review-table__main__cont-imgs-p"},[s("img",{directives:[{name:"lazy",rawName:"v-lazy",value:a.thumbUrl,expression:"item.thumbUrl"}],attrs:{alt:a.fileName},on:{click:function(s){return e.imgShowClick(t.content.indexes,i)}}})])})),0)]),e._v(" "),s("div",{staticClass:"cont-review-table__footer",attrs:{slot:"footer"},slot:"footer"},[s("div",{staticClass:"cont-review-table__footer__lf"},[s("el-button",{attrs:{type:"text"},on:{click:function(s){return e.singleOperationSubmit(1,t.categoryId,t.postId,a)}}},[e._v("通过")]),e._v(" "),s("i"),e._v(" "),s("el-button",{attrs:{type:"text"},on:{click:function(s){return e.singleOperationSubmit(2,t.categoryId,t.postId,a)}}},[e._v("删除")]),e._v(" "),s("i"),e._v(" "),2!==t.isApproved?s("el-button",{attrs:{type:"text"},on:{click:function(s){return e.singleOperationSubmit(3,t.categoryId,t.postId,a)}}},[e._v("忽略")]):e._e()],1),e._v(" "),s("div",{staticClass:"cont-review-table__footer__rt"},[s("span",[e._v("操作理由：")]),e._v(" "),s("el-input",{attrs:{size:"medium",clearable:""},model:{value:e.submitForm[a].message,callback:function(t){e.$set(e.submitForm[a],"message",t)},expression:"submitForm[index].message"}}),e._v(" "),s("el-select",{attrs:{size:"medium",placeholder:"选择操作理由"},on:{change:function(t){return e.reasonForOperationChange(t,a)}},model:{value:e.submitForm[a].Select,callback:function(t){e.$set(e.submitForm[a],"Select",t)},expression:"submitForm[index].Select"}},e._l(e.reasonForOperation,(function(e){return s("el-option",{key:e.value,attrs:{label:e.label,value:e.value}})})),1)],1),e._v(" "),s("div",{staticClass:"cont-review-table__footer__bottom"},[s("el-button",{attrs:{type:"text"},on:{click:function(s){return e.viewClick(t.threadId)}}},[e._v("查看")])],1)])])})),e._v(" "),e.showViewer?s("el-image-viewer",{attrs:{"on-close":e.closeViewer,"url-list":e.url}}):e._e(),e._v(" "),s("tableNoList",{directives:[{name:"show",rawName:"v-show",value:e.themeList.length<1,expression:"themeList.length < 1"}]}),e._v(" "),e.pageCount>1?s("Page",{attrs:{"current-page":e.currentPaga,"page-size":e.pageSelect,total:e.total},on:{"current-change":e.handleCurrentChange}}):e._e()],2),e._v(" "),s("div",{staticClass:"cont-review-footer footer-btn"},[s("el-button",{attrs:{size:"small",type:"primary",loading:e.subLoading},on:{click:e.submitClick}},[e._v("提交")]),e._v(" "),s("el-button",{attrs:{type:"text",loading:1===e.btnLoading},on:{click:function(t){return e.allOperationsSubmit(1)}}},[e._v("全部通过")]),e._v(" "),s("el-button",{attrs:{type:"text",loading:2===e.btnLoading},on:{click:function(t){return e.allOperationsSubmit(2)}}},[e._v("全部删除")]),e._v(" "),s("el-button",{directives:[{name:"show",rawName:"v-show",value:e.ignoreStatus,expression:"ignoreStatus"}],attrs:{type:"text",loading:3===e.btnLoading},on:{click:function(t){return e.allOperationsSubmit(3)}}},[e._v("全部忽略")])],1)])},i=[]},qxrf:function(e,t,s){"use strict";s.d(t,"a",(function(){return a})),s.d(t,"b",(function(){return i}));var a=function(){var e=this,t=e.$createElement,s=e._self._c||t;return s("div",{staticClass:"cont-arrange-box"},[s("div",{staticClass:"cont-arrange-main"},[s("div",{staticClass:"cont-arrange__lf-side"},[e._t("side")],2),e._v(" "),s("main",{staticClass:"cont-arrange__rt-main"},[s("div",{staticClass:"cont-arrange__rt-main-header"},[s("div",{staticClass:"cont-arrange__rt-main-header__release"},[e.$attrs.author?s("p",{ref:"userName",staticClass:"cont-arrange-p"},[s("a",{staticStyle:{color:"#333333"},attrs:{href:"/user/"+e.$attrs.userId,target:"_blank"}},[e._v("\n              "+e._s(e.$attrs.author)+"\n            ")])]):e._e(),e._v(" "),e.$attrs.replyBy?s("p",{ref:"userName",staticClass:"cont-arrange-p"},[s("a",{staticStyle:{color:"#333333"},attrs:{href:"/user/"+e.$attrs.userId,target:"_blank"}},[e._v("\n              "+e._s(e.$attrs.replyBy)+"\n            ")])]):e._e(),e._v(" "),e.$attrs.establish?s("p",{ref:"userName",staticClass:"cont-arrange-p"},[s("a",{staticStyle:{color:"#333333"},attrs:{href:"/user/"+e.$attrs.userId,target:"_blank"}},[e._v("\n              "+e._s(e.$attrs.establish)+"\n            ")])]):e._e(),e._v(" "),e.$attrs.author?s("p",{staticClass:"cont-arrange-span"},[e._v("发布于")]):e._e(),e._v(" "),e.$attrs.establish?s("p",{staticClass:"cont-arrange-span"},[e._v("创建于")]):e._e(),e._v(" "),e.$attrs.replyBy?s("p",{staticClass:"cont-arrange-span"},[e._v("回复主题")]):e._e(),e._v(" "),e.$attrs.time?s("p",{staticClass:"cont-arrange-title"},[e._v(e._s(e.$attrs.time))]):e._e(),e._v(" "),e.$attrs.theme?s("p",{staticClass:"cont-arrange-title"},[e._v(e._s(e.$attrs.theme))]):e._e(),e._v(" "),e.$attrs.themeName?s("p",{ref:"themeName",class:e.$attrs.themeName?"themeName":""},[e._v("\n            "+e._s(e.$attrs.themeName)+"\n            "),e.$attrs.titleIcon?s("span",{staticClass:"iconfont cont-arrange__rt-main-header__release-title-icon",class:e.$attrs.titleIcon}):e._e()]):e._e()]),e._v(" "),e.$attrs.prply>=0&&e.$attrs.browse>=0?s("div",{staticClass:"cont-arrange__rt-main-header__reply-view rt-box"},[s("span",[e._v("回复/查看：")]),e._v(" "),s("span",[e._v(e._s(e.$attrs.prply)+"/"+e._s(e.$attrs.browse))])]):e._e(),e._v(" "),e.$attrs.last?s("div",{staticClass:"cont-arrange__rt-main-header__last-reply rt-box"},[s("span",[e._v("最后回复：")]),e._v(" "),s("span",[e._v(e._s(e.$attrs.last))])]):e._e(),e._v(" "),e.$attrs.ip?s("div",{staticClass:" rt-box"},[s("span",[e._v("IP：")]),e._v(" "),s("span",[e._v(e._s(e.$attrs.ip))])]):e._e(),e._v(" "),e.$attrs.releaseTime?s("div",{staticClass:"cont-arrange__rt-main-header__release-time rt-box"},[s("span",[e._v("发布时间：")]),e._v(" "),s("span",[e._v(e._s(e.$attrs.releaseTime))])]):e._e(),e._v(" "),e.$attrs.finalPost?s("div",{staticClass:"cont-arrange__rt-main-header__release-time rt-box"},[s("span",[e._v("更新时间：")]),e._v(" "),s("span",[e._v(e._s(e.$attrs.finalPost))])]):e._e(),e._v(" "),e.$attrs.deleTime?s("div",{staticClass:" rt-box"},[s("span",[e._v("删除时间：")]),e._v(" "),s("span",[e._v(e._s(e.$attrs.deleTime))])]):e._e(),e._v(" "),e.$attrs.numbertopic>=0?s("div",{staticClass:"cont-arrange__rt-main-header__release-time rt-box"},[s("span",[e._v("主题数：")]),e._v(" "),s("span",[e._v(e._s(e.$attrs.numbertopic))])]):e._e(),e._v(" "),e.$attrs.heatNumber>=0?s("div",{staticClass:"cont-arrange__rt-main-header__release-time rt-box"},[s("span",[e._v("热度数：")]),e._v(" "),s("span",[e._v(e._s(e.$attrs.heatNumber))])]):e._e(),e._v(" "),e.$attrs.type?s("div",{staticClass:"cont-arrange__rt-main-header__release-time rt-box"},[s("span",[e._v("类型：")]),e._v(" "),s("span",[e._v(e._s(e.$attrs.type))])]):e._e(),e._v(" "),e._t("header")],2),e._v(" "),e.$slots.longText?s("div",{staticClass:"cont-arrange__rt-main-long-text"},[e._t("longText")],2):e._e(),e._v(" "),s("div",{ref:"contMain",staticClass:"cont-arrange__rt-main-box",style:{height:e.showContStatus?e.mainHeight+30+"px":e.mainHeight>78?"78PX":""}},[e._t("main")],2),e._v(" "),s("div",{directives:[{name:"show",rawName:"v-show",value:e.mainHeight>78,expression:"mainHeight > 78"}],ref:"contControl",staticClass:"cont-block-control",class:e.showBottomStatus?"is-bottom-out":"",on:{click:e.showCont}},[s("p",[s("span",{staticClass:"iconfont icondown-menu",class:e.showContStatus?"show-down":""}),e._v("\n          "+e._s(e.showContStatus?"收起详情":"展开详情")+"\n        ")])]),e._v(" "),e.$slots.footer?s("div",{staticClass:"cont-arrange__rt-main-footer"},[e._t("footer")],2):e._e()])])])},i=[]},tr5V:function(e,t,s){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var a=r(s("QbLZ"));s("lL+3");var i=r(s("Xz3T"));function r(e){return e&&e.__esModule?e:{default:e}}t.default=(0,a.default)({name:"reply-review-view"},i.default)},u8Dz:function(e,t,s){"use strict";Object.defineProperty(t,"__esModule",{value:!0}),t.default={data:function(){return{showContStatus:!1,showBottomStatus:!1,mainHeight:0,windowWidth:0,themeNameLeft:70,themeNameStyle:{left:"70",width:"calc(100% - "}}},props:{},methods:{showCont:function(){this.mainHeight=this.$slots.main[0].elm.offsetHeight,this.showContStatus=!this.showContStatus;var e=this.$slots.main[0].elm.getBoundingClientRect().width;this.$slots.main[0].elm.offsetHeight+this.$slots.main[0].elm.getBoundingClientRect().top>window.innerHeight&&(this.showBottomStatus=!0,this.$refs.contControl.style.width=e+40+"PX"),this.showContStatus||(this.showBottomStatus=!1,this.$refs.contControl.style.width="100%")},handleScroll:function(){this.$refs.contControl&&(this.$refs.contControl.style.width=this.$slots.main[0].elm.getBoundingClientRect().width+40+"PX"),this.$slots.main[0].elm.offsetHeight+this.$slots.main[0].elm.getBoundingClientRect().top<window.innerHeight?this.showBottomStatus=!1:this.showContStatus&&(this.showBottomStatus=!0)},browserSize:function(){if(this.$refs.contControl){var e=this.$slots.main[0].elm.getBoundingClientRect(),t=e.width,s=e.top,a=this.$refs.contControl.style;this.showContStatus?(this.$slots.main[0].elm.offsetHeight+s>window.innerHeight?a.width=t+40+"PX":a.width="100%",this.$refs.contMain.style.height=this.$slots.main[0].elm.offsetHeight+30+"PX"):a.width="100%"}},removeScrollHandler:function(){window.removeEventListener("scroll",this.handleScroll,!0),window.removeEventListener("resize",this.browserSize,!0)},themeStyle:function(){this.themeNameStyle.left="70",this.themeNameStyle.width="calc(100% - ",this.themeNameStyle.left=70+this.$refs.userName.clientWidth+"px",this.themeNameStyle.width=this.themeNameStyle.width+(100+this.$refs.userName.clientWidth)+"px)"}},mounted:function(){this.mainHeight=this.$slots.main[0].elm.offsetHeight,window.addEventListener("scroll",this.handleScroll,!0),window.addEventListener("resize",this.browserSize,!0),this.windowWidth=window.innerWidth,this.themeStyle()},beforeDestroy:function(){this.removeScrollHandler()}}}}]);