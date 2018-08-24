<?php

namespace App\Modules\Manage\Model;

use Illuminate\Database\Eloquent\Model;

class HouseModel extends Model
{
    protected $table = 'house';

    protected $fillable = [
        'name', 'sort'
    ];

    static function getHouses($type)
    {
        // $info = ManagerModel::where('type', $type)->select('offer_1','offer_2','offer_3','offer_4','offer_5','upgrade','score')->first();
        // if (!empty($info)) {
        //     return $info;
        // }
        // return false;
    }





}
