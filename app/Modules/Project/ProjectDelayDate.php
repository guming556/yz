<?php

namespace App\Modules\Project;

use Illuminate\Database\Eloquent\Model;

class ProjectDelayDate extends Model
{
    protected $fillable = ['task_id','old_labor','sn','end_date','original_date','created_at','updated_at','is_sure'];
}
