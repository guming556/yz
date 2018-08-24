<?php

namespace App\Modules\Project;

use Illuminate\Database\Eloquent\Model;

class ProjectLaborChange extends Model
{
    protected $fillable = ['task_id','old_labor','new_labor','old_labor','project_type','count_refuse','list_detail','status','status_other','sn','change_date','original_date','is_confirm','created_at','updated_at'];
}
