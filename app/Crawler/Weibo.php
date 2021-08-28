<?php

namespace App\Crawler;

class Weibo
{

    /**
     * @method  主入口
     * @param sting $topic 话题
     * @param int $page  获取第几页
     * @return array
     */
    public function main($topic,$page=1)
    {
        set_time_limit(0);
        $res = $this->getList($topic, $page);
        foreach ($res as $key => $value) {
            //爬取评论
            $comment = $this->getComment($value['forum']['id']);
            $res[$key]['comment'] = $comment;
        }
        return $res;
    }


    /**
     * @method  从话题搜索数据，获取到帖子数据
     * @param string $topic 话题
     * @param int $page 话题
     * @return array
     */
    private function getList($topic, $page)
    {
        //获取话题搜索页面结果
        $url = "https://m.weibo.cn/api/container/getIndex?containerid=100103type=1%3D1%26q%3D{$topic}&page_type=searchall&page={$page}";
        $html = $this->curlGet($url);
        $html = json_decode($html, true);
        $data = [];
        if (isset($html['data']['cards']) && !empty($html['data']['cards'])) {
            foreach ($html['data']['cards'] as $key => $value) {
                if (!isset($value['mblog'])) {
                    continue;
                }
                //用户信息
                $forum['user'] = [
                    'avatar' => $value['mblog']['user']['avatar_hd'],//头像
                    'nickname' => $value['mblog']['user']['screen_name'],//昵称
                    'gender' => $value['mblog']['user']['gender'],//性别
                    'home_page' => $value['mblog']['user']['profile_url'],//个人主页
                    'description' => $value['mblog']['user']['description'],//描述
//                    'followers_count' => $value['mblog']['user']['followers_count'],//粉丝
//                    'follow_count' => $value['mblog']['user']['follow_count'],//关注
                ];
                //帖子数据
                $text = $this->dealText($value['mblog']['text']);
                //帖子内容不全的情况下
                if(empty($text['text'])){
                    $text = $this->getForumTextDetail($value['mblog']['mid']);
                }
                //处理转发的情况
                if(isset($value['mblog']['retweeted_status']['text']) && !empty($value['mblog']['retweeted_status']['text'])){
                    $text['text'] = $text['text'] . $value['mblog']['retweeted_status']['text'];
                }
                $forum['forum'] = [
                    'id' => $value['mblog']['id'],
                    'mid' => $value['mblog']['mid'],
                    'nickname' => $value['mblog']['user']['screen_name'],
                    'text' => $text,
                    'create_at' => date('Y-m-d H:i:s', strtotime($value['mblog']['created_at'])),//发贴时间
                ];

                //图片和视频只有一种
                //判断是否帖子中包含图片
                $smallPics = $largePics = [];
                if (isset($value['mblog']['pics']) && !empty($value['mblog']['pics'])) {
                    //取出图片
                    foreach ($value['mblog']['pics'] as $valuePic) {
                        $smallPics[] = $valuePic['url'];
                        $largePics[] = $valuePic['large']['url'];
                    }
                }
                $forum['forum']['pics']['small_pics'] = $smallPics;
                $forum['forum']['pics']['large_pics'] = $largePics;
                //判断是否帖子中包含视频
                $smallMedias = $largeMedias = [];
                if (isset($value['mblog']['page_info']) && !empty($value['mblog']['page_info'])) {
                    if (strtolower($value['mblog']['page_info']['type']) == 'video') {
                        $smallMedias = $value['mblog']['page_info']['media_info'];
                        $largeMedias = $value['mblog']['page_info']['urls'];
                    }
                }
                $forum['forum']['medias']['small_medias'] = $smallMedias;
                $forum['forum']['medias']['large_medias'] = $largeMedias;
                $data[] = $forum;
            }
        }
        return $data;
    }

    /**
     * @method  爬取话题搜索结果页面mid
     * @param string $topic 话题
     * @return array
     */
    private function getMid($topic)
    {
        //获取话题搜索页面结果
        $url = "https://s.weibo.com/weibo/%23{$topic}%23";
        $html = $this->curlGet($url);
        //从话题页面提取出mid
        //<div class=\"card-wrap\" action-type=\"feed_list_item\" mid=\"(.*)\" >
        preg_match_all("/<div class=\"card-wrap\" action-type=\"feed_list_item\" mid=\"(.*)\" >/i", strtolower($html), $matches);
        return $matches[1];
    }

