(window.webpackJsonp=window.webpackJsonp||[]).push([[53],{"16ZA":function(t,e,s){"use strict";s.r(e);var a=s("XlDs"),r=s.n(a);for(var i in a)["default"].indexOf(i)<0&&function(t){s.d(e,t,(function(){return a[t]}))}(i);e.default=r.a},"6Z5u":function(t,e,s){"use strict";s.r(e);var a=s("l4vH"),r=s("16ZA");for(var i in r)["default"].indexOf(i)<0&&function(t){s.d(e,t,(function(){return r[t]}))}(i);var o=s("KHd+"),n=Object(o.a)(r.default,a.a,a.b,!1,null,null,null);e.default=n.exports},XlDs:function(t,e,s){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=i(s("QbLZ"));s("lpfh");var r=i(s("xbrd"));function i(t){return t&&t.__esModule?t:{default:t}}e.default=(0,a.default)({name:"site-data-rules"},r.default)},l4vH:function(t,e,s){"use strict";s.d(e,"a",(function(){return a})),s.d(e,"b",(function(){return r}));var a=function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticClass:"site-sort-set-box"},[s("div",[s("div",{staticClass:"sort-switch-header"},[s("p",{staticClass:"sort-desc",on:{click:t.jumpDataRules}},[t._v("推荐首页")]),t._v(" "),s("p",{staticClass:"data-rules repeat"},[t._v("数据规则")])]),t._v(" "),s("Card",{staticClass:"sort-switch-radio"},[s("p",{staticClass:"sort-switch-radio_title"},[t._v("阅读数计算方式")]),t._v(" "),s("div",{staticClass:"sort-switch-radio_option"},[s("el-radio",{class:"1"===t.radio?"sort-switch-radio_cont":"",attrs:{label:"1"},model:{value:t.radio,callback:function(e){t.radio=e},expression:"radio"}},[t._v("仅点进帖子详情页增加阅读数")])],1),t._v(" "),s("div",[s("el-radio",{class:"0"===t.radio?"sort-switch-radio_cont":"",attrs:{label:"0"},model:{value:t.radio,callback:function(e){t.radio=e},expression:"radio"}},[t._v("操作首页帖子、进入详情页，增加阅读数")])],1),t._v(" "),s("p",{staticClass:"sort-switch-radio_explain"},[t._v('说明：操作包括点赞、点击"查看更多"、分享、下载附件、点开图片预览、播放视频、播放语音、点击帖子中包含的链接或话题。')])]),t._v(" "),s("el-button",{attrs:{type:"primary",size:"medium"},on:{click:t.ruleSubmission}},[t._v("提交")])],1)])},r=[]},xbrd:function(t,e,s){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var a=i(s("4gYi")),r=i(s("pNQN"));function i(t){return t&&t.__esModule?t:{default:t}}e.default={components:{Card:a.default,CardRow:r.default},data:function(){return{radio:""}},created:function(){this.initializeData()},methods:{initializeData:function(){var t=this;this.appFetch({url:"forum_get_v3",method:"get",data:{}}).then((function(e){if(e.errors)t.$message.error(e.errors[0].code);else{if(0!==e.Code)return void t.$message.error(e.Message);var s=e.Data;t.radio=s.setSite.openViewCount}}))},jumpDataRules:function(){this.$router.push({path:"/admin/site-sort-set"})},ruleSubmission:function(){var t=this;this.appFetch({url:"bopen_view_count_post",method:"post",data:{openViewCount:Number(this.radio)}}).then((function(e){if(e.errors)t.$message.error(e.errors[0].code);else{if(0!==e.Code)return void t.$message.error(e.Message);t.$message({message:"提交成功",type:"success"})}}))}}}}}]);