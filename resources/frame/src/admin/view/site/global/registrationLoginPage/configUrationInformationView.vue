<template>
  <div class="register-option-box">
    <Card header="配置注册扩展信息" style="border-bottom: none"></Card>
    <div class="register-option-table">
      <el-table
        ref="multipleTable"
        :data="groupsList"
        tooltip-effect="dark"
        style="width: 100%"
        @selection-change="handleSelectionChange"
      >
        <el-table-column type="selection" width="50"></el-table-column>

        <el-table-column label="字段名称">
          <template slot-scope="scope">
            <el-input clearable v-model="scope.row.name" />
          </template>
        </el-table-column>

        <el-table-column label="字段类型">
          <template slot-scope="scope">
            <el-select class="register-option-table__choice" v-model="scope.row.description" @change="obtainValue" placeholder="请选择">
              <el-option
                v-for="item in options"
                :key="item.value"
                :label="item.label"
                :value="item.value">
              </el-option>
            </el-select>
            <div class="register-option-table__type" v-if="scope.row.description === 2 || scope.row.description === 3">
              <el-input
                class="register-option-table__son"
                type="textarea"
                :rows="2"
                placeholder="输入值，每行一个选项值"
                v-model="scope.row.content">
              </el-input>
            </div>
          </template>
        </el-table-column>

        <el-table-column label="字段排序">
          <template slot-scope="scope">
            <el-input clearable v-model="scope.row.sort" />
          </template>
        </el-table-column>

        <el-table-column label="字段介绍">
          <template slot-scope="scope">
            <el-input clearable v-model="scope.row.introduce" />
          </template>
        </el-table-column>

        <el-table-column label="是否启用">
          <template slot-scope="scope">
            <div class="register-option-table__enable">
              <el-checkbox v-model="scope.row.enable" class="register-option-table__field"></el-checkbox>
            </div>
          </template>
        </el-table-column>

        <el-table-column label="是否必填">
          <template slot-scope="scope">
            <div class="register-option-table__enable">
              <el-checkbox v-model="scope.row.required" class="register-option-table__field"></el-checkbox>
            </div>
          </template>
        </el-table-column>

        <el-table-column label="操作">
          <template slot-scope="scope">
            <div class="register-option-table__detele">
              <el-button type="text" @click="operationDelete(scope)">删除</el-button>
              <!-- <span class="delete-field" @click="">删除</span> -->
            </div>
          </template>
        </el-table-column>
      </el-table>
      <Card>
        <div class="egister-option-btn" @click="increaseList">
          <i class="el-icon-circle-plus-outline"></i>
          <span class="egister-option-increase">新增字段</span>
        </div>
      </Card>
      <Card class="register-option-btn">
        <el-button
          type="primary"
          :loading="subLoading"
          size="medium"
          @click="submitClick"
        >提交</el-button>

        <el-button
          class="register-option__button"
          type="primary"
          :loading="subLoading"
          size="medium"
          @click="submitDetele"
        >删除</el-button>
      </Card>
    </div>
  </div>
</template>

<script>
import '../../../../scss/site/module/globalStyle.scss';
import relatedConfiguration from '../../../../controllers/site/global/registrationRelated/relatedConfiguration';
export default {
  name: 'registration-btn',
  ...relatedConfiguration,
};
</script>