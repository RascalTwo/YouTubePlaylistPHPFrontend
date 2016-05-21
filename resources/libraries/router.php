<?php

class Router{
    private $assets_path;
    private $routes = [
        "GET" => [],
        "POST" => [],
        "PUT" => [],
        "PATCH" => [],
        "DELETE" => [],
        "ANY" => [],
    ];

    public function __construct($assets_path){
        $this -> assets_path = $assets_path;
    }

    public function match(){
        $method = $_SERVER["REQUEST_METHOD"];
        $uri = $_SERVER["REQUEST_URI"];

        error_log(print_r($_SERVER["REMOTE_ADDR"] . " " . $method . " " . $uri, true));

        if (array_key_exists($uri, $this -> routes[$method])){
            $_POST = $this -> sanitize($this -> routes[$method][$uri]["excepted_params"]);
            $this -> routes[$method][$uri]["handler"]();
            return;
        }

        if (file_exists($this -> assets_path . $uri)){
            include $this -> assets_path . $uri;
            if (strpos($uri, ".css")){
                header("Content-type: text/css");
            }
            elseif (strpos($uri, ".js")){
                header("Content-type: text/javascript");
            }
            return;
        }

        $error_message = "File/Path '" . $uri . "' not found.";
        include $this -> assets_path . "/../resources/templates/error.php";
    }

    public function get($path, $handler){
        $this -> addRoute("GET", $path, $handler, NULL);
    }

    public function post($path, $handler, $excepted_params){
        $this -> addRoute("POST", $path, $handler, $excepted_params);
    }

    public function put($path, $handler, $excepted_params){
        $this -> addRoute("PUT", $path, $handler, $excepted_params);
    }

    public function patch($path, $handler, $excepted_params){
        $this -> addRoute("PATCH", $path, $handler, $excepted_params);
    }

    public function delete($path, $handler, $excepted_params){
        $this -> addRoute("DELETE", $path, $handler, $excepted_params);
    }

    public function any($path, $handler, $excepted_params){
        $this -> addRoute("ANY", $path, $handler, $excepted_params);
    }

    private function addRoute($method, $path, $handler, $excepted_params){
        $this -> routes[$method][$path] = ["handler" => $handler, "excepted_params" => $excepted_params];
    }

    private function sanitize($excepted_params){
        if ($excepted_params === NULL){
            return $_POST;
        }
        foreach ($excepted_params as $param => $required){
            if ($required){
                $_POST[$param] = strip_tags(filter_var($_POST[$param], FILTER_SANITIZE_STRING));
            }
            elseif (array_key_exists($param, $_POST)){
                $_POST[$param] = strip_tags(filter_var($_POST[$param], FILTER_SANITIZE_STRING));
            }
        }
        return $_POST;
    }
}

?>