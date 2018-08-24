<?php

namespace App\Http\Controllers\v4\Api;

use App\Modules\Manage\Model\MaterialsModel;
use App\Modules\Task\Model\WorkModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use DB;
use App\Modules\Task\Model\TaskModel;


class MaterialsController extends BaseController {
    //辅材包套餐列表

    public function getMaterials() {
        $data = MaterialsModel::select('materials.name', 'materials.content', 'materials.price', 'materials.sell_num')
            ->get()->toArray();
        if ($data) {
            return $this->success($data);

        } else {
            return $this->error('操作失败', 400);
        }
    }

    public function materDetail(Request $request)//套餐详情
    {
        $m_id = $request->get('id');
        $data = MaterialsModel::select('materials.name', 'materials.content', 'materials.price', 'materials.sell_num')
            ->where('id', $m_id)->get()->toArray();

        if ($data) {
            return $this->success($data);
        } else {
            return $this->error('操作失败', 400);
        }
    }


    //获取顶部菜单
    public function getTopMenu() {
        $menu = [
            ['value' => 1, 'name' => '待处理'],
            ['value' => 2, 'name' => '已确认'],
            ['value' => 3, 'name' => '已驳回']
        ];
        return $this->success($menu);
    }

    //获取所有已提交的主材选购单列表
    public function getPrincipalOrderList(Request $request) {
        $status      = intval($request->get('status'));
        $task_id     = $request->get('task_id');
        $task_detail = TaskModel::find($task_id);

        if ($task_detail->user_type != 3) {
            $task_detail = TaskModel::where('user_type', 3)->where('unique_code', $task_detail->unique_code)->first();
        }

        if (empty($task_detail)) {
            $order_task_id = 0;
        } else {
            $order_task_id = $task_detail->id;
        }

        $orders = DB::table('principal_material_order')->select('code', 'created_at', 'status', 'id')->where('task_id', $order_task_id)->where('status', $status)->orderBy('id','desc')->get();
        foreach ($orders as $key => &$value) {
            if ($value->status == 1) {
                $value->status = '管家已提交，待业主确认';
            }
            if ($value->status == 2) {
                $value->status = '业主已确认';
            }
            if ($value->status == 3) {
                $value->status = '业主已驳回，待管家修改';
            }
        }

        return $this->success($orders);
    }


    //获取主材单详细列表
    public function getPrimaryPrincipal(Request $request) {

        $principal_id = $request->get('principal_id');
        $type         = $request->get('type');   // 1为添加  2为修改

        $cate = DB::table('principal_material_cate')->select('id', 'name')->where('deleted', 0)->get();
        array_push($cate, (object)['id' => 0, 'name' => '其他']);

        $goods = DB::table('principal_material_goods as g')->select('id', 'name', 'brand_name', 'model_name', 'specifications', 'cate_id' , 'img')->where('deleted', 0)->get();

        if ($type == 2) {
            $order_detail = DB::table('principal_material_order_detail')->where('order_id', $principal_id)->get();
            if (empty($order_detail)) {
                return $this->error('找不到选购单', 400);
            }
        }

        foreach ($goods as $key => $value) {
            $goods[$key]->num      = '0';
            $goods[$key]->use_area = '';
            $goods[$key]->checked  = false;
            if(!empty($value->img)){
                $goods[$key]->img = url($value->img);
            }
            if ($type == 2) {
                foreach ($order_detail as $key2 => $value2) {
                    if (intval($value->id) === intval($value2->goods_id)) {
                        $goods[$key]->num      = $value2->num;
                        $goods[$key]->use_area = $value2->use_area;
                        $goods[$key]->checked  = true;
                    }
                }
            }
        }

        foreach ($cate as $key => $value) {
            $cate[$key]->goods = [];
            foreach ($goods as $key2 => $value2) {
                if (intval($value->id) === intval($value2->cate_id)) {
                    $cate[$key]->goods[] = $value2;
                }
            }

            if ($value->id == 0 && $type == 2) {
                foreach ($order_detail as $key3 => $value3) {
                    if ($value3->cate_id == 0) {
                        $others              = (object)[
                            'id' => 0,
                            'name' => $value3->name,
                            'brand_name' => '',
                            'model_name' => '',
                            'specifications' => '',
                            'cate_id' => 0,
                            'num' => (string)$value3->num,
                            'use_area' => $value3->use_area
                        ];
                        $cate[$key]->goods[] = $others;
                    }
                }
            }
        }
        return $this->success($cate);
    }


