_N_E=(window.webpackJsonp_N_E=window.webpackJsonp_N_E||[]).push([[40],{"91s+":function(e,t,r){(window.__NEXT_P=window.__NEXT_P||[]).push(["/my/others",function(){return r("SZdU")}])},QL3K:function(e,t,r){"use strict";r("ma9I"),r("E9XD"),r("tkto"),r("B6y2"),r("rB9j"),r("UxlC");var n,s=r("nKUr"),a=r("RIqP"),i=r.n(a),o=r("o0o1"),c=r.n(o),u=(r("ls82"),r("yXPU")),d=r.n(u),l=r("lwsE"),p=r.n(l),f=r("W8MJ"),h=r.n(f),g=r("7W2i"),v=r.n(g),b=r("a1gu"),j=r.n(b),m=r("Nsbk"),x=r.n(m),U=r("q1tI"),y=r.n(U),w=r("QV9d"),O=r("kMSe"),I=r("TSYQ"),k=r.n(I),T=r("INMq"),P=r("WNcC"),S=r("wXCO"),L=r("20a2"),R=r("9XgB"),N=r("7KEy"),B=r("B5JU"),C=r.n(B),q=r("Niza"),D=r("TC67");function E(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=x()(e);if(t){var s=x()(this).constructor;r=Reflect.construct(n,arguments,s)}else r=n.apply(this,arguments);return j()(this,r)}}var M=Object(O.c)("site")(n=Object(O.c)("index")(n=Object(O.c)("user")(n=Object(O.d)(n=function(e){v()(r,e);var t=E(r);function r(e){var n;return p()(this,r),(n=t.call(this,e)).targetUserId=null,n.fansPopupInstance=null,n.followsPopupInstance=null,n.componentDidMount=d()(c.a.mark((function e(){var t,r,s;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=n.props.router.query,s=null===(t=n.props.user)||void 0===t?void 0:t.id,r.id&&"undefined"!==r.id||C.a.replace({url:"/"}),String(s)!==r.id){e.next=6;break}return C.a.replace({url:"/my"}),e.abrupt("return");case 6:if(!r.id){e.next=11;break}return n.targetUserId=r.id,e.next=10,n.props.user.getTargetUserInfo(r.id);case 10:n.setState({fetchUserInfoLoading:!1});case 11:case"end":return e.stop()}}),e)}))),n.componentDidUpdate=d()(c.a.mark((function e(){var t,r,s;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=n.props.router.query,s=null===(t=n.props.user)||void 0===t?void 0:t.id,String(s)!==r.id){e.next=5;break}return C.a.replace({url:"/my"}),e.abrupt("return");case 5:if(String(n.targetUserId)!==String(r.id)){e.next=7;break}return e.abrupt("return");case 7:if(n.targetUserId=r.id,!r.id){e.next=18;break}return n.fansPopupInstance&&n.fansPopupInstance.closePopup(),n.followsPopupInstance&&n.followsPopupInstance.closePopup(),n.setState({fetchUserInfoLoading:!0,fetchUserThreadsLoading:!0}),n.props.user.removeTargetUserInfo(),e.next=15,n.props.user.getTargetUserInfo(r.id);case 15:return n.setState({fetchUserInfoLoading:!1}),e.next=18,n.fetchTargetUserThreads();case 18:case"end":return e.stop()}}),e)}))),n.fetchTargetUserThreads=d()(c.a.mark((function e(){var t,r;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!(t=n.props.router.query).id){e.next=7;break}return e.next=4,n.props.index.fetchList({namespace:"user/".concat(t.id),filter:{toUserId:t.id,complex:5}});case 4:r=e.sent,n.props.index.setList({namespace:"user/".concat(t.id),data:r}),n.setState({fetchUserThreadsLoading:!1});case 7:return e.abrupt("return");case 8:case"end":return e.stop()}}),e)}))),n.formatUserThreadsData=function(e){return 0===Object.keys(e).length?[]:Object.values(e).reduce((function(e,t){return[].concat(i()(e),i()(t))}))},n.moreFans=function(){n.setState({showFansPopup:!0})},n.moreFollow=function(){n.setState({showFollowPopup:!0})},n.onSearch=function(e){n.props.router.replace("/search?keyword=".concat(e))},n.renderRight=function(){var e=n.props.router.query,t=null===e||void 0===e?void 0:e.id;return Object(s.jsxs)(s.Fragment,{children:[Object(s.jsx)(R.a,{userId:t,getRef:function(e){return n.fansPopupInstance=e}}),Object(s.jsx)(N.a,{userId:t,getRef:function(e){return n.followsPopupInstance=e}}),Object(s.jsx)(S.a,{})]})},n.renderContent=function(){var e=n.state.fetchUserThreadsLoading,t=n.props.index,r=(t.lists,n.props.router.query),a=void 0===r?{}:r,i=t.getList({namespace:"user/".concat(a.id)}),o=t.getAttribute({namespace:"user/".concat(a.id),key:"totalCount"}),c=t.getListRequestError({namespace:"user/".concat(a.id)});return Object(s.jsx)("div",{className:w.a.userContent,children:Object(s.jsx)(P.a,{title:"\u4e3b\u9898",type:"normal",bigSize:!0,isShowMore:!1,isLoading:e,leftNum:void 0!==o?"".concat(o,"\u4e2a\u4e3b\u9898"):"",noData:!(null!==i&&void 0!==i&&i.length),mold:"plane",isError:c.isError,errorText:c.errorText,children:i.length>0&&Object(s.jsx)(q.a,{data:i})})})},n.props.user.cleanTargetUserThreads(),n.state={showFansPopup:!1,showFollowPopup:!1,fetchUserInfoLoading:!0,fetchUserThreadsLoading:!0},n}return h()(r,[{key:"componentWillUnmount",value:function(){this.props.user.removeTargetUserInfo()}},{key:"render",value:function(){var e=this.state.fetchUserInfoLoading,t=this.props.index,r=(t.lists,this.props.router.query),n=void 0===r?{}:r,a=t.getList({namespace:"user/".concat(n.id)}),i=t.getAttribute({namespace:"user/".concat(n.id),key:"totalPage"}),o=t.getAttribute({namespace:"user/".concat(n.id),key:"currentPage"});return Object(s.jsx)(s.Fragment,{children:Object(s.jsxs)(T.a,{isOtherPerson:!0,allowRefresh:!1,onRefresh:this.fetchTargetUserThreads,noMore:i<o,showRefresh:!1,onSearch:this.onSearch,immediateCheck:!0,isShowLayoutRefresh:!(null===a||void 0===a||!a.length)&&!e,showHeaderLoading:e,children:[Object(s.jsx)("div",{children:Object(s.jsx)("div",{children:Object(s.jsx)("div",{className:w.a.headerbox,children:Object(s.jsx)("div",{className:w.a.userHeader,children:Object(s.jsx)(D.a,{showHeaderLoading:e})})})})}),Object(s.jsxs)("div",{className:w.a.userCenterBody,children:[Object(s.jsx)("div",{className:k()(w.a.userCenterBodyItem,w.a.userCenterBodyLeftItem),children:this.renderContent()}),Object(s.jsx)("div",{className:k()(w.a.userCenterBodyItem,w.a.userCenterBodyRightItem),children:this.renderRight()})]})]})})}}]),r}(y.a.Component))||n)||n)||n)||n;t.a=Object(L.withRouter)(M)},SZdU:function(e,t,r){"use strict";r.r(t);var n,s=r("nKUr"),a=r("lwsE"),i=r.n(a),o=r("W8MJ"),c=r.n(o),u=r("7W2i"),d=r.n(u),l=r("a1gu"),p=r.n(l),f=r("Nsbk"),h=r.n(f),g=r("q1tI"),v=r("kMSe"),b=r("zrgt"),j=r("QL3K"),m=r("QcND");function x(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=h()(e);if(t){var s=h()(this).constructor;r=Reflect.construct(n,arguments,s)}else r=n.apply(this,arguments);return p()(this,r)}}var U=Object(v.c)("site")(n=Object(v.d)(n=function(e){d()(r,e);var t=x(r);function r(){return i()(this,r),t.apply(this,arguments)}return c()(r,[{key:"render",value:function(){return"h5"===this.props.site.platform?Object(s.jsx)(b.a,{}):Object(s.jsx)(j.a,{})}}]),r}(g.Component))||n)||n;t.default=Object(m.a)(U)},zrgt:function(e,t,r){"use strict";r("ma9I"),r("E9XD"),r("tkto"),r("B6y2"),r("rB9j"),r("UxlC"),r("R5XZ");var n,s=r("nKUr"),a=r("RIqP"),i=r.n(a),o=r("o0o1"),c=r.n(o),u=(r("ls82"),r("yXPU")),d=r.n(u),l=r("lwsE"),p=r.n(l),f=r("W8MJ"),h=r.n(f),g=r("7W2i"),v=r.n(g),b=r("a1gu"),j=r.n(b),m=r("Nsbk"),x=r.n(m),U=r("q1tI"),y=r.n(U),w=r("/YC7"),O=r("x2xJ"),I=r.n(O),k=r("jqTq"),T=r.n(k),P=r("Jw8/"),S=r.n(P),L=r("GuWI"),R=r("QLYM"),N=r("kMSe"),B=(r("E+SJ"),r("JhUJ"),r("Niza")),C=r("INMq"),q=(r("HoAE"),r("20a2")),D=r("B5JU"),E=r.n(D),M=r("Tk/S");function W(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=x()(e);if(t){var s=x()(this).constructor;r=Reflect.construct(n,arguments,s)}else r=n.apply(this,arguments);return j()(this,r)}}var J=Object(N.c)("site")(n=Object(N.c)("user")(n=Object(N.c)("index")(n=Object(N.d)(n=function(e){v()(r,e);var t=W(r);function r(e){var n;return p()(this,r),(n=t.call(this,e)).targetUserId=null,n.componentDidMount=d()(c.a.mark((function e(){var t,r,s;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=n.props.router.query,s=null===(t=n.props.user)||void 0===t?void 0:t.id,r.id&&"undefined"!==r.id||E.a.replace({url:"/"}),String(s)!==r.id){e.next=6;break}return E.a.replace({url:"/my"}),e.abrupt("return");case 6:if(!r.id){e.next=12;break}return e.next=9,n.props.user.getTargetUserInfo(r.id);case 9:n.setWeixinShare(),n.targetUserId=r.id,n.setState({fetchUserInfoLoading:!1});case 12:case"end":return e.stop()}}),e)}))),n.componentDidUpdate=d()(c.a.mark((function e(){var t,r,s;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=n.props.router.query,s=null===(t=n.props.user)||void 0===t?void 0:t.id,r.id&&"undefined"!==r.id||E.a.replace({url:"/"}),String(s)!==r.id){e.next=6;break}return E.a.replace({url:"/my"}),e.abrupt("return");case 6:if(n.targetUserId){e.next=8;break}return e.abrupt("return");case 8:if(String(n.targetUserId)!==String(r.id)){e.next=10;break}return e.abrupt("return");case 10:if(n.targetUserId=r.id,!r.id){e.next=20;break}return n.setState({fetchUserInfoLoading:!0,fetchUserThreadsLoading:!0}),n.props.user.removeTargetUserInfo(),e.next=16,n.props.user.getTargetUserInfo(r.id);case 16:return n.setWeixinShare(),n.setState({fetchUserInfoLoading:!1}),e.next=20,n.fetchTargetUserThreads();case 20:case"end":return e.stop()}}),e)}))),n.fetchTargetUserThreads=d()(c.a.mark((function e(){var t,r;return c.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!(t=n.props.router.query).id){e.next=6;break}return e.next=4,n.props.index.fetchList({namespace:"user/".concat(t.id),filter:{toUserId:t.id,complex:5}});case 4:r=e.sent,n.props.index.setList({namespace:"user/".concat(t.id),data:r});case 6:return e.abrupt("return");case 7:case"end":return e.stop()}}),e)}))),n.formatUserThreadsData=function(e){return 0===Object.keys(e).length?[]:Object.values(e).reduce((function(e,t){return[].concat(i()(e),i()(t))}))},n.handlePreviewBgImage=function(e){e&&e.stopPropagation(),n.setState({isPreviewBgVisible:!n.state.isPreviewBgVisible})},n.getBackgroundUrl=function(){var e,t,r=null;null!==(e=n.props.user)&&void 0!==e&&e.targetOriginalBackGroundUrl&&(r=null===(t=n.props.user)||void 0===t?void 0:t.targetOriginalBackGroundUrl);return r||!1},n.props.user.cleanTargetUserThreads(),n.state={fetchUserInfoLoading:!0,isPreviewBgVisible:!1},n}return h()(r,[{key:"setWeixinShare",value:function(){var e=this;setTimeout((function(){var t=e.props.user.targetUser;if(t){var r=t.nickname,n=t.avatarUrl,s=t.signature,a=t.id,i="".concat(r,"\u7684\u4e3b\u9875"),o=n,c=s?s.length>35?"".concat(s.substr(0,35),"..."):s:"\u5728\u8fd9\u91cc\uff0c\u53d1\u73b0\u66f4\u591a\u7cbe\u5f69\u5185\u5bb9",u="".concat(window.location.origin,"/user/").concat(a);Object(M.a)(i,c,u,o)}}),500)}},{key:"componentWillUnmount",value:function(){this.props.user.removeTargetUserInfo()}},{key:"render",value:function(){var e=this.props,t=e.site,r=(e.user,t.platform),n=this.props.index,a=(n.lists,this.props.router.query),i=void 0===a?{}:a,o=n.getList({namespace:"user/".concat(i.id)}),c=n.getAttribute({namespace:"user/".concat(i.id),key:"totalPage"}),u=n.getAttribute({namespace:"user/".concat(i.id),key:"totalCount"}),d=n.getAttribute({namespace:"user/".concat(i.id),key:"currentPage"});return Object(s.jsxs)(C.a,{showHeader:!0,showTabBar:!1,immediateCheck:!0,onRefresh:this.fetchTargetUserThreads,noMore:c<d,showRefresh:!this.state.fetchUserInfoLoading,children:[Object(s.jsxs)("div",{className:w.a.mobileLayout,children:[this.state.fetchUserInfoLoading&&Object(s.jsx)("div",{className:w.a.loadingSpin,children:Object(s.jsx)(T.a,{type:"spinner",children:"\u52a0\u8f7d\u4e2d..."})}),!this.state.fetchUserInfoLoading&&Object(s.jsxs)(s.Fragment,{children:[Object(s.jsx)("div",{onClick:this.handlePreviewBgImage,children:Object(s.jsx)(L.a,{isOtherPerson:!0})}),Object(s.jsx)(R.a,{platform:r,isOtherPerson:!0})]}),Object(s.jsxs)("div",{className:w.a.unit,children:[Object(s.jsxs)("div",{className:w.a.threadUnit,children:[Object(s.jsx)("div",{className:w.a.threadTitle,children:"\u4e3b\u9898"}),Object(s.jsx)("div",{className:w.a.threadCount,children:void 0===u?"":"".concat(u,"\u4e2a\u4e3b\u9898")})]}),Object(s.jsx)("div",{className:w.a.dividerContainer,children:Object(s.jsx)(I.a,{className:w.a.divider})}),Object(s.jsx)("div",{className:w.a.threadItemContainer,children:o.length>0&&Object(s.jsx)(B.a,{data:o})})]})]}),this.getBackgroundUrl()&&this.state.isPreviewBgVisible&&Object(s.jsx)(S.a,{visible:this.state.isPreviewBgVisible,onClose:this.handlePreviewBgImage,imgUrls:[this.getBackgroundUrl()],currentUrl:this.getBackgroundUrl()})]})}}]),r}(y.a.Component))||n)||n)||n)||n;t.a=Object(q.withRouter)(J)}},[["91s+",1,0,3,5,4,7,2]]]);