(window.webpackJsonp=window.webpackJsonp||[]).push([[21,14,17],{2:function(e,t,r){r(17);var n=r(752);r(27),e.exports={data:function(){var e=this;return{errorCodeHandler:{default:{model_not_found:function(){return e.$router.replace("/error")},not_authenticated:function(){return e.$router.push("/pages/user/login")}},thread:{permission_denied:function(){return e.$router.replace("/error")}}}}},methods:{handleError:function(e){var t=arguments,r=this;return n(regeneratorRuntime.mark((function n(){var o,c,d,l,h;return regeneratorRuntime.wrap((function(n){for(;;)switch(n.prev=n.next){case 0:if(o=t.length>1&&void 0!==t[1]?t[1]:"",!((c=e.response.data.errors)&&Array.isArray(c)&&c.length>0&&c[0])){n.next=13;break}if(d=c[0].code,l=c[0].detail&&c[0].detail.length>0?c[0].detail[0]:c[0].code,h=c[0].detail&&c[0].detail.length>0?c[0].detail[0]:r.$t("core.".concat(l)),"site_closed"!==c[0].code){n.next=10;break}return n.next=9,r.siteClose(c);case 9:return n.abrupt("return",n.sent);case 10:r.$message.error(h),r.errorCodeHandler.default[d]&&r.errorCodeHandler.default[d](),o&&r.errorCodeHandler[o][d]&&r.errorCodeHandler[o][d]();case 13:case"end":return n.stop()}}),n)})))()},siteClose:function(e){var t=this;return n(regeneratorRuntime.mark((function r(){return regeneratorRuntime.wrap((function(r){for(;;)switch(r.prev=r.next){case 0:return r.prev=0,r.next=3,t.$store.dispatch("forum/setError",{code:e[0].code,detail:e[0].detail&&e[0].detail.length>0&&e[0].detail[0]});case 3:return r.next=5,t.$router.push("/pages/site/close");case 5:r.next=9;break;case 7:r.prev=7,r.t0=r.catch(0);case 9:case"end":return r.stop()}}),r,null,[[0,7]])})))()}}}},373:function(e,t,r){"use strict";t.a={data:function(){return{scrollTop:0}},mounted:function(){this.addEventListener(window,"scroll",this.handleScroll)},destroyed:function(){this.removeEventListener(window,"scroll",this.handleScroll)},methods:{handleScroll:function(){var e=document.documentElement.scrollTop||document.body.scrollTop,t=document.documentElement.clientHeight||document.body.clientHeight,r=document.documentElement.scrollHeight||document.body.scrollHeight;Math.ceil(e+t+10)>=r&&this.scrollLoadMore(),this.scrollTop=e},addEventListener:function(e,t,r,n){e.addEventListener?e.addEventListener(t,r,n):e.attachEvent&&e.attachEvent("on"+t,r)},removeEventListener:function(e,t,r,n){e.removeEventListener?e.removeEventListener(t,r,n):e.detachEvent&&e.detachEvent("on"+t,r)}}}}}]);