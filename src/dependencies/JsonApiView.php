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
class JsonApiView
{
    protected $container;

    public function __construct($container)
    {
        $this->container = $container;
    }
    /**
     * Render a restful.
     *
     * $body restful data
     * $code http code
     *
     * @param array $data
     * @param int   $code
     *
     * @return ResponseInterface
     */
    public function render($body, $code = 200)
    {
        if (is_bool($body)) {
            $msg = $body ? 'success' : 'fail';
            $body = ['data' => $msg];
        } elseif (is_string($body)) {
            $body = ['data' => $body];
        } elseif (is_numeric($body)) {
			$body = ['data' => $body];
		}
        if (!is_array($body)) {
            throw new Exception('response data error', 004);
        }
        $request = $this->container->request;
        $response = $this->container->response;
        $response = $response->withStatus($code)->withHeader('Content-type', 'application/json');
        $output = json_encode($body);
        $jsonp = $request->getParam('jsonp');
        if (!empty($jsonp)) {
            $callback = $jsonp;
            $response->getBody()->write('var '.$callback.'='.$output);
        } else {
            $response->getBody()->write($output);
        }

        return $response;
    }

    public function error($e, $httpcode)
    {
        $request = $this->container->request;
        $response = $this->container->response;
        $response = $response->withStatus($httpcode)->withHeader('Content-type', 'application/json');
        $debug = $this->container->get('settings')['debug'];
        if ($debug) {
            $body = ['errno' => $e->getCode(), 'error' => $e->getMessage(), 'trace' => $e->getTrace()];
        } else {
            $body = ['errno' => $e->getCode(), 'error' => $e->getMessage()];
        }
        $output = json_encode($body);
        $jsonp = $request->getParam('jsonp');
        if (!empty($jsonp)) {
            $callback = $jsonp;
            $response->getBody()->write('var '.$callback.'='.$output);
        } else {
            $response->getBody()->write($output);
        }

        return $response;
    }
}
