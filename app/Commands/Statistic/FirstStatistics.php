<?php

/**
 * Copyright (C) 2020 Tencent Cloud.
 *
 * Licensed under the Apache License, Version 2.0 (the "License");
 * you may not use this file except in compliance with the License.
 * You may obtain a copy of the License at
 *
 *   http://www.apache.org/licenses/LICENSE-2.0
 *
 * Unless required by applicable law or agreed to in writing, software
 * distributed under the License is distributed on an "AS IS" BASIS,
 * WITHOUT WARRANTIES OR CONDITIONS OF ANY KIND, either express or implied.
 * See the License for the specific language governing permissions and
 * limitations under the License.
 */

namespace App\Commands\Statistic;

use App\Models\Post;
use App\Models\Thread;
use App\Models\User;
use App\Models\Finance;
use Discuz\Auth\AssertPermissionTrait;
use Discuz\Auth\Exception\PermissionDeniedException;

class FirstStatistics
{
    use AssertPermissionTrait;

    protected $actor;
    protected $type;
    protected $createdAtBegin;
    protected $createdAtEnd;
    protected $thread;
    protected $post;
    protected $user;

    public function __construct(User $actor,$type, $createdAtBegin, $createdAtEnd)
    {
        $this->actor    = $actor;
        $this->type             = $type;
        $this->createdAtBegin   = $createdAtBegin;
        $this->createdAtEnd     = $createdAtEnd;
    }

    public function handle(Thread $thread,Post $post,User $user)
    {
        $this->thread = $thread;
        $this->post = $post;
        $this->user= $user;
        return call_user_func([$this, '__invoke']);
    }

    /**
     * @return mixed
     * @throws PermissionDeniedException
     */
    public function __invoke()
    {
        $this->assertCan($this->actor, 'viewSiteInfo');
        $beginTime = $this->getBeginTime();
        $endTime = $this->getEndTime();

        $createdAtEnd = date("Y-m-d",strtotime("+1 day",strtotime($this->createdAtEnd)));
        $threadData = $this->getThreadDay($this->type, $this->createdAtBegin,$createdAtEnd);
        $postData = $this->getPostDay($this->type, $this->createdAtBegin,$createdAtEnd);
        $activeUserData = $this->getActiveUser($this->type, $this->createdAtBegin,$createdAtEnd);
        $joinUserData = $this->getJoinUser($this->type, $this->createdAtBegin,$createdAtEnd);

        if($this->type==Finance::TYPE_DAYS){
            $dateArr = $this->getDateFromRange($this->createdAtBegin,$this->createdAtEnd);

            $newThreadData = $this->toDate($threadData,$dateArr);
            $newPostData = $this->toDate($postData,$dateArr);
            $newActiveUserData = $this->toDate($activeUserData,$dateArr);
            $newJoinUserData = $this->toDate($joinUserData,$dateArr);
        }elseif ($this->type==Finance::TYPE_WEEKS){
            $weekArr = $this->getWeek($this->createdAtBegin,$this->createdAtEnd);

            $newThreadData = $this->toWeek($threadData,$weekArr);
            $newPostData = $this->toWeek($postData,$weekArr);
            $newActiveUserData = $this->toWeek($activeUserData,$weekArr);
            $newJoinUserData = $this->toWeek($joinUserData,$weekArr);

        }elseif($this->type==Finance::TYPE_MONTH){
            $monthArr = $this->getMonth($this->createdAtBegin,$this->createdAtEnd);

            $newThreadData = $this->toMonth($threadData,$monthArr);
            $newPostData = $this->toMonth($postData,$monthArr);
            $newActiveUserData = $this->toMonth($activeUserData,$monthArr);
            $newJoinUserData = $this->toMonth($joinUserData,$monthArr);
        }

        $statisticsData = [];

        //每日发帖数
        data_set(
            $statisticsData,
            'threadData',
            $newThreadData
        );
        //每日回帖数
        data_set(
            $statisticsData,
            'postData',
            $newPostData
        );
        //日活用户数
        data_set(
            $statisticsData,
            'activeUserData',
            $newActiveUserData
        );
        //日注册数
        data_set(
            $statisticsData,
            'joinUserData',
            $newJoinUserData
        );

        $overData=[];
        $overData['over']['activeUserNumToday']=User::query()
            ->whereBetween('login_at', array($beginTime, $endTime))
            ->where('status',0)
            ->count();
        $overData['over']['addUserNumToday']=User::query()
            ->whereBetween('joined_at', array($beginTime, $endTime))
            ->where('status',0)
            ->count();
        $overData['over']['addThreadNumToday']=Thread::query()
            ->whereBetween('created_at', array($beginTime, $endTime))
            ->whereNull('deleted_at')
            ->where('is_draft',0)
            ->count();
        $overData['over']['addPostNumToday']=Post::query()
            ->whereBetween('created_at', array($beginTime, $endTime))
            ->where('is_first',0)
            ->count();

        $overData['over']['totalUserNum']=User::query()
            ->where('status',0)
            ->count();
        $overData['over']['totalThreadNum']=Thread::query()
            ->whereNull('deleted_at')
            ->where('is_draft',0)
            ->count();
        $overData['over']['totalPostNum']=Post::query()
            ->where('is_first',0)
            ->count();
        $overData['over']['essenceThreadNum']=Thread::query()
            ->where('is_essence', 1)
            ->whereNull('deleted_at')
            ->where('is_draft',0)
            ->count();

        data_set(
            $statisticsData,
            'overData',
            $overData
        );
        return $statisticsData;
    }
    public function weekday($year,$week=1){
        $year_start = mktime(0,0,0,1,1,$year);
        $year_end = mktime(0,0,0,12,31,$year);

        // 判断第一天是否为第一周的开始
        if (intval(date('W',$year_start))===1){
            $lastday=date("Y-m-d",strtotime(date('Y-m-d',$year_start)." Sunday"));
            $start=strtotime(date("Y-m-d",strtotime($lastday."-6 days")));
        //$start = $year_start;//把第一天做为第一周的开始
        }else{
            $start = strtotime('+1 monday',$year_start);//把第一个周一作为开始
        }

        // 第几周的开始时间
        if ($week==1){
            $weekday['start'] = $start;
        }else{
            $weekday['start'] = strtotime($week.' monday',$start);
        }

        // 第几周的结束时间
        $weekday['end'] = strtotime('+1 sunday',$weekday['start']);
        if (date('Y',$weekday['end'])!=$year){
            $weekday['end'] = $year_end;
        }
        $weekday['start'] = date('Y-m-d',$weekday['start']);
        $weekday['end'] = date('Y-m-d',$weekday['end']);

        return $weekday;
    }

