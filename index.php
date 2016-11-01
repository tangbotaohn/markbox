<?php
require 'libraries/Markbox.php';
$app = new Markbox();
if(!$app->installed()){
	header("Location:./admin/install.php");
}

