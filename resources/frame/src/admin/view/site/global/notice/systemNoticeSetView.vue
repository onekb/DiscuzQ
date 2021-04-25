<template>
    <div class="system-notice-box">
      <div class="system-notice-table marT15">
        <el-table
          :data="tableData"
          style="width: 100%">

          <el-table-column
            prop=""
            label="序号"
            width="100">
            <template slot-scope="scope">
              <span v-text="getIndex(scope.$index)"> </span>
            </template>
          </el-table-column>

          <el-table-column
            prop="_data.name"
            label="通知类型"
          >
          </el-table-column>

          <el-table-column
            prop="_data.type_status"
            label="通知方式"
            align="center"
          >
          <template slot-scope="scope">
              <span
                class="notice_type"
                :class="item.is_error === 1 && 'notice_type_error'"
                v-for="(item, index) in scope.row._data.type_status"
                :key="item.type"
                @click="handleError(item)"
              >
                {{ item.type + (index < scope.row._data.type_status.length -1? "、" : "") }}
              </span>
            </template>
          </el-table-column>

          <el-table-column
            prop="address"
            label="操作"
            width="200">
            <template slot-scope="scope">
                <el-button
                  size="mini"
                  @click="configClick(scope.row._data.id,scope.row._data.name)">
                  配置
                </el-button>
            </template>
          </el-table-column>

        </el-table>
        <Page v-if="total > 1" :total="total" :pageSize="pageLimit" :currentPage="pageNum" @current-change="handleCurrentChange" />
      </div>
    </div>
</template>

<script>
  import '../../../../scss/site/module/globalStyle.scss';
  import systemNoticeSetCon from "../../../../controllers/site/global/notice/systemNoticeSetCon";
  export default {
    name: "systemNoticeSetView",
    ...systemNoticeSetCon
  }
</script>
