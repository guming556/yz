<?php

namespace App\Modules\User\Model;

use Illuminate\Database\Eloquent\Model;

class ProjectConfigureModel extends Model
{
    protected $table = 'project_configure_list';
    protected $dates = ['deleted_at'];
    protected $fillable = [
        'name','provice_id', 'cardnum', 'unit', 'num','desc','price','city_id','work_type','project_type','pid','is_deleted'
    ];

}
