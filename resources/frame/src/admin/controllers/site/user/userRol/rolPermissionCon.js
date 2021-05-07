/**
 * 角色权限编辑
 */

import Card from "../../../../view/site/common/card/card";
import CardRow from "../../../../view/site/common/card/cardRow";

export default {
  data: function() {
    return {
      groupId: 0, // 用户组 ID
      checked: [], // 选中的权限
      videoDisabled: false, // 是否开启云点播
      captchaDisabled: false, // 是否开启验证码
      realNameDisabled: false, // 是否开启实名认证
      is_subordinate: false, // 是否开启推广下线
      is_commission: false, // 是否开启分成
      scale: 0, // 提成比例
      bindPhoneDisabled: false, // 是否开启短信验证
      wechatPayment: false, // 是否开启微信支付
      isReward: false, // 是否开启打赏功能
      canBeOnlooker: false, // 是否可以设置围观
      categoriesList: [], // 分类列表
      selectList: {
        "createThread.0":[], //发布文字帖扩展项
        "createThread.1":[], //发布帖子扩展项
        "createThread.2":[], //发布视频帖扩展项
        "createThread.3":[], //发布图片帖扩展项
        "createThread.4":[], //发布语音帖扩展项
        "createThread.5":[], //发布问答扩展项
        "createThread.6":[], //发布商品帖扩展项
        'viewThreads': [], // 查看主题列表扩展
        'createThread':[], // 发布主题扩展项
        'thread.reply':[], // 回复主题扩展项
        'thread.edit':[], // 编辑主题扩展
        'thread.hide':[], // 删除主题扩展
        'thread.essence':[], // 加精扩展
        'thread.viewPosts':[], // 查看主题详情扩展
        'thread.editPosts':[], // 编辑回复扩展
        'thread.hidePosts':[], // 删除回复扩展
        'thread.canBeReward': [], //打赏扩展
        'thread.editOwnThreadOrPost': [], // 编辑自己的主题、回复
        'thread.hideOwnThreadOrPost': [], // 删除自己的主题、回复
        'thread.freeViewPosts.1':[],
        'thread.freeViewPosts.2':[],
        'thread.freeViewPosts.3':[],
        'thread.freeViewPosts.4':[],
        'thread.freeViewPosts.5':[],
      },
      activeTab: {
        // 设置权限当前项
        title: "操作权限",
        name: "userOperate"
      },
      menuData: [
        // 设置权限
        {
          title: "操作权限",
          name: "userOperate"
        },
        {
          title: "安全设置",
          name: "security"
        },
        // {
        //   title: "价格设置",
        //   name: "pricesetting"
        // },
        {
          title: "其他设置",
          name: "other"
        }
        // {
        //   title: '默认权限',
        //   name: 'default'
        // },
      ],
      value: "",
      purchasePrice: "",
      // lowestPrice: "", // 被提问的最低价格
      dyedate: "",
      ispad: "",
      allowtobuy: "",
      defaultuser: false,
      checkAll: false, //是否全选
      isIndeterminate: false,//全选不确定状态
      selectText: '全选', //全选文字
      checkAllPermission: [], //所有操作权限
      temporaryChecked: [], //接口返回权限
      // 7项发布功能权限的状态
      pubFunc:{
        "createThread.0.disabled":false,
        "createThread.1.disabled":false,
        "createThread.2.disabled":false,
        "createThread.3.disabled":false,
        "createThread.4.disabled":false,
        "createThread.5.disabled":false,
        "createThread.6.disabled":false,
      },
      // 扩展全选
      expandItem: [
        'viewThreads',
        'createThread',
        'thread.reply',
        'thread.edit',
        'thread.hide',
        'thread.essence',
        'thread.viewPosts',
        'thread.editPosts',
        'thread.hidePosts',
        'thread.canBeReward',
        'thread.editOwnThreadOrPost',
        'thread.hideOwnThreadOrPost',
        'thread.freeViewPosts.1',
        'thread.freeViewPosts.2',
        'thread.freeViewPosts.3',
        'thread.freeViewPosts.4',
        'thread.freeViewPosts.5'
      ],
      mapCategoryId: new Map(),
      keyValue: 0
    };
  },
  watch: {
    checked(val){
      let isEqual = true;
      this.checkAllPermission.forEach(item => {
        if(val.indexOf(item) === -1){
          isEqual = false;
          return;
        }
      });
      if(isEqual){
        this.checkAll = true;
      }else{
        this.checkAll = false;
      }
    },
    checkAll(val){
      if(val){
        this.isIndeterminate = false;
        this.selectText = "取消全选";
      } else {
        this.isIndeterminate = true;
        this.selectText = "全选";
      }
    }
  },
  methods: {
    // getLowestPrice: function(e) {
    //   if (Number(e) < 0) {
    //     this.lowestPrice = '';
    //     this.$message.error('允许被提问的最低金额不能小于0');
    //   }
    // },
    duedata: function(evn) {
      this.duedata = evn.replace(/[^\d]/g, "");
    },
    addprice: function(evn) {
      setTimeout(() => {
        this.purchasePrice = evn
          .replace(/[^\d.]/g, "")
          .replace(/\.{2,}/g, ".")
          .replace(".", "$#$")
          .replace(/\./g, "")
          .replace("$#$", ".")
          .replace(/^(-)*(\d+)\.(\d\d).*$/, "$1$2.$3")
          .replace(/^\./g, "");
      }, 5);
    },

    getData() {
      Promise.all([this.getCategories(), this.getGroupResource(), this.getSiteInfo()])
        .then(
          res => {
            this.handleCategories(res[0]);
            this.handleGroupResource(res[1]);
            this.signUpSet(res[2]);
          },
          err => {
            console.log(err);
          }
        )
    },
    handleCategories(res) {
      if (res.errors) return this.$message.error(res.errors[0].code);

      this.categoriesList = [{ id: "", name: "全局", children: [] }]
      res.readdata.forEach(item => {
        this.mapCategoryId.set(parseInt(item._data.id), item._data.parentid);
        const category = {
          id: item._data.id,
          name: item._data.name,
          children: []
        }
        if(item._data.children) {
          item._data.children.forEach(subItem => {
            this.mapCategoryId.set(subItem.id, subItem.parentid);
            category.children.push({
              id: subItem.id,
              name: subItem.name
            })
          })
        }
        this.categoriesList.push(category);
      });
    },
    handleGroupResource(res) {
      if (res.errors) {
        if (res.errors[0].detail) {
          this.$message.error(
            res.errors[0].code + "\n" + res.errors[0].detail[0]
          );
        } else {
          this.$message.error(res.errors[0].code);
        }
        return;
      }

      const data = res.data.attributes;
      this.ispad = data.isPaid;
      this.scale = data.scale;
      // this.dyedate = data.days;
      // this.purchasePrice = data.fee;
      this.defaultuser = data.default;
      this.is_commission = data.is_commission;
      this.is_subordinate = data.is_subordinate;
      // this.value = data.isPaid;
      // this.temporaryChecked = res.readdata.permission;
      const permissions = res.readdata.permission;
      console.log('permissions', permissions)
      this.checked = [];
      permissions.forEach(item => {
        this.checked.push(item._data.permission);
      });
      // 回显选择值
      this.setSelectValue(this.checked);
    },
    signUpSet(res) {
      if (res.errors) return this.$message.error(res.errors[0].code);

      const data =  res.readdata._data;
      const siteData =  res.readdata._data.set_site;
      this.videoDisabled = data.qcloud.qcloud_vod === false;
      this.captchaDisabled =  data.qcloud.qcloud_captcha === false;
      this.realNameDisabled = data.qcloud.qcloud_faceid === false;
      this.bindPhoneDisabled =  data.qcloud.qcloud_sms === false;
      this.wechatPayment = data.paycenter.wxpay_close === false;
      this.isReward = data.set_site.site_can_reward === 1;
      this.canBeOnlooker = siteData.site_onlooker_price > 0;
      this.allowtobuy = siteData.site_pay_group_close;
      // if (!this.allowtobuy) {
      //   this.value = false;
      // }
      // 根据全局设置，判断帖子发布权限是否可选
      this.pubFunc['createThread.0.disabled']=siteData.site_create_thread0===0;
      this.pubFunc['createThread.1.disabled']=siteData.site_create_thread1===0;
      this.pubFunc['createThread.2.disabled']=siteData.site_create_thread2===0;
      this.pubFunc['createThread.3.disabled']=siteData.site_create_thread3===0;
      this.pubFunc['createThread.4.disabled']=siteData.site_create_thread4===0;
      this.pubFunc['createThread.5.disabled']=siteData.site_create_thread5===0;
      this.pubFunc['createThread.6.disabled']=siteData.site_create_thread6===0;
    },
    // 扩展项回显
    setSelectValue(data) {
      const checkedData = data;
      console.log('checkedData', checkedData);
      const selectList = this.selectList;
      checkedData.forEach((value, index) => {

        // 最低金额回显
        // if (value.indexOf('canBeAsked.money.') === 0 ) {
        //   this.lowestPrice = value.slice(17,value.length);
        // }

        // 1 红包、位置、匿名权限回显
        if(
          value.includes("redPacket")
            || value.includes("position")
            || value.includes("anonymous")
        ){
          const str=value.substr(0,14);
          !selectList[str].includes(value) && selectList[str].push(value);
          return;
        }

        // 2 分类-非全局状态回显
        if (value.includes("category")) {
          const splitIndex = value.indexOf(".");
          const obj = value.substring(splitIndex + 1);
          if (checkedData.includes(obj)) {
            checkedData.splice(index, 1);
            return;
          }
          const id = value.substring(8, splitIndex);
          const parentId = this.mapCategoryId.get(parseInt(id));
          const selectItem = parentId === 0 ? [id] : [parentId, id];
          selectList[obj].push(selectItem);
          return;
        }

        // 3 分类-全局状态回显
        this.expandItem.includes(value) && selectList[value].push([""]);
      });
      this.selectList = selectList;
      this.checked = checkedData;
      console.log('this.checked', this.checked);
    },
    // 提交权限选择
    submitClick() {
      if (!this.checkNum()) {
        return;
      }
      if (!this.checkSelect()) {
        return;
      }
      if (this.value) {
        if (this.purchasePrice == 0) {
          this.$message.error("价格不能为0");
          return;
        } else if (this.purchasePrice == " ") {
          this.$message.error("价格不能为空");
          return;
        } else if (this.dyedate == 0) {
          this.$message.error("到期时间不能为0");
          return;
        } else if (this.dyedate == " ") {
          this.$message.error("到期时间不能为空");
          return;
        } else {
          this.patchGroupScale();
        }
      } else {
        this.patchGroupScale();
      }
    },

    /*
     * 接口请求
     * */
    getSiteInfo() {
      return this.appFetch({ url: "forum", method: "get" });
    },
    getCategories() {
      return this.appFetch({ url: "categories", method: "get" });
    },
    getGroupResource() {
      return this.appFetch({
        url: "groups",
        method: "get",
        splice: "/" + this.groupId,
        data: {
          include: ["permission", "categoryPermissions"]
        }
      })
    },

    patchGroupPermission() {
      let checked = this.checked;
      if (this.is_commission || this.is_subordinate) {
        if (checked.indexOf("other.canInviteUserScale") === -1) {
          checked.push("other.canInviteUserScale");
        }
      } else {
        checked = checked.filter(v => v !== "other.canInviteUserScale");
      }
      // if (checked.includes('canBeAsked') > 0) {
      //   checked = checked.filter(item => !item.includes("canBeAsked.money"));
      //   checked.push(`canBeAsked.money.${this.lowestPrice}`)
      // }
      const param = {
        data: {
          attributes: {
            groupId: this.groupId,
            permissions: checked,
          }
        }
      }
      if (checked.includes('canBeAsked') > 0) {
        param.data.attributes.can_be_asked_money = this.lowestPrice;
      }
      
      this.appFetch({
        url: "groupPermission",
        method: "post",
        data: param
      })
        .then(res => {
          if (res.errors) {
            this.$message.error(res.errors[0].code);
          } else {
            this.$message({
              showClose: true,
              message: "提交成功",
              type: "success"
            });
          }
        })
        .catch(err => {});
    },
    patchGroupScale() {
      this.appFetch({
        url: "groups",
        method: "PATCH",
        splice: "/" + this.groupId,
        data: {
          data: {
            attributes: {
              name: this.$route.query.name,
              // is_paid: this.value ? 1 : 0,
              // fee: this.purchasePrice,
              // days: this.dyedate,
              scale: this.scale,
              is_subordinate: this.is_subordinate,
              is_commission: this.is_commission
            }
          }
        }
      })
        .then(res => {
          if (res.errors) {
            this.$message.error(res.errors[0].code);
          } else {
            this.patchGroupPermission();
          }
        })
        .catch(err => {});
    },

    handlePromotionChange(value) {
      this.is_subordinate = value;
    },
    handleScaleChange(value) {
      this.is_commission = value;
    },
    checkNum() {
      if (!this.scale) {
        return true;
      }
      const reg = /^([0-9](\.\d)?|10)$/;
      if (!reg.test(this.scale)) {
        this.$message({
          message: "提成比例必须是0~10的整数或者一位小数",
          type: "error"
        });
        return false;
      }
      return true;
    },
    // 分类下拉改变
    changeCategory(value, obj) {
      let checked = this.checked;
      const isAll = this.checked.includes(obj);

      // 获取当前选中的权限字符串;全选权限不用加category
      const selectPermission = value.map(item => {
        return item[0] ? `category${item[item.length - 1]}.${obj}` : obj;
      })

      if (isAll) {
        // 取消全选
        this.selectList[obj] = value.filter( v => v[0] !== "");
        selectPermission.shift();
        checked = checked.filter( item => item !== obj);
        checked = [...checked, ...selectPermission];

      } else if(selectPermission.includes(obj)) {
        // 非全选-选中全选
        this.selectList[obj].splice(1);
        checked = checked.filter( item => !selectPermission.includes(item));
        checked.push(obj);
        this.keyValue = Math.random();
      } else {
        // 非全选-选中一二级分类项
        checked = checked.filter( item => {
          return !(item.includes('category') && item.includes(obj));
        });
        checked = [...checked, ...selectPermission];
      }
      this.checked = checked;
    },
    // 清除tag
    clearItem(value, obj) {
      let checked = this.checked;
      const removedPermission = value[0] ? `category${value[value.length - 1]}.${obj}` : obj;
      checked = checked.filter(v => v !== removedPermission);
      this.selectList[obj].shift();
      this.checked = checked;
      this.keyValue = Math.random();
    },
    changeChecked(value, obj) {
      if (value) return;
      const checkedData = this.checked;
      const selectedPermission = this.selectList[obj].map(item => {
        return item[0] ? `category${item[item.length - 1]}.${obj}` : obj;
      })
      this.checked = checkedData.filter(v => !selectedPermission.includes(v));
      this.selectList[obj] = [];
    },
    checkSelect() {

      if (this.checked.indexOf('switch.createThread') !== -1) {
        if(this.selectList.createThread.length === 0){
          this.$message.error("请选择发布主题权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.reply') !== -1) {
        if(this.selectList['thread.reply'].length === 0){
          this.$message.error("请选择回复主题权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.canBeReward') !== -1) {
        if(this.selectList['thread.canBeReward'].length === 0){
          this.$message.error("请选择允许被打赏权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.viewThreads') !== -1) {
        if(this.selectList.viewThreads.length === 0){
          this.$message.error("请选择查看主题列表权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.viewPosts') !== -1) {
        if(this.selectList['thread.viewPosts'].length === 0){
          this.$message.error("请选择查看主题详情权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.freeViewPosts.1') !== -1) {
        if(this.selectList['thread.freeViewPosts.1'].length === 0){
          this.$message.error("请选择免费查看付费帖子权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.freeViewPosts.2') !== -1) {
        if(this.selectList['thread.freeViewPosts.2'].length === 0){
          this.$message.error("请选择免费查看付费视频权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.freeViewPosts.3') !== -1) {
        if(this.selectList['thread.freeViewPosts.3'].length === 0){
          this.$message.error("请选择免费查看付费图片权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.freeViewPosts.4') !== -1) {
        if(this.selectList['thread.freeViewPosts.4'].length === 0){
          this.$message.error("请选择免费查看付费语音权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.freeViewPosts.5') !== -1) {
        if(this.selectList['thread.freeViewPosts.5'].length === 0){
          this.$message.error("请选择免费查看付费问答权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.essence') !== -1) {
        if(this.selectList['thread.essence'].length === 0){
          this.$message.error("请选择加精权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.edit') !== -1) {
        if(this.selectList['thread.edit'].length === 0){
          this.$message.error("请选择编辑主题权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.hide') !== -1) {
        if(this.selectList['thread.hide'].length === 0){
          this.$message.error("请选择删除主题权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.editPosts') !== -1) {
        if(this.selectList['thread.editPosts'].length === 0){
          this.$message.error("请选择编辑回复权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.hidePosts') !== -1) {
        if(this.selectList['thread.hidePosts'].length === 0){
          this.$message.error("请选择删除回复权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.editOwnThreadOrPost') !== -1) {
        if(this.selectList['thread.editOwnThreadOrPost'].length === 0){
          this.$message.error("请选择编辑自己的主题或回复权限");
          return false;
        }
      }
      if (this.checked.indexOf('switch.thread.hideOwnThreadOrPost') !== -1) {
        if(this.selectList['thread.hideOwnThreadOrPost'].length === 0){
          this.$message.error("请选择删除自己的主题或回复权限");
          return false;
        }
      }
      // if (this.checked.indexOf('canBeAsked') !== -1 && this.lowestPrice === '') {
      //   this.$message.error('允许被提问的最低金额未填写');
      //   return false;
      // }
      return true;
    },

    // 发帖权限切换扩展项状态
    changeExpandItem(val) {
      if (this.selectList[val.slice(0, 14)].includes(val)) {

        if (val.includes('position') || val.includes('anonymous')) {
          // 位置权限直接添加
          this.checked.push(val);
        } else if (!this.checked.includes(val)) {
          // 红包权限选择性添加
          const str = `
            <p style="text-indent:2em;">开启红包功能，存在被多个马甲刷回复领取红包的风险</p>
            <p style="text-indent:2em;margin-top:10px;">
              建议在
              <span style="color:red;">用户 - 用户角色 - 设置 - 安全设置</span>
              中开启以下权限：
            </p>
            <p style="padding-left:32PX;font-weight:bold;">
              · 发布内容需先实名认证。<br>
              · 发布内容需先绑定手机。
            </p>
          `;
          this.$confirm(str, '提示', {
            dangerouslyUseHTMLString: true,
            confirmButtonText: '确定',
            cancelButtonText: '取消',
            type: 'warning'
          }).then(() => {
            this.checked.push(val);
          }).catch(() => {
            this.selectList[val.slice(0, 14)] =
              this.selectList[val.slice(0, 14)].filter(item => item !== val);
          });
        }
      } else {
        this.checked = this.checked.filter(item => item !== val);
      }
    },
    // 发帖权限切换选中状态
    changePostChecked(value, obj) {
      if (value) return;
      const checkedData = this.checked;
      this.checked = checkedData.filter(v => !this.selectList[obj].includes(v));
      this.selectList[obj] = [];
    },

    // 全选切换
    handleCheckAllChange(val) {
      this.checked = [];
      this.selectList = {
        "createThread.0":[], //发布文字帖扩展项
        "createThread.1":[], //发布帖子扩展项
        "createThread.2":[], //发布视频帖扩展项
        "createThread.3":[], //发布图片帖扩展项
        "createThread.4":[], //发布语音帖扩展项
        "createThread.5":[], //发布问答扩展项
        "createThread.6":[], //发布商品帖扩展项
        'viewThreads': [],
        'createThread':[], // 发布主题扩展项
        'thread.reply':[], // 回复主题扩展项
        'thread.canBeReward': [], //打赏扩展
        'thread.edit':[],
        'thread.hide':[],
        'thread.essence':[],
        'thread.viewPosts':[],
        'thread.editPosts':[],
        'thread.hidePosts':[],
        'thread.editOwnThreadOrPost': [],
        'thread.hideOwnThreadOrPost': [],
        'thread.freeViewPosts.1':[],
        'thread.freeViewPosts.2':[],
        'thread.freeViewPosts.3':[],
        'thread.freeViewPosts.4':[],
        'thread.freeViewPosts.5':[]
      };
      if (val) {
        // 1 主权限全选
        this.checkAllPermission.forEach(item => {
          this.checked.push(item);
        })
        // 2 红包权限全选
        for(let i=0;i<2;i++){
          this.checked.push(`createThread.${i}.redPacket`)
        }
        // 3 位置权限全选
        for(let i=0;i<7;i++){
          this.checked.push(`createThread.${i}.position`)
        }
        // 4 匿名权限全选
        for(let i=0;i<7;i++){
          this.checked.push(`createThread.${i}.anonymous`)
        }
        // 5 分类扩展全选
        this.checked.push(...this.expandItem)

        this.checkAll = true;
        this.setSelectValue(this.checked);
      } else {
        this.checkAll = false;
      }
    },
  },
  created() {
    this.groupId = this.$route.query.id;
    this.activeTab.title = this.$route.query.title || "操作权限";
    this.activeTab.name = this.$route.query.names || "userOperate";
    this.getData();
    if (this.groupId === '7') {
      // 游客权限
      this.checkAllPermission = [
        "switch.viewThreads", //查看主题列表
        "switch.thread.viewPosts", //查看主题详情
        "switch.thread.freeViewPosts.1", //免费查看付费帖子
        "switch.thread.freeViewPosts.2", //免费查看付费视频
        "switch.thread.freeViewPosts.3", //免费查看付费图片
        "switch.thread.freeViewPosts.4", //免费查看付费语音
        "switch.thread.freeViewPosts.5", //免费查看付费问答
      ];
    } else {
      this.checkAllPermission = [
        "createThread.0", //发布文字帖
        "createThread.1", //发布帖子
        "createThread.2", //发布视频帖
        "createThread.3", //发布图片帖
        "createThread.4", //发布语音帖
        "createThread.5", //发布问答
        "createThread.6", //发布商品帖
        "dialog.create", //发布私信
        "canBeAsked", //允许被提问
        "canBeOnlooker", //设置围观
        "attachment.create.0", //上传附件
        "attachment.create.1", //上传图片
        "createThreadPaid", //发布付费内容
        "switch.createThread", //发布主题
        "switch.thread.reply", //回复主题
        "switch.thread.canBeReward", //允许被打赏
        "switch.viewThreads", //查看主题列表
        "switch.thread.viewPosts", //查看主题详情
        "switch.thread.freeViewPosts.1", //免费查看付费帖子
        "switch.thread.freeViewPosts.2", //免费查看付费视频
        "switch.thread.freeViewPosts.3", //免费查看付费图片
        "switch.thread.freeViewPosts.4", //免费查看付费语音
        "switch.thread.freeViewPosts.5", //免费查看付费问答
        "thread.sticky", //置顶
        "createInvite", //邀请加入
        "user.edit.group", //编辑用户组
        "user.edit.status", //编辑用户状态
        "switch.thread.essence", //加精
        "switch.thread.edit", //编辑主题
        "switch.thread.hide", //删除主题
        "switch.thread.editPosts", //编辑回复
        "switch.thread.hidePosts", //删除回复
        "switch.thread.editOwnThreadOrPost", //编辑自己的主题或回复
        "switch.thread.hideOwnThreadOrPost", //删除自己的主题或回复
      ];
    }
  },
  components: {
    Card,
    CardRow
  }
};
