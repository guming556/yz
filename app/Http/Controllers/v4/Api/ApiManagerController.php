<?php

namespace App\Http\Controllers\v4\Api;

use App\Modules\User\Model\BuildingModel;
use App\Modules\User\Model\UserModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\User\Model\DistrictModel;
use App\Modules\Task\Model\TaskCateModel;
use App\Modules\Manage\Model\SpaceModel;
use App\Modules\Manage\Model\HouseModel;
use App\Modules\User\Model\CoordinateModel;
use App\Modules\Manage\Model\ConfigModel;
use DB;


class ApiManagerController extends BaseController
{
    public function getProvince(){
        $province_list = DistrictModel::findTree(0);
        $ret_list = array();
        foreach($province_list as $key => $value){
            $ret_list[] = array(
                'id'=>$value['id'],
                'name'=>$value['name']
            );
        }
        return $this->success($ret_list );
    }

    public function getCity(Request $request){
        $pid = $request->get('id');
        $city_list = DistrictModel::findTree($pid);
        $ret_list = array();
        foreach($city_list as $key => $value){
            $ret_list[] = array(
                'id'=>$value['id'],
                'name'=>$value['name']
            );
        }
        return $this->success($ret_list );
    }

    // 获取风格列表
    public function getStyle(){
        $category_data = TaskCateModel::findByPid([0] , ['id','name']);
        return $this->success( $category_data );
    }


    // 获取空间列表
    public function getSpace(){
        $category_data = SpaceModel::select('id','name')->get()->toArray();
        return $this->success( $category_data );
    }

    // 获取户型列表
    public function getHouse(){
        $category_data = HouseModel::select('id','name')->get()->toArray();
        return $this->success( $category_data );
    }





    // 获取所有楼盘的省、市、区
    public function getManageArea(Request $request){
        $type = $request->get('type');
        $id = $request->get('id');
        if($type==1){
            $type = 'province';
        }elseif($type==2){
            $type = 'city';
        }elseif($type==3){
            $type = 'district';
        }else{
            return $this->error();
        }
        $area = BuildingModel::findAreaIds($type,$id);
        $area[0]['id'] = (string)$area[0]['id'];
        return $this->success($area);
    }


    //根据区id获取楼盘
    public function getBuilding(Request $request){
        $district_id = $request->get('district_id');
        if(empty($district_id)){
            return $this->error();
        }
        $buildings = BuildingModel::findBuildings($district_id);
        return $this->success($buildings);
    }

    //获取工种列表
    public function getWorkType(){
        $list = ConfigModel::where('alias','worker')->first();
        $data = \GuzzleHttp\json_decode($list['rule'],true);
        $type = [];
        foreach($data as $key => $value){
            $type[] = ['id'=>$key,'name'=>$value];
        }
        return $this->success( $type );
    }

    //获取工种列表
    public function deletedImage(Request $request){
        $imageUrl = $request->get('path');
        $user_id = $request->get('user_id');
        return $this->error('删除成功',0);
    }

    //获取报建列表
    public function repairList(Request $request){
        $uid = $request->get('user_id');
        $ret = DB::table('building_repair as r')->select('r.id','r.detail','r.created_at','r.status','r.remark','building.building_name')->leftJoin('building','r.building_id','=','building.id')->where('r.uid',$uid)->orderBy('r.id','desc')->get();
        foreach ($ret as $key =>$value) {
            $ret[$key]->id = $value->id.'';
        }
        foreach($ret as $key => &$value){
            $ret[$key]->current_status = $value->status;
            if($value->status == 0){
                $value->status = '待审核';
            }
            if($value->status == 1){
                $value->status = '已审核';
            }
            if($value->status == 2){
                $value->status = '已完工';
            }
            if($value->status == 3){
                $value->status = '已驳回';
            }
            if(!empty($value->detail)){
                $detail = unserialize($value->detail);
                if(is_array($detail)){
                    foreach($detail as $key2 => &$value2){
                        if(empty($value2)){
                            $value2 = '';
                        }
                        if($key2=='room'){
                            $ret[$key]->room = $value2;
                        }
                    }
                    $value->detail = $detail;
                    unset($value2);
                }
                unset($value->detail);
            }
        }
        unset($value);
        return $this->success($ret);
    }