    //获取已确认的主材选购单（订单已确认时使用）
    public function getPrincipalDetail(Request $request) {
        $principal_id = $request->get('principal_id');
        if (empty($principal_id)) {
            return $this->error('找不到选购单', 400);
        }
        $order_detail = DB::table('principal_material_order_detail as od')->select('od.id', 'od.name', 'od.brand_name', 'od.model_name', 'od.specifications', 'od.num', 'od.use_area', 'od.cate_id', 'od.task_id', 'od.receiving_state', 'od.audit_status','g.img','od.goods_id')->leftJoin('principal_material_goods as g','od.goods_id','=','g.id')->where('od.order_id', $principal_id)->get();

        $history = DB::table('principal_material_order_change_history')->whereIn('status', [1,3])->where('order_id',$principal_id)->get();

        foreach ($order_detail as $key => $value) {
            $order_detail[$key]->update_num = 0;
            $order_detail[$key]->receiving_state_desc = '';
            $receiving_state                          = intval($value->receiving_state);

            if(!empty($value->img)){
                $order_detail[$key]->img = url($value->img);
            }else{
                $order_detail[$key]->img = '';
            }

            if ($receiving_state === 0) {
                $order_detail[$key]->receiving_state_desc = '待发货';
            }
            if ($receiving_state === 1) {
                $order_detail[$key]->receiving_state_desc = '待确认收货';
            }
            if ($receiving_state === 2) {
                $order_detail[$key]->receiving_state_desc = '已收货';
            }

            foreach($history as $key2 => $value2){
                if($value2->order_detail_id == $value->id){
                    $order_detail[$key]->update_num = $value2->update_num;
                }
            }

        }

        return $this->success($order_detail);
    }


    //获取可选择区域（选购单）
    public function getUseArea(Request $request) {
        $principal_id = $request->get('principal_id');
        $useArea      = [['area' => '客厅'], ['area' => '厨房'], ['area' => '阳台'], ['area' => '书房'], ['area' => '天台'], ['area' => '厕所'], ['area' => '客房'], ['area' => '主卧']];

        if (!empty($principal_id)) {
            $order_detail = DB::table('principal_material_order_detail')->where('order_id', $principal_id)->get();
            foreach ($order_detail as $key => $value) {
                if (!in_array($value->use_area, $useArea)) {
                    array_push($useArea, ['area' => $value->use_area]);
                }
            }
        }
        return $this->success($useArea);
    }


    //业主驳回主材选购单
    public function bossRejectMaterialList(Request $request) {
        $user_id      = $request->json('user_id');
        $principal_id = $request->json('principal_id'); // 管家的任务id

        if (empty($user_id) || empty($principal_id)) {
            return $this->error('传递参数为空', 400);
        }

        $ret = DB::table('principal_material_order')->where('id', $principal_id)->update(['status' => 3]);

        if (!empty($ret)) {
            return $this->error('驳回成功',0);
        } else {
            return $this->error('驳回失败');
        }
    }


    //业主确认主材选购单
    public function bossSureMaterialList(Request $request) {
        $user_id      = $request->json('user_id');
        $principal_id = $request->json('principal_id'); // 管家的任务id

        if (empty($user_id) || empty($principal_id)) {
            return $this->error('传递参数为空', 400);
        }

        //  TODO 没有验证合法性

        $ret = DB::table('principal_material_order')->where('id', $principal_id)->update(['status' => 2]);

        if (!empty($ret)) {
            return $this->error('驳回成功',0);
        } else {
            return $this->error('驳回失败');
        }
    }


