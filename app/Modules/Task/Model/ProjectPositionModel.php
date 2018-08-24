<?php

namespace App\Modules\Task\Model;

use Illuminate\Database\Eloquent\Model;
// use DB;
class ProjectPositionModel extends Model
{
    protected $table = 'project_position';
    protected $fillable = [
        'project_position','lat','lng','uid','created_at','square','room_config','region','updated_at','chat_room_id','live_tv_url','open_live_tv_status'
    ];
    // 查询是否该用户的地址信息是否存在
    static function findLocalId($project)
    {
        $info = Self::select('id')
            // ->where('project_position', $project['project_position'])
            ->where('id',$project['project_position'])
            // ->where('lat',$project['lat'])
            // ->where('lng',$project['lng'])
            ->where('uid',$project['uid'])
            ->first();
        if(!empty($info)){
        	return $info['id'];
        }else{
            return 0;
        }

    }


    static function getProjectList($uid)
    {
        $info = Self::select('id' , 'project_position' , 'lat' , 'lng')->where('uid' , $uid)->where('deleted' , 0)->get()->toArray();
        return $info;
    }
}
