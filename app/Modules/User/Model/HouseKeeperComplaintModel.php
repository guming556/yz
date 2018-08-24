<?php

namespace App\Modules\User\Model;

use Illuminate\Database\Eloquent\Model;

class HouseKeeperComplaintModel extends Model
{

    protected $table = 'houserKeeper_complaint_channel';

    protected $fillable = [
        'task_id', 'sn', 'worker', 'status', 'boss_name', 'boss_phone_num', 'house_name', 'house_phone_num', 'visor_name', 'visor_phone_num', 'sn_title', 'position_name', 'work_offer_id'
    ];
}
