export default {
  data() {
    return {
      selectedTheme: 1, // 选中主题 1蓝色 2红色
      newTheme: 1, // 当前绑定主题值
      currentUrl: '', // 预览图地址
      isPreview: false,
      dialogVisible: false,
      Loading: ''
    }
  },
  mounted() {
    this.getThemeSelect();
  },
  methods: {
    showPreview(e) {
      this.currentUrl = e.target.getAttribute('src');
      this.isPreview = true;
    },
    closePreview() {
      this.isPreview = false;
      this.currentUrl = '';
    },

    // 请求
    getThemeSelect() {
      this.appFetch({
        url: 'forum',
        methods: 'get'
      }).then(res => {
        if (res.errors) {
          this.$message.error(res.errors[0].code);
        } else {
          this.selectedTheme = res.data.attributes.set_site.site_skin;
          this.newTheme = res.data.attributes.set_site.site_skin;
        }
      }).catch(error => {
      });
    },
    submitThemeSelect() {
      let str = '';
      if (this.selectedTheme === 1 && this.newTheme === 2) {
        str = `
          <p style="text-indent:2em;">您确定要切换至红色主题吗？</p>
          <p style="text-indent:2em;;margin-top:10px;">
            <span style="color:red">温馨小提示：</span>
            小程序请参考安装手册，重新获取、提交红色三栏的源码。
          </p>
        `;
      } else if (this.selectedTheme === 2 && this.newTheme === 1) {
        str = `
          <p style="text-indent:2em;">您确定要切换至蓝色主题吗？</P>
          <p style="text-indent:2em;margin-top:10px;">
            <span style="color:red;">温馨小提示：</span>
            小程序请参考安装手册，重新获取、提交蓝色两栏的源码。
          </p>
        `;
      } else {
        str = '相同的提交可能不生效。'
      }
      this.$confirm(str, '确认信息', {
        dangerouslyUseHTMLString: true,
        confirmButtonText: '确认',
        cancelButtonText: '取消',
        type: 'success'
      })
        .then(() => {
          this.postThemeSelect();
        })
        .catch(action => {
          this.$message({
            type: 'info',
            message: '取消成功'
          })
        });
    },
    postThemeSelect() {
      this.loading = this.$loading({
        lock: true,
        text: '正在切换...',
        spinner: 'el-icon-loading',
        background: 'rgba(0, 0, 0, 0.1)'
      });
      this.appFetch({
        url: 'switchskin',
        method: 'post',
        data: {
          data: {
            attributes: {
              skin: this.newTheme
            }
          }
        }
      })
      .then(res => {
        this.handleResult(res);
      })
      .catch(err => {
        this.loading.close();
      })
    },
    handleResult(res) {
      this.loading.close();
      if (res.errors && res.errors[0].status === '500') {
        return this.$message.warning(res.rawData[0].code);
      }
      if (res.data && res.data.attributes.code === 200){
        this.$message.success(res.data.attributes.message);
        this.selectedTheme = res.data.attributes.site_skin;
        this.newTheme = res.data.attributes.site_skin;
      } else {
        this.$confirm(res, '提示信息', {
          dangerouslyUseHTMLString: true,
          confirmButtonText: '重试',
          cancelButtonText: '取消',
          type: 'info'
        })
          .then(() => {
            this.postThemeSelect();
          })
          .catch(action => {
            this.$message({
              type: 'info',
              message: '取消成功'
            })
          });
      }
    }
  }
}