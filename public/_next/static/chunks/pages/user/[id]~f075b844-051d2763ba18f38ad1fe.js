_N_E=(window.webpackJsonp_N_E=window.webpackJsonp_N_E||[]).push([[60],{"/0+H":function(e,t,r){"use strict";t.__esModule=!0,t.isInAmpMode=i,t.useAmp=function(){return i(o.default.useContext(a.AmpStateContext))};var n,o=(n=r("q1tI"))&&n.__esModule?n:{default:n},a=r("lwAK");function i(){var e=arguments.length>0&&void 0!==arguments[0]?arguments[0]:{},t=e.ampFirst,r=void 0!==t&&t,n=e.hybrid,o=void 0!==n&&n,a=e.hasQuery,i=void 0!==a&&a;return r||o&&i}},"4Brf":function(e,t,r){"use strict";var n=r("I+eb"),o=r("g6v/"),a=r("2oRo"),i=r("UTVS"),s=r("hh1v"),c=r("m/L8").f,u=r("6JNq"),d=a.Symbol;if(o&&"function"==typeof d&&(!("description"in d.prototype)||void 0!==d().description)){var f={},l=function(){var e=arguments.length<1||void 0===arguments[0]?void 0:String(arguments[0]),t=this instanceof l?new d(e):void 0===e?d():d(e);return""===e&&(f[t]=!0),t};u(l,d);var p=l.prototype=d.prototype;p.constructor=l;var h=p.toString,v="Symbol(test)"==String(d("test")),g=/^Symbol\((.*)\)[^)]+$/;c(p,"description",{configurable:!0,get:function(){var e=s(this)?this.valueOf():this,t=h.call(e);if(i(f,e))return"";var r=v?t.slice(7,-1):t.replace(g,"$1");return""===r?void 0:r}}),n({global:!0,forced:!0},{Symbol:l})}},"4xcg":function(e,t,r){"use strict";r("pNMO"),r("4Brf"),r("ma9I");var n,o=r("nKUr"),a=r("lwsE"),i=r.n(a),s=r("W8MJ"),c=r.n(s),u=r("7W2i"),d=r.n(u),f=r("a1gu"),l=r.n(f),p=r("Nsbk"),h=r.n(p),v=r("q1tI"),g=r.n(v),y=r("kMSe"),m=r("+3IH"),b=r("g4pe"),j=r.n(b),x=(r("yXV3"),r("rB9j"),function(e,t,r,n){var o=w(e+t),a=[],i=[];if(r||!S(o,a)){r&&document.getElementById(o)&&document.getElementById(o).parentNode.removeChild(document.getElementById(o)),a.push(o);var s=document.createElement("script");s.type="text/javascript",s.id=o;try{e?(s.src=e,s.onloadDone=!1,s.onload=function(){s.onloadDone=!0,i[e]=1},s.onreadystatechange=function(){"loaded"!==s.readyState&&"complete"!==s.readyState||s.onloadDone||(s.onloadDone=!0,i[e]=1)}):t&&(s.text=t),document.getElementsByTagName("head")[0].appendChild(s)}catch(c){console.log(c)}}}),w=function(e){var t=arguments.length>1&&void 0!==arguments[1]?arguments[1]:32,r=0,n=0,o="",a="";for(a=t-e.length%t,n=0;n<a;n+=1)e+="0";for(;r<e.length;)o=O(o,e.substr(r,t)),r+=t;return o},O=function(e,t){for(var r="",n=Math.max(e.length,t.length),o=0;o<n;o++){var a=e.charCodeAt(o)^t.charCodeAt(o);r+="abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ".charAt(a%52)}return r},S=function(e,t){if("string"===typeof e||"number"===typeof e)for(var r in t)if(t[r]===e)return!0;return!1};function k(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=h()(e);if(t){var o=h()(this).constructor;r=Reflect.construct(n,arguments,o)}else r=n.apply(this,arguments);return l()(this,r)}}var I=Object(y.c)("site")(n=Object(y.d)(n=function(e){d()(r,e);var t=k(r);function r(e){return i()(this,r),t.call(this,e)}return c()(r,[{key:"componentDidMount",value:function(){var e,t,r;!function(e){if(-1===e.indexOf("<script"))return e;for(var t=/<script[^\>]*?>([^\x00]*?)<\/script>/gi,r=[];r=t.exec(e);){var n=/<script[^\>]*?src=\"([^\>]*?)\"[^\>]*?(reload=\"1\")?(?:charset=\"([\w\-]+?)\")?><\/script>/i,o=[];(o=n.exec(r[0]))?x(o[1],"",o[2],o[3]):(o=(n=/<script(.*?)>([^\x00]+?)<\/script>/i).exec(r[0]),x("",o[2],-1!==o[1].indexOf("reload=")))}}((null===(e=this.props.site)||void 0===e||null===(t=e.webConfig)||void 0===t||null===(r=t.setSite)||void 0===r?void 0:r.siteStat)||"")}},{key:"formatTitle",value:function(){var e=this.props,t=e.site,r=e.title,n=e.showSiteName,o=void 0===n||n,a=Object(m.a)(t,"webConfig.setSite.siteName","\u6b22\u8fce\u60a8");return r&&""!==r&&(a="".concat(r).concat(o?" - ".concat(a):"")),a}},{key:"formatKeywords",value:function(){var e=this.props,t=e.site,r=e.keywords,n=Object(m.a)(t,"webConfig.setSite.siteKeywords","\u6b22\u8fce\u60a8");return r&&""!==r&&(n="".concat(r," - ").concat(n)),n}},{key:"formatDescription",value:function(){var e=this.props,t=e.site,r=e.description,n=Object(m.a)(t,"webConfig.setSite.siteIntroduction","\u6b22\u8fce\u60a8");return r&&""!==r&&(n=r),n}},{key:"render",value:function(){var e,t,r,n=(null===(e=this.props.site)||void 0===e||null===(t=e.webConfig)||void 0===t||null===(r=t.setSite)||void 0===r?void 0:r.siteFavicon)||"";return Object(o.jsxs)(j.a,{children:[Object(o.jsx)("meta",{name:"keywords",content:this.formatKeywords()}),Object(o.jsx)("meta",{name:"description",content:this.formatDescription()}),n&&Object(o.jsx)("link",{rel:"icon",href:n}),Object(o.jsx)("title",{children:this.formatTitle()})]})}}]),r}(g.a.Component))||n)||n;t.a=I},"5Tg+":function(e,t,r){var n=r("tiKp");t.f=n},"8Kt/":function(e,t,r){"use strict";r("lSNA");t.__esModule=!0,t.defaultHead=d,t.default=void 0;var n,o=function(e){if(e&&e.__esModule)return e;if(null===e||"object"!==typeof e&&"function"!==typeof e)return{default:e};var t=u();if(t&&t.has(e))return t.get(e);var r={},n=Object.defineProperty&&Object.getOwnPropertyDescriptor;for(var o in e)if(Object.prototype.hasOwnProperty.call(e,o)){var a=n?Object.getOwnPropertyDescriptor(e,o):null;a&&(a.get||a.set)?Object.defineProperty(r,o,a):r[o]=e[o]}r.default=e,t&&t.set(e,r);return r}(r("q1tI")),a=(n=r("Xuae"))&&n.__esModule?n:{default:n},i=r("lwAK"),s=r("FYa8"),c=r("/0+H");function u(){if("function"!==typeof WeakMap)return null;var e=new WeakMap;return u=function(){return e},e}function d(){var e=arguments.length>0&&void 0!==arguments[0]&&arguments[0],t=[o.default.createElement("meta",{charSet:"utf-8"})];return e||t.push(o.default.createElement("meta",{name:"viewport",content:"width=device-width"})),t}function f(e,t){return"string"===typeof t||"number"===typeof t?e:t.type===o.default.Fragment?e.concat(o.default.Children.toArray(t.props.children).reduce((function(e,t){return"string"===typeof t||"number"===typeof t?e:e.concat(t)}),[])):e.concat(t)}var l=["name","httpEquiv","charSet","itemProp"];function p(e,t){return e.reduce((function(e,t){var r=o.default.Children.toArray(t.props.children);return e.concat(r)}),[]).reduce(f,[]).reverse().concat(d(t.inAmpMode)).filter(function(){var e=new Set,t=new Set,r=new Set,n={};return function(o){var a=!0,i=!1;if(o.key&&"number"!==typeof o.key&&o.key.indexOf("$")>0){i=!0;var s=o.key.slice(o.key.indexOf("$")+1);e.has(s)?a=!1:e.add(s)}switch(o.type){case"title":case"base":t.has(o.type)?a=!1:t.add(o.type);break;case"meta":for(var c=0,u=l.length;c<u;c++){var d=l[c];if(o.props.hasOwnProperty(d))if("charSet"===d)r.has(d)?a=!1:r.add(d);else{var f=o.props[d],p=n[d]||new Set;"name"===d&&i||!p.has(f)?(p.add(f),n[d]=p):a=!1}}}return a}}()).reverse().map((function(e,t){var r=e.key||t;return o.default.cloneElement(e,{key:r})}))}function h(e){var t=e.children,r=(0,o.useContext)(i.AmpStateContext),n=(0,o.useContext)(s.HeadManagerContext);return o.default.createElement(a.default,{reduceComponentsToState:p,headManager:n,inAmpMode:(0,c.isInAmpMode)(r)},t)}h.rewind=function(){};var v=h;t.default=v},"BX/b":function(e,t,r){var n=r("/GqU"),o=r("JBy8").f,a={}.toString,i="object"==typeof window&&window&&Object.getOwnPropertyNames?Object.getOwnPropertyNames(window):[];e.exports.f=function(e){return i&&"[object Window]"==a.call(e)?function(e){try{return o(e)}catch(t){return i.slice()}}(e):o(n(e))}},FYa8:function(e,t,r){"use strict";var n;t.__esModule=!0,t.HeadManagerContext=void 0;var o=((n=r("q1tI"))&&n.__esModule?n:{default:n}).default.createContext({});t.HeadManagerContext=o},IDDC:function(e,t,r){"use strict";r.r(t);var n,o=r("nKUr"),a=r("lwsE"),i=r.n(a),s=r("W8MJ"),c=r.n(s),u=r("7W2i"),d=r.n(u),f=r("a1gu"),l=r.n(f),p=r("Nsbk"),h=r.n(p),v=r("q1tI"),g=r("kMSe"),y=r("zrgt"),m=r("QL3K"),b=r("QcND"),j=r("J0pL");function x(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=h()(e);if(t){var o=h()(this).constructor;r=Reflect.construct(n,arguments,o)}else r=n.apply(this,arguments);return l()(this,r)}}var w=Object(g.c)("site")(n=Object(g.c)("user")(n=Object(g.d)(n=function(e){d()(r,e);var t=x(r);function r(){return i()(this,r),t.apply(this,arguments)}return c()(r,[{key:"render",value:function(){var e,t;return Object(o.jsx)(j.a,{h5:Object(o.jsx)(y.a,{}),pc:Object(o.jsx)(m.a,{}),title:null!==(e=this.props.user)&&void 0!==e&&e.targetUserNickname?"".concat(null===(t=this.props.user)||void 0===t?void 0:t.targetUserNickname,"\u7684\u4e3b\u9875"):"\u4ed6\u4eba\u4e3b\u9875"})}}]),r}(v.Component))||n)||n)||n;t.default=Object(b.a)(w)},J0pL:function(e,t,r){"use strict";r("pNMO"),r("4Brf");var n,o=r("nKUr"),a=r("lwsE"),i=r.n(a),s=r("W8MJ"),c=r.n(s),u=r("7W2i"),d=r.n(u),f=r("a1gu"),l=r.n(f),p=r("Nsbk"),h=r.n(p),v=r("q1tI"),g=r.n(v),y=r("kMSe"),m=r("4xcg");function b(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=h()(e);if(t){var o=h()(this).constructor;r=Reflect.construct(n,arguments,o)}else r=n.apply(this,arguments);return l()(this,r)}}var j=Object(y.c)("site")(n=Object(y.c)("search")(n=Object(y.d)(n=function(e){d()(r,e);var t=b(r);function r(){return i()(this,r),t.apply(this,arguments)}return c()(r,[{key:"renderView",value:function(){var e=this.props,t=e.pc,r=e.h5,n=e.title,a=void 0===n?"":n,i=e.keywords,s=void 0===i?"":i,c=e.description,u=void 0===c?"":c,d=e.showSiteName,f=void 0===d||d,l="pc"===this.props.site.platform?t||null:r||null;return Object(o.jsxs)(o.Fragment,{children:[Object(o.jsx)(m.a,{title:a,keywords:s,description:u,showSiteName:f}),l]})}},{key:"render",value:function(){return this.renderView()}}]),r}(g.a.Component))||n)||n)||n;t.a=j},Q8RO:function(e,t,r){(window.__NEXT_P=window.__NEXT_P||[]).push(["/user/[id]",function(){return r("IDDC")}])},QL3K:function(e,t,r){"use strict";r("ma9I"),r("E9XD"),r("tkto"),r("B6y2"),r("rB9j"),r("UxlC");var n,o=r("nKUr"),a=r("RIqP"),i=r.n(a),s=r("o0o1"),c=r.n(s),u=(r("ls82"),r("yXPU")),d=r.n(u),f=r("lwsE"),l=r.n(f),p=r("W8MJ"),h=r.n(p),v=r("7W2i"),g=r.n(v),y=r("a1gu"),m=r.n(y),b=r("Nsbk"),j=r.n(b),x=r("q1tI"),w=r.n(x),O=r("QV9d"),S=r("kMSe"),k=r("TSYQ"),I=r.n(k),U=r("INMq"),C=r("WNcC"),P=r("wXCO"),M=r("20a2"),R=r("9XgB"),N=r("7KEy"),T=r("B5JU"),B=r.n(T),L=r("Niza"),D=r("TC67");function E(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=j()(e);if(t){var o=j()(this).constructor;r=Reflect.construct(n,arguments,o)}else r=n.apply(this,arguments);return m()(this,r)}}var _=Object(S.c)("site")(n=Object(S.c)("index")(n=Object(S.c)("user")(n=Object(S.d)(n=function(e){g()(r,e);var t=E(r);function r(e){var n;l()(this,r),(n=t.call(this,e)).targetUserId=null,n.fansPopupInstance=null,n.followsPopupInstance=null,n.componentDidMount=d()(c.a.mark((function e(){var t,r,o;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=n.props.router.query,o=null===(t=n.props.user)||void 0===t?void 0:t.id,r.id&&"undefined"!==r.id||B.a.replace({url:"/"}),String(o)!==r.id){e.next=6;break}return B.a.replace({url:"/my"}),e.abrupt("return");case 6:if(!r.id){e.next=11;break}return n.targetUserId=r.id,e.next=10,n.props.user.getTargetUserInfo({userId:r.id});case 10:n.setState({fetchUserInfoLoading:!1});case 11:case"end":return e.stop()}}),e)}))),n.componentDidUpdate=d()(c.a.mark((function e(){var t,r,o;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=n.props.router.query,o=null===(t=n.props.user)||void 0===t?void 0:t.id,String(o)!==r.id){e.next=5;break}return B.a.replace({url:"/my"}),e.abrupt("return");case 5:if(String(n.targetUserId)!==String(r.id)){e.next=7;break}return e.abrupt("return");case 7:if(n.targetUserId=r.id,!r.id){e.next=18;break}return n.fansPopupInstance&&n.fansPopupInstance.closePopup(),n.followsPopupInstance&&n.followsPopupInstance.closePopup(),n.props.user.targetUsers[r.id]||n.setState({fetchUserInfoLoading:!0}),n.setState({fetchUserThreadsLoading:!0}),e.next=15,n.props.user.getTargetUserInfo({userId:r.id});case 15:return n.setState({fetchUserInfoLoading:!1}),e.next=18,n.fetchTargetUserThreads();case 18:case"end":return e.stop()}}),e)}))),n.fetchTargetUserThreads=d()(c.a.mark((function e(){var t,r;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!(t=n.props.router.query).id){e.next=7;break}return e.next=4,n.props.index.fetchList({namespace:"user/".concat(t.id),filter:{toUserId:t.id,complex:5}});case 4:r=e.sent,n.props.index.setList({namespace:"user/".concat(t.id),data:r}),n.setState({fetchUserThreadsLoading:!1});case 7:return e.abrupt("return");case 8:case"end":return e.stop()}}),e)}))),n.formatUserThreadsData=function(e){return 0===Object.keys(e).length?[]:Object.values(e).reduce((function(e,t){return[].concat(i()(e),i()(t))}))},n.moreFans=function(){n.setState({showFansPopup:!0})},n.moreFollow=function(){n.setState({showFollowPopup:!0})},n.onSearch=function(e){n.props.router.replace("/search?keyword=".concat(e))},n.renderRight=function(){var e=n.props.router.query,t=null===e||void 0===e?void 0:e.id;return Object(o.jsxs)(o.Fragment,{children:[Object(o.jsx)(R.a,{userId:t,getRef:function(e){return n.fansPopupInstance=e}}),Object(o.jsx)(N.a,{userId:t,getRef:function(e){return n.followsPopupInstance=e}}),Object(o.jsx)(P.a,{})]})},n.renderContent=function(){var e=n.state.fetchUserThreadsLoading,t=n.props.index,r=(t.lists,n.props.router.query),a=void 0===r?{}:r,i=t.getList({namespace:"user/".concat(a.id)}),s=t.getAttribute({namespace:"user/".concat(a.id),key:"totalCount"}),c=t.getListRequestError({namespace:"user/".concat(a.id)});return Object(o.jsx)("div",{className:O.a.userContent,children:Object(o.jsx)(C.a,{title:"\u4e3b\u9898",type:"normal",bigSize:!0,isShowMore:!1,isLoading:e,leftNum:void 0!==s?"".concat(s,"\u4e2a\u4e3b\u9898"):"",noData:!(null!==i&&void 0!==i&&i.length),mold:"plane",isError:c.isError,errorText:c.errorText,children:i.length>0&&Object(o.jsx)(L.a,{data:i})})})};var a=n.props.router.query;return n.state={showFansPopup:!1,showFollowPopup:!1,fetchUserInfoLoading:!0,fetchUserThreadsLoading:!0},n.props.user.targetUsers[a.id]&&(n.state.fetchUserInfoLoading=!1),n}return h()(r,[{key:"render",value:function(){var e=this.state.fetchUserInfoLoading,t=this.props.index,r=(t.lists,this.props.router.query),n=void 0===r?{}:r,a=t.getList({namespace:"user/".concat(n.id)}),i=t.getAttribute({namespace:"user/".concat(n.id),key:"totalPage"}),s=t.getAttribute({namespace:"user/".concat(n.id),key:"currentPage"});return Object(o.jsx)(o.Fragment,{children:Object(o.jsxs)(U.a,{allowRefresh:!1,onRefresh:this.fetchTargetUserThreads,noMore:i<s,showRefresh:!1,onSearch:this.onSearch,immediateCheck:!0,isShowLayoutRefresh:!(null===a||void 0===a||!a.length)&&!e,showHeaderLoading:e,children:[Object(o.jsx)("div",{children:Object(o.jsx)("div",{children:Object(o.jsx)("div",{className:O.a.headerbox,children:Object(o.jsx)("div",{className:O.a.userHeader,children:Object(o.jsx)(D.a,{showHeaderLoading:e,isOtherPerson:!0})})})})}),Object(o.jsxs)("div",{className:O.a.userCenterBody,children:[Object(o.jsx)("div",{className:I()(O.a.userCenterBodyItem,O.a.userCenterBodyLeftItem),children:this.renderContent()}),Object(o.jsx)("div",{className:I()(O.a.userCenterBodyItem,O.a.userCenterBodyRightItem),children:this.renderRight()})]})]})})}}]),r}(w.a.Component))||n)||n)||n)||n;t.a=Object(M.withRouter)(_)},Xuae:function(e,t,r){"use strict";var n=r("RIqP"),o=r("lwsE"),a=r("W8MJ"),i=(r("PJYZ"),r("7W2i")),s=r("a1gu"),c=r("Nsbk");function u(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=c(e);if(t){var o=c(this).constructor;r=Reflect.construct(n,arguments,o)}else r=n.apply(this,arguments);return s(this,r)}}t.__esModule=!0,t.default=void 0;var d=r("q1tI"),f=function(e){i(r,e);var t=u(r);function r(e){var a;return o(this,r),(a=t.call(this,e))._hasHeadManager=void 0,a.emitChange=function(){a._hasHeadManager&&a.props.headManager.updateHead(a.props.reduceComponentsToState(n(a.props.headManager.mountedInstances),a.props))},a._hasHeadManager=a.props.headManager&&a.props.headManager.mountedInstances,a}return a(r,[{key:"componentDidMount",value:function(){this._hasHeadManager&&this.props.headManager.mountedInstances.add(this),this.emitChange()}},{key:"componentDidUpdate",value:function(){this.emitChange()}},{key:"componentWillUnmount",value:function(){this._hasHeadManager&&this.props.headManager.mountedInstances.delete(this),this.emitChange()}},{key:"render",value:function(){return null}}]),r}(d.Component);t.default=f},"dG/n":function(e,t,r){var n=r("Qo9l"),o=r("UTVS"),a=r("5Tg+"),i=r("m/L8").f;e.exports=function(e){var t=n.Symbol||(n.Symbol={});o(t,e)||i(t,e,{value:a.f(e)})}},g4pe:function(e,t,r){e.exports=r("8Kt/")},lwAK:function(e,t,r){"use strict";var n;t.__esModule=!0,t.AmpStateContext=void 0;var o=((n=r("q1tI"))&&n.__esModule?n:{default:n}).default.createContext({});t.AmpStateContext=o},pNMO:function(e,t,r){"use strict";var n=r("I+eb"),o=r("2oRo"),a=r("0GbY"),i=r("xDBR"),s=r("g6v/"),c=r("STAE"),u=r("/b8u"),d=r("0Dky"),f=r("UTVS"),l=r("6LWA"),p=r("hh1v"),h=r("glrk"),v=r("ewvW"),g=r("/GqU"),y=r("wE6v"),m=r("XGwC"),b=r("fHMY"),j=r("33Wh"),x=r("JBy8"),w=r("BX/b"),O=r("dBg+"),S=r("Bs8V"),k=r("m/L8"),I=r("0eef"),U=r("kRJp"),C=r("busE"),P=r("VpIT"),M=r("93I0"),R=r("0BK2"),N=r("kOOl"),T=r("tiKp"),B=r("5Tg+"),L=r("dG/n"),D=r("1E5z"),E=r("afO8"),_=r("tycR").forEach,q=M("hidden"),A="Symbol",J=T("toPrimitive"),W=E.set,H=E.getterFor(A),K=Object.prototype,F=o.Symbol,X=a("JSON","stringify"),V=S.f,Q=k.f,G=w.f,Y=I.f,z=P("symbols"),$=P("op-symbols"),Z=P("string-to-symbol-registry"),ee=P("symbol-to-string-registry"),te=P("wks"),re=o.QObject,ne=!re||!re.prototype||!re.prototype.findChild,oe=s&&d((function(){return 7!=b(Q({},"a",{get:function(){return Q(this,"a",{value:7}).a}})).a}))?function(e,t,r){var n=V(K,t);n&&delete K[t],Q(e,t,r),n&&e!==K&&Q(K,t,n)}:Q,ae=function(e,t){var r=z[e]=b(F.prototype);return W(r,{type:A,tag:e,description:t}),s||(r.description=t),r},ie=u?function(e){return"symbol"==typeof e}:function(e){return Object(e)instanceof F},se=function(e,t,r){e===K&&se($,t,r),h(e);var n=y(t,!0);return h(r),f(z,n)?(r.enumerable?(f(e,q)&&e[q][n]&&(e[q][n]=!1),r=b(r,{enumerable:m(0,!1)})):(f(e,q)||Q(e,q,m(1,{})),e[q][n]=!0),oe(e,n,r)):Q(e,n,r)},ce=function(e,t){h(e);var r=g(t),n=j(r).concat(le(r));return _(n,(function(t){s&&!ue.call(r,t)||se(e,t,r[t])})),e},ue=function(e){var t=y(e,!0),r=Y.call(this,t);return!(this===K&&f(z,t)&&!f($,t))&&(!(r||!f(this,t)||!f(z,t)||f(this,q)&&this[q][t])||r)},de=function(e,t){var r=g(e),n=y(t,!0);if(r!==K||!f(z,n)||f($,n)){var o=V(r,n);return!o||!f(z,n)||f(r,q)&&r[q][n]||(o.enumerable=!0),o}},fe=function(e){var t=G(g(e)),r=[];return _(t,(function(e){f(z,e)||f(R,e)||r.push(e)})),r},le=function(e){var t=e===K,r=G(t?$:g(e)),n=[];return _(r,(function(e){!f(z,e)||t&&!f(K,e)||n.push(z[e])})),n};(c||(C((F=function(){if(this instanceof F)throw TypeError("Symbol is not a constructor");var e=arguments.length&&void 0!==arguments[0]?String(arguments[0]):void 0,t=N(e),r=function(e){this===K&&r.call($,e),f(this,q)&&f(this[q],t)&&(this[q][t]=!1),oe(this,t,m(1,e))};return s&&ne&&oe(K,t,{configurable:!0,set:r}),ae(t,e)}).prototype,"toString",(function(){return H(this).tag})),C(F,"withoutSetter",(function(e){return ae(N(e),e)})),I.f=ue,k.f=se,S.f=de,x.f=w.f=fe,O.f=le,B.f=function(e){return ae(T(e),e)},s&&(Q(F.prototype,"description",{configurable:!0,get:function(){return H(this).description}}),i||C(K,"propertyIsEnumerable",ue,{unsafe:!0}))),n({global:!0,wrap:!0,forced:!c,sham:!c},{Symbol:F}),_(j(te),(function(e){L(e)})),n({target:A,stat:!0,forced:!c},{for:function(e){var t=String(e);if(f(Z,t))return Z[t];var r=F(t);return Z[t]=r,ee[r]=t,r},keyFor:function(e){if(!ie(e))throw TypeError(e+" is not a symbol");if(f(ee,e))return ee[e]},useSetter:function(){ne=!0},useSimple:function(){ne=!1}}),n({target:"Object",stat:!0,forced:!c,sham:!s},{create:function(e,t){return void 0===t?b(e):ce(b(e),t)},defineProperty:se,defineProperties:ce,getOwnPropertyDescriptor:de}),n({target:"Object",stat:!0,forced:!c},{getOwnPropertyNames:fe,getOwnPropertySymbols:le}),n({target:"Object",stat:!0,forced:d((function(){O.f(1)}))},{getOwnPropertySymbols:function(e){return O.f(v(e))}}),X)&&n({target:"JSON",stat:!0,forced:!c||d((function(){var e=F();return"[null]"!=X([e])||"{}"!=X({a:e})||"{}"!=X(Object(e))}))},{stringify:function(e,t,r){for(var n,o=[e],a=1;arguments.length>a;)o.push(arguments[a++]);if(n=t,(p(t)||void 0!==e)&&!ie(e))return l(t)||(t=function(e,t){if("function"==typeof n&&(t=n.call(this,e,t)),!ie(t))return t}),o[1]=t,X.apply(null,o)}});F.prototype[J]||U(F.prototype,J,F.prototype.valueOf),D(F,A),R[q]=!0},zrgt:function(e,t,r){"use strict";r("ma9I"),r("E9XD"),r("tkto"),r("B6y2"),r("rB9j"),r("UxlC"),r("R5XZ");var n,o=r("nKUr"),a=r("RIqP"),i=r.n(a),s=r("o0o1"),c=r.n(s),u=(r("ls82"),r("yXPU")),d=r.n(u),f=r("lwsE"),l=r.n(f),p=r("W8MJ"),h=r.n(p),v=r("7W2i"),g=r.n(v),y=r("a1gu"),m=r.n(y),b=r("Nsbk"),j=r.n(b),x=r("q1tI"),w=r.n(x),O=r("/YC7"),S=r("x2xJ"),k=r.n(S),I=r("jqTq"),U=r.n(I),C=r("Jw8/"),P=r.n(C),M=r("GuWI"),R=r("QLYM"),N=r("kMSe"),T=(r("E+SJ"),r("JhUJ"),r("Niza")),B=r("INMq"),L=(r("HoAE"),r("20a2")),D=r("B5JU"),E=r.n(D),_=r("Tk/S");function q(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=j()(e);if(t){var o=j()(this).constructor;r=Reflect.construct(n,arguments,o)}else r=n.apply(this,arguments);return m()(this,r)}}var A=Object(N.c)("site")(n=Object(N.c)("user")(n=Object(N.c)("index")(n=Object(N.d)(n=function(e){g()(r,e);var t=q(r);function r(e){var n;l()(this,r),(n=t.call(this,e)).targetUserId=null,n.componentDidMount=d()(c.a.mark((function e(){var t,r,o;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=n.props.router.query,o=null===(t=n.props.user)||void 0===t?void 0:t.id,r.id&&"undefined"!==r.id||E.a.replace({url:"/"}),String(o)!==r.id){e.next=6;break}return E.a.replace({url:"/my"}),e.abrupt("return");case 6:if(!r.id){e.next=12;break}return e.next=9,n.props.user.getTargetUserInfo({userId:r.id});case 9:n.setWeixinShare(),n.targetUserId=r.id,n.setState({fetchUserInfoLoading:!1});case 12:case"end":return e.stop()}}),e)}))),n.componentDidUpdate=d()(c.a.mark((function e(){var t,r,o;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=n.props.router.query,o=null===(t=n.props.user)||void 0===t?void 0:t.id,r.id&&"undefined"!==r.id||E.a.replace({url:"/"}),String(o)!==r.id){e.next=6;break}return E.a.replace({url:"/my"}),e.abrupt("return");case 6:if(n.targetUserId){e.next=8;break}return e.abrupt("return");case 8:if(String(n.targetUserId)!==String(r.id)){e.next=10;break}return e.abrupt("return");case 10:if(n.targetUserId=r.id,!r.id){e.next=20;break}return n.props.user.targetUsers[r.id]||n.setState({fetchUserInfoLoading:!0}),n.setState({fetchUserThreadsLoading:!0}),e.next=16,n.props.user.getTargetUserInfo({userId:r.id});case 16:return n.setWeixinShare(),n.setState({fetchUserInfoLoading:!1}),e.next=20,n.fetchTargetUserThreads();case 20:case"end":return e.stop()}}),e)}))),n.fetchTargetUserThreads=d()(c.a.mark((function e(){var t,r;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!(t=n.props.router.query).id){e.next=6;break}return e.next=4,n.props.index.fetchList({namespace:"user/".concat(t.id),filter:{toUserId:t.id,complex:5}});case 4:r=e.sent,n.props.index.setList({namespace:"user/".concat(t.id),data:r});case 6:return e.abrupt("return");case 7:case"end":return e.stop()}}),e)}))),n.formatUserThreadsData=function(e){return 0===Object.keys(e).length?[]:Object.values(e).reduce((function(e,t){return[].concat(i()(e),i()(t))}))},n.handlePreviewBgImage=function(e){e&&e.stopPropagation(),n.setState({isPreviewBgVisible:!n.state.isPreviewBgVisible})},n.getBackgroundUrl=function(){var e,t=null,r=n.props.router.query,o=null===r||void 0===r?void 0:r.id;return o&&null!==(e=n.props.user)&&void 0!==e&&e.targetUsers[o]&&(t=n.props.user.targetUsers[o].originalBackGroundUrl),t||!1};var o=n.props.router.query;return n.state={fetchUserInfoLoading:!0,isPreviewBgVisible:!1},n.props.user.targetUsers[o.id]&&(n.state.fetchUserInfoLoading=!1),n}return h()(r,[{key:"setWeixinShare",value:function(){var e=this;setTimeout((function(){var t=e.props.user.targetUser;if(t){var r=t.nickname,n=t.avatarUrl,o=t.signature,a=t.id,i="".concat(r,"\u7684\u4e3b\u9875"),s=n,c=o?o.length>35?"".concat(o.substr(0,35),"..."):o:"\u5728\u8fd9\u91cc\uff0c\u53d1\u73b0\u66f4\u591a\u7cbe\u5f69\u5185\u5bb9",u="".concat(window.location.origin,"/user/").concat(a);Object(_.a)(i,c,u,s)}}),500)}},{key:"render",value:function(){var e=this.props,t=e.site,r=(e.user,t.platform),n=this.props.index,a=(n.lists,this.props.router.query),i=void 0===a?{}:a,s=n.getList({namespace:"user/".concat(i.id)}),c=n.getAttribute({namespace:"user/".concat(i.id),key:"totalPage"}),u=n.getAttribute({namespace:"user/".concat(i.id),key:"totalCount"}),d=n.getAttribute({namespace:"user/".concat(i.id),key:"currentPage"});return Object(o.jsxs)(B.a,{showHeader:!0,showTabBar:!1,immediateCheck:!0,onRefresh:this.fetchTargetUserThreads,noMore:c<d,showRefresh:!this.state.fetchUserInfoLoading,children:[Object(o.jsxs)("div",{className:O.a.mobileLayout,children:[this.state.fetchUserInfoLoading&&Object(o.jsx)("div",{className:O.a.loadingSpin,children:Object(o.jsx)(U.a,{type:"spinner",children:"\u52a0\u8f7d\u4e2d..."})}),!this.state.fetchUserInfoLoading&&Object(o.jsxs)(o.Fragment,{children:[Object(o.jsx)("div",{onClick:this.handlePreviewBgImage,children:Object(o.jsx)(M.a,{isOtherPerson:!0})}),Object(o.jsx)(R.a,{platform:r,isOtherPerson:!0})]}),Object(o.jsxs)("div",{className:O.a.unit,children:[Object(o.jsxs)("div",{className:O.a.threadUnit,children:[Object(o.jsx)("div",{className:O.a.threadTitle,children:"\u4e3b\u9898"}),Object(o.jsx)("div",{className:O.a.threadCount,children:void 0===u?"":"".concat(u,"\u4e2a\u4e3b\u9898")})]}),Object(o.jsx)("div",{className:O.a.dividerContainer,children:Object(o.jsx)(k.a,{className:O.a.divider})}),Object(o.jsx)("div",{className:O.a.threadItemContainer,children:s.length>0&&Object(o.jsx)(T.a,{data:s})})]})]}),this.getBackgroundUrl()&&this.state.isPreviewBgVisible&&Object(o.jsx)(P.a,{visible:this.state.isPreviewBgVisible,onClose:this.handlePreviewBgImage,imgUrls:[this.getBackgroundUrl()],currentUrl:this.getBackgroundUrl()})]})}}]),r}(w.a.Component))||n)||n)||n)||n;t.a=Object(L.withRouter)(A)}},[["Q8RO",1,0,3,5,4,6,7,2]]]);