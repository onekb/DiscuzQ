(window.webpackJsonp=window.webpackJsonp||[]).push([[36],{1049:function(e,t){e.exports={data:function(){return{wehcatTimer:null,wechatBind:{}}},destroyed:function(){window.clearInterval(this.wehcatTimer)},methods:{createQRcode:function(){var e=this;this.$store.dispatch("jv/get","/oauth/wechat/pc/qrcode?type=pc_relation").then((function(t){if(t){e.wechatBind=t;var o=e;e.wehcatTimer=setInterval(o.getWechatStatus,3e3)}}),(function(t){return e.handleError(t)}))},getWechatStatus:function(){var e=this;this.wechatBind&&!this.wechatBind.session_token||this.$store.dispatch("jv/get","/oauth/wechat/pc/bind/".concat(this.wechatBind.session_token)).then((function(t){t._jv.json.bind&&(clearInterval(e.wehcatTimer),e.userinfo(),e.isWechatModify=!1,e.$message.success(e.$t("user.BindSuccess")))}),(function(t){var o=t.response.data.errors;if(Array.isArray(o)&&o.length>0){var n=o[0].detail&&o[0].detail.length>0?o[0].detail[0]:o[0].code,r=o[0].detail&&o[0].detail.length>0?o[0].detail[0]:e.$t("core.".concat(n));if("pc_qrcode_scanning_code"===n)return;clearInterval(e.wehcatTimer),e.$message.error(r),e.createQRcode()}}))}}}},1050:function(e,t,o){"use strict";var n=o(889);o.n(n).a},1051:function(e,t,o){"use strict";var n=o(890);o.n(n).a},1052:function(e,t,o){"use strict";var n=o(891);o.n(n).a},1117:function(e,t,o){"use strict";o.r(t);o(27);var n=o(33),r=o(732),l={name:"ShowAvatar",mixins:[o.n(r).a],props:{userId:{type:String,default:""}},data:function(){return{host:"",header:"",dialogVisible:!0,cropImageFormVisible:!1,previews:{},previewCycle:{},option:{img:"",size:1,full:!1,outputType:"png",canMove:!0,fixedBox:!0,original:!0,canMoveBox:!0,autoCrop:!0,autoCropWidth:150,autoCropHeight:150,centerBox:!1,high:!0,max:99999},show:!0,fixed:!0,fixedNumber:[1,1],downImg:"",loading:!1}},mounted:function(){this.header={authorization:"Bearer ".concat(localStorage.getItem("access_token"))}},methods:{handleClose:function(e){var t=this;this.$confirm("确认关闭？").then((function(){e(),t.$emit("change",t.dialogVisible)})).catch((function(){}))},handleClose2:function(){this.dialogVisible=!1,this.$emit("change",this.dialogVisible)},uploadPhoto:function(){this.$refs.photoFile.click()},selectChange:function(e){var t=e.raw;this.fileChange(t)},fileChange:function(e){var t=e;if(/.(png|jpg|jpeg|JPG|JPEG)$/.test(t.name)){var o=new FileReader;o.readAsDataURL(t);var n=this;o.onload=function(){var e=this.result;n.$nextTick((function(){n.pageImage=e,n.option.img=e,n.cropImageFormVisible=!0}))}}else this.$message({message:"请选择符合格式要求的图片",type:"warning"}),this.$refs.photoFile.value=""},realTime:function(data){this.previews=data,this.previewCycle={width:"".concat(this.previews.w,"px"),height:"".concat(this.previews.h,"px"),overflow:"hidden",margin:"0",zoom:.66666666666}},down:function(){var e=this;this.$refs.cropper.getCropBlob((function(data){e.downImg=data,e.$refs.photoFile.submit(),e.loading=!0}))},httpRequest:function(e){var t=this,o=e.action,data=e.data,r=e.filename,l=new FormData;for(var c in data)l.append(c,data[c]);l.append(r,this.downImg,data.fileName),Object(n.a)({url:o,method:"post",data:l,timeout:2e8}).then((function(e){e&&(t.loading=!1,t.$message.success("图片上传成功"),t.dialogVisible=!1,t.$emit("change",t.dialogVisible))}),(function(e){t.loading=!1,t.handleError(e)}))}}},c=(o(1051),o(12)),component=Object(c.a)(l,(function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],staticClass:"showAvatar"},[o("el-dialog",{attrs:{title:"头像",visible:e.dialogVisible,width:"620px","before-close":e.handleClose},on:{"update:visible":function(t){e.dialogVisible=t}}},[o("div",{staticClass:"container"},[o("div",{directives:[{name:"show",rawName:"v-show",value:!e.cropImageFormVisible,expression:"!cropImageFormVisible"}],staticStyle:{top:"40%",display:"inline-block",position:"relative","z-index":"999"}},[o("el-upload",{ref:"photoFile",attrs:{action:e.host+"/users/"+e.userId+"/avatar",headers:e.header,accept:"image/*",data:{type:1,order:1},name:"avatar","show-file-list":!1,"auto-upload":!1,"on-change":e.selectChange,"http-request":e.httpRequest}},[o("el-button",[e._v(e._s(e.$t("profile.showavatar")))])],1),e._v(" "),o("p",{staticClass:"uptext"},[e._v("\n          "+e._s(e.$t("profile.supportupload"))+"\n          "),o("span",[e._v(e._s(e.$t("profile.jpgorpng"))+" ")]),e._v(" "+e._s(e.$t("profile.filemost"))+" "),o("span",[e._v("500kb")])])],1),e._v(" "),o("div",{directives:[{name:"show",rawName:"v-show",value:e.cropImageFormVisible,expression:"cropImageFormVisible"}],staticClass:"imgCrop-content"},[o("div",{staticClass:"cropper-content"},[o("VueCropper",{ref:"cropper",attrs:{img:e.option.img,"output-size":e.option.size,"output-type":e.option.outputType,info:!0,full:e.option.full,fixed:e.fixed,"fixed-number":e.fixedNumber,"can-move":e.option.canMove,"can-move-box":e.option.canMoveBox,"fixed-box":e.option.fixedBox,original:e.option.original,"auto-crop":e.option.autoCrop,"auto-crop-width":e.option.autoCropWidth,"auto-crop-height":e.option.autoCropHeight,"center-box":e.option.centerBox,high:e.option.high,mode:"cover","max-img-size":e.option.max},on:{"real-time":e.realTime}})],1)])]),e._v(" "),o("div",{staticClass:"show-preview",style:{width:e.previews.w+"px",height:e.previews.h+"px",overflow:"hidden",display:"inline-block",position:"absolute","margin-left":"17px","margin-top":"7px"}},[o("div",{staticClass:"preview",style:e.previews.div},[o("img",{style:e.previews.img,attrs:{src:e.previews.url}})])]),e._v(" "),o("div",{staticClass:"show-preview",style:{width:"100px",height:"100px",overflow:"hidden",display:"inline-block",position:"absolute","margin-left":"17px","border-radius":"50%",top:"51%"}},[o("div",{staticClass:"preview",style:e.previewCycle},[o("img",{style:e.previews.img,attrs:{src:e.previews.url}})])]),e._v(" "),o("div",{staticClass:"preview2"},[o("div",{staticClass:"square"},[o("span",{staticClass:"squarep"},[e._v(e._s(e.$t("profile.px1")))])]),e._v(" "),o("div",{staticClass:"circle"},[o("span",{staticClass:"squarep"},[e._v(e._s(e.$t("profile.px2")))])]),e._v(" "),o("div",{staticClass:"pre-button"},[o("el-button",{staticClass:"btnw",attrs:{type:"primary",size:"small"},on:{click:e.down}},[e._v(e._s(e.$t("profile.avataruse")))]),e._v(" "),o("el-button",{staticClass:"btnw",attrs:{size:"small"},on:{click:e.handleClose2}},[e._v(e._s(e.$t("profile.avatarcancel")))])],1)])])],1)}),[],!1,null,"7a200868",null);t.default=component.exports},1118:function(e,t,o){"use strict";o.r(t);var n={name:"VerifyPhone",props:{error:{type:Boolean,default:!1},passwordErrorTip:{type:String,default:""},mobile:{type:String,default:""}},data:function(){return{}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},mounted:function(){this.$emit("sendsms")},methods:{empty:function(){this.$refs.walletinput.deleat()},findpaypwd:function(){this.$emit("close"),this.$emit("findpaypwd")}}},r=(o(1052),o(12)),component=Object(r.a)(n,(function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("message",{attrs:{title:e.$t("modify.authontitle")},on:{close:function(t){return e.$emit("close")}}},[o("div",{staticClass:"container"},[o("div",{staticClass:"block show-amount"},[o("div",{staticClass:"title"},[e._v(e._s("请收入手机 "+e.mobile+" 的验证码"))]),e._v(" "),o("div",{staticClass:"amount"},[o("span",[e._v(e._s(e.$t("modify.editphonewordtip")))])])]),e._v(" "),o("div",{staticClass:"block input-password"},[o("wallet-password-input",{ref:"walletinput",attrs:{error:e.error},on:{password:function(t){return e.$emit("password",t)}}}),e._v(" "),e.error?o("div",{staticClass:"amount"},[e._v(e._s(e.passwordErrorTip?e.passwordErrorTip:e.$t("core.sms_verify_error")))]):e._e()],1)])])}),[],!1,null,"3ecbd1c4",null);t.default=component.exports;installComponents(component,{WalletPasswordInput:o(862).default,Message:o(787).default})},1149:function(e,t,o){"use strict";o.r(t);o(51),o(27);var n=o(5),head=o(733),r=o.n(head),l=o(732),c=o.n(l),d=o(1049),f=o.n(d),h=o(797),m={name:"Profile",layout:"center_layout",mixins:[r.a,c.a,f.a,h],data:function(){return{title:this.$t("profile.myprofile"),userInfo:"",num:140,wordnumber:"",signcontent:"",content:this.$t("modify.sendVerifyCode"),content2:this.$t("modify.sendVerifyCode"),canClick:!0,canClick2:!1,captcha:null,ticket:"",randstr:"",newVerifyCode:"",newPhoneNumber:"",newphon:"",setnum:"",novice:"",oldPassWord:"",newPassWord:"",renewPassword:"",realName:"",idNumber:"",newName:"",isSignModify:!1,isMobileModify:!1,isPassModify:!1,isWechatModify:!1,isRealModify:!1,isNameModify:!1,isMobileVerify:!1,loading:!0,rebind:!1,isShowAvatar:!1,passerror:!1,isChange:!1,phoneError:!1}},computed:{inputVal:{get:function(){return this.signcontent},set:function(e){this.signcontent=e}},forums:function(){return this.$store.state.site.info.attributes||{}},userId:function(){return this.$store.getters["session/get"]("userId")},avataruserInfo:function(){return this.$store.state.user.info.attributes||{}}},watch:{userId:function(e){e&&this.userinfo()}},mounted:function(){this.userId&&this.userinfo(),this.$route.query.phonebind&&(this.isMobileModify=!0)},methods:{countDown:function(e){var t=this;if(this.canClick){this.canClick=!1,this.content=e+this.$t("modify.retransmission");var o=setInterval((function(){e--,t.content=e+t.$t("modify.retransmission"),e<0&&(clearInterval(o),t.content=t.$t("modify.sendVerifyCode"),t.canClick=!0)}),1e3)}},countDown2:function(e){var t=this;if(this.canClick2){this.canClick2=!1,this.content2=e+this.$t("modify.retransmission");var o=window.setInterval((function(){e--,t.content2=e+t.$t("modify.retransmission"),e<0&&(window.clearInterval(o),t.content2=t.$t("modify.sendVerifyCode"),t.canClick2=!0)}),1e3)}},userinfo:function(){var e=this;this.$store.dispatch("jv/get",["users/".concat(this.userId),{params:{include:"groups,wechat"}}]).then((function(t){e.loading=!1,e.userInfo=t,e.signcontent=e.userInfo.signature,e.userInfo.groupsName=e.userInfo.groups?e.userInfo.groups[0].name:"",e.wordnumber=e.signcontent.length}),(function(t){var o=t.response.data.errors;if(o&&Array.isArray(o)&&o.length>0&&o[0]){var n=o[0].detail&&o[0].detail.length>0?o[0].detail[0]:o[0].code;if("Invalid includes [wechat]"===n)e.$message.error(n),localStorage.removeItem("access_token"),localStorage.removeItem("user_id"),window.location.replace("/");else{var r=o[0].detail&&o[0].detail.length>0?o[0].detail[0]:e.$t("core.".concat(n));e.$message.error(r)}}}))},setAvatar:function(){this.isShowAvatar=!0},changeShow:function(e){this.isShowAvatar=e,this.$store.dispatch("user/getUserInfo",this.userId)},fun:function(e){this.wordnumber=e.length},signModify:function(){var e=this;this.isSignModify=!this.isSignModify,this.$nextTick((function(){e.$refs.sign.focus()}))},sigComfirm:function(){var e=this,t={_jv:{type:"users",id:this.userId},signature:this.inputVal};n.status.run((function(){return e.$store.dispatch("jv/patch",t)})).then((function(t){t&&(e.isSignModify=!e.isSignModify,e.$message.success(e.$t("modify.modificationsucc")),e.userinfo())}),(function(t){return e.handleError(t)}))},changeinput:function(){var e=this;this.isChange=!0,""===this.newphon&&(this.isChange=!1),setTimeout((function(){e.newphon=e.newphon.replace(/[^\d]/g,"")}),30),this.newphon.length<11?(this.canClick=!0,this.canClick2=!1):11===this.newphon.length&&(this.canClick=!0,this.canClick2=!0,this.novice=this.newphon.replace(/\s+/g,""))},sendsms:function(){/^1(3|4|5|6|7|8|9)\d{9}$/.test(this.novice)?this.forums.qcloud.qcloud_captcha?this.tcaptcha():this.rebind?this.sendVerifyCode2():this.setphon():this.$message.error("手机号错误")},sendsms2:function(){this.newphon="",this.forums.qcloud.qcloud_captcha?this.tcaptcha():this.sendVerifyCode()},tcaptcha:function(){var e=this;this.captcha=new TencentCaptcha(this.forums.qcloud.qcloud_captcha_app_id,(function(t){0===t.ret&&(e.ticket=t.ticket,e.randstr=t.randstr,e.novice?e.rebind?e.sendVerifyCode2():e.setphon():e.sendVerifyCode())})),this.captcha.show()},setphon:function(){var e=this,t={_jv:{type:"sms/send"},mobile:this.novice,type:"bind",captcha_ticket:this.ticket,captcha_rand_str:this.randstr};n.status.run((function(){return e.$store.dispatch("jv/post",t)})).then((function(t){t.interval&&e.countDown(t.interval),e.ticket="",e.randstr=""}),(function(t){return e.handleError(t)}))},dingphon:function(){this.newphon&&this.bindphon()},bindphon:function(){var e=this,t={_jv:{type:"sms/verify"},mobile:this.newphon,code:this.setnum,type:"bind"};n.status.run((function(){return e.$store.dispatch("jv/post",t)})).then((function(t){t&&(e.isMobileModify=!e.isMobileModify,e.$message.success(e.$t("modify.phontitle")),e.userinfo(),e.$store.dispatch("user/getUserInfo",e.userId),e.newphon="")}),(function(t){return e.handleError(t)}))},mobileModify:function(){this.isMobileModify=!this.isMobileModify},mobileVerify:function(){this.isMobileVerify=!this.isMobileVerify},sendVerifyCode:function(){var e=this;this.canClick=!0;var t={_jv:{type:"sms/send"},type:"verify",captcha_ticket:this.ticket,captcha_rand_str:this.randstr};n.status.run((function(){return e.$store.dispatch("jv/post",t)})).then((function(t){t.interval&&e.countDown(t.interval),e.rebind=!0,e.ticket="",e.randstr=""}),(function(t){return e.handleError(t)}))},sendVerifyCode2:function(){var e=this,t={_jv:{type:"sms/send"},mobile:this.novice,type:"rebind",captcha_ticket:this.ticket,captcha_rand_str:this.randstr};n.status.run((function(){return e.$store.dispatch("jv/post",t)})).then((function(t){t.interval&&e.countDown2(t.interval),e.rebind=!0,e.ticket="",e.randstr=""}),(function(t){return e.handleError(t)}))},mobileComfirm:function(){this.oldVerify()},oldError:function(){this.isMobileModify=!1,this.phoneError=!1},oldVerify:function(){var e=this,t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:"",o={_jv:{type:"sms/verify"},code:t,type:"verify"};this.$store.dispatch("jv/post",o).then((function(t){t&&(e.isMobileModify=!1,e.isMobileVerify=!0)}),(function(t){e.$refs.verifyphone.empty(),e.phoneError=!0,e.handleError(t)}))},newVerify:function(){var e=this,t={_jv:{type:"sms/verify"},mobile:this.newphon,code:this.newVerifyCode,type:"rebind"};n.status.run((function(){return e.$store.dispatch("jv/post",t)})).then((function(t){t&&(e.isMobileModify=!1,e.isMobileVerify=!1,e.$message.success(e.$t("modify.phontitle")),e.newVerifyCode="",e.newphon="",e.userinfo(),e.$store.dispatch("user/getUserInfo",e.userId))}),(function(t){e.handleError(t),e.newVerifyCode="",e.newphon=""}))},passModify:function(){var e=this;this.isPassModify=!this.isPassModify,this.$refs.oldpass&&this.$nextTick((function(){e.$refs.oldpass.focus()}))},passSub:function(){this.oldPassWord&&this.newPassWord&&this.renewPassword&&this.newPassWord===this.renewPassword?this.passwordComfirm():this.oldPassWord?this.newPassWord?this.newPassWord!==this.renewPassword&&(this.$message.error(this.$t("modify.masstext")),this.passerror=!0):this.$message.error(this.$t("modify.newpassword")):this.$message.error(this.$t("modify.oldpassword"))},notsame:function(){this.newPassWord!==this.renewPassword?this.passerror=!0:this.passerror=!1},passSub2:function(){this.newPassWord&&this.renewPassword&&this.newPassWord===this.renewPassword?this.passwordComfirm():this.newPassWord?this.newPassWord!==this.renewPassword&&(this.$message.error(this.$t("modify.masstext")),this.passerror=!0):this.$message.error(this.$t("modify.newpassword"))},passwordComfirm:function(){var e=this,t={_jv:{type:"users",id:this.userId},password:this.oldPassWord,newPassword:this.newPassWord,password_confirmation:this.renewPassword};n.status.run((function(){return e.$store.dispatch("jv/patch",t)})).then((function(t){t&&(e.$message.success(e.$t("modify.titlepassword")),e.isPassModify=!e.isPassModify,e.oldPassWord="",e.newPassWord="",e.renewPassword="",e.passerror=!1,e.userinfo())}),(function(t){return e.handleError(t)}))},wechatModify:function(){var e=this;this.isWechatModify=!this.isWechatModify,this.wehcatTimer&&window.clearInterval(this.wehcatTimer),this.isWechatModify&&!this.userInfo.wechat?this.createQRcode():this.isWechatModify&&this.userInfo.wechat&&this.userInfo.wechat&&this.$confirm('\n        <i class="el-icon-warning"\n        style=" width: 25px;height: 25px;font-size: 25px;color: #E6A23C;position: absolute;left: 0px;"></i>\n        <p style="margin-left:40px;">确定解除微信绑定?</p>\n        <p style="margin-bottom:50px;margin-left:40px;margin-top:10px;">解绑后，您的微信号将不再绑定当前账号，即无法再登录当前账号，确认解除绑定吗？</p>',{confirmButtonText:"确定",cancelButtonText:"取消",dangerouslyUseHTMLString:!0}).then((function(){e.$store.dispatch("jv/delete",["users/".concat(e.userId,"/wechat")]).then((function(t){t&&(e.isWechatModify=!1,e.userinfo(),e.$message({type:"success",message:"解绑成功!"}))}),(function(t){return e.handleError(t)}))})).catch((function(){e.isWechatModify=!1,e.$message({type:"info",message:"已取消解绑"})}))},realModify:function(){var e=this;this.isRealModify=!this.isRealModify,this.$nextTick((function(){e.$refs.realname.focus()}))},realSub:function(){this.realName&&this.idNumber?this.idNumber.length<18||this.idNumber.length>18?this.$message.error(this.$t("modify.idtitl")):this.authentication():this.realName?this.idNumber||this.$message.error(this.$t("modify.idcardisempty")):this.$message.error(this.$t("modify.emptyname"))},authentication:function(){var e=this,t={_jv:{type:"users/real"},realname:this.realName,identity:this.idNumber};n.status.run((function(){return e.$store.dispatch("jv/patch",t)})).then((function(t){e.isRealModify=!e.isRealModify,e.userinfo(),e.$store.dispatch("user/getUserInfo",e.userId),e.$message.success(e.$t("modify.nameauthensucc"))}),(function(t){var o=t.response.data.errors;422===o[0].statusCode&&o[0].detail||"422"===o[0].status&&o[0].detail?e.$message.error(o[0].detail[0]):o[0].detail&&e.$message.error(o[0].detail)}))},usernameModify:function(){var e=this;this.isNameModify=!this.isNameModify,this.$nextTick((function(){e.$refs.username.focus()}))},nameSub:function(){this.newName?this.newName===this.userInfo.username?this.$message.error(this.$t("modify.repeatname")):this.changename():this.$message.error(this.$t("modify.emptyname"))},changename:function(){var e=this,t={_jv:{type:"users",id:this.userId},username:this.newName};n.status.run((function(){return e.$store.dispatch("jv/patch",t)})).then((function(t){t&&(e.isNameModify=!e.isNameModify,e.$message.success(e.$t("modify.modifysucc"))),e.userinfo(),e.$store.dispatch("user/getUserInfo",e.userId)}),(function(t){return e.handleError(t)}))},toTopic:function(){this.$router.push("/user/".concat(this.userId,"?current=1"))},toFollowing:function(){this.$router.push("/user/".concat(this.userId,"?current=4"))},toFollowers:function(){this.$router.push("/user/".concat(this.userId,"?current=5"))},toLikes:function(){this.$router.push("/user/".concat(this.userId,"?current=3"))},toQuestion:function(){this.$router.push("/user/".concat(this.userId,"?current=2"))}}},v=(o(1050),o(12)),component=Object(v.a)(m,(function(){var e=this,t=e.$createElement,o=e._self._c||t;return o("div",{directives:[{name:"loading",rawName:"v-loading",value:e.loading,expression:"loading"}],staticClass:"myprofile"},[e.userInfo?o("div",{staticClass:"myprofile-c",staticStyle:{"padding-bottom":"0px"}},[o("div",{staticClass:"myprofile-top mtop"},[o("Avatar",{staticClass:"avatar",attrs:{user:{id:e.userInfo.id,username:e.userInfo.username,avatarUrl:e.avataruserInfo.avatarUrl,isReal:e.avataruserInfo.isReal},size:50,round:!0}}),e._v(" "),o("div",{staticClass:"usr"},[o("span",{staticClass:"usrname"},[e._v("\n          "+e._s(e.userInfo.username)+"\n          "),e.userInfo&&e.userInfo.isReal?o("span",{staticClass:"iden"},[o("svg-icon",{staticClass:"auth-icon",attrs:{type:"auth"}}),e._v(" "),o("span",{staticClass:"real"},[e._v(e._s(e.$t("modify.isrealname")))])],1):e.userInfo&&!e.userInfo.isReal&&e.forums&&e.forums.qcloud&&e.forums.qcloud.qcloud_faceid?o("span",{staticClass:"iden"},[o("svg-icon",{staticClass:"auth-icon",attrs:{type:"warning"}}),e._v(" "),o("span",{staticClass:"nreal"},[e._v(e._s(e.$t("modify.norealname")))])],1):e._e()]),e._v(" "),o("span",{staticClass:"usrid"},[e._v(e._s(e.userInfo&&e.userInfo.groupsName?e.userInfo.groupsName:""))])]),e._v(" "),o("span",{staticClass:"setavatar"},[o("span",{staticClass:"setbutton",on:{click:e.setAvatar}},[e._v(e._s(e.$t("modify.setavatar")))])]),e._v(" "),e.isShowAvatar?o("show-avatar",{attrs:{"user-id":e.userId},on:{change:e.changeShow}}):e._e()],1),e._v(" "),o("div",{staticClass:"myprofile-bottom"},[o("div",{staticClass:"myprofile-title marglef",on:{click:e.toTopic}},[o("span",[e._v(e._s(e.$t("profile.topic")))]),e._v(" "),o("span",{staticClass:"num"},[e._v(e._s(e.userInfo.threadCount))])]),e._v(" "),o("div",{staticClass:"myprofile-title",on:{click:e.toQuestion}},[o("span",[e._v(e._s(e.$t("profile.question")))]),e._v(" "),o("span",{staticClass:"num"},[e._v(e._s(e.userInfo.questionCount))])]),e._v(" "),o("div",{staticClass:"myprofile-title",on:{click:e.toFollowing}},[o("span",[e._v(e._s(e.$t("profile.following")))]),e._v(" "),o("span",{staticClass:"num"},[e._v(e._s(e.userInfo.followCount))])]),e._v(" "),o("div",{staticClass:"myprofile-title",on:{click:e.toFollowers}},[o("span",[e._v(e._s(e.$t("profile.followers")))]),e._v(" "),o("span",{staticClass:"num"},[e._v(e._s(e.userInfo.fansCount))])]),e._v(" "),o("div",{staticClass:"myprofile-title",on:{click:e.toLikes}},[o("span",[e._v(e._s(e.$t("profile.likes")))]),e._v(" "),o("span",{staticClass:"num"},[e._v(e._s(e.userInfo.likedCount))])])])]):e._e(),e._v(" "),e.userInfo?o("div",{class:e.isNameModify?"myprofile-c bgcolor":"myprofile-c"},[o("div",{class:{profileborder:!0,pborder:!e.isNameModify}},[o("div",{staticClass:"myprofile-top"},[o("span",{staticClass:"sig"},[e._v(e._s(e.$t("profile.username")))]),e._v(" "),o("span",{directives:[{name:"show",rawName:"v-show",value:e.userInfo&&e.userInfo.canEditUsername,expression:"userInfo && userInfo.canEditUsername"}],staticClass:"setavatar"},[o("span",{staticClass:"setbutton",on:{click:e.usernameModify}},[e._v(e._s(e.isNameModify?e.$t("profile.cancelModify"):e.$t("profile.modify")))])])]),e._v(" "),o("div",{directives:[{name:"show",rawName:"v-show",value:!e.isNameModify,expression:"!isNameModify"}],staticClass:"myprofile-btom2"},[e._v("\n        "+e._s(e.userInfo&&e.userInfo.username?e.userInfo.username:"")+"\n      ")]),e._v(" "),o("div",{directives:[{name:"show",rawName:"v-show",value:e.isNameModify,expression:"isNameModify"}],staticClass:"myprofile-btom"},[o("el-dialog",{attrs:{title:e.$t("profile.username"),visible:e.isNameModify,width:"620px","before-close":e.usernameModify},on:{"update:visible":function(t){e.isNameModify=t}}},[o("form",{staticClass:"form"},[o("el-input",{ref:"username",staticClass:"passbtom",attrs:{placeholder:e.$t("modify.numbermodifitions")},model:{value:e.newName,callback:function(t){e.newName=t},expression:"newName"}}),e._v(" "),o("el-button",{staticClass:"ebutton",attrs:{type:"primary"},on:{click:e.nameSub}},[e._v(e._s(e.$t("profile.confirmModify")))])],1)])],1)])]):e._e(),e._v(" "),e.userInfo?o("div",{class:e.isSignModify?"myprofile-c bgcolor":"myprofile-c"},[o("div",{staticClass:"profileborder"},[o("div",{staticClass:"myprofile-top"},[o("span",{staticClass:"sig"},[e._v(e._s(e.$t("modify.signaturetitle")))]),e._v(" "),o("span",{staticClass:"setavatar"},[o("span",{staticClass:"setbutton",on:{click:e.signModify}},[e._v(e._s(e.isSignModify?e.$t("profile.cancelModify"):e.$t("profile.modify")))])])]),e._v(" "),o("div",{directives:[{name:"show",rawName:"v-show",value:!e.isSignModify,expression:"!isSignModify"}],class:""!==e.userInfo.signature?"myprofile-btom-sign":"myprofile-btom-sign signcolor"},[e._v("\n        "+e._s(e.userInfo.signature?e.userInfo.signature:e.$t("modify.nosignature"))+"\n      ")]),e._v(" "),o("div",{directives:[{name:"show",rawName:"v-show",value:e.isSignModify,expression:"isSignModify"}],staticClass:"text"},[o("el-dialog",{attrs:{title:e.$t("modify.signaturetitle"),visible:e.isSignModify,width:"620px","before-close":e.signModify},on:{"update:visible":function(t){e.isSignModify=t}}},[o("form",[o("el-input",{ref:"sign",attrs:{type:"textarea",rows:5,placeholder:e.$t("modify.inputsignautre")},on:{input:e.fun},model:{value:e.inputVal,callback:function(t){e.inputVal=t},expression:"inputVal"}}),e._v(" "),o("div",{staticClass:"cannum"},[e._v("\n              "+e._s(e.$t("modify.canalsoinput")+""+(e.num-e.wordnumber)+e.$t("modify.wordnumber"))+"\n            ")]),e._v(" "),o("div",{staticClass:"confirmbtn"},[o("div",{staticClass:"allbtn"},[o("el-button",{staticClass:"comfirm",attrs:{type:"small"},on:{click:e.sigComfirm}},[e._v(e._s(this.$t("report.confirm")))]),e._v(" "),o("el-button",{staticClass:"cancel",attrs:{type:"small"},on:{click:e.signModify}},[e._v(e._s(this.$t("report.cancel")))])],1)])],1)])],1)])]):e._e(),e._v(" "),e.userInfo&&e.forums&&e.forums.qcloud&&e.forums.qcloud.qcloud_sms?o("div",{staticClass:"myprofile-c",class:e.isMobileModify?"myprofile-c bgcolor":"myprofile-c"},[o("div",{staticClass:"profileborder"},[o("div",{staticClass:"myprofile-top"},[o("span",{staticClass:"sig"},[e._v(e._s(e.$t("profile.mobile")))]),e._v(" "),o("span",{staticClass:"setavatar"},[o("span",{staticClass:"setbutton",on:{click:e.mobileModify}},[e._v("\n            "+e._s(e.isMobileModify?e.$t("profile.cancelModify"):e.userInfo.mobile?e.$t("profile.modify"):e.$t("profile.bindingmobile"))+"\n          ")])])]),e._v(" "),o("div",{directives:[{name:"show",rawName:"v-show",value:!e.isMobileModify,expression:"!isMobileModify"}],staticClass:"myprofile-btom2"},[o("div",{class:e.userInfo.mobile?"pmobile":""},[e._v("\n          "+e._s(e.userInfo.mobile?e.userInfo.mobile:e.$t("modify.setphontitle"))+"\n        ")])]),e._v(" "),o("div",{directives:[{name:"show",rawName:"v-show",value:e.isMobileModify&&e.userInfo.mobile,expression:"isMobileModify && userInfo.mobile"}],staticClass:"myprofile-btom"},[o("div",{staticClass:"pmobile"},[e._v(e._s(e.userInfo.mobile))]),e._v(" "),e.isMobileModify&&e.userInfo.mobile?o("verify-phone",{ref:"verifyphone",attrs:{error:e.phoneError,mobile:e.userInfo.mobile},on:{close:e.oldError,password:e.oldVerify,sendsms:e.sendsms2}}):e._e()],1),e._v(" "),o("el-dialog",{attrs:{title:e.$t("profile.bindingmobile"),visible:e.isMobileVerify,width:"620px","before-close":e.mobileVerify},on:{"update:visible":function(t){e.isMobileVerify=t}}},[o("form",{staticClass:"form"},[o("el-input",{class:e.isChange?"phone-input phonechange":"phone-input",attrs:{placeholder:e.$t("modify.newphonnumber")},on:{input:e.changeinput},model:{value:e.newphon,callback:function(t){e.newphon=t},expression:"newphon"}}),e._v(" "),o("el-button",{staticClass:"count-b",class:{disabled:!e.canClick2},attrs:{disabled:!e.canClick2,size:"middle"},on:{click:e.sendsms}},[e._v(e._s(e.content2))]),e._v(" "),o("el-input",{staticClass:"passbtom",attrs:{placeholder:e.$t("modify.inputnewverifycode")},model:{value:e.newVerifyCode,callback:function(t){e.newVerifyCode=t},expression:"newVerifyCode"}}),e._v(" "),o("el-button",{staticClass:"ebutton",attrs:{type:"primary"},on:{click:e.newVerify}},[e._v(e._s(e.$t("profile.submitchange")))])],1)]),e._v(" "),e.isMobileModify&&!e.userInfo.mobile?o("div",{staticClass:"myprofile-btom"},[o("el-dialog",{attrs:{title:e.$t("profile.bindingmobile"),visible:e.isMobileModify,width:"620px","before-close":e.mobileModify},on:{"update:visible":function(t){e.isMobileModify=t}}},[o("form",{staticClass:"form"},[o("el-input",{ref:"oldphone",class:e.isChange?"phone-input phonechange":"phone-input",attrs:{maxlength:"11",placeholder:e.$t("modify.newphon")},on:{input:e.changeinput},model:{value:e.newphon,callback:function(t){e.newphon=t},expression:"newphon"}}),e._v(" "),o("el-button",{staticClass:"count-b",class:{disabled:!e.canClick},attrs:{disabled:!e.canClick,size:"middle"},on:{click:e.sendsms}},[e._v(e._s(e.content))]),e._v(" "),o("el-input",{staticClass:"passbtom",attrs:{placeholder:e.$t("modify.newverifycode")},model:{value:e.setnum,callback:function(t){e.setnum=t},expression:"setnum"}}),e._v(" "),o("el-button",{staticClass:"ebutton",attrs:{type:"primary"},on:{click:e.dingphon}},[e._v(e._s(e.$t("profile.submitchange")))])],1)])],1):e._e()],1)]):e._e(),e._v(" "),e.userInfo?o("div",{class:e.isPassModify?"myprofile-c bgcolor":"myprofile-c"},[o("div",{staticClass:"profileborder"},[o("div",{staticClass:"myprofile-top"},[o("span",{staticClass:"sig"},[e._v(e._s(e.$t("profile.password")))]),e._v(" "),o("span",{staticClass:"setavatar"},[o("span",{staticClass:"setbutton",on:{click:e.passModify}},[e._v("\n            "+e._s(e.isPassModify?e.userInfo&&e.userInfo.hasPassword?e.$t("profile.cancelModify"):"取消设置":e.userInfo&&e.userInfo.hasPassword?e.$t("profile.modify"):"设置")+"\n          ")])])]),e._v(" "),o("div",{directives:[{name:"show",rawName:"v-show",value:!e.isPassModify,expression:"!isPassModify"}],staticClass:"myprofile-btom2"},[e._v("\n        "+e._s(e.userInfo&&e.userInfo.hasPassword?e.$t("profile.isset"):e.$t("profile.withoutsetpass"))+"\n      ")]),e._v(" "),o("div",{directives:[{name:"show",rawName:"v-show",value:e.isPassModify,expression:"isPassModify"}],staticClass:"myprofile-btom"},[e.userInfo&&e.userInfo.hasPassword?o("div",[o("el-dialog",{attrs:{title:e.$t("profile.password"),visible:e.isPassModify,width:"620px","before-close":e.passModify},on:{"update:visible":function(t){e.isPassModify=t}}},[o("form",{staticClass:"form"},[o("el-input",{ref:"oldpass",staticClass:"passbtom",attrs:{placeholder:e.$t("modify.enterold"),type:"password","show-password":""},model:{value:e.oldPassWord,callback:function(t){e.oldPassWord=t},expression:"oldPassWord"}}),e._v(" "),o("el-input",{staticClass:"passbtom",attrs:{placeholder:e.$t("modify.enterNew"),type:"password","show-password":""},model:{value:e.newPassWord,callback:function(t){e.newPassWord=t},expression:"newPassWord"}}),e._v(" "),o("div",{class:e.passerror?"rep passerr":"rep"},[o("el-input",{class:e.passerror?"passbtom inputerr":"passbtom",attrs:{placeholder:e.$t("modify.enterNewRepeat"),type:"password","show-password":""},on:{input:e.notsame},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.passSub(t)}},model:{value:e.renewPassword,callback:function(t){e.renewPassword=t},expression:"renewPassword"}}),e._v(" "),e.passerror?o("div",{staticClass:"passerror"},[e._v("\n                  "+e._s(e.$t("modify.reenter"))+"\n                ")]):e._e()],1),e._v(" "),o("el-button",{staticClass:"ebutton",attrs:{type:"primary"},on:{click:e.passSub}},[e._v(e._s(e.$t("profile.submitchange")))])],1)])],1):o("div",[o("el-dialog",{attrs:{title:e.$t("profile.password"),visible:e.isPassModify,width:"620px","before-close":e.passModify},on:{"update:visible":function(t){e.isPassModify=t}}},[o("form",{staticClass:"form"},[o("el-input",{staticClass:"passbtom",attrs:{placeholder:e.$t("modify.enterNew"),type:"password","show-password":""},model:{value:e.newPassWord,callback:function(t){e.newPassWord=t},expression:"newPassWord"}}),e._v(" "),o("div",{class:e.passerror?"rep passerr":"rep"},[o("el-input",{class:e.passerror?"passbtom inputerr":"passbtom",attrs:{placeholder:e.$t("modify.enterNewRepeat"),type:"password","show-password":""},on:{input:e.notsame},nativeOn:{keyup:function(t){return!t.type.indexOf("key")&&e._k(t.keyCode,"enter",13,t.key,"Enter")?null:e.passSub2(t)}},model:{value:e.renewPassword,callback:function(t){e.renewPassword=t},expression:"renewPassword"}}),e._v(" "),e.passerror?o("div",{staticClass:"passerror"},[e._v("\n                  "+e._s(e.$t("modify.reenter"))+"\n                ")]):e._e()],1),e._v(" "),o("el-button",{staticClass:"ebutton",attrs:{type:"primary"},on:{click:e.passSub2}},[e._v(e._s(e.$t("modify.submit")))])],1)])],1)])])]):e._e(),e._v(" "),e.userInfo&&e.forums&&e.forums.passport&&e.forums.passport.oplatform_close?o("div",{class:e.isWechatModify?"myprofile-c bgcolor":"myprofile-c"},[o("div",{staticClass:"profileborder"},[o("div",{staticClass:"myprofile-top"},[o("span",{staticClass:"sig"},[e._v(e._s(e.$t("profile.wechat")))]),e._v(" "),o("el-button",{staticClass:"setavatar",attrs:{type:"text"}},[o("span",{staticClass:"setbutton",on:{click:e.wechatModify}},[e._v("\n            "+e._s(e.isWechatModify?e.userInfo&&e.userInfo.wechat&&e.userInfo.wechat.nickname?"取消解绑":"取消绑定":e.userInfo&&e.userInfo.wechat&&e.userInfo.wechat.nickname?"解绑":"绑定")+"\n          ")])])],1),e._v(" "),o("div",{staticClass:"myprofile-btom2"},[e._v("\n        "+e._s(e.userInfo&&e.userInfo.wechat?e.userInfo.wechat.nickname:e.$t("profile.withoutbindwechat"))+"\n      ")]),e._v(" "),e.isWechatModify&&e.userInfo&&!e.userInfo.wechat?o("div",{staticClass:"wehcat-bind"},[o("el-dialog",{attrs:{visible:e.isWechatModify,width:"180px","before-close":e.wechatModify},on:{"update:visible":function(t){e.isWechatModify=t}}},[o("div",{staticClass:"qrcode-text"},[o("svg-icon",{staticClass:"wechat-logo",attrs:{type:"wechat-logo"}}),e._v(" "),o("span",{staticClass:"qrtext"},[e._v("微信扫码绑定")])],1),e._v(" "),o("el-image",{staticClass:"qr-code",attrs:{src:e.wechatBind.base64_img}}),e._v(" "),o("div",[e._v("请用微信扫一扫")]),e._v(" "),o("div",{staticClass:"scanqr"},[e._v("扫码上方二维码")])],1)],1):e._e()])]):e._e(),e._v(" "),e.userInfo&&!e.userInfo.realname&&e.forums&&e.forums.qcloud&&e.forums.qcloud.qcloud_faceid?o("div",{class:e.isRealModify?"myprofile-c bgcolor":"myprofile-c"},[o("div",{staticClass:"profileborder"},[o("div",{staticClass:"myprofile-top"},[o("span",{staticClass:"sig"},[e._v(e._s(e.$t("modify.realnametitle")))]),e._v(" "),o("span",{staticClass:"setavatar"},[o("span",{staticClass:"setbutton",on:{click:e.realModify}},[e._v("\n            "+e._s(e.isRealModify?e.$t("profile.cancelcertification"):e.$t("profile.tocertification"))+"\n          ")])])]),e._v(" "),o("div",{directives:[{name:"show",rawName:"v-show",value:!e.isRealModify,expression:"!isRealModify"}],staticClass:"myprofile-btom2 myprofile-btom3"},[e._v("\n        "+e._s(e.userInfo&&e.userInfo.realname?e.$t("profile.isset"):e.$t("profile.withoutcertification"))+"\n      ")]),e._v(" "),o("div",{directives:[{name:"show",rawName:"v-show",value:e.isRealModify,expression:"isRealModify"}],staticClass:"myprofile-btom"},[o("el-dialog",{attrs:{title:e.$t("modify.realnametitle"),visible:e.isRealModify,width:"620px","before-close":e.realModify},on:{"update:visible":function(t){e.isRealModify=t}}},[o("form",{staticClass:"form"},[o("el-input",{ref:"realname",staticClass:"passbtom",attrs:{placeholder:e.$t("modify.realname")},model:{value:e.realName,callback:function(t){e.realName=t},expression:"realName"}}),e._v(" "),o("el-input",{staticClass:"passbtom",attrs:{placeholder:e.$t("modify.enteridnumber")},model:{value:e.idNumber,callback:function(t){e.idNumber=t},expression:"idNumber"}}),e._v(" "),o("el-button",{staticClass:"ebutton",attrs:{type:"primary"},on:{click:e.realSub}},[e._v(e._s(e.$t("profile.comfirmsubmit")))])],1)])],1)])]):e._e(),e._v(" "),e.userInfo&&e.userInfo.realname&&e.forums&&e.forums.qcloud&&e.forums.qcloud.qcloud_faceid?o("div",{staticClass:"myprofile-c"},[o("div",{staticClass:"profileborder"},[o("div",{staticClass:"myprofile-top"},[o("span",{staticClass:"sig"},[e._v(e._s(e.$t("modify.realnametitle")))])]),e._v(" "),o("div",{staticClass:"myprofile-btom2 myprofile-btom3"},[e._v("\n        "+e._s(e.userInfo&&e.userInfo.realname?e.userInfo.realname+" ("+e.userInfo.identity+")":"")+"\n      ")])])]):e._e()])}),[],!1,null,"49473937",null);t.default=component.exports;installComponents(component,{Avatar:o(268).default,SvgIcon:o(60).default,ShowAvatar:o(1117).default,VerifyPhone:o(1118).default})},889:function(e,t,o){},890:function(e,t,o){},891:function(e,t,o){}}]);