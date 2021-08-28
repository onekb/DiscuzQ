<?php

namespace App\Crawler;

class Douban
{
    private $cookie = '';

    /**
     * @method  主入口
     * @param string $topic 话题
     * @param int $page 获取第几页数据
     * @param string $cookie 用户cookie
     * @return array
     */
    public function main($topic, $page = 1, $cookie = '')
    {
        set_time_limit(0);
        $this->cookie = $cookie;
        return $this->getList($topic, $page);
    }


    /**
     * @method  从话题搜索数据，获取到帖子列表
     * @param string $topic 话题
     * @param int $page 分页
     * @return array
     */
    private function getList($topic, $page = 1)
    {
        $data = [];
        $num = 50;
        $page = ($page - 1) * $num;
        $url = "https://www.douban.com/group/search?start={$page}&cat=1013&q={$topic}&sort=relevance";
        $html = $this->curlGet($url);
        if (empty($html)) {
            return $data;
        }
        $forumList = explode('<tr class="pl">', $html);
        if (!isset($forumList[1])) {
            return $data;
        }
        unset($forumList[0]); //删除头部信息
        if ($forumList) {
            $data = [];
            //遍历
            foreach ($forumList as $k => $v) {
                //帖子链接
                $info = $this->dealMatchStr("/<td class=\"td-subject\">(.*)<\/td>/i", $v);
                //链接
                $link = $this->dealMatchStr('/<a[^>]+href=["\'](.*?)["\']/i', $info);
                if (empty($link)) {
                    continue;
                }
                //提取帖子ID
                $id = intval(substr($link, strpos($link, 'topic/') + 6));
                //标题
                $title = $this->dealMatchStr('/<a[^>]+title=["\'](.*?)["\']/i', $info);
                //发帖时间
                $create_at = $this->dealMatchStr('/<td class="td-time" title="(.*)" nowrap="nowrap">/i', $v);

                //获取帖子基本信息及评论信息
                $result = $this->getForumDetail($link, $id);
                if (isset($result['code'])) {
                    continue;
                }
                if (empty($result['user']['nickname'])) {
                    continue;
                }
                //组装数据
                //发帖人信息
                $data[$k]['user'] = $result['user'];
                //帖子信息
                $data[$k]['forum'] = [
                    'id' => $id,
                    'mid' => $id,
                    'nickname' => $result['user']['nickname'],
                    'text' => [
                        'text' => $title . $result['forum'],
                        'position' => '',
                        'topic_list' => '',
                    ],
                    'create_at' => $create_at,
                    'pics' => [
                        'small_pics' => '',
                        'large_pics' => '',
                    ],
                    'medias' => [
                        'small_medias' => '',
                        'large_medias' => '',
                    ],
                    'link' => $link
                ];
                //评论信息
                $data[$k]['comment'] = $result['comment'];
            }
        }
        return $data;
    }

    /**
     * @method  获取帖子详情及评论
     * @param string $url 链接
     * @param string $mid 帖子ID
     * @return array
     */
    private function getForumDetail($url, $mid)
    {
        $data = ['code' => 0, 'msg' => 'no data！'];
        if (empty($url)) {
            return $data;
        }
        $html = $this->curlGet($url);
        if (empty($html)) {
            return $data;
        }
        $data = [];
        //切分
        $content = explode('<li class="clearfix comment-item reply-item "', $html);
        //提取帖子内容
        $forumInfo = $this->getForumInfo($content[0]);
        $data['user'] = $forumInfo['user'];
        $data['forum'] = $forumInfo['forum'];

        //提取帖子评论信息
        unset($content[0]);
        if (empty($content)) {
            $comment = [];
        } else {
            $comment = $this->getCommentList($content, $mid);
        }
        $data['comment'] = $comment;

        return $data;
    }

    /**
     * @method  获取发帖人信息和帖子内容
     * @param string $content 内容
     * @return array
     */
    private function getForumInfo($content)
    {
        //定义返回参数
        $forumInfo = [
            'user' => [
                'avatar' => '',
                'nickname' => '',
                'gender' => '',
                'home_page' => '',
                'description' => '',
            ],
            'forum' => '',
        ];
        if (empty($content)) {
            return $forumInfo;
        }
        $content = substr($content, strpos($content, '<div class="topic-content clearfix" id="topic-content">'));
        //发帖人信息
        $user = $this->getUser($content);
        $forumInfo['user'] = $user;

        //帖子内容信息
        $forumInfo['forum'] = strip_tags(trim($this->dealMatchStr("/<div class=\"rich-content topic-richtext\">\s(.*?)\s<\/div>/ism", $content)), '<p><br><img>');

        return $forumInfo;
    }


