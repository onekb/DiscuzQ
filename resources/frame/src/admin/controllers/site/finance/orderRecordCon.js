/*
*  订单记录
* */

import Card from '../../../view/site/common/card/card';
import Page from '../../../view/site/common/page/page';
import webDb from 'webDbHelper';


export default {
  data:function () {
    return {
      tableData: [],          //订单记录列表数据
      pickerOptions: {
        shortcuts: [{
          text: '最近一周',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 7);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: '最近一个月',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 30);
            picker.$emit('pick', [start, end]);
          }
        }, {
          text: '最近三个月',
          onClick(picker) {
            const end = new Date();
            const start = new Date();
            start.setTime(start.getTime() - 3600 * 1000 * 24 * 90);
            picker.$emit('pick', [start, end]);
          }
        }]
      },                      //搜索-订单时间

      orderNumber:'',         //搜索-订单号
      operationUser:'',       //搜索-发起方
      commodity:'',           //搜索-商品
      orderTime:['',''],      //搜索-订单时间范围
      incomeSide: '',         //搜索-收入方

      pageCount:0,            //总页数
      currentPaga:1,          //第几页
      total:0,                //总数

      options: [
        {
          value: '0',
          label: '待付款'
        },
        {
          value: '1',
          label: '已付款'
        }
      ],                     //搜索-订单状态选项
      value: '',             //搜索-订单状态值
    }
  },
  methods:{
    /*
    * 跳转到话题详情
    * */
    viewClick(id){
      if (id){
        let routeData = this.$router.resolve({
          path: "/topic/index?id=" + id,
        });
        window.open(routeData.href, '_blank');
      }
    },
    /*
    * 订单状态转换
    * */
    cashStatus(status){
      switch (status){
        case 0:
          return "待付款";
          break;
        case 1:
          return "已付款";
          break;
        case 2:
          return "取消订单";
          break;
        case 3:
          return "支付失败";
          break;
        case 4:
          return "过期未支付";
          break;
        case 10:
          return "已退款";
          break;
        case 11:
          return "异常未处理";
          break;
        default:
          return "未知状态";
      }
    },
    /*
    * 搜索
    * */
    searchClick(){
      if (this.orderTime == null){
        this.orderTime = ['','']
      } else if(this.orderTime[0] !== '' && this.orderTime[1] !== ''){
        this.orderTime[0] = this.orderTime[0] + '-00-00-00';
        this.orderTime[1] = this.orderTime[1] + '-24-00-00';
      }
      this.currentPaga = 1;
      this.getOrderList();
    },
     /*
    * 切换页码
    * */
    handleCurrentChange(val){
      this.currentPaga = val;
      this.getOrderList();
    },

    /*
    * 格式化日期
    * */
    formatDate(data){
      return this.$dayjs(data).format('YYYY-MM-DD HH:mm')
    },

    /*
    * 请求接口 - 获取订单记录
    * */
    getOrderList(){
      this.appFetch({
        url:'orderList',
        method:'get',
        data:{
          include:['user','thread','thread.firstPost','payee'],
          'page[number]':this.currentPaga,
          'page[size]':10,
          'filter[order_sn]':this.orderNumber,
          'filter[product]':this.commodity,
          'filter[username]':this.operationUser,
          'filter[start_time]':this.orderTime[0],
          'filter[end_time]':this.orderTime[1],
          'filter[status]':this.value,
          'filter[payee_username]':this.incomeSide
        }
      }).then(res=>{
        if (res.errors){
          this.$message.error(res.errors[0].code);
        }else {
          this.tableData = [];
          this.tableData = res.readdata;
          this.pageCount = res.meta.pageCount;
          this.total = res.meta.total;
        }
      }).catch(err=>{
      })
    },

    getCreated(state){
      if(state){
        this.currentPaga = 1;
      } else {
        this.currentPaga = Number(webDb.getLItem('currentPag'))||1;
      };
      this.getOrderList();
    }
  },
  beforeRouteEnter (to,from,next){
    next(vm => {
      if (to.name !== from.name && from.name !== null){
        vm.getCreated(true)
      }else {
        vm.getCreated(false)
      }
    })
  },
  components:{
    Card,
    Page
  }
}
