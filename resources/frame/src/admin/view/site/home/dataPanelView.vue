<template>
  <div class="home-data-box">
    <p class="desc">
      <span style="fontWeight: bold">{{ userName }} 您好！</span><br/>
      {{ currentTime }} {{ week[this.$dayjs().day()] }}，欢迎您回到管理后台。
    </p>
    <!-- 统计数据 -->
    <div class="statistics-wrap">
      <div class="statistics-item" v-for="(item,index) in dataList" :key="index">
        <div class="statistics-item-title">{{ item.title }}</div>
        <span class="statistics-item-num" :title="item.num">{{ item.num }}</span>
      </div>
    </div>
    <!-- 站点每日数据 -->
    <div class="chart-wrap">
      <div class="chart-head">
        <div class="chart-head-left">
          <span class="iconfont iconcaiwutongji"></span>
          <span class="chart-head-titles">站点每日数据</span>
        </div>
        <div class="chart-head-right">
          <ul>
            <li
              v-for="(item,index) in items"
              :key="index"
              :class="{active:selectedMode==index}"
              @click="tab(index)"
            >{{ item.name }}</li>
          </ul>
          <el-date-picker
            v-model="selectTime"
            class="input-class"
            v-show="dayTab"
            size="small"
            clearable
            type="daterange"
            value-format="yyyy-MM-dd"
            :default-time="['00:00:00', '23:59:59']"
            range-separator="至"
            start-placeholder="开始日期"
            end-placeholder="结束日期"
            @change="changeDayOrWeek"
          ></el-date-picker>
          <el-date-picker
            class="input-class"
            v-model="valueMouth"
            size="small"
            v-show="mouthTab"
            value-format="yyyy-MM-dd"
            type="monthrange"
            range-separator="至"
            start-placeholder="开始月份"
            end-placeholder="结束月份"
            @change="changeMouth"
          ></el-date-picker>
        </div>
      </div>
      <div class="noData" v-show="noData">暂无数据</div>
      <div class="chart-content" ref="financialProfitEcharts"></div>
    </div>
  </div>
</template>
<script>
import "../../../scss/site/module/homeStyle.scss";
import dataPanelCon from "../../../controllers/site/home/dataPanelCon";
export default {
  name: "data-panel-view",
  ...dataPanelCon
};
</script>