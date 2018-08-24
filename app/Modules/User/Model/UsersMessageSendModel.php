<?php

namespace App\Modules\User\Model;

use Illuminate\Database\Eloquent\Model;

class UsersMessageSendModel extends Model
{
    protected $table = 'users_message_send';

    protected $primaryKey = 'id';

    protected $fillable = [
        'uid','message','is_read','application','title','task_id',
    ];

    public static function saveMassageData($data) {
        self::create($data);
    }
}
