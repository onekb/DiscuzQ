(window.webpackJsonp_N_E=window.webpackJsonp_N_E||[]).push([[83],{"+qE3":function(e,t,r){"use strict";var o,n="object"===typeof Reflect?Reflect:null,i=n&&"function"===typeof n.apply?n.apply:function(e,t,r){return Function.prototype.apply.call(e,t,r)};o=n&&"function"===typeof n.ownKeys?n.ownKeys:Object.getOwnPropertySymbols?function(e){return Object.getOwnPropertyNames(e).concat(Object.getOwnPropertySymbols(e))}:function(e){return Object.getOwnPropertyNames(e)};var s=Number.isNaN||function(e){return e!==e};function a(){a.init.call(this)}e.exports=a,e.exports.once=function(e,t){return new Promise((function(r,o){function n(r){e.removeListener(t,i),o(r)}function i(){"function"===typeof e.removeListener&&e.removeListener("error",n),r([].slice.call(arguments))}y(e,t,i,{once:!0}),"error"!==t&&function(e,t,r){"function"===typeof e.on&&y(e,"error",t,r)}(e,n,{once:!0})}))},a.EventEmitter=a,a.prototype._events=void 0,a.prototype._eventsCount=0,a.prototype._maxListeners=void 0;var u=10;function p(e){if("function"!==typeof e)throw new TypeError('The "listener" argument must be of type Function. Received type '+typeof e)}function c(e){return void 0===e._maxListeners?a.defaultMaxListeners:e._maxListeners}function l(e,t,r,o){var n,i,s,a;if(p(r),void 0===(i=e._events)?(i=e._events=Object.create(null),e._eventsCount=0):(void 0!==i.newListener&&(e.emit("newListener",t,r.listener?r.listener:r),i=e._events),s=i[t]),void 0===s)s=i[t]=r,++e._eventsCount;else if("function"===typeof s?s=i[t]=o?[r,s]:[s,r]:o?s.unshift(r):s.push(r),(n=c(e))>0&&s.length>n&&!s.warned){s.warned=!0;var u=new Error("Possible EventEmitter memory leak detected. "+s.length+" "+String(t)+" listeners added. Use emitter.setMaxListeners() to increase limit");u.name="MaxListenersExceededWarning",u.emitter=e,u.type=t,u.count=s.length,a=u,console&&console.warn&&console.warn(a)}return e}function d(){if(!this.fired)return this.target.removeListener(this.type,this.wrapFn),this.fired=!0,0===arguments.length?this.listener.call(this.target):this.listener.apply(this.target,arguments)}function f(e,t,r){var o={fired:!1,wrapFn:void 0,target:e,type:t,listener:r},n=d.bind(o);return n.listener=r,o.wrapFn=n,n}function h(e,t,r){var o=e._events;if(void 0===o)return[];var n=o[t];return void 0===n?[]:"function"===typeof n?r?[n.listener||n]:[n]:r?function(e){for(var t=new Array(e.length),r=0;r<t.length;++r)t[r]=e[r].listener||e[r];return t}(n):m(n,n.length)}function v(e){var t=this._events;if(void 0!==t){var r=t[e];if("function"===typeof r)return 1;if(void 0!==r)return r.length}return 0}function m(e,t){for(var r=new Array(t),o=0;o<t;++o)r[o]=e[o];return r}function y(e,t,r,o){if("function"===typeof e.on)o.once?e.once(t,r):e.on(t,r);else{if("function"!==typeof e.addEventListener)throw new TypeError('The "emitter" argument must be of type EventEmitter. Received type '+typeof e);e.addEventListener(t,(function n(i){o.once&&e.removeEventListener(t,n),r(i)}))}}Object.defineProperty(a,"defaultMaxListeners",{enumerable:!0,get:function(){return u},set:function(e){if("number"!==typeof e||e<0||s(e))throw new RangeError('The value of "defaultMaxListeners" is out of range. It must be a non-negative number. Received '+e+".");u=e}}),a.init=function(){void 0!==this._events&&this._events!==Object.getPrototypeOf(this)._events||(this._events=Object.create(null),this._eventsCount=0),this._maxListeners=this._maxListeners||void 0},a.prototype.setMaxListeners=function(e){if("number"!==typeof e||e<0||s(e))throw new RangeError('The value of "n" is out of range. It must be a non-negative number. Received '+e+".");return this._maxListeners=e,this},a.prototype.getMaxListeners=function(){return c(this)},a.prototype.emit=function(e){for(var t=[],r=1;r<arguments.length;r++)t.push(arguments[r]);var o="error"===e,n=this._events;if(void 0!==n)o=o&&void 0===n.error;else if(!o)return!1;if(o){var s;if(t.length>0&&(s=t[0]),s instanceof Error)throw s;var a=new Error("Unhandled error."+(s?" ("+s.message+")":""));throw a.context=s,a}var u=n[e];if(void 0===u)return!1;if("function"===typeof u)i(u,this,t);else{var p=u.length,c=m(u,p);for(r=0;r<p;++r)i(c[r],this,t)}return!0},a.prototype.addListener=function(e,t){return l(this,e,t,!1)},a.prototype.on=a.prototype.addListener,a.prototype.prependListener=function(e,t){return l(this,e,t,!0)},a.prototype.once=function(e,t){return p(t),this.on(e,f(this,e,t)),this},a.prototype.prependOnceListener=function(e,t){return p(t),this.prependListener(e,f(this,e,t)),this},a.prototype.removeListener=function(e,t){var r,o,n,i,s;if(p(t),void 0===(o=this._events))return this;if(void 0===(r=o[e]))return this;if(r===t||r.listener===t)0===--this._eventsCount?this._events=Object.create(null):(delete o[e],o.removeListener&&this.emit("removeListener",e,r.listener||t));else if("function"!==typeof r){for(n=-1,i=r.length-1;i>=0;i--)if(r[i]===t||r[i].listener===t){s=r[i].listener,n=i;break}if(n<0)return this;0===n?r.shift():function(e,t){for(;t+1<e.length;t++)e[t]=e[t+1];e.pop()}(r,n),1===r.length&&(o[e]=r[0]),void 0!==o.removeListener&&this.emit("removeListener",e,s||t)}return this},a.prototype.off=a.prototype.removeListener,a.prototype.removeAllListeners=function(e){var t,r,o;if(void 0===(r=this._events))return this;if(void 0===r.removeListener)return 0===arguments.length?(this._events=Object.create(null),this._eventsCount=0):void 0!==r[e]&&(0===--this._eventsCount?this._events=Object.create(null):delete r[e]),this;if(0===arguments.length){var n,i=Object.keys(r);for(o=0;o<i.length;++o)"removeListener"!==(n=i[o])&&this.removeAllListeners(n);return this.removeAllListeners("removeListener"),this._events=Object.create(null),this._eventsCount=0,this}if("function"===typeof(t=r[e]))this.removeListener(e,t);else if(void 0!==t)for(o=t.length-1;o>=0;o--)this.removeListener(e,t[o]);return this},a.prototype.listeners=function(e){return h(this,e,!0)},a.prototype.rawListeners=function(e){return h(this,e,!1)},a.listenerCount=function(e,t){return"function"===typeof e.listenerCount?e.listenerCount(t):v.call(e,t)},a.prototype.listenerCount=v,a.prototype.eventNames=function(){return this._eventsCount>0?o(this._events):[]}},"4fRq":function(e,t){var r="undefined"!=typeof crypto&&crypto.getRandomValues&&crypto.getRandomValues.bind(crypto)||"undefined"!=typeof msCrypto&&"function"==typeof window.msCrypto.getRandomValues&&msCrypto.getRandomValues.bind(msCrypto);if(r){var o=new Uint8Array(16);e.exports=function(){return r(o),o}}else{var n=new Array(16);e.exports=function(){for(var e,t=0;t<16;t++)0===(3&t)&&(e=4294967296*Math.random()),n[t]=e>>>((3&t)<<3)&255;return n}}},Bhkv:function(e,t,r){"use strict";var o=this&&this.__assign||function(){return(o=Object.assign||function(e){for(var t,r=1,o=arguments.length;r<o;r++)for(var n in t=arguments[r])Object.prototype.hasOwnProperty.call(t,n)&&(e[n]=t[n]);return e}).apply(this,arguments)};Object.defineProperty(t,"__esModule",{value:!0});var n=r("s3hC"),i=r("YZgg"),s=function(){function e(e){this.allowReport=!0,this.enableResume=!0,this.getSignature=e.getSignature,void 0!==e.allowReport&&(this.allowReport=e.allowReport),void 0!==e.enableResume&&(this.enableResume=e.enableResume),this.appId=e.appId,this.reportId=e.reportId}return e.prototype.upload=function(e){var t=o({getSignature:this.getSignature,appId:this.appId,reportId:this.reportId,enableResume:this.enableResume},e),r=new n.default(t);return this.allowReport&&this.initReporter(r),r.start(),r},e.prototype.initReporter=function(e){new i.VodReporter(e)},e}();t.default=s},FRsH:function(e){e.exports=JSON.parse('{"name":"vod-js-sdk-v6","version":"1.4.11","description":"tencent cloud vod js sdk v6","main":"lib/src/tc_vod.js","unpkg":"dist/vod-js-sdk-v6.js","typings":"lib/src/tc_vod.d.ts","scripts":{"test":"cross-env NODE_ENV=test mocha -r espower-typescript/guess -r jsdom-global/register -r test/env test/**/*.test.ts","cover":"cross-env NODE_ENV=test nyc mocha -r espower-typescript/guess -r jsdom-global/register -r test/env test/**/*.test.ts","dev":"webpack --config webpack.dev.config.js --watch","dist":"webpack --config webpack.config.js","build":"npm run test && npm run dist && npm run compile","compile":"tsc -p tsconfig.json","prepublish":"npm run build","lint":"tsc --noEmit && eslint \'src/**/*.{js,ts,tsx}\' --quiet --fix"},"repository":{"type":"git","url":"git+https://github.com/tencentyun/vod-js-sdk-v6.git"},"keywords":["tencentcloud","sdk","vod"],"author":"alsotang <alsotang@gmail.com>","license":"MIT","bugs":{"url":"https://github.com/tencentyun/vod-js-sdk-v6/issues"},"homepage":"https://github.com/tencentyun/vod-js-sdk-v6#readme","dependencies":{"axios":"^0.18.0","cos-js-sdk-v5":"0.5.27","js-sha1":"^0.6.0","uuid":"^3.3.2"},"devDependencies":{"@types/mocha":"^5.2.5","@types/semver":"^6.0.0","@types/sha1":"^1.1.1","@types/uuid":"^3.4.4","@typescript-eslint/eslint-plugin":"^1.9.0","@typescript-eslint/parser":"^1.9.0","cross-env":"^6.0.3","eslint":"^5.16.0","eslint-config-prettier":"^4.3.0","eslint-plugin-prettier":"^3.1.0","espower-typescript":"^9.0.1","jsdom":"^13.1.0","jsdom-global":"^3.0.2","mm":"^2.4.1","mocha":"^5.2.0","nyc":"^13.1.0","power-assert":"^1.6.1","prettier":"^1.17.1","semver":"^6.1.1","ts-loader":"^5.3.3","typescript":"^3.5.3","webpack":"^4.28.1","webpack-cli":"^3.2.1"},"nyc":{"extension":[".ts",".tsx"],"include":["src"],"reporter":["html"],"all":true}}')},I2ZF:function(e,t){for(var r=[],o=0;o<256;++o)r[o]=(o+256).toString(16).substr(1);e.exports=function(e,t){var o=t||0,n=r;return[n[e[o++]],n[e[o++]],n[e[o++]],n[e[o++]],"-",n[e[o++]],n[e[o++]],"-",n[e[o++]],n[e[o++]],"-",n[e[o++]],n[e[o++]],"-",n[e[o++]],n[e[o++]],n[e[o++]],n[e[o++]],n[e[o++]],n[e[o++]]].join("")}},YZgg:function(e,t,r){"use strict";var o=this&&this.__assign||function(){return(o=Object.assign||function(e){for(var t,r=1,o=arguments.length;r<o;r++)for(var n in t=arguments[r])Object.prototype.hasOwnProperty.call(t,n)&&(e[n]=t[n]);return e}).apply(this,arguments)};Object.defineProperty(t,"__esModule",{value:!0}),t.VodReporter=t.VodReportEvent=void 0;var n,i,s=r("s3hC"),a=r("FRsH"),u=r("yPq/");!function(e){e.report_apply="report_apply",e.report_cos_upload="report_cos_upload",e.report_commit="report_commit",e.report_done="report_done"}(n=t.VodReportEvent||(t.VodReportEvent={})),function(e){e[e.apply=10001]="apply",e[e.cos_upload=20001]="cos_upload",e[e.commit=10002]="commit",e[e.done=40001]="done"}(i||(i={}));var p=function(){function e(e,t){this.baseReportData={version:a.version,platform:3e3,device:navigator.userAgent},this.reportUrl="https://vodreport.qcloud.com/ugcupload_new",this.uploader=e,this.options=t,this.init()}return e.prototype.init=function(){this.uploader.on(n.report_apply,this.onApply.bind(this)),this.uploader.on(n.report_cos_upload,this.onCosUpload.bind(this)),this.uploader.on(n.report_commit,this.onCommit.bind(this)),this.uploader.on(n.report_done,this.onDone.bind(this))},e.prototype.onApply=function(e){try{var t=this.uploader;if(!t.videoFile)return;Object.assign(this.baseReportData,{appId:t.appId,fileSize:t.videoFile.size,fileName:t.videoFile.name,fileType:t.videoFile.type,vodSessionKey:t.vodSessionKey,reqKey:t.reqKey,reportId:t.reportId});var r={reqType:i.apply,errCode:0,vodErrCode:0,errMsg:"",reqTimeCost:Number(new Date)-Number(e.requestStartTime),reqTime:Number(e.requestStartTime)};e.err&&(r.errCode=1,r.vodErrCode=e.err.code,r.errMsg=e.err.message),e.data&&(this.baseReportData.cosRegion=e.data.storageRegionV5),this.report(r)}catch(o){if(console.error("onApply",o),u.default.isTest)throw o}},e.prototype.onCosUpload=function(e){try{var t={reqType:i.cos_upload,errCode:0,cosErrCode:"",errMsg:"",reqTimeCost:Number(new Date)-Number(e.requestStartTime),reqTime:Number(e.requestStartTime)};e.err&&(t.errCode=1,t.cosErrCode=e.err.error?e.err.error.Code:e.err,e.err&&"error"===e.err.error&&(t.cosErrCode="cors error"),t.errMsg=JSON.stringify(e.err)),this.report(t)}catch(r){if(console.error("onCosUpload",r),u.default.isTest)throw r}},e.prototype.onCommit=function(e){try{var t={reqType:i.commit,errCode:0,vodErrCode:0,errMsg:"",reqTimeCost:Number(new Date)-Number(e.requestStartTime),reqTime:Number(e.requestStartTime)};e.err&&(t.errCode=1,t.vodErrCode=e.err.code,t.errMsg=e.err.message),e.data&&(this.baseReportData.fileId=e.data.fileId),this.report(t)}catch(r){if(console.error("onCommit",r),u.default.isTest)throw r}},e.prototype.onDone=function(e){try{var t={reqType:i.done,errCode:e.err&&e.err.code,reqTimeCost:Number(new Date)-Number(e.requestStartTime),reqTime:Number(e.requestStartTime)};this.report(t)}catch(r){if(console.error("onDone",r),u.default.isTest)throw r}},e.prototype.report=function(e){e=o(o({},this.baseReportData),e),this.send(e)},e.prototype.send=function(e){u.default.isDev||u.default.isTest?console.log("send reportData",e):s.vodAxios.post(this.reportUrl,e)},e}();t.VodReporter=p},s3hC:function(e,t,r){"use strict";var o=this&&this.__extends||function(){var e=function(t,r){return(e=Object.setPrototypeOf||{__proto__:[]}instanceof Array&&function(e,t){e.__proto__=t}||function(e,t){for(var r in t)t.hasOwnProperty(r)&&(e[r]=t[r])})(t,r)};return function(t,r){function o(){this.constructor=t}e(t,r),t.prototype=null===r?Object.create(r):(o.prototype=r.prototype,new o)}}(),n=this&&this.__assign||function(){return(n=Object.assign||function(e){for(var t,r=1,o=arguments.length;r<o;r++)for(var n in t=arguments[r])Object.prototype.hasOwnProperty.call(t,n)&&(e[n]=t[n]);return e}).apply(this,arguments)},i=this&&this.__awaiter||function(e,t,r,o){return new(r||(r=Promise))((function(n,i){function s(e){try{u(o.next(e))}catch(t){i(t)}}function a(e){try{u(o.throw(e))}catch(t){i(t)}}function u(e){var t;e.done?n(e.value):(t=e.value,t instanceof r?t:new r((function(e){e(t)}))).then(s,a)}u((o=o.apply(e,t||[])).next())}))},s=this&&this.__generator||function(e,t){var r,o,n,i,s={label:0,sent:function(){if(1&n[0])throw n[1];return n[1]},trys:[],ops:[]};return i={next:a(0),throw:a(1),return:a(2)},"function"===typeof Symbol&&(i[Symbol.iterator]=function(){return this}),i;function a(i){return function(a){return function(i){if(r)throw new TypeError("Generator is already executing.");for(;s;)try{if(r=1,o&&(n=2&i[0]?o.return:i[0]?o.throw||((n=o.return)&&n.call(o),0):o.next)&&!(n=n.call(o,i[1])).done)return n;switch(o=0,n&&(i=[2&i[0],n.value]),i[0]){case 0:case 1:n=i;break;case 4:return s.label++,{value:i[1],done:!1};case 5:s.label++,o=i[1],i=[0];continue;case 7:i=s.ops.pop(),s.trys.pop();continue;default:if(!(n=(n=s.trys).length>0&&n[n.length-1])&&(6===i[0]||2===i[0])){s=0;continue}if(3===i[0]&&(!n||i[1]>n[0]&&i[1]<n[3])){s.label=i[1];break}if(6===i[0]&&s.label<n[1]){s.label=n[1],n=i;break}if(n&&s.label<n[2]){s.label=n[2],s.ops.push(i);break}n[2]&&s.ops.pop(),s.trys.pop();continue}i=t.call(e,s)}catch(a){i=[6,a],o=0}finally{r=n=0}if(5&i[0])throw i[1];return{value:i[0]?i[1]:void 0,done:!0}}([i,a])}}};Object.defineProperty(t,"__esModule",{value:!0}),t.UploaderEvent=t.vodAxios=void 0;var a,u=r("YZm+"),p=r("NDg5"),c=r("+qE3"),l=r("vDqi"),d=r("yPq/"),f=r("YZgg"),h=r("xk4V");t.vodAxios=l.default.create(),t.vodAxios.interceptors.response.use((function(e){return e}),(function(e){return isNaN(e.code)&&(e.code=500),Promise.reject(e)})),function(e){e.video_progress="video_progress",e.media_progress="media_progress",e.video_upload="video_upload",e.media_upload="media_upload",e.cover_progress="cover_progress",e.cover_upload="cover_upload"}(a=t.UploaderEvent||(t.UploaderEvent={}));var v=function(e){function r(t){var r=e.call(this)||this;return r.sessionName="",r.vodSessionKey="",r.appId=0,r.reqKey=h(),r.reportId="",r.enableResume=!0,r.applyRequestTimeout=5e3,r.applyRequestRetryCount=3,r.commitRequestTimeout=5e3,r.commitRequestRetryCount=3,r.retryDelay=1e3,r.validateInitParams(t),r.videoFile=t.mediaFile||t.videoFile,r.getSignature=t.getSignature,r.enableResume=t.enableResume,r.videoName=t.mediaName||t.videoName,r.coverFile=t.coverFile,r.fileId=t.fileId,r.applyRequestTimeout=t.applyRequestTimeout||r.applyRequestTimeout,r.commitRequestTimeout=t.commitRequestTimeout||r.commitRequestTimeout,r.retryDelay=t.retryDelay||r.retryDelay,r.appId=t.appId||r.appId,r.reportId=t.reportId||r.reportId,r.cosAuthTime=0,r.genFileInfo(),r}return o(r,e),r.prototype.setStorage=function(e,t){if(e){var r="webugc_"+u(e);try{localStorage.setItem(r,t)}catch(o){}}},r.prototype.getStorage=function(e){if(e){var t="webugc_"+u(e),r=null;try{r=localStorage.getItem(t)}catch(o){}return r}},r.prototype.delStorage=function(e){if(e){var t="webugc_"+u(e);try{localStorage.removeItem(t)}catch(r){}}},r.prototype.validateInitParams=function(e){if(!d.default.isFunction(e.getSignature))throw new Error("getSignature must be a function");if(e.videoFile&&!d.default.isFile(e.videoFile))throw new Error("videoFile must be a File")},r.prototype.genFileInfo=function(){var e=this.videoFile;if(e){var t=e.name.lastIndexOf("."),r="";if(this.videoName){if(!d.default.isString(this.videoName))throw new Error("mediaName must be a string");if(/[:*?<>\"\\/|]/g.test(this.videoName))throw new Error('Cant use these chars in filename: \\ / : * ? " < > |');r=this.videoName}else r=e.name.substring(0,t);this.videoInfo={name:r,type:e.name.substring(t+1).toLowerCase(),size:e.size},this.sessionName+=e.name+"_"+e.size+";"}var o=this.coverFile;if(o){var n=o.name,i=n.lastIndexOf(".");this.coverInfo={name:n.substring(0,i),type:n.substring(i+1).toLowerCase(),size:o.size},this.sessionName+=o.name+"_"+o.size+";"}},r.prototype.applyUploadUGC=function(e){return void 0===e&&(e=0),i(this,void 0,void 0,(function(){function o(t){return i(this,void 0,void 0,(function(){return s(this,(function(o){switch(o.label){case 0:if(500===t.code&&(r.host=r.host===d.HOST.MAIN?d.HOST.BACKUP:d.HOST.MAIN),n.emit(f.VodReportEvent.report_apply,{err:t,requestStartTime:h}),n.delStorage(n.sessionName),n.applyRequestRetryCount==e){if(t)throw t;throw new Error("apply upload failed")}return[4,d.default.delay(n.retryDelay)];case 1:return o.sent(),[2,n.applyUploadUGC(e+1)]}}))}))}var n,a,u,p,c,l,h,v,m,y,g,b;return s(this,(function(e){switch(e.label){case 0:return n=this,[4,this.getSignature()];case 1:if(a=e.sent(),p=this.videoInfo,c=this.coverInfo,l=this.vodSessionKey||this.enableResume&&this.getStorage(this.sessionName))u={signature:a,vodSessionKey:l};else if(p)u={signature:a,videoName:p.name,videoType:p.type,videoSize:p.size},c&&(u.coverName=c.name,u.coverType=c.type,u.coverSize=c.size);else{if(!this.fileId||!c)throw"Wrong params, please check and try again";u={signature:a,fileId:this.fileId,coverName:c.name,coverType:c.type,coverSize:c.size}}h=new Date,e.label=2;case 2:return e.trys.push([2,4,,5]),[4,t.vodAxios.post("https://"+r.host+"/v3/index.php?Action=ApplyUploadUGC",u,{timeout:this.applyRequestTimeout,withCredentials:!1})];case 3:return v=e.sent(),[3,5];case 4:return[2,o(e.sent())];case 5:return 0==(m=v.data).code?(y=m.data,g=y.vodSessionKey,this.setStorage(this.sessionName,g),this.vodSessionKey=g,this.appId=y.appId,this.emit(f.VodReportEvent.report_apply,{data:y,requestStartTime:h}),[2,y]):((b=new Error(m.message)).code=m.code,[2,o(b)])}}))}))},r.prototype.uploadToCos=function(e){return i(this,void 0,void 0,(function(){var t,r,o,u,c,l,h,v;return s(this,(function(m){switch(m.label){case 0:return t=this,r={bucket:e.storageBucket+"-"+e.storageAppId,region:e.storageRegionV5},o=new p({getAuthorization:function(r,o){return i(this,void 0,void 0,(function(){var r,n;return s(this,(function(i){switch(i.label){case 0:return r=d.default.getUnix(),n=.9*(e.tempCertificate.expiredTime-e.timestamp),0!==t.cosAuthTime?[3,1]:(t.cosAuthTime=r,[3,3]);case 1:return t.cosAuthTime&&r-t.cosAuthTime>=n?[4,t.applyUploadUGC()]:[3,3];case 2:e=i.sent(),t.cosAuthTime=d.default.getUnix(),i.label=3;case 3:return o({TmpSecretId:e.tempCertificate.secretId,TmpSecretKey:e.tempCertificate.secretKey,XCosSecurityToken:e.tempCertificate.token,StartTime:e.timestamp,ExpiredTime:e.tempCertificate.expiredTime}),[2]}}))}))}}),this.cos=o,u=[],this.videoFile&&(c=n(n({},r),{file:this.videoFile,key:e.video.storagePath,onProgress:function(e){t.emit(a.video_progress,e),t.emit(a.media_progress,e)},onUpload:function(e){t.emit(a.video_upload,e),t.emit(a.media_upload,e)},onTaskReady:function(e){t.taskId=e}}),u.push(c)),this.coverFile&&(l=n(n({},r),{file:this.coverFile,key:e.cover.storagePath,onProgress:function(e){t.emit(a.cover_progress,e)},onUpload:function(e){t.emit(a.cover_upload,e)},onTaskReady:d.default.noop}),u.push(l)),h=new Date,v=u.map((function(e){return new Promise((function(r,n){o.sliceUploadFile({Bucket:e.bucket,Region:e.region,Key:e.key,Body:e.file,onTaskReady:e.onTaskReady,onProgress:e.onProgress},(function(o,i){return e.file===t.videoFile&&t.emit(f.VodReportEvent.report_cos_upload,{err:o,requestStartTime:h}),o?(t.delStorage(t.sessionName),'{"error":"error","headers":{}}'===JSON.stringify(o)?n(new Error("cors error")):void n(o)):(e.onUpload(i),r())}))}))})),[4,Promise.all(v)];case 1:return[2,m.sent()]}}))}))},r.prototype.commitUploadUGC=function(e){return void 0===e&&(e=0),i(this,void 0,void 0,(function(){function o(t){return i(this,void 0,void 0,(function(){return s(this,(function(o){switch(o.label){case 0:if(500===t.code&&(r.host=r.host===d.HOST.MAIN?d.HOST.BACKUP:d.HOST.MAIN),n.emit(f.VodReportEvent.report_commit,{err:t,requestStartTime:p}),n.commitRequestRetryCount==e){if(t)throw t;throw new Error("commit upload failed")}return[4,d.default.delay(n.retryDelay)];case 1:return o.sent(),[2,n.commitUploadUGC(e+1)]}}))}))}var n,a,u,p,c,l,h;return s(this,(function(e){switch(e.label){case 0:return n=this,[4,this.getSignature()];case 1:a=e.sent(),this.delStorage(this.sessionName),u=this.vodSessionKey,p=new Date,e.label=2;case 2:return e.trys.push([2,4,,5]),[4,t.vodAxios.post("https://"+r.host+"/v3/index.php?Action=CommitUploadUGC",{signature:a,vodSessionKey:u},{timeout:this.commitRequestTimeout,withCredentials:!1})];case 3:return c=e.sent(),[3,5];case 4:return[2,o(e.sent())];case 5:return 0==(l=c.data).code?(this.emit(f.VodReportEvent.report_commit,{data:l.data,requestStartTime:p}),[2,l.data]):((h=new Error(l.message)).code=l.code,[2,o(h)])}}))}))},r.prototype.start=function(){var e=this,t=new Date;this.donePromise=this._start().then((function(r){return e.emit(f.VodReportEvent.report_done,{err:{code:0},requestStartTime:t}),r})).catch((function(r){throw e.emit(f.VodReportEvent.report_done,{err:{code:r&&r.code||d.default.CLIENT_ERROR_CODE.UPLOAD_FAIL},requestStartTime:t}),r}))},r.prototype._start=function(){return i(this,void 0,void 0,(function(){var e;return s(this,(function(t){switch(t.label){case 0:return[4,this.applyUploadUGC()];case 1:return e=t.sent(),[4,this.uploadToCos(e)];case 2:return t.sent(),[4,this.commitUploadUGC()];case 3:return[2,t.sent()]}}))}))},r.prototype.done=function(){return this.donePromise},r.prototype.cancel=function(){this.cos.cancelTask(this.taskId)},r.host=d.HOST.MAIN,r}(c.EventEmitter);t.default=v},xk4V:function(e,t,r){var o=r("4fRq"),n=r("I2ZF");e.exports=function(e,t,r){var i=t&&r||0;"string"==typeof e&&(t="binary"===e?new Array(16):null,e=null);var s=(e=e||{}).random||(e.rng||o)();if(s[6]=15&s[6]|64,s[8]=63&s[8]|128,t)for(var a=0;a<16;++a)t[i+a]=s[a];return t||n(s)}},"yPq/":function(e,t,r){"use strict";var o;Object.defineProperty(t,"__esModule",{value:!0}),t.HOST=void 0,function(e){e[e.UPLOAD_FAIL=1]="UPLOAD_FAIL"}(o||(o={})),function(e){e.MAIN="vod2.qcloud.com",e.BACKUP="vod2.dnsv1.com"}(t.HOST||(t.HOST={})),t.default={isFile:function(e){return"[object File]"==Object.prototype.toString.call(e)},isFunction:function(e){return"function"===typeof e},isString:function(e){return"string"===typeof e},noop:function(){},delay:function(e){return new Promise((function(t){setTimeout((function(){t()}),e)}))},getUnix:function(){return Math.floor(Date.now()/1e3)},isTest:!1,isDev:!1,CLIENT_ERROR_CODE:o}}}]);