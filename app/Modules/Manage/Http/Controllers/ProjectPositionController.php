<?php

namespace App\Modules\Manage\Http\Controllers;

use App\Http\Controllers\ManageController;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Modules\Manage\Model\ProjectPositionModel;
use App\Modules\User\Model\TaskModel;
use App\Modules\User\Model\UserModel;
use Illuminate\Http\Request;
use App\Modules\User\Model\DistrictModel;
use Excel;

class ProjectPositionController extends ManageController
{
    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('管理员账号管理');
        $this->theme->set('manageType', 'ProjectPosition');

    }

    /**
     * 工地统计
     *
     */
    public function projectpositionList(Request $request)
    {
        $search = $request->all();
        $paginate = $request->get('paginate') ? $request->get('paginate') : 10;
        $ppList = ProjectPositionModel::select('project_position.uid','project_position.id','region','u.name','u.mobile','t.username','w.uid');

        //输入查询用户名
        if($request->get('name'))
        {
            $ppList = $ppList->where('u.name','like','%'.e($request->get('name')).'%');
        }
        //开播时间查询
//        if($request->get('time_type')){
//            if($request->get('start')){
//                $start = date('Y-m-d H:i:s',strtotime($request->get('start')));
//                $ppList = $ppList->where($request->get('time_type'),'>',$start);
//            }
//            if($request->get('end')){
//                $end = date('Y-m-d H:i:s',strtotime($request->get('end')));
//                $ppList = $ppList->where($request->get('time_type'),'<',$end);
//            }
//        }
        //地区查询
        if($request->get('province')&&$request->get('city')&&$request->get('area'))
        {
            $pro = DistrictModel::getDistrictName($request->get('province'));
            $city = DistrictModel::getDistrictName($request->get('city'));
            $area = DistrictModel::getDistrictName($request->get('area'));
            $region = $pro."".$city."".$area;
            $ppList = $ppList->where('project_position.region',$region);
        }
        //进展
        // TODO

        $ppList =$ppList->leftJoin('users as u','project_position.uid','=','u.id')
                ->leftJoin('task as t','project_position.id','=','t.project_position')
                ->leftJoin('work as w','t.id','=','w.task_id')
                ->where('project_position.deleted','0')
                ->paginate($paginate);
        $province = DistrictModel::findTree(0);// 省份
        $data = array(
            'ppList' => $ppList,
            'province' => $province,
            'paginate' => $paginate,
        );
        $data['merge'] = $search;

        return $this->theme->scope('manage.projectpositionList',$data)->render();
    }

    /**
     * 导出工地统计记录
     */
    public function projectpositionListExport($param)
    {
        $param = \CommonClass::getParamByQueryString($param);
        $pp = ProjectPositionModel::select('project_position.uid','project_position.id','region','u.name','u.mobile','t.username','w.uid')
                ->leftJoin('users as u','project_position.uid','=','u.id')
                ->leftJoin('task as t','project_position.id','=','t.project_position')
                ->leftJoin('work as w','t.id','=','w.task_id');

        if (!empty($param['start'][0])) {
            $start = substr($param['start'][0], 0, -3);
            $pp = $pp->where('project_position.created_at', '>', date('Y-m-d', $start));
        }
        if (!empty($param['end'][0])) {
            $end = substr($param['end'][0], 0, -3);
            $pp = $pp->where('project_position.created_at', '<', date('Y-m-d', $end));
        }
        $data = [
            ['工地编号', '用户昵称', '手机号', '用户名','地区']
        ];

        $i = 0;
        $result = $pp->get()->chunk(100);
        foreach ($result as $key => $chunk) {
            foreach ($chunk as $k => $v) {
                $data[$i + 1] = [
                    $v->id, $v->name, $v->mobile, $v->username, $v->region,
                ];
                $i++;
            }
        }
        Excel::create('工地统计记录', function ($excel) use ($data) {
            $excel->sheet('score', function ($sheet) use ($data) {
                $sheet->rows($data);
            });
        })->export('csv');
    }

    /**
     *
     * 工地详情
     */

    public function projectpositionDetail($id)
    {
        $id = intval($id);
        $ppInfo = ProjectPositionModel::select('project_position.*','u.name','u.mobile')
            ->leftJoin('users as u','project_position.uid','=','u.id')
            ->find($id);
        //关联信息
        $pInfo = ProjectPositionModel::select('project_position.*','t.username','t.phone','t.user_type')
            ->join('task as t','project_position.id','=','t.project_position')
            ->where('project_position.id',$id)->get()->toArray();
        $data =[
            'ppInfo' => $ppInfo,
            'pInfo' => $pInfo,
        ];


        return $this->theme->scope('manage.projectpositionDetail', $data)->render();
    }

    /**
     * 工地概述
     */

    public function projectpositonOverview(Request $request)
    {
        $time = date('Y-m-d H:i:s',time());
        $now = strtotime(date('Y-m-d', time()));
        $ppnum = ProjectPositionModel::where('deleted','0')->count();// 平台总数量

        $oldstart = date('Y-m-d H:i:s',strtotime(date('Y-m-d'.'00:00:00',time()-3600*24)));//获取昨日开始时间
        $oldend = date('Y-m-d H:i:s',strtotime(date('Y-m-d'.'00:00:00',time())));//获取昨日结束时间

        $weekstart =date("Y-m-d H:i:s",mktime(0,0,0,date("m"),date("d")-date("w")+1,date("Y"))); //获取本周开始时间
        $weekend =date("Y-m-d H:i:s",mktime(23,59,59,date("m"),date("t"),date("Y"))); //获取本周结束时间

        $monthstart=date('Y-m-d H:i:s',mktime(0,0,0,date('m'),1,date('Y'))); //获取本月开始时间
        $monthend=date('Y-m-d H:i:s',mktime(23,59,59,date('m'),date('t'),date('Y')));//获取本月结束时间

        $old_ppnum = ProjectPositionModel::where('created_at','>',$oldstart)
            ->where('created_at','<',$oldend)
            ->where('deleted','0')->count();//昨日新增数量

        $week_ppnum = ProjectPositionModel::where('created_at','>',$weekstart)
            ->where('created_at','<',$weekend)
            ->where('deleted','0')->count();//本周新增数量

        $month_ppnum = ProjectPositionModel::where('created_at','>',$monthstart)
            ->where('created_at','<',$monthend)
            ->where('deleted','0')->count();//本月设计师数量

        $maxDay = 10;
        $oneDay = 24 * 60 * 60;
        for ($i = 0; $i < $maxDay; $i++) {
            $timeArr[$i]['min'] = date('Y-m-d H:i:s', ($now - $oneDay * ($i + 1)));
            $timeArr[$i]['max'] = date('Y-m-d H:i:s', ($now - $oneDay * $i));
        }

        $timeArr = array_reverse($timeArr);
        foreach ($timeArr as $k => $v){
            $dateArr[] = date('m', strtotime($timeArr[$k]['min'])) . '-' . date('d', strtotime($timeArr[$k]['min']));
        }

        $user = ProjectPositionModel::where('created_at', '>', $timeArr[0]['min'])->where('created_at', '<', $timeArr[$maxDay - 1]['max'])->get();
        if ($user->count()){
            foreach ($user as $item){
                for ($i = 0; $i < $maxDay; $i++) {
                    if ($item->created_at > $timeArr[$i]['min'] && $item->created_at < $timeArr[$i]['max']) {
                        $arr['user'][$i][] = 1;
                    }
                }
            }
        } else {
            for ($i = 0; $i < $maxDay; $i++){
                $arr['user'][$i] = 0;
            }
        }

        if (!empty($arr['user'])){
            for ($i = 0; $i < $maxDay; $i++){
                if (isset($arr['user'][$i]) && is_array($arr['user'][$i])){
                    $arr['user'][$i] = array_sum($arr['user'][$i]);
                } else {
                    $arr['user'][$i] = 0;
                }
            }
        } else {
            for ($i = 0; $i < $maxDay; $i++){
                $arr['user'][$i] = 0;
            }
        }
        $broken = [

            'user' => $arr['user'],
        ];

        $data = [

            'time' => $time,
            'ppnum' => $ppnum,
            'old_ppnum'=> $old_ppnum,
            'week_ppnum' => $week_ppnum,
            'month_ppnum' => $month_ppnum,

            'maxDay' => json_encode($maxDay),
            'broken' => json_encode($broken),
            'dateArr' => json_encode($dateArr),

        ];

        return $this->theme->scope('manage.projectpositonOverview',$data)->render();
    }
}
