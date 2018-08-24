<?php

namespace App\Modules\Task\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Auxiliary extends Model
{
    use SoftDeletes;


    protected $dates = ['deleted_at'];
    protected $table = 'auxiliary';
    protected $fillable = ['price', 'name', 'city_id', 'content', 'province_id'];
}
