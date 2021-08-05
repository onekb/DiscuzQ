<?php
namespace App\Api\Controller\ReportV3;

use App\Models\Report;
use App\Common\ResponseCode;
use App\Repositories\UserRepository;
use Discuz\Base\DzqController;

class BatchDeleteReportsController extends DzqController
{
    protected function checkRequestPermissions(UserRepository $userRepo)
    {
        return $this->user->isAdmin();
    }

    public function main()
    {
        $idString = $this->inPut('ids');
        if (empty($idString)) {
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '缺少必要参数', '');
        }
        $ids = explode(',', $idString);

        if (count($ids) > 100) {
            return $this->outPut(ResponseCode::INTERNAL_ERROR, '批量添加超过限制', '');
        }

        foreach ($ids as $id) {
            if ($id < 1) {
                return $this->outPut(ResponseCode::INVALID_PARAMETER, '', '');
            }
        }

        $result = Report::query()->whereIn('id', $ids)->delete();
        if (!$result) {
            app('log')->info('requestId：' . $this->requestId . '-' . '删除举报记录出错，ID为： ' . $idString);
            return $this->outPut(ResponseCode::DB_ERROR, '', '');
        }


        return $this->outPut(ResponseCode::SUCCESS, '', '');
    }
}
