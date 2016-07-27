<?php

class ApiExceptionHandler
{
    private $app;
    public function __construct($app)
    {
        $this->app = $app;
    }

    public function __invoke($request, $response, $next)
    {
        try {
            $data = $next($request, $response);

            return $data;
        } catch (Exception $e) {
            return $this->app->getContainer()->json->error($e, 500);
        } catch (TokenException $e) {
            return $this->app->getContainer()->json->error($e, 401);
        }
    }
}
