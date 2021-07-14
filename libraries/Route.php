<?php

class Route {
    
    private $method;
    private $pattern;
    private $action;

    private $list_method = ['GET', 'POST', 'PATCH', 'DELETE'];


    public function __construct($method, $pattern, $action) {
        $this->method = $this->validateMethod($method);
        $this->pattern = $pattern;
        $this->action = $action;
    }

    private function validateMethod($method) {
        if (in_array($method, $this->list_method)) 
            return $method;
    }

    public function getMethod() {
        return $this->method;
    }

    public function getPattern() {
        return $this->pattern;
    }

    public function getAction() {
        return $this->action;
    }
}