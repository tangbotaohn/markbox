# Markbox

基于命令行的 `Markdown` 发布管理系统，使用 PHP 5.4 以上版本不需要数据库。

## 所需环境
1. 一个能够运行 PHP5.4 及以上版本的服务器。
2. 一个已经绑定到服务器的域名，如`example.com`。
3. 您的电脑需安装 PHP 5.4 及以上版本，在命令行输入 `php -v` 查看您的PHP版本。

## 部署网站

1. 下载安装包将 `website` 文件夹上传至Web服务器根目录。
2. linux或unix系统需设置 `website/storages` 目录读写权限，Windows系统可跳过这一步骤。
3. 执行 `php markbox.php -m init` 完成安装，参考「初始化管理」

## 初始化管理
本地打开终端进入到 `client` 目录执行 `php markbox.php -m init` 命令，代码如下：
```
cd ./client/
php markbox.php -m init
```
按照提示输入网站首页URL和用户名密码，首次执行会要求输入加密盐保存您的用户名密码并以这个用户名密码创建管理员帐号。提示如下：
```
Enter Url:http://example.com
Enter User:tmkook
Enter Password:******
Enter Salt:加密盐可输入任意字符
```
如果您还未设置网站信息还将继续提示您完善网站名称和网站描述，提示如下：
```
Enter sitename:网站名称
Enter description:网站描述
```
至此您已完成初始化设置，使用以下命令进行管理站点。


### 全部命令
参数解释：
* [path] 发布的文件路径，可发布目录，如 `./path/filename`
* [url] 文章URL，如 `http://example.com/posts/xx-xx`
* [field] 需修改的字段，如 `sitename`

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
