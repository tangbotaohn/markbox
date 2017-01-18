<?php
$context->config = new Markbox\Config('storages/configs/');

$context->theme = new Markbox\View($context->config->get('settings/theme'));

$context->page = new Markbox\Page;

$context->auth = new Markbox\Auth($context);

$context->posts = new Markbox\Posts($context);