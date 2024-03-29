<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *   http://www.apache.org/licenses/LICENSE-2.0
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Api\Controller\AnalysisV3;

use App\Common\ResponseCode;
use App\Exceptions\TranslatorException;
use App\Models\PostGoods;
use App\Models\Thread;
use App\Traits\PostGoodsTrait;
use App\Repositories\UserRepository;
use Discuz\Auth\Exception\NotAuthenticatedException;
use Discuz\Base\DzqController;
use Discuz\Base\DzqLog;
use GuzzleHttp\Client;
use Illuminate\Contracts\Routing\UrlGenerator;
use Psr\Http\Message\ServerRequestInterface;
use Tobscure\JsonApi\Document;

class ResourceAnalysisGoodsController extends DzqController
{
    use PostGoodsTrait;

    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        if ($this->user->isGuest() || !$userRepo->canInsertGoodsToThread($this->user)) {
            throw new NotAuthenticatedException;
        }
        return true;
    }

    protected $httpClient;

    /**
     * @var UrlGenerator
     */
    protected $url;

    protected $allowDomain = [
        'taobao.com',
        'tmall.com',
        'detail.tmall.com',
        'jd.com',
        'm.jd.com',
        'yangkeduo.com',
        'youzan.com',
        'm.youzan.com',
        'tb.cn',
    ];

    /**
     * ResourceInviteController constructor.
     *
     * @param UrlGenerator $url
     */
    public function __construct(UrlGenerator $url)
    {
        $this->url = $url;

        $config = [
            'timeout' => 30,
        ];

        $this->httpClient = new Client($config);
    }

    /**
     * {@inheritdoc}
     * @param ServerRequestInterface $request
     * @param Document $document
     * @return array|mixed
     * @throws TranslatorException
     * @throws \Discuz\Auth\Exception\NotAuthenticatedException
     */
    public function main()
    {
        $actor = $this->user;

        $readyContent = $this->inPut('address');

        $data = [];
        $data['address'] = $readyContent;

        $this->dzqValidate($data, [
            'address'  => 'required_without:address|max:1500',
        ]);

        /**
         * 查询数据库中是否存在
         */
        $postGoods = PostGoods::query();
        $postGoods->where('post_id', 0)->where('user_id', $actor->id);
        /** @var PostGoods $existsGoods */
        $existsGoods = $postGoods->where('ready_content', $readyContent)->first();
        if (! empty($existsGoods)) {
            if ($this->checkGoodTitle($existsGoods)) {
                return $this->outPut(ResponseCode::SUCCESS,'', $this->camelData($existsGoods->toArray()));
            }
            // TODO 未抓取到，但是商品已存在
        }

        // Filter Url
        $addressRegex = '/(?<address>(https|http):[\S.]+)/i';
        if (! preg_match($addressRegex, $readyContent, $matchAddress)) {
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_goods_not_found_address'));
        }
        $this->address = $matchAddress['address'];

        // Validator Address
        $domainRegex = '/https:\/\/(([^:\/]*?)\.(?<url>.+?\.(cn|com)))/i';
        if (preg_match($domainRegex, $this->address, $domainUrl)) {
            if (! in_array($domainUrl['url'], $this->allowDomain)) {
                return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_goods_does_not_resolve'));
            }
        }

        //过滤域名
        $readyContent = $this->processUrl($readyContent, $domainUrl[0]);

        //过滤ip
        $readyContent = $this->processStr($readyContent);

        // Regular Expression Url
        $extractionUrlRegex = '/(https|http):\/\/(?<url>[0-9a-z.]+)/i';
        if (! preg_match($extractionUrlRegex, $this->address, $match)) {
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_goods_not_found_regex'));
        }

        $url = $match['url'];
        if (empty($url)) {
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_goods_fail_url'));
        }

        // Judge Enum
        if (! PostGoods::enumType(explode('.', $url), function ($callback) {
            $this->goodsType = $callback;
        })) {
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_goods_not_found_enum'));
        }

        /**
         * 如果是淘口令
         * （获取标题判断数据库是否存在该商品）
         */
        if ($this->goodsType['key'] == 5) {
            $titleRegex = '/【(?<title>.*)】/i';
            if (preg_match($titleRegex, $readyContent, $matchContent)) {
                /** @var PostGoods $existTBGoods */
                $existTBGoods = PostGoods::query()
                    ->where('title', $matchContent['title'])
                    ->where('post_id', 0)
                    ->where('user_id', $actor->id)
                    ->first();
                if (! empty($existTBGoods)) {
                    return $this->outPut(ResponseCode::SUCCESS,'',$this->littleHump($existTBGoods->toArray()));
                }
            }
        }

        /**
         * Send
         *
         * @see https://guzzle-cn.readthedocs.io/zh_CN/latest/request-options.html#allow-redirects
         */
        $sendType = PostGoods::setBySending($this->address);
        if ($sendType == 'Guzzle') {
            try {
                $response = $this->httpClient->request('GET', $this->address, [
                    'allow_redirects' => [
                        'max' => 100,
                        'track_redirects' => true,
                    ],
                ]);
            } catch (\Exception $e) {
                DzqLog::error('resource_analysis_goods_error', [
                    'address' => $this->address
                ], $e->getMessage());
                $this->outPut(ResponseCode::INTERNAL_ERROR, '获取商品信息失败');
            }

            if ($response->getStatusCode() != 200) {
                return $this->outPut(ResponseCode::NET_ERROR,trans('post.post_goods_http_client_fail'));
            }
            $this->html = $response->getBody()->getContents();
        } elseif ($sendType == 'File') {
            $ch = curl_init();
            $timeout = 10;
            curl_setopt ($ch, CURLOPT_URL,$this->address);
            curl_setopt ($ch, CURLOPT_USERAGENT,'Mozilla/5.0 (compatible; MSIE 6.0; Windows NT 5.0)');
            curl_setopt ($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt ($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
            $this->html = curl_exec($ch);

            app('log')->info('商品贴京东解析打印'.curl_exec($ch));
        }

        /**
         * Get GoodsInfo
         *
         * @see PostGoodsTrait
         */
        if(empty($this->goodsType['value'])){
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_goods_fail_url'));
        }
        $this->{$this->goodsType['value']}();

        //过滤商品
        if ($this->goodsType['key'] != 3 && empty($this->goodsInfo['title']) && empty($this->goodsInfo['price'])) {
            return $this->outPut(ResponseCode::INVALID_PARAMETER,trans('post.post_goods_fail_url'));
        }

        /**
         * check GoodsInfo
         */
        $this->checkGoods();

        // Build
        $build = [
            'user_id' => $actor->id,
            'post_id' => 0,
            'platform_id' => $this->goodsInfo['platform_id'],
            'title' => $this->goodsInfo['title'],
            'image_path' => $this->goodsInfo['src'],
            'price' => $this->goodsInfo['price'],
            'type' => $this->goodsType['key'],
            'status' => 0,  // TODO 解析商品下架状态
            'ready_content' => $readyContent,
            'detail_content' => $this->address,
        ];

        // Created
        $goods = PostGoods::store(
            $build['user_id'],
            $build['post_id'],
            $build['platform_id'],
            $build['title'],
            $build['price'],
            $build['image_path'],
            $build['type'],
            $build['status'],
            $build['ready_content'],
            $build['detail_content']
        );

        $goods->save();

        return $this->outPut(ResponseCode::SUCCESS,'', $this->camelData($goods));
    }

    private function checkGoodTitle(PostGoods $goods)
    {
        // 检测是否未抓取到，创建了默认商品，如果是 true 就代表抓取到了，返回数据库的该商品信息
        if (! in_array($goods->title, ['淘宝商品', '京东商品', '天猫商品', '有赞商品'])) {
            return true;
        }

        return false;
    }

    protected function checkGoods()
    {
        $this->goodsInfo['title'] = $this->goodsInfo['title'] ?: PostGoods::enumTypeName($this->goodsType['key'], '商品');

        switch ($this->goodsType['key']) {
            case 0: // 淘宝
            case 5: // 淘宝口令粘贴值
                $this->goodsInfo['src'] ?: $this->goodsInfo['src'] = $this->getDefaultIconUrl('taobao.svg');
                break;
            case 1: // 天猫
                $this->goodsInfo['src'] ?: $this->goodsInfo['src'] = $this->getDefaultIconUrl('tmall.svg');
                break;
            case 2: // 京东
            case 6: // 京东粘贴值H5域名
                $this->goodsInfo['src'] ?: $this->goodsInfo['src'] = $this->getDefaultIconUrl('jd.svg');
                break;
            case 3: // 拼多多H5
                $this->goodsInfo['src'] ?: $this->goodsInfo['src'] = $this->getDefaultIconUrl('pdd.svg');
                break;
            case 4: // 有赞
            case 7: // 有赞粘贴值
                $this->goodsInfo['src'] ?: $this->goodsInfo['src'] = $this->getDefaultIconUrl('youzan.svg');
                break;
        }
    }

    protected function getDefaultIconUrl($imgName)
    {
        return $this->url->to('/images/goods/' . $imgName);
    }

    //过滤ip
    protected function processStr($url){

        preg_match_all("/\d+\.\d+\.\d+\.\d+/",$url,$arr);
        if(!empty($arr[0])){
            foreach ($arr[0] as $tcp){
                $isVaildIp = preg_match("/^(((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))\.){3}((1?\d{1,2})|(2[0-4]\d)|(25[0-5]))$/",$tcp);
                if($isVaildIp > 0){
                    $url = str_replace($tcp,'',$url);
                }
            }
        }

        return $url;
    }

    //过滤域名
    protected function processUrl($url,$str){

        $url = str_replace($str,'########',$url);
        preg_match_all('/https:\/\/(([^:\/]*?)\.(?<url>.+?\.(cn|com)))/i', $url, $arr);
        preg_match_all('/http:\/\/(([^:\/]*?)\.(?<url>.+?\.(cn|com)))/i', $url, $arr1);

        $merge = array_merge($arr[0],$arr1[0]);
        foreach ($merge as $merV) {
            $url = str_replace($merV,'',$url);
        }

        return str_replace('########',$str,$url);
    }
}
