_N_E=(window.webpackJsonp_N_E=window.webpackJsonp_N_E||[]).push([[52],{Rs2Y:function(e,t,n){(window.__NEXT_P=window.__NEXT_P||[]).push(["/thread/comment/[id]",function(){return n("wc3I")}])},wc3I:function(e,t,n){"use strict";n.r(t);n("qePV"),n("rB9j"),n("UxlC");var r,i=n("nKUr"),s=n("o0o1"),o=n.n(s),a=(n("ls82"),n("yXPU")),c=n.n(a),u=n("lwsE"),p=n.n(u),l=n("W8MJ"),m=n.n(l),h=n("7W2i"),d=n.n(h),f=n("a1gu"),v=n.n(f),k=n("Nsbk"),y=n.n(k),b=n("q1tI"),j=n.n(b),x=n("20a2"),C=n("kMSe"),D=n("sho3"),w=(n("TeQF"),n("2B1R"),n("R5XZ"),n("B5JU")),O=n.n(w),P=n("5gkB"),R=n("7mQc"),g=n("c7hV"),I=n("aIz1"),L=n("z8Av"),S=n("wdT/"),N=n.n(S),E=n("iVyX"),M=n("woGs"),F=n("IvHc");function T(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var n,r=y()(e);if(t){var i=y()(this).constructor;n=Reflect.construct(r,arguments,i)}else n=r.apply(this,arguments);return v()(this,n)}}var A,B=Object(C.c)("site")(r=Object(C.c)("user")(r=Object(C.c)("comment")(r=Object(C.c)("thread")(r=Object(C.d)(r=function(e){d()(n,e);var t=T(n);function n(e){var r;return p()(this,n),(r=t.call(this,e)).onOperClick=function(e){if(!r.props.user.isLogin())return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),void Object(F.a)({url:"/user/login"});r.setState({showMorePopup:!1}),"delete"===e&&r.setState({showDeletePopup:!0}),"report"===e&&r.setState({showReportPopup:!0})},r.state={showReportPopup:!1,showMorePopup:!1,showCommentInput:!1,commentSort:!0,showDeletePopup:!1,showReplyDeletePopup:!1,inputText:"\u8bf7\u8f93\u5165\u5185\u5bb9"},r.commentData=null,r.replyData=null,r.recordCommentLike={id:null,status:null},r.recordReplyLike={id:null,status:null},r.reportContent=["\u5e7f\u544a\u5783\u573e","\u8fdd\u89c4\u5185\u5bb9","\u6076\u610f\u704c\u6c34","\u91cd\u590d\u53d1\u5e16"],r.inputText="\u5176\u4ed6\u7406\u7531...",r.positionRef=j.a.createRef(),r.isPositioned=!1,r}return m()(n,[{key:"componentDidUpdate",value:function(){var e,t,n=this;null!==(e=this.props.comment)&&void 0!==e&&e.postId&&!this.isPositioned&&null!==(t=this.positionRef)&&void 0!==t&&t.current&&(this.isPositioned=!0,setTimeout((function(){n.positionRef.current.scrollIntoView()}),1e3))}},{key:"onMoreClick",value:function(){this.setState({showMorePopup:!0})}},{key:"likeClick",value:function(){var e=c()(o.a.mark((function e(t){var n,r,i,s,a;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.props.user.isLogin()){e.next=4;break}return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),Object(F.a)({url:"/user/login"}),e.abrupt("return");case 4:if(t.id){e.next=6;break}return e.abrupt("return");case 6:if(this.recordCommentLike.id!==t.id&&(this.recordCommentLike.status=null),this.recordCommentLike.status===t.isLiked){e.next=12;break}this.recordCommentLike.status=t.isLiked,this.recordCommentLike.id=t.id,e.next=13;break;case 12:return e.abrupt("return");case 13:return n={id:t.id,isLiked:!t.isLiked},e.next=16,this.props.comment.updateLiked(n,this.props.thread);case 16:r=e.sent,i=r.success,s=r.msg,i&&(this.props.comment.setCommentDetailField("isLiked",n.isLiked),a=n.isLiked?t.likeCount+1:t.likeCount-1,this.props.comment.setCommentDetailField("likeCount",a)),i||N.a.error({content:s});case 21:case"end":return e.stop()}}),e,this)})));return function(t){return e.apply(this,arguments)}}()},{key:"replyLikeClick",value:function(){var e=c()(o.a.mark((function e(t){var n,r,i,s,a;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.props.user.isLogin()){e.next=4;break}return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),Object(F.a)({url:"/user/login"}),e.abrupt("return");case 4:if(t.id){e.next=6;break}return e.abrupt("return");case 6:if(this.recordCommentLike.id!==t.id&&(this.recordCommentLike.status=null),this.recordCommentLike.status===t.isLiked){e.next=12;break}this.recordCommentLike.status=t.isLiked,this.recordCommentLike.id=t.id,e.next=13;break;case 12:return e.abrupt("return");case 13:return n={id:t.id,isLiked:!t.isLiked},e.next=16,this.props.comment.updateLiked(n,this.props.comment);case 16:r=e.sent,i=r.success,s=r.msg,i&&(this.props.comment.setReplyListDetailField(t.id,"isLiked",n.isLiked),a=n.isLiked?t.likeCount+1:t.likeCount-1,this.props.comment.setReplyListDetailField(t.id,"likeCount",a)),i||N.a.error({content:s});case 21:case"end":return e.stop()}}),e,this)})));return function(t){return e.apply(this,arguments)}}()},{key:"deleteClick",value:function(){var e=c()(o.a.mark((function e(t){return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:this.commentData=t,this.setState({showDeletePopup:!0});case 2:case"end":return e.stop()}}),e,this)})));return function(t){return e.apply(this,arguments)}}()},{key:"onBtnClick",value:function(){this.deleteComment(),this.setState({showDeletePopup:!1})}},{key:"deleteComment",value:function(){var e=c()(o.a.mark((function e(){var t,n,r,i,s,a;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(null!==(t=this.props)&&void 0!==t&&null!==(n=t.comment)&&void 0!==n&&null!==(r=n.commentDetail)&&void 0!==r&&r.id){e.next=2;break}return e.abrupt("return");case 2:return e.next=4,this.props.comment.delete(this.props.comment.commentDetail.id,this.props.thread);case 4:if(i=e.sent,s=i.success,a=i.msg,this.setState({showDeletePopup:!1}),!s){e.next=12;break}return N.a.success({content:"\u5220\u9664\u6210\u529f"}),O.a.back(),e.abrupt("return");case 12:N.a.error({content:a});case 13:case"end":return e.stop()}}),e,this)})));return function(){return e.apply(this,arguments)}}()},{key:"replyDeleteClick",value:function(){var e=c()(o.a.mark((function e(t,n){return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:this.commentData=n,this.replyData=t,this.setState({showReplyDeletePopup:!0});case 3:case"end":return e.stop()}}),e,this)})));return function(t,n){return e.apply(this,arguments)}}()},{key:"replyDeleteComment",value:function(){var e=c()(o.a.mark((function e(){var t,n,r,i;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.replyData.id){e.next=2;break}return e.abrupt("return");case 2:return t={},this.replyData&&this.commentData&&(t.replyData=this.replyData,t.commentData=this.commentData),e.next=6,this.props.comment.deleteReplyComment(t,this.props.thread);case 6:if(n=e.sent,r=n.success,i=n.msg,this.setState({showReplyDeletePopup:!1}),!r){e.next=13;break}return N.a.success({content:"\u5220\u9664\u6210\u529f"}),e.abrupt("return");case 13:N.a.error({content:i});case 14:case"end":return e.stop()}}),e,this)})));return function(){return e.apply(this,arguments)}}()},{key:"replyClick",value:function(e){var t;if(!this.props.user.isLogin())return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),void Object(F.a)({url:"/user/login"});this.props.canPublish()&&(this.commentData=e,this.replyData=null,this.setState({showCommentInput:!0,inputText:null!==e&&void 0!==e&&null!==(t=e.user)&&void 0!==t&&t.nickname?"\u56de\u590d".concat(e.user.nickname):"\u8bf7\u8f93\u5165\u5185\u5bb9"}))}},{key:"replyReplyClick",value:function(e,t){var n;if(!this.props.user.isLogin())return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),void Object(F.a)({url:"/user/login"});this.props.canPublish()&&(this.commentData=null,this.replyData=e,this.replyData.commentId=t.id,this.setState({showCommentInput:!0,inputText:null!==e&&void 0!==e&&null!==(n=e.user)&&void 0!==n&&n.nickname?"\u56de\u590d".concat(e.user.nickname):"\u8bf7\u8f93\u5165\u5185\u5bb9"}))}},{key:"createReply",value:function(){var e=c()(o.a.mark((function e(t,n){var r,i,s,a,c,u;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(t.replace(/\s/g,"")){e.next=4;break}return N.a.info({content:"\u8bf7\u8f93\u5165\u5185\u5bb9"}),e.abrupt("return");case 4:if(r=this.props.comment.threadId){e.next=7;break}return e.abrupt("return");case 7:return i={id:r,content:t},this.replyData&&(i.replyId=this.replyData.id,i.isComment=!0,i.commentId=this.replyData.commentId,i.commentPostId=this.replyData.id),this.commentData&&(i.replyId=this.commentData.id,i.isComment=!0,i.commentId=this.commentData.id),null!==n&&void 0!==n&&n.length&&(i.attachments=n.filter((function(e){return"success"===e.status&&e.response})).map((function(e){return{id:e.response.id,type:"attachments"}}))),e.next=13,this.props.comment.createReply(i,this.props.thread);case 13:if(s=e.sent,a=s.success,c=s.msg,u=s.isApproved,!a){e.next=21;break}return this.setState({showCommentInput:!1,inputText:"\u8bf7\u8f93\u5165\u5185\u5bb9"}),u?N.a.success({content:c}):N.a.warning({content:c}),e.abrupt("return",!0);case 21:N.a.error({content:c});case 22:case"end":return e.stop()}}),e,this)})));return function(t,n){return e.apply(this,arguments)}}()},{key:"onReportOk",value:function(){var e=c()(o.a.mark((function e(t){var n,r,i,s,a,c,u;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(t){e.next=2;break}return e.abrupt("return");case 2:return s={threadId:this.props.comment.threadId,type:2,reason:t,userId:this.props.user.userInfo.id,postId:null===(n=this.props)||void 0===n||null===(r=n.comment)||void 0===r||null===(i=r.commentDetail)||void 0===i?void 0:i.id},e.next=5,this.props.thread.createReports(s);case 5:if(a=e.sent,c=a.success,u=a.msg,!c){e.next=12;break}return N.a.success({content:"\u64cd\u4f5c\u6210\u529f"}),this.setState({showReportPopup:!1}),e.abrupt("return",!0);case 12:N.a.error({content:u});case 13:case"end":return e.stop()}}),e,this)})));return function(t){return e.apply(this,arguments)}}()},{key:"avatarClick",value:function(e){var t=e.userId;t&&this.props.router.push("/user/".concat(t))}},{key:"replyAvatarClick",value:function(e,t,n){if(2===n){var r=e.userId;if(!r)return;this.props.router.push("/user/".concat(r))}if(3===n){var i=e.commentUserId;if(!i)return;this.props.router.push("/user/".concat(i))}}},{key:"render",value:function(){var e,t,n=this,r=this.props.comment,s=r.commentDetail,o=r.isReady,a={canEdit:!1,canDelete:null===s||void 0===s?void 0:s.canDelete,canEssence:!1,canStick:!1,isAdmini:null===(e=this.props)||void 0===e||null===(t=e.user)||void 0===t?void 0:t.isAdmini};return Object(i.jsxs)("div",{className:P.a.index,children:[Object(i.jsx)(L.a,{}),Object(i.jsx)("div",{className:P.a.content,children:o&&Object(i.jsx)(R.a,{data:s,likeClick:function(){return n.likeClick(s)},replyClick:function(){return n.replyClick(s)},avatarClick:function(){return n.avatarClick(s)},deleteClick:function(){return n.deleteClick(s)},replyLikeClick:function(e){return n.replyLikeClick(e,s)},replyReplyClick:function(e){return n.replyReplyClick(e,s)},replyAvatarClick:function(e,t){return n.replyAvatarClick(e,s,t)},replyDeleteClick:function(e){return n.replyDeleteClick(e,s)},onMoreClick:function(){return n.onMoreClick()},isHideEdit:!0,postId:this.props.comment.postId,positionRef:this.positionRef})}),Object(i.jsxs)("div",{className:P.a.footer,children:[Object(i.jsx)(E.a,{visible:this.state.showCommentInput,inputText:this.state.inputText,onClose:function(){return n.setState({showCommentInput:!1})},onSubmit:function(e,t){return n.createReply(e,t)},site:this.props.site}),Object(i.jsx)(g.a,{permissions:a,statuses:{isEssence:!1,isStick:!1},visible:this.state.showMorePopup,onClose:function(){return n.setState({showMorePopup:!1})},onSubmit:function(){return n.setState({showMorePopup:!1})},onOperClick:function(e){return n.onOperClick(e)}}),Object(i.jsx)(I.a,{visible:this.state.showDeletePopup,onClose:function(){return n.setState({showDeletePopup:!1})},onBtnClick:function(){return n.deleteComment()}}),Object(i.jsx)(I.a,{visible:this.state.showReplyDeletePopup,onClose:function(){return n.setState({showReplyDeletePopup:!1})},onBtnClick:function(){return n.replyDeleteComment()}}),Object(i.jsx)(M.a,{reportContent:this.reportContent,inputText:this.inputText,visible:this.state.showReportPopup,onCancel:function(){return n.setState({showReportPopup:!1})},onOkClick:function(e){return n.onReportOk(e)}})]})]})}}]),n}(j.a.Component))||r)||r)||r)||r)||r,U=Object(x.withRouter)(B),_=(n("ma9I"),n("9cud")),J=n("JMpY"),W=n("PuBq"),q=n("Wpw2"),H=n("MCNy"),X=n.n(H),V=n("wrmh"),z=n("jpI4"),Q=n("myta"),Y=n("gdiw"),Z=n("TSYQ"),K=n.n(Z);function G(e){var t=e.empty;return t?Object(i.jsx)("div",{className:K()(Y.a.container,t&&Y.a.empty)}):Object(i.jsx)("div",{className:Y.a.container,children:"\u6ca1\u6709\u66f4\u591a\u6570\u636e\u4e86"})}function $(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var n,r=y()(e);if(t){var i=y()(this).constructor;n=Reflect.construct(r,arguments,i)}else n=r.apply(this,arguments);return v()(this,n)}}var ee,te=Object(C.c)("site")(A=Object(C.c)("user")(A=Object(C.c)("comment")(A=Object(C.c)("thread")(A=Object(C.d)(A=function(e){d()(n,e);var t=$(n);function n(e){var r;return p()(this,n),(r=t.call(this,e)).state={commentSort:!0,showDeletePopup:!1,showReplyDeletePopup:!1,commentId:null},r.commentData=null,r.replyData=null,r.positionRef=j.a.createRef(),r.isPositioned=!1,r}return m()(n,[{key:"componentDidUpdate",value:function(){var e,t,n=this;null!==(e=this.props.comment)&&void 0!==e&&e.postId&&!this.isPositioned&&null!==(t=this.positionRef)&&void 0!==t&&t.current&&(this.isPositioned=!0,setTimeout((function(){n.positionRef.current.scrollIntoView()}),1e3))}},{key:"onBackClick",value:function(){this.props.router.back()}},{key:"likeClick",value:function(){var e=c()(o.a.mark((function e(t){var n,r,i,s,a;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.props.user.isLogin()){e.next=4;break}return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),Object(F.a)({url:"/user/login"}),e.abrupt("return");case 4:if(t.id){e.next=6;break}return e.abrupt("return");case 6:return n={id:t.id,isLiked:!t.isLiked},e.next=9,this.props.comment.updateLiked(n,this.props.thread);case 9:r=e.sent,i=r.success,s=r.msg,i&&(this.props.comment.setCommentDetailField("isLiked",n.isLiked),a=n.isLiked?t.likeCount+1:t.likeCount-1,this.props.comment.setCommentDetailField("likeCount",a)),i||N.a.error({content:s});case 14:case"end":return e.stop()}}),e,this)})));return function(t){return e.apply(this,arguments)}}()},{key:"onFollowClick",value:function(){var e,t,n,r,i,s;if(!this.props.user.isLogin())return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),void Object(F.a)({url:"/user/login"});null!==(e=this.props.comment)&&void 0!==e&&null!==(t=e.commentDetail)&&void 0!==t&&t.userId&&(2===(null===(n=this.props.comment)||void 0===n||null===(r=n.authorInfo)||void 0===r?void 0:r.follow)||1===(null===(i=this.props.comment)||void 0===i||null===(s=i.authorInfo)||void 0===s?void 0:s.follow)?this.props.comment.cancelFollow({id:this.props.comment.commentDetail.userId,type:1},this.props.user):this.props.comment.postFollow(this.props.comment.commentDetail.userId,this.props.user))}},{key:"replyLikeClick",value:function(){var e=c()(o.a.mark((function e(t){var n,r,i,s,a;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.props.user.isLogin()){e.next=4;break}return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),Object(F.a)({url:"/user/login"}),e.abrupt("return");case 4:if(t.id){e.next=6;break}return e.abrupt("return");case 6:return n={id:t.id,isLiked:!t.isLiked},e.next=9,this.props.comment.updateLiked(n,this.props.thread);case 9:r=e.sent,i=r.success,s=r.msg,i&&(this.props.comment.setReplyListDetailField(t.id,"isLiked",n.isLiked),a=n.isLiked?t.likeCount+1:t.likeCount-1,this.props.comment.setReplyListDetailField(t.id,"likeCount",a)),i||N.a.error({content:s});case 14:case"end":return e.stop()}}),e,this)})));return function(t){return e.apply(this,arguments)}}()},{key:"deleteClick",value:function(){var e=c()(o.a.mark((function e(){return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.props.user.isLogin()){e.next=4;break}return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),Object(F.a)({url:"/user/login"}),e.abrupt("return");case 4:this.commentData=this.props.comment.commentDetail,this.setState({showDeletePopup:!0});case 6:case"end":return e.stop()}}),e,this)})));return function(){return e.apply(this,arguments)}}()},{key:"deleteComment",value:function(){var e=c()(o.a.mark((function e(){var t,n,r,i;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.commentData.id){e.next=2;break}return e.abrupt("return");case 2:return e.next=4,this.props.comment.delete(this.commentData.id,this.props.thread);case 4:if(t=e.sent,n=t.success,r=t.msg,this.setState({showDeletePopup:!1}),!n){e.next=13;break}return N.a.success({content:"\u5220\u9664\u6210\u529f"}),(i=this.props.comment.threadId)&&this.props.router.push("/thread/".concat(i)),e.abrupt("return");case 13:N.a.error({content:r});case 14:case"end":return e.stop()}}),e,this)})));return function(){return e.apply(this,arguments)}}()},{key:"replyDeleteClick",value:function(){var e=c()(o.a.mark((function e(t,n){return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:this.commentData=n,this.replyData=t,this.setState({showReplyDeletePopup:!0});case 3:case"end":return e.stop()}}),e,this)})));return function(t,n){return e.apply(this,arguments)}}()},{key:"replyDeleteComment",value:function(){var e=c()(o.a.mark((function e(){var t,n,r,i;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.replyData.id){e.next=2;break}return e.abrupt("return");case 2:return t={},this.replyData&&this.commentData&&(t.replyData=this.replyData,t.commentData=this.commentData),e.next=6,this.props.comment.deleteReplyComment(t,this.props.thread);case 6:if(n=e.sent,r=n.success,i=n.msg,this.setState({showReplyDeletePopup:!1}),!r){e.next=13;break}return N.a.success({content:"\u5220\u9664\u6210\u529f"}),e.abrupt("return");case 13:N.a.error({content:i});case 14:case"end":return e.stop()}}),e,this)})));return function(){return e.apply(this,arguments)}}()},{key:"replyClick",value:function(e){if(!this.props.user.isLogin())return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),void Object(F.a)({url:"/user/login"});this.props.canPublish()&&(this.commentData=e,this.replyData=null,this.setState({commentId:e.id}))}},{key:"replyReplyClick",value:function(e,t){if(!this.props.user.isLogin())return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),void Object(F.a)({url:"/user/login"});this.props.canPublish()&&(this.commentData=null,this.replyData=e,this.replyData.commentId=t.id,this.setState({commentId:null}))}},{key:"createReply",value:function(){var e=c()(o.a.mark((function e(t,n){var r,i,s,a,c,u;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(this.props.user.isLogin()){e.next=4;break}return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),Object(F.a)({url:"/user/login"}),e.abrupt("return");case 4:if(t.replace(/\s/g,"")){e.next=8;break}return N.a.info({content:"\u8bf7\u8f93\u5165\u5185\u5bb9"}),e.abrupt("return");case 8:if(r=this.props.comment.threadId){e.next=11;break}return e.abrupt("return");case 11:return i={id:r,content:t},this.replyData&&(i.replyId=this.replyData.id,i.isComment=!0,i.commentId=this.replyData.commentId,i.commentPostId=this.replyData.id),this.commentData&&(i.replyId=this.commentData.id,i.isComment=!0,i.commentId=this.commentData.id),null!==n&&void 0!==n&&n.length&&(i.attachments=n.filter((function(e){return"success"===e.status&&e.response})).map((function(e){return{id:e.response.id,type:"attachments"}}))),e.next=17,this.props.comment.createReply(i,this.props.thread);case 17:if(s=e.sent,a=s.success,c=s.msg,u=s.isApproved,!a){e.next=25;break}return this.setState({commentId:null}),u?N.a.success({content:c}):N.a.warning({content:c}),e.abrupt("return",!0);case 25:N.a.error({content:c});case 26:case"end":return e.stop()}}),e,this)})));return function(t,n){return e.apply(this,arguments)}}()},{key:"onPrivateLetter",value:function(){var e;if(!this.props.user.isLogin())return N.a.info({content:"\u8bf7\u5148\u767b\u5f55!"}),void Object(F.a)({url:"/user/login"});var t=null===(e=this.props.comment)||void 0===e?void 0:e.authorInfo,n=t.username,r=t.nickname;n&&O.a.push({url:"/message?page=chat&username=".concat(n,"&nickname=").concat(r)})}},{key:"onUserClick",value:function(e){e&&O.a.push({url:"/user/".concat(e)})}},{key:"render",value:function(){var e,t,n,r,s,o,a,c,u,p,l=this,m=this.props.comment,h=m.commentDetail,d=m.isReady,f=m.isAuthorInfoError,v=(null===(e=this.props.user)||void 0===e||null===(t=e.userInfo)||void 0===t?void 0:t.id)&&(null===(n=this.props.user)||void 0===n||null===(r=n.userInfo)||void 0===r?void 0:r.id)===(null===h||void 0===h?void 0:h.userId);return Object(i.jsxs)("div",{className:_.a.container,children:[Object(i.jsx)("div",{className:_.a.header,children:Object(i.jsx)(L.a,{})}),Object(i.jsxs)("div",{className:_.a.body,children:[Object(i.jsxs)("div",{children:[Object(i.jsxs)("div",{className:_.a.bodyLeft,children:[Object(i.jsxs)("div",{className:_.a.bodyLeftHeader,children:[Object(i.jsxs)("div",{className:_.a.back,onClick:function(){return l.onBackClick()},children:[Object(i.jsx)(X.a,{name:"ReturnOutlined"}),Object(i.jsx)("span",{className:_.a.text,children:"\u8fd4\u56de"})]}),Object(i.jsxs)("div",{className:_.a.bodyHeaderOperate,children:[null!==(s=this.props.comment)&&void 0!==s&&s.rewards?Object(i.jsx)("div",{className:_.a.reward,children:Object(i.jsx)(z.a,{number:this.props.comment.rewards})}):"",null!==(o=this.props.comment)&&void 0!==o&&o.redPacketAmount?Object(i.jsx)("div",{className:_.a.redpacket,children:Object(i.jsx)(Q.a,{number:this.props.comment.redPacketAmount})}):"",(null===(a=this.props.comment)||void 0===a||null===(c=a.commentDetail)||void 0===c?void 0:c.canDelete)&&Object(i.jsxs)("div",{className:_.a.delete,onClick:function(){return l.deleteClick()},children:[Object(i.jsx)(X.a,{name:"DeleteOutlined"}),Object(i.jsx)("span",{className:_.a.text,children:"\u5220\u9664"})]})]})]}),d?Object(i.jsx)(W.a,{data:h,likeClick:function(){return l.likeClick(h)},replyClick:function(){return l.replyClick(h)},replyLikeClick:function(e){return l.replyLikeClick(e,h)},replyReplyClick:function(e){return l.replyReplyClick(e,h)},replyDeleteClick:function(e){return l.replyDeleteClick(e,h)},avatarClick:function(e){return l.onUserClick(e)},isHideEdit:!0,isFirstDivider:!0,isShowInput:this.state.commentId===h.id,onSubmit:function(e,t){return l.createReply(e,t)},postId:this.props.comment.postId,positionRef:this.positionRef}):Object(i.jsx)(V.a,{type:"init"})]}),Object(i.jsx)(G,{empty:!1})]}),Object(i.jsxs)("div",{className:_.a.bodyRigth,children:[Object(i.jsx)("div",{className:_.a.authorInfo,children:null!==(u=this.props.comment)&&void 0!==u&&u.authorInfo?Object(i.jsx)(J.a,{user:null===(p=this.props.comment)||void 0===p?void 0:p.authorInfo,onFollowClick:function(){return l.onFollowClick()},isShowBtn:!v,onPrivateLetter:function(){return l.onPrivateLetter()},onPersonalPage:function(){var e,t;return l.onUserClick(null===(e=l.props.comment)||void 0===e||null===(t=e.authorInfo)||void 0===t?void 0:t.id)}}):Object(i.jsx)(V.a,{isError:f,type:"init"})}),Object(i.jsx)("div",{className:_.a.recommend,children:Object(i.jsx)(q.a,{})})]})]}),Object(i.jsx)(I.a,{visible:this.state.showDeletePopup,onClose:function(){return l.setState({showDeletePopup:!1})},onBtnClick:function(){return l.deleteComment()}}),Object(i.jsx)(I.a,{visible:this.state.showReplyDeletePopup,onClose:function(){return l.setState({showReplyDeletePopup:!1})},onBtnClick:function(){return l.replyDeleteComment()}})]})}}]),n}(j.a.Component))||A)||A)||A)||A)||A,ne=Object(x.withRouter)(te),re=n("ZCww"),ie=n("L0er"),se=n("QcND"),oe=n("zZWi"),ae=n("J0pL");function ce(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var n,r=y()(e);if(t){var i=y()(this).constructor;n=Reflect.construct(r,arguments,i)}else n=r.apply(this,arguments);return v()(this,n)}}var ue=Object(C.c)("site")(ee=Object(C.c)("comment")(ee=Object(C.d)(ee=function(e){d()(n,e);var t=ce(n);function n(e){var r;p()(this,n);var i=(r=t.call(this,e)).props,s=i.serverData,o=i.comment;return s&&o.setCommentDetail(s),r.state={isServerError:!1,serverErrorMsg:""},r}return m()(n,null,[{key:"getInitialProps",value:function(){var e=c()(o.a.mark((function e(t){var n,r,i;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(r=null===t||void 0===t||null===(n=t.query)||void 0===n?void 0:n.id){e.next=3;break}return e.abrupt("return",{props:{serverData:null}});case 3:return e.next=5,Object(D.readCommentDetail)({params:{pid:r}});case 5:return i=e.sent,e.abrupt("return",{props:{serverData:i.data}});case 7:case"end":return e.stop()}}),e)})));return function(t){return e.apply(this,arguments)}}()}]),m()(n,[{key:"componentDidMount",value:function(){var e=c()(o.a.mark((function e(){var t,n,r,i,s,a,c,u,p,l,m,h,d,f;return o.a.wrap((function(e){for(;;)switch(e.prev=e.next){case 0:if(i=this.props.router.query,s=i.id,a=i.threadId,c=i.postId,u=null===(t=this.props)||void 0===t||null===(n=t.comment)||void 0===n||null===(r=n.commentDetail)||void 0===r?void 0:r.id,Number(s)!==u||!s||!u){e.next=4;break}return e.abrupt("return");case 4:if(this.props.comment.reset(),a&&this.props.comment.setThreadId(a),c&&this.props.comment.setPostId(Number(c)),this.props.serverData||!s){e.next=22;break}return e.next=10,this.props.comment.fetchCommentDetail(s);case 10:if(0===(m=e.sent).code){e.next=18;break}if(-4004!==m.code){e.next=15;break}return O.a.replace({url:"/404"}),e.abrupt("return");case 15:return m.code>-5e3&&m.code<-4e3&&this.setState({serverErrorMsg:m.msg}),this.setState({isServerError:!0}),e.abrupt("return");case 18:h=this.props.site,d=h.platform,f=null===(p=this.props.comment)||void 0===p||null===(l=p.commentDetail)||void 0===l?void 0:l.userId,"pc"===d&&f&&this.props.comment.fetchAuthorInfo(f);case 22:case"end":return e.stop()}}),e,this)})));return function(){return e.apply(this,arguments)}}()},{key:"render",value:function(){var e=this.props.site.platform;return this.state.isServerError?"h5"===e?Object(i.jsx)(ie.a,{text:this.state.serverErrorMsg}):Object(i.jsx)(re.a,{text:this.state.serverErrorMsg}):Object(i.jsx)(ae.a,{h5:Object(i.jsx)(U,{canPublish:this.props.canPublish}),pc:Object(i.jsx)(ne,{canPublish:this.props.canPublish})})}}]),n}(j.a.Component))||ee)||ee)||ee;t.default=Object(se.a)(Object(oe.a)(Object(x.withRouter)(ue)))},zZWi:function(e,t,n){"use strict";n.d(t,"a",(function(){return g}));var r=n("nKUr"),i=n("o0o1"),s=n.n(i),o=n("lSNA"),a=n.n(o),c=(n("ls82"),n("yXPU")),u=n.n(c),p=n("lwsE"),l=n.n(p),m=n("W8MJ"),h=n.n(m),d=n("7W2i"),f=n.n(d),v=n("a1gu"),k=n.n(v),y=n("Nsbk"),b=n.n(y),j=n("q1tI"),x=n.n(j),C=n("kMSe"),D=n("n4oF"),w=n("lN2P");function O(e,t){var n=Object.keys(e);if(Object.getOwnPropertySymbols){var r=Object.getOwnPropertySymbols(e);t&&(r=r.filter((function(t){return Object.getOwnPropertyDescriptor(e,t).enumerable}))),n.push.apply(n,r)}return n}function P(e){for(var t=1;t<arguments.length;t++){var n=null!=arguments[t]?arguments[t]:{};t%2?O(Object(n),!0).forEach((function(t){a()(e,t,n[t])})):Object.getOwnPropertyDescriptors?Object.defineProperties(e,Object.getOwnPropertyDescriptors(n)):O(Object(n)).forEach((function(t){Object.defineProperty(e,t,Object.getOwnPropertyDescriptor(n,t))}))}return e}function R(e){var t=function(){if("undefined"===typeof Reflect||!Reflect.construct)return!1;if(Reflect.construct.sham)return!1;if("function"===typeof Proxy)return!0;try{return Date.prototype.toString.call(Reflect.construct(Date,[],(function(){}))),!0}catch(e){return!1}}();return function(){var n,r=b()(e);if(t){var i=b()(this).constructor;n=Reflect.construct(r,arguments,i)}else n=r.apply(this,arguments);return k()(this,n)}}function g(e){var t;return Object(C.c)("user")(t=Object(C.d)(t=function(t){f()(i,t);var n=R(i);function i(e){return l()(this,i),n.call(this,e)}return h()(i,null,[{key:"getInitialProps",value:function(){var t=u()(s.a.mark((function t(n,r){var i;return s.a.wrap((function(t){for(;;)switch(t.prev=t.next){case 0:if(r){t.next=2;break}throw Error("CheckLoginStatus\u5fc5\u987b\u524d\u7f6e\u4f7f\u7528HOCFetchSiteData");case 2:if(t.prev=2,i={},!Object(D.a)()){t.next=9;break}if(!e.getInitialProps){t.next=9;break}return t.next=8,e.getInitialProps(n);case 8:i=t.sent;case 9:return t.abrupt("return",P({},i));case 12:return t.prev=12,t.t0=t.catch(2),console.log("err",t.t0),t.abrupt("return",{});case 16:case"end":return t.stop()}}),t,null,[[2,12]])})));return function(e,n){return t.apply(this,arguments)}}()}]),h()(i,[{key:"componentDidMount",value:function(){this.props.user.loginStatus||w.a.saveAndLogin()}},{key:"componentDidUpdate",value:function(){this.props.user.loginStatus||w.a.saveAndLogin()}},{key:"render",value:function(){var t=this.props.user.loginStatus;return"padding"!==t&&t?Object(r.jsx)(e,P({},this.props)):null}}]),i}(x.a.Component))||t)||t}}},[["Rs2Y",1,0,3,4,7,2]]]);