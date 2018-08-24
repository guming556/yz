<?php

namespace App\Modules\Manage\Model;

use Illuminate\Database\Eloquent\Model;

class SpaceModel extends Model
{
    protected $table = 'space';

    protected $fillable = [
        'name', 'sort'
    ];
}
