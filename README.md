# Markbox 介绍

基于命令行的 Markdown 发布管理系统，没有管理后台，不需要数据库，一篇文章就是一个 Markdown 文件。
你可以使用你喜欢的编辑器编写，文件就存在你的电脑里，可通过终端命令或FTP等进行发布。

支持子账号，本地备份，主题插件等丰富的自定义配置，
适用于个人博客、开发文档、企业WIKI等场景。

官网：[http://dooim.com/](http://dooim.com/)

### 全部命令预览
参数解释：
* [path] 发布的文件，可发布文件或目录，如 `./path/filename`
* [url] 文章URL或posts参数，如 `http://example.com/posts/folder-filename` 或 `folder-filename`
* [field] 修改的字段名，如 `sitename`

```
# 初始化：
php markbox.php -m init
# 发布文章：
php markbox.php -m publish -p [path]
# 删除文章：
php markbox.php -m remove -p [url]
# 查看用户：
php markbox.php -m users
添加用户：
php markbox.php -m users -p add
删除用户：
php markbox.php -m users -p del
查看设置：
php markbox.php -m settings
修改设置：
php markbox.php -m settings -p [field]
查看站点信息：  
php markbox.php -m siteinfo
设置站点信息：  
php markbox.php -m siteinfo -p [field]
备份：  
php markbox.php -m backup
```
