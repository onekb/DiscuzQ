<template>
  <div class="operation-log-box">
    <!-- 操作日志搜索 -->
    <div class="log-search">
      <div class="log-search-item log-search-time">
        <span class="log-search-title">操作时间：</span>
        <el-date-picker
          v-model="startTime"
          type="date"
          placeholder="选择开始日期">
        </el-date-picker>
        <div class="line"></div>
        <el-date-picker
          v-model="endTime"
          type="date"
          placeholder="选择结束日期">
        </el-date-picker>
      </div>
      <div class="log-search-item">
        <span class="log-search-title">操作内容：</span>
        <el-input v-model="operationCon" size="medium"></el-input>
      </div>
      <div class="log-search-item">
        <span class="log-search-title">操作者：</span>
        <el-input v-model="operator" size="medium"></el-input>
      </div>
      <el-button type="primary" :loading="loading" @click="logSearchBtn">搜索</el-button>
    </div>
    <!-- 日志展示列表 -->
    <div class="log-search-list">
      <el-table :data="logListData">
        <el-table-column type="index" width="50"></el-table-column>
        <el-table-column prop="time" label="操作时间" width="200"> </el-table-column>
        <el-table-column prop="content" label="操作内容"> </el-table-column>
        <el-table-column prop="operator" label="操作者" width="120"> </el-table-column>
        <el-table-column prop="ip" label="ip" width="200"></el-table-column>
      </el-table>
    </div>
    <Page :total="total" :pageSize="pageLimit" :currentPage="pageNum" @current-change="handleCurrentChange" />
  </div>
</template>

<script>
import "../../../scss/site/module/globalStyle.scss";
import operationLogCon from "../../../controllers/site/global/operationLogCon";
export default {
    name: "operation-log-view",
  ...operationLogCon
}
</script>