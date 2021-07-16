<?php

class Router
{
    private $routers = [];
    private $matchRouter = [];

    private $url;
    private $method;
    private $params = [];

    public function __construct() {
        $this->method = $_SERVER['REQUEST_METHOD'];
        
        $this->getUrl();
    }

    // Функції, що встановлюють тип запиту до маршруту
    public function get($pattern, $action) {
        $this->addRoute("GET", $pattern, $action);
    }

    public function post($pattern, $action) {
        $this->addRoute('POST', $pattern, $action);
    }

    public function patch($pattern, $action) {
        $this->addRoute('PATCH', $pattern, $action);
    }

    public function delete($pattern, $action) {
        $this->addRoute('DELETE', $pattern, $action);
    }

    //Додає шлях до списку
    private function addRoute($method, $pattern, $action) {
        array_push($this->routers, new Route($method, $pattern, $action));
    }

    private function getUrl() {
        if (isset($_GET['url'])) {
            $this->url = rtrim($_GET['url'], '/');

            $param = explode('/', $this->url);
            array_shift($param);
        }
        if (isset($param[0])) {
            array_push($this->params, (int)$param[0]);
        }
    }
    
    // Фільтрує шляхи відносно типу запиту
    private function matchRoutersByRequest() {
        foreach ($this->routers as $router) {
            if ($this->method == $router->getMethod())
                array_push($this->matchRouter, $router);
        }
    }

    // Фільтрує шляхи відносно посилання
    private function matchRoutersByPattern($pattern) {
        $this->matchRouter = [];
        foreach ($pattern as $router) {
            if ($this->dispatch($this->url, $router->getPattern()))
                array_push($this->matchRouter, $router);
        }
    }

    // Порівнння посилання та паттерна
    public function dispatch($uri, $pattern) {
        if (preg_match("~$pattern~", $uri)) {
            return true;
        }
        return false;
    }

    public function run() {
        $this->matchRoutersByRequest();
        $this->matchRoutersByPattern($this->matchRouter);

        if (empty($this->matchRouter)) {
			echo 'Неіснуючий запит';
		} else {
            if (is_callable($this->matchRouter[0]->getAction()))
                call_user_func($this->matchRouter[0]->getAction(), $this->params);
            else
                $this->runController($this->matchRouter[0]->getAction(), $this->params);
        }
    }

    // Виклик метода контроллера
    private function runController($controller, $params) {
        $parts = explode('@', $controller);
        $controller = $parts[0];
        $method = $parts[1];

        $file = "controllers/" . ucfirst($controller) . 'Controller.php';

        if (file_exists($file)) {
            require_once($file);

            // Клас контроллера
            $controller = ucfirst($controller) . 'Controller';

            if (class_exists($controller))
                $controller = new $controller();

            // Виклик методу
            if (is_callable([$controller, $method]))
                return call_user_func_array([$controller, $method], $params);
            else
                echo 'Неможливо викликати контроллер або його метод';
        }
    }
}