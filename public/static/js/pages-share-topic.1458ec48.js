(window.webpackJsonp=window.webpackJsonp||[]).push([["pages-share-topic"],{"20fe":function(t,e,i){"use strict";var o=i("f969");i.n(o).a},"245f":function(t,e,i){"use strict";(function(e){var o=i("4ea4"),n=o(i("6f74")),r=i("b95e"),s=o(i("4c82"));t.exports={mixins:[n.default,s.default],methods:{getForum:function(){var t=this;this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(e){e&&(t.forum=e)}))},jump2PhoneLoginPage:function(){uni.redirectTo({url:"/pages/user/phone-login"})},jump2PhoneLoginRegisterPage:function(){uni.redirectTo({url:"/pages/user/phone-login-register"})},jump2LoginPage:function(){uni.redirectTo({url:"/pages/user/login"})},jump2RegisterPage:function(){uni.redirectTo({url:"/pages/user/register"})},jump2RegisterExtendPage:function(){uni.redirectTo({url:"/pages/user/supple-mentary"})},jump2LoginBindPage:function(){uni.redirectTo({url:"/pages/user/login-bind"})},jump2RegisterBindPage:function(){uni.redirectTo({url:"/pages/user/register-bind"})},jump2LoginBindPhonePage:function(){uni.redirectTo({url:"/pages/user/login-bind-phone"})},jump2RegisterBindPhonePage:function(){uni.redirectTo({url:"/pages/user/register-bind-phone"})},jump2findpwdPage:function(){uni.navigateTo({url:"/pages/modify/findpwd?pas=reset_pwd"})},mpLoginMode:function(){this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&this.jump2LoginPage(),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open())},h5LoginMode:function(){s.default.isWeixin().isWeixin?(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&(uni.setStorageSync("register",1),this.$store.dispatch("session/wxh5Login"))):(this.forums&&this.forums.set_reg&&0===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}),this.forums&&this.forums.set_reg&&1===this.forums.set_reg.register_type&&this.jump2PhoneLoginRegisterPage(),this.forums&&this.forums.set_reg&&2===this.forums.set_reg.register_type&&uni.navigateTo({url:"/pages/user/login"}))},refreshmpParams:function(){var t=this;uni.login({success:function(i){if("login:ok"===i.errMsg){var o=i.code;uni.getUserInfo({success:function(e){var i={data:{attributes:{js_code:o,iv:e.iv,encryptedData:e.encryptedData}}};t.$store.dispatch("session/setParams",i)},fail:function(t){e.log(t)}})}},fail:function(t){e.log(t)}})},mpLogin:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0;uni.setStorageSync("register",t),uni.setStorageSync("isSend",!0),this.$store.getters["session/get"]("auth").open()},wxh5Login:function(){var t=arguments.length>0&&void 0!==arguments[0]?arguments[0]:0,e=arguments.length>1&&void 0!==arguments[1]?arguments[1]:0;uni.setStorageSync("register",t),uni.setStorageSync("rebind",e),uni.setStorageSync("h5_wechat_login",1),this.$store.dispatch("session/wxh5Login")},getLoginParams:function(t,e){var i=t;if(""===t.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===t.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{var o=uni.getStorageSync("token");""!==o&&(i.data.attributes.token=o),this.login(i,e)}},getLoginBindParams:function(t,e){var i=arguments.length>2&&void 0!==arguments[2]?arguments[2]:0;this.refreshmpParams();var o=t;if(""===t.data.attributes.username)uni.showToast({icon:"none",title:this.i18n.t("user.usernameEmpty"),duration:2e3});else if(""===t.data.attributes.password)uni.showToast({icon:"none",title:this.i18n.t("user.passwordEmpty"),duration:2e3});else{1===i&&(o.data.attributes.rebind=1);var n=uni.getStorageSync("token");""!==n&&(o.data.attributes.token=n),this.login(o,e)}},login:function(t,i){var o=this;this.$store.dispatch("session/h5Login",t).then((function(t){if(t&&t.data&&t.data.data&&t.data.data.id&&(o.logind(),o.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]).then((function(t){t&&t.set_site&&t.set_site.site_mode!==r.SITE_PAY&&uni.getStorage({key:"page",success:function(t){e.log("resData",t),uni.redirectTo({url:t.data})}}),t&&t.set_site&&t.set_site.site_mode===r.SITE_PAY&&o.user&&!o.user.paid&&uni.redirectTo({url:"/pages/site/info"})})),uni.showToast({title:i,duration:2e3})),t&&t.data&&t.data.errors){if("401"===t.data.errors[0].status||"402"===t.data.errors[0].status||"500"===t.data.errors[0].status){var n=o.i18n.t("core.".concat(t.data.errors[0].code));uni.showToast({icon:"none",title:n,duration:2e3})}if("403"===t.data.errors[0].status||"422"===t.data.errors[0].status){var s=o.i18n.t("core.".concat(t.data.errors[0].code))||o.i18n.t(t.data.errors[0].detail[0]);uni.showToast({icon:"none",title:s,duration:2e3})}}})).catch((function(t){return e.log(t)}))}}}}).call(this,i("5a52").default)},"368d":function(t,e,i){t.exports=i.p+"static/img/msg-warning.f35ce51f.svg"},3871:function(t,e,i){"use strict";var o=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=o(i("d4ec")),r=o(i("bee2")),s=function(){function t(){(0,n.default)(this,t)}return(0,r.default)(t,[{key:"palette",value:function(t){return{width:"700px",height:"708px",background:"#ffffff",views:[{type:"image",url:t.recoimg,css:{width:"80px",height:"80px",top:"40px",left:"40px",rotate:"0",borderRadius:"40px",borderWidth:"",borderColor:"#000000",shadow:"",mode:"scaleToFill"}},{type:"text",text:t.reconame,css:{color:"#000000",background:"rgba(0,0,0,0)",width:"500px",height:"40.04px",top:"41px",left:"140px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"bold",maxLines:"1",lineHeight:"40.40400000000001px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.username+t.stay+t.useratttype+t.published+t.contents,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"535px",height:"34.32px",top:"88px",left:"140px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"2",lineHeight:"34.632000000000005px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"rect",css:{background:"#F7F7F7",width:"".concat(t.marglength,"px"),height:"50px",top:"384px",left:"40px",rotate:"0",borderRadius:"6px",shadow:"",color:"#F7F7F7"}},{type:"text",text:t.goddessvideo,css:{color:"#777777",background:"rgba(0,0,0,0)",width:"".concat(t.attachlength,"px"),height:"27.119999999999997px",top:"395px",left:"60px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"1",lineHeight:"26.64px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"center"}},{type:"rect",css:{background:"#F9FAFC",width:"700px",height:"200px",top:"508px",left:"0px",rotate:"0",borderRadius:"",shadow:"",color:"#F9FAFC"}},{type:"image",url:t.userweixincode,css:{width:"140px",height:"140px",top:"538px",left:"40px",rotate:"0",borderRadius:"0px",borderWidth:"",borderColor:"#000000",shadow:"",mode:"scaleToFill"}},{type:"text",text:t.longpressrecog,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"560px",height:"31.639999999999997px",top:"568px",left:"210px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"400",maxLines:"1",lineHeight:"31.080000000000002px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.comefrom+t.slitename,css:{color:"#AAAAAA",background:"rgba(0,0,0,0)",width:"450px",height:"27.119999999999997px",top:"615px",left:"210px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"1",lineHeight:"26.64px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"image",url:t.usercontimg[0],css:{height:"100px",top:"200px",left:"".concat(t.heightdefill,"px"),rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",mode:"aspectFit"}}]}}}]),t}();e.default=s},"578d":function(t,e,i){"use strict";(function(t){var o=i("4ea4");i("99af"),i("4160"),i("07ac"),i("ac1f"),i("5319"),i("159b"),Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=o(i("7e24")),r=o(i("a646")),s=o(i("3871")),a=o(i("cf52")),d=o(i("8d80")),h={mixins:[o(i("b469")).default],data:function(){return{imagePath:"",width:700,template:{},userid:"",slitename:"",slitelogo:"",themeid:"",headerImg:"",headerName:"",postyTepy:"",subHeading:"",contentTitle:"",content:"",contentImg:[],video:"",videoduc:"",videotime:"",attachmentsType:"",themwidth:180,renamewidth:400,reconame:"",recoimg:"",constyle:0,paddingtop:43,imgtop:0,jurisdiction:!0,that:"",attachlength:97,marglength:160,heightdefill:"",picutre:"",picutrecopy:"",contentheight:"",implement:"",times:null}},onLoad:function(t){this.showToast(),this.themeid=t.id,this.userid=this.usersid,this.slitename=this.forums.set_site.site_name,this.slitelogo=this.forums.set_site.site_logo||"".concat(this.$u.host(),"static/logo.png"),this.getusertitle()},computed:{usersid:function(){return this.$store.getters["session/get"]("userId")},userInfo:function(){return this.$store.getters["jv/get"]("users/".concat(this.userid))}},watch:{heightdefill:{handler:function(t){(t||0===t)&&this.initData()},deep:!0},content:{handler:function(t){t&&this.implement&&this.initData()},deep:!0}},mounted:function(){var t=this,e=this;clearInterval(this.times),this.times=setTimeout((function(){""===t.imagePath&&(t.openLoading(),uni.showToast({title:e.i18n.t("share.titlePainter"),icon:"none",duration:3e3}))}),5e3)},methods:{getusertitle:function(){this.reconame=this.userInfo.username,this.recoimg=this.userInfo.avatarUrl||"".concat(this.$u.host(),"static/noavatar.gif"),this.getthemdata()},showToast:function(){this.$refs.toast.showLoading({icon:"icon-load",message:this.i18n.t("share.generating"),position:"middle"})},openLoading:function(){this.$refs.toast.close()},getthemdata:function(){var e=this,i=this;this.$store.dispatch("jv/get","threads/".concat(this.themeid,"?include=user,firstPost,firstPost.images,threadVideo,category")).then((function(o){if(t.log(o),e.headerName=o.user.username,e.postyTepy=o.type,e.headerImg=o.user.avatarUrl||"".concat(e.$u.host(),"static/images/noavatar.gif"),o.price>0)e.contentImg.push(e.slitelogo),e.contentImg&&uni.getImageInfo({src:i.contentImg[0],success:function(t){var e=t.width/(t.height/100);i.heightdefill=(700-e)/2}}),e.attachmentsType=o.category.name;else{o.firstPost.images.length>=1||2===e.postyTepy?(2===e.postyTepy&&o.threadVideo.cover_url,e.implement=!1):e.implement=!0,Object.values(o.firstPost.images).forEach((function(t){e.contentImg.push(t.url||t.thumbUrl)})),e.contentImg&&uni.getImageInfo({src:i.contentImg[0],success:function(t){var e=t.height*(620/t.width);i.heightdefill=e-402}}),e.contentTitle=o.title,1===o.type?o.firstPost.images.length<1&&(e.content=o.firstPost.content):e.content=o.firstPost.content;var n=e.content.length-e.content.replace(/[\r\n]/g,"").length;if(e.content){var r=Math.ceil(e.content.length/23)+n;e.contentheight=r>=11?0:472-42*r}e.attachmentsType=o.category.name,e.attachlength=24*e.attachmentsType.length+3,e.marglength=e.attachlength+40,2===e.postyTepy&&(e.video=o.threadVideo.cover_url,e.videoduc=o.threadVideo.file_name,e.video?uni.getImageInfo({src:i.video,success:function(t){var e=t.height*(620/t.width);i.heightdefill=e-402}}):i.heightdefill=0)}}))},initData:function(){this.contentTitle||(this.imgtop=80);var t={username:this.headerName,userheader:this.headerImg,usertitle:this.contentTitle,usercontent:this.content,usercontimg:this.contentImg,userattname:this.attachmentsName,useratttype:this.attachmentsType,userweixincode:"".concat(this.$u.host(),"api/oauth/wechat/miniprogram/code?path=/topic/index?id=").concat(this.themeid),slitename:this.slitename,uservideo:this.video,uservideoduc:this.videoduc,namewidth:this.themwidth,renamewidth:this.renamewidth,reconame:this.reconame+this.i18n.t("share.recomment"),recoimg:this.recoimg,imgtop:this.imgtop,attachlength:this.attachlength,marglength:this.marglength,heightdefill:this.heightdefill,contentheight:this.contentheight,longpressrecog:this.i18n.t("share.longpressrecog"),recomment:this.i18n.t("share.recomment"),goddessvideo:this.attachmentsType,comefrom:this.i18n.t("share.comefrom"),stay:this.i18n.t("share.stay"),published:this.i18n.t("share.published"),contents:this.i18n.t("share.contents")};this.contentTitle?1===this.contentImg.length||this.contentImg.length>1?(this.constyle=1100+this.heightdefill,this.paddingtop=43,this.template=(new r.default).palette(t)):0===this.contentImg.length&&this.content?(this.constyle=1083-this.contentheight,this.paddingtop=41,this.template=(new n.default).palette(t)):2===this.postyTepy&&(this.constyle=1100+this.heightdefill,this.paddingtop=43,this.template=(new a.default).palette(t)):this.contentTitle?(this.constyle=1082-this.contentheight,this.paddingtop=46,this.template=(new d.default).palette(t)):this.content&&1===this.contentImg.length?(this.constyle=1100+this.heightdefill,this.paddingtop=43,this.template=(new r.default).palette(t)):this.content||1!==this.contentImg.length?this.content&&this.contentImg.length>1?(this.constyle=1100+this.heightdefill,this.paddingtop=43,this.template=(new r.default).palette(t)):2===this.postyTepy?(this.constyle=1100+this.heightdefill,this.paddingtop=43,this.template=(new a.default).palette(t)):(this.constyle=1082-this.contentheight,this.paddingtop=46,this.template=(new d.default).palette(t)):(this.constyle=728,this.paddingtop=90,this.template=(new s.default).palette(t))},onImgOK:function(t){this.imagePath=t.detail.path,clearInterval(this.times),this.openLoading()},imgErr:function(){this.openLoading(),uni.showModal({title:this.i18n.t("discuzq.msgbox.title"),content:this.i18n.t("share.buildfailed"),showCancel:!1})},fun:function(){var t=this;uni.getSetting({success:function(e){e.authSetting["scope.writePhotosAlbum"]?t.jurisdiction=e.authSetting["scope.writePhotosAlbum"]:t.jurisdiction=!1}}),this.jurisdiction||uni.openSetting({success:function(e){t.jurisdiction=e.authSetting["scope.writePhotosAlbum"]}}),uni.showModal({title:t.i18n.t("discuzq.msgbox.title"),content:t.i18n.t("share.confirm"),success:function(e){e.confirm&&uni.saveImageToPhotosAlbum({filePath:t.imagePath,success:function(){uni.showToast({title:t.i18n.t("share.successfully"),icon:"none",duration:2e3})},fail:function(e){"saveImageToPhotosAlbum:fail auth deny"===e.errMsg&&(t.jurisdiction=!1),uni.showToast({title:t.i18n.t("share.savefailed"),icon:"none",duration:2e3})}})}})},previewImage:function(){var t=this.imagePath;uni.previewImage({current:t,urls:[t]})}}};e.default=h}).call(this,i("5a52").default)},"6f74":function(t,e,i){"use strict";var o=i("b95e");t.exports={computed:{user:function(){var t=this.$store.getters["session/get"]("userId");return t?this.$store.getters["jv/get"]("users/".concat(t)):{}}},methods:{getUserInfo:function(){var t=arguments.length>0&&void 0!==arguments[0]&&arguments[0],e=(new Date).getTime(),i=uni.getStorageSync(o.STORGE_GET_USER_TIME);if(t||(e-i)/1e3>60){var n={include:"groups,wechat"},r=this.$store.getters["session/get"]("userId");this.$store.commit("jv/deleteRecord",{_jv:{type:"users",id:r}}),this.$store.dispatch("jv/get",["users/".concat(r),{params:n}]).then((function(){return uni.$emit("updateNotiNum")})),uni.setStorageSync(o.STORGE_GET_USER_TIME,(new Date).getTime())}},logind:function(){var t=this,e=this.$store.getters["session/get"]("userId");if(e){this.$store.dispatch("jv/get",["forum",{params:{include:"users"}}]);this.$store.dispatch("jv/get",["users/".concat(e),{params:{include:"groups,wechat"}}]).then((function(e){t.$u.event.$emit("logind",e)})),this.$store.dispatch("forum/setError",{loading:!1})}}}}},"7e24":function(t,e,i){"use strict";var o=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=o(i("d4ec")),r=o(i("bee2")),s=function(){function t(){(0,n.default)(this,t)}return(0,r.default)(t,[{key:"palette",value:function(t){return{width:"700px",height:"".concat(1082-t.contentheight,"px"),background:"#ffffff",views:[{type:"rect",css:{background:"#F7F7F7",width:"".concat(t.marglength,"px"),height:"50px",top:"".concat(757-t.contentheight,"px"),left:"40px",rotate:"0",borderRadius:"6px",shadow:"",color:"#F7F7F7"}},{type:"text",text:t.goddessvideo,css:{color:"#777777",background:"rgba(0,0,0,0)",width:"".concat(t.attachlength,"px"),height:"27.119999999999997px",top:"".concat(769-t.contentheight,"px"),left:"60px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"1",lineHeight:"26.64px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"center"}},{type:"rect",css:{background:"#F9FAFC",width:"700px",height:"200px",top:"".concat(882-t.contentheight,"px"),left:"0px",rotate:"0",borderRadius:"",shadow:"",color:"#F9FAFC"}},{type:"image",url:t.userweixincode,css:{width:"140px",height:"140px",top:"".concat(912-t.contentheight,"px"),left:"40px",rotate:"0",borderRadius:"0px",borderWidth:"",borderColor:"#000000",shadow:"",mode:"scaleToFill"}},{type:"text",text:t.longpressrecog,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"560px",height:"31.639999999999997px",top:"".concat(942-t.contentheight,"px"),left:"210px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"400",maxLines:"1",lineHeight:"31.080000000000002px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.comefrom+t.slitename,css:{color:"#AAAAAA",background:"rgba(0,0,0,0)",width:"450px",height:"27.119999999999997px",top:"".concat(989-t.contentheight,"px"),left:"210px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"1",lineHeight:"26.64px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.usertitle,css:{color:"#303133",background:"rgba(0,0,0,0)",width:"453px",height:"33.9px",top:"159px",left:"40px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"30px",fontWeight:"bold",maxLines:"1",lineHeight:"33.300000000000004px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.usercontent,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"616px",height:"".concat(472.77999999999986-t.contentheight,"px"),top:"240px",left:"40px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"400",maxLines:"11",lineHeight:"46.620000000000005px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"image",url:t.recoimg,css:{width:"80px",height:"80px",top:"40px",left:"40px",rotate:"0",borderRadius:"40px",borderWidth:"",borderColor:"#000000",shadow:"",mode:"scaleToFill"}},{type:"text",text:t.username+t.stay+t.useratttype+t.published+t.contents,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"535px",height:"27.119999999999997px",top:"88px",left:"140px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"2",lineHeight:"26.64px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.reconame,css:{color:"#000000",background:"rgba(0,0,0,0)",width:"500px",height:"31.639999999999997px",top:"39px",left:"139px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"bold",maxLines:"1",lineHeight:"31.080000000000002px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}}]}}}]),t}();e.default=s},8663:function(t,e,i){"use strict";i.r(e);var o=i("a132"),n=i("c6f1");for(var r in n)["default"].indexOf(r)<0&&function(t){i.d(e,t,(function(){return n[t]}))}(r);i("20fe");var s=i("f0c5"),a=Object(s.a)(n.default,o.b,o.c,!1,null,"2d5e2807",null,!1,o.a,void 0);e.default=a.exports},"8d80":function(t,e,i){"use strict";var o=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=o(i("d4ec")),r=o(i("bee2")),s=function(){function t(){(0,n.default)(this,t)}return(0,r.default)(t,[{key:"palette",value:function(t){return{width:"700px",height:"".concat(1082-t.contentheight,"px"),background:"#ffffff",views:[{type:"image",url:t.recoimg,css:{width:"80px",height:"80px",top:"40px",left:"40px",rotate:"0",borderRadius:"40px",borderWidth:"",borderColor:"#000000",shadow:"",mode:"scaleToFill"}},{type:"text",text:t.usercontent,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"620px",height:"".concat(520.2399999999998-t.contentheight,"px"),top:"161px",left:"40px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"400",maxLines:"12",lineHeight:"46.620000000000005px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"rect",css:{background:"#F7F7F7",width:"".concat(t.marglength,"px"),height:"50px",top:"".concat(757-t.contentheight,"px"),left:"40px",rotate:"0",borderRadius:"6px",shadow:"",color:"#F7F7F7"}},{type:"rect",css:{background:"#F9FAFC",width:"700px",height:"200px",top:"".concat(882-t.contentheight,"px"),left:"0px",rotate:"0",borderRadius:"",shadow:"",color:"#F9FAFC"}},{type:"image",url:"".concat(t.userweixincode),css:{width:"140px",height:"140px",top:"".concat(912-t.contentheight,"px"),left:"40px",rotate:"0",borderRadius:"0px",borderWidth:"",borderColor:"#000000",shadow:"",mode:"scaleToFill"}},{type:"text",text:t.longpressrecog,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"560px",height:"31.639999999999997px",top:"".concat(942-t.contentheight,"px"),left:"210px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"400",maxLines:"1",lineHeight:"31.080000000000002px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.comefrom+t.slitename,css:{color:"#AAAAAA",background:"rgba(0,0,0,0)",width:"450px",height:"27.119999999999997px",top:"".concat(989-t.contentheight,"px"),left:"210px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"1",lineHeight:"26.64px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.reconame,css:{color:"#000000",background:"rgba(0,0,0,0)",width:"500px",height:"31.639999999999997px",top:"41px",left:"140px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"bold",maxLines:"1",lineHeight:"31.080000000000002px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.username+t.stay+t.useratttype+t.published+t.contents,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"535px",height:"27.119999999999997px",top:"88px",left:"140px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"2",lineHeight:"26.64px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.goddessvideo,css:{color:"#777777",background:"rgba(0,0,0,0)",width:"".concat(t.attachlength,"px"),height:"27.119999999999997px",top:"".concat(769-t.contentheight,"px"),left:"60px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"1",lineHeight:"26.64px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"center"}}]}}}]),t}();e.default=s},"8e1e":function(t,e,i){(e=i("24fb")(!1)).push([t.i,'@charset "UTF-8";\n/**\n * 这里是uni-app内置的常用样式变量\n *\n * uni-app 官方扩展插件及插件市场（https://ext.dcloud.net.cn）上很多三方插件均使用了这些样式变量\n * 如果你是插件开发者，建议你使用scss预处理，并在插件代码中直接使用这些变量（无需 import 这个文件），方便用户通过搭积木的方式开发整体风格一致的App\n *\n */\n/**\n * 如果你是App开发者（插件使用者），你可以通过修改这些变量来定制自己的插件主题，实现自定义主题功能\n *\n * 如果你的项目同样使用了scss预处理，你也可以直接在你的 scss 代码中使用如下变量，同时无需 import 这个文件\n */\n/* 颜色变量 */\n/* 行为相关颜色 */\n/* 文字基本颜色 */\n/* 背景颜色 */\n/* 边框颜色 */\n/* 尺寸变量 */\n/* 文字尺寸 */\n/* 图片尺寸 */\n/* Border Radius */\n/* 水平间距 */\n/* 垂直间距 */\n/* 透明度 */\n/* 文章场景相关 */\n/* eg:\n  .container {\n    color: --color(BG-1);\n  }\n*/.quipage[data-v-2d5e2807]{width:100vw;height:100vh}.painter[data-v-2d5e2807]{display:-webkit-box;display:-webkit-flex;display:flex;min-height:100vh;-webkit-box-orient:vertical;-webkit-box-direction:normal;-webkit-flex-direction:column;flex-direction:column;-webkit-box-pack:justify;-webkit-justify-content:space-between;justify-content:space-between;background-color:var(--qui-BG-2)}.canvas-box[data-v-2d5e2807]{margin-bottom:%?155?%}.cent[data-v-2d5e2807]{width:%?700?%;margin:0 auto;background:var(--qui-FC-FFF);border-radius:%?7?%;box-shadow:0 %?3?% %?6?% rgba(0,0,0,.16)}.cent .cent-image[data-v-2d5e2807]{width:100%;height:100%}#front[data-v-2d5e2807]{position:fixed;width:0;height:0}.btn-box[data-v-2d5e2807]{margin:0 auto %?40?%}',""]),t.exports=e},a132:function(t,e,i){"use strict";var o=i("de9b");i.d(e,"a",(function(){return o.a})),i.d(e,"b",(function(){return o.b})),i.d(e,"c",(function(){return o.c}))},a646:function(t,e,i){"use strict";var o=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=o(i("d4ec")),r=o(i("bee2")),s=function(){function t(){(0,n.default)(this,t)}return(0,r.default)(t,[{key:"palette",value:function(t){return{width:"700px",height:"".concat(t.heightdefill+1100,"px"),background:"#ffffff",views:[{type:"image",url:t.recoimg,css:{"min-width":"80px",height:"80px",top:"40px",left:"40px",rotate:"0",borderRadius:"40px",borderWidth:"",borderColor:"#000000",shadow:"",mode:"scaleToFill"}},{type:"text",text:t.reconame,css:{color:"#000000",background:"rgba(0,0,0,0)",width:"500px",height:"40.04px",top:"41px",left:"140px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"bold",maxLines:"1",lineHeight:"40.40400000000001px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.username+t.stay+t.useratttype+t.published+t.contents,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"535px",height:"34.32px",top:"88px",left:"140px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"2",lineHeight:"34.632000000000005px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"rect",css:{background:"#F7F7F7",width:"".concat(t.marglength,"px"),height:"50px",top:"".concat(757+t.heightdefill,"px"),left:"40px",rotate:"0",borderRadius:"6px",shadow:"",color:"#F7F7F7"}},{type:"text",text:t.goddessvideo,css:{color:"#777777",background:"rgba(0,0,0,0)",width:"".concat(t.attachlength,"px"),height:"27.119999999999997px",top:"".concat(769+t.heightdefill,"px"),left:"60px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"1",lineHeight:"26.64px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"center"}},{type:"rect",css:{background:"#F9FAFC",width:"700px",height:"200px",top:"".concat(900+t.heightdefill,"px"),left:"0px",rotate:"0",borderRadius:"",shadow:"",color:"#F9FAFC"}},{type:"image",url:t.userweixincode,css:{"min-width":"140px",height:"140px",top:"".concat(930+t.heightdefill,"px"),left:"40px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",mode:"scaleToFill"}},{type:"text",text:t.longpressrecog,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"560px",height:"31.639999999999997px",top:"".concat(960+t.heightdefill,"px"),left:"210px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"400",maxLines:"1",lineHeight:"31.080000000000002px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.comefrom+t.slitename,css:{color:"#AAAAAA",background:"rgba(0,0,0,0)",width:"450px",height:"27.119999999999997px",top:"".concat(1006+t.heightdefill,"px"),left:"210px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"1",lineHeight:"26.64px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.usertitle,css:{color:"#303133",background:"rgba(0,0,0,0)",width:"650px",height:"33.9px",top:"160px",left:"40px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"30px",fontWeight:"bold",maxLines:"1",lineHeight:"33.300000000000004px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"image",url:t.usercontimg[0],css:{width:"620px",top:"".concat(240-t.imgtop,"px"),left:"40px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",mode:"aspectFill"}},{type:"text",text:t.usercontent,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"616px",height:"81.172px",top:"".concat(672-t.imgtop+t.heightdefill,"px"),left:"40px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"400",maxLines:"2",lineHeight:"40.40400000000001px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}}]}}}]),t}();e.default=s},b469:function(t,e){t.exports={computed:{forums:function(){return this.$store.getters["jv/get"]("forums/1")}}}},c6f1:function(t,e,i){"use strict";i.r(e);var o=i("578d"),n=i.n(o);for(var r in o)["default"].indexOf(r)<0&&function(t){i.d(e,t,(function(){return o[t]}))}(r);e.default=n.a},cf52:function(t,e,i){"use strict";var o=i("4ea4");Object.defineProperty(e,"__esModule",{value:!0}),e.default=void 0;var n=o(i("d4ec")),r=o(i("bee2")),s=function(){function t(){(0,n.default)(this,t)}return(0,r.default)(t,[{key:"palette",value:function(t){return{width:"700px",height:"".concat(1100+t.heightdefill,"px"),background:"#ffffff",views:[{type:"image",url:t.recoimg,css:{width:"80px",height:"80px",top:"40px",left:"40px",rotate:"0",borderRadius:"40px",borderWidth:"",borderColor:"#000000",shadow:"",mode:"scaleToFill"}},{type:"text",text:t.reconame,css:{color:"#000000",background:"rgba(0,0,0,0)",width:"500px",height:"40.04px",top:"41px",left:"140px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"bold",maxLines:"2",lineHeight:"40.40400000000001px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.username+t.stay+t.useratttype+t.published+t.contents,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"535px",height:"34.32px",top:"88px",left:"140px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"2",lineHeight:"34.632000000000005px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"rect",css:{background:"#F7F7F7",width:"".concat(t.marglength,"px"),height:"50px",top:"".concat(773+t.heightdefill,"px"),left:"40px",rotate:"0",borderRadius:"6px",shadow:"",color:"#F7F7F7"}},{type:"text",text:t.goddessvideo,css:{color:"#777777",background:"rgba(0,0,0,0)",width:"".concat(t.attachlength,"px"),height:"27.119999999999997px",top:"".concat(784+t.heightdefill,"px"),left:"60px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"1",lineHeight:"26.64px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"center"}},{type:"rect",css:{background:"#F9FAFC",width:"700px",height:"200px",top:"".concat(900+t.heightdefill,"px"),left:"0px",rotate:"0",borderRadius:"",shadow:"",color:"#F9FAFC"}},{type:"image",url:"".concat(t.userweixincode),css:{width:"140px",height:"140px",top:"".concat(930+t.heightdefill,"px"),left:"40px",rotate:"0",borderRadius:"0px",borderWidth:"",borderColor:"#000000",shadow:"",mode:"scaleToFill"}},{type:"text",text:t.longpressrecog,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"560px",height:"31.639999999999997px",top:"".concat(960+t.heightdefill,"px"),left:"210px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"400",maxLines:"1",lineHeight:"31.080000000000002px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.comefrom+t.slitename,css:{color:"#AAAAAA",background:"rgba(0,0,0,0)",width:"560px",height:"27.119999999999997px",top:"".concat(1006+t.heightdefill,"px"),left:"210px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"24px",fontWeight:"400",maxLines:"1",lineHeight:"26.64px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"text",text:t.usertitle,css:{color:"#303133",background:"rgba(0,0,0,0)",width:"453px",height:"33.9px",top:"160px",left:"40px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"30px",fontWeight:"bold",maxLines:"1",lineHeight:"33.300000000000004px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}},{type:"image",url:t.uservideo,css:{width:"620px",top:"240px",left:"40px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",mode:"scaleToFill"}},{type:"text",text:t.usercontent,css:{color:"#333333",background:"rgba(0,0,0,0)",width:"616px",height:"81.172px",top:"".concat(672+t.heightdefill,"px"),left:"40px",rotate:"0",borderRadius:"",borderWidth:"",borderColor:"#000000",shadow:"",padding:"0px",fontSize:"28px",fontWeight:"400",maxLines:"2",lineHeight:"40.40400000000001px",textStyle:"fill",textDecoration:"none",fontFamily:"",textAlign:"left"}}]}}}]),t}();e.default=s},de9b:function(t,e,i){"use strict";(function(t){var o;i.d(e,"b",(function(){return n})),i.d(e,"c",(function(){return r})),i.d(e,"a",(function(){return o}));try{o={quiPage:i("29c4").default,quiToast:i("2039").default,quiButton:i("8397").default}}catch(e){if(-1===e.message.indexOf("Cannot find module")||-1===e.message.indexOf(".vue"))throw e;t.error(e.message),t.error("1. 排查组件名称拼写是否正确"),t.error("2. 排查组件是否符合 easycom 规范，文档：https://uniapp.dcloud.net.cn/collocation/pages?id=easycom"),t.error("3. 若组件不符合 easycom 规范，需手动引入，并在 components 中注册该组件")}var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("qui-page",{staticClass:"quipage",attrs:{"data-qui-theme":t.theme}},[i("v-uni-view",{staticClass:"painter"},[i("v-uni-view",{staticClass:"canvas-box",style:{paddingTop:t.paddingtop+"rpx"}},[i("v-uni-view",{staticClass:"cent",style:{height:t.constyle+"rpx"}},[i("v-uni-image",{staticClass:"cent-image",attrs:{src:t.imagePath,mode:t.widthFix,"show-menu-by-longpress":!0},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.previewImage.apply(void 0,arguments)}}})],1),i("v-uni-view",{staticClass:"box-img"},[i("painter",{attrs:{"custom-style":"margin-left: 40rpx; height: 0rpx; position:fixed",palette:t.template,"width-pixels":"1040"},on:{imgErr:function(e){arguments[0]=e=t.$handleEvent(e),t.imgErr.apply(void 0,arguments)},imgOK:function(e){arguments[0]=e=t.$handleEvent(e),t.onImgOK.apply(void 0,arguments)}}})],1)],1),i("qui-toast",{ref:"toast"}),i("v-uni-view",{staticClass:"btn-box"},[i("qui-button",{attrs:{type:"primary",size:"large"},on:{click:function(e){arguments[0]=e=t.$handleEvent(e),t.fun.apply(void 0,arguments)}}},[t._v(t._s(t.i18n.t("share.savealbum")))])],1)],1)],1)},r=[]}).call(this,i("5a52").default)},e972:function(t,e,i){t.exports=i.p+"static/img/msg-404.3ba2611f.svg"},f969:function(t,e,i){var o=i("8e1e");"string"==typeof o&&(o=[[t.i,o,""]]),o.locals&&(t.exports=o.locals);(0,i("4f06").default)("8400d5b6",o,!0,{sourceMap:!1,shadowMode:!1})}}]);