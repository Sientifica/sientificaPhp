<?php

class SiViewException extends Exception {

    public function __construct($msg=null){

        if ($msg == null){

            parent::__construct($msg);
        }

    }
    
}

class SiView {

    private $data;
    private $template;
    public $controller;
    public $action;
    public $module;
    private $layout = false;
    public $script = '';

    public function __construct() {
        
    }

    public function setData($data) {
        if (!is_array($data)) {
            throw new SiViewException('$data se esperaba fuera un arreglo, se envio un ' . gettype($data));
        }
        $this->data = $data;
    }

    public function setLayout($layout) {
        if (!file_exists($layout)) {
            throw new SiViewException('$layout  no es un archivo existente');
        }

        $this->layout = $layout;
    }

    public function setTemplate($template) {
        if (!file_exists($template)) {
            throw new SiViewException('$template no es un archivo existente');
        }

        $this->template = $template;
    }

    public function render() {
        $content = $this->renderTemplate();
        if ($this->layout) {
            ob_start();
            include($this->layout);
            $content = ob_get_clean();
            $content = $this->addScript($content);
         
            echo $content;
        }else
            echo $content;
    }

    private function renderTemplate() {
        ob_start();
        @extract($this->data, EXTR_OVERWRITE);
        include( $this->template );
        $content = ob_get_clean();
        if ($this->script != '') {
            $content = $content . '<###end###>';
        }
        $content = $this->addScript($content);


        return $content;
    }

    private function addScript($content) {
        if ($this->script != '') {
            $count = 0;
            $content = preg_replace('/(<\\/body\s*>)/is', '<###end###>$1', $content, 1, $count);

            $content = str_replace('<###end###>', $this->script, $content);
        }
        return $content;
    }

    public function renderFile($route, $data = array(), $return = false) {
        $route = str_replace("application", "safe", $route);
        $route = str_replace(".", "/", $route);
        $route = $route . ".php";
        ob_start();
        @extract($data, EXTR_OVERWRITE);

        if (is_readable($route)){
         include($route);
        }
        $content = ob_get_clean();

        if (!$return)
            echo $content;
        else
            return $content;
    }

    public function createUrl($route, $params = array(), $ampersand = '&') {


        $request = explode("/", $route);
        if (is_array($request) && sizeof($request) > 1) {

            return Form::createUrl($route, $params, $ampersand);
        }
        return Form::createUrl($this->module."/".$this->controller . "/" . $route, $params, $ampersand);
    }
    
    public function redirect($route, $params = array()){
        Base::redirect($route, $params);
    }
      
}

?>
