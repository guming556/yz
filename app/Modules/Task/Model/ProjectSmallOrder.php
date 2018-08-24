<?php

namespace App\Modules\Task\Model;

use Illuminate\Database\Eloquent\Model;

class ProjectSmallOrder extends Model
{
    protected $fillable = ['task_id','project_type','sn','is_confirm','offer_change_detail','offer_change_price','change_date','original_date','work_offer_apply_id','small_order_id','desc','project_position','labor','boss_id','house_keeper_id','status','sub_order_id','cash_house_keeper'];
}
