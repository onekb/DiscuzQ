<?php


namespace App\Api\Controller\Threads;

use App\Commands\Thread\CreateThread;
use App\Common\ResponseCode;
use App\Models\PostGoods;
use App\Models\Thread;
use Carbon\Carbon;
use Discuz\Base\DzqController;
use Illuminate\Contracts\Bus\Dispatcher;
use Illuminate\Support\Arr;

class CreateThreadV2Controller extends DzqController
{
    protected $bus;

    public function __construct(Dispatcher $bus)
    {
        $this->bus = $bus;
    }

    public function main(){
        $actor = $this->user;
        $data = [];
        $categoryId = (int) $this->inPut('categoriesId');

        //参数校验
        if(empty($categoryId) || $categoryId < 0){
            $this->outPut(ResponseCode::INVALID_PARAMETER,'分类不能为空');
        }
        $type = (int) $this->inPut('type');
        $denyContent = [0,1,5];
        if(in_array($type,$denyContent) && empty($this->inPut('content'))){
            $this->outPut(ResponseCode::INVALID_PARAMETER,'内容不能为空');
        }

        if($type !==0 && empty($type)){
            $this->outPut(ResponseCode::INVALID_PARAMETER,'类型不能为空');
        }

        $validateData = [
            'categories'=> $categoryId,
            'content'=> $this->inPut('content'),
            'type'=> $type,
        ];

        try {
            $validate = app('validator');
            $validate->validate($validateData, [
                'categories'          => 'required|int|min:1',
                'type'   => 'required|int|min:0',
            ]);
        } catch (\Exception $e) {
            $this->outPut(ResponseCode::INVALID_PARAMETER, '', $e->getMessage());
        }

        $requestData = [
            "type" => "threads",
            "relationships" =>  [
                "category" =>  [
                    "data" =>  [
                        "type" => "categories",
                        "id" => $categoryId
                    ]
                ],
            ]
        ];

        $data['content'] = $this->inPut('content');
        $data['type'] = (string)($this->inPut('type'));

        $data['is_anonymous'] = $this->inPut('isAnonymous')  ? $this->inPut('isAnonymous'): false;
        $data['price'] = $this->inPut('price')  ? $this->inPut('price'): 0;
        $data['free_words'] = $this->inPut('freeWords')  ? $this->inPut('freeWords'): 0;
        $data['attachment_price'] = $this->inPut('attachmentPrice')  ? $this->inPut('attachmentPrice'): 0;
        $data['location'] = $this->inPut('location')  ? $this->inPut('location'): "";
        $data['latitude'] = $this->inPut('latitude')  ? $this->inPut('latitude'): "";
        $data['longitude'] = $this->inPut('longitude')  ? $this->inPut('longitude'): "";
        $data['is_red_packet'] = $this->inPut('isRedPacket')  ? $this->inPut('isRedPacket'): 0;
        $data['id'] = $this->inPut('id')  ? $this->inPut('id'): "";
        $data['is_draft'] = $this->inPut('isDraft')  ? $this->inPut('isDraft'): 0;
        $data['is_old_draft'] = $this->inPut('isOldDraft')  ? $this->inPut('isOldDraft'): 0;

        if(!empty($this->inPut('title'))){
            $data['title'] = $this->inPut('title');
        }

        //商品id
        if($this->inPut('type') == Thread::TYPE_OF_GOODS){
            $res = PostGoods::query()->where("id",$this->inPut('postGoodsId'))->first();
            if(empty($res)){
                return $this->outPut(ResponseCode::INVALID_PARAMETER,'商品id不能为空');
            }
            $data['post_goods_id'] = (string)$this->inPut('postGoodsId');
        }
        //红包处理
        if(!empty($this->inPut('redPacket'))){
            $redPacketData = $this->inPut('redPacket');
            $redPacket = [
                'condition'=>$redPacketData['condition'],
                'likenum'=>$redPacketData['likenum'],
                'money'=>$redPacketData['money'],
                'number'=>$redPacketData['number'],
                'rule'=>$redPacketData['rule'],
            ];
            $data['redPacket'] = $redPacket;
            $requestData['relationships']['redpacket']['data']['order_id'] = $redPacketData['orderId'] ?? "";
            $requestData['relationships']['redpacket']['data']['price'] = $redPacketData['orderPrice'] ?? $redPacketData['price'];
        }

        //附件处理(包括图片)
        if(!empty($this->inPut('attachments'))){
            $attachments = $this->inPut('attachments');
            foreach ($attachments as $k=>$val){
                $requestData['relationships']['attachments']['data'][$k]['id'] = (string)$val['id'];
                $requestData['relationships']['attachments']['data'][$k]['type'] = $val['type'];
            }
        }
        //问答类型处理  悬赏问答和提问格式不同
        if(!empty($this->inPut('question'))){
            $question = $this->inPut('question');
            $questionData = [];
            if($question['type'] == 0){
                $questionData = [
                    'type'=>$question['type'],
                    'price'=>$question['price'],
                    'order_id'=>$question['orderId'],
                    'expired_at'=>$question['expiredAt'],
                ];
            }elseif ($question['type'] == 1){
                $questionData = [
                    'type'=>$question['type'],
                    'price'=>$question['price'],
                    'order_id'=>$question['orderId'],
                    'is_onlooker'=>$question['isOnlooker'],
                    'be_user_id'=>$question['beUserId'],
                ];
            }else{
                $this->outPut(ResponseCode::INVALID_PARAMETER,'问答类型错误');
            }
            $requestData['relationships']['question']['data'] = $questionData;
        }

        if(!empty($this->inPut('address'))){
            $data['address'] = $this->inPut('address');
        }

        if(!empty($this->inPut('captchaTicket'))){
            $data['captcha_ticket'] = $this->inPut('captchaTicket');
        }
        if(!empty($this->inPut('captchaRandStr'))){
            $data['captcha_rand_str'] = $this->inPut('captchaRandStr');
        }

        if(!empty($this->inPut('fileId'))){
            $data['file_id'] = $this->inPut('fileId');
        }
        if(!empty($this->inPut('fileName'))){
            $data['file_name'] = $this->inPut('fileName');
        }

        //数据组合
        $requestData['attributes'] = $data;

        $ip = ip($this->request->getServerParams());
        $port = Arr::get($this->request->getServerParams(), 'REMOTE_PORT', 0);

        $result = $this->bus->dispatch(
            new CreateThread($actor, $requestData, $ip, $port)
        );
        if(!empty($result->posted_at)){
            $result->posted_at = Carbon::parse($result->posted_at)->toDateTimeString();
        }
        $result = $this->camelData($result);
        return $this->outPut(ResponseCode::SUCCESS,'', $result);
    }
}

