import Card from "../../../../view/site/common/card/card";
import CardRow from "../../../../view/site/common/card/cardRow";

export default {
  data(){
    return {
      // purchase:false, // 购买权限
      reward: false, // 打赏功能
      // 发布功能
      publishing:{
        text:true,
        post:true,
        picture:true,
        video:true,
        voice:true,
        goods:true,
        question:false
      }
    }
  },
  methods:{
    // 加载功能权限
    loadFunctionStatus() {
      this.appFetch({
        url: "forum",
        method: "get",
        data: {}
      })
        .then(data => {
          if (data.errors) {
            this.$message.error(data.errors[0].code);
          } else {
            // 购买权限
            // this.purchase = data.readdata._data.set_site.site_pay_group_close === '1';
            // 打赏权限
            this.reward = data.readdata._data.set_site.site_can_reward === 1;

            // 发布功能
            this.publishing.text = data.readdata._data.set_site.site_create_thread0===1;
            this.publishing.post = data.readdata._data.set_site.site_create_thread1===1;
            this.publishing.video = data.readdata._data.set_site.site_create_thread2===1;
            this.publishing.picture = data.readdata._data.set_site.site_create_thread3===1;
            this.publishing.voice = data.readdata._data.set_site.site_create_thread4===1;
            this.publishing.question = data.readdata._data.set_site.site_create_thread5===1;
            this.publishing.goods = data.readdata._data.set_site.site_create_thread6===1;
          }
        })
        .catch(error => {});
    },
    // 为问答添加风险提示
    handleQuestionChange(val) {
      if (val) {
        const str = `
          <p style="text-indent:2em;">开启问答功能，存在被多个马甲刷回复领取剩余悬赏金额的风险。</p>
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
        }).catch(() => {
          this.publishing.question = false;      
        });
      }
    },
    // 提交功能状态更改
    handlePublishingSubmit(){
      this.appFetch({
        url: "settings",
        method: "post",
        data: {
          data: [
            {
              attributes: {
                key: "site_can_reward",
                value: this.reward ? 1 : 0,
                tag: "default"
              }
            },
            // {
            //   attributes: {
            //     key: "site_pay_group_close",
            //     value: this.purchase,
            //     tag: "default"
            //   }
            // },
            {
              attributes: {
                key: "site_create_thread0",
                value: this.publishing.text ?1:0,
                tag: "default"
              }
            },
            {
              attributes: {
                key: "site_create_thread1",
                value: this.publishing.post?1:0,
                tag: "default"
              }
            },
            {
              attributes: {
                key: "site_create_thread2",
                value: this.publishing.video?1:0,
                tag: "default"
              }
            },
            {
              attributes: {
                key: "site_create_thread3",
                value: this.publishing.picture?1:0,
                tag: "default"
              }
            },
            {
              attributes: {
                key: "site_create_thread4",
                value: this.publishing.voice?1:0,
                tag: "default"
              }
            },
            {
              attributes: {
                key: "site_create_thread5",
                value: this.publishing.question?1:0,
                tag: "default"
              }
            },
            {
              attributes: {
                key: "site_create_thread6",
                value: this.publishing.goods?1:0,
                tag: "default"
              }
            }
          ]
        }
      })
        .then(data => {
          if (data.errors) {
            if (data.errors[0].detail) {
              this.$message.error(
                data.errors[0].code + "\n" + data.errors[0].detail[0]
              );
            } else {
              this.$message.error(data.errors[0].code);
            }
          } else {
            this.$message({
              message: "提交成功",
              type: "success"
            });
          }
        })
        .catch(error => {});
    },
  },
  created(){
    this.loadFunctionStatus()
  },
  components:{
    Card,
    CardRow
  }
}