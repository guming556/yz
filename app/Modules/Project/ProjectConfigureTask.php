<?php

namespace App\Modules\Project;

use Illuminate\Database\Eloquent\Model;

class ProjectConfigureTask extends Model
{
    protected $table = 'project_configure_tasks';
    protected $fillable = [
        'project_con_list','task_id','city_id','auxiliary_id','house_keeper_id','is_sure'
    ];

}
