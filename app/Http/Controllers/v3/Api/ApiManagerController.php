<?php

namespace App\Http\Controllers\v3\Api;

use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\User\Model\DistrictModel;
use App\Modules\Task\Model\TaskCateModel;
use App\Modules\Manage\Model\SpaceModel;
use App\Modules\Manage\Model\HouseModel;


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
        return response()->json($ret_list );
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
        return response()->json($ret_list );
    }

    // 获取风格列表
    public function getStyle(){
        $category_data = TaskCateModel::findByPid([0] , ['id','name']);
        return response()->json( $category_data );
    }


    // 获取空间列表
    public function getSpace(){
        $category_data = SpaceModel::select('id','name')->get()->toArray();
        return response()->json( $category_data );
    }

    // 获取户型列表
    public function getHouse(){
        $category_data = HouseModel::select('id','name')->get()->toArray();
        return response()->json( $category_data );
    }









}