    //获取报建详细
    public function repairDetail(Request $request) {
        $id  = $request->get('id');
        $ret = DB::table('building_repair')->select('id as repair_id','detail', 'created_at', 'status' , 'remark','repair_type')->where('id', $id)->first();
        $ret->repair_id = $ret->repair_id.'';
        $ret->current_status = $ret->status;
        if ($ret->status == 0) {
            $ret->status = '待审核';
        }
        if ($ret->status == 1) {
            $ret->status = '已审核';
        }
        if ($ret->status == 2) {
            $ret->status = '已完工';
        }
        if($ret->status == 3){
        }
        if (!empty($ret->detail)) {
            $detail = unserialize($ret->detail);
            if (is_array($detail)) {
                foreach ($detail as $key2 => $value2) {
                    if (empty($value2) && !in_array($key2, ['workers', 'project_picture'])) {
                        $value2 = '';
                    }
                    if ($key2 == 'workers' && !empty($value2)) {
                        foreach ($value2 as $key3 => $value3) {
                            if (!empty($value3['worker_positive_identity_card'])) {
                                $value2[$key3]['worker_positive_identity_card'] = url($value3['worker_positive_identity_card']);
                            }
                            if (!empty($value3['worker_opposite_identity_card'])) {
                                $value2[$key3]['worker_opposite_identity_card'] = url($value3['worker_opposite_identity_card']);
                            }
                        }
                    }
                    $ret->$key2 = $value2;
                }
            }
            if (!empty($ret->project_picture)) {
                foreach ($ret->project_picture as $key => $value) {
                    $ret->project_picture[$key] = url($value);
                }
            }
            if (!empty($ret->business_license)) {
                $ret->business_license = url($ret->business_license);
            }
            if (!empty($ret->copy_of_work)) {
                $ret->copy_of_work = url($ret->copy_of_work);
            }
            if (!empty($ret->owner_opposite_identity_card)) {
                $ret->owner_opposite_identity_card = url($ret->owner_opposite_identity_card);
            }
            if (!empty($ret->owner_positive_identity_card)) {
                $ret->owner_positive_identity_card = url($ret->owner_positive_identity_card);
            }
            if (!empty($ret->charger_positive_identity_card)) {
                $ret->charger_positive_identity_card = url($ret->charger_positive_identity_card);
            }
            if (!empty($ret->charger_opposite_identity_card)) {
                $ret->charger_opposite_identity_card = url($ret->charger_opposite_identity_card);
            }
        }
        unset($ret->detail);
        return $this->success($ret);
    }

