# Discuz-update

Discuz 更新脚本

下载脚本文件到 /path/to/discuz/app/Console/Commands/Upgrades 目录后执行

> cd /path/to/discuz && 命令名称

## 脚本列表

| 命令名称                                | 版本  | 描述                    |
| :------------------------------------- | :----| :-------------------   |
|  php disco upgrade:category-permission |      | 初始化分类权限           |
|  php disco upgrade:videoSize           |      | 初始化转码成功的视频宽高、时长      |
|  php disco upgrade:noticeAdd {--i|init} |      | 初始化/新增通知类型数据格式      |
|  php disco upgrade:avatar              |      | 更新用户头像信息            |
|  php disco upgrade:ordersExpiredAt     |      | 初始化付费用户注册订单过期时间            |
|  php disco upgrade:postContent         |      | 初始化帖子内容，把原内容转为块编辑器的json数据。需要在迁移之前执行。    |
|  php disco upgrade:split-permissions   |      | 分类权限拆分，处理历史权限。  |
|  php disco upgrade:noticeIteration     |      | 更新迭代通知数据  |
