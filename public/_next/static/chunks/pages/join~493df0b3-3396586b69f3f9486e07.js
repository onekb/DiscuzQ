_N_E=(window.webpackJsonp_N_E=window.webpackJsonp_N_E||[]).push([[19],{"/Nde":function(e,t,r){"use strict";r.d(t,"a",(function(){return d})),r.d(t,"b",(function(){return p})),r.d(t,"c",(function(){return b})),r.d(t,"d",(function(){return v})),r.d(t,"e",(function(){return h}));var n=r("lSNA"),a=r.n(n),s=(r("o0o1"),r("ls82"),r("yXPU"),r("+3IH")),i=r("dJ22");function c(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function o(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?c(Object(r),!0).forEach((function(t){a()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):c(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var u={Code:"common_0001",Message:"\u9700\u8981\u8865\u5145\u6635\u79f0"},f={Code:"common_0002",Message:"\u9700\u8981\u8865\u5145\u9644\u52a0\u4fe1\u606f"},l={Code:"common_0003",Message:"\u9700\u8981\u8865\u5145\u6635\u79f0\u548c\u9644\u52a0\u4fe1\u606f"},d=-4009,p=2,b=-4007,h=function(e){var t=e.webConfig.setSite;return"1"===(t=void 0===t?{}:t).openExtFields},v=function(e){if(function(e){var t=Object(s.a)(e,"data.isMissNickname",!1),r=10===Object(s.a)(e,"data.userStatus"),n=Object(s.a)(e,"data.accessToken",""),a=Object(s.a)(e,"data.uid","");if(Object(i.a)({accessToken:n}),r&&t)throw o({uid:a},l);if(t)throw o({uid:a},u);if(r)throw o({uid:a},f)}(e),0===e.code||e.code===d||e.code===b){var t=e.code,r=Object(s.a)(e,"data.userStatus",0),n=Object(s.a)(e,"data.uid","");if(0===t&&r===p){var a=Object(s.a)(e,"data.accessToken","");Object(i.a)({accessToken:a}),t=r}if(t)throw{Code:t,Message:Object(s.a)(e,"data.rejectReason",""),uid:n}}}},JTJg:function(e,t,r){"use strict";var n=r("I+eb"),a=r("WjRb"),s=r("HYAF");n({target:"String",proto:!0,forced:!r("qxPZ")("includes")},{includes:function(e){return!!~String(s(this)).indexOf(a(e),arguments.length>1?arguments[1]:void 0)}})},QcND:function(e,t,r){"use strict";r.d(t,"a",(function(){return ee}));r("ma9I"),r("yq1k"),r("4mDm"),r("Rfxz"),r("07d7"),r("rB9j"),r("JTJg"),r("PKPk"),r("Rm1S"),r("UxlC"),r("3bBZ"),r("Kz25");var n=r("nKUr"),a=r("o0o1"),s=r.n(a),i=r("lSNA"),c=r.n(i),o=(r("ls82"),r("yXPU")),u=r.n(o),f=r("lwsE"),l=r.n(f),d=r("PJYZ"),p=r.n(d),b=r("W8MJ"),h=r.n(b),v=r("7W2i"),O=r.n(v),m=r("a1gu"),g=r.n(m),w=r("Nsbk"),j=r.n(w),y=r("q1tI"),P=r.n(y),k=r("kMSe"),x=r("n4oF"),S=r("zDaA"),C=r("sho3"),D=r("B5JU"),R=r.n(D),I=r("20a2"),N=r("bK+J"),U=r("/Nde"),A=r("MCNy"),E=r.n(A),M=r("pGE/"),_=r("Tk/S"),J="rKwiNl2JOBH1dcaoggUsN",T="_3_kaucwAnhguPyKqHJUwsL",W=(r("5s+n"),r("RIqP")),q=r.n(W),z=r("rGXy"),L=r("lY1M");function X(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function F(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?X(Object(r),!0).forEach((function(t){c()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):X(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var B=["closeWindow","chooseImage","uploadImage","getLocalImgData","updateAppMessageShareData","updateTimelineShareData","getNetworkType"],K=!1;function Q(){return H.apply(this,arguments)}function H(){return(H=u()(s.a.mark((function e(){var t,r,n,a,i,c,o=arguments;return s.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(t=o.length>0&&void 0!==o[0]&&o[0],r=o.length>1&&void 0!==o[1]?o[1]:[],L.b.env("weixin")){e.next=4;break}return e.abrupt("return");case 4:return n=[],window.wx&&wx.config||(a=new Promise((function(e){var t=document.createElement("script");t.src="https://res.wx.qq.com/open/js/jweixin-1.6.0.js",t.onload=function(){return e()},document.body.appendChild(t)})),n.push(a)),e.next=8,Promise.all(n);case 8:if(t&&!K){e.next=10;break}return e.abrupt("return");case 10:return e.next=12,Object(z.getWXConfig)({params:{url:encodeURIComponent(window.location.href)}});case 12:if(0!==(i=e.sent).code||!i.data||!i.data.appId){e.next=21;break}return s=i.data,c={appId:s.appId,timestamp:s.timestamp,nonceStr:s.nonceStr,signature:s.signature},wx&&wx.config(F(F({debug:!1},c),{},{jsApiList:[].concat(B,q()(r))})),wx&&(wx.hasDoneConfig=!0),K=!0,e.abrupt("return",!0);case 21:console.error("\u521d\u59cb\u5316\u5fae\u4fe1jssdk\u5931\u8d25\uff01",i);case 22:return e.abrupt("return",!1);case 23:case"end":return e.stop()}var s}),e)})))).apply(this,arguments)}var G=r("yO9+"),Y=r("lN2P");function Z(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function V(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?Z(Object(r),!0).forEach((function(t){c()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):Z(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function $(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=j()(e);if(t){var a=j()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return g()(this,r)}}function ee(e,t){var r,a=Object(k.c)("site")(r=Object(k.c)("user")(r=Object(k.d)(r=function(r){O()(i,r);var a=$(i);function i(e){var t,r;l()(this,i),(t=a.call(this,e)).handleWxShare=t.handleWxShare.bind(p()(t));var n=e.serverUser,s=e.serverSite,c=e.user,o=e.site;return s&&s.platform&&o.setPlatform(s.platform),s&&s.closeSite&&o.setCloseSiteConfig(s.closeSite),s&&s.webConfig&&o.setSiteConfig(s.webConfig),n&&n.userInfo&&c.setUserInfo(n.userInfo),n&&n.userPermissions&&c.setUserPermissions(n.userPermissions),r=Object(x.a)()?!s:!(o&&o.webConfig),t.state={isNoSiteData:r,isPass:!1},t}return h()(i,null,[{key:"getInitialProps",value:function(){var t=u()(s.a.mark((function t(r){var n,a,i,c,o,u,f,l,d,p,b;return s.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(t.prev=0,n="static",a={},o={},!Object(x.a)()){t.next=24;break}return b=r.req.headers,n=b&&!M.a.isEmptyObject(b)?Object(S.a)(b["user-agent"]):"static",t.next=9,Object(C.readForum)({},r);case 9:if(a=t.sent,c={platform:n,closeSite:-3005===a.code?a.data:null,webConfig:a&&a.data||null},!a||0!==a.code||null===(l=a)||void 0===l||null===(d=l.data)||void 0===d||null===(p=d.user)||void 0===p||!p.userId){t.next=20;break}return t.next=14,Object(C.readUser)({params:{pid:a.data.user.userId}},r);case 14:return i=t.sent,t.next=17,Object(C.readPermissions)({},r);case 17:f=t.sent,u=i&&0===i.code?i.data:null,f=f&&0===f.code?f.data:null;case 20:if(!a||0!==a.code||!e.getInitialProps){t.next=24;break}return t.next=23,e.getInitialProps(r,{user:u,site:c});case 23:o=t.sent;case 24:return t.abrupt("return",V(V({},o),{},{serverSite:c,serverUser:{userInfo:u,userPermissions:f}}));case 27:return t.prev=27,t.t0=t.catch(0),console.log("err",t.t0),t.abrupt("return",{serverSite:{},serverUser:{}});case 31:case"end":return t.stop()}}),t,null,[[0,27]])})));return function(e){return t.apply(this,arguments)}}()}]),h()(i,[{key:"componentDidMount",value:function(){var e=u()(s.a.mark((function e(){var r,n,a,i,c,o,u,f,l,d,p,b;return s.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=this.state.isNoSiteData,n=this.props,a=n.serverUser,i=n.serverSite,c=n.user,o=n.site,f=!1,o.setPlatform(Object(S.a)(window.navigator.userAgent)),!r){e.next=15;break}if(u=(null===i||void 0===i?void 0:i.webConfig)||null){e.next=13;break}return e.next=9,Object(C.readForum)({});case 9:(l=e.sent).data&&o.setSiteConfig(l.data),this.setAppCommonStatus(l),u=l.data||null;case 13:e.next=16;break;case 15:u=o?o.webConfig:null;case 16:if(o.initUserLoginEntryStatus(),!u||!u.user){e.next=33;break}if(c&&c.userInfo||a&&a.userInfo){e.next=30;break}return e.next=21,Object(C.readUser)({params:{pid:u.user.userId}});case 21:return d=e.sent,e.next=24,Object(C.readPermissions)({});case 24:0===(p=e.sent).code&&p.data&&c.setUserPermissions(p.data),d.data&&c.setUserInfo(d.data),f=!!d.data,e.next=31;break;case 30:f=!0;case 31:e.next=34;break;case 33:f=!1;case 34:return c.updateLoginStatus(f),b=this.isPass(),t&&b&&(b=t(b)),this.setState({isPass:b}),e.next=40,Q(u&&u.passport&&u.passport.offiaccountOpen);case 40:e.sent&&(this.handleWxShare(this.props.router.asPath),this.props.router.events.on("routeChangeComplete",this.handleWxShare));case 42:case"end":return e.stop()}}),e,this)})));return function(){return e.apply(this,arguments)}}()},{key:"handleWxShare",value:function(e){if(window.wx&&window.wx.hasDoneConfig){var t=this.props,r=t.user,n=t.site,a=r.userInfo,s=n.webConfig.setSite,i=s.siteName,c=s.siteIntroduction,o=s.siteHeaderLogo,u=a.nickname,f=a.avatarUrl,l=a.signature,d=a.id,p=document.title,b=c?c.length>35?"".concat(c.substr(0,35),"..."):c:"\u5728\u8fd9\u91cc\uff0c\u53d1\u73b0\u66f4\u591a\u7cbe\u5f69\u5185\u5bb9",h=window.location.href,v=o;e.match(/\/thread\/\d+/)||e.match(/\/user\/\d+/)||("/my"===e&&u&&(p="".concat(u,"\u7684\u4e3b\u9875"),v=f,b=l?l.length>35?"".concat(l.substr(0,35),"..."):l:"\u5728\u8fd9\u91cc\uff0c\u53d1\u73b0\u66f4\u591a\u7cbe\u5f69\u5185\u5bb9",h="".concat(window.location.origin,"/user/").concat(d)),(e.includes("/forum/partner-invite")||e.match(/\/user\/(username|wx|phone)-login/)||e.includes("/user/register"))&&(p="\u9080\u8bf7\u60a8\u52a0\u5165".concat(i)),(e.includes("/invite")||"/"===e)&&(p="".concat(u,"\u9080\u8bf7\u60a8\u52a0\u5165").concat(i)),e.includes("/message?page=chat")&&(p="\u6211\u7684\u79c1\u4fe1 - ".concat(i),h="".concat(window.location.origin,"/message")),Object(_.a)(p,b,h,v))}}},{key:"setAppCommonStatus",value:function(e){var t=this.props.site;switch([G.i,G.l,G.f,G.k,G.g,G.m,G.j].includes(e.code)&&Y.a.saveCurrentUrl(),e.code){case 0:break;case G.r:t.setCloseSiteConfig(e.data),R.a.redirect({url:"/close"});break;case G.d:break;case G.t:Object(N.a)(),window.location.reload();break;case G.e:R.a.redirect({url:"/404"});break;case G.i:Object(N.a)(),Y.a.gotoLogin();break;case G.l:Object(N.a)(),Y.a.saveAndRedirect("/user/register");break;case G.f:R.a.push({url:"/user/status?statusCode=2"});break;case G.k:R.a.push({url:"/user/status?statusCode=-4007"});break;case G.g:R.a.push({url:"/user/status?statusCode=-4009"});break;case G.m:Y.a.saveAndRedirect("/user/supplementary");break;case G.h:R.a.redirect({url:"/"});break;case G.j:Y.a.saveAndRedirect("/forum/partner-invite");break;default:R.a.redirect({url:"/500"})}}},{key:"checkJump",value:function(){var e=this.props.router,t=Y.a.getUrl();if(t)if(new URL(t).pathname===e.asPath)Y.a.clear();else if("/"===e.asPath)return Y.a.restore(),!1;return!0}},{key:"isPass",value:function(){var e=this.props,t=e.site,r=e.router,n=e.user,a=this.state.isNoSiteData;if(t&&t.webConfig){var s,i;if(a&&this.setState({isNoSiteData:!1}),"/close"!==r.asPath&&t.closeSiteConfig)return R.a.redirect({url:"/close"}),!1;if(n.isLogin()){if(!t.isOffiaccountOpen&&!t.isMiniProgramOpen&&"/user/bind-phone"!==r.asPath&&t.isSmsOpen&&!n.mobile)return Y.a.saveAndRedirect("/user/bind-phone"),!1;if("/user/bind-nickname"!==r.asPath&&!n.nickname)return Y.a.saveAndRedirect("/user/bind-nickname"),!1;if(n.userStatus===U.b&&!G.q.includes(r.pathname))return R.a.replace({url:"/user/status?statusCode=".concat(n.userStatus)}),!1}if("pay"===(null===t||void 0===t||null===(s=t.webConfig)||void 0===s||null===(i=s.setSite)||void 0===i?void 0:i.siteMode)){if(G.u.some((function(e){return r.asPath.match(e)})))return this.checkJump(),!0;var c=r.query.inviteCode,o=c?"?inviteCode=".concat(c):"";if(null===n||void 0===n||!n.paid)return Y.a.saveAndRedirect("/forum/partner-invite".concat(o)),!1}}return this.checkJump()}},{key:"filterProps",value:function(e){var t=V({},e);return delete t.serverUser,delete t.serverSite,delete t.user,delete t.site,t}},{key:"render",value:function(){var t=this.state,r=t.isNoSiteData,a=t.isPass;return"static"===this.props.site.platform?null:r||!a?Object(n.jsx)("div",{className:J,children:Object(n.jsx)(E.a,{className:T,name:"LoadingOutlined",size:"large"})}):Object(n.jsx)(e,V({},this.filterProps(this.props)))}}]),i}(P.a.Component))||r)||r)||r;return Object(I.withRouter)(a)}},"Tk/S":function(e,t,r){"use strict";r.d(t,"a",(function(){return a}));var n=r("lY1M");function a(e,t,r,a){n.b.env("weixin")&&window.wx&&window.wx.ready((function(){var n={title:e||"Discuz!Q",desc:t&&""!=t?t:e||"Discuz!Q",link:r||window.location.href,imgUrl:a};wx.updateAppMessageShareData(n),wx.updateTimelineShareData(n)}))}},VwUA:function(e,t,r){(window.__NEXT_P=window.__NEXT_P||[]).push(["/join",function(){return r("nzzv")}])},nzzv:function(e,t,r){"use strict";r.r(t);var n,a=r("nKUr"),s=r("lwsE"),i=r.n(s),c=r("W8MJ"),o=r.n(c),u=r("7W2i"),f=r.n(u),l=r("a1gu"),d=r.n(l),p=r("Nsbk"),b=r.n(p),h=r("q1tI"),v=r.n(h),O=r("kMSe"),m=(r("n4oF"),r("B5JU"),r("QcND")),g=r("brci");function w(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=b()(e);if(t){var a=b()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return d()(this,r)}}var j,y=Object(O.c)("site")(n=Object(O.d)(n=function(e){f()(r,e);var t=w(r);function r(){return i()(this,r),t.apply(this,arguments)}return o()(r,[{key:"render",value:function(){this.props.site;return Object(a.jsx)("div",{className:g.a.page,children:Object(a.jsx)("h1",{className:g.a.main,children:"\u52a0\u5165\u7ad9\u70b9"})})}}]),r}(v.a.Component))||n)||n,P=r("eWps");function k(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=b()(e);if(t){var a=b()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return d()(this,r)}}var x,S=Object(O.c)("site")(j=Object(O.d)(j=function(e){f()(r,e);var t=k(r);function r(){return i()(this,r),t.apply(this,arguments)}return o()(r,[{key:"render",value:function(){var e=this.props.site;return console.log(e),Object(a.jsx)("div",{className:P.a.page,children:Object(a.jsx)("h1",{className:P.a.main,children:"\u52a0\u5165\u7ad9\u70b9"})})}}]),r}(v.a.Component))||j)||j;function C(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=b()(e);if(t){var a=b()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return d()(this,r)}}var D=Object(O.c)("site")(x=Object(O.d)(x=function(e){f()(r,e);var t=C(r);function r(){return i()(this,r),t.apply(this,arguments)}return o()(r,[{key:"render",value:function(){var e=this.props.site;e.closeSiteConfig;return"pc"===e.platform?Object(a.jsx)(y,{}):Object(a.jsx)(S,{})}}]),r}(v.a.Component))||x)||x;t.default=Object(m.a)(D)},rGXy:function(e,t,r){"use strict";var n=r("TqRt");Object.defineProperty(t,"__esModule",{value:!0}),t.getWXConfig=function(){return p.apply(this,arguments)};var a=n(r("o0o1")),s=n(r("lSNA")),i=n(r("QILm"));r("ls82");var c=n(r("yXPU")),o=r("0QFe"),u=n(r("m4Ii"));function f(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function l(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?f(Object(r),!0).forEach((function(t){(0,s.default)(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):f(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var d={messageText:{type:"string",required:!0},recipientUsername:{type:"string",required:!0},imageUrl:{type:"string"},attachmentId:{type:"number"}};function p(){return(p=(0,c.default)(a.default.mark((function e(){var t,r,n,s,c,f,p,b,h=arguments;return a.default.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t=h.length>0&&void 0!==h[0]?h[0]:{},e.prev=1,r=t.params,n=void 0===r?{}:r,s=t.data,c=void 0===s?{}:s,f=(0,i.default)(t,["params","data"]),p=l(l({url:"/apiv3/offiaccount/jssdk",method:"GET",params:n,data:c},f),{},{validateRules:d}),e.next=6,u.default.dispatcher(p);case 6:return b=e.sent,e.abrupt("return",b);case 10:return e.prev=10,e.t0=e.catch(1),e.abrupt("return",(0,o.handleError)(e.t0));case 13:case"end":return e.stop()}}),e,null,[[1,10]])})))).apply(this,arguments)}},yq1k:function(e,t,r){"use strict";var n=r("I+eb"),a=r("TWQb").includes,s=r("RNIs");n({target:"Array",proto:!0,forced:!r("rkAj")("indexOf",{ACCESSORS:!0,1:0})},{includes:function(e){return a(this,e,arguments.length>1?arguments[1]:void 0)}}),s("includes")},zDaA:function(e,t,r){"use strict";r.d(t,"a",(function(){return a}));var n=r("n4oF");function a(e){var t=/Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(e)?"h5":"pc";if(!Object(n.a)()){var r=90===Math.abs(window.orientation),a=window.innerWidth;"pc"===t&&a<800&&(t="h5"),"h5"===t&&!r&&a>=800&&(t="pc")}return t}}},[["VwUA",1,0,3,4,2]]]);