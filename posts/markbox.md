# Markbox
Markbox 是一个简易的在线 Markdown 管理工具。

纯文件存储，无需复杂的安装与数据库支持。

你可以用它当作私人 Blog，也可以用于简单的 wiki 支持。

# 安装步骤
1. 将 markbox 代码包上传至服务器
2. cd 到 markbox 目录执行 `chmod -R 777 posts` 完成安装

# 登录管理
登录地址：[./login.php](./login.php)，初始密码为：123456，
如需修改密码可打开 login.php 文件修改 `$savepwd` 的值。

# 使用到的开源库
[Parsedown.php](https://github.com/erusev/parsedown)

[editor.md](https://github.com/pandao/editor.md)