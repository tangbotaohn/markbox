# Markbox
Markbox 是一个简易的在线 Markdown 管理工具。

纯文件存储，无需复杂的安装与数据库支持。

你可以用它当作私人 Blog，也可以用于简单的 wiki 支持。

# 开发理由
反反复复搭建 Blog 无数次，写的文章也都因为没有及时备份而丢失。

使用 HEXO 固然好，但也是有不少缺点，比如安困难和墙内无法打开等问题。

于是突发奇想用了一个下午的时间做了这么个工具。

Markbox 安装简单，只需一个域名和一台支持 PHP 的主机，全部文章都是 markdown 文件，可在线编写。

后续会在用户体验上做优化，并新增打包备份功能。

# 安装步骤
1. 将 markbox 代码包上传至服务器
2. cd 到 markbox 目录执行 `chmod -R 777 posts` 完成安装

# 登录管理
登录地址：[./login.php](./login.php)，初始密码为：123456，
如需修改密码可打开 login.php 文件修改 `$savepwd` 的值。

# 使用到的开源库
[Parsedown.php](https://github.com/erusev/parsedown)

[editor.md](https://github.com/pandao/editor.md)