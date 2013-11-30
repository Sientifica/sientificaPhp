<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SiApplicationConsole extends SiApplication {

    public function Request() {
        
        $argv = ($_SERVER['argv']);

       if(@$argv[1] == 'null' || @$argv[1] == 'NULL'){
          $module =  "";
          $controller = @$argv[2];
          $action = @$argv[3];          
 
        }else{
           $module =  @$argv[1];
           $controller = @$argv[2];
           $action = @$argv[3];
        }

        
        
        if(count($argv)==1){
                  die("Error!!, debe ingresar argv1 = module|NULL, argv2 = controller, argv3 = action\n");
        }
        $this->executeRequest($controller, $action, $module);
    }

    private function executeRequest($controllerID, $actionID, $moduleID) {
        @session_start();
        include(SIENTIFICA_PATH . "/includes/validate_xss.php");

        searchXSS(); // validacion XSS

        if ($_POST) {
            if ($_SESSION['CSRF'] != @$_REQUEST['token']) {

                Base::request()->errorHandle(406, "Error de seguridad!! token CSRF no valido");
            }
        }

        $basepath = Base::request()->getBasePathAbsolute(); //$this->getBasePath();
        if (isset(Base::getConfigApp()->path_safe_dir)) {
            $basepath .= Base::getConfigApp()->path_safe_dir;
        } else {
            $basepath .= "/safe";
        }
        if ($moduleID != '') {
            $file = $basepath . "/modules/{$moduleID}/controller/{$controllerID}Controller.php";
        } else {
            $file = $basepath . "/controller/{$controllerID}Controller.php";
        }




        if (is_readable($file) == false) {
            Base::request()->errorHandle(404, "Controller not Found");
        }

        include_once($file);

        $class = "{$controllerID}Controller";
        $controller = new $class();
        $action = "action" . ucfirst($actionID);

        if (is_callable(array($controller, $action)) == false) {
            Base::request()->errorHandle(404, "Action not Found");
        }
        $controller->action = $actionID;
        $controller->id = $controllerID;
        $controller->module = $moduleID;

        if (!class_exists("Action")) {
            $controller->$action();
            die;
        }

        $controller->$action();
    }

    

}

?>
