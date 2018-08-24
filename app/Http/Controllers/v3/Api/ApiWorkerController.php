<?php

namespace App\Http\Controllers\v3\Api;

use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;
use App\Modules\User\Model\UserModel;
use App\Modules\User\Model\UserDetailModel;
use App\Modules\User\Model\RealnameAuthModel;
class ApiWorkerController extends BaseController
{

    /**
     * 工人列表
     */
    public function getWorkerList(){
        $workers = UserModel::select('users.id' , 'users.name' , 'user_detail.realname' , 'user_detail.native_place' , 'user_detail.user_age' , 'user_detail.work_type' , 'user_detail.star' , 'user_detail.avatar' , 'realname_auth.serve_area','user_detail.experience')
            ->where('users.user_type',5)
            ->where('users.status',1)
            ->leftJoin('user_detail','users.id','=','user_detail.uid')
            ->leftJoin('realname_auth','realname_auth.uid','=','user_detail.uid')
            ->orderBy('users.sort_id')
            ->get()->toArray();

        foreach($workers as $key => &$value){
            $value['avatar'] = url($value['avatar']);
        }

        return response()->json($workers);
    }

    /***
     * @param Request $request
     * @return \Illuminate\Contracts\Routing\ResponseFactory|\Symfony\Component\HttpFoundation\Response
     * 根据工种获取列表
     */
    public function workerListByWorkType(Request $request) {
        $work_type = $request->get('work_type');
        $workers   = UserModel::select('users.id', 'users.name', 'user_detail.realname', 'user_detail.native_place', 'user_detail.user_age', 'user_detail.work_type', 'user_detail.star', 'user_detail.avatar', 'realname_auth.serve_area', 'user_detail.experience')
            ->where('users.user_type', 5)
            ->where('users.status', 1)
            ->where('user_detail.work_type', $work_type)
            ->leftJoin('user_detail', 'users.id', '=', 'user_detail.uid')
            ->leftJoin('realname_auth', 'realname_auth.uid', '=', 'user_detail.uid')
            ->orderBy('users.sort_id')
            ->get()->toArray();

        foreach($workers as $key => &$value){
            $value['avatar'] = url($value['avatar']);
        }
        $no_labor = [
            "id" => 0,
            "name" => "",
            "realname" => "不需要工人",
            "native_place" => "",
            "user_age" => "",
            "work_type" => "",
            "star" => "",
            "avatar" => "",
            "serve_area" => "",
            "experience" => "",
        ];
        array_push($workers,$no_labor);
        //排序
        $ages = array();
        foreach ($workers as $user) {
            $ages[] = $user['id'];
        }
        array_multisort($ages, SORT_ASC, $workers);
        return response()->json($workers);
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }
}
