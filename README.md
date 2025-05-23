## 一. 简介 ##
markdown-blog-with-laravel是一个简单易用的Markdown博客系统，它不需要数据库，没有管理后台功能，更新博客只需要添加你写好的Markdown文件即可。它摆脱了在线编辑器排版困难，无法实时预览的缺点，一切都交给Markdown来完成，一篇博客就是一个Markdown文件。同时也支持评论，代码高亮，数学公式，页面PV统计等常用功能。提供了不同的主题样式，你可以根据自己的喜好配置，如果你想自己制作博客主题，也是非常容易的。支持整站静态导出，你完全可以导出整站静态网页部署到Github Pages。


预览

![screenshot](/img/img.png)

## 二. 功能特点 ##

1. 使用Markdown
2. 评论框
3. 代码高亮
4. PV统计
5. Latex数学公式
6. 自制主题
7. 响应式
8. 全站静态导出
9. 良好的SEO

## 三. markdown-blog优势 ##

1. 无需数据库，系统更轻量，移植更方便
2. 使用Markdown编写，摆脱后台编辑排版困难，无法实时预览的缺点
3. 可全站静态导出
4. 配置灵活，可自由开关某些功能
5. 多主题支持，可自制主题
6. 博客，分类，标签，归档

## 四. 环境要求 ##

- PHP8.2+
- mbstring扩展支持
- php.ini开启short_open_tag = On

## 五. 安装步骤 ##

1. 下载源代码
2. 解压上传到你的PHP网站根目录
3. 打开浏览器，访问网站首页
4. 上传Markdown文件到`blog`文件夹

## 六. 详细说明 ##

本地开发
cp .env.example .env
composer install
php artisan serve

## 七. 问题及bug反馈 ##


## 八. 使用者列表 ##




## 九. 感谢 ##

成长需要喜欢Markdown，喜欢写博客的各位亲们支持！感谢你们使用，感激你们对本项目的良好建议和Bug反馈。



# 已测试
- [x] 导出静态网站
- [x] wordpresss导入
- [x] 文章搜索功能
- [x] 404


# 更新日志

* 20240912
    * 升级一些依赖版本
* 20230720
    * 升级到支持php8.1.x,laravel框架升级到10.x

* 20200718
    * quest模板全部改成twig后缀,便于IDE识别模板引擎
    * 渲染页面的时候直接选择模板名,不带后缀
    * develop环境不缓存页面内容
    * phpQuery改成composer导入
    * 修复导入wordpress5.4版本的文章分类不正确的问题
