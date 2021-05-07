<template>
  <div>
    <Card :header="query.typeName"></Card>
    <Card header="通知方式：" class="card-radio-con">
      <CardRow description="若没勾选，则下面不显示对应的方式。若不能支持，则置灰不能勾选 。 ">
      <el-checkbox-group v-model="noticeList" @change="noticeListChange">
        <el-checkbox label=0>系统通知</el-checkbox>
        <el-checkbox label=4 v-if="query.typeName != '新用户注册通知' && query.typeName != '注册审核通过通知' && query.typeName != '注册审核不通过通知' && query.typeName != '问答提问通知' && query.typeName != '问答回答通知' && query.typeName != '问答过期通知'">小程序通知</el-checkbox>
        <el-checkbox label=1>微信模板通知</el-checkbox>
        <el-checkbox label=2>短信通知</el-checkbox>
      </el-checkbox-group>
      </CardRow>
    </Card>
    <!-- 系统通知 -->
    <div class="system-notice" v-show="showSystem">
      <p class="system-title">系统通知</p>
    <Card :header="query.typeName">
      <CardRow :description="systemList.disabled ? '当前通知的内容和格式为系统内置，无法自定义配置' : '系统发送的欢迎信息的标题，不支持HTML，不超过75字节'">
        <el-input type="text" maxlength="75" v-model="systemList.title" :disabled="systemList.disabled" ></el-input>
      </CardRow>
    </Card>

    <Card header="通知内容：">
      <CardRow row :description="systemList.disabled ? '' : systemDes">
        <el-input type="textarea" :autosize="{ minRows: 5, maxRows: 5}" v-model="systemList.content" :disabled="systemList.disabled" clearable></el-input>
      </CardRow>
    </Card>
  </div>

    <!-- 小程序订阅信息 -->
    <div class="system-notice" v-show="showMini">
      <p class="system-title">小程序订阅信息</p>
    <Card header="模板ID：">
      <CardRow :description="miniProgramList.mini_program_prompt ? `请填写小程序订阅消息的模版ID，此消息的触发操作为「${miniProgramList.mini_program_prompt}」，每一个触发操作最多支持3个不同模板ID的订阅消息` : '请填写模板消息的ID'">
        <el-input type="text" maxlength="75" v-model="miniProgramList.template_id" ></el-input>
      </CardRow>
    </Card>

    <Card header="">
    <div class="applets-box">
      <div class="applets-box-content">
      <CardRow row :description="miniDes+miniTips">
    <div v-for="(item, index) in keyList" :key="index" class="applets">
      <div class="applets-titles">{{item}}</div>
      <el-input type="input" v-if ="keyList.length > 0" v-model="miniKeyWord[index]"  class="applets-input"></el-input>
    </div>

      </CardRow>
    <CardRow row description="请填写正确的小程序路径，填写错误将导致用户无法接收到消息通知">
      <div class="applets">
        <span class="applets-titles">小程序路径：</span>
        <el-input type="input" v-model="miniProgramList.page_path" class="applets-input"></el-input>
      </div>
      </CardRow>
      </div>
    </div>
    </Card>
  </div>
  <!-- 微信模板信息 -->
    <div class="system-notice" v-show="showWx">
      <p class="system-title">微信模板信息</p>
    <Card header="模板ID：">
      <CardRow description="请填写模板消息的ID">
        <el-input type="text" maxlength="75" v-model="wxList.template_id" ></el-input>
      </CardRow>
    </Card>

    <Card header="">
    <div class="applets-box">
      <div class="applets-box-content">
      <CardRow row :description="wxDes">
      <div class="applets">
        <span class="applets-titles">first：</span>
        <el-input type="input" v-model="wxList.first_data" class="applets-input"></el-input>
      </div>
      <div v-for="(item, index) in appletsList" :key="index" class="applets">
        <span class="applets-title">keyword{{index + 1}}:</span>
        <el-input type="input" v-model="appletsList[index]"  class="applets-input"></el-input>
        <span class="iconfont iconicon_delect iconhuishouzhan" @click="delectClick(index, 'appletsList')" v-show="index>1"></span>
      </div>
      <div class="applets">
      <span class="applets-titles"></span>
      <TableContAdd
        @tableContAddClick="tableContAdd('appletsList')"
        cont="添加关键字"
        v-show="showClick"
      ></TableContAdd>
      </div>
      <div class="applets">
        <span class="applets-titles">remark：</span>
        <el-input type="input" v-model="wxList.remark_data" class="applets-input"></el-input>
      </div>
      <div class="applets">
        <span class="applets-title">跳转类型：</span>
        <div class="applets-radio">
          <el-radio v-model="wxList.redirect_type" :label="0">无跳转</el-radio>
          <el-radio v-model="wxList.redirect_type" :label="2">跳转至小程序</el-radio>
          <el-radio v-model="wxList.redirect_type" :label="1">跳转至H5</el-radio>
        </div>
      </div>
      </CardRow>
    <CardRow row :description="wxList.redirect_type === 2 ?'请填写正确的小程序路径，填写错误将导致用户无法接收到消息通知。' : ''">
      <div class="applets" v-show="wxList.redirect_type === 1">
        <span class="applets-titles">H5网址：</span>
        <el-input type="input" v-model="wxList.redirect_url" class="applets-input"></el-input>
      </div>
      <div class="applets" v-show="wxList.redirect_type === 2">
        <span class="applets-titles">小程序路径：</span>
        <el-input type="input" v-model="wxList.page_path" class="applets-input"></el-input>
      </div>
      </CardRow>
      </div>
    </div>
    </Card>
  </div>
  <!-- 短信通知 -->
    <div class="system-notice" v-show="showSms">
      <p class="system-title">短信通知</p>
    <Card header="短信模板ID：">
      <CardRow description="填写在腾讯云已配置并审核通过的短信验证码的模版的ID">
        <el-input type="text" maxlength="75" v-model="smsList.template_id"></el-input>
      </CardRow>
    </Card>

    <Card header="">
    <div class="applets-box">
      <div class="applets-box-content">
      <CardRow row :description="smsDes">
      <div v-for="(item, index) in smsKeyWord" :key="index" class="applets">
        <span class="applets-title">变量{{"{" + (index + 1) + "}"}}:</span>
        <el-input type="input" v-model="smsKeyWord[index]"  class="applets-input"></el-input>
        <span class="iconfont iconicon_delect iconhuishouzhan" @click="delectClick(index, 'smsKeyWord')"></span>
      </div>
      <div class="applets">
      <span class="applets-titles"></span>
      <TableContAdd
        @tableContAddClick="tableContAdd('smsKeyWord')"
        cont="添加关键字"
      ></TableContAdd>
      </div>
      </CardRow>
      </div>
    </div>
    </Card>
  </div>
    <Card class="footer-btn">
      <el-button type="primary" size="medium" @click="Submission">提交</el-button>
    </Card>
  </div>
</template>

<script>
import "../../../../scss/site/module/globalStyle.scss";
import noticeConfigureCon from "../../../../controllers/site/global/notice/noticeConfigureCon";

export default {
  name: "notice-configure-view",
  ...noticeConfigureCon
};
</script>
