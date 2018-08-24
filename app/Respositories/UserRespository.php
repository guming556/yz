<?php

namespace App\Respositories;

use App\Modules\Employ\Models\UnionAttachmentModel;
use App\Modules\Manage\Model\LevelModel;
use App\Modules\Shop\Models\ShopModel;
use App\Modules\Shop\Models\GoodsModel;
use DB;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\WorkModel;
use App\Modules\Task\Model\WorkOfferModel;
use App\Modules\User\Model\UserModel;
use App\Modules\User\Model\CommentModel;
class UserRespository {

    /**
     * @param $designer_id
     * @param $user_id
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 设计师详细
     */
    public function getDesignerDetailByid($designer_id, $user_id) {
        $detail = ShopModel::select(
            'user_detail.uid',
            'user_detail.avatar',
            'user_detail.nickname',
            'user_detail.experience',
            'user_detail.employee_num',
            'user_detail.address',
//            'user_detail.tag',
            'user_detail.introduce',
            'user_detail.cost_of_design',
//            'user_detail.native_place',
            'user_detail.city',
            'shop.pageviews',
            'shop.total_comment',
            'shop.id as shop_id'
        )->leftjoin('user_detail', 'shop.uid', '=', 'user_detail.uid')->where('user_detail.uid', $designer_id)->first()->toArray();

        if (!empty($detail)) {
            $detail['goods_list'] = GoodsModel::select('id', 'cover', 'goods_address','view_num')->where('status', 1)->where('type', 1)->where('is_delete', 0)->where('shop_id', $detail['shop_id'])->get()->toArray();
            $view_num = 0;

            foreach ($detail['goods_list'] as $key => &$value) {
                $view_num                  = $value['view_num'];
                GoodsModel::find($value['id'])->increment('view_num', 1);
                $value['cover']            = !empty($value['cover']) ? url(str_replace('.', '{@fill}.', $value['cover'])) : '';
                $value['num_of_small_pic'] = UnionAttachmentModel::where('object_id', $value['id'])->count();
            }
            //浏览量
            $detail['pageviews']   = $view_num;
            $detail['total_goods'] = count($detail['goods_list']);

            $detail['total_comment'] = count(CommentModel::where('to_uid',$designer_id)->get()->toArray());

//            empty($detail['total_comment']) && $detail['total_comment'] = 0;
            $detail['avatar'] = empty($detail['avatar']) ? '' : url($detail['avatar']);
//            $detail['tag']    = empty($detail['tag']) ? [] : unserialize($detail['tag']);
            unset($detail['shop_id']);

            $detail['is_foucus'] = DB::table('user_focus')->where('focus_uid', $designer_id)->where('uid', $user_id)->count();

        }

        $detail = empty($detail) ? [] : $detail;
        return $detail;

    }


