<?php

$context->config = new Markbox\Config('storages/configs/');
$context->theme = new Markbox\View($context);
$context->auth = new Markbox\Auth($context);
$context->posts = new Markbox\Posts($context);
