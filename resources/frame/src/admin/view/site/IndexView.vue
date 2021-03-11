<template>
  <div>
    <el-container class="index-container-box">
      <el-header height="80px" class="index-header">
        <h1 class="index-header__icon">
          <a href="/admin/home">
            <img src="/static-admin/images/admin-logo-x2.png"  alt="Logo">
          </a>
        </h1>

        <div class="index-header__nav">
          <ul class="menu-demo">
            <li class="menu-item"
                @click="menuClick(item)"
                :class="navSelect === item.name?'is-active':''"
                v-for="(item,index) in navList"
                :key="index">
              {{item.title}}
            </li>
          </ul>
        </div>

        <div class="index-header__info-menu">
          <span>您好，{{userName}}</span>
          <span @click="quitClick">&nbsp;[退出]</span>
          <span :style="{marginLeft: '20px'}" @click="clearCache">清空缓存</span>
          <span class="site-home"><a :href="appConfig.baseUrl" target="_blank">{{$t('admin.siteHome')}}</a></span>
        </div>
      </el-header>

      <el-container class="index-main-con">

        <el-aside class="index-main-con__side" width="256px">
          <div class="index-main-con__side-title">
            <span>{{sideTitle}}</span>
          </div>

          <div class="index-main-con__side-list">

            <ul class="index-side-ul">
              <li
                v-for="(item, index) in sideList"
                :key="index"
                class="index-side-li"
                :class="sideSelect === item.name?'is-active':''"
                @click="sideClick(item)"
              >
                <span class="iconfont" :class="item.icon"></span>
                <span>{{item.title}}</span>
              </li>

            </ul>

          </div>

          <div class="index-main-con__side-footer">
            <p>Powered by Discuz! Q</p>
          </div>
        </el-aside>

        <el-main ref="indexMainCon" class="index-main-con__main">

          <div class="index-main-con__main-title">
            <h1>{{indexTitle}}</h1>
            <div class="index-main-con__main-title__class">
              <i v-if="sideSubmenu.length > 0"></i>
              <span v-for="(item,index) in sideSubmenu" @click="sideSubmenuClick(item.title)" :class="item.title === sideSubmenuSelect?'is-active':''" :key="index">{{item.title}}</span>
            </div>
          </div>

          <div class="router-con">
            <router-view></router-view>
          </div>

        </el-main>
      </el-container>

    </el-container>
    <el-dialog
      title="最后一步"
      class="index-qcloud-dialog"
      width="422px"
      :visible.sync="dialogVisible"
      :before-close="handleClose">
      <Card header="配置腾讯云的云API信息"></Card>
      <Card header="Secretid：" class="input-box" style="margin-top:5PX">
        <el-input v-model="secretId" clearable></el-input>
      </Card>

      <Card header="SecretKey：" class="input-box">
        <el-input v-model="secretKey" clearable></el-input>
      </Card>
      <el-button type="primary" @click='Submission' class="dialog-button">提交</el-button>
      <div class="dialog-des">
        <h2>说明：</h2>
        <div>云 API 获取免费，可帮助站长快捷的按需开通腾讯云的能力，提升站点功能和性能。也可帮助腾讯云旗下的 Discuz! Q 团队更好的改进产品。</div>
      </div>
      <div class="dialog-des">
        <h2>获取方式：</h2>
        <div>1. <a href="https://cloud.tencent.com/login" target="_blank">登录腾讯云</a></div>
        <div>2. 账号-访问管理-访问密钥-<a href="https://console.cloud.tencent.com/cam/capi" target="_blank">API密钥管理</a>（主账号和子账号密钥均可）</div>
      </div>
    </el-dialog>
  </div>
</template>

<script>
import '../../scss/site/module/indexView.scss';
import IndexCon from '../../controllers/site/IndexCon';
export default {
	name: "adminIndex",
  ...IndexCon
}
</script>
