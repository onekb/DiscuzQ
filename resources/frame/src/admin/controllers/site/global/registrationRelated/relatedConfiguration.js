import Card from "../../../../view/site/common/card/card";
import TableContAdd from "../../../../view/site/common/table/tableContAdd";

export default {
  data: function() {
    return {
      groupsList: [], 
      // 扩展列表
      options: [
        {
          value: 0,
          label: '单行文本框'
        },
        {
          value: 1,
          label: '多行文本框'
        },
        {
          value: 2,
          label: '单选'
        },
        {
          value: 3,
          label: '复选'
        },
        {
          value: 4,
          label: '图片上传'
        },
        {
          value: 5,
          label: '附件上传'
        }
      ],
      value: '',
      textarea: '',
      arr: [],
      stry: '',
      visible: false,
      dataList: [],
      arrsLiist: [],
    };
  },
  created() {
    this.extendedFields();
  },
  methods:{
    extendedFields() {
      this.appFetch({
        url: 'signInFields',
        method: 'get',
        data: {},
      }).then(res => {
        this.informationList(res.data);
      }) 
    },

    informationList(datalist) {
      this.groupsList = [];
      for (let i = 0; i < datalist.length; i++) {
        let data = {
          name: datalist[i].attributes.name,   // 字段名称
          id: datalist[i].attributes.id,           // 字段id
          content: '',
          description: datalist[i].attributes.type,  // 字段类型
          sort: datalist[i].attributes.sort,           // 字段排序
          introduce: datalist[i].attributes.fields_desc,  // 字段介绍
          enable: datalist[i].attributes.status === 1 ? true : false,      // 是否启用
          required: datalist[i].attributes.required === 1 ? true : false,   // 是否必填
        }
        let fieldsExt = ''
        if (datalist[i].attributes.fields_ext) {
          fieldsExt = JSON.parse(datalist[i].attributes.fields_ext);
        }
        if (fieldsExt.options) {
          const num = fieldsExt.options;
          for (let j = 0; j < num.length; j++) {
            data.content += num[j].value + "\n";
          }
          this.groupsList.push(data);
        } else {
          data.content = '';
          this.groupsList.push(data);
        }
      }
      this.orderList();
    },

    // 数据排序
    orderList() {
      this.groupsList.sort(this.soreoder('sort'));
    },

    // 数据排序
    soreoder(property) {
      return (a, b) => {
      var value1 = a[property];
      var value2 = b[property];
      return value1 - value2;
      }
    },
    changInput(e) {
      if (e !== '' && !this.isNumber(e)) {
        this.$message.error('请输入整数');
      }
    },
    isNumber(value) {
      const patrn = /^(-)?\d+(\.\d+)?$/;
      if (patrn.exec(value) == null) {
        return false
      } else {
        return true
      }
    },
    obtainValue(e) {
      this.value = e;
    },

    handleSelectionChange(list) {
      this.arrsLiist = [];
      list.forEach((item, index) => {
        let  data = {
          "attributes": {
            "status": 0
          },
        }
        if (item.newly) {
          let arrData = [];
          arrData.push(item);
        } else {
          data.attributes.id = item.id;
          this.arrsLiist.push(data);
        }
      })
    },
    
    deleteList() {
      this.appFetch({
        url: 'signInFields',
        method: 'post',
        data: {
          data: this.arrsLiist,
        },
      }).then(res => {
        if (res.errors){
          this.$message.error(res.errors[0].code);
        } else {
          this.$message({
            message: '删除成功',
            type: 'success'
          });
          this.extendedFields();
        }
      })
    },
    
    deleteField(single) {
      this.appFetch({
        url: 'signInFields',
        method: 'post',
        data: {
          "data": {
            "attributes": {
              "id": single.row.id,
              "status": 0
            }
          }
        }
      }).then(res => {
        if (res.errors){
          this.$message.error(res.errors[0].code);
        } else {
          this.$message({
            message: '删除成功',
            type: 'success'
          });
          this.extendedFields();
        }
      }) 
    },
    // 点击右侧删除事件
    operationDelete(index) {
      this.$confirm('删除后，则此字段及其历史用户信息，将从系统中彻底删除，且无法恢复，请谨慎操作，点击确认删除，则删除', {
        confirmButtonText: '确定',
        cancelButtonText: '取消',
        type: 'warning'
      }).then(() => {
        if (index.row.newly) {
          this.groupsList.splice(index.$index, 1);
        } else {
          this.groupsList.splice(index.$index, 1);
          this.deleteField(index);
        }
      }).catch(() => {
        this.$message({
          type: 'info',
          message: '已取消删除'
        });          
      });
    },
    
    // 增加注册字段事件
    increaseList() {
      this.groupsList.push({
        name: '',   // 字段名称
        id: '',           // 字段id
        content: '',
        description: '',  // 字段类型
        sort: '',           // 字段排序
        introduce: '',  // 字段介绍
        enable: false,      // 是否启用
        required: false,   // 是否必填
        newly: 1,  // 用作判断是否新增
      })
    },

    // 增加数据字段处理数据格式
    submitClick() {
      this.dataList = [];
      for (let i = 0; i < this.groupsList.length; i++) {
        let  data = {
          "type": 'admin_sign_in',
          "attributes": {
            "name": this.groupsList[i].name,
            "type": this.groupsList[i].description,
            "fields_desc": this.groupsList[i].introduce,
            "sort": this.groupsList[i].sort,
            "status": this.groupsList[i].enable ? 1 : -1,
            "required": this.groupsList[i].required ? 1 : 0,
          },
        }
        if (this.groupsList[i].id) {
          data.attributes.id = this.groupsList[i].id;
        }
        if (this.groupsList[i].content) {
          let lines = this.groupsList[i].content.split(/\n/);
          for (var j =0; j < lines.sort().length; j++) {
            if (lines[j].trim() !== '') {
              this.arr.push({value: lines[j].trim(), checked: false});
            }
          }
          let fieldsExtData = {"options": this.arr};
          data.attributes.fields_ext = JSON.stringify(fieldsExtData);
          this.dataList.push(data);
        } else {
          // let fieldsExtData = {"necessary": this.groupsList[i].required};
          data.attributes.fields_ext = '',
          this.dataList.push(data);
        }
        this.arr = [];
      }
      this.addRegistration(this.dataList);
    },
    testDataRun() {
      let num = true;
      this.groupsList.forEach(item => {
        if (item.name === '') {
          this.$message.error('字段名称未填写');
          num = false;
          return
        }
        if (item.description === '') {
          this.$message.error('字段类型未填写');
          num = false;
          return
        }
        if (item.description === 2 || item.description === 3) {
          if (item.content === '') {
            this.$message.error('字段选项未填写');
            num = false;
            return
          }
        }
        if (item.sort === '') {
          this.$message.error('字段排序未填写');
          num = false;
          return
        }
      })
      return num;
    },
    // 添加数据请求
    addRegistration(data) {
      if (!this.testDataRun()) {
        return;
      }
      this.appFetch({
        url: "signInFields",
        method: "post",
        data: {
          data,
        }
      }).then((res) => {
        if (res.errors){
          this.$message.error(res.errors[0].code);
        } else {
          this.$message({
            message: '操作成功',
            type: 'success'
          });
          this.extendedFields();
        }
      })
    },
    submitDetele() {
      if (this.arrsLiist.length > 0) {
        this.$confirm('删除后，则此字段及其历史用户信息，将从系统中彻底删除，且无法恢复，请谨慎操作，点击确认删除，则删除', {
          confirmButtonText: '确定',
          cancelButtonText: '取消',
          type: 'warning'
        }).then(() => {
          this.deleteList();
        }).catch(() => {
          this.$message({
            type: 'info',
            message: '已取消删除'
          });          
        });
      } else {
        this.$message.warning('请选择需要删除的字段');
      }
    },
    channelInputLimit (e) {
      let key = e.key;
      if (key === 'e' || key === '.') {
        e.returnValue = false
        return false
      }
      return true
    }
  },
  components: {
    Card,
    TableContAdd
  }
}