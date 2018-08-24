<?php

namespace App\Modules\Manage\Model;

use Illuminate\Database\Eloquent\Model;

class UploadModel extends Model
{
    protected $table = 'upload';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','date','order_id','name','phone','addr','created_at','updated_at'
    ];

    public $timestamps = false;
}
