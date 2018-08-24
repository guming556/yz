<?php

namespace App\Modules\Manage\Model;

use Illuminate\Database\Eloquent\Model;

class ExplainModel extends Model
{
    protected $table = 'explain';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','title','profile','content','editor','deleted','created_at','updated_at'
    ];

    public $timestamps = false;
}
