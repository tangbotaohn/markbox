<?php
/**
 * Slim Framework (http://slimframework.com).
 *
 * @link      https://moredoo.com
 *
 * @copyright Copyright (c) 2011-2015 Moredoo
 * @license   https://github.com/slimphp/PHP-View/blob/master/LICENSE.md (MIT License)
 */

/**
 * Php Json View.
 *
 * Render Restful api
 */
class UserToken
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
        @session_start();
    }

    public function build($user)
    {
        $setting = $this->container->get('settings');
        if ($setting['debug']) {
            $token = $setting['test_token'];
        } else {
            $token = md5(json_encode($user).time());
        }
        $_SESSION[$token] = $user;

        return $token;
    }

    public function get($token)
    {
        return $_SESSION[$token];
    }

    public function update(array $user)
    {
        $token = $this->container->request->getParam('token');
        $_SESSION[$token] = $user;
    }

    public function check()
    {
        $token = $this->container->request->getParam('token');
        if (empty($token)) {
            throw new TokenException('token is empty', 101);
        } elseif (!$user = $this->get($token)) {
            throw new TokenException('not found token', 102);
        }

        return $user;
    }
}
