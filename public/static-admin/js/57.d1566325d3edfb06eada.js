(window.webpackJsonp=window.webpackJsonp||[]).push([[57],{"1PNB":function(t,e,a){"use strict";a.r(e);var n=a("ut8Y"),s=a.n(n);for(var c in n)["default"].indexOf(c)<0&&function(t){a.d(e,t,(function(){return n[t]}))}(c);e.default=s.a},KVfl:function(t,e,a){"use strict";a.r(e);var n=a("zYKR"),s=a("1PNB");for(var c in s)["default"].indexOf(c)<0&&function(t){a.d(e,t,(function(){return s[t]}))}(c);var o=a("KHd+"),i=Object(o.a)(s.default,n.a,n.b,!1,null,null,null);e.default=i.exports},"WUP+":function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=c(a("4gYi")),s=c(a("pNQN"));function c(t){return t&&t.__esModule?t:{default:t}}e.default={data:function(){return{tableData:[{name:"云API",type:"qcloud_close",description:'配置云API的密钥后，才可使用腾讯云的各项服务和能力，<a href="https://discuz.chat/manual-admin/2.html#_2-7-1-%E4%BA%91api" target="_blank">查看文档</a>',status:"",icon:"iconAPI",setFlag:!0},{name:"图片内容安全",type:"qcloud_cms_image",description:'请先配置云API，开通腾讯云图片内容安全服务，并确保有对应套餐包，<a href="https://discuz.chat/manual-admin/2.html#_2-7-2-%E5%9B%BE%E7%89%87%E5%86%85%E5%AE%B9%E5%AE%89%E5%85%A8" target="_blank">查看文档</a>',status:"",icon:"icontupian",setFlag:!1},{name:"文本内容安全",type:"qcloud_cms_text",description:'请先配置云API，开通腾讯云文本内容安全服务，并确保有对应套餐包，<a href="https://discuz.chat/manual-admin/2.html#_2-7-3-%E6%96%87%E6%9C%AC%E5%86%85%E5%AE%B9%E5%AE%89%E5%85%A8" target="_blank">查看文档</a>',status:"",icon:"iconwenben",setFlag:!1},{name:"短信",type:"qcloud_sms",description:'请先配置云API，开通腾讯云短信服务，并确保腾讯云账户的短信额度充足，<a href="https://discuz.chat/manual-admin/2.html#_2-7-4-%E7%9F%AD%E4%BF%A1" target="_blank">查看文档</a>',status:"",icon:"iconduanxin",setFlag:!0},{name:"实名认证",type:"qcloud_faceid",description:'请先配置云API，开通腾讯云，并确保有对应资源包，<a href="https://discuz.chat/manual-admin/2.html#_2-7-5-%E5%AE%9E%E5%90%8D%E8%AE%A4%E8%AF%81" target="_blank">查看文档</a>',status:"",icon:"iconshimingrenzheng",setFlag:!1},{name:"对象存储",type:"qcloud_cos",description:'请先配置云API，开通腾讯云的对象存储服务，并确保有对应资源包，<a href="https://discuz.chat/manual-admin/2.html#_2-7-6-%E5%AF%B9%E8%B1%A1%E5%AD%98%E5%82%A8" target="_blank">查看文档</a>',status:"",icon:"iconduixiangcunchu",setFlag:!0},{name:"云点播",type:"qcloud_vod",description:'请先配置云API，开通腾讯云的云点播VOD服务，并确保有对应资源包，<a href="https://discuz.chat/manual-admin/2.html#_2-7-7-%E8%A7%86%E9%A2%91" target="_blank">查看文档</a>',status:"",icon:"iconshipin",setFlag:!0},{name:"验证码",type:"qcloud_captcha",description:'请先配置云API，开通腾讯云的验证码服务，并确保有对应的资源包，<a href="https://discuz.chat/manual-admin/2.html#_2-7-8-%E9%AA%8C%E8%AF%81%E7%A0%81" target="_blank">查看文档</a>',status:"",icon:"iconyanzhengma",setFlag:!0}]}},created:function(){this.tencentCloudStatus()},methods:{configClick:function(t){switch(t){case"qcloud_close":this.$router.push({path:"/admin/tencent-cloud-config/cloud",query:{type:t}});break;case"qcloud_sms":this.$router.push({path:"/admin/tencent-cloud-config/sms",query:{type:t}});break;case"qcloud_cos":this.$router.push({path:"/admin/tencent-cloud-config/cos",query:{type:t}});break;case"qcloud_vod":this.$router.push({path:"/admin/tencent-cloud-config/vod",query:{type:t}});break;case"qcloud_captcha":this.$router.push({path:"/admin/tencent-cloud-config/code",query:{type:t}});default:this.loginStatus="default"}},tencentCloudStatus:function(){var t=this;this.appFetch({url:"forum_get_v3",method:"get",data:{}}).then((function(e){if(e.errors)t.$message.error(e.errors[0].code);else{if(0!==e.Code)return void t.$message.error(e.Message);var a=e.Data;a.qcloud.qcloudClose?t.tableData[0].status=!0:t.tableData[0].status=!1,a.qcloud.qcloudCmsImage?t.tableData[1].status=!0:t.tableData[1].status=!1,a.qcloud.qcloudCmsText?t.tableData[2].status=!0:t.tableData[2].status=!1,a.qcloud.qcloudSms?t.tableData[3].status=!0:t.tableData[3].status=!1,a.qcloud.qcloudFaceid?t.tableData[4].status=!0:t.tableData[4].status=!1,a.qcloud.qcloudCos?t.tableData[5].status=!0:t.tableData[5].status=!1,a.qcloud.qcloudVod?t.tableData[6].status=!0:t.tableData[6].status=!1,a.qcloud.qcloudCaptcha?t.tableData[7].status=!0:t.tableData[7].status=!1}}))},loginSetting:function(t,e,a){var n=this;"qcloud_close"==e?this.changeSettings("qcloud_close",a):"qcloud_cms_image"==e?this.changeSettings("qcloud_cms_image",a):"qcloud_cms_text"==e?this.changeSettings("qcloud_cms_text",a):"qcloud_sms"==e?0==a?this.$confirm("若您在用户角色中设置了发布内容需先绑定手机，关闭短信服务将同时清空该设置。若当前注册模式为手机号模式，将更改为用户名模式。",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then((function(){n.changeSettings("qcloud_sms",a)})):this.changeSettings("qcloud_sms",a):"qcloud_faceid"==e?0==a?this.$confirm("若您在用户角色中设置了发布内容需先实名认证，关闭实名认证服务将同时清空该设置",{confirmButtonText:"确定",cancelButtonText:"取消",type:"warning"}).then((function(){n.changeSettings("qcloud_faceid",a)})):this.changeSettings("qcloud_faceid",a):"qcloud_cos"==e?this.changeSettings("qcloud_cos",a):"qcloud_vod"==e?this.changeSettings("qcloud_vod",a):"qcloud_captcha"==e&&this.changeSettings("qcloud_captcha",a)},changeSettings:function(t,e){var a=this;this.appFetch({url:"settings_post_v3",method:"post",data:{data:[{key:t,value:e,tag:"qcloud"}]}}).then((function(t){if(t.errors)a.$message.error(t.errors[0].code);else{if(0!==t.Code)return void a.$message.error(t.Message);a.$message({message:"修改成功",type:"success"}),a.tencentCloudStatus()}})).catch((function(t){}))}},components:{Card:n.default,CardRow:s.default}}},ut8Y:function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var n=c(a("QbLZ")),s=c(a("WUP+"));function c(t){return t&&t.__esModule?t:{default:t}}a("lpfh"),e.default=(0,n.default)({name:"tencent-cloud-set-view"},s.default)},zYKR:function(t,e,a){"use strict";a.d(e,"a",(function(){return n})),a.d(e,"b",(function(){return s}));var n=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",[a("div",{staticStyle:{"padding-top":"15PX"}},[a("el-table",{staticStyle:{width:"100%"},attrs:{data:t.tableData}},[a("el-table-column",{attrs:{prop:"date",label:"腾讯云设置"},scopedSlots:t._u([{key:"default",fn:function(e){return[a("div",{staticClass:"pay-set-type-box"},[a("i",{staticClass:"iconfont table-icon",class:e.row.icon}),t._v(" "),a("div",{staticClass:"table-con-box"},[a("p",[t._v(t._s(e.row.name))]),t._v(" "),a("p",[a("span",{domProps:{innerHTML:t._s(e.row.description)}})])])])]}}])}),t._v(" "),a("el-table-column",{attrs:{prop:"name",label:"状态",width:"100",align:"center"},scopedSlots:t._u([{key:"default",fn:function(t){return[t.row.status?a("span",{staticClass:"iconfont iconicon_select"}):a("span",{staticClass:"iconfont iconicon_"})]}}])}),t._v(" "),a("el-table-column",{attrs:{prop:"address",label:"操作",width:"180"},scopedSlots:t._u([{key:"default",fn:function(e){return[e.row.setFlag&&"img"!==e.row.type&&"text"!==e.row.type&&"name"!==e.row.type?a("el-button",{attrs:{size:"mini"},on:{click:function(a){return t.configClick(e.row.type)}}},[t._v("配置")]):t._e(),t._v(" "),e.row.status?a("el-button",{attrs:{size:"mini"},nativeOn:{click:function(a){return a.preventDefault(),t.loginSetting(e.$index,e.row.type,"0")}}},[t._v("关闭")]):a("el-button",{attrs:{size:"mini"},nativeOn:{click:function(a){return a.preventDefault(),t.loginSetting(e.$index,e.row.type,"1")}}},[t._v("开启")])]}}])})],1)],1)])},s=[]}}]);