# MDWiki 使用指南

![wiki icon](images/icon.jpg)  
`版本：v1.0`

### 为什么使用 Wiki？

很多难懂深奥的问题或者一项新潮的技术，研究半天终于搞懂了，
但是你不一定能思路清晰的给别人讲出来，并且过了1个月之后再回头看，可能又不懂了。

如果能有一篇总结性的文章出来，这样对自己对大家，都是很好的提升。

写的越多，自我提升的也越快。

在开始写作前您需要具备一定的 [markdown 语法知识](http://wowubuntu.com/markdown/)。

### 使用 Markdown 的优点

* 专注你的文字内容而不是排版样式。
* 轻松的导出 HTML、PDF 和本身的 .md 文件。
* 纯文本内容，兼容所有的文本编辑器与文字处理软件。
* 可读，直观。适合所有人的写作语言。

### 我该用什么工具？
所有文本编辑器都可以编写 Markdown 文件，下面推荐的工具可以支持实时预览，帮助你写出更优美的排版文档。

在 Mac OS X 平台推荐 [Mou](http://mouapp.com/)  这款免费且十分好用的
Markdown 编辑器，它支持实时预览，既左边是你编辑 Markdown 语言，右边会实时的生成预览效果。

Windows 平台下的 [Atom](https://atom.io/) 由 Github 打造，是一款文本代码编辑器并且默认
支持 Markdown，使用 快捷键 ctrl + shift + M 即可开启实时预览。

Web 端也有非常多的在线编辑工具如 [MaHua](http://mahua.jser.me/)

### 如何提交文档？
svn/git 提交：  
* 将 svn/git 版本库中的 `posts` 目录设置为自动同步到服务器。
* 再将 `posts` 版本库同步到你的电脑中。
* 在你电脑中的 `posts` 文件夹中编辑 markdown 文档提交即可。

Web 上传工具：  
如果你本地没有安装 svn/git 工具也可以用系统提供的 [Web上传工具](./upload.php) 上传文档。

MDWiki 列表会自动收录并按最后修改时间排序。
