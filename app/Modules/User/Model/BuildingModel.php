<?php

namespace App\Modules\User\Model;

use Illuminate\Database\Eloquent\Model;
use App\Modules\User\Model\CoordinateModel;
use Cache;

class BuildingModel extends Model
{
    protected $table = 'building';
    public $timestamps = false;
    protected $fillable = [
        'province_id', 'city_id', 'district_id', 'building_name','deleted'
    ];

    // province , city , district
    static function findAreaIds($type , $id = 0){
        if(Cache::has('building_'.$type.'_'.$id))
        {
            $ret = Cache::get('building_'.$type.'_'.$id);
        }else{
            if($type == 'province'){
                $list = BuildingModel::select($type.'_id')->where('deleted',0)->groupBy($type.'_id')->get()->toArray();
            }
            if($type == 'city'){
                $list = BuildingModel::select($type.'_id')->where('deleted',0)->where('province_id',$id)->groupBy($type.'_id')->get()->toArray();
            }
            if($type == 'district'){
                $list = BuildingModel::select($type.'_id')->where('deleted',0)->where('city_id',$id)->groupBy($type.'_id')->get()->toArray();
            }

//            $list = BuildingModel::select($type.'_id')->where('deleted',0)->groupBy($type.'_id')->get()->toArray();
            $ids = [];
            $ret = [];

            foreach($list as $key => $value){
                $ids[] = $value[$type.'_id'];
            }

            if(!empty($ids)){
                $ret = CoordinateModel::select('id','name')->whereIn('id',$ids)->get();
                Cache::put('building_'.$type.'_'.$id,$ret,24*60);
            }
        }
        return $ret;
    }

    //获取楼盘
    static function findBuildings($district_id){
        if(Cache::has('district_building_list_'.$district_id))
        {
            $ret = Cache::get('district_building_list_'.$district_id);
        }else{
            $ret = BuildingModel::select('id','building_name as name')->where('deleted',0)->where('district_id',$district_id)->get()->toArray();
            if(!empty($ret)){
                Cache::put('district_building_list_'.$district_id,$ret,12*60);
            }
        }
        return $ret;
    }

}
