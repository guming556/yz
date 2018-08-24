<?php

namespace App\Modules\Manage\Http\Controllers;

use App\Http\Requests;
use App\Http\Controllers\ManageController;
use Illuminate\Http\Request;
use DB;
use App\Modules\User\Model\CoordinateModel;


class RepairController extends ManageController
{

    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('主材管理');
        $this->theme->set('manageType', 'Overview');
    }

    // 物管列表
    public function property(Request $request)
    {
        $search = $request->all();
        $property = DB::table('building_property')->where('deleted',0)->paginate(10);
        $data['property'] = $property;
        $data['merge'] = $search;
        return $this->theme->scope('manage.repair.property', $data)->render();
    }

    // 楼盘列表
    public function building(Request $request)
    {
        $search = $request->all();
        $building = DB::table('building')->paginate(10);
        $list = $building->toArray()['data'];
        $ids = [];

        foreach($list as $key => $value){
            if(!in_array($value->province_id , $ids)){ $ids[] = $value->province_id; }
            if(!in_array($value->city_id , $ids)){ $ids[] = $value->city_id; }
            if(!in_array($value->district_id , $ids)){ $ids[] = $value->district_id; }
        }
//var_dump();exit;
        $id_name = [];
        if(!empty($ids)){
            $areaName = CoordinateModel::whereIn('id',$ids)->get()->toArray();
            foreach($areaName as $key => $value){
                $id_name[$value['id']] = $value['name'];
            }
        }

        $data['area'] = $id_name;
        $data['building'] = $building;
        $data['merge'] = $search;
        return $this->theme->scope('manage.repair.building', $data)->render();
    }

    //楼盘编辑和添加
    public function buildingEdit(Request $request){
        $building_id = intval($request->get('building_id'));

        $building = DB::table('building')->where('id',$building_id)->first();
//        var_dump($building);exit;
        $property = DB::connection(env('DB_WECHAT_DATABASE'))->table('ims_kppw_building_property')->get();

        $data['province'] = CoordinateModel::provinceTree();
        $data['city']     = !empty($building_id)?CoordinateModel::getDistrictName($building->city_id):[];
        $data['area']     = !empty($building_id)?CoordinateModel::getDistrictName($building->district_id):[];
//        var_dump($data['area']->toArray());exit;
        $data['building'] = $building;
        $data['property'] = $property;
        $data['building_id'] = $building_id;

        return $this->theme->scope('manage.repair.buildingEdit', $data)->render();
    }

    //获取城市或省份下级
    public function getCity(Request $request){
        $pid = intval($request->get('pid'));
        $ret = CoordinateModel::where('pid',$pid)->get()->toArray();
        echo \GuzzleHttp\json_encode($ret);exit;
    }



    //楼盘编辑提交
    public function subBuildingEdit(Request $request){
        $building_id = intval($request->get('edit_id'));
//        echo $building_id;exit;
        $property_id = intval($request->get('property'));
        $province_id = intval($request->get('province'));
        $city_id = intval($request->get('city'));
        $district_id = intval($request->get('area'));
        $building_name = $request->get('building_name');
//var_dump($district_id);exit;
        if(empty($building_id)){
            DB::table('building')->insert([
                'building_name'=>$building_name ,
                'property_id'=>$property_id ,
                'province_id'=>$province_id ,
                'city_id'=>$city_id ,
                'district_id'=>$district_id
            ]);
        }else{

            DB::table('building')->where('id',$building_id)->update(['building_name'=>$building_name , 'property_id'=>$property_id ,'province_id'=>$province_id , 'city_id'=>$city_id , 'district_id'=>$district_id]);
        }

        return redirect('/manage/building')->with(array('message' => '操作成功'));
    }


    //后台自己添加报建（页面）
    public function createRepairOrderView(Request $request){
        return $this->theme->scope('manage.repair.createRepairOrder')->render();
    }


    // 报建列表
    public function repairOrder(Request $request)
    {
        $search = $request->all();
        $repair = DB::table('building_repair as b')->leftJoin('users as u','b.uid','=','u.id')->select('u.name as boss_tel','b.*')->paginate(10);

        foreach($repair->toArray()['data'] as $key => &$value){
            $value->detail = unserialize($value->detail);
        }
//        var_dump($repair->toArray()['data']);exit;
        $data['repair'] = $repair;
        $data['merge'] = $search;
//        var_dump($data);exit;
        return $this->theme->scope('manage.repair.repairList', $data)->render();
    }

    // 通过或拒绝
    public function updateOrderStatus(Request $request)
    {
        $order_id = $request->get('order_id');
        $type = max(1,intval($request->get('type')));
        $remark = $request->get('remark');
        // 1审核通过 2拒绝
        if($type == 1){
            $ret = DB::table('building_repair')->where('id',$order_id)->update(['status'=>1]);
        }else{
            $ret = DB::table('building_repair')->where('id',$order_id)->update(['status'=>3 , 'remark'=>$remark]);
        }
        if(!empty($ret)){
            echo \GuzzleHttp\json_encode(['code'=>200,'msg'=>'操作成功']);exit;
        }else{
            echo \GuzzleHttp\json_encode(['code'=>500,'msg'=>'操作失败']);exit;
        }

    }



    //详细
