(window.webpackJsonp=window.webpackJsonp||[]).push([[26],{"9Qtb":function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s,i=a("VVfg"),n=(s=i)&&s.__esModule?s:{default:s};e.default={data:function(){return{userName:"",currentTime:"",week:["星期天","星期一","星期二","星期三","星期四","星期五","星期六"],dataList:[{title:"今日活跃用户数",num:"0",key:"activeUserNumToday"},{title:"今日新增用户数",num:"0",key:"addUserNumToday"},{title:"今日发帖数",num:"0",key:"addThreadNumToday"},{title:"今日回帖数",num:"0",key:"addPostNumToday"},{title:"用户总数量",num:"0",key:"totalUserNum"},{title:"发帖总数量",num:"0",key:"totalThreadNum"},{title:"回帖总数量",num:"0",key:"totalPostNum"},{title:"精华内容数",num:"0",key:"essenceThreadNum"}],dataPanelEcharts:null,selectTime:["",""],valueMouth:["",""],noData:!1,selectedMode:0,dayTab:!0,mouthTab:!1,indexStatistics:!0,items:[{name:"按日统计",sort:1},{name:"按周统计",sort:2},{name:"按月统计",sort:3}],chartItems:[["每日发帖","每日回帖","日活用户","日注册数"],["每周发帖","每周回帖","周活用户","周注册数"],["每月发帖","每月回帖","月活用户","月注册数"]]}},created:function(){var t=this.$dayjs().format("YYYY-MM-DD").split("-");this.currentTime=t[0]+"年"+t[1]+"月"+t[2]+"日",this.userName=n.default.getLItem("username"),this.statistic()},mounted:function(){this.siteDataStatistics()},methods:{tab:function(t){this.selectedMode=t,0!=t&&1!=t||(this.dayTab=!0,this.mouthTab=!1,this.indexStatistics=!0),2==t&&(this.mouthTab=!0,this.dayTab=!1,this.indexStatistics=!1),this.siteDataStatistics()},statistic:function(){var t=this;this.appFetch({url:"statisticPanel",method:"get"}).then((function(e){if(e&&e.errors)return t.$message.error(e.errors[0].code);if(e&&e.readdata){var a=e.readdata._data.overData.over||{};t.dataList.forEach((function(t){t.num=a[t.key]}))}})).catch((function(t){}))},changeDayOrWeek:function(){this.siteDataStatistics()},changeMouth:function(){null==this.valueMouth?this.valueMouth=["",""]:""!==this.valueMouth[0]&&""!==this.valueMouth[1]&&(this.valueMouth[0]=this.valueMouth[0],this.valueMouth[1]=this.valueMouth[1]),this.siteDataStatistics()},siteDataStatistics:function(){var t=this,e={};if(0===this.selectedMode){var a=this.$dayjs().subtract(6,"day").format("YYYY-MM-DD"),s=this.$dayjs().format("YYYY-MM-DD");e={"filter[type]":this.selectedMode+1,"filter[createdAtBegin]":this.selectTime[0]||a,"filter[createdAtEnd]":this.selectTime[1]||s}}if(1===this.selectedMode){var i=this.$dayjs().startOf("week").format("YYYY-MM-DD"),n=this.$dayjs().endOf("week").format("YYYY-MM-DD");e={"filter[type]":this.selectedMode+1,"filter[createdAtBegin]":this.selectTime[0]||i,"filter[createdAtEnd]":this.selectTime[1]||n}}if(2===this.selectedMode){var r=this.valueMouth[0],o=this.$dayjs(this.valueMouth[1]).endOf("month").format("YYYY-MM-DD");""!==this.valueMouth[0]&&""!==this.valueMouth[1]||(r=this.$dayjs().startOf("month").format("YYYY-MM-DD"),o=this.$dayjs().endOf("month").format("YYYY-MM-DD")),e={"filter[type]":this.selectedMode+1,"filter[createdAtBegin]":r,"filter[createdAtEnd]":o}}this.appFetch({url:"statisticPanel",method:"get",data:e}).then((function(e){if(e&&e.errors)return t.$message.error(e.errors[0].code);if(t.noData=!(!e||""!=e.readdata),e&&e.readdata){var a,s,i,n,r,o=e.readdata._data;a=o.threadData.map((function(e){return 0===t.selectedMode?e.date:1===t.selectedMode?e.week:2===t.selectedMode?e.month:[]})),s=o.threadData.map((function(t){return t.count})),i=o.postData.map((function(t){return t.count})),n=o.activeUserData.map((function(t){return t.count})),r=o.joinUserData.map((function(t){return t.count})),t.siteDataEcharts(a,s,i,n,r)}})).catch((function(t){}))},siteDataEcharts:function(t,e,a,s,i){this.dataPanelEcharts||(this.dataPanelEcharts=this.$echarts.init(this.$refs.financialProfitEcharts));var n=this.chartItems[this.selectedMode],r={tooltip:{trigger:"axis",axisPointer:{type:"cross",label:{backgroundColor:"#6a7985"}}},color:["#ee6666","#5470c6","#91cc75","#fac858"],legend:{data:n},grid:{left:"1%",right:"8%",bottom:"2%",containLabel:!0},xAxis:[{type:"category",boundaryGap:!1,data:t,axisLabel:{interval:0,rotate:-40}}],yAxis:[{type:"value"}],series:[{name:n[0],type:"line",data:e},{name:n[1],type:"line",data:a},{name:n[2],type:"line",data:s},{name:n[3],type:"line",data:i}]};this.dataPanelEcharts.setOption(r)}}}},Jr5O:function(t,e,a){"use strict";a.r(e);var s=a("a+Tf"),i=a.n(s);for(var n in s)["default"].indexOf(n)<0&&function(t){a.d(e,t,(function(){return s[t]}))}(n);e.default=i.a},"KHd+":function(t,e,a){"use strict";function s(t,e,a,s,i,n,r,o){var c,d="function"==typeof t?t.options:t;if(e&&(d.render=e,d.staticRenderFns=a,d._compiled=!0),s&&(d.functional=!0),n&&(d._scopeId="data-v-"+n),r?(c=function(t){(t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext)||"undefined"==typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),i&&i.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(r)},d._ssrRegister=c):i&&(c=o?function(){i.call(this,(d.functional?this.parent:this).$root.$options.shadowRoot)}:i),c)if(d.functional){d._injectStyles=c;var u=d.render;d.render=function(t,e){return c.call(e),u(t,e)}}else{var l=d.beforeCreate;d.beforeCreate=l?[].concat(l,c):[c]}return{exports:t,options:d}}a.d(e,"a",(function(){return s}))},ZV0N:function(t,e,a){},"a+Tf":function(t,e,a){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var s=n(a("QbLZ"));a("ZV0N");var i=n(a("9Qtb"));function n(t){return t&&t.__esModule?t:{default:t}}e.default=(0,s.default)({name:"data-panel-view"},i.default)},cHvI:function(t,e,a){"use strict";a.r(e);var s=a("zq7I"),i=a("Jr5O");for(var n in i)["default"].indexOf(n)<0&&function(t){a.d(e,t,(function(){return i[t]}))}(n);var r=a("KHd+"),o=Object(r.a)(i.default,s.a,s.b,!1,null,null,null);e.default=o.exports},zq7I:function(t,e,a){"use strict";a.d(e,"a",(function(){return s})),a.d(e,"b",(function(){return i}));var s=function(){var t=this,e=t.$createElement,a=t._self._c||e;return a("div",{staticClass:"home-data-box"},[a("p",{staticClass:"desc"},[a("span",{staticStyle:{fontWeight:"bold"}},[t._v(t._s(t.userName)+" 您好！")]),a("br"),t._v("\n    "+t._s(t.currentTime)+" "+t._s(t.week[this.$dayjs().day()])+"，欢迎您回到管理后台。\n  ")]),t._v(" "),a("div",{staticClass:"statistics-wrap"},t._l(t.dataList,(function(e,s){return a("div",{key:s,staticClass:"statistics-item"},[a("div",{staticClass:"statistics-item-title"},[t._v(t._s(e.title))]),t._v(" "),a("span",{staticClass:"statistics-item-num",attrs:{title:e.num}},[t._v(t._s(e.num))])])})),0),t._v(" "),a("div",{staticClass:"chart-wrap"},[a("div",{staticClass:"chart-head"},[t._m(0),t._v(" "),a("div",{staticClass:"chart-head-right"},[a("ul",t._l(t.items,(function(e,s){return a("li",{key:s,class:{active:t.selectedMode==s},on:{click:function(e){return t.tab(s)}}},[t._v(t._s(e.name))])})),0),t._v(" "),a("el-date-picker",{directives:[{name:"show",rawName:"v-show",value:t.dayTab,expression:"dayTab"}],staticClass:"input-class",attrs:{size:"small",clearable:"",type:"daterange","value-format":"yyyy-MM-dd","default-time":["00:00:00","23:59:59"],"range-separator":"至","start-placeholder":"开始日期","end-placeholder":"结束日期"},on:{change:t.changeDayOrWeek},model:{value:t.selectTime,callback:function(e){t.selectTime=e},expression:"selectTime"}}),t._v(" "),a("el-date-picker",{directives:[{name:"show",rawName:"v-show",value:t.mouthTab,expression:"mouthTab"}],staticClass:"input-class",attrs:{size:"small","value-format":"yyyy-MM-dd",type:"monthrange","range-separator":"至","start-placeholder":"开始月份","end-placeholder":"结束月份"},on:{change:t.changeMouth},model:{value:t.valueMouth,callback:function(e){t.valueMouth=e},expression:"valueMouth"}})],1)]),t._v(" "),a("div",{directives:[{name:"show",rawName:"v-show",value:t.noData,expression:"noData"}],staticClass:"noData"},[t._v("暂无数据")]),t._v(" "),a("div",{ref:"financialProfitEcharts",staticClass:"chart-content"})])])},i=[function(){var t=this.$createElement,e=this._self._c||t;return e("div",{staticClass:"chart-head-left"},[e("span",{staticClass:"iconfont iconcaiwutongji"}),this._v(" "),e("span",{staticClass:"chart-head-titles"},[this._v("站点每日数据")])])}]}}]);