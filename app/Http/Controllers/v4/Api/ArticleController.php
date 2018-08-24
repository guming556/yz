<?php

namespace App\Http\Controllers\v4\Api;

use App\Modules\Article\Model\ArticleCategoryModel;
use App\Modules\Manage\Model\ArticleModel;
use Illuminate\Http\Request;


use App\Http\Controllers\BaseController;

class ArticleController extends BaseController
{
    /**
     * 根据文章类型返回文章
     *
     */

    public function getInfo($type)//根据id来获取详情
    {
        if($type=="news")
        {
            $type = "新闻";
        }elseif($type=="dynamic")
        {
            $type = "行业动态";
        }
        elseif($type=="experience")
        {
            $type = "经验分享";
        }
        $data = ArticleModel::join('article_category as c', 'article.cat_id', '=', 'c.id')->
        select('article.title','article.created_at','article.content','article.view_times')
            ->where('c.cate_name',$type)->get()->toArray();
        if($data)
        {
            return $this->success($data);
        }else{
            return $this->error('操作失败',500);
        }
    }

    public function getName()//获取分类名称
    {
        $data = ArticleCategoryModel::select('article_category.id','article_category.cate_name')
            ->where('is_deleted','0')
            ->get()->toArray();
        if($data)
        {
            return $this->success($data);
        }else{
            return $this->error('操作失败',500);
        }
    }
}
