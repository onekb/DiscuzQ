import webDb from "../../../../helpers/webDbHelper";

export default {
  data: function () {
    return {
      userName: "", //用户名
      currentTime: "",
      week: [
        "星期天",
        "星期一",
        "星期二",
        "星期三",
        "星期四",
        "星期五",
        "星期六",
      ],
      // 统计数据列表
      dataList: [
        {
          title: "今日活跃用户数",
          num: "0",
          key: "activeUserNumToday",
        },
        {
          title: "今日新增用户数",
          num: "0",
          key: "addUserNumToday",
        },
        {
          title: "今日发帖数",
          num: "0",
          key: "addThreadNumToday",
        },
        {
          title: "今日回帖数",
          num: "0",
          key: "addPostNumToday",
        },
        {
          title: "用户总数量",
          num: "0",
          key: "totalUserNum",
        },
        {
          title: "发帖总数量",
          num: "0",
          key: "totalThreadNum",
        },
        {
          title: "回帖总数量",
          num: "0",
          key: "totalPostNum",
        },
        {
          title: "精华内容数",
          num: "0",
          key: "essenceThreadNum",
        },
      ],
      dataPanelEcharts: null, // 图表
      selectTime: ["", ""], // 日、周统计时间
      valueMouth: ["", ""], // 月统计时间
      noData: false, // 暂无数据
      selectedMode: 0, // 选择模式 0-日，1-周，2-月
      dayTab: true, // 按日、周统计
      mouthTab: false, // 按月统计组件
      indexStatistics: true, // 默认按日、周统计
      items: [
        { name: "按日统计", sort: 1 },
        { name: "按周统计", sort: 2 },
        { name: "按月统计", sort: 3 },
      ],
      chartItems: [
        ["每日发帖", "每日回帖", "日活用户", "日注册数"],
        ["每周发帖", "每周回帖", "周活用户", "周注册数"],
        ["每月发帖", "每月回帖", "月活用户", "月注册数"],
      ],
    };
  },
  created() {
    const dateArr = this.$dayjs().format("YYYY-MM-DD").split("-");
    this.currentTime = `${dateArr[0]}年${dateArr[1]}月${dateArr[2]}日`;
    this.userName = webDb.getLItem("username");
    this.statistic();
  },
  mounted() {
    this.siteDataStatistics(); //初始化统计图表
  },
  methods: {
    // 数据看板统计切换日期
    tab(index) {
      this.selectedMode = index;
      if (index == 0 || index == 1) {
        this.dayTab = true;
        this.mouthTab = false;
        this.indexStatistics = true;
      }
      if (index == 2) {
        this.mouthTab = true;
        this.dayTab = false;
        this.indexStatistics = false;
      }
      this.siteDataStatistics();
    },
    // 获取首页站点数据统计
    statistic() {
      this.appFetch({
        url: "statisticPanel",
        method: "get",
      })
        .then((res) => {
          if (res && res.errors) return this.$message.error(res.errors[0].code);
          if (res && res.readdata) {
            const data = res.readdata._data.overData.over || {};
            this.dataList.forEach((item) => {
              item.num = data[item.key];
            });
          }
        })
        .catch((err) => {
          console.log(err);
        });
    },
    // 数据统计日、周
    changeDayOrWeek() {
      this.siteDataStatistics();
    },
    // 数据统计月
    changeMouth() {
      if (this.valueMouth == null) {
        this.valueMouth = ["", ""];
      } else if (this.valueMouth[0] !== "" && this.valueMouth[1] !== "") {
        this.valueMouth[0] = this.valueMouth[0];
        this.valueMouth[1] = this.valueMouth[1];
      }
      this.siteDataStatistics();
    },
    // 请求统计数据渲染图表
    siteDataStatistics() {
      let data = {};
      if (this.selectedMode === 0) {
        // 按日统计，默认最近7天
        const startTime = this.$dayjs().subtract(6, "day").format("YYYY-MM-DD");
        const endTime = this.$dayjs().format("YYYY-MM-DD");
        data = {
          "filter[type]": this.selectedMode + 1,
          "filter[createdAtBegin]": this.selectTime[0] || startTime,
          "filter[createdAtEnd]": this.selectTime[1] || endTime,
        };
      }

      if (this.selectedMode === 1) {
        // 按周统计，默认当前周
        const startTime = this.$dayjs().startOf("week").format("YYYY-MM-DD");
        const endTime = this.$dayjs().endOf("week").format("YYYY-MM-DD");
        data = {
          "filter[type]": this.selectedMode + 1,
          "filter[createdAtBegin]": this.selectTime[0] || startTime,
          "filter[createdAtEnd]": this.selectTime[1] || endTime,
        };
      }

      if (this.selectedMode === 2) {
        let startTime = this.valueMouth[0];
        let endTime = this.$dayjs(this.valueMouth[1])
          .endOf("month")
          .format("YYYY-MM-DD");
        // 按月统计，默认当前月
        if (this.valueMouth[0] === "" || this.valueMouth[1] === "") {
          startTime = this.$dayjs().startOf("month").format("YYYY-MM-DD");
          endTime = this.$dayjs().endOf("month").format("YYYY-MM-DD");
        }
        data = {
          "filter[type]": this.selectedMode + 1,
          "filter[createdAtBegin]": startTime,
          "filter[createdAtEnd]": endTime,
        };
      }

      this.appFetch({
        url: "statisticPanel",
        method: "get",
        data: data,
      })
        .then((res) => {
          if (res && res.errors) return this.$message.error(res.errors[0].code);
          this.noData = res && res.readdata == "" ? true : false;
          if (res && res.readdata) {
            const data = res.readdata._data;
            let date = []; // 日期
            let threadData = []; // 每日发帖
            let postData = []; // 每日回帖
            let activeUserData = []; // 日活用户
            let joinUserData = []; // 日注册数
            date = data.threadData.map((item) => {
              if (this.selectedMode === 0) {
                return item.date;
              } else if (this.selectedMode === 1) {
                return item.week;
              } else if (this.selectedMode === 2) {
                return item.month;
              } else {
                return [];
              }
            });
            threadData = data.threadData.map((item) => {
              return item.count;
            });
            postData = data.postData.map((item) => {
              return item.count;
            });
            activeUserData = data.activeUserData.map((item) => {
              return item.count;
            });
            joinUserData = data.joinUserData.map((item) => {
              return item.count;
            });

            this.siteDataEcharts(
              date,
              threadData,
              postData,
              activeUserData,
              joinUserData
            );
          }
        })
        .catch((err) => {
          console.log(err);
        });
    },
    // 绘制统计图表
    siteDataEcharts(date, threadData, postData, activeUserData, joinUserData) {
      //初始化Echarts实例
      if (!this.dataPanelEcharts) {
        this.dataPanelEcharts = this.$echarts.init(
          this.$refs.financialProfitEcharts
        );
      }

      const items = this.chartItems[this.selectedMode];
      const option = {
        tooltip: {
          trigger: "axis",
          axisPointer: {
            type: "cross",
            label: {
              backgroundColor: "#6a7985",
            },
          },
        },
        color: ["#ee6666", "#5470c6", "#91cc75", "#fac858"],
        legend: {
          data: items,
        },
        grid: {
          left: "1%",
          right: "8%",
          bottom: "2%",
          containLabel: true,
        },
        xAxis: [
          {
            type: "category",
            boundaryGap: false,
            data: date,
            axisLabel: {
              interval: 0,
              rotate: -40,
            },
          },
        ],
        yAxis: [
          {
            type: "value",
          },
        ],
        series: [
          {
            name: items[0], //threadData
            type: "line",
            data: threadData,
          },
          {
            name: items[1], //postData
            type: "line",
            data: postData,
          },
          {
            name: items[2], //activeUserData
            type: "line",
            data: activeUserData,
          },
          {
            name: items[3], //joinUserData
            type: "line",
            data: joinUserData,
          },
        ],
      };
      this.dataPanelEcharts.setOption(option);
    },
  },
};
