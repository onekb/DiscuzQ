_N_E=(window.webpackJsonp_N_E=window.webpackJsonp_N_E||[]).push([[45],{"/Nde":function(e,t,r){"use strict";r.d(t,"a",(function(){return p})),r.d(t,"b",(function(){return d})),r.d(t,"c",(function(){return b})),r.d(t,"d",(function(){return v})),r.d(t,"e",(function(){return h}));var n=r("lSNA"),a=r.n(n),s=(r("o0o1"),r("ls82"),r("yXPU"),r("+3IH")),i=r("dJ22");function o(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function c(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?o(Object(r),!0).forEach((function(t){a()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):o(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var u={Code:"common_0001",Message:"\u9700\u8981\u8865\u5145\u6635\u79f0"},l={Code:"common_0002",Message:"\u9700\u8981\u8865\u5145\u9644\u52a0\u4fe1\u606f"},f={Code:"common_0003",Message:"\u9700\u8981\u8865\u5145\u6635\u79f0\u548c\u9644\u52a0\u4fe1\u606f"},p=-4009,d=2,b=-4007,h=function(e){var t=e.webConfig.setSite;return"1"===(t=void 0===t?{}:t).openExtFields},v=function(e){if(function(e){var t=Object(s.a)(e,"data.isMissNickname",!1),r=10===Object(s.a)(e,"data.userStatus"),n=Object(s.a)(e,"data.accessToken","");if(-8e3!==e.code){var a=Object(s.a)(e,"data.uid","");if(Object(i.a)({accessToken:n}),r&&t)throw c({uid:a},f);if(t)throw c({uid:a},u);if(r)throw c({uid:a},l)}}(e),0===e.code||e.code===p||e.code===b){var t=e.code,r=Object(s.a)(e,"data.userStatus",0),n=Object(s.a)(e,"data.uid","");if(0===t&&r===d){var a=Object(s.a)(e,"data.accessToken","");Object(i.a)({accessToken:a}),t=r}if(t)throw{Code:t,Message:Object(s.a)(e,"data.rejectReason",""),uid:n}}}},JTJg:function(e,t,r){"use strict";var n=r("I+eb"),a=r("WjRb"),s=r("HYAF");n({target:"String",proto:!0,forced:!r("qxPZ")("includes")},{includes:function(e){return!!~String(s(this)).indexOf(a(e),arguments.length>1?arguments[1]:void 0)}})},QcND:function(e,t,r){"use strict";r.d(t,"a",(function(){return ee}));r("ma9I"),r("yq1k"),r("4mDm"),r("Rfxz"),r("07d7"),r("rB9j"),r("JTJg"),r("PKPk"),r("Rm1S"),r("UxlC"),r("3bBZ"),r("Kz25");var n=r("nKUr"),a=r("o0o1"),s=r.n(a),i=r("lSNA"),o=r.n(i),c=(r("ls82"),r("yXPU")),u=r.n(c),l=r("lwsE"),f=r.n(l),p=r("PJYZ"),d=r.n(p),b=r("W8MJ"),h=r.n(b),v=r("7W2i"),g=r.n(v),m=r("a1gu"),O=r.n(m),w=r("Nsbk"),y=r.n(w),j=r("q1tI"),P=r.n(j),k=r("kMSe"),S=r("n4oF"),x=r("zDaA"),D=r("sho3"),C=r("B5JU"),I=r.n(C),U=r("20a2"),E=r("bK+J"),N=r("/Nde"),R=r("MCNy"),A=r.n(R),M=r("pGE/"),W=r("Tk/S"),J="rKwiNl2JOBH1dcaoggUsN",_="_3_kaucwAnhguPyKqHJUwsL",T=(r("5s+n"),r("RIqP")),q=r.n(T),L=r("rGXy"),B=r("lY1M");function z(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function F(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?z(Object(r),!0).forEach((function(t){o()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):z(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var X=["closeWindow","chooseImage","uploadImage","getLocalImgData","updateAppMessageShareData","updateTimelineShareData","getNetworkType"],K=!1;function Q(){return H.apply(this,arguments)}function H(){return(H=u()(s.a.mark((function e(){var t,r,n,a,i,o,c=arguments;return s.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(t=c.length>0&&void 0!==c[0]&&c[0],r=c.length>1&&void 0!==c[1]?c[1]:[],B.b.env("weixin")){e.next=4;break}return e.abrupt("return");case 4:return n=[],window.wx&&wx.config||(a=new Promise((function(e){var t=document.createElement("script");t.src="https://res.wx.qq.com/open/js/jweixin-1.6.0.js",t.onload=function(){return e()},document.body.appendChild(t)})),n.push(a)),e.next=8,Promise.all(n);case 8:if(t&&!K){e.next=10;break}return e.abrupt("return");case 10:return e.next=12,Object(L.getWXConfig)({params:{url:encodeURIComponent(window.location.href)}});case 12:if(0!==(i=e.sent).code||!i.data||!i.data.appId){e.next=21;break}return s=i.data,o={appId:s.appId,timestamp:s.timestamp,nonceStr:s.nonceStr,signature:s.signature},wx&&wx.config(F(F({debug:!1},o),{},{jsApiList:[].concat(X,q()(r))})),wx&&(wx.hasDoneConfig=!0),K=!0,e.abrupt("return",!0);case 21:console.error("\u521d\u59cb\u5316\u5fae\u4fe1jssdk\u5931\u8d25\uff01",i);case 22:return e.abrupt("return",!1);case 23:case"end":return e.stop()}var s}),e)})))).apply(this,arguments)}var Z=r("lN2P");var G=r("yO9+");function Y(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function V(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?Y(Object(r),!0).forEach((function(t){o()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):Y(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function $(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=y()(e);if(t){var a=y()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return O()(this,r)}}function ee(e,t){var r,a=Object(k.c)("site")(r=Object(k.c)("user")(r=Object(k.c)("emotion")(r=Object(k.d)(r=function(r){g()(i,r);var a=$(i);function i(e){var t,r;f()(this,i),(t=a.call(this,e)).handleWxShare=t.handleWxShare.bind(d()(t)),t.canPublish=t.canPublish.bind(d()(t));var n=e.serverUser,s=e.serverSite,o=e.serverEmotion,c=e.user,u=e.site,l=e.emotion;return s&&s.platform&&u.setPlatform(s.platform),s&&s.closeSite&&u.setCloseSiteConfig(s.closeSite),s&&s.webConfig&&u.setSiteConfig(s.webConfig),n&&n.userInfo&&c.setUserInfo(n.userInfo),n&&n.userPermissions&&c.setUserPermissions(n.userPermissions),n&&n.userPermissions&&c.setUserPermissions(n.userPermissions),o&&o.emojis&&l.setEmoji(o.emojis),r=Object(S.a)()?!s:!(u&&u.webConfig),t.state={isNoSiteData:r,isPass:!1},t}return h()(i,null,[{key:"getInitialProps",value:function(){var t=u()(s.a.mark((function t(r){var n,a,i,o,c,u,l,f,p,d,b;return s.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(t.prev=0,n="static",a={},c={},!Object(S.a)()){t.next=25;break}return b=r.req.headers,n=b&&!M.a.isEmptyObject(b)?Object(x.a)(b["user-agent"]):"static",Object(D.readEmoji)({},r),t.next=10,Object(D.readForum)({},r);case 10:if(a=t.sent,o={platform:n,closeSite:-3005===a.code?a.data:null,webConfig:a&&a.data||null},!a||0!==a.code||null===(f=a)||void 0===f||null===(p=f.data)||void 0===p||null===(d=p.user)||void 0===d||!d.userId){t.next=21;break}return t.next=15,Object(D.readUser)({params:{pid:a.data.user.userId}},r);case 15:return i=t.sent,t.next=18,Object(D.readPermissions)({},r);case 18:l=t.sent,u=i&&0===i.code?i.data:null,l=l&&0===l.code?l.data:null;case 21:if(!a||0!==a.code||!e.getInitialProps){t.next=25;break}return t.next=24,e.getInitialProps(r,{user:u,site:o});case 24:c=t.sent;case 25:return t.abrupt("return",V(V({},c),{},{serverSite:o,serverUser:{userInfo:u,userPermissions:l}}));case 28:return t.prev=28,t.t0=t.catch(0),console.log("err",t.t0),t.abrupt("return",{serverSite:{},serverUser:{}});case 32:case"end":return t.stop()}}),t,null,[[0,28]])})));return function(e){return t.apply(this,arguments)}}()}]),h()(i,[{key:"componentDidMount",value:function(){var e=u()(s.a.mark((function e(){var r,n,a,i,o,c,u,l,f,p,d,b,h,v;return s.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(n=this.state.isNoSiteData,a=this.props,i=a.serverUser,o=a.serverSite,c=a.user,u=a.site,l=a.emotion,p=!1,null!==(r=l.emojis)&&void 0!==r&&r.length||l.fetchEmoji(),u.setPlatform(Object(x.a)(window.navigator.userAgent)),!n){e.next=16;break}if(f=(null===o||void 0===o?void 0:o.webConfig)||null){e.next=14;break}return e.next=10,Object(D.readForum)({});case 10:(d=e.sent).data&&u.setSiteConfig(d.data),this.setAppCommonStatus(d),f=d.data||null;case 14:e.next=17;break;case 16:f=u?u.webConfig:null;case 17:if(u.initUserLoginEntryStatus(),!f||!f.user){e.next=34;break}if(c&&c.userInfo||i&&i.userInfo){e.next=31;break}return e.next=22,Object(D.readUser)({params:{pid:f.user.userId}});case 22:return b=e.sent,e.next=25,Object(D.readPermissions)({});case 25:0===(h=e.sent).code&&h.data&&c.setUserPermissions(h.data),b.data&&c.setUserInfo(b.data),p=!!b.data,e.next=32;break;case 31:p=!0;case 32:e.next=35;break;case 34:p=!1;case 35:return c.updateLoginStatus(p),v=this.isPass(),t&&v&&(v=t(v)),this.setState({isPass:v}),e.next=41,Q(f&&f.passport&&f.passport.offiaccountOpen);case 41:e.sent&&(this.handleWxShare(this.props.router.asPath),this.props.router.events.on("routeChangeComplete",this.handleWxShare));case 43:case"end":return e.stop()}}),e,this)})));return function(){return e.apply(this,arguments)}}()},{key:"handleWxShare",value:function(e){if(window.wx&&window.wx.hasDoneConfig){var t=this.props,r=t.user,n=t.site,a=r.userInfo,s=n.webConfig.setSite,i=s.siteName,o=s.siteIntroduction,c=s.siteHeaderLogo,u=s.siteFavicon,l=a||{},f=l.nickname,p=l.avatarUrl,d=l.signature,b=l.id,h=document.title,v=o?o.length>35?"".concat(o.substr(0,35),"..."):o:"\u5728\u8fd9\u91cc\uff0c\u53d1\u73b0\u66f4\u591a\u7cbe\u5f69\u5185\u5bb9",g=window.location.href,m=c||u;e.match(/\/thread\/\d+/)||e.match(/\/user\/\d+/)||("/my"===e&&f&&(h="".concat(f,"\u7684\u4e3b\u9875"),m=p,v=d?d.length>35?"".concat(d.substr(0,35),"..."):d:"\u5728\u8fd9\u91cc\uff0c\u53d1\u73b0\u66f4\u591a\u7cbe\u5f69\u5185\u5bb9",g="".concat(window.location.origin,"/user/").concat(b)),(e.includes("/forum/partner-invite")||e.match(/\/user\/(username|wx|phone)-login/)||e.includes("/user/register"))&&(h="\u9080\u8bf7\u60a8\u52a0\u5165".concat(i)),(e.includes("/invite")||"/"===e)&&(h="".concat(f,"\u9080\u8bf7\u60a8\u52a0\u5165").concat(i)),e.includes("/message?page=chat")&&(h="\u6211\u7684\u79c1\u4fe1 - ".concat(i),g="".concat(window.location.origin,"/message")),Object(W.a)(h,v,g,m))}}},{key:"setAppCommonStatus",value:function(e){var t=this.props.site;switch([G.k,G.n,G.h,G.m,G.i,G.o,G.l].includes(e.code)&&Z.a.saveCurrentUrl(),e.code){case 0:break;case G.v:t.setCloseSiteConfig(e.data),I.a.redirect({url:"/close"});break;case G.f:break;case G.x:Object(E.a)(),window.location.reload();break;case G.g:I.a.redirect({url:"/404"});break;case G.k:Object(E.a)(),Z.a.gotoLogin();break;case G.n:Object(E.a)(),Z.a.saveAndRedirect("/user/register");break;case G.h:I.a.push({url:"/user/status?statusCode=2"});break;case G.m:I.a.push({url:"/user/status?statusCode=-4007"});break;case G.i:I.a.push({url:"/user/status?statusCode=-4009"});break;case G.o:Z.a.saveAndRedirect("/user/supplementary");break;case G.j:I.a.redirect({url:"/"});break;case G.l:Z.a.saveAndRedirect("/forum/partner-invite");break;default:t.setErrPageType("site"),I.a.redirect({url:"/500"})}}},{key:"checkJump",value:function(){var e=this.props.router,t=Z.a.getUrl();if(t)if(new URL(t).pathname===e.asPath)Z.a.clear();else if("/"===e.asPath)return Z.a.restore(),!1;return!0}},{key:"isPass",value:function(){var e=this.props,t=e.site,r=e.router,n=e.user,a=this.state.isNoSiteData;if(t&&t.webConfig){var s,i;if(a&&this.setState({isNoSiteData:!1}),"/close"!==r.asPath&&t.closeSiteConfig)return I.a.redirect({url:"/close"}),!1;if(n.isLogin()){if(!t.isOffiaccountOpen&&!t.isMiniProgramOpen&&"/user/bind-phone"!==r.asPath&&t.isSmsOpen&&!n.mobile)return Z.a.saveAndRedirect("/user/bind-phone"),!1;if("/user/bind-nickname"!==r.asPath&&!n.nickname)return Z.a.saveAndRedirect("/user/bind-nickname"),!1;if(n.userStatus===N.b&&!G.u.includes(r.pathname))return I.a.replace({url:"/user/status?statusCode=".concat(n.userStatus)}),!1}if("pay"===(null===t||void 0===t||null===(s=t.webConfig)||void 0===s||null===(i=s.setSite)||void 0===i?void 0:i.siteMode)){if(G.z.some((function(e){return r.asPath.match(e)})))return this.checkJump(),!0;var o=r.query.inviteCode,c=o?"?inviteCode=".concat(o):"";if(null===n||void 0===n||!n.paid)return Z.a.saveAndRedirect("/forum/partner-invite".concat(c)),!1}}return this.checkJump()}},{key:"filterProps",value:function(e){var t=V({},e);return delete t.serverUser,delete t.serverSite,delete t.user,delete t.site,t}},{key:"canPublish",value:function(){var e=this.props;return function(e,t){if(!e.isLogin())return Z.a.gotoLogin(),!1;if(!t.publishNeedBindPhone)return!0;var r="";switch("".concat(t.isSmsOpen?"mobile":"").concat("none"!==t.wechatEnv&&"wechat")){case"mobile":r=e.mobile?"":"/user/bind-phone";break;case"wechat":r=e.isBindWechat?"":"/user/wx-bind-qrcode";break;case"mobilewechat":r=e.isBindWechat||e.mobile?"":"/user/wx-bind-qrcode"}return r&&Z.a.setUrl(r),r&&I.a.push({url:r}),!r}(e.user,e.site)}},{key:"render",value:function(){var t=this.state,r=t.isNoSiteData,a=t.isPass;return"static"===this.props.site.platform?null:r||!a?Object(n.jsx)("div",{className:J,children:Object(n.jsx)(A.a,{className:_,name:"LoadingOutlined",size:"large"})}):Object(n.jsx)(e,V({canPublish:this.canPublish},this.filterProps(this.props)))}}]),i}(P.a.Component))||r)||r)||r)||r;return Object(U.withRouter)(a)}},"Tk/S":function(e,t,r){"use strict";r.d(t,"a",(function(){return a}));var n=r("lY1M");function a(e,t,r,a){n.b.env("weixin")&&window.wx&&window.wx.ready((function(){var n={title:e||"Discuz!Q",desc:t&&""!=t?t:e||"Discuz!Q",link:r||window.location.href,imgUrl:a};wx.updateAppMessageShareData(n),wx.updateTimelineShareData(n)}))}},cdnk:function(e,t,r){(window.__NEXT_P=window.__NEXT_P||[]).push(["/search/index",function(){return r("jS0e")}])},jS0e:function(e,t,r){"use strict";r.r(t);r("rB9j"),r("UxlC");var n=r("lwsE"),a=r.n(n),s=r("W8MJ"),i=r.n(s),o=r("7W2i"),c=r.n(o),u=r("a1gu"),l=r.n(u),f=r("Nsbk"),p=r.n(f),d=r("q1tI"),b=r.n(d),h=r("QcND"),v=r("zZWi"),g=r("B5JU"),m=r.n(g);function O(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=p()(e);if(t){var a=p()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return l()(this,r)}}var w=function(e){c()(r,e);var t=O(r);function r(){return a()(this,r),t.apply(this,arguments)}return i()(r,[{key:"componentDidMount",value:function(){m.a.replace({url:"/search"})}},{key:"render",value:function(){return null}}]),r}(b.a.Component);t.default=Object(h.a)(Object(v.a)(w))},rGXy:function(e,t,r){"use strict";var n=r("TqRt");Object.defineProperty(t,"__esModule",{value:!0}),t.getWXConfig=function(){return d.apply(this,arguments)};var a=n(r("o0o1")),s=n(r("lSNA")),i=n(r("QILm"));r("ls82");var o=n(r("yXPU")),c=r("0QFe"),u=n(r("m4Ii"));function l(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function f(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?l(Object(r),!0).forEach((function(t){(0,s.default)(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):l(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var p={messageText:{type:"string",required:!0},recipientUsername:{type:"string",required:!0},imageUrl:{type:"string"},attachmentId:{type:"number"}};function d(){return(d=(0,o.default)(a.default.mark((function e(){var t,r,n,s,o,l,d,b,h=arguments;return a.default.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t=h.length>0&&void 0!==h[0]?h[0]:{},e.prev=1,r=t.params,n=void 0===r?{}:r,s=t.data,o=void 0===s?{}:s,l=(0,i.default)(t,["params","data"]),d=f(f({url:"/apiv3/offiaccount/jssdk",method:"GET",params:n,data:o},l),{},{validateRules:p}),e.next=6,u.default.dispatcher(d);case 6:return b=e.sent,e.abrupt("return",b);case 10:return e.prev=10,e.t0=e.catch(1),e.abrupt("return",(0,c.handleError)(e.t0));case 13:case"end":return e.stop()}}),e,null,[[1,10]])})))).apply(this,arguments)}},yq1k:function(e,t,r){"use strict";var n=r("I+eb"),a=r("TWQb").includes,s=r("RNIs");n({target:"Array",proto:!0,forced:!r("rkAj")("indexOf",{ACCESSORS:!0,1:0})},{includes:function(e){return a(this,e,arguments.length>1?arguments[1]:void 0)}}),s("includes")},zDaA:function(e,t,r){"use strict";r.d(t,"a",(function(){return a}));var n=r("n4oF");function a(e){var t=/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(e)?"h5":"pc";if(!Object(n.a)()){var r=90===Math.abs(window.orientation),a=window.innerWidth;"pc"===t&&a<800&&(t="h5"),"h5"===t&&!r&&a>=800&&(t="pc")}return t}},zZWi:function(e,t,r){"use strict";r.d(t,"a",(function(){return C}));var n=r("nKUr"),a=r("o0o1"),s=r.n(a),i=r("lSNA"),o=r.n(i),c=(r("ls82"),r("yXPU")),u=r.n(c),l=r("lwsE"),f=r.n(l),p=r("W8MJ"),d=r.n(p),b=r("7W2i"),h=r.n(b),v=r("a1gu"),g=r.n(v),m=r("Nsbk"),O=r.n(m),w=r("q1tI"),y=r.n(w),j=r("kMSe"),P=r("n4oF"),k=r("lN2P");function S(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function x(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?S(Object(r),!0).forEach((function(t){o()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):S(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function D(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=O()(e);if(t){var a=O()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return g()(this,r)}}function C(e){var t;return Object(j.c)("user")(t=Object(j.d)(t=function(t){h()(a,t);var r=D(a);function a(e){return f()(this,a),r.call(this,e)}return d()(a,null,[{key:"getInitialProps",value:function(){var t=u()(s.a.mark((function t(r,n){var a;return s.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(n){t.next=2;break}throw Error("CheckLoginStatus\u5fc5\u987b\u524d\u7f6e\u4f7f\u7528HOCFetchSiteData");case 2:if(t.prev=2,a={},!Object(P.a)()){t.next=9;break}if(!e.getInitialProps){t.next=9;break}return t.next=8,e.getInitialProps(r);case 8:a=t.sent;case 9:return t.abrupt("return",x({},a));case 12:return t.prev=12,t.t0=t.catch(2),console.log("err",t.t0),t.abrupt("return",{});case 16:case"end":return t.stop()}}),t,null,[[2,12]])})));return function(e,r){return t.apply(this,arguments)}}()}]),d()(a,[{key:"componentDidMount",value:function(){this.props.user.loginStatus||k.a.saveAndLogin()}},{key:"componentDidUpdate",value:function(){this.props.user.loginStatus||k.a.saveAndLogin()}},{key:"render",value:function(){var t=this.props.user.loginStatus;return"padding"!==t&&t?Object(n.jsx)(e,x({},this.props)):null}}]),a}(y.a.Component))||t)||t}}},[["cdnk",1,0,3,4,2]]]);