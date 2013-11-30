<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SiControllerException extends Exception {
    
}

class SiController {

    public $id;
    public $action;
    public $module;
    public $layout = false;
    private $view;

    public function __construct() {
        $this->view = new SiView();
    }

    public function accessRules() {
        return array();
    }

    /*
     * Pinta una vista de acuerdo a los parámetros 
     * suministrados, así:
     * 
     * 1. Nombre de la vista
     * 2. El arreglo de parametros que se le pasan a la vista (que también es una clase)
     * 3. Define la forma de "imprimir" la salida, puede ser por un retorno tipo función
     *    O puede ser en forma de las funciones echo y/o print.
     */

    public function render($template, $data = array(), $return = false) {

        $config = Base::getConfigApp();
        $basePath = $config->basePath;

        $this->view->setData($data);
        $this->view->controller = $this->id;
        $this->view->action = $this->action;
        $this->view->module = $this->module;


        if (empty($this->module)) {
            $pathTemplate = $basePath . "/views/" . $this->id . "/" . $template . ".php";
        } else {
            $pathTemplate = $basePath . "/modules/" . $this->module . "/views/" . $this->id . "/" . $template . ".php";
        }


        $this->view->setTemplate($pathTemplate);
        if ($this->layout) {

            $pathLayout = $basePath . "/views/layout/" . $this->layout . ".php";

            $this->view->setLayout($pathLayout);
        }
        if (!$return) {
            $this->view->render();
        } else {
            ob_start();
            $this->view->render();
            $content = ob_get_clean();
            return $content;
        }
    }

    public function redirect($route, $params = array()) {
        $vars = '';

        //$params['token'] = Base::$CSRF;

        if (sizeof($params) > 0) {
            $vars = array();
            foreach ($params as $key => $value) {
                array_push($vars, $key . "=" . $value);
            }
            $vars = "&" . implode("&", $vars);
        }
        $request = explode("/", $route);
        if (sizeof($request) > 1) {
            if (sizeof($request) == 2) {
                 list($controllerID, $actionID) = $request;
                 header("Location: index.php?controller={$controllerID}&action={$actionID}" . $vars);
            }
            if (sizeof($request) == 3) {
                 list($moduleID,$controllerID,$actionID) = $request;
                 header("Location: index.php?module={$moduleID}&controller={$controllerID}&action={$actionID}" . $vars);
            }
           
        } else {
            if (!empty($this->module)) {
                header("Location: index.php?module={$this->module}&controller={$this->id}&action={$route}" . $vars);
            } else {
                header("Location: index.php?controller={$this->id}&action={$route}" . $vars);
            }
        }
    }

    public function renderFile($route, $data = array(),$return) {
        return $this->view->renderFile($route, $data,$return);
    }

    public function addScript($textScript, $print = false) {
        $script = '';

        if (!$print) {
            $script .= '<script> try{ $(document).ready(function(){ ';
        }
        $script .= $textScript;

        if (!$print) {
            $script .='}); }catch(e){}</script>';
        }

        $this->view->script = $script;
        if ($print)
            echo $this->view->script;
    }

}

?>