    public function toMonth($data,$monthArr){
        //dump($monthArr);
        $tdData = $data->toArray();
        //dump($tdData);
        $month = [];
        $months = [];
        foreach ($tdData as $k=>$value){
            $da = explode('/',$value['date']);
            //dump($da);
            if(substr($da[1],0,1) == '0'){
                $n = substr($da[1],1,1);
            }else{
                $n = $da[1];
            }

            $month[]=$da[0].$n;

            $months["$da[0]$n"]=$value['count'];
        }
        //dump($month);
        //dump($months);
        $newData = [];
        foreach ($monthArr as $item=> $val){
            if(in_array($val,$month)){
                $newData[$item]['month'] = $val;
                $newData[$item]['count'] = $months[$val];
            }else{
                $newData[$item]['month'] = $val;
                $newData[$item]['count'] = 0;
            }
        }
        return $newData;
    }

    public function toWeek($data,$weekArr){

        $tdData = $data->toArray();
        $week = [];
        $weeks = [];
        foreach ($tdData as $k=>$value){
            $da = explode('/',$value['date']);
            if(substr($da[1],0,1) == '0'){
              $n = substr($da[1],1,1);
            }else{
                $n = $da[1];
            }
            $ls = $this->weekday($da[0],$n);

            $week[$ls['start']]=$value['count'];
            $weeks[]=$ls['start'];
        }
        $newData = [];
        foreach ($weekArr as $item=> $val){
            if(in_array($val[0],$weeks)){
                $newData[$item]['week'] = $val[0]."至".$val[1];
                $newData[$item]['count'] = $week[$val[0]];
            }else{
                $newData[$item]['week'] = $val[0]."至".$val[1];
                $newData[$item]['count'] = 0;
            }
        }
        return $newData;
    }

    public function toDate($data,$dateArr){
        $tdData = $data->toArray();
        $newData = [];
        foreach ($tdData as $k=>$value){
            $newTd1[$value['date']]=$value['count'];
        }
        foreach ($tdData as $k=>$value){
            $newTd2[]=$value['date'];
        }
        foreach ($dateArr as $item=> $val){
            if(in_array($val,$newTd2)){
                $newData[$item]['date'] = $val;
                $newData[$item]['count'] = $newTd1[$val];
            }else{
                $newData[$item]['date'] = $val;
                $newData[$item]['count'] = 0;
            }
        }
        return $newData;
    }

