<?php

class FrontController extends Controller {
    const DEFAULT_ACTION = "show";
    const DEFAULT_CONTROLLER = "HomeController";
    
    /**
     * Despacha la solicitud HTTP al controlador y la acción correspondientes.
     *
     * @throws Exception Cuando no se encuentra el controlador o la acción especificada.
     */
    public function dispatch() {
        $params = null;
        if (count($_GET) == 0) {
            $controller_name = self::DEFAULT_CONTROLLER;
            $action = self::DEFAULT_ACTION;
        } else {
            $url = array_keys($_GET)[0];
            $url = $this->sanitize($url, 0);
            $url = trim($url, "/");
            $url = filter_var($url, FILTER_SANITIZE_URL);
            $url = explode("/", $url);
            if (isset($url[0])) {
                $controller_name = ucwords($url[0]) . "Controller";
                if (isset($url[1])) {
                    $action = $url[1];
                }
                if (count($url) > 2) {
                    for ($i = 2; $i < count($url); $i++) {
                        $params[] = $url[$i];
                    }
                }
            }
        }
        
        if (file_exists("classes/Controller/" . ucfirst($controller_name) . ".class.php")) {
            $controller = new $controller_name();
            if (method_exists($controller, $action)) {
                $controller->$action($params);
            } else {
                throw new Exception("No existeix l'acció definida $action de $controller_name");
            }
        } else {
            throw new Exception("No existeix el controlador demanat $controller_name");
        }
    }
    
}
