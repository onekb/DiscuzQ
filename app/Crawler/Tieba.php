<?php

namespace App\Crawler;

class Tieba
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
        return $this->getListWap($topic, $page);
    }

    /**
     * @method  从话题搜索数据，获取到帖子数据[从WAP端获取数据]
     * @param string $topic 话题
     * @param int $page 分页
     * @return array
     */
    private function getListWap($topic, $page = 1)
    {
        $data = [];
        $num = 30;
        $page = ($page - 1) * $num;
        $url = "https://tieba.baidu.com/mo/q/m?word={$topic}&page_from_search=index&tn6=bdISP&tn4=bdKSW&tn7=bdPSB&lm=16842752&lp=6093&sub4=进吧&pn={$page}&";
        $html = $this->curlGet($url);
        if (empty($html)) {
            return $data;
        }
        $forumList = explode('<div class="ti_infos clearfix" data-tid="">', $html);
        if (!isset($forumList[1])) {
            return $data;
        }
        unset($forumList[0]); //删除头部信息
        if ($forumList) {
            $data = [];
            //遍历
            foreach ($forumList as $k => $v) {
                $content = explode('<span class="ti_time">', $v);
                //用户信息
                $user = $this->getUserWap($content[0]);
                //如果用户信息不能抓取,跳过
                if (empty($user['nickname'])) {
                    continue;
                }
                //帖子信息
                $forum = $this->getForumDetailWap('<span class="ti_time">' . $content[1]);
                //如果帖子信息不能抓取，跳过
                if (empty($forum['id'])) {
                    continue;
                }
                $data[$k]['user'] = $user;
                $data[$k]['forum'] = $forum;
                //增加nickname
                $data[$k]['forum']['nickname'] = $user['nickname'];
                //评论信息
                $data[$k]['comment'] = $this->getCommentList($forum['id']);
            }
        }
        return $data;
    }

    /**
     * @method  获取用户信息, 从html页面中提取用户数据[从WAP端获取数据]
     * @param string $content 内容
     * @return array
     */
    private function getUserWap($content)
    {
        //返回参数
        $userInfo = [
            'avatar' => '',
            'nickname' => '',
            'gender' => '',
            'home_page' => '',
            'description' => '',
        ];
        if ($content) {
            //头像
            $avatar = $this->dealMatchStr("/<img src=\"(.*)\" alt=\"\">/i", $content);
            $userInfo['avatar'] = trim($avatar);
            if (!empty($avatar)) {
                $userId = substr($avatar, strpos($avatar, '/item/'));
                $userId = str_replace('/item/', '', $userId);
            }

            //昵称
            preg_match_all("/<span class=\"ti_author(.*)<\/span> /i", $content, $nicknameMatches);
            if (isset($nicknameMatches[0][0]) && !empty($nicknameMatches[0][0])) {
                $userInfo['nickname'] = trim(strip_tags($nicknameMatches[0][0]));
            }

            //主页
            $userInfo['home_page'] = 'https://tieba.baidu.com/home/main?un=' . $userInfo['nickname'];

            //性别
//            if(isset($userId)) {
//                $user = $this->getUserInfo($userId);
//                $userInfo['nickname'] = $user['nickname'];
//                $userInfo['gender'] = $user['gender'];
//            }
        }
        return $userInfo;
    }

    /**
     * @method  获取帖子基本信息
     * @param string $content 内容
     * @return array
     */
    private function getForumDetailWap($content)
    {
        //返回参数
        $forumData = [
            'id' => '',
            'mid' => '',
            'text' => [
                'text' => '',
                'position' => '',
                'topic_list' => []
            ],
            'create_at' => '',
            'pics' => [
                'small_pics' => [],
                'large_pics' => []
            ],
            'medias' => [
                'small_medias' => [],
                'large_medias' => [],
            ]
        ];
        if ($content) {
            //id
            $id = $this->dealMatchStr("/<a href=\"\/p\/(.*)\?lp=/i", $content);
            $forumData['id'] = trim($id);
            $forumData['mid'] = trim($id);

            //帖子内容
            $text = $this->dealMatchStr("/<div class=\"ti_title\">(.*)<\/span>(?)<\/div> *<div/i", $content);
            $forumData['text']['text'] = trim(strip_tags($text, '<p><br><img>'));

            //发帖时间
            $createAt = $this->dealMatchStr("/<span class=\"ti_time\">(.*)<\/span><\/div> *<\/div><a/i", $content);
            $createAt = trim($createAt);
            //判断是否为当天时间
            if (strpos($createAt, ':') != false) {
                $createAt = date('Y-m-d ') . $createAt;
            }
            $forumData['create_at'] = $createAt;

            //图片
            //判断是否包含图片
            $localtion = strpos($content, 'medias_wrap ordinary_thread clearfix');
            if ($localtion != false) {
                $picsHtml = substr($content, $localtion);
                preg_match_all("/[img|IMG].*?url=['|\"](.*?(?:[.gif|.jpg]))['|\"].*?[\/]?>/", $picsHtml, $pics);
                foreach ($pics[1] as $picKey => $picValue) {
                    if (strpos($picValue, '.jpg') == false && strpos($picValue, '.gif') == false) {
                        unset($pics[1][$picKey]);
                    }
                }
                $forumData['pics']['small_pics'] = $pics[1];
            }
        }
        return $forumData;
    }

    /**
     * @method  根据mid获取评论信息
     * @param string $mid mid
     * @return array
     */
    private function getCommentList($mid)
    {
        $data = [];
        $url = "https://tieba.baidu.com/p/{$mid}";
        $html = $this->curlGet($url);
        if (empty($html)) {
            return $data;
        }

        $forum = explode('<ul class="p_author">', $html);
        unset($forum[0]); //删除头部信息
        foreach ($forum as $key => $value) {
            //将评论信息和用户信息切分
            $content = explode('<li class="d_nameplate">', $value);
            //用户信息
            $user = $this->getUser($content[0]);
            //如果用户昵称为空，抛弃
            if (empty($user['nickname'])) {
                continue;
            }

            //评论信息
            $commentDetail = $this->getComment($content[1]);
            //如果评论ID为空，抛弃
            if (empty($commentDetail['id']) || empty($commentDetail['text']['text'])) {
                continue;
            }
            //用户信息
            $data[$key]['user'] = $user;
            //评论信息
            $data[$key]['comment'] = $commentDetail;
            //增加帖子ID，和用户昵称
            $data[$key]['comment']['forumId'] = $mid;
            $data[$key]['comment']['nickname'] = $user['nickname'];
        }
        return $data;
    }

    /**
     * @method  获取用户信息, 从html页面中提取用户数据
     * @param string $content 内容
     * @return array
     */
    private function getUser($content)
    {
        //返回数据
        $userInfo = [
            'avatar' => '',
            'nickname' => '',
            'gender' => '',
            'home_page' => '',
            'description' => '',
        ];
        if ($content) {
            //头像
            $avatar = $this->dealMatchStr("/\" class=\"\" src=\"(.*)\"\/>/i", $content);
            //判断是否有头像
            if (strpos($avatar, 'https://')) {
                $avatar = substr($avatar, strpos($avatar, 'https://'));
            }
            $userInfo['avatar'] = trim($avatar);

            //昵称
            $nickname = $this->dealMatchStr("/img username=\"(.*)\" class=\"\"/i", $content);
            $userInfo['nickname'] = trim($nickname);

            //主页
            $homePage = $this->dealMatchStr("/class=\"p_author_face \" href=\"(.*)\">/i", $content);
            $userInfo['home_page'] = 'https://tieba.baidu.com' . trim($homePage);
            if ($homePage) {
                $userId = substr($homePage, strpos($homePage, 'id='));
            }

            //性别
//            if(isset($userId)) {
//                $user = $this->getUserInfo($userId);
//                $userInfo['nickname'] = $user['nickname'];
//                $userInfo['gender'] = $user['gender'];
//            }

        }
        return $userInfo;
    }

    /**
     * @method  获取评论信息
     * @param string $content 内容
     * @return array
     */
    private function getComment($content)
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
        //评论ID
        $commentInfo['id'] = $this->dealMatchStr("/<div id=\"post_content_(.*)\" class=\"d_post_content j_d_post_content/i", $content);

        //评论内容
        $commentText = $this->dealMatchStr("/class=\"d_post_content j_d_post_content \" style=\"display:;\">(.*)<\/div><br>/i", $content);
        $commentInfo['text']['text'] = trim(strip_tags($commentText, '<p><br><img>'));

        //评论时间
        $commentInfo['created_at'] = $this->dealMatchStr("/楼<\/span><span class=\"tail-info\">(.*)<\/span><\/div><ul class=\"p_props_tail props_appraise_wrap/i", $content);

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
     * @return string  采集内容
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
        curl_setopt($ch, CURLOPT_COOKIE, $this->cookie);
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
