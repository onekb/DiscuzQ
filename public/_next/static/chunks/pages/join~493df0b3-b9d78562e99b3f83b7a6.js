_N_E=(window.webpackJsonp_N_E=window.webpackJsonp_N_E||[]).push([[19],{JTJg:function(e,t,r){"use strict";var n=r("I+eb"),a=r("WjRb"),s=r("HYAF");n({target:"String",proto:!0,forced:!r("qxPZ")("includes")},{includes:function(e){return!!~String(s(this)).indexOf(a(e),arguments.length>1?arguments[1]:void 0)}})},QcND:function(e,t,r){"use strict";r.d(t,"a",(function(){return Y}));r("yq1k"),r("4mDm"),r("Rfxz"),r("07d7"),r("rB9j"),r("JTJg"),r("PKPk"),r("Rm1S"),r("UxlC"),r("3bBZ"),r("Kz25");var n=r("nKUr"),a=r("o0o1"),s=r.n(a),i=r("lSNA"),c=r.n(i),o=(r("ls82"),r("yXPU")),u=r.n(o),l=r("lwsE"),f=r.n(l),p=r("W8MJ"),d=r.n(p),b=r("7W2i"),v=r.n(b),h=r("a1gu"),g=r.n(h),m=r("Nsbk"),y=r.n(m),O=r("q1tI"),j=r.n(O),w=r("kMSe"),P=r("n4oF"),k=r("zDaA"),x=r("sho3"),S=r("B5JU"),I=r.n(S),C=r("20a2"),R=r("bK+J"),D=r("/Nde"),N=r("MCNy"),U=r.n(N),E=r("pGE/"),A=r("Tk/S"),_="rKwiNl2JOBH1dcaoggUsN",q="_3_kaucwAnhguPyKqHJUwsL",J=(r("ma9I"),r("5s+n"),r("RIqP")),M=r.n(J),T=r("rGXy"),z=r("lY1M");function L(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function W(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?L(Object(r),!0).forEach((function(t){c()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):L(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var X=["closeWindow","chooseImage","uploadImage","getLocalImgData","updateAppMessageShareData","updateTimelineShareData","getNetworkType"],F=!1;function K(){return B.apply(this,arguments)}function B(){return(B=u()(s.a.mark((function e(){var t,r,n,a,i,c,o=arguments;return s.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(t=o.length>0&&void 0!==o[0]&&o[0],r=o.length>1&&void 0!==o[1]?o[1]:[],z.a.env("weixin")){e.next=4;break}return e.abrupt("return");case 4:return n=[],window.wx&&wx.config||(a=new Promise((function(e){var t=document.createElement("script");t.src="https://res.wx.qq.com/open/js/jweixin-1.6.0.js",t.onload=function(){return e()},document.body.appendChild(t)})),n.push(a)),e.next=8,Promise.all(n);case 8:if(t&&!F){e.next=10;break}return e.abrupt("return");case 10:return e.next=12,Object(T.getWXConfig)({params:{url:encodeURIComponent(window.location.href)}});case 12:if(0!==(i=e.sent).code||!i.data||!i.data.appId){e.next=21;break}return s=i.data,c={appId:s.appId,timestamp:s.timestamp,nonceStr:s.nonceStr,signature:s.signature},wx&&wx.config(W(W({debug:!1},c),{},{jsApiList:[].concat(X,M()(r))})),wx&&(wx.hasDoneConfig=!0),F=!0,e.abrupt("return",!0);case 21:console.error("\u521d\u59cb\u5316\u5fae\u4fe1jssdk\u5931\u8d25\uff01",i);case 22:return e.abrupt("return",!1);case 23:case"end":return e.stop()}var s}),e)})))).apply(this,arguments)}var G=r("yO9+");function Q(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function H(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?Q(Object(r),!0).forEach((function(t){c()(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):Q(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}function V(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=y()(e);if(t){var a=y()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return g()(this,r)}}function Y(e){var t,r=Object(w.c)("site")(t=Object(w.c)("user")(t=Object(w.d)(t=function(t){v()(a,t);var r=V(a);function a(e){var t,n;f()(this,a),t=r.call(this,e);var s=e.serverUser,i=e.serverSite,c=e.user,o=e.site;return i&&i.platform&&o.setPlatform(i.platform),i&&i.closeSite&&o.setCloseSiteConfig(i.closeSite),i&&i.webConfig&&o.setSiteConfig(i.webConfig),s&&s.userInfo&&c.setUserInfo(s.userInfo),s&&s.userPermissions&&c.setUserPermissions(s.userPermissions),n=Object(P.a)()?!i:!(o&&o.webConfig),t.state={isNoSiteData:n,isPass:!1},t}return d()(a,null,[{key:"getInitialProps",value:function(){var t=u()(s.a.mark((function t(r){var n,a,i,c,o,u,l,f,p,d,b;return s.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(t.prev=0,n="static",a={},o={},!Object(P.a)()){t.next=24;break}return b=r.req.headers,n=b&&!E.a.isEmptyObject(b)?Object(k.a)(b["user-agent"]):"static",t.next=9,Object(x.readForum)({},r);case 9:if(a=t.sent,c={platform:n,closeSite:-3005===a.code?a.data:null,webConfig:a&&a.data||null},!a||0!==a.code||null===(f=a)||void 0===f||null===(p=f.data)||void 0===p||null===(d=p.user)||void 0===d||!d.userId){t.next=20;break}return t.next=14,Object(x.readUser)({params:{pid:a.data.user.userId}},r);case 14:return i=t.sent,t.next=17,Object(x.readPermissions)({},r);case 17:l=t.sent,u=i&&0===i.code?i.data:null,l=l&&0===l.code?l.data:null;case 20:if(!a||0!==a.code||!e.getInitialProps){t.next=24;break}return t.next=23,e.getInitialProps(r,{user:u,site:c});case 23:o=t.sent;case 24:return t.abrupt("return",H(H({},o),{},{serverSite:c,serverUser:{userInfo:u,userPermissions:l}}));case 27:return t.prev=27,t.t0=t.catch(0),console.log("err",t.t0),t.abrupt("return",{serverSite:{},serverUser:{}});case 31:case"end":return t.stop()}}),t,null,[[0,27]])})));return function(e){return t.apply(this,arguments)}}()}]),d()(a,[{key:"componentDidMount",value:function(){var e=u()(s.a.mark((function e(){var t,r,n,a,i,c,o,u,l,f,p,d,b,v,h;return s.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(t=this.state.isNoSiteData,r=this.props,n=r.serverUser,a=r.serverSite,i=r.user,c=r.site,u=!1,c.setPlatform(Object(k.a)(window.navigator.userAgent)),!t){e.next=15;break}if(o=(null===a||void 0===a?void 0:a.webConfig)||null){e.next=13;break}return e.next=9,Object(x.readForum)({});case 9:(l=e.sent).data&&c.setSiteConfig(l.data),this.setAppCommonStatus(l),o=l.data||null;case 13:e.next=16;break;case 15:o=c?c.webConfig:null;case 16:if(c.initUserLoginEntryStatus(),!o||!o.user){e.next=33;break}if(i&&i.userInfo||n&&n.userInfo){e.next=30;break}return e.next=21,Object(x.readUser)({params:{pid:o.user.userId}});case 21:return f=e.sent,e.next=24,Object(x.readPermissions)({});case 24:0===(p=e.sent).code&&p.data&&i.setUserPermissions(p.data),f.data&&i.setUserInfo(f.data),u=!!f.data,e.next=31;break;case 30:u=!0;case 31:e.next=34;break;case 33:u=!1;case 34:return i.updateLoginStatus(u),this.setState({isPass:this.isPass()}),e.next=38,K(o&&o.passport&&o.passport.offiaccountOpen);case 38:e.sent&&o&&o.setSite&&(d=o.setSite,b=d.siteTitle,v=d.siteIntroduction,h=d.siteFavicon,Object(A.a)(b,v,window.location.href,h));case 40:case"end":return e.stop()}}),e,this)})));return function(){return e.apply(this,arguments)}}()},{key:"setAppCommonStatus",value:function(e){var t=this.props.site;switch([G.i,G.l,G.f,G.k,G.g,G.m,G.j].includes(e.code)&&this.saveInitialPage(),e.code){case 0:break;case G.q:t.setCloseSiteConfig(e.data),I.a.redirect({url:"/close"});break;case G.d:break;case G.s:Object(R.a)(),window.location.reload();break;case G.e:I.a.redirect({url:"/404"});break;case G.i:Object(R.a)(),window.location.replace("/user/login");break;case G.l:Object(R.a)(),window.location.replace("/user/register");break;case G.f:I.a.push({url:"/user/status?statusCode=2"});break;case G.k:I.a.push({url:"/user/status?statusCode=-4007"});break;case G.g:I.a.push({url:"/user/status?statusCode=-4009"});break;case G.m:I.a.push({url:"/user/supplementary"});break;case G.h:I.a.redirect({url:"/"});break;case G.j:I.a.push({url:"/forum/partner-invite"});break;default:I.a.redirect({url:"/500"})}}},{key:"saveInitialPage",value:function(){var e=this.props.site;e.getInitialPage()||e.setInitialPage(window.location.href)}},{key:"saveAndRedirect",value:function(e){this.saveInitialPage(e),I.a.redirect({url:e})}},{key:"isPass",value:function(){var e=this.props,t=e.site,r=e.router,n=e.user,a=this.state.isNoSiteData;if(t&&t.webConfig){var s,i;if(a&&this.setState({isNoSiteData:!1}),"/close"!==r.asPath&&t.closeSiteConfig)return I.a.redirect({url:"/close"}),!1;if(n.isLogin()){if(!t.isOffiaccountOpen&&!t.isMiniProgramOpen&&"/user/bind-phone"!==r.asPath&&t.isSmsOpen&&!n.mobile)return this.saveAndRedirect("/user/bind-phone"),!1;if("/user/bind-nickname"!==r.asPath&&!n.nickname)return this.saveAndRedirect("/user/bind-nickname"),!1;if(n.userStatus===D.b&&!G.p.includes(r.pathname))return I.a.replace({url:"/user/status?statusCode=".concat(n.userStatus)}),!1}if("pay"!==(null===t||void 0===t||null===(s=t.webConfig)||void 0===s||null===(i=s.setSite)||void 0===i?void 0:i.siteMode))return!0;if(G.t.some((function(e){return r.asPath.match(e)})))return!0;var c=r.query.inviteCode,o=c?"?inviteCode=".concat(c):"";if(null===n||void 0===n||!n.paid)return this.saveAndRedirect("/forum/partner-invite".concat(o)),!1;if("/"===r.asPath){var u=t.getInitialPage();if(u){if(new URL(u).pathname!==r.asPath)return t.clearInitialPage(),I.a.redirect({url:u}),!1;t.clearInitialPage()}}}return!0}},{key:"filterProps",value:function(e){var t=H({},e);return delete t.serverUser,delete t.serverSite,delete t.user,delete t.site,t}},{key:"render",value:function(){var t=this.state,r=t.isNoSiteData,a=t.isPass;return"static"===this.props.site.platform?null:r||!a?Object(n.jsx)("div",{className:_,children:Object(n.jsx)(U.a,{className:q,name:"LoadingOutlined",size:"large"})}):Object(n.jsx)(e,H({},this.filterProps(this.props)))}}]),a}(j.a.Component))||t)||t)||t;return Object(C.withRouter)(r)}},Rfxz:function(e,t,r){"use strict";var n=r("I+eb"),a=r("tycR").some,s=r("pkCn"),i=r("rkAj"),c=s("some"),o=i("some");n({target:"Array",proto:!0,forced:!c||!o},{some:function(e){return a(this,e,arguments.length>1?arguments[1]:void 0)}})},VwUA:function(e,t,r){(window.__NEXT_P=window.__NEXT_P||[]).push(["/join",function(){return r("nzzv")}])},nzzv:function(e,t,r){"use strict";r.r(t);var n,a=r("nKUr"),s=r("lwsE"),i=r.n(s),c=r("W8MJ"),o=r.n(c),u=r("7W2i"),l=r.n(u),f=r("a1gu"),p=r.n(f),d=r("Nsbk"),b=r.n(d),v=r("q1tI"),h=r.n(v),g=r("kMSe"),m=(r("n4oF"),r("B5JU"),r("QcND")),y=r("brci");function O(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=b()(e);if(t){var a=b()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return p()(this,r)}}var j,w=Object(g.c)("site")(n=Object(g.d)(n=function(e){l()(r,e);var t=O(r);function r(){return i()(this,r),t.apply(this,arguments)}return o()(r,[{key:"render",value:function(){this.props.site;return Object(a.jsx)("div",{className:y.a.page,children:Object(a.jsx)("h1",{className:y.a.main,children:"\u52a0\u5165\u7ad9\u70b9"})})}}]),r}(h.a.Component))||n)||n,P=r("eWps");function k(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=b()(e);if(t){var a=b()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return p()(this,r)}}var x,S=Object(g.c)("site")(j=Object(g.d)(j=function(e){l()(r,e);var t=k(r);function r(){return i()(this,r),t.apply(this,arguments)}return o()(r,[{key:"render",value:function(){var e=this.props.site;return console.log(e),Object(a.jsx)("div",{className:P.a.page,children:Object(a.jsx)("h1",{className:P.a.main,children:"\u52a0\u5165\u7ad9\u70b9"})})}}]),r}(h.a.Component))||j)||j;function I(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=b()(e);if(t){var a=b()(this).constructor;r=Reflect.construct(n,arguments,a)}else r=n.apply(this,arguments);return p()(this,r)}}var C=Object(g.c)("site")(x=Object(g.d)(x=function(e){l()(r,e);var t=I(r);function r(){return i()(this,r),t.apply(this,arguments)}return o()(r,[{key:"render",value:function(){var e=this.props.site;e.closeSiteConfig;return"pc"===e.platform?Object(a.jsx)(w,{}):Object(a.jsx)(S,{})}}]),r}(h.a.Component))||x)||x;t.default=Object(m.a)(C)},rGXy:function(e,t,r){"use strict";var n=r("TqRt");Object.defineProperty(t,"__esModule",{value:!0}),t.getWXConfig=function(){return d.apply(this,arguments)};var a=n(r("o0o1")),s=n(r("lSNA")),i=n(r("QILm"));r("ls82");var c=n(r("yXPU")),o=r("0QFe"),u=n(r("m4Ii"));function l(e,t){var r=Object.keys(e);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(e);t&&(n=n.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),r.push.apply(r,n)}return r}function f(e){for(var t=1;t<arguments.length;t++){var r=null!=arguments[t]?arguments[t]:{};t%2?l(Object(r),!0).forEach((function(t){(0,s.default)(e,t,r[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(r)):l(Object(r)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(r,t))}))}return e}var p={messageText:{type:"string",required:!0},recipientUsername:{type:"string",required:!0},imageUrl:{type:"string"},attachmentId:{type:"number"}};function d(){return(d=(0,c.default)(a.default.mark((function e(){var t,r,n,s,c,l,d,b,v=arguments;return a.default.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:return t=v.length>0&&void 0!==v[0]?v[0]:{},e.prev=1,r=t.params,n=void 0===r?{}:r,s=t.data,c=void 0===s?{}:s,l=(0,i.default)(t,["params","data"]),d=f(f({url:"/apiv3/offiaccount/jssdk",method:"GET",params:n,data:c},l),{},{validateRules:p}),e.next=6,u.default.dispatcher(d);case 6:return b=e.sent,e.abrupt("return",b);case 10:return e.prev=10,e.t0=e.catch(1),e.abrupt("return",(0,o.handleError)(e.t0));case 13:case"end":return e.stop()}}),e,null,[[1,10]])})))).apply(this,arguments)}}},[["VwUA",1,0,3,4,2]]]);