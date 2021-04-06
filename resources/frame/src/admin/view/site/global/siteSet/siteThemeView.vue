<template>
  <div class="site-theme-box">
      <div class="site-theme-head">选择主题：</div>
      <div class="site-theme-content">
        <div class="left">
          <el-radio v-model="newTheme" :label="1">
            蓝色两栏
            <!-- <img
              class="theme-img"
              ref="img1"
              src="../../../../../../static/images/theme_blue.jpg"
              alt="蓝色皮肤"
              @click="showPreview"
            > -->
            <img
              class="theme-img"
              ref="img1"
              src="/static-admin/images/theme_blue.jpg"
              alt="蓝色皮肤"
              @click="showPreview"
            >
          </el-radio>
          <el-radio v-model="newTheme" :label="2">
            红色三栏
            <!-- <img
              class="theme-img"
              ref="img2"
              src="../../../../../../static/images/theme_red.jpg"
              alt="红色皮肤"
              @click="showPreview"
            > -->
            <img
              class="theme-img"
              ref="img2"
              src="/static-admin/images/theme_red.jpg"
              alt="红色皮肤"
              @click="showPreview"
            >
          </el-radio>
        </div>
      </div>
      <div v-if="isPreview" class="preview-box">
        <div class="preview-img-wrap">
          <img class="preview-img" ref="img3" :src="currentUrl"  @click="closePreview">
        </div>
      </div>
      <el-button size="medium" type="primary" @click="submitThemeSelect">提交</el-button>
  </div>
</template>

<script>
  import "../../../../scss/site/module/globalStyle.scss";
  import siteThemeCon from "../../../../controllers/site/global/siteSet/siteThemeCon";
  export default {
    name: "site-theme-view",
    ...siteThemeCon
  };
</script>
