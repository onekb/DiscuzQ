<template>
    <div class="recycle-bin-box">
      <div class="recycle-bin-header">
        <div class="recycle-bin-header__section">
          <div class="section-top">
            <span class="cont-review-header__lf-title">作者：</span>
            <el-input size="medium" v-model="searchUserName" clearable placeholder="搜索作者"></el-input>
          </div>
          <div>
            <span class="cont-review-header__lf-title">搜索范围：</span>
            <!-- <el-select v-model="categoriesListSelect" clearable  size="medium" placeholder="选择主题分类">
              <el-option
                v-for="item in categoriesList"
                :key="item.id"
                :label="item.name"
                :value="item.id">
              </el-option>
            </el-select> -->
            <el-cascader
              v-model="categoriesListSelect"
              :options="categoriesList"
              :props="{ expandTrigger: 'hover', checkStrictly: true }">
            </el-cascader>
          </div>
        </div>

        <div class="recycle-bin-header__section">
          <div class="section-top">
            <span class="cont-review-header__lf-title">内容包含：</span>
            <el-input size="medium" v-model="keyWords" clearable placeholder="搜索内容包含"></el-input>
          </div>
          <div>
            <span class="cont-review-header__lf-title">操作人：</span>
            <el-input size="medium" v-model="operator" clearable placeholder="搜索操作人"></el-input>
          </div>
        </div>

        <div class="recycle-bin-header__section">
          <div class="section-top">
            <span class="cont-review-header__lf-title time-title">发布时间范围：</span>
            <el-date-picker
              v-model="releaseTime"
              value-format="yyyy-MM-dd"
              type="daterange"
              align="right"
              unlink-panels
              size="medium"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              :picker-options="pickerOptions">
            </el-date-picker>
          </div>
          <div>
            <span class="cont-review-header__lf-title time-title">删除时间范围：</span>
            <el-date-picker
              v-model="deleteTime"
              value-format="yyyy-MM-dd"
              type="daterange"
              align="right"
              unlink-panels
              size="medium"
              range-separator="至"
              start-placeholder="开始日期"
              end-placeholder="结束日期"
              :picker-options="pickerOptions">
            </el-date-picker>
          </div>
        </div>

        <div class="recycle-bin-header__section">
          <el-button size="small" type="primary" @click="searchClick">搜索</el-button>
        </div>
      </div>

      <div class="recycle-bin-table">
        <ContArrange
          v-for="(items,index) in themeList"
          :author="!items.user?'该用户被删除':items.user._data.username"
          :theme="items.category._data.name"
          :finalPost="formatDate(items._data.createdAt)"
          :deleTime="formatDate(items._data.deletedAt)"
          :userId="!items.user?'该用户被删除':items.user._data.id"
          :key="items._data.id"
        >
          <div class="recycle-bin-table__side" slot="side">
            <el-radio-group @change="radioChange($event,index)" v-model="submitForm[index].radio">
              <el-radio label="还原"></el-radio>
              <el-radio label="删除"></el-radio>
            </el-radio-group>
          </div>

          <a slot="longText" class="recycle-bin-table__long-text" v-if="items._data.type === 1" :href="'/topic/index?id=' + items._data.id" target="_blank">
            {{items._data.title}}
            <span  class="iconfont" :class="parseInt(items._data.price) > 0?'iconmoney':'iconchangwen'" ></span>
          </a>

          <div class="recycle-bin-table__main" slot="main">
            <a class="recycle-bin-table__main__cont-text" :href="'/topic/index?id=' + items._data.id" target="_blank" :style="{'display':(items.threadVideo ? 'inline':'block')}" v-html="items.firstPost._data.contentHtml"></a>
            <span class="iconfont iconvideo" v-if="items.threadVideo"></span>
            <div class="recycle-bin-table__main__cont-imgs" v-if="!items._data.title">
              <p class="recycle-bin-table__main__cont-imgs-p" v-for="(item,index) in items.firstPost.images" :key="item._data.thumbUrl">
                <img  v-lazy="item._data.thumbUrl" @click="imgShowClick(items.firstPost.images,index)" :alt="item._data.fileName">
              </p>
            </div>
            <div class="recycle-bin-table__main__cont-annex" v-show="items.firstPost.attachments.length > 0">
              <span>附件：</span>
              <p v-for="(item,index) in items.firstPost.attachments" :key="index">
                <a :href="item._data.url" target="_blank">{{item._data.fileName}}</a>
              </p>
            </div>
          </div>

          <div class="recycle-bin-table__footer" slot="footer">
            <div class="recycle-bin-table__footer-operator">
              <span>操作者：</span>
              <span>{{!items.user?'操作者被禁止或删除':items.deletedUser._data.username}}</span>
            </div>

            <div class="recycle-bin-table__footer-reason" v-if="items.lastDeletedLog && items.lastDeletedLog._data.message.length > 0">
              <span>原因：</span>
              <span>{{!items.user?'操作者被禁止或删除':items.lastDeletedLog && items.lastDeletedLog._data.message}}</span>
            </div>
            <div class="transcodStatus">
              <span class="transcoding_status" v-if="items.threadVideo && items.threadVideo._data.status == 0">转码中</span>
              <span class="transcoding_status" v-if="items.threadVideo && items.threadVideo._data.status == 2">转码失败</span>
            </div>
          </div>

        </ContArrange>

        <el-image-viewer
          v-if="showViewer"
          :on-close="closeViewer"
          :url-list="url" />

        <tableNoList v-show="themeList.length < 1"></tableNoList>

        <Page
          v-if="pageCount > 1"
          @current-change="handleCurrentChange"
          :current-page="currentPaga"
          :page-size="10"
          :total="total">
        </Page>
      </div>

      <div class="recycle-bin-footer footer-btn">
        <el-button size="small" :loading="subLoading" type="primary" @click="submitClick">提交</el-button>
        <el-button type="text" :loading="btnLoading === 1" @click="allOperationsSubmit(1)">全部还原</el-button>
        <el-popover
          width="100"
          placement="top"
          v-model="visible"
        >
          <p>确定删除该项吗？</p>
          <div style="text-align: right; margin: 10PX 0 0 0 ">
            <el-button
              type="danger"
              size="mini"
              @click="visible = false"
            >
              取消
            </el-button>
            <el-button
              type="primary"
              size="mini"
              @click="
                allOperationsSubmit(2)
                visible = false"
              >确定</el-button
            >
          </div>
          <el-button slot="reference" type="text" :loading="btnLoading === 2">全部删除</el-button>
        </el-popover>
        <!-- <el-checkbox v-model="appleAll">将操作应用到其他所有页面</el-checkbox> -->
      </div>

    </div>
</template>

<script>
import '../../../../scss/site/module/contStyle.scss';
import recycleBinCon from '../../../../controllers/site/cont/recycleBin/recycleBinCon'
import ElRadio from "element-ui/packages/radio/src/radio";
export default {
  components: {ElRadio},
  name: "recycle-bin-view",
  ...recycleBinCon
}
</script>
