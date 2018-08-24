<?php

namespace App\Modules\Project;

use Illuminate\Database\Eloquent\Model;

class ProjectLog extends Model
{
    protected $table = 'project_log';

    protected $fillable = ['project_log_id','uid','pass','comments',];
}
