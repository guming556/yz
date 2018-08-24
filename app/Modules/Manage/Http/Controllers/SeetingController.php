<?php
namespace App\Modules\Manage\Http\Controllers;

use App\Http\Controllers\BasicController;
use App\Http\Controllers\ManageController;
use App\Http\Requests;
use App\Modules\Manage\Model\ProjectModel;
use App\Modules\Manage\Model\ChargeModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Validator;

class SeetingController extends ManageController
{

    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('设置管理');
        $this->theme->set('manageType', 'Seeting');
    }

    //收费单设置 - 列表
    public function getCharge()
    {
        $children = array();
        $achargeList = ChargeModel::whereRaw('deleted = 0');
        $list = $achargeList->orderBy('listorder', 'asc')->get();
        $category = $list->toArray();
        foreach ($category as $index => $row) {
            if (!empty($row['pid'])) {
                $children[$row['pid']][] = $row;
                unset($category[$index]);
            }
        }

        foreach ($category as $item) {
            switch ($item['type']){
                case 1:
                    $paternal [1][] = $item;
                    break;
                case 2:
                    $paternal [2][] = $item;
                    break;
                case 3:
                    $paternal [3][] = $item;
                    break;
            }
        }
        $data = [
            'designer' => isset($paternal[1])?$paternal[1]:[],
            'housekeeper' => isset($paternal[2])?$paternal[2]:[],
            'supervisor' => isset($paternal[3])?$paternal[3]:[],
            'children' => $children
        ];
        return $this->theme->scope('manage.seeting.charge', $data)->render();
    }

    //收费单设置 - 添加
    public function addCharge($type,$pid = 0)
    {
        $data = [
            'type' => $type,
            'pid'=> $pid
        ];
        return $this->theme->scope('manage.seeting.addCharge', $data)->render();
    }

    //收费单设置 - 编辑
    public function editCharge($id)
    {
        $cgInfo = ChargeModel::find($id);
        $data = [
            'id' => $id,
            'cgInfo' => $cgInfo
        ];
        return $this->theme->scope('manage.seeting.editCharge', $data)->render();
    }

    //收费单设置 - 更新
    public function updateCharge(Request $request)
    {
        $data = $request->except(['_token','_url']);
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
        ],[
            'title.required' => '请输入收费单名称',
            'content.required' => '请输入描述',
        ]);
        $error = $validator->errors()->all();
        if(count($error)){
            return redirect()->back()->with(['error'=>$validator->errors()->first()]);
        }
