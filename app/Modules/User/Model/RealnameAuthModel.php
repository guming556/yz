<?php

namespace App\Modules\User\Model;
namespace App\Modules\User\Model;

use Illuminate\Database\Eloquent\Model;
use DB;
use Auth;
use App\Modules\Shop\Models\ShopModel;

class RealnameAuthModel extends Model
{
    protected $table = 'realname_auth';
    
    protected $fillable = [
        'uid', 'username', 'card_front_side', 'card_back_dside', 'validation_img', 'status', 'auth_time','card_type','type','realname','card_number'
    ];

    
    static function getRealnameAuthStatus($uid)
    {
        $realnameInfo = RealnameAuthModel::where('uid', $uid)->first();
        if ($realnameInfo) {
            return $realnameInfo->status;
        }
        return null;
    }

    public $transactionData;

    //  TODO
    static function createRealnameAuth($realnameInfo, $authRecordInfo, $user_type = 1)
    {   
        $status = DB::transaction(function () use ($realnameInfo, $authRecordInfo, $user_type) {
            $authRecordInfo['auth_id'] = DB::table('realname_auth')->insertGetId($realnameInfo);
            DB::table('auth_record')->insert($authRecordInfo);
            // DB::table('users')->where('id',$realnameInfo['uid'])->update(['user_type' => $user_type]);
        });
        return is_null($status) ? true : $status;
    }

    
    public function removeRealnameAuth()
    {
        $status = DB::transaction(function () {
            $user = Auth::User();
            RealnameAuthModel::where('uid', $user->id)->delete();
            AuthRecordModel::where('auth_code', 'realname')->where('uid', $user->id)->delete();
        });
        return is_null($status) ? true : $status;
    }

    
    static function realnameAuthPass($id)
    {

        $status = DB::transaction(function () use ($id) {
            RealnameAuthModel::where('id', $id)->update(array('status' => 1, 'auth_time' => date('Y-m-d H:i:s')));
            AuthRecordModel::where('auth_id', $id)
                ->where('auth_code', 'realname')
                ->update(array('status' => 1, 'auth_time' => date('Y-m-d H:i:s')));

 //         TODO 若是工作端的申请通过，则同时创建一个属于该角色的商店
            $uid = RealnameAuthModel::where('id', $id)->first();
//            var_dump($uid);exit;
            $data['uid'] = $uid['uid'];
            $data['type'] = 1;
            $data['shop_pic'] = '';
            $data['shop_name'] = $uid['username'];
            $data['shop_desc'] = '';
//            TODO 这两个参数暂时不管了
            $data['province'] = 0;
            $data['city'] = 0;
            UserModel::where('id',$data['uid'])->update( array('status'=>1,'email_status'=>2));
            // UserDetailModel::where('id',$data['uid'])->update( array('status'=>1,'email_status'=>2));
            DB::table('user_detail')->where('uid',$data['uid'])->update(['realname'=>$uid['realname'] , 'nickname'=>$uid['realname']]);
            ShopModel::createShopInfo($data);
        });

        return is_null($status) ? true : $status;
    }

    
    static function realnameAuthDeny($id)
    {
        $status = DB::transaction(function () use ($id) {
            RealnameAuthModel::where('id', $id)->update(array('status' => 2));
            AuthRecordModel::where('auth_id', $id)
                ->where('auth_code', 'realname')
                ->update(array('status' => 2));
        });

        return is_null($status) ? true : $status;
    }


}
