<?php

namespace App\Modules\Advertisement\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AdImg extends Model
{
    use SoftDeletes;

    /**
     * 应该被调整为日期的属性
     * @var array
     */
    protected $fillable = ['id', 'type', 'url'];
    protected $dates = ['deleted_at'];
}
