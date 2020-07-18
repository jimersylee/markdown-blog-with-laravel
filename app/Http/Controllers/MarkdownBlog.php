<?php

namespace App\Http\Controllers;

use App\Services\Markdown;
use App\Services\TestService;
use App\Services\Twig;
use App\Services\Yaml;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

class MarkdownBlog extends BaseController
{
    protected $twig;
    protected $export = false;
    protected $data;
    protected $confObj;
    protected $markdown;
    /**
     * @var Request
     */
    protected $request;
    /**
     * @var Response
     */
    protected $response;
    protected $yaml;
    /**
     * @var string
     */
    private $blogPath;

    public function __construct(Request $request, Response $response, Markdown $markdown, Yaml $yaml)
    {

        $this->request = $request;
        $this->response = $response;
        $this->markdown = $markdown;

        $this->yaml = $yaml;
        $this->confObj = $this->yaml->getConfObject(app()->basePath() . "/conf.yaml");

        $this->twig = new Twig($this->confObj['theme']);
        $this->blogPath = app()->basePath() . "/blog";
        //初始化博客信息
        $this->markdown->initAllBlogData($this->blogPath, $this->confObj);

        //配置名件对象别名
        $this->setData("site", $this->confObj);
    }

    /** 首页
     * @return Response
     */
    public function index()
    {
        return new Response(TestService::test());
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
        $blogIdMd5 = md5($this->request->getRequestUri());
        $blog = $this->markdown->getBlogById($blogIdMd5);

        if ($blog == null) {
            return 404;
        }

        $this->setData("pageName", "blog");
        $this->setData("blog", $blog);

        $this->render('detail');
    }


    //渲染页面
    private function render($tpl)
    {
        $htmlPage = $this->twig->render($tpl, $this->data);

        if ($this->export) {
            return $htmlPage;
        }

        //不是cli导出
        //是否使用缓存呢
        if ($this->confObj['enableCache'] && env("APP_ENV") != "development") {
            $cacheKey = $this->getCacheKey();
            Cache::put($cacheKey, $htmlPage, GB_PAGE_CACHE_TIME);
        }
        $this->response->setContent($htmlPage);
        return $this->response->send();

    }


    //设置渲染数据
    private function setData($key, $dataObj)
    {
        $this->data[$key] = $dataObj;
    }

    /**
     * 计算缓存Key
     * @return string
     */
    private function getCacheKey()
    {
        return $this->confObj['theme'] . "_" . md5($this->request->url()) . ".html"; //category/1460001917
    }

}
