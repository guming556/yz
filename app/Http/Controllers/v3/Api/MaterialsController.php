<?php

namespace App\Http\Controllers\v3\Api;

use App\Modules\Manage\Model\MaterialsModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;

class MaterialsController extends BaseController
{
    //辅材包套餐列表

    public function getMaterials()
    {
        $data = MaterialsModel::select('materials.name','materials.content','materials.price','materials.sell_num')
            ->get()->toArray();
        if($data)
        {
            return response()->json($data);

        }else{
            return response()->json(['error'=> '操作失败'],'400');
        }
    }
    public function materDetail(Request $request)//套餐详情
    {
        $m_id = $request ->get('id');
        $data = MaterialsModel::select('materials.name','materials.content','materials.price','materials.sell_num')
            ->where('id',$m_id)-> get()-> toArray();

        if($data)
        {
            return response()->json($data);
        }else{
            return response()->json(['error' => '操作失败'],'400');
        }
    }
}
