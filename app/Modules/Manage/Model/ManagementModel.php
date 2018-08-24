<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20
 * Time: 14:56
 */

namespace App\Modules\Manage\Model;

use Illuminate\Database\Eloquent\Model;

class ManagementModel extends Model
{
    protected $table = 'management';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','manage_id','pwd','name','tel','qq','email','job','status','created_at','updated_at'
    ];

    public $timestamps = false;
}