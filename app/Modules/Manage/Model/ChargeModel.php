<?php

namespace App\Modules\Manage\Model;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class ChargeModel extends Model
{
    protected $table = 'charge';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','title','content','listorder','pid','type','created_at','updated_at','price'
    ];

    public $timestamps = false;
}