    /**
     * @param $worker_id
     * @param $user_id
     * @return array
     * 管家和监理详细
     */
    public function getHouseKeeperAndSuperVisorDetailByid($worker_id, $user_id) {

        $detail = ShopModel::select(
            'user_detail.uid',
            'user_detail.star',
            'user_detail.avatar',
            'user_detail.nickname',
            'user_detail.experience',
            'user_detail.employee_num',
            'user_detail.address',
            'user_detail.tag',
            'users.user_type',
            'user_detail.introduce',
            'user_detail.native_place',
            'user_detail.city',
            'shop.pageviews',
            'shop.total_comment',
            'shop.id as shop_id'
        )->leftjoin('user_detail', 'shop.uid', '=', 'user_detail.uid')->leftjoin('users', 'users.id', '=', 'user_detail.uid')->where('user_detail.uid', $worker_id)->first();



        if (!empty($detail)) {


            // 管家星级单价
            if ($detail['user_type'] == 3) {
                $config1         = LevelModel::getConfigByType(1)->toArray();
                $workerStarPrice = LevelModel::getConfig($config1, 'price');
                $detail['star']  = empty($detail['star']) ? '1' : $detail['star'];
                $unit_price      = $workerStarPrice[$detail['star'] - 1]->price;
            }

            // 监理星级单价
            if ($detail['user_type'] == 4) {
                $config1           = LevelModel::getConfigByType(2)->toArray();
                $workerStarPrice   = LevelModel::getConfig($config1, 'price');
                $detail['star']    = empty($detail['star']) ? '1' : $detail['star'];
                $star_house_keeper = $detail['star'];//这里的星级以业主的所选星级为准
                $unit_price        = $workerStarPrice[$star_house_keeper - 1]->price;
            }



            $room_config_arr = array(
                'bedroom' => '房',
                'living_room' => '厅',
                'kitchen' => '厨',
                'washroom' => '卫',
                'balcony' => '阳台'      //  阳台
            );
            $task_arr        = WorkModel::select('task_id')->where('uid', $worker_id)->where('status', '>', 1)->get();
            $tasks = [];
            if (!$task_arr->isEmpty()) {
                foreach ($task_arr as $k => $v) {
                    $task_result = TaskModel::select(
                        'p.room_config',
                        'task.status',
                        'task.project_position as project_position_id',
                        'task.id as task_id',
                        'p.region',
                        'p.project_position',
                        'c.name as favourite_style',
                        'user_detail.avatar',
                        'users.name',
                        'user_detail.nickname'
                    )
                        ->where('task.id', $v['task_id'])
                        ->where('task.status', '>=', 3)
                        ->where('task.bounty_status', 1)
                        ->leftJoin('users', 'users.id', '=', 'task.uid')
                        ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
                        ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
                        ->leftJoin('cate as c', 'c.id', '=', 'task.favourite_style')
                        ->distinct('task.id')
                        ->first();
						if($task_result!=null){
							array_push($tasks,$task_result);
						}
						
                }

            }
            $str = '';
            foreach ($tasks as $key => $value) {
 
                $room_config_decode = json_decode($value['room_config'],true);
                $str = '';
                foreach ($room_config_decode as $key2 => $value2) {

                    if (isset($room_config_arr[$key2])) {
                        $str .= $value2 . $room_config_arr[$key2];
                    }
                }
                $tasks[$key]['boss_avatar']     = url($value['avatar']);
                $tasks[$key]['favourite_style'] = empty($value['favourite_style']) ? '' : $value['favourite_style'];
                $tasks[$key]['boss_nike_name']  = !empty($value['nickname']) ? $value['nickname'] : $value['name'];
                unset($tasks[$key]['nickname']);
                unset($tasks[$key]['name']);
                unset($tasks[$key]['avatar']);
                $tasks[$key]['room_config']             = $str;
                $tasks[$key]['is_have_dismantle'] = 0;
                if ($value['status'] == 7) {
                    $work_offer_status = WorkOfferModel::select('task_id', 'project_type', 'status', 'sn', 'count_submit', 'updated_at as task_status_time', 'price')
                        ->where('task_id', $value['task_id'])
                        ->orderBy('sn', 'ASC')
                        ->get()->toArray();

                        foreach ($work_offer_status as $n => $m) {
                            if ($m['status'] == 0) {
                                unset($work_offer_status[$n]);
                            }
                            // 判断是否有拆除
                            if ($m['project_type'] == 1) {
                                $tasks[$key]['is_have_dismantle'] = 1;
                            }
                        }
                        if (empty($work_offer_status)) {
                            $last_work_offer_status = ['sn' => 0, 'status' => 0, 'count_submit' => 0, 'task_status_time' => 0, 'task_id' => $value['task_id'], 'project_type' => 0];
                        } else {
                            $last_work_offer_status = array_values($work_offer_status)[count($work_offer_status) - 1];
                        }
                        $tasks[$key]['node']   = $value['status'];
                        $tasks[$key]['sn']     = $last_work_offer_status['sn'];
                        $tasks[$key]['status'] = $last_work_offer_status['status'];

                    } else {
                        $tasks[$key]['node']   = $value['status'];
                        $tasks[$key]['sn']     = 0;
                        $tasks[$key]['status'] = 0;
                    }
                }

            $detail['avatar']       = empty($detail['avatar']) ? '' : url($detail['avatar']);
            $detail['unit_price']   = $unit_price;
            $detail['tag']          = empty($detail['tag']) ? [] : unserialize($detail['tag']);
            $detail['tasks_detail'] = $tasks;
            $detail['goods_list']   = GoodsModel::select('id', 'cover', 'goods_address', 'view_num')->where('status', 1)->where('type', 1)->where('is_delete', 0)->where('shop_id', $detail['shop_id'])->get();

            $view_num = 0;
//var_dump($detail['goods_list']->toArray());exit;
            //TODO 这里上传要注释
//            if (empty($detail['goods_list']->toArray())) {
//                $detail['goods_list'] = GoodsModel::select('id', 'cover', 'goods_address', 'view_num')->where('status', 1)->where('type', 1)->where('is_delete', 0)->where('shop_id', 1)->get();
//            }

            $detail['total_comment'] = count(CommentModel::select('id')->where('comments.to_uid', $worker_id)->get());




//var_dump(CommentModel::select('id')->where('comments.to_uid',$worker_id)->get()->toArray());exit;
            foreach ($detail['goods_list'] as $key => &$value) {
                GoodsModel::find($value['id'])->increment('view_num', 1);
                $view_num                  += $value['view_num'];
                $value['cover']            = !empty($value['cover']) ? url(str_replace('.', '{@fill}.', $value['cover'])) : '';
                $value['num_of_small_pic'] = UnionAttachmentModel::where('object_id', $value['id'])->count();
            }

            //浏览量
            $detail['pageviews']  = $view_num;
            $detail['is_foucus'] = DB::table('user_focus')->where('focus_uid', $worker_id)->where('uid', $user_id)->count();

        }
        $detail = empty($detail) ? [] : $detail;
        return $detail;

    }


