(window.webpackJsonp=window.webpackJsonp||[]).push([[64],{"/V7O":function(e,t,r){"use strict";r.r(t);var a=r("rxdq"),s=r("yDFb");for(var i in s)["default"].indexOf(i)<0&&function(e){r.d(t,e,(function(){return s[e]}))}(i);var c=r("KHd+"),d=Object(c.a)(s.default,a.a,a.b,!1,null,null,null);t.default=d.exports},Iqqv:function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var a=i(r("QbLZ"));r("I1+7");var s=i(r("zxK7"));function i(e){return e&&e.__esModule?e:{default:e}}t.default=(0,a.default)({name:"user-permission-view"},s.default)},rxdq:function(e,t,r){"use strict";r.d(t,"a",(function(){return a})),r.d(t,"b",(function(){return s}));var a=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"rol-permission-box"},[r("div",{staticClass:"index-main-con__main-title__class permission__title"},[r("i"),e._v(" "),e._l(e.menuData,(function(t,a){return r("span",{key:a,class:e.activeTab.name===t.name?"is-active":"",on:{click:function(r){e.activeTab=t}}},[e._v(e._s(t.title))])}))],2),e._v(" "),r("Card",{attrs:{header:e.$router.history.current.query.name+"--"+e.activeTab.title}}),e._v(" "),r("div",{directives:[{name:"show",rawName:"v-show",value:"userOperate"===e.activeTab.name,expression:"activeTab.name === 'userOperate'"}]},[r("div",{staticClass:"user-operate__title"},[r("el-checkbox",{attrs:{indeterminate:e.isIndeterminate},on:{change:e.handleCheckAllChange},model:{value:e.checkAll,callback:function(t){e.checkAll=t},expression:"checkAll"}}),e._v(" "),r("p",{staticStyle:{"margin-left":"10PX"}},[e._v(e._s(e.selectText))])],1),e._v(" "),r("div",{staticClass:"user-operate"},[r("div",{staticClass:"user-operate__header"},[r("div",{staticClass:"scope-action"},[e._v("\n            生效范围\n        ")]),e._v(" "),r("Card",{attrs:{header:"内容发布权限"}})],1),e._v(" "),r("Card",{staticClass:"hasSelect"},[r("CardRow",{attrs:{description:"允许发布帖子的权限"}},[r("el-checkbox",{attrs:{label:"switch.createThread",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},on:{change:function(t){return e.changeChecked(t,"createThread")}},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("发布帖子")])],1),e._v(" "),r("el-cascader",{key:e.keyValue,attrs:{placeholder:"请选择",options:e.categoriesList,disabled:-1===e.checked.indexOf("switch.createThread"),clearable:"",props:{value:"id",label:"name",children:"children",multiple:!0,checkStrictly:!0,expandTrigger:"hover"},"collapse-tags":""},on:{change:function(t){return e.changeCategory(t,"createThread")},"remove-tag":function(t){return e.clearItem(t,"createThread")}},model:{value:e.selectList.createThread,callback:function(t){e.$set(e.selectList,"createThread",t)},expression:"selectList.createThread"}})],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"允许发帖插入图片的权限"}},[r("el-checkbox",{attrs:{label:"thread.insertImage",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("插入图片")])],1)],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"允许发帖插入视频的权限"}},[r("el-checkbox",{attrs:{label:"thread.insertVideo",disabled:e.videoDisabled||"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("插入视频")])],1)],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"允许发帖插入语音的权限"}},[r("el-checkbox",{attrs:{label:"thread.insertAudio",disabled:e.videoDisabled||"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("插入语音")])],1)],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"允许发帖插入附件的权限"}},[r("el-checkbox",{attrs:{label:"thread.insertAttachment",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("插入附件")])],1)],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"允许发帖插入商品的权限"}},[r("el-checkbox",{attrs:{label:"thread.insertGoods",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("插入商品")])],1)],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"允许发帖插入付费的权限"}},[r("el-checkbox",{attrs:{label:"thread.insertPay",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("插入付费")])],1)],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"允许发帖插入悬赏的权限"}},[r("el-checkbox",{attrs:{label:"thread.insertReward",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("插入悬赏")])],1)],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"允许发帖插入红包的权限"}},[r("el-checkbox",{attrs:{label:"thread.insertRedPacket",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("插入红包")])],1)],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"允许发帖插入位置的权限"}},[r("el-checkbox",{attrs:{label:"thread.insertPosition",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("插入位置")])],1)],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"允许发帖发布匿名的权限"}},[r("el-checkbox",{attrs:{label:"thread.allowAnonymous",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("允许匿名")])],1)],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"允许发布私信"}},[r("el-checkbox",{attrs:{label:"dialog.create",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("发布私信")])],1)],1),e._v(" "),r("Card",{staticClass:"hasSelect"},[r("CardRow",{attrs:{description:"允许在内容分类回复主题的权限"}},[r("el-checkbox",{attrs:{label:"switch.thread.reply",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},on:{change:function(t){return e.changeChecked(t,"thread.reply")}},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("回复主题")])],1),e._v(" "),r("el-cascader",{key:e.keyValue,attrs:{placeholder:"请选择",options:e.categoriesList,disabled:-1===e.checked.indexOf("switch.thread.reply"),props:{value:"id",label:"name",children:"children",multiple:!0,checkStrictly:!0,expandTrigger:"hover"},"collapse-tags":""},on:{change:function(t){return e.changeCategory(t,"thread.reply")},"remove-tag":function(t){return e.clearItem(t,"thread.reply")}},model:{value:e.selectList["thread.reply"],callback:function(t){e.$set(e.selectList,"thread.reply",t)},expression:"selectList['thread.reply']"}})],1)],1),e._v(" "),r("div",{staticClass:"user-operate"},[r("div",{staticClass:"user-operate__header"},[r("div",{staticClass:"scope-action"},[e._v("\n            生效范围\n        ")]),e._v(" "),r("Card",{attrs:{header:"查看权限"}})],1),e._v(" "),r("Card",{staticClass:"hasSelect"},[r("CardRow",{attrs:{description:"查看内容分类主题列表的权限"}},[r("el-checkbox",{attrs:{label:"switch.viewThreads",disabled:"1"===e.$router.history.current.query.id},on:{change:function(t){return e.changeChecked(t,"viewThreads")}},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("查看主题列表")])],1),e._v(" "),r("el-cascader",{key:e.keyValue,attrs:{placeholder:"请选择",options:e.categoriesList,disabled:-1===e.checked.indexOf("switch.viewThreads"),props:{value:"id",label:"name",children:"children",multiple:!0,checkStrictly:!0,expandTrigger:"hover"},"collapse-tags":""},on:{change:function(t){return e.changeCategory(t,"viewThreads")},"remove-tag":function(t){return e.clearItem(t,"viewThreads")}},model:{value:e.selectList.viewThreads,callback:function(t){e.$set(e.selectList,"viewThreads",t)},expression:"selectList.viewThreads"}})],1),e._v(" "),r("Card",{staticClass:"hasSelect"},[r("CardRow",{attrs:{description:"查看内容分类主题详情的权限"}},[r("el-checkbox",{attrs:{label:"switch.thread.viewPosts",disabled:"1"===e.$router.history.current.query.id},on:{change:function(t){return e.changeChecked(t,"thread.viewPosts")}},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("查看主题详情")])],1),e._v(" "),r("el-cascader",{key:e.keyValue,attrs:{placeholder:"请选择",options:e.categoriesList,disabled:-1===e.checked.indexOf("switch.thread.viewPosts"),props:{value:"id",label:"name",children:"children",multiple:!0,checkStrictly:!0,expandTrigger:"hover"},"collapse-tags":""},on:{change:function(t){return e.changeCategory(t,"thread.viewPosts")},"remove-tag":function(t){return e.clearItem(t,"thread.viewPosts")}},model:{value:e.selectList["thread.viewPosts"],callback:function(t){e.$set(e.selectList,"thread.viewPosts",t)},expression:"selectList['thread.viewPosts']"}})],1),e._v(" "),r("Card",{staticClass:"hasSelect"},[r("CardRow",{attrs:{description:"免费查看内容分类下的付费帖子"}},[r("el-checkbox",{attrs:{label:"switch.thread.freeViewPosts",disabled:"1"===e.$router.history.current.query.id},on:{change:function(t){return e.changeChecked(t,"thread.freeViewPosts")}},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("免费查看付费帖子")])],1),e._v(" "),r("el-cascader",{key:e.keyValue,attrs:{placeholder:"请选择",options:e.categoriesList,disabled:-1===e.checked.indexOf("switch.thread.freeViewPosts"),props:{value:"id",label:"name",children:"children",multiple:!0,checkStrictly:!0,expandTrigger:"hover"},"collapse-tags":""},on:{change:function(t){return e.changeCategory(t,"thread.freeViewPosts")},"remove-tag":function(t){return e.clearItem(t,"thread.freeViewPosts")}},model:{value:e.selectList["thread.freeViewPosts"],callback:function(t){e.$set(e.selectList,"thread.freeViewPosts",t)},expression:"selectList['thread.freeViewPosts']"}})],1)],1),e._v(" "),r("div",{staticClass:"user-operate"},[r("div",{staticClass:"user-operate__header"},[r("div",{staticClass:"scope-action"},[e._v("\n            生效范围\n        ")]),e._v(" "),r("Card",{attrs:{header:"管理权限"}})],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"前台置顶、取消置顶主题的权限"}},[r("el-checkbox",{attrs:{label:"thread.sticky",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("置顶")])],1)],1),e._v(" "),r("Card",{staticClass:"hasSelect"},[r("CardRow",{attrs:{description:"前台精华、取消精华主题的权限"}},[r("el-checkbox",{attrs:{label:"switch.thread.essence",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},on:{change:function(t){return e.changeChecked(t,"thread.essence")}},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("加精")])],1),e._v(" "),r("el-cascader",{key:e.keyValue,attrs:{placeholder:"请选择",options:e.categoriesList,disabled:-1===e.checked.indexOf("switch.thread.essence"),props:{value:"id",label:"name",children:"children",multiple:!0,checkStrictly:!0,expandTrigger:"hover"},"collapse-tags":""},on:{change:function(t){return e.changeCategory(t,"thread.essence")},"remove-tag":function(t){return e.clearItem(t,"thread.essence")}},model:{value:e.selectList["thread.essence"],callback:function(t){e.$set(e.selectList,"thread.essence",t)},expression:"selectList['thread.essence']"}})],1),e._v(" "),r("Card",{staticClass:"hasSelect"},[r("CardRow",{attrs:{description:"前台单个主题的编辑权限"}},[r("el-checkbox",{attrs:{label:"switch.thread.edit",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},on:{change:function(t){return e.changeChecked(t,"thread.edit")}},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("编辑主题")])],1),e._v(" "),r("el-cascader",{key:e.keyValue,attrs:{placeholder:"请选择",options:e.categoriesList,disabled:-1===e.checked.indexOf("switch.thread.edit"),props:{value:"id",label:"name",children:"children",multiple:!0,checkStrictly:!0,expandTrigger:"hover"},"collapse-tags":""},on:{change:function(t){return e.changeCategory(t,"thread.edit")},"remove-tag":function(t){return e.clearItem(t,"thread.edit")}},model:{value:e.selectList["thread.edit"],callback:function(t){e.$set(e.selectList,"thread.edit",t)},expression:"selectList['thread.edit']"}})],1),e._v(" "),r("Card",{staticClass:"hasSelect"},[r("CardRow",{attrs:{description:"前台删除单个主题的权限"}},[r("el-checkbox",{attrs:{label:"switch.thread.hide",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},on:{change:function(t){return e.changeChecked(t,"thread.hide")}},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("删除主题")])],1),e._v(" "),r("el-cascader",{key:e.keyValue,attrs:{placeholder:"请选择",options:e.categoriesList,disabled:-1===e.checked.indexOf("switch.thread.hide"),props:{value:"id",label:"name",children:"children",multiple:!0,checkStrictly:!0,expandTrigger:"hover"},"collapse-tags":""},on:{change:function(t){return e.changeCategory(t,"thread.hide")},"remove-tag":function(t){return e.clearItem(t,"thread.hide")}},model:{value:e.selectList["thread.hide"],callback:function(t){e.$set(e.selectList,"thread.hide",t)},expression:"selectList['thread.hide']"}})],1),e._v(" "),r("Card",{staticClass:"hasSelect"},[r("CardRow",{attrs:{description:"前台删除单个回复的权限"}},[r("el-checkbox",{attrs:{label:"switch.thread.hidePosts",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},on:{change:function(t){return e.changeChecked(t,"thread.hidePosts")}},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("删除回复")])],1),e._v(" "),r("el-cascader",{key:e.keyValue,attrs:{placeholder:"请选择",options:e.categoriesList,disabled:-1===e.checked.indexOf("switch.thread.hidePosts"),props:{value:"id",label:"name",children:"children",multiple:!0,checkStrictly:!0,expandTrigger:"hover"},"collapse-tags":""},on:{change:function(t){return e.changeCategory(t,"thread.hidePosts")},"remove-tag":function(t){return e.clearItem(t,"thread.hidePosts")}},model:{value:e.selectList["thread.hidePosts"],callback:function(t){e.$set(e.selectList,"thread.hidePosts",t)},expression:"selectList['thread.hidePosts']"}})],1),e._v(" "),r("Card",{staticClass:"hasSelect"},[r("CardRow",{attrs:{description:"作者编辑自己的主题的权限"}},[r("el-checkbox",{attrs:{label:"switch.thread.editOwnThreadOrPost",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},on:{change:function(t){return e.changeChecked(t,"thread.editOwnThreadOrPost")}},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("编辑自己的主题")])],1),e._v(" "),r("el-cascader",{key:e.keyValue,attrs:{placeholder:"请选择",options:e.categoriesList,disabled:-1===e.checked.indexOf("switch.thread.editOwnThreadOrPost"),props:{value:"id",label:"name",children:"children",multiple:!0,checkStrictly:!0,expandTrigger:"hover"},"collapse-tags":""},on:{change:function(t){return e.changeCategory(t,"thread.editOwnThreadOrPost")},"remove-tag":function(t){return e.clearItem(t,"thread.editOwnThreadOrPost")}},model:{value:e.selectList["thread.editOwnThreadOrPost"],callback:function(t){e.$set(e.selectList,"thread.editOwnThreadOrPost",t)},expression:"selectList['thread.editOwnThreadOrPost']"}})],1),e._v(" "),r("Card",{staticClass:"hasSelect"},[r("CardRow",{attrs:{description:"作者删除自己的主题或回复的权限"}},[r("el-checkbox",{attrs:{label:"switch.thread.hideOwnThreadOrPost",disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},on:{change:function(t){return e.changeChecked(t,"thread.hideOwnThreadOrPost")}},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("删除自己的主题或回复")])],1),e._v(" "),r("el-cascader",{key:e.keyValue,attrs:{placeholder:"请选择",options:e.categoriesList,disabled:-1===e.checked.indexOf("switch.thread.hideOwnThreadOrPost"),props:{value:"id",label:"name",children:"children",multiple:!0,checkStrictly:!0,expandTrigger:"hover"},"collapse-tags":""},on:{change:function(t){return e.changeCategory(t,"thread.hideOwnThreadOrPost")},"remove-tag":function(t){return e.clearItem(t,"thread.hideOwnThreadOrPost")}},model:{value:e.selectList["thread.hideOwnThreadOrPost"],callback:function(t){e.$set(e.selectList,"thread.hideOwnThreadOrPost",t)},expression:"selectList['thread.hideOwnThreadOrPost']"}})],1)],1)]),e._v(" "),r("div",{directives:[{name:"show",rawName:"v-show",value:"security"===e.activeTab.name,expression:"activeTab.name === 'security'"}]},[r("Card",[r("CardRow",{attrs:{description:"启用验证码需先在腾讯云设置中开启验证码服务"}},[r("el-checkbox",{attrs:{label:"createThreadWithCaptcha",disabled:e.captchaDisabled||"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("发布内容时启用验证码")])],1)],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"实名认证后才可发布内容"}},[r("el-checkbox",{attrs:{label:"publishNeedRealName",disabled:e.realNameDisabled||"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("发布内容需先实名认证")])],1)],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:"绑定手机后才可发布内容"}},[r("el-checkbox",{attrs:{label:"publishNeedBindPhone",disabled:e.bindPhoneDisabled||"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("发布内容需先绑定手机")])],1)],1)],1),e._v(" "),r("div",{directives:[{name:"show",rawName:"v-show",value:"default"===e.activeTab.name,expression:"activeTab.name === 'default'"}]},[r("Card",[r("CardRow",{attrs:{description:""}},[r("p",{staticStyle:{"margin-left":"24PX"}},[e._v("站点信息")])])],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:""}},[r("p",{staticStyle:{"margin-left":"24PX"}},[e._v("主题点赞")])])],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:""}},[r("p",{staticStyle:{"margin-left":"24PX"}},[e._v("主题收藏")])])],1),e._v(" "),r("Card",[r("CardRow",{attrs:{description:""}},[r("p",{staticStyle:{"margin-left":"24PX"}},[e._v("主题打赏")])])],1)],1),e._v(" "),r("div",{directives:[{name:"show",rawName:"v-show",value:"other"===e.activeTab.name,expression:"activeTab.name === 'other'"}]},[r("Card",{attrs:{header:"裂变推广："}},[r("CardRow",{attrs:{description:"允许用户裂变推广以及通过推广注册进来的用户收入是否能分成"}},[r("el-checkbox",{attrs:{disabled:"1"===e.$router.history.current.query.id||"7"===e.$router.history.current.query.id},on:{change:e.handlePromotionChange},model:{value:e.isSubordinate,callback:function(t){e.isSubordinate=t},expression:"isSubordinate"}},[e._v("裂变推广")])],1),e._v(" "),e.isSubordinate?r("CardRow",{staticClass:"proportion-box",attrs:{description:"站点开启付费模式时下线付费加入、主题被打赏、被付费等的分成比例设置，填1表示10%，不填或为0时为不分成"}},[r("div",[r("span",[e._v("提成比例")]),e._v(" "),r("el-input",{attrs:{type:"number"},on:{blur:e.checkNum},model:{value:e.scale,callback:function(t){e.scale=t},expression:"scale"}})],1)]):e._e()],1)],1),e._v(" "),r("Card",{staticClass:"footer-btn",class:"userOperate"===e.activeTab.name?"footer-btn__inner":""},[r("el-button",{attrs:{size:"medium",type:"primary"},on:{click:e.submitClick}},[e._v("提交")])],1)],1)},s=[]},yDFb:function(e,t,r){"use strict";r.r(t);var a=r("Iqqv"),s=r.n(a);for(var i in a)["default"].indexOf(i)<0&&function(e){r.d(t,e,(function(){return a[e]}))}(i);t.default=s.a},zxK7:function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var a=h(r("m1cH")),s=h(r("4d7F")),i=h(r("ODRq")),c=h(r("4gYi")),d=h(r("pNQN"));function h(e){return e&&e.__esModule?e:{default:e}}t.default={data:function(){return{groupId:0,checked:[],videoDisabled:!1,captchaDisabled:!1,realNameDisabled:!1,isSubordinate:!1,scale:0,bindPhoneDisabled:!1,categoriesList:[],selectList:{createThread:[],viewThreads:[],"thread.reply":[],"thread.edit":[],"thread.hide":[],"thread.essence":[],"thread.viewPosts":[],"thread.hidePosts":[],"thread.editOwnThreadOrPost":[],"thread.hideOwnThreadOrPost":[],"thread.freeViewPosts":[]},activeTab:{title:"操作权限",name:"userOperate"},menuData:[{title:"操作权限",name:"userOperate"},{title:"安全设置",name:"security"},{title:"其他设置",name:"other"}],value:"",purchasePrice:"",dyedate:"",ispad:"",allowtobuy:"",defaultuser:!1,checkAll:!1,isIndeterminate:!1,selectText:"全选",checkAllPermission:[],temporaryChecked:[],expandItem:["createThread","viewThreads","thread.reply","thread.edit","thread.hide","thread.essence","thread.viewPosts","thread.hidePosts","thread.editOwnThreadOrPost","thread.hideOwnThreadOrPost","thread.freeViewPosts"],mapCategoryId:new i.default,keyValue:0}},watch:{checked:function(e){var t=!0;this.checkAllPermission.forEach((function(r){-1!==e.indexOf(r)||(t=!1)})),this.checkAll=!!t},checkAll:function(e){e?(this.isIndeterminate=!1,this.selectText="取消全选"):(this.isIndeterminate=!0,this.selectText="全选")}},methods:{duedata:function(e){this.duedata=e.replace(/[^\d]/g,"")},addprice:function(e){var t=this;setTimeout((function(){t.purchasePrice=e.replace(/[^\d.]/g,"").replace(/\.{2,}/g,".").replace(".","$#$").replace(/\./g,"").replace("$#$",".").replace(/^(-)*(\d+)\.(\d\d).*$/,"$1$2.$3").replace(/^\./g,"")}),5)},getData:function(){var e=this;s.default.all([this.getCategories(),this.getGroupResource(),this.getSiteInfo()]).then((function(t){e.handleCategories(t[0]),e.handleGroupResource(t[1]),e.signUpSet(t[2])}),(function(e){}))},handleCategories:function(e){var t=this;if(e.errors)return this.$message.error(e.errors[0].code);this.categoriesList=[{id:"",name:"全局",children:[]}],e.Data.forEach((function(e){t.mapCategoryId.set(parseInt(e.pid),e.parentid);var r={id:e.pid,name:e.name,children:[]};e.children&&e.children.forEach((function(e){t.mapCategoryId.set(e.pid,e.parentid),r.children.push({id:e.pid,name:e.name})})),t.categoriesList.push(r)}))},handleGroupResource:function(e){var t=this;if(0!==e.Code)return this.$message.error(e.Code+" "+e.Message);var r=e.Data;this.ispad=r.isPaid,this.scale=r.scale,this.defaultuser=r.default,this.isSubordinate=r.isSubordinate;var a=r.permission||[];this.checked=[],a.forEach((function(e){t.checked.push(e.permission)})),this.setSelectValue(this.checked)},signUpSet:function(e){if(e.errors)return this.$message.error(e.errors[0].code);var t=e.Data,r=e.Data.setSite;this.videoDisabled=!1===t.qcloud.qcloudVod,this.captchaDisabled=!1===t.qcloud.qcloudCaptcha,this.realNameDisabled=!1===t.qcloud.qcloudFaceid,this.bindPhoneDisabled=!1===t.qcloud.qcloudSms,this.allowtobuy=r.sitePayGroupClose},setSelectValue:function(e){var t=this,r=e,a=this.selectList;r.forEach((function(e,s){if(e.includes("category")){var i=e.indexOf("."),c=e.substring(i+1);if(r.includes(c))return void r.splice(s,1);var d=e.substring(8,i),h=t.mapCategoryId.get(parseInt(d)),n=0===h?[d]:[h,d];a[c].push(n)}else t.expandItem.includes(e)&&a[e].push([""])})),this.selectList=a,this.checked=r},submitClick:function(){if(this.checkNum()&&this.checkSelect())if(this.value){if(0==this.purchasePrice)return void this.$message.error("价格不能为0");if(" "==this.purchasePrice)return void this.$message.error("价格不能为空");if(0==this.dyedate)return void this.$message.error("到期时间不能为0");if(" "==this.dyedate)return void this.$message.error("到期时间不能为空");this.patchGroupScale()}else this.patchGroupScale()},getSiteInfo:function(){return this.appFetch({url:"forum_get_v3",method:"get"})},getCategories:function(){return this.appFetch({url:"categories_list_get_v3",method:"get"})},getGroupResource:function(){return this.appFetch({url:"permission_get_v3",method:"get",params:{id:this.groupId,include:"permission"}})},patchGroupPermission:function(){var e=this,t=this.checked;this.isSubordinate?-1===t.indexOf("other.canInviteUserScale")&&t.push("other.canInviteUserScale"):t=t.filter((function(e){return"other.canInviteUserScale"!==e}));var r={groupId:this.groupId,permissions:t};this.appFetch({url:"permission_update_v3",method:"post",data:r}).then((function(t){0===t.Code?(e.$message({showClose:!0,message:"提交成功",type:"success"}),e.getData()):e.$message.error(t.Message)})).catch((function(e){}))},patchGroupScale:function(){var e=this;this.appFetch({url:"groups_batchupdate_post_v3",method:"post",data:{data:[{id:this.groupId,name:this.$route.query.name,scale:this.scale,isSubordinate:this.isSubordinate}]}}).then((function(t){if(t.errors)e.$message.error(t.errors[0].code);else{if(0!==t.Code)return void e.$message.error(t.Message);e.patchGroupPermission()}})).catch((function(e){}))},handlePromotionChange:function(e){this.isSubordinate=e},checkNum:function(){if(!this.scale)return!0;return!!/^([0-9](\.\d)?|10)$/.test(this.scale)||(this.$message({message:"提成比例必须是0~10的整数或者一位小数",type:"error"}),!1)},changeCategory:function(e,t){var r=this,s=e,i=this.checked,c=this.checked.includes(t),d=[],h=[];e.forEach((function(e){if(e.length>1)-1===h.indexOf(e[0])&&(h.push(e[0]),s.push([e[0]]),r.selectList[t]=s.filter((function(e){return""!==e[0]}))),e.map((function(e){d.indexOf(!d.includes(t))&&d.push("category"+e+"."+t)}));else{e[0]&&h.push(e[0]);var a=e[0]?"category"+e[0]+"."+t:t;d.push(a)}})),c?(this.selectList[t]=e.filter((function(e){return""!==e[0]})),d.shift(),i=i.filter((function(e){return e!==t})),i=[].concat((0,a.default)(i),d)):d.includes(t)?(this.selectList[t].splice(1),(i=i.filter((function(e){return!d.includes(e)}))).push(t),this.keyValue=Math.random()):(i=i.filter((function(e){return!(e.includes("category")&&e.includes(t))})),i=[].concat((0,a.default)(i),d)),this.checked=i},clearItem:function(e,t){var r=this.checked,a=e[0]?"category"+e[e.length-1]+"."+t:t;r=r.filter((function(e){return e!==a})),this.selectList[t].shift(),this.checked=r,this.keyValue=Math.random()},changeChecked:function(e,t){if(!e){var r=this.checked,a=this.selectList[t].map((function(e){return e[0]?"category"+e[e.length-1]+"."+t:t}));this.checked=r.filter((function(e){return!a.includes(e)})),this.selectList[t]=[]}},checkSelect:function(){return this.checked.includes("switch.createThread")&&0===this.selectList.createThread.length?(this.$message.error("请选择发布帖子权限"),!1):-1!==this.checked.indexOf("switch.thread.reply")&&0===this.selectList["thread.reply"].length?(this.$message.error("请选择回复主题权限"),!1):-1!==this.checked.indexOf("switch.viewThreads")&&0===this.selectList.viewThreads.length?(this.$message.error("请选择查看主题列表权限"),!1):-1!==this.checked.indexOf("switch.thread.viewPosts")&&0===this.selectList["thread.viewPosts"].length?(this.$message.error("请选择查看主题详情权限"),!1):-1!==this.checked.indexOf("switch.thread.freeViewPosts")&&0===this.selectList["thread.freeViewPosts"].length?(this.$message.error("请选择免费查看付费帖子权限"),!1):-1!==this.checked.indexOf("switch.thread.essence")&&0===this.selectList["thread.essence"].length?(this.$message.error("请选择加精权限"),!1):-1!==this.checked.indexOf("switch.thread.edit")&&0===this.selectList["thread.edit"].length?(this.$message.error("请选择编辑主题权限"),!1):-1!==this.checked.indexOf("switch.thread.hide")&&0===this.selectList["thread.hide"].length?(this.$message.error("请选择删除主题权限"),!1):-1!==this.checked.indexOf("switch.thread.hidePosts")&&0===this.selectList["thread.hidePosts"].length?(this.$message.error("请选择删除回复权限"),!1):-1!==this.checked.indexOf("switch.thread.editOwnThreadOrPost")&&0===this.selectList["thread.editOwnThreadOrPost"].length?(this.$message.error("请选择编辑自己的主题或回复权限"),!1):-1===this.checked.indexOf("switch.thread.hideOwnThreadOrPost")||0!==this.selectList["thread.hideOwnThreadOrPost"].length||(this.$message.error("请选择删除自己的主题或回复权限"),!1)},handleCheckAllChange:function(e){var t,r=this;(this.checked=[],this.selectList={createThread:[],viewThreads:[],"thread.reply":[],"thread.edit":[],"thread.hide":[],"thread.essence":[],"thread.viewPosts":[],"thread.hidePosts":[],"thread.editOwnThreadOrPost":[],"thread.hideOwnThreadOrPost":[],"thread.freeViewPosts":[]},e)?(this.checkAllPermission.forEach((function(e){r.checked.push(e)})),(t=this.checked).push.apply(t,(0,a.default)(this.expandItem)),this.checkAll=!0,this.setSelectValue(this.checked)):this.checkAll=!1}},created:function(){this.groupId=this.$route.query.id,this.activeTab.title=this.$route.query.title||"操作权限",this.activeTab.name=this.$route.query.names||"userOperate",this.getData(),"7"===this.groupId?this.checkAllPermission=["switch.viewThreads","switch.thread.viewPosts","switch.thread.freeViewPosts"]:this.checkAllPermission=["switch.createThread","thread.insertImage","thread.insertVideo","thread.insertAudio","thread.insertAttachment","thread.insertGoods","thread.insertPay","thread.insertReward","thread.insertRedPacket","thread.insertPosition","thread.allowAnonymous","dialog.create","switch.thread.reply","switch.viewThreads","switch.thread.viewPosts","switch.thread.freeViewPosts","thread.sticky","switch.thread.essence","switch.thread.edit","switch.thread.hide","switch.thread.hidePosts","switch.thread.editOwnThreadOrPost","switch.thread.hideOwnThreadOrPost"]},components:{Card:c.default,CardRow:d.default}}}}]);