<?php
$args = getopt('m:p');
if(empty($args['m'])){
	echo 'm invalid';
}

switch($args['m']):
	case 'init':
		$init = array();
		echo 'Enter Url:';
		$init['url'] = trim(trim(fgets(STDIN)),'/');
		echo 'Enter Username:';
		$init['user'] = trim(fgets(STDIN));
		$init['password'] = fgetpwd();
		savetmp(json_encode($init));
		unset($init['url']);
		$http = new ApiRequest();
		echo $http->post('rest/sign',$init);
	break;
	case 'state':
		$http = new ApiRequest();
		echo $http->get('rest/state');
	break;
	case 'users':
		$http = new ApiRequest();
		echo $http->get('rest/users');
	break;
endswitch;



function savetmp($data){
	$tmpfile = sys_get_temp_dir().'markbox_userdata.tmp';
	file_put_contents($tmpfile,$data);
}

function gettmp(){
	$tmpfile = sys_get_temp_dir().'markbox_userdata.tmp';
	return file_get_contents($tmpfile);
}

function fgetpwd($prompt = "Enter Password:") {
  if (preg_match('/^win/i', PHP_OS)) {
    $vbscript = sys_get_temp_dir() . 'prompt_password.vbs';
    file_put_contents(
      $vbscript, 'wscript.echo(InputBox("'
      . addslashes($prompt)
      . '", "", "password here"))');
    $command = "cscript //nologo " . escapeshellarg($vbscript);
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
      . addslashes($prompt)
      . "\" mypassword && echo \$mypassword'";
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
		
		curl_setopt($this->ch, CURLOPT_COOKIEFILE, sys_get_temp_dir().'./markbox_cookoie.txt');
		curl_setopt($this->ch, CURLOPT_COOKIEJAR, sys_get_temp_dir().'./markbox_cookoie.txt');
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