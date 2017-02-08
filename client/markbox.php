<?php

$args = getopt('m:p:');
if (empty($args['m'])) {
    response('-m invalid');
	exit;
}

$http = new ApiRequest();
switch ($args['m']):
    case 'init':
        cleantmp();
        echo 'Enter Url:';
        $url = trim(trim(fgets(STDIN)), '/');
        echo 'Enter User:';
        $user = trim(fgets(STDIN));
        $password = fgetpwd();
        $save = array('url' => $url, 'user' => $user, 'password' => $password);
        savetmp(json_encode($save));
        $state = json_decode($http->get('rest/state'));
        if ( ! $state->data->storages) {
            response('run chmod -r 777 storages');
			exit;
        }
        if (!$state->data->users) {
            if (!$state->data->settings) {
                echo 'Enter Salt:';
                $salt = md5(uniqid().trim(trim(fgets(STDIN)), '/'));
                $params = array('theme' => 'default', 'host' => $url, 'salt' => $salt);
                response($http->post('rest/setSettings', $params));
            }
            $params = array('user' => $user, 'password' => $password, 'level' => 0);
            response($http->post('rest/addUser', $params));
        }
        $params = array('user' => $user, 'password' => $password);
        response($http->post('rest/sign', $params));
        if (!$state->data->siteinfo) {
            echo 'Enter sitename:';
            $sitename = trim(fgets(STDIN));
            echo 'Enter description:';
            $description = trim(fgets(STDIN));
            $params = array('sitename' => $sitename, 'description' => $description);
            response($http->post('rest/setSiteinfo', $params));
        }
    break;
    case 'state':
        response($http->get('rest/state'));
    break;
    case 'users':
        if (empty($args['p'])) {
            response($http->get('rest/users'));
        } elseif ($args['p'] == 'add') {
            $params = array();
            echo 'Enter User:';
            $params['user'] = trim(fgets(STDIN));
            echo 'Enter Password:';
            $params['password'] = trim(fgets(STDIN));
            echo 'Enter Level:';
            $params['level'] = intval(trim(fgets(STDIN)));
            response($http->post('rest/addUser', $params));
        } elseif ($args['p'] == 'del') {
            $params = array();
            echo 'Enter User:';
            $params['user'] = trim(fgets(STDIN));
            response($http->post('rest/delUser', $params));
        }
    break;
    case 'siteinfo':
        if (empty($args['p'])) {
            response($http->get('rest/siteinfo'));
        } else {
            $params = array();
            echo $args['p'].':';
            $params[$args['p']] = trim(fgets(STDIN));
            response($http->post('rest/setSiteinfo', $params));
        }
    break;
    case 'settings':
        if (empty($args['p'])) {
            response($http->get('rest/settings'));
        } else {
            $params = array();
            echo $args['p'].':';
            $params[$args['p']] = trim(fgets(STDIN));
            response($http->post('rest/setSettings', $params));
        }
    break;
    case 'publish':
        $path = realpath($args['p']);
        if (is_dir($path)) {
            $folder = new Folder();
            $folder->open($path);
            $files = $folder->getSubFiles();
			$publishes = array();
            foreach ($files as $file) {
                $filename = trim(str_replace($path,'',$file),'/');
				$publishes[] = array('filename'=>$filename,'file'=>$file);
				response($filename);
            }
			unset($files,$file);
			response("total: (".count($publishes).") files confirm publish? (Y|N)");
			$isConfirm = trim(fgets(STDIN));
			if($isConfirm == 'y'){
				foreach($publishes as $item){
					$params = array('file' => $item['filename'], 'content' => file_get_contents($item['file']));
					$result = $http->post('rest/publish', $params);
					echo $item['filename']."  ";
					response($result);
				}
			}else{
				response("Canceled");
			}
        } else {
            $params = array('file' => basename($path), 'content' => file_get_contents($path));
            response($http->post('rest/publish', $params));
        }
    break;
    case 'remove':
        if (empty($args['p']) || strpos($args['p'], 'posts') < 0) {
            response('-p invalid, please paste posts url');
			exit;
        }
        $url = $args['p'];
        $params = parse_url($url);
        $path = str_replace('-', '/', basename($params['path']));
		response("confirm remove {$path}? (Y|N)");
		$isConfirm = trim(fgets(STDIN));
		if($isConfirm == 'y'){
			$params = array('file' => $path);
			response($http->post('rest/remove', $params));
		}else{
			response("Canceled");
		}
    break;
	case 'backup':
		$url = $http->get('rest/backup');
		exec('wget '.$url);
	break;
    default:
echo "
-m init  登录
-m state 状态
-m users 用户列表
-m users -p add 添加用户,level=0 is admin
-m users -p del 删除用户
-m siteinfo 站点信息
-m siteinfo -p [field] 修改站点信息\r\n
";
    break;
endswitch;

function savetmp($data)
{
    $tmpfile = sys_get_temp_dir().'markbox_userdata.tmp';
    file_put_contents($tmpfile, $data);
}

function gettmp()
{
    $tmpfile = sys_get_temp_dir().'markbox_userdata.tmp';

    return file_get_contents($tmpfile);
}