    /**
     * @method  根据mid获取帖子基本信息
     * @param string $mid mid
     * @return array
     */
    private function getForumDetail($mid)
    {
        $forumUrl = "https://m.weibo.cn/detail/{$mid}";
        $html = $this->curlGet($forumUrl);
        //获取js标签之间的数据
        $start = strpos($html, '[{');
        $end = strpos($html, '}]');
        $result = substr($html, $start, $end - $start);
        $result = $result . '}]';
        $result = json_decode($result, true);
        if (empty($result) || !is_array($result)) {
            return false;
        }
        //用户信息
        $forum['user'] = [
            'avatar' => $result[0]['status']['user']['avatar_hd'],//头像
            'nickname' => $result[0]['status']['user']['screen_name'],//昵称
            'gender' => $result[0]['status']['user']['gender'],//性别
            'home_page' => $result[0]['status']['user']['profile_url'],//个人主页
            'description' => $result[0]['status']['user']['description'],//描述
            'followers_count' => $result[0]['status']['user']['followers_count'],//粉丝
            'follow_count' => $result[0]['status']['user']['follow_count'],//关注
        ];
        //发帖信息
        $text = $this->dealText($result[0]['status']['text']);

        $forum['forum'] = [
            'text' => $text,//发帖内容
            'create_at' => date('Y-m-d H:i:s', strtotime($result[0]['status']['created_at'])),//发贴时间
        ];
        //图片和视频只有一种
        //判断是否帖子中包含图片
        $smallPics = $largePics = [];
        if (isset($result[0]['status']['pics']) && !empty($result[0]['status']['pics'])) {
            //取出图片
            foreach ($result[0]['status']['pics'] as $valuePic) {
                $smallPics[] = $valuePic['url'];
                $largePics[] = $valuePic['large']['url'];
            }
        }
        $forum['forum']['pics']['small_pics'] = $smallPics;
        $forum['forum']['pics']['large_pics'] = $largePics;
        //判断是否帖子中包含视频
        $smallMedias = $largeMedias = [];
        if (isset($result[0]['status']['page_info'])) {
            if (strtolower($result[0]['status']['page_info']['type']) == 'video') {
                $smallMedias = $result[0]['status']['page_info']['media_info'];
                $largeMedias = $result[0]['status']['page_info']['urls'];
            }
        }
        $forum['forum']['medias']['small_medias'] = $smallMedias;
        $forum['forum']['medias']['large_medias'] = $largeMedias;

        return $forum;
    }

    /**
     * @method  根据mid获取帖子内容信息【适用于内容显示不全的情况】
     * @param string $mid mid
     * @return string
     */
    private function getForumTextDetail($mid)
    {
        $forumUrl = "https://m.weibo.cn/detail/{$mid}";
        $html = $this->curlGet($forumUrl);
        //获取js标签之间的数据
        $start = strpos($html, '[{');
        $end = strpos($html, '}]');
        $result = substr($html, $start, $end - $start);
        $result = $result . '}]';
        $result = json_decode($result, true);
        if (empty($result) || !is_array($result)) {
            return false;
        }
        $text = $result[0]['status']['text'];
        //处理转发的情况
        if(isset($result[0]['status']['retweeted_status']['text']) && !empty($result[0]['status']['retweeted_status']['text'])){
            $text = $text . $result[0]['status']['retweeted_status']['text'];
        }
        //发帖信息
        return $this->dealText($text);
    }

    /**
     * @method  根据mid获取帖子评论信息
     * @param string $mid mid
     * @return array
     */
    private function getComment($mid)
    {
        $commentUrl = "https://m.weibo.cn/comments/hotflow?id={$mid}&mid={$mid}&max_id_type=0";
        $html = $this->curlGet($commentUrl);
        $html = json_decode($html, true);
        $comment = [];
        if (isset($html['data']['data']) || !empty($html['data']['data'])) {
            foreach ($html['data']['data'] as $key => $value) {
                //评论信息
                $comment[$key]['comment'] = [
                    'id' => $value['id'],//评论ID
                    'rootid' => $value['rootid'],//评论ID
                    'forumId' => $mid, // 帖子ID
                    'nickname' => $value['user']['screen_name'],//昵称
                    'created_at' => date('Y-m-d H:i:s', strtotime($value['created_at'])),//评论时间
                    'text' => $this->dealText($value['text']),//评论内容
                ];
                //评论用户信息
                $comment[$key]['user'] = [
                    'avatar' => $value['user']['avatar_hd'],//头像
                    'nickname' => $value['user']['screen_name'],//昵称
                    'gender' => $value['user']['gender'],//性别
                    'home_page' => $value['user']['profile_url'],//个人主页
                    'description' => $value['user']['description'],//描述
                    //'followers_count' => $value['user']['followers_count'],//粉丝
                    //'follow_count' => $value['user']['follow_count'],//关注
                ];

            }
        }
        return $comment;
    }

