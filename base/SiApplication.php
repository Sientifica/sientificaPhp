<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class SiApplication {

    private $basePath;
    private $config;

    abstract public function Request();

    public function __construct($config = null) {

        // set basePath at early as possible to avoid trouble
        if (is_string($config))
            $this->config = Base::getConfigApp();


        if (isset($this->config->basePath)) {
            $this->setBasePath($this->config->basePath);
        }
    }

    public function run() {

        if (isset($this->config->db)) {

            $this->connectActiveRecord();
        }

        // Se cargan los modelos de los modulos
        $config = Base::getConfigApp();
        if (isset($config->modules)) {
            $mods = $config->modules;
          
            $path = Base::request()->getBasePathAbsolute();

            if (isset(Base::getConfigApp()->path_safe_dir)) {
                $basepath = $path . Base::getConfigApp()->path_safe_dir;
            } else {
                $basepath = $path . "/safe";
            }


            foreach ($mods as $key => $value) {
                $key = is_integer($key)?$value:$key;
                $pathModels = $basepath . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "{$key}" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR;
                $files = SiFunctions::listar_ficheros(array("php"), $pathModels);
                foreach ($files[0] as $model) {
                    require_once($basepath . DIRECTORY_SEPARATOR . "modules" . DIRECTORY_SEPARATOR . "{$key}" . DIRECTORY_SEPARATOR . "models" . DIRECTORY_SEPARATOR . $model);
                }
            }
        }

        $this->Request();
    }

    public function setBasePath($path) {
        $this->basePath = $path;
    }

    public function getBasePath() {
        return $this->basePath;
    }

    private function connectActiveRecord() {

        $connections = $this->config->db['connections'];

        ActiveRecord\Config::initialize(function($cfg) use ($connections) {
                    $config = Base::getConfigApp();
                    $basePath = $config->basePath;

                    $cfg->set_model_directory($basePath . '/models');
                    $cfg->set_connections($connections);
                    $cfg->configApp = $config;

                    # default connection is now production
                    $cfg->set_default_connection($config->db['useConnection']);
                });
    }

}

?>
