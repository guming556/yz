<?php

namespace App\Http\Controllers\v4\Api;

use App\Modules\Manage\Model\ExplainModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;

class ExplainController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function getExplain() //获取所有说明
    {
        $data = ExplainModel::select('title','content','editor','updated_at')
            ->where('deleted' ,'0')
            ->get()->toArray();
        if($data)
        {
            return $this->success($data);
        }
        else{
            return $this->error('访问失败',204);
        }
    }

    public function detailExplain(Request $request)
    {
        $id = $request->get('id');
        $data = ExplainModel::select('title','content','editor','updated_at')
            ->where('id',$id)->get()->toArray();
        if($data)
        {
            return $this->success($data);
        }else{
            return $this->error('访问失败',204);
        }
    }

}
