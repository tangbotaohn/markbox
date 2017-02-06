<?php

require 'vendor/autoload.php';
define('__BASEPATH__', dirname(__FILE__));
$app = new App();
$app->run();

class App
{
    private $context;
    public function __construct()
    {
        $this->context = $context = new Context();
        require 'src/contexts.php';
    }

    public function run()
    {
        $router = new CutePHP\Route\Router();
        require 'src/routes.php';
        $uri = (array) explode('?', $_SERVER['REQUEST_URI']);
        $uri = $uri[0];
        $method = $_SERVER['REQUEST_METHOD'];
        $request = $router->match($uri, $method);
        if (empty($request)) {
            return header('HTTP/1.1 404 Not Found');
        }

        //route callback
        $callable = $request->getStorage();
        $this->context->request = $request;
        if ($callable instanceof Closure) {
            try {
                $data = $callable($this->context);
                if (is_array($data)) {
                    echo json_encode(array('code' => 1, 'data' => $data));
                } elseif (is_bool($data)) {
                    if ($data) {
                        echo json_encode(array('code' => 1, 'data' => 'success'));
                    } else {
                        echo json_encode(array('code' => 0, 'errno' => 0, 'data' => 'fail'));
                    }
                } else {
                    echo $data;
                }
            } catch (Exception $e) {
                echo json_encode(array('code' => -1, 'errno' => $e->getCode(), 'data' => $e->getMessage()));
            } catch (FolderException $e) {
                echo json_encode(array('code' => -2, 'errno' => $e->getCode(), 'data' => $e->getMessage()));
            }
        } else {
            echo $callable;
        }
    }
}

class Context
{
    public $request;
}
