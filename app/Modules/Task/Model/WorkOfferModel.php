<?php

namespace App\Modules\Task\Model;

use Illuminate\Database\Eloquent\Model;

class WorkOfferModel extends Model
{
    protected $table = 'work_offer';
//    public  $timestamps = false;  记录时间打开
    public $fillable = [
        'work_id',
        'title',
        'price',
        'actual_square',
        'created_at',
        'from_uid',
        'to_uid',
        'type',
        'percent',
        'task_id',
        'sn',
        'status',
        'project_type',
        'evaluate_status',
        'upload_status',
        'count_submit',
    ];
}