function cleantmp()
{
    savetmp('');
    file_put_contents(sys_get_temp_dir().'markbox_cookoie.txt', '');
}

function response($data)
{
    $msg = json_decode($data, true);
    if (empty($msg)) {
        echo $data." \r\n";
    } elseif ($msg['code'] > 0) {
        if (is_array($msg['data'])) {
            print_r($msg['data']);
            echo "\r\n";
        } else {
            echo $msg['data']." \r\n";
        }
    } else {
        echo "Errno:{$msg['errno']} Error:{$msg['data']} \r\n";
    }
}

function fgetpwd($prompt = 'Enter Password:')
{
    if (preg_match('/^win/i', PHP_OS)) {
        $vbscript = sys_get_temp_dir().'prompt_password.vbs';
        file_put_contents(
      $vbscript, 'wscript.echo(InputBox("'
      .addslashes($prompt)
      .'", "", "password here"))');
        $command = 'cscript //nologo '.escapeshellarg($vbscript);
        $password = rtrim(shell_exec($command));
        unlink($vbscript);

        return $password;
    } else {
        $command = "/usr/bin/env bash -c 'echo OK'";
        if (rtrim(shell_exec($command)) !== 'OK') {
            trigger_error("Can't invoke bash");

            return;
        }
        $command = "/usr/bin/env bash -c 'read -s -p \""
      .addslashes($prompt)
      ."\" mypassword && echo \$mypassword'";
        $password = rtrim(shell_exec($command));
        echo "\n";

        return $password;
    }
}

class ApiRequest
{
    private $ch;
    public $baseUrl;
    public $responseInfo;
    public function __construct()
    {
        $tmp = json_decode(gettmp());
        // set the base URL for the API endpoint
        $this->baseUrl = $tmp->url;
        // initialize the cURL resource
        $this->ch = curl_init();
        // set cURL and credential options
        curl_setopt($this->ch, CURLOPT_URL, $this->baseUrl);
        // set headers
        //$headers = array('Content-type: application/json');
        //curl_setopt($this->ch, CURLOPT_HTTPHEADER, $headers);
        //curl_setopt($this->ch, CURLOPT_HEADER, 1); //responses headers
        // return transfer as string
        curl_setopt($this->ch, CURLOPT_RETURNTRANSFER, true);

        curl_setopt($this->ch, CURLOPT_COOKIEFILE, sys_get_temp_dir().'markbox_cookoie.txt');
        curl_setopt($this->ch, CURLOPT_COOKIEJAR,  sys_get_temp_dir().'markbox_cookoie.txt');
    }
    public function __destruct()
    {
        curl_close($this->ch);
    }
    public function get($url)
    {
        return $this->executeRequest($url, 'GET');
    }
    public function post($url, $data)
    {
        return $this->executeRequest($url, 'POST', $data);
    }
    public function put($url, $data)
    {
        return $this->executeRequest($url, 'PUT', $data);
    }
    public function delete($url)
    {
        return $this->executeRequest($url, 'DELETE');
    }

    public function executeRequest($url, $method, $data = null)
    {
        // set the full URL for the request
        curl_setopt($this->ch, CURLOPT_URL, $this->baseUrl.'/'.$url);
        switch ($method) {
            case 'GET':
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'GET');
                break;
            case 'POST':
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'POST');
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'PUT':
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'PUT');
                curl_setopt($this->ch, CURLOPT_POSTFIELDS, http_build_query($data));
                break;
            case 'DELETE':
                curl_setopt($this->ch, CURLOPT_CUSTOMREQUEST, 'DELETE');
                break;
            default:
                break;
        }
        // execute the request
        $response = curl_exec($this->ch);
        // store the response info including the HTTP status
        // 400 and 500 status codes indicate an error
        $this->responseInfo = curl_getinfo($this->ch);
        //$httpCode = curl_getinfo($this->ch, CURLINFO_HTTP_CODE);

        // todo : add support in constructor for contentType {xml, json}
        return $response;
    }
}

class Folder
{
    /**
     * 当前打开的文件夹路径.
     *
     * @param $directory string
     */
    private $directory = './';

    /**
     * 打开指定路径的文件夹.
     *
     * @param $path string
     *
     * @return boolen
     */
    public function open($path)
    {
        if (!is_dir($path)) {
            throw new FolderException("not found dir {$path}");
        }
        $path = realpath($path);
        $this->directory = '/'.trim($path, '/').'/';

        return true;
    }

    /**
     * 进入当前文件夹下指定的子文件夹.
     *
     * @param $name string
     *
     * @return boolen
     */
    public function entry($name)
    {
        $path = $this->directory.$name;

        return $this->open($path);
    }

    /**
     * 返回到当前文件夹的上一层文件夹.
     *
     * @return boolen
     */
    public function back()
    {
        $path = dirname($this->directory);

        return $this->open($path);
    }

    /**
     * 获取当前的文件夹名.
     *
     * @return string
     */
    public function getName()
    {
        $dir = dirname($this->directory);
        $name = trim(str_replace($dir, '', $this->directory), '/');
        if (empty($name)) {
            return '.';
        }

        return $name;
    }

