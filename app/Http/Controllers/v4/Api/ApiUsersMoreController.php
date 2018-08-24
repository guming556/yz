<?php

namespace App\Http\Controllers\v4\Api;

use App\Modules\Manage\Model\LevelModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\WorkModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\User\Model\UserFocusModel;
use App\Modules\User\Model\TagsModel;
use App\Http\Controllers\UserCenterController;

// use App\Modules\Employ\Models\UnionAttachmentModel;
// use App\Modules\Manage\Model\ConfigModel;
// use App\Modules\Manage\Model\ServiceModel;
// use App\Modules\Order\Model\ShopOrderModel;
// use App\Modules\Shop\Models\GoodsModel;
// use App\Modules\Shop\Models\ShopModel;
// use App\Modules\Task\Model\TaskAttachmentModel;
// use App\Modules\Task\Model\TaskCateModel;
// use App\Modules\Task\Model\TaskFocusModel;
// use App\Modules\Task\Model\TaskModel;
// use App\Modules\Task\Model\TaskTypeModel;
// use App\Modules\Task\Model\WorkModel;
// use App\Modules\User\Http\Requests\PubGoodsRequest;
// use App\Modules\User\Model\AttachmentModel;
// use App\Modules\User\Model\CommentModel;
// use App\Modules\User\Model\DistrictModel;
// use App\Modules\User\Model\TagsModel;
// use App\Modules\User\Model\UserFocusModel;
// use App\Modules\User\Model\UserModel;
// use Illuminate\Support\Facades\Auth;

// use Illuminate\Support\Facades\DB;
// use Illuminate\Support\Facades\Redirect;
// use Illuminate\Support\Facades\Session;
// use Theme;
// use App\Modules\User\Model\UserDetailModel;
class ApiUsersMoreController extends BaseController
{

    //取消关注
    public function userFocusDelete(Request $request) {
        $user_id   = $request->json('user_id');
        $focus_uid = $request->json('focus_uid');

        $result = UserFocusModel::where('uid', $user_id)->where('focus_uid', $focus_uid)->delete();
        if (empty($result)) {
            return $this->error('取消失败');
        }
        return $this->error('已取消',0);
    }



    /**
     * @param user_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 我的关注
     */
    public function userFocus(Request $request) {

        $user_id    = $request->get('user_id');
        $user_type  = !empty($request->get('user_type')) ? $request->get('user_type') : 2;
        $focus_data = UserFocusModel::select('user_focus.focus_uid', 'user_focus.created_at', 'ud.avatar','ud.realname', 'ud.nickname as nickname', 'us.user_type', 'ud.star', 'ud.cost_of_design', 'realname_auth.serve_area')
            ->where('user_focus.uid', $user_id)
            ->where('us.user_type', $user_type)
            ->where('us.status', 1)
            ->join('user_detail as ud', 'user_focus.focus_uid', '=', 'ud.uid')
            ->join('realname_auth', 'user_focus.focus_uid', '=', 'realname_auth.uid')
            ->leftjoin('users as us', 'user_focus.focus_uid', '=', 'us.id')
            ->get()->toArray();

        if (empty($focus_data)) {
            $result = ['focus_list' => []];
        } else {
            foreach ($focus_data as $k => $v) {

                if ($user_type == 3) {
                    $config1 = LevelModel::getConfigByType(1)->toArray();
                } elseif ($user_type == 4) {
                    $config1 = LevelModel::getConfigByType(2)->toArray();
                }
                if (!empty($config1)) {
                    $workerStarPrice     = LevelModel::getConfig($config1, 'price');
                    $res_price           = $workerStarPrice[$v['star'] - 1]->price;
                    $v['cost_of_design'] = !empty($res_price) ? (int)$res_price : 0;
                }
                $v['avatar']         = !empty($v['avatar']) ? url($v['avatar']) : '';
                $v['nickname']       = !empty($v['nickname']) ? $v['nickname'] : $v['realname'];
                unset($v['realname']);
                $result['focus_list'][] = $v;
            }
        }

        return $this->success($result);
    }



    /**
     * @param user_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 我的关注(排除)
     */
    public function userFocusRemove(Request $request) {

        $user_id     = $request->get('user_id');
        $position_id = $request->get('position_id');
        $user_type   = !empty($request->get('user_type')) ? $request->get('user_type') : 2;
        $focus_data  = UserFocusModel::select('user_focus.focus_uid', 'user_focus.created_at', 'ud.avatar', 'ud.realname', 'ud.nickname as nickname', 'us.user_type', 'ud.cost_of_design', 'realname_auth.serve_area')
            ->where('user_focus.uid', $user_id)
            ->where('us.user_type', $user_type)
            ->join('user_detail as ud', 'user_focus.focus_uid', '=', 'ud.uid')
            ->join('realname_auth', 'user_focus.focus_uid', '=', 'realname_auth.uid')
            ->leftjoin('users as us', 'user_focus.focus_uid', '=', 'us.id')
            ->get()->toArray();

        if (empty($focus_data)) {
            $result = ['focus_list' => []];
        } else {
            //找到该工地异常结单的订单
            $id_all = TaskModel::select('work.uid as designer_uid')
                ->where('task.uid', $user_id)
                ->where('task.project_position', $position_id)
                ->where('task.user_type', 2)
                ->where('task.status', 9)
                ->leftJoin('work', 'work.task_id', '=', 'task.id')
                ->leftJoin('project_position', 'project_position.id', '=', 'task.project_position')
                ->distinct('work.uid')
                ->get();//找到业主所有的单

            foreach ($focus_data as $k => $v) {
                foreach ($id_all as $n => $m) {
                    if ($v['focus_uid'] == $m['designer_uid']) {
                        unset($focus_data[$k]);
                    }
                }
            }

            foreach ($focus_data as $k => $v) {
                $v['avatar']   = !empty($v['avatar']) ? url($v['avatar']) : '';
                $v['nickname'] = !empty($v['nickname']) ? $v['nickname'] : $v['realname'];
                unset($v['realname']);
                $result['focus_list'][] = $v;
            }
        }
        return $this->success($result);
    }

}
