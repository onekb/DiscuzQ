_N_E=(window.webpackJsonp_N_E=window.webpackJsonp_N_E||[]).push([[42],{"91s+":function(e,t,r){(window.__NEXT_P=window.__NEXT_P||[]).push(["/my/others",function(){return r("SZdU")}])},QL3K:function(e,t,r){"use strict";r("ma9I"),r("E9XD"),r("tkto"),r("B6y2"),r("rB9j"),r("UxlC");var n,s=r("nKUr"),a=r("RIqP"),i=r.n(a),c=r("o0o1"),o=r.n(c),u=(r("ls82"),r("yXPU")),d=r.n(u),p=r("lwsE"),l=r.n(p),f=r("W8MJ"),h=r.n(f),g=r("7W2i"),b=r.n(g),v=r("a1gu"),j=r.n(v),x=r("Nsbk"),m=r.n(x),U=r("q1tI"),y=r.n(U),I=r("QV9d"),w=r("kMSe"),O=r("TSYQ"),k=r.n(O),S=r("INMq"),P=r("WNcC"),L=r("wXCO"),T=r("20a2"),R=r("9XgB"),N=r("7KEy"),B=r("B5JU"),C=r.n(B),q=r("Niza"),D=r("TC67");function E(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=m()(e);if(t){var s=m()(this).constructor;r=Reflect.construct(n,arguments,s)}else r=n.apply(this,arguments);return j()(this,r)}}var M=Object(w.c)("site")(n=Object(w.c)("index")(n=Object(w.c)("user")(n=Object(w.d)(n=function(e){b()(r,e);var t=E(r);function r(e){var n;l()(this,r),(n=t.call(this,e)).targetUserId=null,n.fansPopupInstance=null,n.followsPopupInstance=null,n.componentDidMount=d()(o.a.mark((function e(){var t,r,s;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=n.props.router.query,s=null===(t=n.props.user)||void 0===t?void 0:t.id,r.id&&"undefined"!==r.id||C.a.replace({url:"/"}),String(s)!==r.id){e.next=6;break}return C.a.replace({url:"/my"}),e.abrupt("return");case 6:if(!r.id){e.next=11;break}return n.targetUserId=r.id,e.next=10,n.props.user.getTargetUserInfo({userId:r.id});case 10:n.setState({fetchUserInfoLoading:!1});case 11:case"end":return e.stop()}}),e)}))),n.componentDidUpdate=d()(o.a.mark((function e(){var t,r,s;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=n.props.router.query,s=null===(t=n.props.user)||void 0===t?void 0:t.id,String(s)!==r.id){e.next=5;break}return C.a.replace({url:"/my"}),e.abrupt("return");case 5:if(String(n.targetUserId)!==String(r.id)){e.next=7;break}return e.abrupt("return");case 7:if(n.targetUserId=r.id,!r.id){e.next=18;break}return n.fansPopupInstance&&n.fansPopupInstance.closePopup(),n.followsPopupInstance&&n.followsPopupInstance.closePopup(),n.props.user.targetUsers[r.id]||n.setState({fetchUserInfoLoading:!0}),n.setState({fetchUserThreadsLoading:!0}),e.next=15,n.props.user.getTargetUserInfo({userId:r.id});case 15:return n.setState({fetchUserInfoLoading:!1}),e.next=18,n.fetchTargetUserThreads();case 18:case"end":return e.stop()}}),e)}))),n.fetchTargetUserThreads=d()(o.a.mark((function e(){var t,r;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!(t=n.props.router.query).id){e.next=7;break}return e.next=4,n.props.index.fetchList({namespace:"user/".concat(t.id),filter:{toUserId:t.id,complex:5}});case 4:r=e.sent,n.props.index.setList({namespace:"user/".concat(t.id),data:r}),n.setState({fetchUserThreadsLoading:!1});case 7:return e.abrupt("return");case 8:case"end":return e.stop()}}),e)}))),n.formatUserThreadsData=function(e){return 0===Object.keys(e).length?[]:Object.values(e).reduce((function(e,t){return[].concat(i()(e),i()(t))}))},n.moreFans=function(){n.setState({showFansPopup:!0})},n.moreFollow=function(){n.setState({showFollowPopup:!0})},n.onSearch=function(e){n.props.router.replace("/search?keyword=".concat(e))},n.renderRight=function(){var e=n.props.router.query,t=null===e||void 0===e?void 0:e.id;return Object(s.jsxs)(s.Fragment,{children:[Object(s.jsx)(R.a,{userId:t,getRef:function(e){return n.fansPopupInstance=e}}),Object(s.jsx)(N.a,{userId:t,getRef:function(e){return n.followsPopupInstance=e}}),Object(s.jsx)(L.a,{})]})},n.renderContent=function(){var e=n.state.fetchUserThreadsLoading,t=n.props.index,r=(t.lists,n.props.router.query),a=void 0===r?{}:r,i=t.getList({namespace:"user/".concat(a.id)}),c=t.getAttribute({namespace:"user/".concat(a.id),key:"totalCount"}),o=t.getListRequestError({namespace:"user/".concat(a.id)});return Object(s.jsx)("div",{className:I.a.userContent,children:Object(s.jsx)(P.a,{title:"\u4e3b\u9898",type:"normal",bigSize:!0,isShowMore:!1,isLoading:e,leftNum:void 0!==c?"".concat(c,"\u4e2a\u4e3b\u9898"):"",noData:!(null!==i&&void 0!==i&&i.length),mold:"plane",isError:o.isError,errorText:o.errorText,children:i.length>0&&Object(s.jsx)(q.a,{data:i})})})};var a=n.props.router.query;return n.state={showFansPopup:!1,showFollowPopup:!1,fetchUserInfoLoading:!0,fetchUserThreadsLoading:!0},n.props.user.targetUsers[a.id]&&(n.state.fetchUserInfoLoading=!1),n}return h()(r,[{key:"render",value:function(){var e=this.state.fetchUserInfoLoading,t=this.props.index,r=(t.lists,this.props.router.query),n=void 0===r?{}:r,a=t.getList({namespace:"user/".concat(n.id)}),i=t.getAttribute({namespace:"user/".concat(n.id),key:"totalPage"}),c=t.getAttribute({namespace:"user/".concat(n.id),key:"currentPage"});return Object(s.jsx)(s.Fragment,{children:Object(s.jsxs)(S.a,{allowRefresh:!1,onRefresh:this.fetchTargetUserThreads,noMore:i<c,showRefresh:!1,onSearch:this.onSearch,immediateCheck:!0,isShowLayoutRefresh:!(null===a||void 0===a||!a.length)&&!e,showHeaderLoading:e,children:[Object(s.jsx)("div",{children:Object(s.jsx)("div",{children:Object(s.jsx)("div",{className:I.a.headerbox,children:Object(s.jsx)("div",{className:I.a.userHeader,children:Object(s.jsx)(D.a,{showHeaderLoading:e,isOtherPerson:!0})})})})}),Object(s.jsxs)("div",{className:I.a.userCenterBody,children:[Object(s.jsx)("div",{className:k()(I.a.userCenterBodyItem,I.a.userCenterBodyLeftItem),children:this.renderContent()}),Object(s.jsx)("div",{className:k()(I.a.userCenterBodyItem,I.a.userCenterBodyRightItem),children:this.renderRight()})]})]})})}}]),r}(y.a.Component))||n)||n)||n)||n;t.a=Object(T.withRouter)(M)},SZdU:function(e,t,r){"use strict";r.r(t);var n,s=r("nKUr"),a=r("lwsE"),i=r.n(a),c=r("W8MJ"),o=r.n(c),u=r("7W2i"),d=r.n(u),p=r("a1gu"),l=r.n(p),f=r("Nsbk"),h=r.n(f),g=r("q1tI"),b=r("kMSe"),v=r("zrgt"),j=r("QL3K"),x=r("QcND");function m(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=h()(e);if(t){var s=h()(this).constructor;r=Reflect.construct(n,arguments,s)}else r=n.apply(this,arguments);return l()(this,r)}}var U=Object(b.c)("site")(n=Object(b.d)(n=function(e){d()(r,e);var t=m(r);function r(){return i()(this,r),t.apply(this,arguments)}return o()(r,[{key:"render",value:function(){return"h5"===this.props.site.platform?Object(s.jsx)(v.a,{}):Object(s.jsx)(j.a,{})}}]),r}(g.Component))||n)||n;t.default=Object(x.a)(U)},zrgt:function(e,t,r){"use strict";r("ma9I"),r("E9XD"),r("tkto"),r("B6y2"),r("rB9j"),r("UxlC"),r("R5XZ");var n,s=r("nKUr"),a=r("RIqP"),i=r.n(a),c=r("o0o1"),o=r.n(c),u=(r("ls82"),r("yXPU")),d=r.n(u),p=r("lwsE"),l=r.n(p),f=r("W8MJ"),h=r.n(f),g=r("7W2i"),b=r.n(g),v=r("a1gu"),j=r.n(v),x=r("Nsbk"),m=r.n(x),U=r("q1tI"),y=r.n(U),I=r("/YC7"),w=r("x2xJ"),O=r.n(w),k=r("jqTq"),S=r.n(k),P=r("Jw8/"),L=r.n(P),T=r("GuWI"),R=r("QLYM"),N=r("kMSe"),B=(r("E+SJ"),r("JhUJ"),r("Niza")),C=r("INMq"),q=(r("HoAE"),r("20a2")),D=r("B5JU"),E=r.n(D),M=r("Tk/S");function J(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var r,n=m()(e);if(t){var s=m()(this).constructor;r=Reflect.construct(n,arguments,s)}else r=n.apply(this,arguments);return j()(this,r)}}var _=Object(N.c)("site")(n=Object(N.c)("user")(n=Object(N.c)("index")(n=Object(N.d)(n=function(e){b()(r,e);var t=J(r);function r(e){var n;l()(this,r),(n=t.call(this,e)).targetUserId=null,n.componentDidMount=d()(o.a.mark((function e(){var t,r,s;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=n.props.router.query,s=null===(t=n.props.user)||void 0===t?void 0:t.id,r.id&&"undefined"!==r.id||E.a.replace({url:"/"}),String(s)!==r.id){e.next=6;break}return E.a.replace({url:"/my"}),e.abrupt("return");case 6:if(!r.id){e.next=12;break}return e.next=9,n.props.user.getTargetUserInfo({userId:r.id});case 9:n.setWeixinShare(),n.targetUserId=r.id,n.setState({fetchUserInfoLoading:!1});case 12:case"end":return e.stop()}}),e)}))),n.componentDidUpdate=d()(o.a.mark((function e(){var t,r,s;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=n.props.router.query,s=null===(t=n.props.user)||void 0===t?void 0:t.id,r.id&&"undefined"!==r.id||E.a.replace({url:"/"}),String(s)!==r.id){e.next=6;break}return E.a.replace({url:"/my"}),e.abrupt("return");case 6:if(n.targetUserId){e.next=8;break}return e.abrupt("return");case 8:if(String(n.targetUserId)!==String(r.id)){e.next=10;break}return e.abrupt("return");case 10:if(n.targetUserId=r.id,!r.id){e.next=20;break}return n.props.user.targetUsers[r.id]||n.setState({fetchUserInfoLoading:!0}),n.setState({fetchUserThreadsLoading:!0}),e.next=16,n.props.user.getTargetUserInfo({userId:r.id});case 16:return n.setWeixinShare(),n.setState({fetchUserInfoLoading:!1}),e.next=20,n.fetchTargetUserThreads();case 20:case"end":return e.stop()}}),e)}))),n.fetchTargetUserThreads=d()(o.a.mark((function e(){var t,r;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(!(t=n.props.router.query).id){e.next=6;break}return e.next=4,n.props.index.fetchList({namespace:"user/".concat(t.id),filter:{toUserId:t.id,complex:5}});case 4:r=e.sent,n.props.index.setList({namespace:"user/".concat(t.id),data:r});case 6:return e.abrupt("return");case 7:case"end":return e.stop()}}),e)}))),n.formatUserThreadsData=function(e){return 0===Object.keys(e).length?[]:Object.values(e).reduce((function(e,t){return[].concat(i()(e),i()(t))}))},n.handlePreviewBgImage=function(e){e&&e.stopPropagation(),n.setState({isPreviewBgVisible:!n.state.isPreviewBgVisible})},n.getBackgroundUrl=function(){var e,t=null,r=n.props.router.query,s=null===r||void 0===r?void 0:r.id;return s&&null!==(e=n.props.user)&&void 0!==e&&e.targetUsers[s]&&(t=n.props.user.targetUsers[s].originalBackGroundUrl),t||!1};var s=n.props.router.query;return n.state={fetchUserInfoLoading:!0,isPreviewBgVisible:!1},n.props.user.targetUsers[s.id]&&(n.state.fetchUserInfoLoading=!1),n}return h()(r,[{key:"setWeixinShare",value:function(){var e=this;setTimeout((function(){var t=e.props.user.targetUser;if(t){var r=t.nickname,n=t.avatarUrl,s=t.signature,a=t.id,i="".concat(r,"\u7684\u4e3b\u9875"),c=n,o=s?s.length>35?"".concat(s.substr(0,35),"..."):s:"\u5728\u8fd9\u91cc\uff0c\u53d1\u73b0\u66f4\u591a\u7cbe\u5f69\u5185\u5bb9",u="".concat(window.location.origin,"/user/").concat(a);Object(M.a)(i,o,u,c)}}),500)}},{key:"render",value:function(){var e=this.props,t=e.site,r=(e.user,t.platform),n=this.props.index,a=(n.lists,this.props.router.query),i=void 0===a?{}:a,c=n.getList({namespace:"user/".concat(i.id)}),o=n.getAttribute({namespace:"user/".concat(i.id),key:"totalPage"}),u=n.getAttribute({namespace:"user/".concat(i.id),key:"totalCount"}),d=n.getAttribute({namespace:"user/".concat(i.id),key:"currentPage"});return Object(s.jsxs)(C.a,{showHeader:!0,showTabBar:!1,immediateCheck:!0,onRefresh:this.fetchTargetUserThreads,noMore:o<d,showRefresh:!this.state.fetchUserInfoLoading,children:[Object(s.jsxs)("div",{className:I.a.mobileLayout,children:[this.state.fetchUserInfoLoading&&Object(s.jsx)("div",{className:I.a.loadingSpin,children:Object(s.jsx)(S.a,{type:"spinner",children:"\u52a0\u8f7d\u4e2d..."})}),!this.state.fetchUserInfoLoading&&Object(s.jsxs)(s.Fragment,{children:[Object(s.jsx)("div",{onClick:this.handlePreviewBgImage,children:Object(s.jsx)(T.a,{isOtherPerson:!0})}),Object(s.jsx)(R.a,{platform:r,isOtherPerson:!0})]}),Object(s.jsxs)("div",{className:I.a.unit,children:[Object(s.jsxs)("div",{className:I.a.threadUnit,children:[Object(s.jsx)("div",{className:I.a.threadTitle,children:"\u4e3b\u9898"}),Object(s.jsx)("div",{className:I.a.threadCount,children:void 0===u?"":"".concat(u,"\u4e2a\u4e3b\u9898")})]}),Object(s.jsx)("div",{className:I.a.dividerContainer,children:Object(s.jsx)(O.a,{className:I.a.divider})}),Object(s.jsx)("div",{className:I.a.threadItemContainer,children:c.length>0&&Object(s.jsx)(B.a,{data:c})})]})]}),this.getBackgroundUrl()&&this.state.isPreviewBgVisible&&Object(s.jsx)(L.a,{visible:this.state.isPreviewBgVisible,onClose:this.handlePreviewBgImage,imgUrls:[this.getBackgroundUrl()],currentUrl:this.getBackgroundUrl()})]})}}]),r}(y.a.Component))||n)||n)||n)||n;t.a=Object(q.withRouter)(_)}},[["91s+",1,0,3,5,4,6,7,2]]]);