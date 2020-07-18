<?php

namespace App\Http\Controllers;

use Illuminate\Http\Response;

class MarkdownBlog extends Controller
{
    /** 首页
     * @return Response
     */
    public function index()
    {
        return new Response("2222");
    }

    public function feed()
    {
        return "fed";
    }

    public function page($pageNo)
    {
        return $pageNo;
    }

    public function category($categoryId, $pageNo = 1)
    {
        var_dump($categoryId, $pageNo);
    }

    public function tags($tagId, $pageNo = 1)
    {
        var_dump($tagId, $pageNo);
    }

    /**
     * 博客详情
     * @param $blogId
     */
    public function blog($blogId)
    {
        $blogIdMd5 = md5($blogId);
        var_dump($blogIdMd5);
    }


}
