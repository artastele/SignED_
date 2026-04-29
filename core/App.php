<?php

class App
{
    protected $controller = 'AuthController';
    protected $method = 'login';
    protected $params = [];

    public function __construct()
    {
        $url = $this->getUrl();

        if (isset($url[0])) {
            $controllerName = ucfirst($url[0]) . 'Controller';

            if (file_exists('../app/controllers/' . $controllerName . '.php')) {
                $this->controller = $controllerName;
                unset($url[0]);
            }
        }

        require_once '../app/controllers/' . $this->controller . '.php';
        $this->controller = new $this->controller;

        if (isset($url[1])) {
            if (method_exists($this->controller, $url[1])) {
                $this->method = $url[1];
                unset($url[1]);
            }
        }

        $this->params = $url ? array_values($url) : [];

        // Check if method exists before calling
        if (method_exists($this->controller, $this->method)) {
            call_user_func_array([$this->controller, $this->method], $this->params);
        } else {
            // Method doesn't exist, redirect to default
            header('Location: ' . URLROOT . '/auth/login');
            exit;
        }
    }

    public function getUrl()
    {
        if (isset($_GET['url'])) {
            $url = rtrim($_GET['url'], '/');
            $url = filter_var($url, FILTER_SANITIZE_URL);
            return explode('/', $url);
        }

        return [];
    }
}