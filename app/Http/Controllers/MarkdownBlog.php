<?php

namespace App\Http\Controllers;

use App\Services\Markdown;
use App\Services\Pager;
use App\Services\Twig;
use App\Services\Yaml;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Support\Facades\Cache;

class MarkdownBlog extends BaseController
{
    protected Twig $twig;
    protected bool $export = false;
    protected array $data;
    protected array $confObj;
    protected Markdown $markdown;
    /**
     * @var Request
     */
    protected Request $request;
    /**
     * @var Response
     */
    protected Response $response;
    protected Yaml $yaml;
    /**
     * @var Pager
     */
    protected Pager $pager;
    /**
     * @var string
     */
    private string $blogPath;

    public function __construct(Request $request, Response $response, Markdown $markdown, Yaml $yaml, Pager $pager)
    {

        $this->request = $request;
        $this->response = $response;
        $this->markdown = $markdown;
        $this->pager = $pager;

        $this->yaml = $yaml;
        $this->confObj = $this->yaml->getConfObject(app()->basePath() . "/conf.yaml");

        $this->twig = new Twig($this->confObj['theme']);
        $this->blogPath = app()->basePath() . "/blog";
        //初始化博客信息
        $this->markdown->initAllBlogData($this->blogPath, $this->confObj);


        //侧边栏最近博客条数
        $recentSize = $this->confObj['blog']['recentSize'];

        //是否需要所有博客
        $allBlogsForPage = $this->confObj['blog']['allBlogsForPage'];

        //所有博客
        $allBlogsList = null;
        if ($allBlogsForPage) {
            //所有博客
            $allBlogsList = $this->markdown->getAllBlogs();
        }

        //所有分类
        $categoryList = $this->markdown->getAllCategories();

        //所有标签
        $tagsList = $this->markdown->getAllTags();

        //归档月份
        $yearMonthList = $this->markdown->getAllYearMonths();

        //最近博客
        $recentBlogList = $this->markdown->getBlogsRecent($recentSize);

        //设置数据
        $this->setData("allBlogsList", $allBlogsList);
        $this->setData("categoryList", $categoryList);
        $this->setData("tagsList", $tagsList);
        $this->setData("yearMonthList", $yearMonthList);
        $this->setData("recentBlogList", $recentBlogList);
        $this->setData("confObj", $this->confObj);
        //配置名件对象别名
        $this->setData("site", $this->confObj);

    }

    /** 首页
     * @return string
     */
    public function index(): string
    {
        return $this->page(1);
    }

    public function feed(): string
    {
        return "feed";
    }

    public function page($pageNo): string
    {
        $pageNo = (int)$pageNo;
        $pageSize = $this->confObj['blog']['pageSize'];
        $pageBarSize = $this->confObj['blog']['pageBarSize'];

        $pages = $this->markdown->getTotalPages($pageSize);
        if ($pageNo <= 0) {
            $pageNo = 1;
        }

        if ($pageNo > $pages) {
            $pageNo = $pages;
        }
        $pageData = $this->markdown->getBlogsByPage($pageNo, $pageSize);

        $pagination = $this->pager->splitPage($pages, $pageNo, $pageBarSize, $this->confObj['url']);
        $this->setData("pagination", $pagination);

        $this->setData("pageName", "home");
        $this->setData("pageNo", $pageNo);
        $this->setData("pages", $pageData['pages']);
        $this->setData("blogList", $pageData['blogList']);

        return $this->render('index');
    }

    public function category($categoryId, $pageNo = 1): string
    {
        $categoryId = urldecode($categoryId);
        $pageNo = (int)$pageNo;
        $pageSize = $this->confObj['blog']['pageSize'];
        $pageBarSize = $this->confObj['blog']['pageBarSize'];

        $pages = $this->markdown->getCategoryTotalPages($categoryId, $pageSize);

        if ($pageNo <= 0) {
            $pageNo = 1;
        }

        if ($pageNo > $pages) {
            $pageNo = $pages;
        }

        $category = $this->markdown->getCategoryById($categoryId);
        $pageData = $this->markdown->getBlogsPageByCategory($categoryId, $pageNo, $pageSize);
        $pagination = $this->pager->splitPage($pages, $pageNo, $pageBarSize, $this->confObj['url'] . "category/$categoryId/");

        $this->setData("pagination", $pagination);
        $this->setData("pageName", "category");
        $this->setData("categoryId", $categoryId);
        $this->setData("category", $category);
        $this->setData("pageNo", $pageNo);
        $this->setData("pages", $pageData['pages']);
        $this->setData("blogList", $pageData['blogList']);

        return $this->render('index');
    }

