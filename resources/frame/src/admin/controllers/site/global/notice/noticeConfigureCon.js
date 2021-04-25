/*
* 通知设置配置控制器
* */

import Card from '../../../../view/site/common/card/card';
import CardRow from '../../../../view/site/common/card/cardRow';
import TableContAdd from '../../../../view/site/common/table/tableContAdd';

export default {
    data: function () {
      return {
        query: '',            //获取当前用户的ID
        typeName: '',         //获取当前typename
        showSystem: false,    //系统显示
        showWx: false,        //微信显示
        showMini: false,      //小程序显示
        showSms: false,       //短信显示
        noticeList: [],       //通知方式
        wxDes: '',            //微信描述
        systemDes:'',         //系统通知描述
        smsDes: '',           //短信描述
        miniDes: '',          //小程序描述
        systemList: '',       //系统通知数据
        wxList: '',           //微信通知数据
        miniProgramList: '',  //小程序通知数据
        smsList: '',          //短信通知数据
        appletsList: [],      //keyword数组
        smsKeyWord: [],       //短信keyword数组
        miniKeyWord: [],      //小程序keyword数组
        showClick: true,      //微信通知keyword超过五个不显示增加
        keyList: [],
        miniTips:''
      }
    },
    components: {
      Card,
      CardRow,
      TableContAdd
    },
    created() {
      this.query = this.$route.query;
      this.typeName = this.$route.query.typeName;
      this.noticeConfigure();
    },
    methods: {
      // 点击添加关键字
      tableContAdd(type, index) {
        if (type === 'appletsList' && this.appletsList.length <= 4) {
          this.appletsList.push('');
        } else if (type === 'appletsList' && this.appletsList.length > 4) {
          this.showClick = false;
        } else if (type === 'smsKeyWord') {
          this.smsKeyWord.push('');
        } else if (type === 'miniKeyWord') {
          this.miniKeyWord.push('')
        }
      },
      // 点击删除图标
      delectClick(index, type) {
        if (type === 'appletsList') {
          this.showClick = true;
          this.appletsList.splice(index, 1);
        } else if (type === 'smsKeyWord') {
          this.smsKeyWord.splice(index, 1);
        } else if (type === 'miniKeyWord') {
          this.miniKeyWord.splice(index, 1);
        }
      },
      // 通知方式切换
      noticeListChange(data) {
        this.showSystem = data.includes("0");
        this.showWx = data.includes("1");
        this.showMini = data.includes("4");
        this.showSms = data.includes("2");
      },
      // 初始化配置列表信息
      noticeConfigure() {
        this.appFetch({
          url: 'noticeDetail',
          method: 'get',
          splice: `?type_name=${this.typeName}`,
          data: {}
        }).then(res => {
          // 系统通知数据
          if (res.readdata[0]) {
            this.systemList = res.readdata[0]._data;
            let vars = this.systemList.template_variables;
            if (vars) {
              this.systemDes = '请输入模板消息详细内容对应的变量。关键字个数需与已添加的模板一致。\n\n可以使用如下变量：\n';
              for (let key in vars) {
                this.systemDes += `${key} ${vars[key]}\n`;
              }
            }
            if (this.systemList.status) {
              !this.noticeList.includes("0") && this.noticeList.push("0")
              this.showSystem = true
            } else {
              this.showSystem = false
            }
          }
          // 微信模板通知
          if (res.readdata[1]) {
            this.wxList = res.readdata[1]._data;
            let vars = this.wxList.template_variables;
            if (vars) {
              this.wxDes = '请输入模板消息详细内容对应的变量。关键字个数需与已添加的模板一致。\n\n可以使用如下变量：\n';
              for (let key in vars) {
                this.wxDes += `${key} ${vars[key]}\n`;
              }
            }
            this.appletsList = this.wxList.keywords_data.length > 0
              ? this.wxList.keywords_data
              : ['', ''];
              if (this.wxList.status) {
                !this.noticeList.includes("1") && this.noticeList.push("1")
                this.showWx = true;
              } else {
                this.showWx = false;
              }
          }

          // 短信通知
          if (res.readdata[2]) {
            this.smsList = res.readdata[2]._data;
            this.smsKeyWord = this.smsList.keywords_data.length > 0
              ? this.smsList.keywords_data
              : [''];
              let vars = this.smsList.template_variables;
              if (vars) {
                this.smsDes = '请输入模板消息详细内容对应的变量。关键字个数需与已添加的模板一致。\n\n可以使用如下变量：\n';
                for (let key in vars) {
                  this.smsDes += `${key} ${vars[key]}\n`;
                }
              }
              if (this.smsList.status) {
                !this.noticeList.includes("2") && this.noticeList.push("2")
                this.showSms = true;
              }else {
                this.showSms = false;
              }
          }

          // 小程序通知
          if (res.readdata[3]) {
            this.miniProgramList = res.readdata[3]._data;
            this.keyList = this.miniProgramList.keys;
            this.miniKeyWord = this.miniProgramList.keywords_data.length > 0
              ? this.miniProgramList.keywords_data
              : ['', ''];
              let vars = this.miniProgramList.template_variables;
              this.miniTips = '\n<a href="https://developers.weixin.qq.com/miniprogram/dev/api-backend/open-api/subscribe-message/subscribeMessage.send.html" target="_blank">订阅消息参数值内容限制说明</a>填写错误将导致用户无法接收到消息通知'
              if (vars) {
                this.miniDes = '请输入模板消息详细内容对应的变量。关键字个数需与已添加的模板一致。\n\n可以使用如下变量：\n';
                for (let key in vars) {
                  this.miniDes += `${key} ${vars[key]}\n`;
                }
              }
              if (this.miniProgramList.status) {
                !this.noticeList.includes("4") && this.noticeList.push("4")
                this.showMini = true;
              }else {
                this.showMini = false
              }
          }
        })
      },
      // 提交按钮
      Submission() {
        let data = [];
        // 系统通知提交数据
        if (this.showSystem === true){
          if (this.systemList.content === '') {
            this.$message.error('请填写通知内容');
            return;
          }
          data.push({
            'attributes':{
              "id": this.systemList.tpl_id,
              "status": 1,
              "template_id": this.systemList.template_id,
              "title": this.systemList.title,
              "content": this.systemList.content
            }
          });
        } else {
          data.push({
            'attributes':{
              "id": this.systemList.tpl_id,
              "status": 0,
            }
          });
        }
        // 微信通知提交数据
        if (this.showWx === true){
          if (this.wxList.first_data === '') {
            this.$message.error('请填写first');
            return;
          }
          for (let key in this.appletsList) {
            if (key >= 2) {
              break;
            }
            if (!this.appletsList[key]) {
            this.$message.error('请填写keywords');
            return;
            }
          }
          if (this.wxList.remark_data === '') {
            this.$message.error('请填写remark');
            return;
          }
          data.push({
            'attributes':{
              "id": this.wxList.tpl_id,
              "status": 1,
              "template_id": this.wxList.template_id,
              "first_data": this.wxList.first_data,
              "keywords_data": this.appletsList,
              "remark_data": this.wxList.remark_data,
              "redirect_type": this.wxList.redirect_type,
              "redirect_url": this.wxList.redirect_url,
              "page_path":this.wxList.page_path,
            }
          });
        } else {
          data.push({
            'attributes':{
              "id": this.wxList.tpl_id,
              "status": 0,
            }
          });
        }

        // 短信通知提交数据
        if (this.showSms === true) {
          if (this.smsList.template_id === '') {
            this.$message.error('请填写短信模版ID');
            return;
          }
          for (let key in this.smsKeyWord) {
            if (key >= 2) {
              break;
            }
            if (!this.smsKeyWord[key]) {
            this.$message.error('请填写keywords');
            return;
            }
          }
          data.push({
            'attributes':{
              "id": this.smsList.tpl_id,
              "status": 1,
              "title": this.smsList.title,
              "template_id": this.smsList.template_id,
              "keywords_data": this.smsKeyWord,
            }
          });
        } else {
          data.push({
            'attributes':{
              "id": this.smsList.tpl_id,
              "status": 0,
            }
          });
        }

      // 小程序订阅提交数据
      if (this.showMini === true) {
        if (this.miniProgramList.template_id === '') {
          this.$message.error('请填写小程序模版ID');
          return;
        }
        if (this.keyList.length > 0) {
          for (let i = 0, len = this.miniKeyWord.length; i < len; i++) {
            if (this.miniKeyWord[i] === "") {
              this.$message.error("请填写keywords");
              return;
            }
          }
        }
         if (this.miniProgramList.page_path === '') {
          this.$message.error('请填写小程序路径');
          return;
        }
        data.push({
          'attributes':{
            "id": this.miniProgramList.tpl_id,
            "status": 1,
            "template_id": this.miniProgramList.template_id,
            "title": this.miniProgramList.title,
            "keywords_data": this.miniKeyWord,
            "page_path": this.miniProgramList.page_path
          }
        });
      } else {
        data.push({
          'attributes':{
            "id": this.miniProgramList.tpl_id,
            "status": 0,
          }
        });
      }

        this.appFetch({
          url: 'noticeList',
          method: 'patch',
          data: {
            "data": data,
          }
      }).then(res=>{
        if (res.errors) {
          if (res.errors[0].detail) {
            this.$message.error(
              res.errors[0].code + "\n" + res.errors[0].detail[0]
            );
          } else {
            this.$message.error(res.errors[0].code);
          }
          }else {
            this.$message({
              message: '提交成功',
              type: 'success'
          });
          this.noticeConfigure();
        }
      })
      }
    }
}
