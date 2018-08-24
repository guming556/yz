<?php

namespace App\Modules\Task\Model;

use Illuminate\Database\Eloquent\Model;

class workDesignerLog extends Model
{
    protected $fillable = ['old_uid', 'new_uid', 'task_id', 'is_refuse'];
}
