<?php

namespace App\Http\Controllers\v3\Api;

use App\Modules\Task\Model\ProjectPositionModel;
use App\Modules\Task\Model\TaskModel;
use App\Modules\Task\Model\WorkOfferModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;

use App\Modules\Task\Model\WorkModel;


use App\Respositories\TaskRespository;
use App\Respositories\UserRespository;

class ApiSupervisorController extends BaseController
{

    //注入
    protected $taskRespository;
    protected $userRespository;

    public function __construct(TaskRespository $taskRespository,UserRespository $userRespository) {
        $this->userRespository = $userRespository;
        $this->taskRespository = $taskRespository;
    }


    /**
     * Display a listing of the resource.
     * 管家提交报价
     * @return \Illuminate\Http\Response
     */
    public function supervisorSubOffer(Request $request)
    {
        $task_id = $request->json('task_id');
        $supervisor_id = $request->json('supervisor_id');

    }





    /************************************************************************************************************
     * 重构的接口
     *********************************************************************************************************/

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 根据task_id获取监理的订单详细
     */
    public function getSupervisorTaskDetail(Request $request) {
        $task_id = intval($request->get('task_id'));
        $tasks   = $this->taskRespository->getSupervisorTaskDetail($task_id);
        return response($tasks);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory
     * 监理确认（用于设计师任务的确认,或者管家工程阶段的验收流程）
     */
    public function supervisorConfirmation(Request $request) {

        $data['task_id']  = $request->json('task_id');//
        $data['from_uid'] = $request->json('user_id');  // 当为设计师任务时为业主id；当为管家任务时为业主id或者监理id
        $data['sn']       = $request->json('sn');// 查询去到那个工程阶段和工程阶段处于什么状态(管家的sn)
        foreach ($data as $key => $value) {
            if (empty($value)) {
                return response()->json(['error' => '非法参数'], '500');
            }
        }

        $curSnStatus = [];    //初始化阶段状态数组合

        //监理订单
        $supervisor_task_data = TaskModel::where('status', '<', 9)->where('id', $data['task_id'])->first();
        if (empty($supervisor_task_data)) return response()->json(['error' => '找不到监理订单'], 500);
        //根据监理工地找到管家的订单
        $project_ppsition  = $supervisor_task_data->project_position;
        $house_keeper_task = TaskModel::where('project_position', $project_ppsition)->where('status', '<', 9)->where('user_type', 3)->first();
        if (empty($house_keeper_task)) return response()->json(['error' => '找不到对应的管家订单'], 500);
        $house_keeper_work = WorkModel::where('task_id', $house_keeper_task->id)->where('status', '>', 0)->first();

        // 判断任务去到哪一个工程阶段
        $ret = WorkOfferModel::where('work_id', $house_keeper_work->id)->where('task_id', $house_keeper_task->id)
            ->where('status', '>', 0)
            ->orderBy('project_type', 'ASC')
            ->get()->toArray();

        if (empty($ret)) {
            return response()->json(['error' => '找不到任何阶段'], '500');
        }
        foreach ($ret as $key => $value) {
            // status = 1 ， 处于需要监理确认的阶段 ； status = 1.5 ， 处于需要用户确认的阶段
            if ($value['status'] == 1) {
                $curSnStatus = $value;
                break;
            }
        }

        // 数据库的记录与传递过来的阶段参数不匹配
        if ($curSnStatus['sn'] != $data['sn']) {
            return response()->json(['error' => '阶段参数不匹配或处于整改阶段'], '500');
        }

        $res_change_status = WorkOfferModel::where('task_id', $house_keeper_task->id)->where('sn', $data['sn'])->update(['status' => 1.5]);
        if ($res_change_status) return response()->json(['message' => '确认成功']);
        return response()->json(['error' => '确认失败'], 500);

    }


    /**
     * @param Request $request
     * 监理详细
     */
    public function supervisorDetail(Request $request) {

        $supervisor_id      = $request->get('supervisor_id');
        $user_id            = $request->get('user_id');
        $supervisor_detail = $this->userRespository->getHouseKeeperAndSuperVisorDetailByid($supervisor_id, $user_id);

        return response()->json($supervisor_detail);
    }
}
