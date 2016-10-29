<?php
$settings = $app->config('settings');
if(!empty($settings)){
	exit('installed');
}
$name = array(true=>'ok',false=>'no');
$files = array(
	'markbox/configs',
	'markbox/mdfiles',
	'markbox/caches'
);
$power = array();
$next = true;
foreach ($files as $key => $value) {
	$power[$key]['w'] = is_writable($value);
	$power[$key]['r'] = is_readable($value);
	if(!$power[$key]['w'] || !$power[$key]['r']){
		$next = false;
	}
}

//installing
if($next && !empty($_POST)){
	$settings = $_POST['settings'];
	$settings['salt'] = md5(time());
	$app->setConfig('settings',$settings);

	$users = $_POST['user'];
	$users['password'] = md5($users['password'].$settings['salt']);
	$app->setConfig('users',$users);
	header('Location:./');
}
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
<?php if($_GET['m'] == 'settings'): ?>
站点信息：
<form method="POST">
	<div>网站标题：<input type="text" name="settings[sitename]"></div>
	<div>隐私：<input type="checkbox" value="1" name="settings[spider]" checked="checked"> 允许搜索引擎收录我的网站</div>

	<div>用户名：<input type="text" name="user[username]"></div>
	<div>邮箱：<input type="text" name="user[email]"></div>
	<div>密码：<input type="text" name="user[password]"></div>
	<div><button>立即安装</button></div>
</form>

<?php else: ?>
<h3>环境检测：</h3>
<table>
	<tr>
	<th>目录</th>
	<td>读</td>
	<th>写</th>
	</tr>
	<?php foreach($files as $k=>$v): ?>
	<tr>
	<td><?= $v ?></td>
	<td><?= $name[$power[$k]['r']] ?></td>
	<td><?= $name[$power[$k]['w']] ?></td>
	</tr>
	<?php endforeach; ?>
</table>
<?php if($next): ?><a href="?m=settings">下一步</a><?php endif; ?>

<?php endif; ?>



</body>
</html>

