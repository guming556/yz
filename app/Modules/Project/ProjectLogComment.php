<?php

namespace App\Modules\Project;

use Illuminate\Database\Eloquent\Model;

class ProjectLogComment extends Model
{
    //
    protected $dates = ['deleted_at'];

    protected $fillable = ['project_log_id','uid','pass','comments',];
}