    /**
     * @method  根据mid获取帖子评论信息
     * @param string $mid mid
     * @return array
     */
    private function getMultiComment($rootid)
    {
        $commentUrl = "https://m.weibo.cn/comments/hotFlowChild?cid={$rootid}&max_id=0&max_id_type=0";
        $html = $this->curlGet($commentUrl);
        $html = json_decode($html, true);
        $comment = [];
        if (isset($html['data']) || !empty($html['data'])) {
            foreach ($html['data'] as $value) {
                //评论信息
                $comment[]['comment'] = [
                    'id' => $value['id'],//评论ID
                    'rootid' => $value['rootid'],//评论ID
                    'forumId' => $rootid, // 帖子ID
                    'nickname' => $value['user']['screen_name'],//昵称
                    'created_at' => date('Y-m-d H:i:s', strtotime($value['created_at'])),//评论时间
                    'text' => $value['text'],//评论内容
                ];
                //评论用户信息
                $comment[]['user'] = [
                    'avatar' => $value['user']['avatar_hd'],//头像
                    'nickname' => $value['user']['screen_name'],//昵称
                    'gender' => $value['user']['gender'],//性别
                    'home_page' => $value['user']['profile_url'],//个人主页
                    'description' => $value['user']['description'],//描述
                    'followers_count' => $value['user']['followers_count'],//粉丝
                    'follow_count' => $value['user']['follow_count'],//关注
                ];

            }
        }
        return $comment;
    }


    /**
     * @method  处理帖子内容格式
     * @param string $text 文本内容
     * @return string
     */
    private function dealText($text)
    {
        //发帖信息
        $text = strip_tags($text,'<p><br><img>');   //保留<p><br><img>
        //判断是否完成,以最后两位是否为全文进行判断
        if (mb_substr($text, -2) == '全文') {
            return ['text' => ''];
        }
        //去除@某人数据
        if(strpos($text,'@') != false){
            $text = preg_replace('/@(.*?) /is','',$text);
        }
        //位置，根据定位图标处理
        $position = '';
        if(strpos($text,'timeline_card_small_location_default.png') != false) {
            //特殊处理，内容末尾出现定位，拼接一个空格
            $text = $text.' ';
            $text = str_replace("'","\"",$text);
            preg_match_all('/timeline_card_small_location_default.png\">(.*?) /is', $text, $position);
            if (isset($position[1][0]) && !empty($position[1][0])) {
                $position = strip_tags($position[1][0]);
            }
        }
        //去掉文案后面的视频链接文案
//        if(strpos($text,'timeline_card_small_video_default.png') != false) {
//            //特殊处理，内容末尾出现定位，拼接一个空格
//            $text = $text.' ';
//            $text = str_replace("'","\"",$text);
//            $text = preg_replace('/timeline_card_small_video_default.png\">(.*?) /is','timeline_card_small_video_default.png">',$text);
//        }

        /*        preg_match_all('/<img.*?src="(.*?)".*?>/is',$text,$images);*/
//        if(isset($images[1]) && !empty($images[1])) {
//            //下载图片或者视频
//            foreach ($images[1] as $imageValue) {
//                $imageUrl = $this->downloadFile($imageValue);
//                //替换为本地上传的图片
//                $text = str_replace($imageValue,$imageUrl,$text);
//            }
//        }

        //话题提取
        preg_match_all('/#(.*?)#/is',$text,$topics);

        return [
            'text' => $text,
            'position' => $position,
            'topic_list' => $topics[1]
        ];
    }

    /**
     * @method  下载文件
     * @param string $url 远程文件地址
     * @param int $type 是否为图片
     * @param string $path 路径
     * @return string
     */
    private function downloadFile($url, $type=1,$path = 'images/')
    {
        $pix = $type == 1 ? '.jpg': '.mp4';
        $content = file_get_contents($url);
        $filename = $path.md5($url.time()).$pix;
        if(!is_file($filename)){
            file_put_contents($filename, $content);
        }
        return $filename;
    }

    /**
     * @method  规范参数格式
     * @param string $str 参数
     * @return string
     */
    private function checkPara($str)
    {
        return trim($str);
        if (empty($str)) {
            return '';
        }
        $str = str_replace("-", ",", $str);
        $str = str_replace("，", ",", $str);
        $str = str_replace(" ", ",", $str);
        $str = str_replace("|", ",", $str);
        $str = str_replace("、", ",", $str);
        $str = str_replace(",,", ",", $str);
        return trim($str);
    }

    /**
     * @method  curl-get请求
     * @param string $url 请求地址
     * @param int $port 端口号
     * @return string $filecontent  采集内容
     */
    private function curlGet($url, $port = 80)
    {
        $ch = curl_init();
        $header = array();
        $header[] = 'Content-Type:application/x-www-form-urlencoded';
        curl_setopt($ch, CURLOPT_URL, $url);
        if ($port !== 80) {
            curl_setopt($ch, CURLOPT_PORT, $port);
        }
        curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/72.0.3626.119 Safari/537.36");
        curl_setopt($ch, CURLOPT_HEADER, 0);//设定是否输出页面内容
//        curl_setopt($ch,CURLOPT_HTTPHEADER,$header);
//        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, true); //不验证证书
//        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, true); //不验证证书
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);       //链接超时时间
        curl_setopt($ch, CURLOPT_TIMEOUT, 20);       //设置超时时间

//        curl_setopt($ch, CURLOPT_PROXY, '39.98.196.223'); //代理服务器地址
//        curl_setopt($ch, CURLOPT_PROXYPORT, 80); //代理服务器端口
        $filecontent = curl_exec($ch);
        curl_close($ch);

        return $filecontent;
    }

}