    //主材选购单材料确认收货接口
    public function confirmReceive(Request $request) {
        $task_id        = $request->json('task_id');  //管家任务id
        $material_id    = $request->json('material_id');
        $housekeeper_id = $request->json('housekeeper_id');
        if (empty($material_id) || empty($housekeeper_id) || empty($task_id)) {
            return $this->error('必要参数为空');
        }

        $workDetail = WorkModel::where('task_id', $task_id)->where('uid', $housekeeper_id)->first();
        $taskDetail = TaskModel::where('id', $task_id)->first();
        if (empty($workDetail)) {
            return $this->error('确认失败，没有权限进行此操作',2);
        }

        if (empty($taskDetail)) {
            return $this->error('确认失败，没有权限进行此操作');
        } else {
            if ($taskDetail->status >= 9 && intval($taskDetail->end_order_status) === 0) {
                return $this->error('确认失败，没有权限进行此操作');
            }
        }

        $materialDetail = DB::table('principal_material_order_detail')->where('id', $material_id)->first();
        if ($materialDetail->audit_status != 0) {
            return $this->error('确认失败，业主未确认该材料项');
        }


        $ret = DB::table('principal_material_order_detail')->where('task_id', $task_id)->where('id', $material_id)->update(['receiving_state' => 2]);
        if (!empty($ret)) {
            return $this->error('确认成功',0);
        } else {
            return $this->error('确认失败');
        }
    }


    //提交主材选购单
    public function subMaterialList(Request $request) {
        /**
         * {
         *    "id": 245,
         *    "name": "地砖",
         *    "brand_name": "中式",
         *    "model_name": "型号",
         *    "specifications": "规格",
         *    "cate_id": 分类id, 若分类为其他，则默认传0
         *    "num": 需要数量
         *    "task_id": 任务id
         *    "area":选择地域
         *  }
         */
        $materialList   = $request->json('materialList');
        $housekeeper_id = $request->json('housekeeper_id');
        $task_id        = $request->json('task_id');
        $principal_id   = $request->json('principal_id');
        $is_ios = intval($request->json('is_ios'));
        if (empty($materialList)) {
            return $this->error('提交的清单为空');
        }

        $work       = WorkModel::where('task_id', $task_id)->where('uid', $housekeeper_id)->where('status', '>=' ,1)->first();
        $taskDetail = TaskModel::where('id', $task_id)->where('user_type', 3)->first();
        if (empty($taskDetail) || empty($work)) {
            return $this->error('提交失败，没有权限进行此操作');
        }

        $insertArr    = [];
        if(!$is_ios){
            $materialList = \GuzzleHttp\json_decode($materialList,true);
        }


        foreach ($materialList as $key => $value) {
            if (intval($value['num']) <= 0) {
                return $this->error('选材数量不可为0');
            }
            if (empty($value['use_area'])) {
                return $this->error('使用区域不可为空');
            }
        }


        if (empty(intval($principal_id))) {
            $ret      = DB::table('principal_material_order')->insertGetId([
                'code' => date('YmdHis') . mt_rand(1, 99),
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s'),
                'task_id' => $task_id,
                'housekeeper_id' => $housekeeper_id,
                'unique_code' => $taskDetail->unique_code
            ]);
            $order_id = $ret;
        } else {
            $order_id = $principal_id;
            DB::table('principal_material_order')->where('id', $order_id)->update(['status'=>1]);
            DB::table('principal_material_order_detail')->where('order_id', $order_id)->delete();
        }


        foreach ($materialList as $key => $value) {
            $insertArr[] = [
                'order_id' => $order_id,
                'created_at' => date('Y-m-d H:i:s'),
                'goods_id' => isset($value['id']) ? $value['id'] : 0,
                'name' => $value['name'],
                'model_name' => empty($value['model_name'])?'':$value['model_name'],
                'brand_name' => $value['brand_name'],
                'specifications' => $value['specifications'],
                'cate_id' => $value['cate_id'],
                'num' => $value['num'],
                'task_id' => $task_id,
                'use_area' => $value['use_area']
            ];
        }

        $ret = DB::table('principal_material_order_detail')->insert($insertArr);
        if (empty($ret)) {
            return $this->error('提交失败');
        }
        return $this->error('提交成功',0);
    }




