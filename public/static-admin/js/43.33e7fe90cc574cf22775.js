(window.webpackJsonp=window.webpackJsonp||[]).push([[43],{"4QZA":function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var a=i(r("QbLZ")),s=i(r("56wF"));function i(e){return e&&e.__esModule?e:{default:e}}r("lpfh"),t.default=(0,a.default)({name:"sign-up-set-view"},s.default)},"56wF":function(e,t,r){"use strict";Object.defineProperty(t,"__esModule",{value:!0});var a=i(r("4gYi")),s=i(r("pNQN"));function i(e){return e&&e.__esModule?e:{default:e}}t.default={data:function(){return{checked:"",register_validate:"",pwdLength:"",checkList:[],register_captcha:"",disabled:!0,register_type:0,qcloud_sms:!0,qcloud_wx:!0,privacy:"0",register:"0",register_content:"",privacy_content:"",registerFull:!1,privacyFull:!1}},created:function(){this.signUpSet()},methods:{signUpSet:function(){var e=this;this.appFetch({url:"forum",method:"get",data:{"filter[tag]":"agreement"}}).then((function(t){if(t.errors)e.$message.error(t.errors[0].code);else{var r=t.readdata._data.agreement;e.checked=t.readdata._data.set_reg.register_close,e.register_validate=t.readdata._data.set_reg.register_validate,e.pwdLength=t.readdata._data.set_reg.password_length,e.checkList=t.readdata._data.set_reg.password_strength,e.register_captcha=t.readdata._data.set_reg.register_captcha,e.register_type=t.readdata._data.set_reg.register_type,e.privacy=r.privacy?"1":"0",e.register=r.register?"1":"0",e.register_content=r.register_content,e.privacy_content=r.privacy_content,1==t.readdata._data.qcloud.qcloud_sms&&(e.qcloud_sms=!1),1==t.readdata._data.passport.offiaccount_close&&(e.qcloud_wx=!1),1==t.readdata._data.qcloud.qcloud_captcha&&(e.disabled=!1)}}))},changeRegister:function(e){this.register=e,"0"===e&&(this.register_content="")},changePrivacy:function(e){this.privacy=e,"0"===e&&(this.privacy_content="")},changeSize:function(e){this[e]=!this[e]},submission:function(){var e=this,t=(this.pwdLength,this.checkList.join(","));this.appFetch({url:"settings",method:"post",data:{data:[{attributes:{key:"register_close",value:this.checked,tag:"default"}},{attributes:{key:"register_validate",value:this.register_validate,tag:"default"}},{attributes:{key:"register_captcha",value:this.register_captcha,tag:"default"}},{attributes:{key:"privacy",value:this.privacy,tag:"agreement"}},{attributes:{key:"register",value:this.register,tag:"agreement"}},{attributes:{key:"register_content",value:this.register_content?this.register_content:"",tag:"agreement"}},{attributes:{key:"privacy_content",value:this.privacy_content?this.privacy_content:"",tag:"agreement"}},{attributes:{key:"password_length",value:this.pwdLength,tag:"default"}},{attributes:{key:"password_strength",value:t,tag:"default"}},{attributes:{key:"register_type",value:this.register_type,tag:"default"}}]}}).then((function(t){t.errors?t.errors[0].detail?e.$message.error(t.errors[0].code+"\n"+t.errors[0].detail[0]):e.$message.error(t.errors[0].code):e.$message({message:"提交成功",type:"success"})}))}},components:{Card:a.default,CardRow:s.default}}},"e+0/":function(e,t,r){"use strict";r.r(t);var a=r("g8KI"),s=r("pOCi");for(var i in s)["default"].indexOf(i)<0&&function(e){r.d(t,e,(function(){return s[e]}))}(i);var c=r("KHd+"),n=Object(c.a)(s.default,a.a,a.b,!1,null,null,null);t.default=n.exports},g8KI:function(e,t,r){"use strict";r.d(t,"a",(function(){return a})),r.d(t,"b",(function(){return s}));var a=function(){var e=this,t=e.$createElement,r=e._self._c||t;return r("div",{staticClass:"sign-up-set-box"},[r("Card",{attrs:{header:"新用户注册："}},[r("CardRow",{attrs:{description:"设置是否允许游客注册成为会员"}},[r("el-checkbox",{model:{value:e.checked,callback:function(t){e.checked=t},expression:"checked"}},[e._v("允许新用户注册")])],1)],1),e._v(" "),r("Card",{attrs:{header:"注册模式："}},[r("CardRow",{attrs:{description:"开启无感模式后，微信下将自动注册。开启手机号模式后，将用手机号的方式注册登录。开启\n用户名模式后，将以用户名的方式注册登录。"}},[r("el-radio",{attrs:{label:0},model:{value:e.register_type,callback:function(t){e.register_type=t},expression:"register_type"}},[e._v(" 用户名模式 ")]),e._v(" "),r("el-radio",{attrs:{label:1,disabled:e.qcloud_sms},model:{value:e.register_type,callback:function(t){e.register_type=t},expression:"register_type"}},[e._v("手机号模式")]),e._v(" "),r("el-radio",{attrs:{label:2,disabled:e.qcloud_wx},model:{value:e.register_type,callback:function(t){e.register_type=t},expression:"register_type"}},[e._v("无感模式")])],1)],1),e._v(" "),r("Card",{attrs:{header:"新用户审核："}},[r("CardRow",{attrs:{description:"设置新注册的用户是否需要审核"}},[r("el-checkbox",{model:{value:e.register_validate,callback:function(t){e.register_validate=t},expression:"register_validate"}},[e._v("新用户注册审核")])],1)],1),e._v(" "),r("Card",{attrs:{header:"启用验证码："}},[r("CardRow",{attrs:{description:"启用验证码需先在腾讯云设置中开启验证码服务"}},[r("el-checkbox",{attrs:{disabled:e.disabled},model:{value:e.register_captcha,callback:function(t){e.register_captcha=t},expression:"register_captcha"}},[e._v("新用户注册启用验证码")])],1)],1),e._v(" "),r("Card",{attrs:{header:"注册密码最小长度："}},[r("CardRow",{attrs:{description:"新用户注册时密码最小长度，0或不填为不限制"}},[r("el-input",{attrs:{type:"number",clearable:""},model:{value:e.pwdLength,callback:function(t){e.pwdLength=t},expression:"pwdLength"}})],1)],1),e._v(" "),r("Card",{attrs:{header:"密码字符类型："}},[r("CardRow",{attrs:{description:"新用户注册时密码中必须存在所选字符类型，不选则为无限制"}},[r("el-checkbox-group",{model:{value:e.checkList,callback:function(t){e.checkList=t},expression:"checkList"}},[r("el-checkbox",{attrs:{label:"0"}},[e._v("数字")]),e._v(" "),r("el-checkbox",{attrs:{label:"1"}},[e._v("小写字母")]),e._v(" "),r("el-checkbox",{attrs:{label:"2"}},[e._v("符号")]),e._v(" "),r("el-checkbox",{attrs:{label:"3"}},[e._v("大写字母")])],1)],1)],1),e._v(" "),r("Card",{staticClass:"card-radio-con",attrs:{header:"用户协议："}},[r("CardRow",{attrs:{description:"新用户注册时显示网站用户协议"}},[r("el-radio",{attrs:{label:"1"},on:{change:function(t){return e.changeRegister("1")}},model:{value:e.register,callback:function(t){e.register=t},expression:"register"}},[e._v("是")]),e._v(" "),r("el-radio",{attrs:{label:"0"},on:{change:function(t){return e.changeRegister("0")}},model:{value:e.register,callback:function(t){e.register=t},expression:"register"}},[e._v("否")])],1)],1),e._v(" "),r("Card",{directives:[{name:"show",rawName:"v-show",value:"1"===e.register,expression:"register === '1'"}],class:{fullScreen:e.registerFull}},[r("CardRow",{attrs:{description:"用户协议的详细内容 双击输入框可扩大/缩小"}},[r("el-input",{attrs:{type:"textarea",autosize:{minRows:4,maxRows:4},placeholder:"用户协议"},nativeOn:{dblclick:function(t){return e.changeSize("registerFull")}},model:{value:e.register_content,callback:function(t){e.register_content=t},expression:"register_content"}})],1)],1),e._v(" "),r("Card",{staticClass:"card-radio-con",attrs:{header:"隐私政策："}},[r("CardRow",{attrs:{description:"新用户注册时显示网站隐私政策"}},[r("el-radio",{attrs:{label:"1"},on:{change:function(t){return e.changePrivacy("1")}},model:{value:e.privacy,callback:function(t){e.privacy=t},expression:"privacy"}},[e._v("是")]),e._v(" "),r("el-radio",{attrs:{label:"0"},on:{change:function(t){return e.changePrivacy("0")}},model:{value:e.privacy,callback:function(t){e.privacy=t},expression:"privacy"}},[e._v("否")])],1)],1),e._v(" "),r("Card",{directives:[{name:"show",rawName:"v-show",value:"1"===e.privacy,expression:"privacy === '1'"}],class:{fullScreen:e.privacyFull}},[r("CardRow",{attrs:{description:"隐私政策的详细内容 双击输入框可扩大/缩小"}},[r("el-input",{attrs:{type:"textarea",autosize:{minRows:4,maxRows:4},placeholder:"隐私政策"},nativeOn:{dblclick:function(t){return e.changeSize("privacyFull")}},model:{value:e.privacy_content,callback:function(t){e.privacy_content=t},expression:"privacy_content"}})],1)],1),e._v(" "),r("Card",{staticClass:"footer-btn"},[r("el-button",{attrs:{type:"primary",size:"medium"},on:{click:e.submission}},[e._v("提交")])],1)],1)},s=[]},pOCi:function(e,t,r){"use strict";r.r(t);var a=r("4QZA"),s=r.n(a);for(var i in a)["default"].indexOf(i)<0&&function(e){r.d(t,e,(function(){return a[e]}))}(i);t.default=s.a}}]);