    public function tags($tagId, $pageNo = 1): string
    {
        $tagId = urldecode($tagId);
        $pageNo = (int)$pageNo;
        $pageSize = $this->confObj['blog']['pageSize'];
        $pageBarSize = $this->confObj['blog']['pageBarSize'];

        $pages = $this->markdown->getTagTotalPages($tagId, $pageSize);

        if ($pageNo <= 0) {
            $pageNo = 1;
        }

        if ($pageNo > $pages) {
            $pageNo = $pages;
        }

        $tag = $this->markdown->getTagById($tagId);
        $pageData = $this->markdown->getBlogsPageByTag($tagId, $pageNo, $pageSize);
        $pagination = $this->pager->splitPage($pages, $pageNo, $pageBarSize, $this->confObj['url'] . "tags/$tagId/");

        $this->setData("pagination", $pagination);
        $this->setData("pageName", "tags");
        $this->setData("tagId", $tagId);
        $this->setData("tag", $tag);
        $this->setData("pageNo", $pageNo);
        $this->setData("pages", $pageData['pages']);
        $this->setData("blogList", $pageData['blogList']);

        return $this->render('index');
    }

    public function search(): string
    {
        $keyword = $this->request->get("keyword");
        $keyword = trim($keyword);
        $blogList = array();

        if (!empty($keyword)) {
            $blogList = $this->markdown->getBlogByKeyword($keyword);
        }

        $this->setData("pageName", "search");
        $this->setData("keyword", $keyword);
        $this->setData("blogList", $blogList);

        return $this->render('index');
    }

    /**
     * 博客详情
     * @param $blogId
     * @return Response|int|string
     */
    public function blog($blogId): Response|int|string
    {
        $blogIdMd5 = md5($blogId);
        //dd($blogIdMd5);
        $blog = $this->markdown->getBlogById($blogIdMd5);
        if ($blog == null) {
            return 404;
        }

        $this->setData("pageName", "blog");
        $this->setData("blog", $blog);

        return $this->render('detail');
    }


    //渲染页面
    private function render($tpl): string
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
        $this->response->send();
        return "";
    }

    /**
     * 按月归档下的博客列表
     * @param $yearMonthId
     * @param int $pageNo
     * @return string
     */
    public function archive($yearMonthId, int $pageNo = 1): string
    {
        $pageSize = $this->confObj['blog']['pageSize'];
        $pageBarSize = $this->confObj['blog']['pageBarSize'];

        $pages = $this->markdown->getYearMonthTotalPages($yearMonthId, $pageSize);

        if ($pageNo <= 0) {
            $pageNo = 1;
        }

        if ($pageNo > $pages) {
            $pageNo = $pages;
        }

        $yearMonth = $this->markdown->getYearMonthById($yearMonthId);
        $pageData = $this->markdown->getBlogsPageByYearMonth($yearMonthId, $pageNo, $pageSize);
        $pagination = $this->pager->splitPage($pages, $pageNo, $pageBarSize, $this->confObj['url'] . "archive/$yearMonthId/");

        $this->setData("pagination", $pagination);
        $this->setData("pageName", "archive");
        $this->setData("yearMonthId", $yearMonthId);
        $this->setData("yearMonth", $yearMonth);
        $this->setData("pageNo", $pageNo);
        $this->setData("pages", $pageData['pages']);
        $this->setData("blogList", $pageData['blogList']);

        return $this->render('index');
    }

    //设置渲染数据
    private function setData($key, $dataObj): void
    {
        $this->data[$key] = $dataObj;
    }

    /**
     * 计算缓存Key
     * @return string
     */
    private function getCacheKey(): string
    {
        return $this->confObj['theme'] . "_" . md5($this->request->url()) . ".html"; //category/1460001917
    }

}