//{
//"room": "房号",
//"owner_positive_identity_card": "业主身份证正面",
//"owner_opposite_identity_card": "业主身份证反面",
//"business_license": "装修公司营业执照",
//"project_picture": ["工程图纸1","工程图纸2","工程图纸3"],
//"charger_name": "负责人姓名",
//"charger_tel": "负责人电话",
//"charger_positive_identity_card": "负责人身份证正面",
//"charger_opposite_identity_card": "负责人身份证反面",
//"copy_of_work": "特种作业复印件",
//"workers": [
//{
//"name": "工人姓名",
//"tel": "工人电话",
//"worker_positive_identity_card": "身份证正面",
//"worker_opposite_identity_card": "身份证反面",
//"worker_type": "工人工种",
//"worker_type_name": "工种名称"
//}
//]
//}
    public function orderDetail(Request $request){
        $id = $request->get('order_id');

        $orderDetail = DB::table('building_repair')->find($id);
        $arr['room'] = '';
        $arr['owner_positive_identity_card'] = '';
        $arr['owner_opposite_identity_card'] = '';
        $arr['business_license'] = '';
        $arr['project_picture'] = [];
        $arr['charger_name'] = '';
        $arr['charger_tel'] = '';
        $arr['charger_positive_identity_card'] = '';
        $arr['charger_opposite_identity_card'] = '';
        $arr['copy_of_work'] = '';
        $arr['workers'] = [];
        $arr['repair_type'] = ($orderDetail->repair_type == 1)?'全房装修':'局部装修';
        $info = [];
        if(!empty($orderDetail->detail)){
            $info = unserialize($orderDetail->detail);
            if(!empty($info['room'])){
                $arr['room'] = $info['room'];
            }
            if(!empty($info['owner_positive_identity_card'])){
                $arr['owner_positive_identity_card'] = url($info['owner_positive_identity_card']);
            }
            if(!empty($info['owner_opposite_identity_card'])){
                $arr['owner_opposite_identity_card'] = url($info['owner_opposite_identity_card']);
            }
            if(!empty($info['project_picture'])){
                foreach($info['project_picture'] as $key => $value){
                    if(!empty($value)){
                        $info['project_picture'][$key] = url($value);
                    }
                }
                $arr['project_picture'] = $info['project_picture'];
            }
            if(!empty($info['charger_name'])){
                $arr['charger_name'] = $info['charger_name'];
            }
            if(!empty($info['business_license'])){
                $arr['business_license'] = url($info['business_license']);
            }
            if(!empty($info['charger_tel'])){
                $arr['charger_tel'] = $info['charger_tel'];
            }
            if(!empty($info['charger_positive_identity_card'])){
                $arr['charger_positive_identity_card'] = url($info['charger_positive_identity_card']);
            }
            if(!empty($info['charger_opposite_identity_card'])){
                $arr['charger_opposite_identity_card'] = url($info['charger_opposite_identity_card']);
            }
            if(!empty($info['copy_of_work'])){
                $arr['copy_of_work'] = url($info['copy_of_work']);
            }
            if(!empty($info['workers'])){
                foreach($info['workers'] as $key => $value){
                    if(!empty($value['worker_positive_identity_card'])){
                        $info['workers'][$key]['worker_positive_identity_card'] = url($value['worker_positive_identity_card']);
                    }
                    if(!empty($value['worker_opposite_identity_card'])){
                        $info['workers'][$key]['worker_opposite_identity_card'] = url($value['worker_opposite_identity_card']);
                    }
                }
                $arr['workers'] = $info['workers'];
            }
        }
        echo \GuzzleHttp\json_encode(['code'=>200 , 'msg'=>'请求成功' , 'data'=>$arr]);exit;


    }

    public function zipUpload(Request $request){

        $id = $request->get('id');
        if(empty($id)){
            echo '非法参数';exit;
        }
        $detail = DB::table('building_repair')->find($id);
        $filename = time().".zip";

        if(!empty($detail)){
            $info = unserialize($detail->detail);
            $detail = [
                'owner_card_1'=>['业主身份证正面',$info['owner_positive_identity_card']],
                'owner_card_2'=>['业主身份证反面',$info['owner_positive_identity_card']],
                'business_license'=>['装修公司营业执照',$info['business_license']],
                'charger_card_1'=>['负责人身份证正面',$info['charger_positive_identity_card']],
                'charger_card_2'=>['负责人身份证反面',$info['charger_opposite_identity_card']],
                'copy_of_work'=>['特种作业复印件',$info['copy_of_work']],
            ];
            if(!empty($info['project_picture'])){
                foreach($info['project_picture'] as $key => $value){
                    $detail['project_picture_'.$key] = ['施工图纸'.($key+1) , $value];
                }
            }
            if(!empty($info['workers'])){
                foreach($info['workers'] as $key => $value){
                    $detail['workers_position'.$key] = [$value['worker_type_name'].'-'.$value['name'].'身份证正面' , $value['worker_positive_identity_card']];
                    $detail['workers_opposite'.$key] = [$value['worker_type_name'].'-'.$value['name'].'身份证反面' , $value['worker_opposite_identity_card']];
                }
            }
        }

        $datalist=$detail;

        if(!file_exists($filename)){
            $zip = new \ZipArchive;
            if ($zip->open($filename, $zip::CREATE)==TRUE) {
                foreach( $datalist as $key => $val){
                    if(file_exists($val[1])){
                        $img_name = explode('.',basename($val[1]));
                        $zip->addFile( $val[1], $val[0].'.'.$img_name[1]);
                    }
                }
                $zip->close();
            }
        }
        if(!file_exists($filename)){
            exit("无法找到文件");
        }
        header("Cache-Control: public");
        header("Content-Description: File Transfer");
        header('Content-disposition: attachment; filename='.basename($filename)); //文件名
        header("Content-Type: application/zip"); //zip格式的
        header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件
        header('Content-Length: '. filesize($filename)); //告诉浏览器，文件大小
        @readfile($filename);
        exit;

    }


//    public function reg_property(){
//        header('Location: http://grandway020.com/bc/app/index.php?i=8&c=entry&m=yz_property&do=home');exit;
//    }
}
