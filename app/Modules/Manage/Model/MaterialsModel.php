<?php


namespace App\Modules\Manage\Model;

use Illuminate\Database\Eloquent\Model;

class MaterialsModel extends Model
{
    protected $table = 'materials';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','name','content','count','price','sell_num','status','created_at','updated_at'
    ];

    public $timestamps = false;
}