<?php

use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Route::get('/', function () {
//    return view('welcome');
//});

Route::get("/",'MarkdownBlog@index');

Route::get("feed.xml",'MarkdownBlog@feed');

Route::get("page/{pageNo}.html","MarkdownBlog@page");

Route::get("category/{categoryId}/page/{pageNo}.html","MarkdownBlog@category");
Route::get("category/{categoryId}.html","MarkdownBlog@category");

Route::get("tags/{tagId}/page/{pageNo}.html","MarkdownBlog@tags");
Route::get("tags/{tagId}.html","MarkdownBlog@tags");

Route::get("archive/{yearMonthId}/page/{pageNo}.html","MarkdownBlog@archive");
Route::get("archive/{yearMonthId}.html","MarkdownBlog@archive");

Route::get("blog/{blogId}.html","MarkdownBlog@blog");

Route::get("search","MarkdownBlog@search");

//todo:以下实现

$route['export'] 							= 'MarkdownBlog::exportSite';
$route['wp2gb'] 							= 'Wp2Gb';

$route['default_controller'] 				= 'MarkdownBlog';

$route['404_override'] 						= 'MarkdownBlog::go404';

$route['translate_uri_dashes'] = FALSE;
