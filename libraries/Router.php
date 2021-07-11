<?php

class Router {

    private $routes;

    public function __construct()
    {
        $routesPath = 'config/routes.php';
        $this->routes = include($routesPath);

        $url = $this->getUrl();

        foreach ($this->routes as $urlPattern => $path) {
            if (preg_match("~$urlPattern~", $url[0])) {

                $controllerName = ucfirst($path) . 'Controller';
                $method = $_SERVER['REQUEST_METHOD'];

                switch ($method) {
                    case "GET":
                        if (isset($url[1])) {
                            $actionName = 'getPost';
                        } else {
                            $actionName = 'getPosts';
                        }
                        break;
                    case "POST":
                        $actionName = 'addPost';
                        break;
                    case "PATCH":
                        $actionName = 'updatePost';
                        break;
                    case "DELETE":
                        $actionName = 'deletePost';
                        break;
                    default:
                        http_response_code(404);
                        echo "Запит недійсний";
                        break;
                }

                $controllerFile = 'controllers/' . $controllerName . '.php';
                if (file_exists($controllerFile)) {
                    require_once($controllerFile);
                }
                $controllerOject = new $controllerName;
                $result = $controllerOject->$actionName($url[1]);
                if ($result != null) {
                    break;
                }
                

            }
        }
    }

    private function getUrl() {
        if(isset($_GET['url'])){
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode('/', $url);
            return $url;
        }
    }

}