<?php

namespace App\Modules\Manage\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ProjectModel extends Model
{
    protected $table = 'project';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','title','complete','content','listorder','pid','created_at','updated_at'
    ];

    public $timestamps = false;
}
