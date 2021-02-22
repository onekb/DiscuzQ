<template>
    <div class="user-review-box">
      <div class="user-review-table">
        <el-table
          ref="multipleTable"
          :data="tableData"
          tooltip-effect="dark"
          style="width: 100%"
          @selection-change="handleSelectionChange">

          <el-table-column
            type="selection"
            width="55">
          </el-table-column>

          <el-table-column
            label="编号"
            prop="_data.id"
            width="100">
          </el-table-column>

          <el-table-column
            prop="_data.username"
            label="用户名"
            width="200">
          </el-table-column>

          <!-- <el-table-column
            prop="_data.registerReason"
            label="注册原因"
            width="150"
            show-overflow-tooltip>
          </el-table-column> -->
          
          <el-table-column
            width="200"
            label="注册时间">
            <template slot-scope="scope">{{ formatDate(scope.row._data.createdAt) }}</template>
          </el-table-column>
          
          <el-table-column
            prop="_data.originalMobile"
            label="手机号"
            width="150">
          </el-table-column>

          <el-table-column
            :key="index"
            width="150"
            label="扩展字段">
            <template slot-scope="scope">
                <el-button type="text" @click="dialogTableVisibleFun(scope)">查看</el-button>
                <el-dialog title="扩展信息" :visible.sync="visibleExtends[scope.$index].dialogTableVisible">
                  <el-table
                  class="user-review-table__box"
                    ref="multipleTable"
                    :data="gridDataFun(scope)"
                    tooltip-effect="dark"
                    max-height="400"
                    :border="true"
                    @selection-change="handleSelectionChange">
                    <el-table-column
                      class="user-review-table__trbox"
                      label="字段名称"
                      prop="_data.name"
                      width="100">
                       <template slot-scope="scope">
                         <div class="user-review-table__tdbox">{{scope.row._data.name}}</div>
                       </template>
                    </el-table-column>

                    <el-table-column
                      label="字段信息"
                      width="571">
                      <template slot-scope="scopes">
                        <div class="user-review-table__exdent">
                            <p v-if="scopes.row._data.type === 0 || scopes.row._data.type === 1">
                                {{ scopes.row._data.fields_ext }}
                            </p>
                            <p v-if="scopes.row._data.type === 2 || scopes.row._data.type === 3">
                                {{ optionFun(scopes.row._data.fields_ext)}}
                            </p>
                            <div v-if="scopes.row._data.type === 4 || scopes.row._data.type === 5">
                              <p v-for="(iamge, indexImg) in scopes.row._data.fields_ext" :key="indexImg">
                                <a :href="iamge.url"  target="_blank">{{ iamge.name }}</a>
                              </p>
                            </div>
                          </div>
                      </template>
                    </el-table-column>
                  </el-table>
                </el-dialog>
              <!-- <el-collapse v-model="activeNames" @change="handleChange">
                <el-collapse-item title="查看" name="1">
                  <div v-for="(extend, index) in scope.row.extFields" :key="index"  class="user-review-table__exdent">
                    <p v-if="extend._data.type === 0 || extend._data.type === 1">
                      <span class="user-review-table__span">
                        {{extend._data.name + '：'}}
                      </span>
                      <span>
                        {{ extend._data.fields_ext }}
                      </span>
                    </p>
                    <p v-if="extend._data.type === 2 || extend._data.type === 3">
                      <span class="user-review-table__span">
                        {{extend._data.name + '：'}}
                      </span>
                      <span>
                        {{ optionFun(extend._data.fields_ext)}}
                      </span>
                    </p>
                    <div v-if="extend._data.type === 4 || extend._data.type === 5">
                      <p v-for="(iamge, indexImg) in extend._data.fields_ext" :key="indexImg">
                        <a :href="iamge.url"  target="_blank">{{ iamge.name }}</a>
                      </p>
                    </div>
                  </div>
                </el-collapse-item>
              </el-collapse> -->
            </template>
          </el-table-column>

          <el-table-column
            label="操作"
            width="230">
            <template slot-scope="scope">
              <el-button type="text" @click="singleOperation('pass',scope.row._data.id, scope)" >通过</el-button>
              <el-button type="text" @click="singleOperation('no',scope.row._data.id, scope)" >否决</el-button>

              <el-popover
                width="100"
                placement="top"
                :ref="`popover-${scope.$index}`">
                <p>确定删除该项吗？</p>
                <div style="text-align: right; margin: 10PX 0 0 0 ">
                  <el-button type="text" size="mini" @click="scope._self.$refs[`popover-${scope.$index}`].doClose()">取消</el-button>

                  <el-button type="danger" size="mini" @click="singleOperation('del',scope.row._data.id);scope._self.$refs[`popover-${scope.$index}`].doClose()" >确定</el-button>
                </div>
                <!-- <el-button type="text" slot="reference">删除</el-button> -->
              </el-popover>
            </template>
          </el-table-column>
        </el-table>
      <!-- 注释掉 暂无数据重复问题-->
        <!-- <tableNoList v-show="tableData.length < 1"></tableNoList> -->

        <Page
          v-if="pageCount > 1"
          @current-change="handleCurrentChange"
          :current-page="currentPaga"
          :page-size="10"
          :total="total">
        </Page>

      </div>

      <Card class="footer-btn">
        <el-button type="primary" :loading="btnLoading" @click="allOperation('pass')">通过</el-button>
        <el-button type="primary" plain @click="allOperation('no')">否决</el-button>
        <el-popover
          placement="top"
          width="160"
          v-model="visible">
          <p>确定删除选中的用户吗？</p>
          <div style="text-align: right; margin: 10PX 0 0 0">
            <el-button size="mini" type="text" @click="visible = false">取消</el-button>
            <el-button type="danger" size="mini" @click="allOperation('del')">确认</el-button>
          </div>
          <!-- <el-button size="medium" slot="reference">删除</el-button> -->
        </el-popover>

      </Card>
    </div>
</template>

<script>
import userReviewCon from '../../../controllers/site/user/userReviewCon';
import '../../../scss/site/module/userStyle.scss';

export default {
    name: "userReview",
  ...userReviewCon
}
</script>
