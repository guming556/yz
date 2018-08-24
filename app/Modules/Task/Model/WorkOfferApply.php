<?php

namespace App\Modules\Task\Model;

use Illuminate\Database\Eloquent\Model;

class WorkOfferApply extends Model
{
    protected $fillable = ['task_id', 'project_position', 'sn', 'project_type', 'labor', 'boss_id', 'house_keeper_id', 'status', 'all_price', 'desc'];
}