    //修改已提交材料项的数量或者使用区域
    public function updateMaterials(Request $request) {
        $housekeeper_id = $request->json('user_id');
        $task_id        = $request->json('task_id');
        $material_id    = $request->json('material_id');
        $principal_id   = $request->json('principal_id');
        $data['num']    = intval($request->json('num'));
//        $data['use_area'] = $request ->json('use_area');
        $taskDetail = TaskModel::where('id', $task_id)->where('user_type', 3)->first();

        $work       = WorkModel::where('task_id', $task_id)->where('uid', $housekeeper_id)->where('status','>=', 1)->first();

        if (empty($taskDetail) || empty($work)) {
            return $this->error('提交失败，没有权限进行此操作');
        }
        $principalDetail = DB::table('principal_material_order')->where('id', $principal_id)->first();
        if ($principalDetail->status != 2) {
            return $this->error('提交失败，主材选购单未通过业主确认');
        }

        $currentStatus = DB::table('principal_material_order_detail')->where('id',$material_id)->first();
        if($currentStatus->receiving_state > 0){
            return $this->error('提交失败，该材料已发货或已确认');
        }

        $data['audit_status'] = 1;
        $ret                  = DB::table('principal_material_order_detail')->where('id', $material_id)->update($data);

        $history = DB::table('principal_material_order_change_history')->where('order_detail_id',$material_id)->first();

        if(empty($history)){
            DB::table('principal_material_order_change_history')->insert([
                'order_id'=>$principal_id,
                'created_at'=>date('Y-m-d H:i:s'),
                'status'=>1,
                'order_detail_id'=>$material_id,
                'update_num'=>$data['num'],
                'name'=>$currentStatus->name,
                'primary_num'=>$currentStatus->num
            ]);
        }else{
            DB::table('principal_material_order_change_history')->where('order_detail_id',$material_id)->update(['status'=>1,'update_num'=>$data['num']]);
        }

        if (empty($ret)) {
            return $this->error('修改失败');
        }
        return $this->error('修改成功',0);
    }

    //业主确认某个修改项的修改请求
    public function bossSureMaterial(Request $request) {
        $user_id     = $request->json('user_id');
        $material_id = $request->json('material_id');

        $materialDetail = DB::table('principal_material_order_detail')->where('id', $material_id)->first();
        $taskDetail     = TaskModel::where('uid', $user_id)->where('id', $materialDetail->task_id)->first();
        if (empty($taskDetail)) {
            return $this->error('提交失败，没有权限进行此操作');
        }

        if ($materialDetail->num == 0) {
            $ret = DB::table('principal_material_order_detail')->where('id', $material_id)->delete();
        } else {
            $ret = DB::table('principal_material_order_detail')->where('id', $material_id)->update(['audit_status' => 0]);
        }

//        $historyStatus = DB::table('principal_material_order_change_history')->where('material_id',$material_id)->update(['audit_status'=>2]);
        if (empty($ret)) {
            return $this->error('确认失败');
        }

        DB::table('principal_material_order_change_history')->where('order_detail_id',$material_id)->where('status',1)->update(['status'=>0]);

        return $this->error('确认成功',0);
    }

    //业主驳回某个修改项的修改请求
    public function bossRejectMaterial(Request $request) {
        $user_id     = $request->json('user_id');
        $material_id = $request->json('material_id');
        $materialDetail = DB::table('principal_material_order_detail')->where('id', $material_id)->first();
        $taskDetail     = TaskModel::where('uid', $user_id)->where('id', $materialDetail->task_id)->first();

        if (empty($taskDetail)) {
            return $this->error('提交失败，没有权限进行此操作');
        }

        $ret = DB::table('principal_material_order_detail')->where('id', $material_id)->update(['audit_status' => 3]);
        if (empty($ret)) {
            return $this->error('驳回失败');
        }
        DB::table('principal_material_order_change_history')->where('order_detail_id',$material_id)->where('status',1)->update(['status'=>3]);
        return $this->error('驳回成功',0);
    }

    //历史修改记录
    public function updateHistoryList(Request $request) {
        $principal_id = intval($request->get('principal_id'));
        $history      = DB::table('principal_material_order_change_history')->where('order_id', $principal_id)->get();
//        1为已提交，2为业主确认，3为业主驳回
        foreach($history as $key => &$value){
            if($value->status == 0){
                $value->status = '业主已确认修改';
            }
            if($value->status == 1){
                $value->status = '管家已提交，待业主确认';
            }
            if($value->status == 2){
                $value->status = '业主已确认修改';
            }
            if($value->status == 3){
                $value->status = '业主已驳回';
            }
        }

        return $this->success($history);
    }


}
