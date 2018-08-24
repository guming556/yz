<?php

namespace App\Modules\User\Model;
use Cache;
use Illuminate\Database\Eloquent\Model;

class CoordinateModel extends Model
{
    protected $table = 'coordinate';
    public $timestamps = false;
    protected $fillable = [
        'id','pid', 'name', 'lat', 'lng'
    ];


    static function findTree(){
        if(Cache::has('coordinate_list'))
        {
            $ret = Cache::get('coordinate_list');
        }else{
            $arr = [];
            $list = CoordinateModel::select('id','name','lat','lng','pid','level','pinyin')->where('level', '<=', 3)->where('level', '>', 1)->orderBy('level', 'asc')->get()->toArray();

            foreach ($list as $key => $value) {
                if ($value['level'] == 2) {
                    $value['child']    = [];
                    $arr[$value['id']] = $value;
                } else {
                    $arr[$value['pid']]['child'][] = $value;
                }
            }

            $ret = array_values($arr);

            Cache::put('coordinate_list',$ret,24*60);
        }
        return $ret;
    }


    static function findTreeProvince(){
        if(Cache::has('coordinate_province_tree_list'))
        {
            $list = Cache::get('coordinate_province_tree_list');
        }else{
            $list = CoordinateModel::select('id','name','lat','lng','pid','level','pinyin')->where('level', 1)->get()->toArray();
            Cache::put('coordinate_province_tree_list',$list,24*60);
        }
        return $list;
    }

    static function findTreeById($pid){
        if(Cache::has('coordinate_tree_list'.$pid))
        {
            $list = Cache::get('coordinate_tree_list'.$pid);
        }else{
            $list = CoordinateModel::select('id','name','lat','lng','pid','level','pinyin')->where('pid', $pid)->get()->toArray();
            Cache::put('coordinate_tree_list'.$pid,$list,24*60);
        }
        return $list;
    }

    static function findTreeAndroid(){
        if(Cache::has('coordinate_list_Android'))
        {
            $ret = Cache::get('coordinate_list_Android');
        }else{
            $list = CoordinateModel::select('id','name','lat','lng','pid','level','pinyin')->where('level', 2)->orderBy('pinyin', 'asc')->orderBy('pid','asc')->get()->toArray();
            $ret = array_values($list);
            Cache::put('coordinate_list_Android',$ret,24*60);
        }
        return $ret;
    }


    static function workCity(){

        $list = CoordinateModel::select('id','name','lat','lng','pid','level','pinyin')->where('level', 2)->where('work_select',1)->orderBy('pinyin', 'asc')->orderBy('pid','asc')->get()->toArray();
        $ret = array_values($list);
        return $ret;
    }

    static function provinceTree(){
        if(Cache::has('coordinate_province_tree'))
        {
            $ret = Cache::get('coordinate_province_tree');
        }else{
            $ret = CoordinateModel::select('id','name')->where('pid', 0)->get()->toArray();
            Cache::put('coordinate_province_tree',$ret,24*60);
        }
        return $ret;
    }

    static function getDistrictName($id){
        $ret = CoordinateModel::select('id','name')->find($id);
        return $ret;
    }

}