    //上传或重新上传装修资料
    /**
     * @param Request $request
     *  省:选择
     *  市:选择
     *  区:选择
     *  楼盘：选择
     *  房号：填写
     *  业主身份证复印件图片（正反面）
     *  装饰公司营业执照（可不填）：图片（单张）
     *  施工CAD图纸（可不填）：多张
     *  装饰负责人信息（可不填）：姓名，电话，身份证图片（正反面），特种作业复印件
     *  多个工人信息（可不填）：姓名、电话、身份证图片（正反面）、工种
     */
    public function uploadRenovationData(Request $request){
        $replace_str = url().'/';
        $uid = $request->json('user_id');
        $repair_id = intval($request->json('repair_id'));    //订单id，为0则为添加，否则为修改
        $data['building_id'] = $request->json('building_id');
        $data['room'] = $request->json('room');

        $data['owner_positive_identity_card'] = empty($request->json('owner_positive_identity_card'))?'':str_replace($replace_str,'',$request->json('owner_positive_identity_card'));
        $data['owner_opposite_identity_card'] = empty($request->json('owner_opposite_identity_card'))?'':str_replace($replace_str,'',$request->json('owner_opposite_identity_card'));
        $data['business_license'] = empty($request->json('business_license'))?'':str_replace($replace_str,'',$request->json('business_license'));
        $data['project_picture'] = empty($request->json('project_picture'))?[]:$request->json('project_picture');

        $data['charger_name'] = empty($request->json('charger_name'))?'':$request->json('charger_name');
        $data['charger_tel'] = empty($request->json('charger_tel'))?'':$request->json('charger_tel');
        $data['charger_positive_identity_card'] = empty($request->json('charger_positive_identity_card'))?'':str_replace($replace_str,'',$request->json('charger_positive_identity_card'));
        $data['charger_opposite_identity_card'] = empty($request->json('charger_opposite_identity_card'))?'':str_replace($replace_str,'',$request->json('charger_opposite_identity_card'));
        $data['copy_of_work'] = empty($request->json('copy_of_work'))?'':str_replace($replace_str,'',$request->json('copy_of_work'));

        $data['workers'] = empty($request->json('workers'))?[]:$request->json('workers');
        $repair_type = max(intval($request->json('repair_type')) , 1);


        if(!empty($data['charger_name']) || !empty($data['charger_tel'])){
            if(empty($data['charger_name']) || empty($data['charger_tel'])){
                return $this->error('请填写装修负责人的电话和姓名');
            }
        }

        // TODO 重新提交时需要确认一下图片是否短地址
        $workers = [];

        if(!empty($data['workers'])){
            foreach($data['workers'] as $key => $value){

                if(empty($value) && !in_array($key , ['worker_positive_identity_card','worker_opposite_identity_card'])){
                    return $this->error('请填写工人姓名，电话，工种');
                }
                if(empty($value)){
                    continue;
                }
//                str_replace(url(),'',$request->json('charger_opposite_identity_card')))
                $workers[] = [
                    "name"=>isset($value['name'])?$value['name']:'',
                    "tel"=>isset($value['tel'])?$value['tel']:'',
                    "worker_positive_identity_card"=>isset($value['worker_positive_identity_card'])?str_replace($replace_str,'',$value['worker_positive_identity_card']):'',
                    "worker_opposite_identity_card"=>isset($value['worker_opposite_identity_card'])?str_replace($replace_str,'',$value['worker_opposite_identity_card']):'',
                    "worker_type"=>$value['worker_type'],
                    "worker_type_name"=>isset($value['worker_type_name'])?$value['worker_type_name']:'',
                ];
            }
        }

        foreach($workers as $key => $value){
            foreach($value as $key2 => $value2){
                if(in_array($key2 , ['name','tel','worker_type_name','worker_type'])){
                    if(empty($value2)){
                        return $this->error('请填写工人姓名，电话，工种');
                    }
                }
            }
        }

        $data['workers'] = $workers;

//        $data['business_license'] = empty($request->json('business_license'))?'':$request->json('business_license');
        $boss = UserModel::find($uid);

        if(empty($data['building_id']) || empty($data['room']) || empty($boss)){
            return $this->error('未填写报建楼盘地址或报建人');
        }

        foreach($data['project_picture'] as $key => $value){
            $data['project_picture'][$key] = str_replace($replace_str,'',$value);
        }

        $buildingInfo = DB::table('building')->where('id',$data['building_id'])->first();

        $insertArr = ['uid'=>$uid,'detail'=>serialize($data) , 'repair_type'=>$repair_type , 'building_id'=>$data['building_id'] , 'property_id'=>$buildingInfo->property_id];

        if(!empty($repair_id)){
            unset($insertArr['uid']);
            $insertArr['status'] = 0;
            $ret = DB::table('building_repair')->where('id',$repair_id)->update($insertArr);
        }else{
            $insertArr['created_at'] = date('Y-m-d H:i:s');
            $ret = DB::table('building_repair')->insert($insertArr);
        }

        if(!$ret){
            return $this->error('提交失败');
        }


//        if(!empty($buildingInfo) && !empty($buildingInfo->property_id)){
//            $this->sendWeChatNotice($buildingInfo->property_id , $boss->name , $data['room']);
//        }

        return $this->error('提交成功',0);
    }



    public function sendWeChatNotice($property_id , $boss_tel , $room) {

//        $url = "http://grandway020.com/bc/app/index.php?i=8&c=entry&m=yz_property&do=SendMessageToProperty&property_id=".$property_id."&boss_tel=".$boss_tel."&room=".urlencode($room);
//
//        $timeout = 3;
//        $ch      = curl_init();
//        // 　　//设置选项，包括URL
//        curl_setopt($ch, CURLOPT_URL, $url);
//        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
//        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
//        curl_setopt($ch, CURLOPT_HEADER, 0);
//        $output     = curl_exec($ch);
//        $curl_errno = curl_errno($ch);
//        curl_close($ch);

    }




}