    public function getThreadDay($type,$createdAtBegin,$createdAtEnd){
        $threadQuery = Thread::query();
        $threadQuery->whereNull('deleted_at')
            ->where('is_draft',0)
            ->whereBetween('created_at', [$createdAtBegin, $createdAtEnd]);
        return $this->querysql($threadQuery,'thread',$type);
    }
    public function getPostDay($type,$createdAtBegin,$createdAtEnd){
        $postQuery = Post::query();
        $postQuery->where('is_first',0)
            ->whereBetween('created_at', [$createdAtBegin, $createdAtEnd]);
        return $this->querysql($postQuery,'post',$type);
    }
    public function getActiveUser($type,$createdAtBegin,$createdAtEnd){
        $userQuery = User::query();
        $userQuery->whereBetween('login_at', [$createdAtBegin, $createdAtEnd])
            ->where('status',0);
        return $this->querysql($userQuery,'ActiveUser',$type);
    }
    public function getJoinUser($type,$createdAtBegin,$createdAtEnd){
        $userQuery = User::query();
        $userQuery->whereBetween('joined_at', [$createdAtBegin, $createdAtEnd]);
        return $this->querysql($userQuery,'JoinUser',$type);
    }

    public function querysql($query,$types,$type){
        if($types=='thread' || $types=='post'){
            $column = 'created_at';
        }
        if($types=='ActiveUser'){
            $column = 'login_at';
        }
        if($types=='JoinUser'){
            $column = 'joined_at';
        }

        if ($type != 1) {
            if ($type == 2) {
                $format = '%Y/%u';
            } elseif ($type == 3) {
                $format = '%Y/%m';
            }
            $query->selectRaw(
                "DATE_FORMAT($column,'{$format}') as `date`," .
                'COUNT(id) as count'
            );
            $query->groupBy('date');
            $query->orderBy('date', 'asc');
        } else {
            $query->selectRaw("DATE_FORMAT($column,'%Y/%m/%d') as `date`," .
                'COUNT(id) as count
                ');
            $query->groupBy('date');
            $query->orderBy('date', 'asc');
        }
        return $query->get();
    }

    public function getBeginTime(){
        return date("Y-m-d H:i:s", mktime(0, 0, 0, date('m'), date('d'), date('Y')));
    }
    public function getEndTime(){
        return date("Y-m-d H:i:s",mktime(0,0,0,date('m'),date('d')+1,date('Y'))-1);
    }

    /**
     *
     * @param  Date  $startdate 开始日期
     * @param  Date  $enddate   结束日期
     * @return Array
     */

    function getDateFromRange($startdate, $enddate){
        $stimestamp = strtotime($startdate);
        $etimestamp = strtotime($enddate);
        // 计算日期段内有多少天
        $days = ($etimestamp-$stimestamp)/86400+1;
        // 保存每天日期
        $date = array();
        for($i=0; $i<$days; $i++){
            $date[] = date('Y/m/d', $stimestamp+(86400*$i));
        }
        return $date;
    }

    function getWeek($startdate,$enddate)
    {
        //参数不能为空
        if(!empty($startdate) && !empty($enddate)){
            //先把两个日期转为时间戳
            $startdate=strtotime($startdate);
            $enddate=strtotime($enddate);
            //开始日期不能大于结束日期
            if($startdate<=$enddate){
                $end_date=strtotime("next monday",$enddate);
                if(date("w",$startdate)==1){
                    $start_date=$startdate;
                }else{
                    $start_date=strtotime("last monday",$startdate);
                }
                //计算时间差多少周
                $countweek=($end_date-$start_date)/(7*24*3600);
                for($i=0;$i<$countweek;$i++){
                    $sd=date("Y-m-d",$start_date);
                    $ed=strtotime("+ 6 days",$start_date);
                    $eed=date("Y-m-d",$ed);
                    $arr[]=array($sd,$eed);
                    $start_date=strtotime("+ 1 day",$ed);
                }
                return $arr;
            }
        }
    }

    public function getMonth($startTime,$endTime){
        //2.对时间进行拆分：
        $time['start_time'] = explode('-', $startTime);
        $time['end_time'] = explode('-', $endTime);

        $j = $time['end_time'][0] - $time['start_time'][0];  //得到开始月
        $year1 = $time['start_time'][0];
        $time1 = array();
        $k = 0;
        for ($i = $time['start_time'][1]; $i <= $time['end_time'][1] + $j * 12; $i++) {
            if (!($i % 12)) $moth1 = 12;
            else $moth1 = $i % 12;
            $data = date('Y-m', strtotime($year1 . "-" . $moth1));
            $non = $year1 . $moth1;
            $time1[$k++] = $non;
            if (!($i % 12)) $year1++;
        }
        return $time1;
    }
}
