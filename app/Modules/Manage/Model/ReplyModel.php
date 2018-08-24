<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/19
 * Time: 18:02
 */

namespace App\Modules\Manage\Model;

use Illuminate\Database\Eloquent\Model;

class ReplyModel extends Model
{
    protected $table = 'reply';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id','keywords','conent','created_at','updated_at'
    ];

    public $timestamps = false;
}