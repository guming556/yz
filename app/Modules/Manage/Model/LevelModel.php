<?php

namespace App\Modules\Manage\Model;

use Illuminate\Database\Eloquent\Model;

class LevelModel extends Model
{
    protected $table = 'level';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id','offer_1','offer_2','offer_3','offer_4','offer_5','type','upgrade','score','created_at','updated_at'
    ];

    public $timestamps = false;


    static function getConfigByType($type)
    {
        $info = LevelModel::where('type', $type)->select('offer_1','offer_2','offer_3','offer_4','offer_5','upgrade','score')->first();
        if (!empty($info)) {
            return $info;
        }
        return false;
    }

    //格式化数据
    static function getConfig($config , $retKey=false)
    {
        $result['upgrade'] = json_decode($config['upgrade']);
        unset($config['upgrade']);
        $result['score'] = json_decode($config['score']);
        unset($config['score']);
        foreach ($config as $key => $item) {
            $result['price'][] = json_decode($item);
        }

        if($retKey){
            return $result[$retKey];
        }

        return $result;
    }

    static function isCreate($type)
    {
        $result = LevelModel::where('type', '=', $type)->first();
        if(!$result) {
            $addData = [
                'offer_1' => '{"price":"100","company":""}',
                'offer_2' => '{"price":"110","company":""}',
                'offer_3' => '{"price":"120","company":""}',
                'offer_4' => '{"price":"130","company":""}',
                'offer_5' => '{"price":"140","company":""}',
                'type' => $type,
                'upgrade' => '{"1":"0","2":"100","3":"200","4":"300","5":"400"}',
                'score' => '{"1":"0","2":"0","3":"0","4":"0","5":"0"}',
                'created_at' => date('Y-m-d H:i:s',time()),
                'updated_at' => date('Y-m-d H:i:s',time())
            ];
            LevelModel::firstOrCreate($addData);
        }
        return true;
    }
}
