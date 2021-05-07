<template>
  <div class="site-function-set-box">
    <!-- 发布功能 -->
    <Card>
      <CardRow class="publishing-title" description="生效范围">
        发布功能
      </CardRow>
    </Card>

    <Card >
      <CardRow class="card-points" description="允许站点发布文字帖">
        <el-checkbox v-model="publishing.text">文字</el-checkbox>
      </CardRow>
    </Card>

    <Card >
      <CardRow class="card-points" description="允许站点发布帖子">
        <el-checkbox v-model="publishing.post">帖子</el-checkbox>
      </CardRow>
    </Card>

    <Card >
      <CardRow class="card-points" description="允许站点发布图片帖">
        <el-checkbox v-model="publishing.picture">图片</el-checkbox>
      </CardRow>
    </Card>

    <Card >
      <CardRow class="card-points" description="允许站点发布视频帖">
        <el-checkbox v-model="publishing.video">视频</el-checkbox>
      </CardRow>
    </Card>

    <Card >
      <CardRow class="card-points" description="允许站点发布语音帖">
        <el-checkbox v-model="publishing.voice">语音</el-checkbox>
      </CardRow>
    </Card>

    <Card >
      <CardRow class="card-points" description="允许站点发布商品帖（暂不支持小程序）">
        <el-checkbox v-model="publishing.goods">商品</el-checkbox>
      </CardRow>
    </Card>

    <Card >
      <CardRow class="card-points" description="允许站点发布问答帖">
        <el-checkbox v-model="publishing.question" @change="handleQuestionChange">问答</el-checkbox>
      </CardRow>
    </Card>

    <!-- 其它功能 -->
    <Card>
      <CardRow class="publishing-title other">
        其它功能
      </CardRow>
    </Card>
    <!-- <Card>
      <CardRow
        class="card-points"
        description="开启后，用户角色将可以配置价格和有效期，用户可在前台进行用户角色购买"
      >
        <el-checkbox v-model="purchase">权限购买功能</el-checkbox>
      </CardRow>
    </Card> -->
    <Card>
      <CardRow
        class="card-points"
        description="允许发布的内容被其它用户打赏"
      >
        <el-checkbox v-model="reward">打赏功能</el-checkbox>
      </CardRow>
    </Card>

    <el-button type="primary" size="medium" @click="handlePublishingSubmit">提交</el-button>

  </div>
</template>

<script>
  import "../../../../scss/site/module/globalStyle.scss";
  import siteFunctionSetCon from "../../../../controllers/site/global/siteSet/siteFunctionSetCon";
  export default {
    name: "site-function-set-view",
    ...siteFunctionSetCon
  };
</script>