    /**
     * @param $uid
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 我的直播
     */
    public function broadcastList($uid) {
        $user = UserModel::find($uid);
        if (empty($user))             return ['status' => false, 'errMsg' => '找不到用户'];
        $user_type = $user->user_type;

        $task_info_boss = [];
        //业主
        if ($user_type == 1) {
            $task_info_boss = TaskModel::select('task.id as task_id', 'task.room_config', 'task.status as node', 'c.name as favourite_style', 'p.region', 'p.project_position', 'user_detail.avatar as boss_avatar', 'user_detail.nickname as boss_nike_name')
                ->where('task.uid', $uid)
                ->where('task.user_type', 3)
                ->leftJoin('cate as c', 'c.id', '=', 'task.favourite_style')
                ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
                ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
                ->get();
        } else {

            //看监理和设计师或管家参与过的项目id,根据项目id,找到工地,找到工地对应的管家的项目id,再找到该管家的任务信息
            $task_id_other = WorkModel::where('uid', $uid)->where('status', 2)->lists('task_id');

            if ($task_id_other->isEmpty()) {

                $task_info_boss = [];

            } else {
                foreach ($task_id_other as $n => $m) {
                    $project_position       = TaskModel::find($m)->project_position;
                    $houseKeeper_task_all[] = TaskModel::select('id')->where('user_type', 3)->where('project_position', $project_position)->where('status', 7)->get()->toArray();
                }

                //三维变二维
                foreach ($houseKeeper_task_all as $key => $v) {
                    if (!empty($v[0])) $houseKeeper_task[] = $v[0];
                }

                if (!empty($houseKeeper_task)) {
                    foreach ($houseKeeper_task as $o => $p) {
                        $task_info_old[] = TaskModel::select('task.id as task_id', 'task.room_config', 'task.status as node', 'c.name as favourite_style', 'p.region', 'p.project_position', 'user_detail.avatar as boss_avatar', 'user_detail.nickname as boss_nike_name')
                            ->where('task.id', $p['id'])
                            ->leftJoin('cate as c', 'c.id', '=', 'task.favourite_style')
                            ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
                            ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
                            ->get();
                    }
                    //三维变二维
                    foreach ($task_info_old as $key => $v) {
                        $task_info_boss[] = $v[0];
                    }
                }
            }
        }

        $is_have_dismantle = 0;
        foreach ($task_info_boss as $k => $v) {

            $work_offer_status = WorkOfferModel::select('work_offer.task_id', 'work_offer.project_type', 'work_offer.status', 'work_offer.sn', 'work_offer.updated_at as task_status_time', 'work_offer.work_id')
                ->where('work_offer.task_id', $v->task_id)
                ->orderBy('work_offer.sn', 'ASC')
                ->get()->toArray();

            //返回work_offer中status为0的前一条数据
            foreach ($work_offer_status as $n => $m) {
                if ($m['status'] == 0) {
                    unset($work_offer_status[$n]);
                }
                // 判断是否有拆除
                if ($m['project_type'] == 1) {
                    $is_have_dismantle = 1;
                }
            }

            //status全部为0的情况判断下
            if (empty($work_offer_status)) {
                $last_work_offer_status = ['sn' => 0, 'status' => 0, 'task_id' => $v->id, 'task_status_time' => 0, 'project_type' => 0];
            } else {
                $last_work_offer_status = array_values($work_offer_status)[count($work_offer_status) - 1];
            }

            $task_info_boss[$k]['sn']                = $last_work_offer_status['sn'];
            $task_info_boss[$k]['status']            = floatval($last_work_offer_status['status']);
            $task_info_boss[$k]['is_have_dismantle'] = $is_have_dismantle;
            $task_info_boss[$k]['boss_avatar']       = !empty($v['boss_avatar']) ? url($v['boss_avatar']) : '';
            $task_info_boss[$k]['favourite_style']   = !empty($v['favourite_style']) ? $v['favourite_style'] : '';
        }
        return ['status' => true, 'successMsg' => $task_info_boss];
    }