    /**
     * @method  获取用户信息
     * @param string $userInfo 内容
     * @return array
     */
    private function getUser($userInfo)
    {
        $info = $this->dealMatchStr("/<div class=\"user-face\">\s(.*?)\s<\/div>/ism", $userInfo);
        //用户头像
        $user['avatar'] = $this->dealMatchStr("/[img|IMG].*?src=['|\"](.*?(?:[.gif|.jpg]))['|\"].*?[\/]?>/", $info);

        //用户昵称
        $user['nickname'] = $this->dealMatchStr("/[img|IMG].*?alt=['|\"](.*?)['|\"].*?[\/]?>/", $info);

        //用户主页
        $user['home_page'] = $this->dealMatchStr('/<a[^>]+href=["\'](.*?)["\']/i', $info);

        //描述
        $user['description'] = '';
        return $user;
    }

    /**
     * @method  从话题搜索数据，获取到帖子列表
     * @param string $content 内容
     * @param string $mid 帖子ID
     * @return array
     */
    private function getCommentList($content, $mid)
    {
        $comment = [];
        if (empty($content)) {
            return $comment;
        }
        foreach ($content as $commentKey => $commentValue) {
            //判断是否为回复评论，直接跳过
            if (strpos($commentValue, 'reply-quote-content') != false) {
                continue;
            }
            //评论用户信息
            $user = $this->getUser($commentValue);
            //评论信息
            $commentDetail = $this->getComment($commentValue);
            if (empty($user['nickname']) || empty($commentDetail['text']['text'])) {
                continue;
            }
            $comment[$commentKey]['user'] = $user;
            $comment[$commentKey]['comment'] = $commentDetail;
            //增加评论人昵称
            $comment[$commentKey]['comment']['forumId'] = $mid;
            $comment[$commentKey]['comment']['nickname'] = $user['nickname'];
        }

        return $comment;
    }

    /**
     * @method  从话题搜索数据，获取到单个帖子信息
     * @param string $comment 内容
     * @return array
     */
    private function getComment($comment)
    {
        $commentInfo = [
            'id' => '',
            'rootid' => '',
            'created_at' => '',
            'text' => [
                'text' => '',
                'position' => '',
                'topic_list' => [],
            ],
        ];
        if (empty($comment)) {
            return $commentInfo;
        }

        //id
        $commentInfo['id'] = $this->dealMatchStr("/data-cid=\"(.*)\" >/i", $comment);
        $commentInfo['rootid'] = $commentInfo['id'];
        //评论时间
        $commentInfo['created_at'] = $this->dealMatchStr("/<span class=\"pubtime\">(.*)<\/span>/i", $comment);
        //评论内容
        $text = $this->dealMatchStr("/<p class=\" reply-content\">(.*)<\/p>/i", $comment);
        $commentInfo['text']['text'] = $text;

        return $commentInfo;
    }


    /**
     * @method  处理单个正则匹配，返回结果
     * @param string $match 正则
     * @param string $content 内容
     * @return string
     */
    private function dealMatchStr($match, $content)
    {
        $result = '';
        if ($content) {
            //评论ID
            preg_match_all($match, $content, $matches);
            if (isset($matches[1][0]) && !empty($matches[1][0])) {
                $result = $matches[1][0];
            }
        }
        return $result;
    }

    /**
     * @method  curl-get请求
     * @param string $url 请求地址D
     * @param array $headers 请求头信息
     * @param int $port 端口号
     * @return string $filecontent  采集内容
     */
    private function curlGet($url, $headers = [], $port = 80)
    {
        $ch = curl_init();
        $header = array();
        $header[] = 'Content-Type:application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($port !== 80) {
            curl_setopt($ch, CURLOPT_PORT, $port);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/91.0.4472.114 Safari/537.36");
        curl_setopt($ch, CURLOPT_HEADER, 0);//设定是否输出页面内容
        if ($headers) {
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if ($this->cookie) {
            curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
        }
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 3);       //链接超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 3);       //设置超时时间
        $filecontent = curl_exec($ch);
        curl_close($ch);

        return $filecontent;
    }

}
