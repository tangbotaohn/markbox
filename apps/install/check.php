<?php
$power['markbox/configs']['w'] = is_writable('markbox/configs');
$power['markbox/configs']['r'] = is_readable('markbox/configs');

$power['markbox/mdfiles']['w'] = is_writable('markbox/mdfiles');
$power['markbox/mdfiles']['r'] = is_readable('markbox/mdfiles');

$power['markbox/caches']['w'] = is_writable('markbox/caches');
$power['markbox/caches']['r'] = is_readable('markbox/caches');

$name = array(true=>'ok',false=>'no');
?>

<!DOCTYPE HTML>
<html lang="zh-CN">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
<meta name = "format-detection" content = "telephone=no">
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<meta name="renderer" content="webkit">
<meta name="keywords" content="markbox,install">
<meta name="description" content="安装markbox">
<title>markbox - install</title>
</head>
<body>
<h3>环境检测：</h3>
<table>
	<tr>
	<th>目录</th>
	<td>读</td>
	<th>写</th>
	</tr>
	<?php foreach($power as $k=>$v): ?>
	<tr>
	<td><?= $k ?></td>
	<td><?= $name[$v['r']] ?></td>
	<td><?= $name[$v['w']] ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<a href="?m=install&c=settings">下一步</a>
</body>
</html>

