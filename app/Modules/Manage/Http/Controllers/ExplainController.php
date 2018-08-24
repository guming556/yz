<?php

namespace App\Modules\Manage\Http\Controllers;

use App\Http\Controllers\ManageController;
use App\Http\Requests;
use App\Http\Controllers\BaseController;
use App\Modules\Manage\Model\ExplainModel;
use Illuminate\Http\Request;

class ExplainController extends ManageController
{
    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('说明设置');
        $this->theme->set('manageType', 'Explain');
    }

    //列表
    public function getExplain(Request $request)
    {
        $arr = $request->all();
        $paginate = $request->get('paginate') ? $request->get('paginate') : 10;
        $projectList = ExplainModel::whereRaw('deleted = 0');
        $list = $projectList->orderBy('updated_at', 'desc')->paginate($paginate);
        $data = $list->toArray();

        $view = [
            'merge' => $arr,
            'exList' => $data
        ];
        return $this->theme->scope('manage.seeting.explain', $view)->render();
    }

    //添加
    public function addExplain()
    {
        return $this->theme->scope('manage.seeting.addExplain')->render();
    }

    /*
     * 保存设置
     * id为空则创建，不为空则更新
     */
    public function saveExplain(Request $request)
    {
        $data = $request->except(['_token','_url']);
        $result['title'] = $data['title'];
        $result['profile'] = $data['profile'];
        $result['content'] = htmlspecialchars($data['content']);
        $result['editor'] = $this->theme->getManager();
        $result['updated_at'] = date('Y-m-d H:i:s',time());
        $result['deleted'] = 0;


        if(mb_strlen($data['content']) > 4294967295/3){
            if (!empty($error)) {
                return redirect('/manage/explain')->withErrors(array('message' => '文章内容太长，建议减少上传图片'));
            }
        }

        //保存
        if(isset($data['id'])) {
            //更新
            $res = ExplainModel::where('id',$data['id'])->update($result);
        } else {
            //创建
            $result['created_at'] = date('Y-m-d H:i:s',time());
            $res = ExplainModel::create($result);
        }

        if ($res) {
            return redirect('/manage/explain')->with(array('message' => '操作成功'));
        } else {
            return redirect('/manage/explain')->with(array('message' => '操作成功'));
        }
        return false;
    }

    //浏览
    public function explainDetail($id)
    {
        $exInfo = ExplainModel::find($id);
        $data = [
            'exInfo' => $exInfo
        ];
        return $this->theme->scope('manage.seeting.explainDetail', $data)->render();
    }

    //编辑
    public function editExplain($id)
    {
        $exInfo = ExplainModel::find($id);
        $data = [
            'exInfo' => $exInfo
        ];
        return $this->theme->scope('manage.seeting.editEx', $data)->render();
    }

    /*
     * 删除
     * 更新 deleted 字段为 1
     */
    public function deleteExplain($id)
    {
        $pjInfo = ExplainModel::find($id);
        if(empty($pjInfo)){
            return redirect('/manage/explain')->with(['error'=>'传送参数错误！']);
        }
        $res = ExplainModel::where('id',$id)->update(['deleted'=>1]);
        if($res){
            return redirect('/manage/explain')->with(['message'=>'删除成功！']);
        }
        else{
            return redirect('/manage/explain')->with(['message'=>'删除失败！']);
        }
    }
}
