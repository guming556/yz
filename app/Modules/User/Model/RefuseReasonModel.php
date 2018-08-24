<?php

namespace App\Modules\User\Model;

use Illuminate\Database\Eloquent\Model;

class RefuseReasonModel extends Model
{
    protected $table = 'refuse_reason';
    protected $fillable =
        [   'id',
            'reason',
            'is_deleted'
        ];
}
