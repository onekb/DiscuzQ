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
        noticeList: [],       //通知方式
        wxDes: '',            //微信描述
        systemDes:'',         //系统通知描述
        systemList: '',       //系统通知数据
        wxList: '',           //微信通知数据
        appletsList: [],      //keyword数组
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
      tableContAdd() {
        this.appletsList.push('');
      },
      // 点击删除图标
      delectClick(index) {
        this.appletsList.splice(index, 1);
      },
      // 通知方式切换
      noticeListChange(data) {
        if (data.indexOf('0') === -1) {
          this.showSystem = false;
        } else {
          this.showSystem = true;
        }
        if (data.indexOf('1') === -1) {
          this.showWx = false;
        } else {
          this.showWx = true;
        }
      },
      // 初始化配置列表信息
      noticeConfigure() {
        this.appFetch({
          url: 'noticeDetail',
          method: 'get',
          splice: `?type_name=${this.typeName}`,
          data: {}
        }).then(res => {
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
              this.noticeList.push("0");
              this.showSystem = true
            } else {
              this.showSystem = false
            }
          }
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
          }

          if (this.wxList.status) {
            this.noticeList.push("1");
            this.showWx = true;
          } else {
            this.showWx = false;
          }
        })
      },
      // 提交按钮
      Submission() {
        let data = [];
        if (this.showSystem === true){
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
