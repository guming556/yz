<?php

namespace App\Modules\Employ\Models;

use Illuminate\Database\Eloquent\Model;

class EmployWorkOfferModel extends Model
{
    protected $table = 'employ_work_offer';
    protected $fillable = [
        'employ_work_id','status','sn','employ_id','from_uid','to_uid','count_submit','title','actual_square','percent','pay_to_user_cash'
    ];
}
