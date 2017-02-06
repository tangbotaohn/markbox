<?php

namespace Markbox;

class Api
{
    private $context;
    private $publish;
    public function __construct($context)
    {
        $this->context = $context;
        $this->publish = __BASEPATH__.'/storages/publish/';
    }

    public function state()
    {
        $check = array(
            'storages/configs',
            'storages/publish',
            'storages/themes',
            'storages/system',
        );
        $conf = array('storages' => 1, 'settings' => 1, 'users' => 1, 'siteinfo' => 1);
        foreach ($check as $dir) {
            if (!is_writable($dir) || !is_readable($dir)) {
                $conf['storages'] = 0;
                break;
            }
        }

        $settings = $this->context->config->get('settings');
        if (empty($settings)) {
            $conf['settings'] = 0;
        }
        $users = $this->context->config->get('users');
        if (empty($users)) {
            $conf['users'] = 0;
        }
        $siteinfo = $this->context->config->get('siteinfo');
        if (empty($siteinfo)) {
            $conf['siteinfo'] = 0;
        }

        return $conf;
    }

    public function siteinfo()
    {
        $website = $this->context->config->get('siteinfo');

        return $website;
    }

    public function setSiteinfo($params)
    {
        $users = $this->context->config->get('users');
        if (!empty($users)) {
            $this->context->auth->check(0);
        }
        $website = $this->context->config->get('siteinfo');
        $website = array_merge($website, $params);
        $this->context->config->set('siteinfo', $website);
        $this->context->config->save('siteinfo');

        return $this->context->config->get('siteinfo');
    }

    public function settings()
    {
        $website = $this->context->config->get('settings');

        return $website;
    }

    public function setSettings($params)
    {
        $users = $this->context->config->get('users');
        if (!empty($users)) {
            $this->context->auth->check(0);
        }
        $website = $this->context->config->get('settings');
        $website = array_merge($website, $params);
        $this->context->config->set('settings', $website);
        $this->context->config->save('settings');

        return $this->context->config->get('settings');
    }

    public function sign($params)
    {
        if (empty($params['user']) || empty($params['password'])) {
            return false;
        }

        return $this->context->auth->sign($params['user'], $params['password']);
    }

    public function users()
    {
        $this->context->auth->check(0);
        $users = $this->context->config->get('users');

        return $users;
    }

    public function addUser($params)
    {
        $users = $this->context->config->get('users');
        if (!empty($users)) {
            $this->context->auth->check(0);
        }
        if (empty($params['user'])) {
            throw new ApiException('user invalid', 101);
        }
        if (empty($params['password'])) {
            throw new ApiException('password invalid', 102);
        }
        if (!isset($params['level'])) {
            $params['level'] = 0;
        }
        $params['password'] = $this->context->auth->makePassword($params['password']);
        $users[] = $params;
        $this->context->config->set('users', $users);

        return $this->context->config->save('users');
    }

    public function delUser($params)
    {
        $this->context->auth->check(0);
        if (empty($params['user'])) {
            throw new ApiException('user invalid', 101);
        }
        $users = $this->context->config->get('users');
        $userid = null;
        foreach ($users as $k => $user) {
            if ($user['user'] == $params['user']) {
                $userid = $k;
                break;
            }
        }
        if ($userid !== null && $userid > -1) {
            unset($users[$userid]);
        }
        $users = array_values($users);
        $this->context->config->set('users', $users);

        return $this->context->config->save('users');
    }

    public function publish($params)
    {
        $this->context->auth->check(1);
        $folder = new \Tmkook\Folder();
        $folder->open(__BASEPATH__.'/storages/publish/');
        $path = (array) explode('/', $params['file']);
        $len = count($path) - 1;
        $file = $path[$len];
        unset($path[$len]);
        foreach ($path as $dir) {
            $folder->create($dir);
            $folder->entry($dir);
        }

        return $folder->addFile($file, stripslashes($params['content']));
    }

    public function remove($params)
    {
        $publish = __BASEPATH__.'/storages/publish/';
        $folder = new \Tmkook\Folder();
        $folder->open($publish);
        if (is_dir($publish.$params['file'])) {
            $folder->entry(dirname($params['file']));

            return $folder->remove(basename($params['file']), true);
        } elseif (is_file($publish.$params['file'].'.md')) {
            return $folder->delFile($params['file'].'.md');
        } else {
            return false;
        }
    }
}

class ApiException extends \Exception
{
}
