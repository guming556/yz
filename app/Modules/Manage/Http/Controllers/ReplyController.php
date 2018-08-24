<?php
/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/4/19
 * Time: 17:48
 */
namespace App\Modules\Manage\Http\Controllers;

use App\Http\Controllers\ManageController;
use App\Http\Controllers\BaseController;
use App\Http\Requests;
use App\Modules\Manage\Model\ReplyModel;
use Illuminate\Http\Request;


class ReplyController extends ManageController
{
    public function __construct()
    {
        parent::__construct();

        $this->initTheme('manage');
        $this->theme->setTitle('回复管理');
        $this->theme->set('manageType', 'Reply');
    }

    /**
     * 回复管理列表
     *
     */
    public function replyList(Request $request)
    {
        $search = $request-> all();

        $paginate = $request->get('paginate') ? $request->get('paginate') : 10;

        $replyList = ReplyModel::select('reply.id','reply.keywords','reply.conent');

        $replyList = $replyList-> orderby('updated_at','desc')->paginate($paginate);

        $data = $replyList->toArray();

        $view = array(
            'rList' => $data,
            'merge' => $search
        );

        return $this->theme->scope('manage.replyList',$view)->render();

    }

    /**
     *
     * 删除回复信息
     *
     */
    public function delreply($id)
    {
        $id = intval($id);
        $result = ReplyModel::destroy($id);
        if(!$result)
        {
            return response()->json(['errCode'=>0,'errMsg'=>'删除失败！']);
        }
        return response()->json(['errCode'=>1,'id'=> $id ]);
    }

    /**
     * 编辑并提交回复
     *
     */
    public function upreply($id)
    {
        $reInfo = ReplyModel::find($id);
        $data = [
            'reInfo' => $reInfo
        ];
        return $this->theme->scope('manage.upreply', $data)->render();

    }
    public function savereply(Request $request)
    {
        $data = $request->except(['_token','_url']);
        $result['keywords'] = $data ['keywords'];
        $result['conent'] = $data ['content'];
        $result['updated_at'] = date('Y-m-d H:i:s',time());
        //保存
        if(isset($data['id'])) {
            //更新
            $res = ReplyModel::where('id',$data['id'])->update($result);
        } else {
            //创建
            $result['created_at'] = date('Y-m-d H:i:s',time());
            $res = ReplyModel::create($result);
        }
        if ($res) {
            return redirect('/manage/reply')->with(array('message' => '操作成功'));
        } else {
            return redirect('/manage/reply')->with(array('message' => '操作成功'));
        }
        return false;
    }
}