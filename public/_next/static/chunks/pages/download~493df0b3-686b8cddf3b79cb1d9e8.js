_N_E=(window.webpackJsonp_N_E=window.webpackJsonp_N_E||[]).push([[17],{"/Nde":function(e,t,r){"use strict";r.d(t,"a",(function(){return f})),r.d(t,"b",(function(){return p})),r.d(t,"c",(function(){return b})),r.d(t,"d",(function(){return v})),r.d(t,"e",(function(){return h}));var n=r("lSNA"),a=r.n(n),i=(r("o0o1"),r("ls82"),r("yXPU"),r("+3IH")),s=r("dJ22");function o(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function c(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?o(Object(r),!0).forEach((function(t){a()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):o(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var u={Code:"common_0001",Message:"\u9700\u8981\u8865\u5145\u6635\u79f0"},d={Code:"common_0002",Message:"\u9700\u8981\u8865\u5145\u9644\u52a0\u4fe1\u606f"},l={Code:"common_0003",Message:"\u9700\u8981\u8865\u5145\u6635\u79f0\u548c\u9644\u52a0\u4fe1\u606f"},f=-4009,p=2,b=-4007,h=function(e){var t=e.webConfig.setSite;return"1"===(t=void 0===t?{}:t).openExtFields},v=function(e){if(function(e){var t=Object(i.a)(e,"data.isMissNickname",!1),r=10===Object(i.a)(e,"data.userStatus"),n=Object(i.a)(e,"data.accessToken","");if(-8e3!==e.code){var a=Object(i.a)(e,"data.uid","");if(Object(s.a)({accessToken:n}),r&&t)throw c({uid:a},l);if(t)throw c({uid:a},u);if(r)throw c({uid:a},d)}}(e),0===e.code||e.code===f||e.code===b){var t=e.code,r=Object(i.a)(e,"data.userStatus",0),n=Object(i.a)(e,"data.uid","");if(0===t&&r===p){var a=Object(i.a)(e,"data.accessToken","");Object(s.a)({accessToken:a}),t=r}if(t)throw{Code:t,Message:Object(i.a)(e,"data.rejectReason",""),uid:n}}}},"3QY5":function(e,t,r){"use strict";r.d(t,"a",(function(){return i}));var n=r("X6IV"),a=r("m5TM"),i=function(e,t){var r=!(arguments.length>2&&void 0!==arguments[2])||arguments[2],i=n.a.get(a.c.ACCESS_TOKEN_NAME),s=new XMLHttpRequest;s.open("GET",e,!0),s.setRequestHeader("authorization","Bearer ".concat(i)),s.responseType="blob",s.send(),s.onload=function(){if(200===s.status&&r){var e=s.response,n=new FileReader;n.readAsDataURL(e),n.onload=function(e){var r=document.createElement("a");r.download=t,r.href=e.target.result,document.body.appendChild(r),r.click(),document.body.removeChild(r)}}}}},"7zbd":function(e,t,r){"use strict";r.r(t);r("ma9I"),r("qePV"),r("rB9j"),r("EnZy");var n,a=r("nKUr"),i=r("o0o1"),s=r.n(i),o=(r("ls82"),r("yXPU")),c=r.n(o),u=r("lwsE"),d=r.n(u),l=r("W8MJ"),f=r.n(l),p=r("7W2i"),b=r.n(p),h=r("a1gu"),v=r.n(h),m=r("Nsbk"),g=r.n(m),w=r("q1tI"),O=r.n(w),j=r("kMSe"),y=r("wdT/"),P=r.n(y),k=r("RiVy"),x=r("B5JU"),S=r.n(x),C=r("sho3"),D=r("IvHc"),I=r("QcND"),E=r("3QY5");function N(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=g()(e);if(t){var a=g()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return v()(this,r)}}var R=Object(j.c)("user")(n=Object(j.d)(n=function(e){b()(r,e);var t=N(r);function r(e){var n;return d()(this,r),(n=t.call(this,e)).state={},n}return f()(r,[{key:"componentDidMount",value:function(){var e=c()(s.a.mark((function e(){var t,r,n,a,i,o;return s.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.props.router.query.url){e.next=3;break}return e.abrupt("return");case 3:if(this.props.user.isLogin()){e.next=7;break}return P.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),Object(D.a)({url:"/user/login"}),e.abrupt("return");case 7:if(!Object(k.a)()){e.next=11;break}return P.a.info({content:"\u6682\u4e0d\u652f\u6301\u5fae\u4fe1\u5185\u4e0b\u8f7d"}),S.a.redirect({url:"/"}),e.abrupt("return");case 11:return t=this.props.router.asPath,r=decodeURI(t).split("?"),n="".concat(r[1].split("=")[1],"?").concat(r[2].split("&")[0],"&").concat(r[2].split("&")[1]),a=r[2].split("&"),i=a[2].split("=")[1],o={sign:a[0].split("=")[1],attachmentsId:Number(a[1].split("=")[1]),isCode:1},e.next=19,this.downloadAttachmentStatus(o);case 19:e.sent&&Object(E.a)(n,i);case 21:case"end":return e.stop()}}),e,this)})));return function(){return e.apply(this,arguments)}}()},{key:"downloadAttachmentStatus",value:function(){var e=c()(s.a.mark((function e(t){var r;return s.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return e.next=2,Object(C.readDownloadAttachmentStatus)(t);case 2:if(0!==(null===(r=e.sent)||void 0===r?void 0:r.code)){e.next=6;break}return S.a.redirect({url:"/"}),e.abrupt("return",!0);case 6:return-7083===(null===r||void 0===r?void 0:r.code)&&(P.a.info({content:null===r||void 0===r?void 0:r.msg}),S.a.redirect({url:"/"})),-7082===(null===r||void 0===r?void 0:r.code)&&(P.a.info({content:null===r||void 0===r?void 0:r.msg}),S.a.redirect({url:"/"})),-4004===(null===r||void 0===r?void 0:r.code)&&P.a.info({content:null===r||void 0===r?void 0:r.msg}),e.abrupt("return",!1);case 10:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}()},{key:"render",value:function(){return Object(a.jsx)("div",{})}}]),r}(O.a.Component))||n)||n;t.default=Object(I.a)(R)},IvHc:function(e,t,r){"use strict";var n=r("lN2P");t.a=n.a.saveAndLogin},JTJg:function(e,t,r){"use strict";var n=r("I+eb"),a=r("WjRb"),i=r("HYAF");n({target:"String",proto:!0,forced:!r("qxPZ")("includes")},{includes:function(e){return!!~String(i(this)).indexOf(a(e),arguments.length>1?arguments[1]:void 0)}})},QcND:function(e,t,r){"use strict";r.d(t,"a",(function(){return te}));r("ma9I"),r("yq1k"),r("4mDm"),r("Rfxz"),r("07d7"),r("rB9j"),r("JTJg"),r("PKPk"),r("Rm1S"),r("UxlC"),r("3bBZ"),r("Kz25");var n=r("nKUr"),a=r("o0o1"),i=r.n(a),s=r("lSNA"),o=r.n(s),c=(r("ls82"),r("yXPU")),u=r.n(c),d=r("lwsE"),l=r.n(d),f=r("PJYZ"),p=r.n(f),b=r("W8MJ"),h=r.n(b),v=r("7W2i"),m=r.n(v),g=r("a1gu"),w=r.n(g),O=r("Nsbk"),j=r.n(O),y=r("q1tI"),P=r.n(y),k=r("kMSe"),x=r("n4oF"),S=r("zDaA"),C=r("sho3"),D=r("B5JU"),I=r.n(D),E=r("20a2"),N=r("bK+J"),R=r("/Nde"),U=r("MCNy"),A=r.n(U),M=r("pGE/"),T=r("Tk/S"),W="rKwiNl2JOBH1dcaoggUsN",q="_3_kaucwAnhguPyKqHJUwsL",_=(r("5s+n"),r("RIqP")),L=r.n(_),J=r("rGXy"),B=r("lY1M");function X(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function z(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?X(Object(r),!0).forEach((function(t){o()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):X(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var F=["closeWindow","chooseImage","uploadImage","getLocalImgData","updateAppMessageShareData","updateTimelineShareData","getNetworkType"],H=!1;function Q(){return K.apply(this,arguments)}function K(){return(K=u()(i.a.mark((function e(){var t,r,n,a,s,o,c=arguments;return i.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(t=c.length>0&&void 0!==c[0]&&c[0],r=c.length>1&&void 0!==c[1]?c[1]:[],B.b.env("weixin")){e.next=4;break}return e.abrupt("return");case 4:return n=[],window.wx&&wx.config||(a=new Promise((function(e){var t=document.createElement("script");t.src="https://res.wx.qq.com/open/js/jweixin-1.6.0.js",t.onload=function(){return e()},document.body.appendChild(t)})),n.push(a)),e.next=8,Promise.all(n);case 8:if(t&&!H){e.next=10;break}return e.abrupt("return");case 10:return e.next=12,Object(J.getWXConfig)({params:{url:encodeURIComponent(window.location.href)}});case 12:if(0!==(s=e.sent).code||!s.data||!s.data.appId){e.next=21;break}return i=s.data,o={appId:i.appId,timestamp:i.timestamp,nonceStr:i.nonceStr,signature:i.signature},wx&&wx.config(z(z({debug:!1},o),{},{jsApiList:[].concat(F,L()(r))})),wx&&(wx.hasDoneConfig=!0),H=!0,e.abrupt("return",!0);case 21:console.error("\u521d\u59cb\u5316\u5fae\u4fe1jssdk\u5931\u8d25\uff01",s);case 22:return e.abrupt("return",!1);case 23:case"end":return e.stop()}var i}),e)})))).apply(this,arguments)}var Y=r("lN2P");var G=r("yO9+"),V=10;function Z(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function $(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?Z(Object(r),!0).forEach((function(t){o()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):Z(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function ee(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=j()(e);if(t){var a=j()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return w()(this,r)}}function te(e,t){var r,a=Object(k.c)("site")(r=Object(k.c)("user")(r=Object(k.c)("emotion")(r=Object(k.c)("commonLogin")(r=Object(k.d)(r=function(r){m()(s,r);var a=ee(s);function s(e){var t,r;l()(this,s),(t=a.call(this,e)).handleWxShare=t.handleWxShare.bind(p()(t)),t.canPublish=t.canPublish.bind(p()(t));var n=e.serverUser,i=e.serverSite,o=e.serverEmotion,c=e.user,u=e.site,d=e.emotion;return i&&i.platform&&u.setPlatform(i.platform),i&&i.closeSite&&u.setCloseSiteConfig(i.closeSite),i&&i.webConfig&&u.setSiteConfig(i.webConfig),n&&n.userInfo&&c.setUserInfo(n.userInfo),n&&n.userPermissions&&c.setUserPermissions(n.userPermissions),n&&n.userPermissions&&c.setUserPermissions(n.userPermissions),o&&o.emojis&&d.setEmoji(o.emojis),r=Object(x.a)()?!i:!(u&&u.webConfig),t.state={isNoSiteData:r,isPass:!1},t}return h()(s,null,[{key:"getInitialProps",value:function(){var t=u()(i.a.mark((function t(r){var n,a,s,o,c,u,d,l,f,p,b;return i.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(t.prev=0,n="static",a={},c={},!Object(x.a)()){t.next=25;break}return b=r.req.headers,n=b&&!M.a.isEmptyObject(b)?Object(S.a)(b["user-agent"]):"static",Object(C.readEmoji)({},r),t.next=10,Object(C.readForum)({},r);case 10:if(a=t.sent,o={platform:n,closeSite:-3005===a.code?a.data:null,webConfig:a&&a.data||null},!a||0!==a.code||null===(l=a)||void 0===l||null===(f=l.data)||void 0===f||null===(p=f.user)||void 0===p||!p.userId){t.next=21;break}return t.next=15,Object(C.readUser)({params:{pid:a.data.user.userId}},r);case 15:return s=t.sent,t.next=18,Object(C.readPermissions)({},r);case 18:d=t.sent,u=s&&0===s.code?s.data:null,d=d&&0===d.code?d.data:null;case 21:if(!a||0!==a.code||!e.getInitialProps){t.next=25;break}return t.next=24,e.getInitialProps(r,{user:u,site:o});case 24:c=t.sent;case 25:return t.abrupt("return",$($({},c),{},{serverSite:o,serverUser:{userInfo:u,userPermissions:d}}));case 28:return t.prev=28,t.t0=t.catch(0),console.log("err",t.t0),t.abrupt("return",{serverSite:{},serverUser:{}});case 32:case"end":return t.stop()}}),t,null,[[0,28]])})));return function(e){return t.apply(this,arguments)}}()}]),h()(s,[{key:"componentDidMount",value:function(){var e=u()(i.a.mark((function e(){var r,n,a,s,o,c,u,d,l,f,p,b,h,v;return i.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(n=this.state.isNoSiteData,a=this.props,s=a.serverUser,o=a.serverSite,c=a.user,u=a.site,d=a.emotion,f=!1,null!==(r=d.emojis)&&void 0!==r&&r.length||d.fetchEmoji(),u.setPlatform(Object(S.a)(window.navigator.userAgent)),!n){e.next=16;break}if(l=(null===o||void 0===o?void 0:o.webConfig)||null){e.next=14;break}return e.next=10,Object(C.readForum)({});case 10:(p=e.sent).data&&u.setSiteConfig(p.data),this.setAppCommonStatus(p),l=p.data||null;case 14:e.next=17;break;case 16:l=u?u.webConfig:null;case 17:if(u.initUserLoginEntryStatus(),!l||!l.user){e.next=34;break}if(c&&c.userInfo||s&&s.userInfo){e.next=31;break}return e.next=22,Object(C.readUser)({params:{pid:l.user.userId}});case 22:return b=e.sent,e.next=25,Object(C.readPermissions)({});case 25:0===(h=e.sent).code&&h.data&&c.setUserPermissions(h.data),b.data&&c.setUserInfo(b.data),f=!!b.data,e.next=32;break;case 31:f=!0;case 32:e.next=35;break;case 34:f=!1;case 35:return c.updateLoginStatus(f),v=this.isPass(),t&&v&&(v=t(v)),this.setState({isPass:v}),e.next=41,Q(l&&l.passport&&l.passport.offiaccountOpen);case 41:e.sent&&(this.handleWxShare(this.props.router.asPath),this.props.router.events.on("routeChangeComplete",this.handleWxShare));case 43:case"end":return e.stop()}}),e,this)})));return function(){return e.apply(this,arguments)}}()},{key:"handleWxShare",value:function(e){if(window.wx&&window.wx.hasDoneConfig){var t=this.props,r=t.user,n=t.site,a=r.userInfo,i=n.webConfig.setSite,s=i.siteName,o=i.siteIntroduction,c=i.siteHeaderLogo,u=i.siteFavicon,d=a||{},l=d.nickname,f=d.avatarUrl,p=d.signature,b=d.id,h=document.title,v=o?o.length>35?"".concat(o.substr(0,35),"..."):o:"\u5728\u8fd9\u91cc\uff0c\u53d1\u73b0\u66f4\u591a\u7cbe\u5f69\u5185\u5bb9",m=window.location.href,g=c||u;e.match(/\/thread\/\d+/)||e.match(/\/user\/\d+/)||("/my"===e&&l&&(h="".concat(l,"\u7684\u4e3b\u9875"),g=f,v=p?p.length>35?"".concat(p.substr(0,35),"..."):p:"\u5728\u8fd9\u91cc\uff0c\u53d1\u73b0\u66f4\u591a\u7cbe\u5f69\u5185\u5bb9",m="".concat(window.location.origin,"/user/").concat(b)),(e.includes("/forum/partner-invite")||e.match(/\/user\/(username|wx|phone)-login/)||e.includes("/user/register"))&&(h="\u9080\u8bf7\u60a8\u52a0\u5165".concat(s)),(e.includes("/invite")||"/"===e)&&(h="".concat(l,"\u9080\u8bf7\u60a8\u52a0\u5165").concat(s)),e.includes("/message?page=chat")&&(h="\u6211\u7684\u79c1\u4fe1 - ".concat(s),m="".concat(window.location.origin,"/message")),Object(T.a)(h,v,m,g))}}},{key:"setAppCommonStatus",value:function(e){var t=this.props.site;switch([G.k,G.n,G.h,G.m,G.i,G.o,G.l].includes(e.code)&&Y.a.saveCurrentUrl(),e.code){case 0:break;case G.v:t.setCloseSiteConfig(e.data),I.a.redirect({url:"/close"});break;case G.f:break;case G.x:Object(N.a)(),window.location.reload();break;case G.g:I.a.redirect({url:"/404"});break;case G.k:Object(N.a)(),Y.a.gotoLogin();break;case G.n:Object(N.a)(),Y.a.saveAndRedirect("/user/register");break;case G.h:I.a.push({url:"/user/status?statusCode=2"});break;case G.m:I.a.push({url:"/user/status?statusCode=-4007"});break;case G.i:I.a.push({url:"/user/status?statusCode=-4009"});break;case G.o:Y.a.saveAndRedirect("/user/supplementary");break;case G.j:I.a.redirect({url:"/"});break;case G.l:Y.a.saveAndRedirect("/forum/partner-invite");break;default:t.setErrPageType("site"),I.a.redirect({url:"/500"})}}},{key:"checkJump",value:function(){var e=this.props.router,t=Y.a.getUrl();if(t)if(new URL(t).pathname===e.asPath)Y.a.clear();else if("/"===e.asPath)return Y.a.restore(),!1;return!0}},{key:"isPass",value:function(){var e=this.props,t=e.site,r=e.router,n=e.user,a=e.commonLogin,i=this.state.isNoSiteData;if(t&&t.webConfig){var s,o;if(i&&this.setState({isNoSiteData:!1}),"/close"!==r.asPath&&t.closeSiteConfig)return I.a.redirect({url:"/close"}),!1;if(n.isLogin()){if(!t.isOffiaccountOpen&&!t.isMiniProgramOpen&&"/user/bind-phone"!==r.asPath&&t.isSmsOpen&&!n.mobile)return Y.a.saveAndRedirect("/user/bind-phone"),!1;if("/user/bind-nickname"!==r.asPath&&!n.nickname)return a.needToCompleteExtraInfo=n.userStatus===V,Y.a.saveAndRedirect("/user/bind-nickname"),!1;if(n.userStatus===R.b&&!G.u.includes(r.pathname))return I.a.replace({url:"/user/status?statusCode=".concat(n.userStatus)}),!1}if("pay"===(null===t||void 0===t||null===(s=t.webConfig)||void 0===s||null===(o=s.setSite)||void 0===o?void 0:o.siteMode)){if(G.z.some((function(e){return r.asPath.match(e)})))return this.checkJump(),!0;var c=r.query.inviteCode,u=c?"?inviteCode=".concat(c):"";if(null===n||void 0===n||!n.paid)return Y.a.saveAndRedirect("/forum/partner-invite".concat(u)),!1}}return this.checkJump()}},{key:"filterProps",value:function(e){var t=$({},e);return delete t.serverUser,delete t.serverSite,delete t.user,delete t.site,t}},{key:"canPublish",value:function(){var e=this.props;return function(e,t){if(!e.isLogin())return Y.a.gotoLogin(),!1;if(!t.publishNeedBindPhone&&!t.publishNeedBindWechat)return!0;var r="bind".concat(t.publishNeedBindPhone&&!e.mobile?"Mobile":"").concat(t.publishNeedBindWechat&&!e.isBindWechat?"Wechat":""),n="";switch("".concat(t.isSmsOpen?"mobile":"").concat("none"!==t.wechatEnv?"wechat":"")){case"mobile":n=e.mobile?"":"/user/bind-phone";break;case"wechat":n=e.isBindWechat?"":"/user/wx-bind-qrcode";break;case"mobilewechat":switch(r){case"bindMobile":n=e.mobile?"":"/user/bind-phone";break;case"bindWechat":n=e.isBindWechat?"":"/user/wx-bind-qrcode";break;case"bindMobileWechat":n=e.isBindWechat||e.mobile?"":"/user/wx-bind-qrcode?bindPhone=1"}}return n&&Y.a.saveAndPush(n),!n}(e.user,e.site)}},{key:"render",value:function(){var t=this.state,r=t.isNoSiteData,a=t.isPass;return"static"===this.props.site.platform?null:r||!a?Object(n.jsx)("div",{className:W,children:Object(n.jsx)(A.a,{className:q,name:"LoadingOutlined",size:"large"})}):Object(n.jsx)(e,$({canPublish:this.canPublish},this.filterProps(this.props)))}}]),s}(P.a.Component))||r)||r)||r)||r)||r;return Object(E.withRouter)(a)}},RiVy:function(e,t,r){"use strict";function n(){if("undefined"===typeof window)return!1;var e=window.navigator.userAgent.toLowerCase();return/micromessenger/.test(e)}r.d(t,"a",(function(){return n}))},"Tk/S":function(e,t,r){"use strict";r.d(t,"a",(function(){return a}));var n=r("lY1M");function a(e,t,r,a){n.b.env("weixin")&&window.wx&&window.wx.ready((function(){var n={title:e||"Discuz! Q",desc:t&&""!=t?t:e||"Discuz! Q",link:r||window.location.href,imgUrl:a};wx.updateAppMessageShareData(n),wx.updateTimelineShareData(n)}))}},"o6+f":function(e,t,r){(window.__NEXT_P=window.__NEXT_P||[]).push(["/download",function(){return r("7zbd")}])},rGXy:function(e,t,r){"use strict";var n=r("TqRt");Object.defineProperty(t,"__esModule",{value:!0}),t.getWXConfig=function(){return p.apply(this,arguments)};var a=n(r("o0o1")),i=n(r("lSNA")),s=n(r("QILm"));r("ls82");var o=n(r("yXPU")),c=r("0QFe"),u=n(r("m4Ii"));function d(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function l(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?d(Object(r),!0).forEach((function(t){(0,i.default)(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):d(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var f={messageText:{type:"string",required:!0},recipientUsername:{type:"string",required:!0},imageUrl:{type:"string"},attachmentId:{type:"number"}};function p(){return(p=(0,o.default)(a.default.mark((function e(){var t,r,n,i,o,d,p,b,h=arguments;return a.default.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t=h.length>0&&void 0!==h[0]?h[0]:{},e.prev=1,r=t.params,n=void 0===r?{}:r,i=t.data,o=void 0===i?{}:i,d=(0,s.default)(t,["params","data"]),p=l(l({url:"/apiv3/offiaccount/jssdk",method:"GET",params:n,data:o},d),{},{validateRules:f}),e.next=6,u.default.dispatcher(p);case 6:return b=e.sent,e.abrupt("return",b);case 10:return e.prev=10,e.t0=e.catch(1),e.abrupt("return",(0,c.handleError)(e.t0));case 13:case"end":return e.stop()}}),e,null,[[1,10]])})))).apply(this,arguments)}},yq1k:function(e,t,r){"use strict";var n=r("I+eb"),a=r("TWQb").includes,i=r("RNIs");n({target:"Array",proto:!0,forced:!r("rkAj")("indexOf",{ACCESSORS:!0,1:0})},{includes:function(e){return a(this,e,arguments.length>1?arguments[1]:void 0)}}),i("includes")},zDaA:function(e,t,r){"use strict";r.d(t,"a",(function(){return a}));var n=r("n4oF");function a(e){var t=/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(e)?"h5":"pc";if(!Object(n.a)()){var r=90===Math.abs(window.orientation),a=window.innerWidth;"pc"===t&&a<800&&(t="h5"),"h5"===t&&!r&&a>=800&&(t="pc")}return t}}},[["o6+f",1,0,3,4,2]]]);