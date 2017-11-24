<?php
namespace Core;

use Config\Routes;

class Application {
    protected $debug = false;
    protected $request;
    protected $router;
    protected $response;
    protected $session;

    /**
    * 初期化処理
    */

    public function __construct($debug = false)
    {
        $this->setDebugMode($debug);
        $this->initialize();
        $this->configure();
    }

    protected function setDebugMode($debug)
    {
        if ($debug) {
            $this->debug = true;
            ini_set('display_errors', 1);
            error_reporting(-1);
        } else {
            $this->debug = false;
            ini_set('display_errors', 0);
        }
    }

    protected function initialize()
    {
        $this->request = new Request();
        $this->router = new Router($this->registerRoutes());
        $this->response = new Response();
        $this->session = new Session();
    }

    protected function configure()
    {
    }

    public function getRootDir()
    {
        return dirname(__FILE__, 2);
    }

    protected function registerRoutes()
    {
        $routes = new Routes();
        return $routes->getRoutes();
    }
    /**
    * コントローラをリクエストurlから取得し、レスポンスを返す
    */
    public function run()
    {
        try {
            $path_info = $this->request->getPathInfo();
            $method = $this->request->getRequestMethod();
            $params = $this->router->resolve($path_info, $method);
            if ($params === false) {
                throw new HttpNotFoundException('No route found for ' . $this->request->getPathInfo());
            }

            $controller = $params['controller'];
            $action = $params['action'];

            $this->runAction($controller, $action, $params);
        } catch (HttpNotFoundException $e){
            $this->render404Page($e);
        }

        $this->response->send();
    }

    /**
    * コントローラのアクションメソッドを実効し、レスポンスを作成する
    */
    public function runAction($controller_name, $action, $params = array())
    {
        $controller_class = ucfirst($controller_name) . 'Controller';
        $controller = $this->findController($controller_class);
        if ($controller === false) {
            throw new HttpNotFoundException($controller_class . ' controller is not found.');
        }

        $content = $controller->run($action, $params);
        $this->response->setContent($content);
    }

    /**
    * コントローラを検索する
    */
    public function findController($controller_class)
    {
        if (!class_exists($controller_class)) {
            $controller_file = $this->getControllerDir() . '/' . $controller_class . '.php';
        }

        if (!is_readable($controller_file)) {
            return false;
        } else {
            require_once $controller_file;
            if (!class_exists($controller_class)) {
                return false;
            }
        }

        return new $controller_class($this);
    }

    /**
    * 404ページのレンダー
    */
    protected function render404Page($e)
    {
        $this->response->setStatusCode(404, 'Not Found');
        $message = $this->isDebugMode() ? $e->getMessage() : 'Page not found.';
        $message = htmlspecialchars($message, ENT_QUOTES, 'UTF-8');

        $this->response->setContent(
<<<EOF
<!DOCTYPE html>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
        <title>404 Not Found</title>
    </head>
    <body>
        {$message}
    </body>
</html>
EOF
        );
    }

    /**
    * アクセッサー
    */

    public function isDebugMode()
    {
        return $this->debug;
    }

    public function getRequest()
    {
        return $this->request;
    }

    public function getResponse()
    {
        return $this->response;
    }

    public function getSession()
    {
        return $this->session;
    }

    public function getControllerDir()
    {
        return $this->getRootDir() . '/app/Controllers';
    }

    public function getViewDir()
    {
        return $this->getRootDir() . '/app/Views';
    }

    public function getModelDir()
    {
        return $this->getRootDir() . '/app/Models';
    }

    public function getWebDir()
    {
        return $this->getRootDir() . '/web';
    }
}