    /**
     * @param $uid
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 除去自己的直播外的其他人的工地直播
     */
    public function broadcastListExclude($uid) {
        $user = UserModel::find($uid);
        if (empty($user))             return ['status' => false, 'errMsg' => '找不到用户'];
        $user_type = $user->user_type;

        $task_info_boss = [];
        //业主
        if ($user_type == 1) {
            //找到除自己之外的所有工地直播
            $task_info_boss = TaskModel::select('task.id as task_id', 'task.room_config', 'task.status as node', 'c.name as favourite_style', 'p.region', 'p.project_position', 'user_detail.avatar as boss_avatar', 'user_detail.nickname as boss_nike_name')
                ->where('task.uid', '!=', $uid)
                ->where('task.user_type', 3)
                ->where('task.hidden_status', 2)
                ->leftJoin('cate as c', 'c.id', '=', 'task.favourite_style')
                ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
                ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
                ->orderBy('task.broadcastOrderBy')
                ->get();
        } else {

            //看监理和设计师或管家参与过的项目id,根据项目id,找到工地,找到工地对应的管家的项目id,再找到该管家的任务信息
            $task_id_other = WorkModel::where('uid', $uid)->where('status', 2)->lists('task_id');

            //自己进行的工地没有的话,直接看其他人的
            if ($task_id_other->isEmpty()) {

                //找到除自己之外的所有工地直播
                $task_info_boss = TaskModel::select('task.id as task_id', 'task.room_config', 'task.status as node', 'c.name as favourite_style', 'p.region', 'p.project_position', 'user_detail.avatar as boss_avatar', 'user_detail.nickname as boss_nike_name')
                    ->where('task.user_type', 3)
                    ->where('task.hidden_status', 2)
                    ->leftJoin('cate as c', 'c.id', '=', 'task.favourite_style')
                    ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
                    ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
                    ->orderBy('task.broadcastOrderBy')
                    ->get();

            } else {
                foreach ($task_id_other as $n => $m) {
                    $task_info_boss = TaskModel::select('task.id as task_id', 'task.room_config', 'task.status as node', 'c.name as favourite_style', 'p.region', 'p.project_position', 'user_detail.avatar as boss_avatar', 'user_detail.nickname as boss_nike_name')
                        ->where('task.id', '!=', $m)
                        ->where('task.user_type', 3)
                        ->where('task.hidden_status', 2)
                        ->leftJoin('cate as c', 'c.id', '=', 'task.favourite_style')
                        ->leftJoin('project_position as p', 'p.id', '=', 'task.project_position')
                        ->leftJoin('user_detail', 'user_detail.uid', '=', 'task.uid')
                        ->orderBy('task.broadcastOrderBy')
                        ->get();
                }

            }
        }

        $task_id_arr = array_column($task_info_boss->toArray(),'task_id');

        $work_offer_status_all = WorkOfferModel::select('work_offer.task_id', 'work_offer.project_type', 'work_offer.status', 'work_offer.sn', 'work_offer.updated_at as task_status_time', 'work_offer.work_id')
            ->whereIn('work_offer.task_id', $task_id_arr)
            ->orderBy('work_offer.sn', 'ASC')
            ->get()->toArray();

        $new_all_work_offer_status = [];
        foreach($work_offer_status_all as $key => $value){
            $new_all_work_offer_status[$value['task_id']][] = $value;
        }
//var_dump($new_all_work_offer_status[]);exit;
        foreach ($task_info_boss as $k => $v) {

            $is_have_dismantle = 0;
            $task_detail_offer = isset($new_all_work_offer_status[$v->task_id]);

            //返回work_offer中status为0的前一条数据

            if($task_detail_offer){
                foreach ($new_all_work_offer_status[$v->task_id] as $n => $m) {
                    if ($m['status'] == 0) {
                        unset($new_all_work_offer_status[$v->task_id][$n]);
                    }
                    // 判断是否有拆除
                    if ($m['project_type'] == 1) {
                        $is_have_dismantle = 1;
                    }
                }
            }

            //status全部为0的情况判断下
            if (!$task_detail_offer) {
                $last_work_offer_status = ['sn' => 0, 'status' => 0, 'task_id' => $v->id, 'task_status_time' => 0, 'project_type' => 0];
            } else {
                if(empty(!empty($new_all_work_offer_status[$v->task_id]))){
                    $last_work_offer_status = ['sn' => 0, 'status' => 0, 'task_id' => $v->id, 'task_status_time' => 0, 'project_type' => 0];
                }else{
                    $last_work_offer_status = array_values($new_all_work_offer_status[$v->task_id])[count($new_all_work_offer_status[$v->task_id]) - 1];
                }
            }

            $task_info_boss[$k]['sn']                = $last_work_offer_status['sn'];
            $task_info_boss[$k]['status']            = $last_work_offer_status['status'];
            $task_info_boss[$k]['is_have_dismantle'] = $is_have_dismantle;
            $task_info_boss[$k]['boss_avatar']       = !empty($v['boss_avatar']) ? url($v['boss_avatar']) : '';
            $task_info_boss[$k]['favourite_style']   = !empty($v['favourite_style']) ? $v['favourite_style'] : '';
        }
        return ['status' => true, 'successMsg' => $task_info_boss];
    }




}
