<?php

namespace App\Modules\Manage\Model;
use Illuminate\Database\Eloquent\Model;

class RoleUserModel extends Model
{
    
    protected $table = 'role_user';
    protected $fillable = [
        'user_id',
        'role_id'
    ];

    public $timestamps = false;
}
