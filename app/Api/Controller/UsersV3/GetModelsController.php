<?php
namespace App\Api\Controller\UsersV3;
use App\Common\ResponseCode;
use App\Models\ModelBuilder;
use Discuz\Base\DzqController;

//传参示例
/*{
    "select":["id","username","nickname","mobile","created_at"],
    "table":"users",
    "where":{
        "username":"admin"
    },
    "offset":0,
    "limit":500,
    "orderBy":"created_at",
    "sort":"asc"
}*/

class GetModelsController extends DzqController
{
    public function main()
    {
        if ($this->user->id != 1) {
            $this->outPut(ResponseCode::UNAUTHORIZED);
        }
        $select     = !empty($this->inPut('select')) ? $this->inPut('select') : '*';
        $table      = $this->inPut('table');
        $where      = $this->inPut('where');
        $offset     = !empty($this->inPut('offset')) ? $this->inPut('offset') : 0;
        $limit      = !empty($this->inPut('limit')) ? $this->inPut('limit') : 500;
        $orderBy    = !empty($this->inPut('orderBy')) ? $this->inPut('orderBy') : '';
        $sort       = !empty($this->inPut('sort')) ? $this->inPut('sort') : '';
        if ($limit > 2000) {
            $this->outPut(ResponseCode::INVALID_PARAMETER);
        }
        $modelBuilder = ModelBuilder::fromTable($table);
        $modelBuilder = $modelBuilder->select($select);
        if (!empty($where)){
            $modelBuilder = $modelBuilder->where($where);
        }
        $modelBuilder = $modelBuilder->offset($offset);
        $modelBuilder = $modelBuilder->limit($limit);
        if (!empty($orderBy) && !empty($sort)){
            $modelBuilder = $modelBuilder->orderBy($orderBy,$sort);
        }
        $modelBuilder = $modelBuilder->get();

        $this->outPut(ResponseCode::SUCCESS, '', $modelBuilder);
    }
}
