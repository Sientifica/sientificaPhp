<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SiApplicationWeb extends SiApplication {

    public function Request() {

        $controller = isset($_REQUEST['controller']) ? $_REQUEST['controller'] : "main";
        $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : "index";
        $module = isset($_REQUEST['module']) ? $_REQUEST['module'] : "";
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

        try {

            if ($this->validateExcecuteAction($class, $actionID) || @Base::getConfigApp()->params['profiles']['SUPER'] == Base::$user->getRegisterVars('profile')) {
                $controller->$action();
            } else {
                if (!Base::$user->getIsLogued()) {

                    $controller->redirect("main/index");
                }else
                    Base::request()->errorHandle(403, "No tiene permisos para ejecutar esta accion");
            }
        } catch (ActiveRecord\DatabaseException $e) {
            $controller->$action();
        }
    }

    private function validateExcecuteAction($controller, $action) {

        $criteria = array('conditions' => "action='" . $action . "' AND id_controller='" . $controller . "'");
        @$act = Action::find($criteria);
        if (@$act->active == 0) {
            return true;
        }

        $join = 'LEFT JOIN actions a ON(permissions.action_id = a.action_id)';
        $criteria = array("joins" => $join, "conditions" => array("permissions.user_id = ? AND a.action=? AND a.active=1 AND a.id_controller=?", Base::$user->getId(), $action, $controller));

        if (Permission::exists($criteria))
            return true;
        else
            return false;
    }

}

?>
