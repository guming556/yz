<?php

namespace App\Modules\Project;

use Illuminate\Database\Eloquent\Model;

class ProjectConfigureChangeModel extends Model
{
    protected $table = 'project_list_changes';
    protected $fillable = [
        'old_labor','new_labor','handle_people','task_id','list_changes','is_sure','pay_old_worker','project_type_id','created_at','updated_at'
    ];
}

