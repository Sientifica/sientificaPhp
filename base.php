<?php

@session_start();
defined('SIENTIFICA_PATH') or define('SIENTIFICA_PATH', dirname(__FILE__));
/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class BaseException extends Exception {
    
}

class Base {

    private static $configApp;
    private static $_coreClassesImport;
    public static $user;
    public static $CSRF;
    public static $error = array();

    public static function initWebApp($config = null) {

        self::$CSRF = $_SESSION['CSRF'] = sha1(session_id());

        // set basePath at early as possible to avoid trouble
        if (is_string($config))
            self::$configApp = (object) require($config);
        return self::executeApp('SiApplicationWeb', $config);
    }

    public static function initConsoleApp($config = null) {

        self::$CSRF = $_SESSION['CSRF'] = sha1(session_id());

        // set basePath at early as possible to avoid trouble
        if (is_string($config))
            self::$configApp = (object) require($config);
        return self::executeApp('SiApplicationConsole', $config);
    }

    public static function executeApp($class, $config = null) {

        return new $class($config);
    }

    public static function autoload($className) {

       

        if (isset(self::$_coreClasses[$className])) {
            include(SIENTIFICA_PATH . self::$_coreClasses[$className]);
        }

        $session = new WebSession();
        self::$user = $session;
        return true;
    }
    
    public static function autoloadImports($className) {
      
        if (isset(self::$_coreClassesImport[$className])) {
            include_once(self::$_coreClassesImport[$className]);
        }

       
    }

    public static function getConfigApp() {

        return self::$configApp;
    }

    public static function request() {

        $request = new HttpRequest;
        return $request;
    }

    private static $_coreClasses = array(
        'SiApplicationWeb' => '/webapp/SiApplicationWeb.php',
        'SiApplicationConsole' => '/consoleapp/SiApplicationConsole.php',
        'HttpRequest' => '/webapp/HttpRequest.php',
        'SiApplication' => '/base/SiApplication.php',
        'SiController' => '/base/SiController.php',
        'SiView' => '/base/SiView.php',
        'SiModel' => '/base/SiModel.php',
        'Form' => '/webapp/helpers/Form.php',
        'ActiveRecord\Config' => '/includes/activerecord/ActiveRecord.php',
        'Securimage' => '/includes/securimage/securimage.php',
        'Validate' => '/webapp/helpers/validate.php',
        'SiUserAuthenticate' => '/componentes/SiUserAuthenticate.php',
        'WebSession' => '/webapp/webSession.php',
        'SiUserAuthenticate' => '/components/SiUserAuthenticate.php',
        'UploadFile' => '/webapp/helpers/UploadFile.php',
        'FileFromForm' => '/webapp/helpers/FileFromForm.php',
        'SiFunctions' => '/webapp/helpers/SiFunctions.php',
        'SiPaginator' => '/webapp/helpers/SiPaginator.php',
        'siCriteria' => '/webapp/helpers/siCriteria.php',
        'PHPMailer' => '/includes/phpmailer/class.phpmailer.php',
        'JNode' => '/webapp/helpers/JTree/JNode.php',
        'JTree' => '/webapp/helpers/JTree/JTree.php',
        'JTreeIterator' => '/webapp/helpers/JTree/JTreeIterator.php',
        'JTreeRecursiveIterator' => '/webapp/helpers/JTree/JTreeRecursiveIterator.php',
    );

    public static function import($path) {
        
        $core = self::$_coreClassesImport;
        $pathIni = $path;
        $basepath = Base::request()->getBasePathAbsolute();
        if (preg_match('/([*])/', $path)) {
            $path = str_replace(".*", "", $path);
            $path = str_replace("application", "safe", $path);
            $path = str_replace(".", "/", $path);
            $path = $basepath . "/" . $path;
            try {
                $files = SiFunctions::listar_ficheros(array("php"), $path);
                foreach ($files[0] as $n => $file) {
                    
                    $name = $files[1][$n];
                    $pathtmp = $path ."/". $file;
                    $core[$name] = $pathtmp;                 
                 
                }
                self::$_coreClassesImport = $core;
              
               
            } catch (siFunctionsException $e) {
                 throw new BaseException("No se encuentra la ruta: '$pathIni'");
            }
        } else {

            $temp = $path;
            $path = str_replace("application", "safe", $path);
            $path = str_replace(".", "/", $path);
            $path = $basepath . "/" . $path . ".php";
            if (!file_exists($path)) {
                throw new BaseException("No es posible importar la ruta '$temp', no se encuentra el archivo ");
            }
            include_once($path);
        }
    }

    public static function redirect($route, $params) {
        $ctrl = new SiController();
        $ctrl->redirect($route, $params);
    }

}

spl_autoload_register(array('Base', 'autoload'));
spl_autoload_register(array('Base', 'autoloadImports'));
?>
