/*
 * 系统通知管理器
 * */

import Card from "../../../../view/site/common/card/card";
import CardRow from "../../../../view/site/common/card/cardRow";
import Page from "../../../../view/site/common/page/page";

export default {
  data: function() {
    return {
      tableData: [],
      pageNum: 1,
      pageLimit: 20,
      total: 0,
      type: [],
      // pageCount: 0
    };
  },
  created() {
    this.getNoticeList();
  },
  methods: {
    getNoticeList() {
      //初始化通知设置列表
      this.appFetch({
        url: "noticeList",
        method: "get",
        data: {
          "page[number]": this.pageNum,
          "page[size]": this.pageLimit
        }
      })
        .then(res => {
          if (res.errors) {
            this.$message.error(res.errors[0].code);
          } else {
            this.tableData = res.readdata;
            this.total = res.meta.total;
          }
        })
        .catch(err => {});
    },
    noticeSetting(id, actionName) {
      //修改开启状态
      let statusTemp = 1; // 默认开启状态
      if (actionName == "close") {
        statusTemp = 0;
      } else if (actionName == "open") {
        statusTemp = 1;
      }
      this.appFetch({
        url: "notification",
        method: "patch",
        splice: id,
        data: {
          data: {
            attributes: {
              status: statusTemp
            }
          }
        }
      }).then(res => {
        if (res.errors) {
          this.$message.error(res.errors[0].code);
        } else {
          this.$message({
            message: "修改成功",
            type: "success"
          });
          this.getNoticeList();
        }
      });
    },

    //获取表格序号
    getIndex($index) {
      //表格序号
      return (this.pageNum - 1) * this.pageLimit + $index + 1;
    },
    handleCurrentChange(val) {
      this.pageNum = val;
      this.getNoticeList();
    },
    configClick(id,typeName) {
      //点击配置跳到对应的配置页面
      this.$router.push({
        path: "/admin/notice-configure",
        query: { id: id,typeName: typeName }
      });
    },
    // 通知类型的点击事件
    handleError(item) {
      if (item.is_error === 1) {
        let json = item.error_msg;

        this.$alert(`
          <div class="notice_error_info">
            <div class="notice_error_title">Code</div>
            <div class="notice_error_message">${json.err_code}</div>
          </div>
          <div class="notice_error_info">
            <div class="notice_error_title">Message</div>
            <div class="notice_error_message">${json.err_msg}</div>
          </div>`,
          `${json.type_name}（${item.type}）`, {
          dangerouslyUseHTMLString: true,
        }).catch(() => {
          console.log('点击了关闭')
        })
      }
    }
  },

  components: {
    Card,
    CardRow,
    Page
  }
};
