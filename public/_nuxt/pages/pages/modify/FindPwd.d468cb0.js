(window.webpackJsonp=window.webpackJsonp||[]).push([[10,28],{67:function(t,c,o){o(9);var n=o(285);t.exports={mixins:[n],computed:{forums:function(){return this.$store.state.site.info.attributes||{}}},methods:{checkCaptcha:function(t){var c=this;return new Promise((function(o,n){if(c.forums&&c.forums.qcloud&&c.forums.qcloud.qcloud_captcha)return new TencentCaptcha(c.forums.qcloud.qcloud_captcha_app_id,(function(c){0===c.ret?(t.captcha_rand_str=c.randstr,t.captcha_ticket=c.ticket,o(t)):n(c)})).show();o(t)}))}}}}}]);