// var_dump($data);exit;
        $updateData['title'] = $data['title'];
        $updateData['price'] = intval($data['price']);
        $updateData['listorder'] = $data['listorder']?$data['listorder']:0;
        $updateData['content'] = htmlspecialchars($data['content']);
        $updateData['created_at'] = date('Y-m-d H:i:s',time());
        $updateData['updated_at'] = date('Y-m-d H:i:s',time());
        $res = ChargeModel::where('id',$data['id'])->update($updateData);
        if($res){
            return redirect('/manage/charge')->with(['message'=>'操作成功！']);
        }
        return redirect('/manage/charge')->with(['message'=>'操作失败！']);
    }

    //收费单设置 - 保存
    public function saveCharge(Request $request)
    {
        $data = $request->except(['_token','_url']);
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'content' => 'required',
            'type' => 'required|numeric|between:1,3'
        ],[
            'title.required' => '请输入收费单名称',
            'content.required' => '请输入描述',
            'type.required' => '参数错误',
            'type.numeric' => '参数错误',
            'type.between' => '参数错误'
        ]);
        $error = $validator->errors()->all();
        if(count($error)){
            return redirect()->back()->with(['error'=>$validator->errors()->first()]);
        }

        $addData['title'] = $data['title'];
        $addData['price'] = intval($data['price']);
        $addData['listorder'] = $data['listorder'];
        $addData['content'] = htmlspecialchars($data['content']);
        $addData['type'] = $data['type'];
        $addData['pid'] = isset($data['pid']) ? $data['pid'] : '0';
        $addData['created_at'] = date('Y-m-d H:i:s',time());
        $addData['updated_at'] = date('Y-m-d H:i:s',time());
        $res = ChargeModel::create($addData);
        if($res){
            return redirect('/manage/charge')->with(['message'=>'操作成功！']);
        }
        return redirect('/manage/charge')->with(['message'=>'操作失败！']);
    }

    //收费单设置 - 删除
    public function deleteCharge($id)
    {
        $cgInfo = ChargeModel::find($id);
        if(empty($cgInfo)){
            return redirect('/manage/charge')->with(['error'=>'传送参数错误！']);
        }
        $res = ChargeModel::where('id',$id)->update(['deleted'=>1]);
        if($res){
            return redirect('/manage/charge')->with(['message'=>'删除成功！']);
        }
        else{
            return redirect('/manage/charge')->with(['message'=>'删除成功！']);
        }
    }

    //工程设置 - 列表
    public function getProject(Request $request)
    {
        $arr = $request->all();
        $paginate = $request->get('paginate') ? $request->get('paginate') : 20;
        $projectList = ProjectModel::whereRaw('deleted = 0');
        $list = $projectList->orderBy('listorder', 'asc')->paginate($paginate);
        $data = $list->toArray();
        
        $children = [];
        foreach ($data['data'] as $index => $row) {
            if (!empty($row['pid'])) {
                $children[$row['pid']][] = $row;
                unset($data['data'][$index]);
            }
        }
        // $data['data'][1]['num'] = 3;
        // $data['data'][3]['num'] = 1;
        $view = [
            'merge' => $arr,
            'pjList' => $data,
            'pjChild' =>$children
        ];
        // var_dump($view['pjList']['data']);exit;

        return $this->theme->scope('manage.seeting.project', $view)->render();
    }

    //工程设置 - 添加
    public function addProject($id = 0)
    {
        $data = [
            'id' => $id
        ];
        return $this->theme->scope('manage.seeting.addProject', $data)->render();
    }

    //工程设置 - 编辑
    public function editProject($id)
    {
        $pjInfo = ProjectModel::find($id);
        $data = [
            'id' => $id,
            'pjInfo' => $pjInfo
        ];
        return $this->theme->scope('manage.seeting.editProject', $data)->render();
    }

    //工程设置 - 更新
    public function updateProject(Request $request)
    {
        $data = $request->except(['_token','_url']);
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'complete' => 'required',
            'content' => 'required'
        ],[
            'title.required' => '请输入工程名称',
            'complete.required' => '请设置默认完成时间',
            'content.required' => '请输入描述',
        ]);
        $error = $validator->errors()->all();
        if(count($error)){
            return redirect()->back()->with(['error'=>$validator->errors()->first()]);
        }

        $updateData['title'] = $data['title'];
        $updateData['listorder'] = $data['listorder']?$data['listorder']:0;
        $updateData['complete'] = $data['complete'];
        $updateData['content'] = htmlspecialchars($data['content']);
        $updateData['created_at'] = date('Y-m-d H:i:s',time());
        $updateData['updated_at'] = date('Y-m-d H:i:s',time());
        $res = ProjectModel::where('id',$data['id'])->update($updateData);
        if($res){
            return redirect('/manage/project')->with(['message'=>'操作成功！']);
        }
        return redirect('/manage/project')->with(['message'=>'操作失败！']);
    }

    //工程设置 - 保存
    public function saveProject(Request $request)
    {
        $data = $request->except(['_token','_url']);
        $validator = Validator::make($request->all(), [
            'title' => 'required',
            'complete' => 'required',
            'content' => 'required'
        ],[
            'title.required' => '请输入工程名称',
            'complete.required' => '请设置默认完成时间',
            'content.required' => '请输入描述',
        ]);
        $error = $validator->errors()->all();
        if(count($error)){
            return redirect()->back()->with(['error'=>$validator->errors()->first()]);
        }

        $addData['pid'] = $data['pid'];
        $addData['title'] = $data['title'];
        $addData['complete'] = $data['complete'];
        $addData['listorder'] = $data['listorder'];
        $addData['content'] = htmlspecialchars($data['content']);
        $addData['created_at'] = date('Y-m-d H:i:s',time());
        $addData['updated_at'] = date('Y-m-d H:i:s',time());

        $res = ProjectModel::create($addData);
        if($res){
            return redirect('/manage/project')->with(['message'=>'工程创建成功！']);
        }
        return redirect('/manage/project')->with(['message'=>'工程创建失败！']);
    }

    //工程设置 - 删除
    public function deleteProject($id)
    {
        $pjInfo = ProjectModel::find($id);
        if(empty($pjInfo)){
            return redirect('/manage/project')->with(['error'=>'传送参数错误！']);
        }
        $res = ProjectModel::where('id',$id)->update(['deleted'=>1]);
        if($res){
            return redirect('/manage/project')->with(['message'=>'删除成功！']);
        }
        else{
            return redirect('/manage/project')->with(['message'=>'删除失败！']);
        }
    }
}