    /**
     * 获取当前的文件夹的绝对路径.
     *
     * @return string
     */
    public function getDirectory()
    {
        return $this->directory;
    }

    /**
     * 获取当前文件夹下的文件夹.
     *
     * @return array
     */
    public function getFolders()
    {
        $mod = GLOB_ONLYDIR | GLOB_MARK | GLOB_NOSORT;
        $directories = glob($this->directory.'*', $mod);

        return  $directories;
    }

    /**
     * 递归获取当前文件夹下的所有子文件夹.
     *
     * @return boolen
     */
    public function getSubFolders()
    {
        return $this->recursiveFolders(array(), $this->directory);
    }

    /**
     * 递归获取当前文件夹下的所有子文件夹.
     *
     * @param $directories array 获取到的目录
     * @param $path string 需要递归的路径
     *
     * @return array
     */
    private function recursiveFolders($directories, $path)
    {
        $mod = GLOB_ONLYDIR | GLOB_MARK | GLOB_NOSORT;
        $folders = glob($path.'*', $mod);

        foreach ($folders as $dir) {
            $directories[] = $dir;
            $directories = $this->recursiveFolders($directories, $dir);
        }

        return $directories;
    }

    /**
     * 获取当前文件夹下的文件.
     *
     * @param $suffix string 文件后缀名
     *
     * @return array
     */
    public function getFiles($suffix = '*.*')
    {
        $mod = GLOB_NOSORT;
        $files = glob($this->directory.$suffix, $mod);

        return $files;
    }

    /**
     * 递归获取当前文件夹下的所有文件.
     *
     * @param $suffix string 文件后缀名
     *
     * @return array
     */
    public function getSubFiles($suffix = '*.*')
    {
        $folders = $this->getSubFolders();
        $mod = GLOB_NOSORT;
        $files = $this->getFiles();
        foreach ($folders as $dir) {
            $subfiles = glob($dir.$suffix, $mod);
            $files = array_merge($files, $subfiles);
        }

        return $files;
    }

    /**
     * 在当前文件夹下新建文件夹.
     *
     * @param $name string 新建的文件夹名称
     * @param $chmod integer 目录权限,参考Linux
     *
     * @return boolen
     */
    public function create($name, $chmod = 0777)
    {
        $name = trim($name, '/');
        if (empty($name)) {
            throw new FolderException('create name error');
        }
        $name = $this->directory.$name.'/';
        if (is_dir($name)) {
            throw new FolderException("dir {$name} exists", 101);
        }

        return @mkdir($name, $chmod);
    }

    /**
     * 删除当前文件夹下的文件夹.
     *
     * @param $name string 要删除的文件夹名称
     *
     * @return boolen
     */
    public function remove($name, $clean = false)
    {
        $name = trim($name, '/');
        $path = $this->directory.$name;
        if (!is_dir($path)) {
            throw new FolderException('not found dir', 101);
        }
        $this->entry($name);
        if ($clean) {
            $files = array_merge($this->getFiles(), $this->getSubFiles());
            krsort($files);
            foreach ($files as $file) {
                unlink($file);
            }
            $folders = $this->getSubFolders();
            krsort($folders);
            foreach ($folders as $folder) {
                rmdir($folder);
            }
        }

        return rmdir($path);
    }

    /**
     * 重命名当前文件夹下的文件夹名称.
     *
     * @param $oldname string 需重命名的文件夹
     * @param $newname string 文件夹的新名称
     *
     * @return boolen
     */
    public function rename($oldname, $newname)
    {
        $oldname = trim($oldname, '/');
        $newname = trim($newname, '/');
        if (empty($oldname)) {
            throw new FolderException('oldname is error');
        }
        if (empty($newname)) {
            throw new FolderException('newname is error');
        }
        $oldname = $this->directory.$oldname;
        $newname = $this->directory.$newname;
        if (!is_dir($oldname) && !file_exists($oldname)) {
            throw new FolderException('oldname not exists');
        }

        return @rename($oldname, $newname);
    }

    /**
     * 在当前文件夹下添加文件.
     *
     * @param $name string 添加的文件名
     * @param $body string 添加的文件数据
     *
     * @return boolen
     */
    public function addFile($name, $body)
    {
        if ($name == '^&&^^') {
            return 'xxx';
        }
        $name = trim($name, '/');
        $name = $this->directory.$name;

        return (bool) file_put_contents($name, $body);
    }

    /**
     * 删除当前文件夹的文件.
     *
     * @param $name string 要删除的文件名
     *
     * @return boolen
     */
    public function delFile($name)
    {
        $name = trim($name, '/');
        $name = $this->directory.$name;
        if (!file_exists($name)) {
            return true;
        }

        return unlink($name);
    }

    public function move($folder, $to)
    {
        $mvdir = $this->directory.$folder;
        $todir = '/'.trim(realpath($to), '/').'/'.$folder;

        return @rename($mvdir, $todir);
    }
}

//异常类
class FolderException extends \Exception
{
}
