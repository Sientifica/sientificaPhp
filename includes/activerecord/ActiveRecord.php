<?php

if (!defined('PHP_VERSION_ID') || PHP_VERSION_ID < 50300)
    die('PHP ActiveRecord requires PHP 5.3 or higher');

define('PHP_ACTIVERECORD_VERSION_ID', '1.0');

require 'lib/Singleton.php';
require 'lib/Config.php';
require 'lib/Utils.php';
require 'lib/DateTime.php';
require 'lib/Model.php';
require 'lib/Table.php';
require 'lib/ConnectionManager.php';
require 'lib/Connection.php';
require 'lib/SQLBuilder.php';
require 'lib/Reflections.php';
require 'lib/Inflector.php';
require 'lib/CallBack.php';
require 'lib/Exceptions.php';

spl_autoload_register('activerecord_autoload');

//spl_autoload_register('loadInternalModels');

function activerecord_autoload($class_name) {
    $path = ActiveRecord\Config::instance()->get_model_directory();
    $root = realpath(isset($path) ? $path : '.');

    if (($namespaces = ActiveRecord\get_namespaces($class_name))) {
        $class_name = array_pop($namespaces);
        $directories = array();

        foreach ($namespaces as $directory)
            $directories[] = $directory;

        $root .= DIRECTORY_SEPARATOR . implode($directories, DIRECTORY_SEPARATOR);
    }

    $file = "$root/$class_name.php";

    if (file_exists($file))
        require_once $file;

    // Se cargan los modelos de los modulos
    $config = Base::getConfigApp();
    if (isset($config->modules)) {
        $mods = $config->modules;
         $path = Base::request()->getBasePathAbsolute();
        
         if (isset(Base::getConfigApp()->path_safe_dir)) {
             $basepath = $path.Base::getConfigApp()->path_safe_dir;
        } else {
             $basepath  = $path."/safe";
        }
        

        foreach ($mods as $key => $value) {
            $pathModels = $basepath .DIRECTORY_SEPARATOR. "modules".DIRECTORY_SEPARATOR."{$value}".DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR;
            $files = SiFunctions::listar_ficheros(array("php"), $pathModels);
            foreach ($files[0] as $model) {
                require_once($basepath .DIRECTORY_SEPARATOR. "modules".DIRECTORY_SEPARATOR."{$value}".DIRECTORY_SEPARATOR."models".DIRECTORY_SEPARATOR. $model);
            }
        }
    }
}

/* function loadInternalModels() {

  $pathModels = SIENTIFICA_PATH . "/webapp/models/";
  $files = SiFunctions::listar_ficheros(array("php"), $pathModels);

  foreach ($files[0] as $model) {
  require_once(SIENTIFICA_PATH . "/webapp/models/" . $model);
  }
  } */
?>
