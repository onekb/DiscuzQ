(window.webpackJsonp=window.webpackJsonp||[]).push([[66],{1139:function(e,t,n){"use strict";var r=n(979);n.n(r).a},1226:function(e,t,n){"use strict";n.r(t);var r=n(23),head=(n(27),n(180),n(61),n(752)),o=n.n(head),l=n(751),c=n.n(l),d=n(179),m=n.n(d),f={name:"SuppleMentary",mixins:[o.a,c.a,m.a],data:function(){return{title:this.$t("user.supplementary"),loading:!1,onUploadImage:!1,id:"",onUploadAttached:!1,dataList:[]}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}},url:function(){return"/api"},imageTypeLimit:function(){return this.forums.set_attach?this.forums.set_attach.support_img_ext.split(",").map((function(e){return".".concat(e)})).join(","):""},attachedTypeLimit:function(){return this.forums.set_attach?this.forums.set_attach.support_file_ext.split(",").map((function(e){return".".concat(e)})).join(","):""},attachedSizeLimit:function(){return this.forums.set_attach?1024*this.forums.set_attach.support_max_size*1024:10485760}},mounted:function(){var e=this.$route.query.id;e&&(this.id=e),this.getData()},methods:{registerConfirm:function(){for(var e=this.dataList,i=0;i<e.length;i++)if(e[i].required){var t=new RegExp("^[ ]+$"),n=e[i].detail;if(!n||n.length<1||t.test(n))return this.$message.error("".concat(e[i].name,"不能为空")),void("string"==typeof n&&(this.dataList[i].detail=""))}this.submit()},onPostContentChange:function(e,t,n){this.dataList[n].detail.push(t[0])},removeItem:function(e,t){this.dataList[t].detail.splice(e,1)},submit:function(){var e=this,t=this.dataList,n={data:[]};t.forEach((function(t){var o=Object(r.a)(t.detail),l="";2===t.type?(t.options.forEach((function(e){e.value===t.detail.value&&(e.checked=!0)})),l=JSON.stringify({options:t.options})):3===t.type?(t.options.forEach((function(e){-1!==t.detail.indexOf(e.value)&&(e.checked=!0)})),l=JSON.stringify({options:t.options})):l="string"===o?t.detail:JSON.stringify(t.detail),n.data.push({type:"user_sign_in",attributes:{sort:t.sort,name:t.name,id:t.id,user_id:e.id,type:t.type,fields_desc:t.fields_desc,type_desc:t.type_desc,required:t.required,fields_ext:l,remark:t.remark,status:t.status}})})),this.$store.dispatch("jv/post",[{_jv:{type:"user/signinfields"}},{data:n}]).then((function(data){e.getUserInfo()}))},getUserInfo:function(){var e=this,t=this.$store.getters["session/get"]("userId");this.$store.dispatch("jv/get",["users/".concat(t),{params:{include:"groups"}}]).then((function(t){e.$router.push("/")})).catch((function(t){var n=t.response.data.errors;if(Array.isArray(n)&&n.length>0&&"register_validate"===n[0].code)return e.$store.commit("session/SET_AUDIT_INFO",{errorCode:"register_validate",username:n[0].data.userName}),void e.$router.push("/user/warning")}))},getData:function(){var e=this,t={user_id:this.id};this.$store.dispatch("jv/get",["user/signinfields",{params:t}]).then((function(t){t.forEach((function(e,i){!e.fields_ext||2!==e.type&&3!==e.type||(e.options=JSON.parse(e.fields_ext).options),e.detail=e.type>2?[]:""})),e.dataList=t}),(function(t){return e.handleError(t)}))}}},h=(n(1139),n(11)),component=Object(h.a)(f,(function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{staticClass:"supple"},[n("h2",{staticClass:"supple-title"},[e._v(e._s(e.$t("user.supplementary")))]),e._v(" "),e._l(e.dataList,(function(t,r){return n("div",{key:r,staticClass:"supple-content"},[n("div",{staticClass:"supple-content__item"},[n("div",{staticClass:"supple-content__item__title"},[e._v("\n        "+e._s(r+1+"."+t.name)+"\n        "),t.required?n("span",{staticClass:"supple-content__item__title-necessary"},[e._v("*\n        ")]):e._e()]),e._v(" "),n("div",{staticClass:"supple-content__item__detail"},[0===t.type?n("el-input",{attrs:{clearable:"",placeholder:e.$t("user.inputcontent")},model:{value:t.detail,callback:function(n){e.$set(t,"detail",n)},expression:"item.detail"}}):1===t.type?n("el-input",{attrs:{clearable:"",type:"textarea",autosize:{minRows:2,maxRows:4},placeholder:e.$t("user.inputcontent")},model:{value:t.detail,callback:function(n){e.$set(t,"detail",n)},expression:"item.detail"}}):2===t.type?n("el-radio-group",{model:{value:t.detail,callback:function(n){e.$set(t,"detail",n)},expression:"item.detail"}},e._l(t.options,(function(t){return n("el-radio",{key:t.value,attrs:{label:t}},[e._v(e._s(t.value)+"\n          ")])})),1):3===t.type?n("el-checkbox-group",{model:{value:t.detail,callback:function(n){e.$set(t,"detail",n)},expression:"item.detail"}},e._l(t.options,(function(e){return n("el-checkbox",{key:e.value,attrs:{label:e.value}})})),1):4===t.type?n("div",{staticClass:"supple-content__item__detail-image"},[n("image-upload",{attrs:{"on-upload":e.onUploadImage,type:1,"file-list":[],action:"/attachments",accept:e.imageTypeLimit,"size-limit":e.attachedSizeLimit},on:{"update:onUpload":function(t){e.onUploadImage=t},"update:on-upload":function(t){e.onUploadImage=t},success:function(t){return e.onPostContentChange("imageList",t,r)},remove:function(t,n){return e.removeItem(n,r)}}})],1):5===t.type?n("div",{staticClass:"supple-content__item__detail-file"},[n("attachment-upload",{attrs:{"file-list":[],"on-upload":e.onUploadAttached,action:"/attachments",accept:e.attachedTypeLimit,type:0,"size-limit":e.attachedSizeLimit,"add-tips":e.$t("post.postAttachment")},on:{"update:onUpload":function(t){e.onUploadAttached=t},"update:on-upload":function(t){e.onUploadAttached=t},success:function(t){return e.onPostContentChange("attachedList",t,r)},remove:function(t,n){return e.removeItem(n,r)}}})],1):e._e()],1)])])})),e._v(" "),n("el-button",{staticClass:"r-button",attrs:{type:"primary"},on:{click:e.registerConfirm}},[e._v("\n    "+e._s(e.$t("modify.submit"))+"\n  ")])],2)}),[],!1,null,"e645c398",null);t.default=component.exports;installComponents(component,{ImageUpload:n(841).default,AttachmentUpload:n(840).default})},751:function(e,t,n){n(32);var r=n(733);n(51),e.exports={data:function(){var e=this;return{errorCodeHandler:{default:{model_not_found:function(){return e.$router.replace("/error")},not_authenticated:function(){return e.$router.push("/user/login")}},thread:{permission_denied:function(){return e.$router.replace("/error")}}}}},methods:{handleError:function(e){var t=arguments,n=this;return r(regeneratorRuntime.mark((function r(){var o,l,c,d,m,f;return regeneratorRuntime.wrap((function(r){for(;;)switch(r.prev=r.next){case 0:if(o=t.length>1&&void 0!==t[1]?t[1]:"",l=e.response.data.errors,!(Array.isArray(l)&&l.length>0)){r.next=17;break}if(c=l[0].code,d=l[0].detail&&l[0].detail.length>0?l[0].detail[0]:l[0].code,m=l[0].detail&&l[0].detail.length>0?l[0].detail[0]:n.$t("core.".concat(d)),"site_closed"!==l[0].code){r.next=10;break}return r.next=9,n.siteClose(l);case 9:return r.abrupt("return",r.sent);case 10:if("need_ext_fields"!==l[0].code){r.next=14;break}return f=n.$store.getters["session/get"]("userId"),n.$router.push("/user/supple-mentary?id=".concat(f)),r.abrupt("return");case 14:"Permission Denied"===c?n.$message.error(n.$t("core.permission_denied2")):n.$message.error(m),n.errorCodeHandler.default[c]&&n.errorCodeHandler.default[c](),o&&n.errorCodeHandler[o][c]&&n.errorCodeHandler[o][c]();case 17:case"end":return r.stop()}}),r)})))()},siteClose:function(e){var t=this;return r(regeneratorRuntime.mark((function n(){return regeneratorRuntime.wrap((function(n){for(;;)switch(n.prev=n.next){case 0:return n.prev=0,n.next=3,t.$store.dispatch("forum/setError",{code:e[0].code,detail:e[0].detail&&e[0].detail.length>0&&e[0].detail[0]});case 3:return n.next=5,t.$router.push("/site/close");case 5:n.next=9;break;case 7:n.prev=7,n.t0=n.catch(0);case 9:case"end":return n.stop()}}),n,null,[[0,7]])})))()}}}},752:function(e,t){e.exports={data:function(){return{title:"‎"}},computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},head:function(){return{title:"‎"!==this.title&&this.forums&&this.forums.set_site&&this.forums.set_site.site_name?"".concat(this.title," - ").concat(this.forums.set_site.site_name):this.title}}}},768:function(e,t){t.extensionList=["7Z","AI","APK","CAD","CDR","DOC","DOCX","EPS","EXE","IPA","MP3","MP4","PDF","PPT","PSD","RAR","TXT","XLS","XLSX","ZIP","JPG","WAV"]},772:function(e,t,n){},773:function(e,t,n){},802:function(e,t,n){e.exports={methods:{handleAttachmentError:function(e){if(e&&e.message){var t=e.message.errors,n=t[0].detail?t[0].detail[0]:t[0].code,r=t[0].detail?t[0].detail[0]:this.$t("core.".concat(n));this.$message.error(r)}else this.$message.error(this.$t("post.imageUploadFail"))}}}},803:function(e,t,n){n(17),n(36),n(30),n(12),n(27);var r=n(736);e.exports={watch:{fileList:{handler:function(){var e;this.fileList.length>this.previewFiles.length&&0===this.previewFiles.length&&((e=this.previewFiles).push.apply(e,r(this.fileList)),this.previewFiles.map((function(e){e.progress=100})))},deep:!0,immediate:!0}},methods:{uploaderFile:function(e){e.dispatchEvent(new MouseEvent("click"))},onInput:function(e){var t=this,n=e.target.files,r=[];if(this.onUpload)return this.$message.warning(0===this.type?"请等待上传中的文件完成上传":"请等待上传中的图片完成上传");if(this.checkSizeLimit(n)&&this.checkLengthLimit(n)){for(var i=0;i<n.length;i++){var o=this.getObjectURL(n[i]);this.previewFiles.push({name:n[i].name,url:o,progress:0,deleted:!1,size:n[i].size}),r.push(n[i])}var l=r.reduce((function(e,n,r,o){return e.push(t.uploadFile(n,r,o.length)),e}),[]);this.uploadFiles(l)}},uploadFile:function(e,t,n){var r=this,o={onUploadProgress:function(e){e.lengthComputable?r.previewFiles[r.previewFiles.length-n+t].progress=.9*parseInt(Math.round(e.loaded/e.total*100).toString()):r.previewFiles[r.previewFiles.length-n+t].progress=100}},l=new FormData;return l.append("type",this.type),l.append("file",e),this.service.post(this.action,l,o)},uploadFiles:function(e){var t=this;this.$emit("update:onUpload",!0),Promise.all(e).then((function(e){t.previewFiles.map((function(e){e.progress=100}));var n=e.map((function(e){return e.data.data})),o=r(t.fileList);n.forEach((function(e){return o.push({id:e.id,name:e.attributes.fileName,url:e.attributes.url})})),t.currentInput.value="",t.$emit("success",o),t.$emit("update:onUpload",!1)}),(function(n){t.currentInput.value="";var r=e.length;t.$emit("update:onUpload",!1),t.previewFiles.splice(t.previewFiles.length-r,r),t.handleError(n).then((function(){}))}))},removeItem:function(e){var t=this;this.$confirm(this.$t("topic.confirmDelete"),this.$t("discuzq.msgBox.title"),{confirmButtonText:this.$t("discuzq.msgBox.confirm"),cancelButtonText:this.$t("discuzq.msgBox.cancel"),type:"warning"}).then((function(){t.previewFiles[e].deleted=!0;var n=r(t.fileList);n.splice(e,1),t.$emit("remove",n,e),setTimeout((function(){t.previewFiles.splice(e,1),t.$message.success("删除成功")}),900)}),(function(){}))},checkSizeLimit:function(e){for(var t=!0,i=0;i<e.length;i++)e[i].size>this.sizeLimit&&(t=!1);return t||this.$message.error(0===this.type?"文件大小不可超过 ".concat(this.sizeLimit/1024/1024," MB"):"图片大小不可超过 ".concat(this.sizeLimit/1024/1024," MB")),t},checkLengthLimit:function(e){return!(this.previewFiles.length+e.length>this.limit)||(this.$message.warning(0===this.type?"文件最多上传".concat(this.limit,"张"):"图片最多上传".concat(this.limit,"张")),this.$emit("exceed",e),!1)},getObjectURL:function(e){var t=null;return window.createObjectURL?t=window.createObjectURL(e):window.URL?t=window.URL.createObjectURL(e):window.webkitURL&&(t=window.webkitURL.createObjectURL(e)),t}}}},837:function(e,t,n){"use strict";var r=n(772);n.n(r).a},838:function(e,t,n){"use strict";var r=n(773);n.n(r).a},840:function(e,t,n){"use strict";n.r(t);n(61),n(269);var r=n(751),o=n.n(r),l=n(802),c=n.n(l),d=n(768),m=n(803),f=n.n(m),h=n(33),v={name:"AttachmentUpload",mixins:[o.a,c.a,f.a],props:{action:{type:String,default:"",required:!0},fileList:{type:Array,default:function(){return[]},required:!0},accept:{type:String,default:""},limit:{type:Number,default:9999},sizeLimit:{type:Number,default:1e17},onUpload:{type:Boolean,default:!1},addTips:{type:String,default:""},type:{type:Number,default:0}},data:function(){return{previewFiles:[],currentInput:""}},computed:{input:function(){return document.getElementById("upload")},service:function(){return h.a}},methods:{onClick:function(){var e=this.$refs.uploadFile;this.currentInput=e,this.uploaderFile(e)},extensionValidate:function(e){var t=e.split(".")[e.split(".").length-1];return d.extensionList.indexOf(t.toUpperCase())>0?t.toUpperCase():"UNKNOWN"}}},_=(n(837),n(11)),component=Object(_.a)(v,(function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{directives:[{name:"viewer",rawName:"v-viewer"}],staticClass:"container-upload"},[n("div",{staticClass:"upload"},[n("span",{staticClass:"attachment-list"},[e._v(e._s(e.$t("post.attachmentList")))]),e._v(" "),n("span",{staticClass:"add-attachment",on:{click:e.onClick}},[e._v(e._s(e.addTips||e.$t("post.addAttachment")))]),e._v(" "),n("input",{ref:"uploadFile",attrs:{id:"upload",accept:e.accept,type:"file",multiple:""},on:{input:e.onInput}})]),e._v(" "),e._l(e.previewFiles,(function(t,r){return n("div",{key:r,class:{"preview-item":!0,deleted:t.deleted}},[n("div",{staticClass:"container-item"},[n("div",{staticClass:"info"},[n("svg-icon",{staticStyle:{"font-size":"18px","vertical-align":"middle"},attrs:{type:e.extensionValidate(t.name)}}),e._v(" "),n("span",{class:{"file-name":!0,uploading:t.progress<100}},[e._v(e._s(t.name))])],1),e._v(" "),n("span",{staticClass:"size"},[e._v(e._s(parseInt((t.size/1024).toString()).toLocaleString())+"\n        KB")]),e._v(" "),n("div",{directives:[{name:"show",rawName:"v-show",value:t.progress<100,expression:"file.progress < 100"}],staticClass:"progress",style:{width:t.progress+"%"}})]),e._v(" "),n("div",{staticClass:"remove"},[n("svg-icon",{directives:[{name:"show",rawName:"v-show",value:t.progress&&100===t.progress,expression:"file.progress && file.progress === 100"}],staticClass:"remove-icon",attrs:{type:"delete"},on:{click:function(t){return e.removeItem(r)}}})],1)])}))],2)}),[],!1,null,"4e4dc3b6",null);t.default=component.exports;installComponents(component,{SvgIcon:n(60).default})},841:function(e,t,n){"use strict";n.r(t);n(269);var r=n(751),o=n.n(r),l=n(802),c=n.n(l),d=n(803),m=n.n(d),f=n(33),h={name:"ImageUpload",mixins:[o.a,c.a,m.a],props:{action:{type:String,default:"",required:!0},fileList:{type:Array,default:function(){return[]},required:!0},accept:{type:String,default:""},limit:{type:Number,default:9999},sizeLimit:{type:Number,default:1e17},onUpload:{type:Boolean,default:!1},type:{type:Number,default:0}},data:function(){return{previewFiles:[],currentInput:""}},computed:{input:function(){return document.getElementById("upload")},service:function(){return f.a}},methods:{onClick:function(){var e=this.$refs.uploadImage;this.currentInput=e,this.uploaderFile(e)}}},v=(n(838),n(11)),component=Object(v.a)(h,(function(){var e=this,t=e.$createElement,n=e._self._c||t;return n("div",{directives:[{name:"viewer",rawName:"v-viewer"}],staticClass:"container-upload"},[e._l(e.previewFiles,(function(image,t){return n("div",{key:t,class:{"preview-item":!0,deleted:image.deleted}},[n("img",{attrs:{src:image.url,alt:""}}),e._v(" "),n("el-progress",{directives:[{name:"show",rawName:"v-show",value:image.progress<100,expression:"image.progress < 100"}],staticClass:"progress",attrs:{percentage:image.progress,color:"#25A9F6","show-text":!1}}),e._v(" "),n("div",{directives:[{name:"show",rawName:"v-show",value:image.progress<100,expression:"image.progress < 100"}],staticClass:"cover"},[e._v("图片上传中...")]),e._v(" "),n("div",{class:{"upload-delete":!0,"show-delete":100===image.progress},on:{click:function(n){return e.removeItem(t)}}},[n("svg-icon",{staticStyle:{"font-size":"14px",fill:"white"},attrs:{type:"delete"}})],1)],1)})),e._v(" "),n("div",{directives:[{name:"show",rawName:"v-show",value:e.previewFiles.length<e.limit,expression:"previewFiles.length < limit"}],staticClass:"upload",on:{click:function(t){return e.onClick(t)}}},[n("input",{ref:"uploadImage",attrs:{id:"upload",accept:e.accept,type:"file",multiple:""},on:{input:e.onInput}}),e._v(" "),n("svg-icon",{staticClass:"upload-icon",attrs:{type:"add"}})],1)],2)}),[],!1,null,"42e9b14c",null);t.default=component.exports;installComponents(component,{SvgIcon:n(60).default})},979:function(e,t,n){}}]);