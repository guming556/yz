<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/20
 * Time: 14:26
 */


namespace App\Modules\Manage\Http\Controllers;


use App\Http\Controllers\ManageController;
use App\Modules\Manage\Model\ManagementModel;
use App\Http\Controllers\BaseController;
use App\Http\Requests;
use Illuminate\Http\Request;

class ManagementController extends ManageController
{
    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('管理员账号管理');
        $this->theme->set('manageType', 'Management');

    }

    /**
     *
     * 管理账号列表
     */

    public function managementList(Request $request)
    {
        $search = $request->all();

        $paginate = $request->get('paginate') ? $request->get('paginate') : 10 ;

        $mList = ManagementModel::whereRaw('status = 0 ');

        //禁用与启用
        if($request->get('status'))
        {
            switch($request->get('status'))
            {
                case 1:
                    $status = [0];
                    break;
                case 2:
                    $status = [1];
                    break;
                case 3:
                    $status = [2];
                    break;
            }
            $mList = $mList -> whereIn('management.status',$status);
        }
        //请输入查询



        $mList = $mList-> orderby('updated_at','desc')
            ->paginate($paginate);
        $data = $mList -> toArray();

        $view = array(

            'mList' => $data,
            'merge' => $search
        );

        return $this->theme->scope('manage.managementList', $view)->render();
    }

    /**
     *
     * 编辑并提交
     *
     */
    public function editmanagement($id)
    {
        $id = intval($id);
        $maInfo = ManagementModel::find($id);
        $data =[
            'maInfo' => $maInfo,
        ];

        return $this->theme->scope('manage.editmanagement', $data)->render();
    }

    public function savemanagement(Request $request)
    {
        $data = $request ->except('_token');
        $result['manage_id'] = $data['manage_id'];
        $result['name'] = $data['name'];
        $result['tel'] = $data['tel'];
        $result['qq'] = $data['qq'];
        $result['email'] = $data['name'];
        $result['job'] = $data['job'];
        $result['updated_at'] = date('Y-m-d H:i:s',time());
        var_dump($data['manage_id']);

        exit();

        if(isset($data['id']))//更新
        {
            $res = ManagementModel::where('id' ,$data['id'])-> update($result);
        }else{

            $result['pwd'] =  $data['pwd'];
            var_dump($data['pwd']);
            exit();
            $result['created_at'] = date('Y-m-d H:i:s',time());
            $result['status'] = "0";
            $res = ManagementModel::create($result);
        }
        if ($res) {
            return redirect('/manage/management')->with(array('message' => '操作成功'));
        } else {
            return redirect('/manage/management')->with(array('message' => '操作成功'));
        }
        return false;

    }

    /**
     * @param $id
     * @return \Illuminate\Http\JsonResponse
     * 删除
     */

    public function delmanagement($id)
    {
        $id = intval($id);
        $res = ManagementModel::destroy($id);
        if(!$res)
        {
            return response()->json(['errCode'=>0,'errMsg'=>'删除失败！']);
        }
        return response()->json(['errCode'=>1,'id'=> $id ]);
    }

    /**
     * 禁用
     *
     */
    public function enmanagement($id)
    {
        $id = intval($id);
        $res = ManagementModel::where('id' , $id ) ->update(['status'=> '1']);
        if(!$res)
        {
            return response()->json(['errCode'=>0,'errMsg'=>'删除失败！']);
        }
        return response()->json(['errCode'=>1,'id'=> $id]);
    }

}