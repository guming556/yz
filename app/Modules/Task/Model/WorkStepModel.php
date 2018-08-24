<?php

namespace App\Modules\Task\Model;

use Illuminate\Database\Eloquent\Model;

class WorkStepModel extends Model
{
    protected $table = 'work_step';
    public  $timestamps = true;  
    public $fillable = ['uid','employ_work_id','work_id','status','created_at','updated_at','deleted_at'];
}
