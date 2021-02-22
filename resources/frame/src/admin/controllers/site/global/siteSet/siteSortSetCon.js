import Card from "../../../../view/site/common/card/card";
import CardRow from "../../../../view/site/common/card/cardRow";

export default {
  components:{
    Card,
    CardRow
  },
  data(){
    return {
      categoriesList:[], // 分类
      groupsList:[], // 角色
      sortData:{
        usersList:[], // 用户添加
        topicsList:[], // 话题添加
        blockUsersList:[], // 用户排除
        blockTopicsList:[], // 话题排除
      },
      threads:"", // 主题添加
      blockThreads:"", // 主题排除
      isShow:true,
      type:'',
      searchText:'',
      timeout:null,
      state:'', // 搜索关键字
      selectedData:[], // 搜索界面展示数据
      recommendData:[], // 推荐数据
      siteOpenSort:false, // 智能排序开关
    }
  },
  created(){
    // 初始化状态
    this.loadSortStatus()
  },
  methods:{
    // 获取智能排序状态
    loadSortStatus() {
      this.getCategories()
      this.getGroups()
      this.getSortData()
      this.getSortBtnStatus()
    },
    // 1 获取分类设置状态
    getCategories(){
      this.appFetch({
        url:'categories',
        method:'get',
        data:{}
      }).then(res=>{
        if (res.errors){
          this.$message.error(res.errors[0].code);
        }else {
          res.data.forEach(item => {
            let checkedList=[];
            let allList=[];

            // 遍历子类，查看checked情况
            let childrens=[];
            item.attributes.children.forEach(child=>{
              allList.push(child.id)
              if(child.checked){
                checkedList.push(child.id)
              }
              childrens.push({
                id:child.id,
                name:child.name,
              })
            })

            // 判断是否全选
            let checkAll=checkedList.length===allList.length;
            if(item.attributes.checked&&checkAll){
              checkedList=[item.id]
            }

            this.categoriesList.push({
              id:item.id,
              name:item.attributes.name,
              checked:item.attributes.checked===1,
              childrens,
              checkedList,
              allList,
              checkAll
            })
          })
        }
      }).catch(err=>{
      })
    },
    // 2 获取用户设置状态
    getGroups(){
      this.appFetch({
        url:'groups',
        method:'get',
      }).then(res=>{
        if (res.errors){
          this.$message.error(res.errors[0].code);
        }else {
          res.data.forEach(item=>{
            this.groupsList.push({
              id:item.id,
              name:item.attributes.name,
              checked:item.attributes.checked===1
            })
          })
        }
      }).catch(err=>{
      })
    },
    // 3 获取用户、话题、主题添加 排除
    getSortData(){
      this.appFetch({
        url:'sequence',
        methods:'get'
      }).then(res=>{
        const data=res.data.attributes;

        data.users.forEach(item=>{
          this.sortData.usersList.push({
            id:item.id,
            name:item.username
          })
        });
        data.topics.forEach(item=>{
          this.sortData.topicsList.push({
            id:item.id,
            name:item.content
          })
        });
        this.threads= data.threads;
        data.block_users.forEach(item=>{
          this.sortData.blockUsersList.push({
            id:item.id,
            name:item.username
          })
        });
        data.block_topics.forEach(item=>{
          this.sortData.blockTopicsList.push({
            id:item.id,
            name:item.content
          })
        });
        this.blockThreads= data.block_threads;
      })
    },
    // 4 开关状态
    getSortBtnStatus(){
      this.appFetch({
        url: "forum",
        method: "get",
        data: {}
      }).then(data => {
          if (data.errors) {
            this.$message.error(data.errors[0].code);
          } else {
            this.siteOpenSort=data.readdata._data.set_site.site_open_sort===1;
          }
      }).catch(error => {
      });
    },
    // 5 推荐的用户、话题
    getRecommendUsers(){
      const data=[]
      this.appFetch({
        url:'randomUsers',
        method:'get',
      }).then(res=>{
        if (res.errors){
          this.$message.error(res.errors[0].code);
        }else {
          res.data.forEach(item=>{
            data.push({
              id:item.id,
              name:item.attributes.username,
            })
          })
        }
      }).catch(err=>{
      })
      return data;
    },
    getRecommendTopics(){
      const data=[]
      this.appFetch({
        url:'randomTopics',
        method:'get',
      }).then(res=>{
        if (res.errors){
          this.$message.error(res.errors[0].code);
        }else {
          res.data.forEach(item=>{
            data.push({
              id:item.id,
              name:item.attributes.content
            })
          })
        }
      }).catch(err=>{
      })
      return data;
    },


    // 用户、话题搜索 查询数据
    querySearchAsync(queryString,cb) {
      if(!queryString){return}
      var results;
      results=this.searchText==='用户'?this.searchByUser(queryString):this.searchByTopic(queryString)
      
      clearTimeout(this.timeout);
      this.timeout = setTimeout(() => {
        cb(results);
        this.timeout=null;
      }, 1000);
    },
    searchByUser(queryString){
      const data=[]
      this.appFetch({
        methods:'get',
        url:'users',
        params:{
          "filter[username]": '*' + queryString + '*'
        }
      }).then(res=>{
        res.data.forEach(item=>{
          data.push({
            id:item.attributes.id,
            value:item.attributes.username,
            name:item.attributes.username
          })
        })
      })
      return data
    },
    searchByTopic(queryString){
      const data=[]
      this.appFetch({
        methods:'get',
        url:'topics',
        params:{
          "filter[content]":queryString
        }
      }).then(res=>{
        res.data.forEach(item=>{
          data.push({
            id:item.attributes.id,
            value:item.attributes.content,
            name:item.attributes.content
          })
        })
      })
      return data
    },

    // 处理一级分类选中取消
    handleCheckChange(index){
      if(!this.categoriesList[index].checked){
        this.categoriesList[index].checkedList=[]
        this.categoriesList[index].checkAll=false
      }
    },
    // 处理二级分类全选状态
    handleChange(index){
      const item=this.categoriesList[index]
      if(item.checkedList.includes(item.id)){
        this.categoriesList[index].checkedList=[item.id]
        this.categoriesList[index].checkAll=true
      }else{
        this.categoriesList[index].checkAll=false
      }
    },
    // 禁止显示选择下拉框
    isShowSelectOptions(val){
      if(val){
        for(let item in this.$refs)
          this.$refs[item].blur();
        }
    },
    // 进入搜索页
    toSearch(type,text){
      this.isShow=false;
      this.searchText=text;
      this.type=type;
      this.selectedData=this.sortData[this.type];
      this.recommendData=text==="用户"?this.getRecommendUsers():this.getRecommendTopics();
    },
    // 选择搜索项
    handleSelect(newItem){
      const isSelect=this.selectedData.some(item=>{
        return item.id===newItem.id      
      })
      this.state='';
      if(isSelect){
        this.$message.warning(`该${this.searchText}已被添加`)
        return
      }
      this.selectedData.push({id:newItem.id,name:newItem.name})
    },
    // 关闭选中标签
    handleClose(item){
      this.selectedData.splice(this.selectedData.indexOf(item),1)
    },
    // 选择推荐标签
    selectRecommend(newItem){
      const isSelect=this.selectedData.some(item=>{
        return item.id===newItem.id      
      })
      if(isSelect){
        this.$message.warning(`该${this.searchText}已被添加`)
        return
      }
      this.selectedData.push(newItem)
    },
    // 返回排序主页
    backSortPage(){
      this.isShow=true;
      this.sortData[this.type]=this.selectedData;
      this.type='';
      this.searchText="";
      this.selectedData=[];
      this.timeout=null;
    },
    // 检验主题格式
    checkFormat(str){
      if(!str){return true}
      return /^(\d+,?)*\d+$/.test(str)
    },
    getIdString(arr){
      const data=[]
      arr.forEach(item=>{
        data.push(item.id)
      })
      return data.toString()
    },

    
    // 提交
    handleSortSubmit(){
      // 判断主题格式
      if(!this.checkFormat(this.threads)||!this.checkFormat(this.blockThreads)){
        this.$message.error('主题id格式：以英文逗号分隔，数字结尾，且中间不能有空格');
        return
      }
      let category_ids=[]
      let group_ids=[]
      // 1 处理分类
      this.categoriesList.forEach(item=>{
        if(!item.checked){
          return
        }
        category_ids.push(item.id)
        if(item.checkAll){
          category_ids.push(...item.allList)
        }else{
          category_ids.push(...item.checkedList)
        }
      })
      // 2 处理角色
      this.groupsList.forEach(item=>{
        if(item.checked){
          group_ids.push(item.id)
        }
      })

      const data={
        category_ids:category_ids.toString() || "",
        group_ids:group_ids.toString() || "",
        user_ids:this.getIdString(this.sortData.usersList) || "",
        topic_ids:this.getIdString(this.sortData.topicsList) || "",
        thread_ids:this.threads,
        block_user_ids:this.getIdString(this.sortData.blockUsersList) || "",
        block_topic_ids:this.getIdString(this.sortData.blockTopicsList) || "",
        block_thread_ids:this.blockThreads,
        site_open_sort:this.siteOpenSort?1:0
      }
      this.appFetch({
        url: "sequence",
        method: "post",
        data: {
          "data":{
            "attributes":data
          }
        }
      })
      .then(data => {
        if (data.errors) {
          if (data.errors[0].detail) {
            this.$message.error(
              data.errors[0].code + "\n" + data.errors[0].detail[0]
            );
          } else {
            this.$message.error(data.errors[0].code);
          }
        } else {
          this.$message({
            message: "提交成功",
            type: "success"
          });
        }
      })
    },
  },
}