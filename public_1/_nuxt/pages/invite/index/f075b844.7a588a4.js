(window.webpackJsonp=window.webpackJsonp||[]).push([[31],{1031:function(t,e,n){"use strict";var r=n(919);n.n(r).a},1032:function(t,e,n){"use strict";var r=n(920);n.n(r).a},1033:function(t,e,n){"use strict";var r=n(921);n.n(r).a},1165:function(t,e,n){"use strict";n.r(e);n(12);var r=n(751),o=n.n(r),time=n(758),c={name:"Invited",filters:{formatDate:function(t){return Object(time.b)(t)}},mixins:[o.a],props:{groupMap:{type:Object,default:function(){return{}}}},data:function(){return{loading:!1,selectedStatus:"",handleValue:"",pageNum:1,pageSize:10,searchText:"",sort:"-created_at",inviteTotal:0,totalMoney:0,total:0,inviteList:[]}},computed:{userId:function(){return this.$store.state.user.info.id},forums:function(){return this.$store.state.site.info.attributes||{}}},watch:{userId:function(t){t&&0===this.totalMoney&&this.getIncome()}},mounted:function(){this.getInvite(),this.getIncome(),this.getInviteList()},methods:{getInvite:function(){var t=this;this.$store.dispatch("jv/get",["invite/users",{params:{"page[number]":1,"page[limit]":1}}]).then((function(e){e&&e._jv&&e._jv.json&&e._jv.json.meta&&(t.inviteTotal=e._jv.json.meta.total)}))},getIncome:function(){var t=this;if(this.userId){var e={"filter[user]":this.userId,"filter[change_type]":"33, 62, 34","page[number]":1,"page[limit]":1};this.$store.dispatch("jv/get",["wallet/log",{params:e}]).then((function(e){e&&e._jv&&e._jv.json&&e._jv.json.meta&&(t.totalMoney=e._jv.json.meta.sumChangeAvailableAmount)}))}},getInviteList:function(){var t=this;this.loading=!0;var e={"page[number]":this.pageNum,"page[limit]":this.pageSize,"filter[username]":this.searchText,sort:this.sort};this.$store.dispatch("jv/get",["invite/users",{params:e}]).then((function(e){e&&(t.inviteList=e,e._jv&&e._jv.json&&e._jv.json.meta&&(t.total=e._jv.json.meta.total))}),(function(e){t.handleError(e)})).finally((function(){t.loading=!1}))},createUserInvite:function(){var t=this;if(!(this.forums&&this.forums.other&&this.forums.other.can_invite_user_scale))return this.$message.error(this.$t("core.permission_denied"));this.$store.dispatch("jv/get",{_jv:{type:"userInviteCode"}}).then((function(e){e&&e._jv&&t.copyLink(e._jv.code)}),(function(e){t.handleError(e)})).finally((function(){t.loading=!1}))},viewDetail:function(t){this.$emit("view-detail",t)},copyLink:function(code){var t=document.createElement("input");t.value="".concat(window.location.protocol,"//").concat(window.location.host,"/site/partner-invite?code=").concat(code),t.id="copyInput",document.body.appendChild(t),t.select(),document.execCommand("Copy"),this.$message.success(this.$t("discuzq.msgBox.copySuccess")),setTimeout((function(){t.remove()}),100)},sortChange:function(t){var e=t.order;this.sort="descending"===e?"-created_at":"ascending"===e?"created_at":"",this.getInviteList()},onClickSearch:function(){this.pageNum=1,this.getInviteList()},handleSizeChange:function(t){this.pageNum=1,this.pageSize=t,this.getInviteList()},handleCurrentChange:function(t){this.pageNum=t,this.getInviteList()}}},l=(n(1032),n(11)),component=Object(l.a)(c,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"user-manage"},[n("div",{staticClass:"filter-cont"},[n("div",{staticClass:"content"},[t.loading?t._e():[t._v("\n        "+t._s(t.$t("invite.inviteTotal"))+"\n        "),n("span",{staticClass:"bold"},[t._v(t._s(t.inviteTotal))]),t._v("\n        "+t._s(t.$t("invite.people"))+"\n        "+t._s(t.$t("invite.allIncome"))+"\n        "),n("span",{staticClass:"bold"},[t._v(t._s(t.$t("post.yuanItem")+t.totalMoney))])]],2),t._v(" "),n("el-input",{staticClass:"search",attrs:{placeholder:t.$t("invite.searchPlaceholder"),size:"medium"},on:{input:t.onClickSearch},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.onClickSearch(e)}},model:{value:t.searchText,callback:function(e){t.searchText=e},expression:"searchText"}},[n("i",{staticClass:"el-icon-search el-input__icon",attrs:{slot:"suffix"},slot:"suffix"})]),t._v(" "),n("el-button",{staticClass:"create-url",attrs:{type:"primary",size:"medium"},on:{click:t.createUserInvite}},[t._v(t._s(t.$t("manage.generateInvitationUrl")))])],1),t._v(" "),n("div",{staticClass:"main"},[n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],ref:"multipleTable",attrs:{data:t.inviteList,"default-sort":{prop:"created_at",order:"descending"}},on:{"sort-change":t.sortChange}},[n("div",{staticClass:"table-empty",attrs:{slot:"empty"},slot:"empty"},[n("svg-icon",{staticClass:"empty-icon",attrs:{type:"empty"}}),t._v("\n        "+t._s(t.$t("discuzq.list.noData"))+"\n      ")],1),t._v(" "),n("el-table-column",{attrs:{label:t.$t("invite.inviteUserName")},scopedSlots:t._u([{key:"default",fn:function(e){return[e.row.user?n("div",{staticClass:"flex"},[n("avatar",{attrs:{user:{id:e.row.user.id,username:e.row.user.username,avatarUrl:e.row.user.avatarUrl},size:30,round:!0}}),t._v(" "),n("nuxt-link",{staticClass:"user-name",attrs:{to:"/user/"+e.row.user.id}},[t._v(t._s(e.row.user.username))])],1):t._e()]}}])}),t._v(" "),n("el-table-column",{attrs:{prop:"created_at",label:t.$t("invite.createdAt"),sortable:"custom"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n          "+t._s(t._f("formatDate")(e.row.created_at))+"\n        ")]}}])}),t._v(" "),n("el-table-column",{attrs:{label:t.$t("manage.operate"),width:"100"},scopedSlots:t._u([{key:"default",fn:function(e){return n("div",{staticClass:"last-table"},[n("el-button",{staticClass:"btn",attrs:{type:"text"},on:{click:function(n){return t.viewDetail(e.row)}}},[t._v(t._s(t.$t("invite.viewDetail")))])],1)}}])})],1)],1),t._v(" "),n("div",{staticClass:"pagination"},[n("el-pagination",{attrs:{background:"","hide-on-single-page":"","pager-count":5,"current-page":t.pageNum,"page-sizes":[10,20,50,100],"page-size":t.pageSize,layout:"total, sizes, prev, pager, next, jumper",total:t.total},on:{"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}})],1)])}),[],!1,null,"ac44cac4",null);e.default=component.exports;installComponents(component,{SvgIcon:n(60).default,Avatar:n(270).default})},1166:function(t,e,n){"use strict";n.r(e);n(12);var r=n(751),o=n.n(r),time=n(758),c={name:"Income",filters:{formatDate:function(t){return Object(time.b)(t)}},mixins:[o.a],props:{groupMap:{type:Object,default:function(){return{}}}},data:function(){return{loading:!1,pageNum:1,pageSize:10,searchText:"",sort:"-created_at",total:0,incomeList:[],totalMoney:0}},computed:{userId:function(){return this.$store.state.user.info.id},forums:function(){return this.$store.state.site.info.attributes||{}}},watch:{userId:function(t){t&&0===this.incomeList.length&&this.getIncomeList()}},mounted:function(){this.getIncomeList()},methods:{getIncomeList:function(){var t=this;if(this.userId){this.loading=!0;var e={include:"sourceUser","filter[user]":this.userId,"filter[change_type]":"33, 62, 34","page[number]":this.pageNum,"page[limit]":this.pageSize,"filter[source_username]":this.searchText,sort:this.sort};this.$store.dispatch("jv/get",["wallet/log",{params:e}]).then((function(e){e&&e._jv&&e._jv.json&&e._jv.json.meta&&(t.totalMoney=e._jv.json.meta.sumChangeAvailableAmount,t.total=e._jv.json.meta.total),t.incomeList=e}),(function(e){t.handleError(e)})).finally((function(){t.loading=!1}))}},createUserInvite:function(){var t=this;if(!(this.forums&&this.forums.other&&this.forums.other.can_invite_user_scale))return this.$message.error(this.$t("core.permission_denied"));this.$store.dispatch("jv/get",{_jv:{type:"userInviteCode"}}).then((function(e){e&&e._jv&&t.copyLink(e._jv.code)}),(function(e){t.handleError(e)})).finally((function(){t.loading=!1}))},copyLink:function(code){var t=document.createElement("input");t.value="".concat(window.location.protocol,"//").concat(window.location.host,"/site/partner-invite?code=").concat(code),t.id="copyInput",document.body.appendChild(t),t.select(),document.execCommand("Copy"),this.$message.success(this.$t("discuzq.msgBox.copySuccess")),setTimeout((function(){t.remove()}),100)},sortChange:function(t){var e=t.order;this.sort="descending"===e?"-created_at":"ascending"===e?"created_at":"",this.getIncomeList()},onClickSearch:function(){this.pageNum=1,this.getIncomeList()},handleSizeChange:function(t){this.pageNum=1,this.pageSize=t,this.getIncomeList()},handleCurrentChange:function(t){this.pageNum=t,this.getIncomeList()}}},l=(n(1033),n(11)),component=Object(l.a)(c,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"user-manage"},[n("div",{staticClass:"filter-cont"},[n("div",{staticClass:"content"},[t.loading?t._e():[t._v("\n        "+t._s(t.$t("invite.incomeTotal"))+"\n        "),n("span",{staticClass:"bold"},[t._v(t._s(t.$t("post.yuanItem")+t.totalMoney))])]],2),t._v(" "),n("el-input",{staticClass:"search",attrs:{placeholder:t.$t("invite.searchPlaceholder"),size:"medium"},on:{input:t.onClickSearch},nativeOn:{keyup:function(e){return!e.type.indexOf("key")&&t._k(e.keyCode,"enter",13,e.key,"Enter")?null:t.onClickSearch(e)}},model:{value:t.searchText,callback:function(e){t.searchText=e},expression:"searchText"}},[n("i",{staticClass:"el-icon-search el-input__icon",attrs:{slot:"suffix"},slot:"suffix"})]),t._v(" "),n("el-button",{staticClass:"create-url",attrs:{type:"primary",size:"medium"},on:{click:t.createUserInvite}},[t._v(t._s(t.$t("manage.generateInvitationUrl")))])],1),t._v(" "),n("div",{staticClass:"main"},[n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],ref:"multipleTable",attrs:{data:t.incomeList,"default-sort":{prop:"created_at",order:"descending"}},on:{"sort-change":t.sortChange}},[n("div",{staticClass:"table-empty",attrs:{slot:"empty"},slot:"empty"},[n("svg-icon",{staticClass:"empty-icon",attrs:{type:"empty"}}),t._v("\n        "+t._s(t.$t("discuzq.list.noData"))+"\n      ")],1),t._v(" "),n("el-table-column",{attrs:{label:t.$t("invite.inviteUserName")},scopedSlots:t._u([{key:"default",fn:function(e){return[e.row.sourceUser&&e.row.sourceUser.username?n("div",{staticClass:"flex"},[n("avatar",{attrs:{user:{id:e.row.sourceUser.id,username:e.row.sourceUser.username,avatarUrl:e.row.sourceUser.avatarUrl},size:30,round:!0}}),t._v(" "),n("nuxt-link",{staticClass:"user-name",attrs:{to:"/user/"+e.row.sourceUser.id}},[t._v(t._s(e.row.sourceUser.username))])],1):[t._v(t._s(t.$t("core.userDeleted")))]]}}])}),t._v(" "),n("el-table-column",{attrs:{prop:"created_at",label:t.$t("invite.incomeAt"),sortable:"custom"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n          "+t._s(t._f("formatDate")(e.row.created_at))+"\n        ")]}}])}),t._v(" "),n("el-table-column",{attrs:{label:t.$t("invite.income"),width:"100"},scopedSlots:t._u([{key:"default",fn:function(e){return n("div",{staticClass:"last-table"},[t._v("\n          "+t._s(t.$t("post.yuanItem")+e.row.change_available_amount)+"\n        ")])}}])})],1)],1),t._v(" "),n("div",{staticClass:"pagination"},[n("el-pagination",{attrs:{background:"","hide-on-single-page":"","pager-count":5,"current-page":t.pageNum,"page-sizes":[10,20,50,100],"page-size":t.pageSize,layout:"total, sizes, prev, pager, next, jumper",total:t.total},on:{"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}})],1)])}),[],!1,null,"99d9c3dc",null);e.default=component.exports;installComponents(component,{SvgIcon:n(60).default,Avatar:n(270).default})},1194:function(t,e,n){"use strict";n.r(e);n(12);var time=n(758),head=n(752),r={layout:"center_layout",name:"InviteExtension",filters:{formatDate:function(t){return Object(time.b)(t)}},mixins:[n.n(head).a],data:function(){return{title:this.$t("invite.invite"),loading:!1,activeName:"invited",isShowDetail:!1,detail:{},pageNum:1,pageSize:10,total:0,detailTotalMoney:0,detailUserId:"",incomeDetailList:[]}},computed:{userId:function(){return this.$store.state.user.info.id}},mounted:function(){},methods:{viewDetail:function(t){this.detail=t,this.isShowDetail=!0,this.detailUserId=t.user_id,this.getIncomeDetailList()},getIncomeDetailList:function(){var t=this;if(this.userId){this.loading=!0;var e={include:"sourceUser","filter[user]":this.userId,"filter[change_type]":"33, 62, 34","page[number]":this.pageNum,"page[limit]":this.pageSize,sort:this.sort,"filter[source_user_id]":this.detailUserId};this.$store.dispatch("jv/get",["wallet/log",{params:e}]).then((function(e){e&&e._jv&&e._jv.json&&e._jv.json.meta&&(t.detailTotalMoney=e._jv.json.meta.sumChangeAvailableAmount,t.total=e._jv.json.meta.total),t.incomeDetailList=e}),(function(e){t.handleError(e)})).finally((function(){t.loading=!1}))}},sortChange:function(t){var e=t.order;this.sort="descending"===e?"-created_at":"ascending"===e?"created_at":"",this.getIncomeDetailList()},handleSizeChange:function(t){this.pageNum=1,this.pageSize=t,this.getIncomeDetailList()},handleCurrentChange:function(t){this.pageNum=t,this.getIncomeDetailList()}}},o=(n(1031),n(11)),component=Object(o.a)(r,(function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"invite-container"},[t.isShowDetail?[t.detail&&t.detail.user?n("header",{staticClass:"detail-header"},[n("div",{staticClass:"title"},[t._v(t._s(t.$t("invite.userDetail")))]),t._v(" "),n("div",{staticClass:"user"},[n("avatar",{attrs:{user:{id:t.detail.user.id,username:t.detail.user.username,avatarUrl:t.detail.user.avatarUrl},size:50}}),t._v(" "),n("div",{staticClass:"user-info"},[n("nuxt-link",{staticClass:"user-name",attrs:{to:"/user/"+t.detail.user.id}},[t._v("\n            "+t._s(t.detail.user.username)+"\n          ")]),t._v(" "),n("div",{staticClass:"create-at"},[t._v("\n            "+t._s(t.$t("invite.createdAt")+t.$t("discuzq.symbol.colon"))+" "+t._s(t._f("formatDate")(t.detail.created_at))+"\n          ")])],1),t._v(" "),n("div",{staticClass:"total-money"},[n("div",{staticClass:"label"},[t._v(t._s(t.$t("invite.allIncome")))]),t._v(" "),n("div",{staticClass:"value"},[t._v(t._s(t.$t("post.yuanItem")+t.detailTotalMoney))])])],1)]):t._e(),t._v(" "),n("main",[n("div",{staticClass:"main"},[n("el-table",{directives:[{name:"loading",rawName:"v-loading",value:t.loading,expression:"loading"}],attrs:{data:t.incomeDetailList,"default-sort":{prop:"created_at",order:"descending"}},on:{"sort-change":t.sortChange}},[n("div",{staticClass:"table-empty",attrs:{slot:"empty"},slot:"empty"},[n("svg-icon",{staticClass:"empty-icon",attrs:{type:"empty"}}),t._v("\n            "+t._s(t.$t("discuzq.list.noData"))+"\n          ")],1),t._v(" "),n("el-table-column",{attrs:{label:t.$t("invite.inviteUserName")},scopedSlots:t._u([{key:"default",fn:function(e){return[e.row.sourceUser?n("div",{staticClass:"flex"},[n("avatar",{attrs:{user:{id:e.row.sourceUser.id,username:e.row.sourceUser.username,avatarUrl:e.row.sourceUser.avatarUrl},size:30,round:!0}}),t._v(" "),n("nuxt-link",{staticClass:"user-name",attrs:{to:"/user/"+e.row.sourceUser.id}},[t._v("\n                  "+t._s(e.row.sourceUser.username))])],1):t._e()]}}],null,!1,2955613281)}),t._v(" "),n("el-table-column",{attrs:{prop:"created_at",label:t.$t("invite.incomeAt"),sortable:"custom"},scopedSlots:t._u([{key:"default",fn:function(e){return[t._v("\n              "+t._s(t._f("formatDate")(e.row.created_at))+"\n            ")]}}],null,!1,419549433)}),t._v(" "),n("el-table-column",{attrs:{label:t.$t("invite.income"),width:"100"},scopedSlots:t._u([{key:"default",fn:function(e){return n("div",{staticClass:"last-table"},[t._v("\n              "+t._s(t.$t("post.yuanItem")+e.row.change_available_amount)+"\n            ")])}}],null,!1,3286361409)})],1)],1),t._v(" "),n("div",{staticClass:"pagination"},[n("el-pagination",{attrs:{background:"","hide-on-single-page":"","pager-count":5,"current-page":t.pageNum,"page-sizes":[10,20,50,100],"page-size":t.pageSize,layout:"total, sizes, prev, pager, next, jumper",total:t.total},on:{"size-change":t.handleSizeChange,"current-change":t.handleCurrentChange}})],1)])]:n("div",{staticClass:"invite-cont"},[n("main",[n("el-tabs",{model:{value:t.activeName,callback:function(e){t.activeName=e},expression:"activeName"}},[n("el-tab-pane",{attrs:{label:t.$t("invite.invitedUser"),name:"invited"}},[n("invited",{on:{"view-detail":t.viewDetail}})],1),t._v(" "),n("el-tab-pane",{attrs:{label:t.$t("invite.incomeDetail"),name:"income"}},[n("income")],1)],1)],1)])],2)}),[],!1,null,"10628d92",null);e.default=component.exports;installComponents(component,{Avatar:n(270).default,Header:n(86).default,SvgIcon:n(60).default,Invited:n(1165).default,Income:n(1166).default})},751:function(t,e,n){n(32);var r=n(733);n(51),t.exports={data:function(){var t=this;return{errorCodeHandler:{default:{model_not_found:function(){return t.$router.replace("/error")},not_authenticated:function(){return t.$router.push("/user/login")}},thread:{permission_denied:function(){return t.$router.replace("/error")}}}}},methods:{handleError:function(t){var e=arguments,n=this;return r(regeneratorRuntime.mark((function r(){var o,c,l,d,v,m;return regeneratorRuntime.wrap((function(r){for(;;)switch(r.prev=r.next){case 0:if(o=e.length>1&&void 0!==e[1]?e[1]:"",c=t.response.data.errors,!(Array.isArray(c)&&c.length>0)){r.next=17;break}if(l=c[0].code,d=c[0].detail&&c[0].detail.length>0?c[0].detail[0]:c[0].code,v=c[0].detail&&c[0].detail.length>0?c[0].detail[0]:n.$t("core.".concat(d)),"site_closed"!==c[0].code){r.next=10;break}return r.next=9,n.siteClose(c);case 9:return r.abrupt("return",r.sent);case 10:if("need_ext_fields"!==c[0].code){r.next=14;break}return m=n.$store.getters["session/get"]("userId"),n.$router.push("/user/supple-mentary?id=".concat(m)),r.abrupt("return");case 14:"Permission Denied"===l?n.$message.error(n.$t("core.permission_denied2")):n.$message.error(v),n.errorCodeHandler.default[l]&&n.errorCodeHandler.default[l](),o&&n.errorCodeHandler[o][l]&&n.errorCodeHandler[o][l]();case 17:case"end":return r.stop()}}),r)})))()},siteClose:function(t){var e=this;return r(regeneratorRuntime.mark((function n(){return regeneratorRuntime.wrap((function(n){for(;;)switch(n.prev=n.next){case 0:return n.prev=0,n.next=3,e.$store.dispatch("forum/setError",{code:t[0].code,detail:t[0].detail&&t[0].detail.length>0&&t[0].detail[0]});case 3:return n.next=5,e.$router.push("/site/close");case 5:n.next=9;break;case 7:n.prev=7,n.t0=n.catch(0);case 9:case"end":return n.stop()}}),n,null,[[0,7]])})))()}}}},752:function(t,e){t.exports={data:function(){return{title:"‎"}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},head:function(){return{title:"‎"!==this.title&&this.forums&&this.forums.set_site&&this.forums.set_site.site_name?"".concat(this.title," - ").concat(this.forums.set_site.site_name):this.title}}}},758:function(t,e,n){"use strict";n.d(e,"b",(function(){return r})),n.d(e,"c",(function(){return o})),n.d(e,"a",(function(){return c}));n(180),n(51);var r=function(time){var t=((window.currentTime||new Date)-new Date(time))/1e3;return 0===parseInt(t)?"刚刚发布":parseInt(t)<60?"".concat(parseInt(t),"秒前"):parseInt(t/60)<60?"".concat(parseInt(t/60),"分钟前"):parseInt(t/60/60)<16?"".concat(parseInt(t/60/60),"小时前"):time.replace(/T/," ").replace(/Z/,"").substring(0,16)},o=function(t){var e=t-Math.round(new Date/1e3);return parseInt(e/86400,0)},c=function(t){var e=Math.round(new Date(t)/1e3),n=Math.round(new Date/1e3)-e,r=parseInt(n/86400,0);if(r>365){var o=parseInt(n/86400/365,0);return"".concat(o,"年")}return"".concat(r,"天")}},919:function(t,e,n){},920:function(t,e,n){},921:function(t,e,n){}}]);