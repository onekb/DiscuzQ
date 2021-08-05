<?php
namespace App\Api\Controller\ReportV3;

use App\Models\Report;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class BatchUpdateReportsController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }

    public function main()
    {
        $data = $this->inPut('data');
        if (empty($data)) {
            return $this->outPut(ResponseCode::INVALID_PARAMETER, '缺少必要参数', '');
        }

        if (count($data) > 100) {
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '批量添加超过限制', '');
        }

        foreach ($data as $key => $value) {
            try{
                $this->dzqValidate($value, [
                    'id'       => 'required|int|min:1',
                    'status'   => 'required|int|in:1'
                ]);

                $report = Report::query()->findOrFail($value['id']);
                $report->status = $value['status'];
                $report->save();

            } catch (\Exception $e) {
                app('log')->info('requestId：' . $this->requestId . '-' . '修改举报反馈出错，举报ID为： "' . $value['id'] . '" 。错误信息： ' . $e->getMessage());
                return $this->outPut(ResponseCode::INTERNAL_ERROR, '修改出错', [$e->getMessage(), $value]);
            }
        }

        return $this->outPut(ResponseCode::SUCCESS, '', '');
    }
}
