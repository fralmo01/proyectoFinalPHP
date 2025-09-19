<?php

class Router {
    public function run() {
        $controller = isset($_GET['controller']) ? ucfirst($_GET['controller']).'Controller' : 'HomeController';
        $action = $_GET['action'] ?? 'index';

        $path = "../app/controllers/$controller.php";

        if (file_exists($path)) {
            require_once $path;
            $objController = new $controller();

            if (method_exists($objController, $action)) {
                $objController->$action();
            } else {
                echo " Método <b>$action</b> no encontrado en $controller";
            }
        } else {
            echo " Controlador <b>$controller</b> no encontrado";
        }
    }
}


?>