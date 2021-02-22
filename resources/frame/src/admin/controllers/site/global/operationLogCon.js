import Page from '../../../view/site/common/page/page';

export default {
    data(){
      return {
        startTime:'',
        endTime:'',
        operationCon:'',
        operator:'',
        loading:false,
        logListData:[], // 日志列表数据
        total:0,
        pageLimit: 20,
        pageNum: 1,
        // 搜索参数
        query:{
          startTime:'',
          endTime:'',
          operationCon:'',
          operator:''
        }
      }
    },
    methods:{
      async getLogList() {
        try {
          const response = await this.appFetch({
            url: 'adminactionlog',
            method: "get",
            data: {
              "page[size]": this.pageLimit,
              "page[number]": this.pageNum,
              "filter[start_time]":this.query.startTime,
              "filter[end_time]":this.query.endTime,
              "filter[action_desc]":this.query.operationCon,
              "filter[username]":this.query.operator,
            }
          });
          if (response.errors) {
            throw new Error(response.errors[0].code);
          } else {
            this.total = response.meta.total;
            this.logListData = [];
            response.data.forEach(item=>{
              this.logListData.push({
                id:item.id,
                time:this.$dayjs(item.attributes.created_at).format('YYYY-MM-DD HH:mm'),
                content:item.attributes.action_desc,
                operator:item.attributes.username,
                ip:item.attributes.ip,
              })
            })
          }
        } catch (err) {
  
        }
      },
      logSearchBtn(){
        if((this.startTime===''&&this.endTime!=='')||(this.startTime!==''&&this.endTime==='')){
          this.$message.error('按时间搜索时，请同时输入开始时间、结束时间。')
          return
        }
        if(this.startTime>this.endTime){
          this.$message.error('开始时间不能大于结束时间。')
           this.startTime=''
           this.endTime=''
           return
        }
  
        const t1=this.startTime?this.$dayjs(this.startTime).format('YYYY-MM-DD')+'-00-00-00':''
        const t2=this.endTime?this.$dayjs(this.endTime).format('YYYY-MM-DD')+'-24-00-00':''
  
        this.query={
          startTime:t1,
          endTime:t2,
          operationCon:this.operationCon,
          operator:this.operator,
        }
  
        this.pageNum=1;
        this.getLogList();
      },
      handleCurrentChange(val){
        this.pageNum=val
        this.getLogList()
      }
    },
    created(){
      // 初始化时，默认请求一页
      this.getLogList()
    },
    components:{
      Page
    }
  }