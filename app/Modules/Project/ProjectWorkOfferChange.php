<?php

namespace App\Modules\Project;

use Illuminate\Database\Eloquent\Model;

class ProjectWorkOfferChange extends Model
{
    protected $fillable = ['task_id','project_type','sn','offer_origin_price','offer_change_price','work_origin_price','work_change_price','offer_origin_detail','offer_change_detail','task_origin_detail','task_change_detail','created_at','updated_at'];
}
