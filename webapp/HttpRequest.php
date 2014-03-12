<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class HttpRequest {

    private $urlScript;
    private $baseUrl;
    private $_requestUri;
    private $basePath;
    
    
   

    public function getBaseUrl() {
        if ($this->baseUrl === null)
            $this->baseUrl = rtrim(dirname($this->getUrlScript()), '\\/');
        return $this->baseUrl;
    }

    public function getHost(){
         $response = $_SERVER['SERVER_PROTOCOL'];
         $server_host = $_SERVER['HTTP_HOST'];
         @list($protocol,$version) = explode("/",$response);
         $protocol = strtolower ($protocol);
         $host = $protocol."://".$server_host;
       return $host;
    }
    
    

    public function getBasePathAbsolute() {
        if ($this->baseUrl === null)
            $this->baseUrl = $_SERVER['DOCUMENT_ROOT'] . rtrim(dirname($this->getUrlScript()), '\\/');
        return $this->baseUrl;
    }

    public function getUrlScript() {
        if ($this->urlScript === null) {
            $scriptName = basename($_SERVER['SCRIPT_FILENAME']);
            if (basename($_SERVER['SCRIPT_NAME']) === $scriptName)
                $this->urlScript = $_SERVER['SCRIPT_NAME'];
            else if (basename($_SERVER['PHP_SELF']) === $scriptName)
                $this->urlScript = $_SERVER['PHP_SELF'];
            else if (isset($_SERVER['ORIG_SCRIPT_NAME']) && basename($_SERVER['ORIG_SCRIPT_NAME']) === $scriptName)
                $this->urlScript = $_SERVER['ORIG_SCRIPT_NAME'];
            else if (($pos = strpos($_SERVER['PHP_SELF'], '/' . $scriptName)) !== false)
                $this->urlScript = substr($_SERVER['SCRIPT_NAME'], 0, $pos) . '/' . $scriptName;
            else if (isset($_SERVER['DOCUMENT_ROOT']) && strpos($_SERVER['SCRIPT_FILENAME'], $_SERVER['DOCUMENT_ROOT']) === 0)
                $this->urlScript = str_replace('\\', '/', str_replace($_SERVER['DOCUMENT_ROOT'], '', $_SERVER['SCRIPT_FILENAME']));
            else
                die('HttpReques no puede determinar la URL del script de entrada.');
        }
        return $this->urlScript;
    }

    public function getRequestUri() {
        if ($this->_requestUri === null) {
            if (isset($_SERVER['HTTP_X_REWRITE_URL'])) // IIS
                $this->_requestUri = $_SERVER['HTTP_X_REWRITE_URL'];
            else if (isset($_SERVER['REQUEST_URI'])) {
                $this->_requestUri = $_SERVER['REQUEST_URI'];
                if (isset($_SERVER['HTTP_HOST'])) {
                    if (strpos($this->_requestUri, $_SERVER['HTTP_HOST']) !== false)
                        $this->_requestUri = preg_replace('/^\w+:\/\/[^\/]+/', '', $this->_requestUri);
                }
                else
                    $this->_requestUri = preg_replace('/^(http|https):\/\/[^\/]+/i', '', $this->_requestUri);
            }
            else if (isset($_SERVER['ORIG_PATH_INFO'])) {  // IIS 5.0 CGI
                $this->_requestUri = $_SERVER['ORIG_PATH_INFO'];
                if (!empty($_SERVER['QUERY_STRING']))
                    $this->_requestUri.='?' . $_SERVER['QUERY_STRING'];
            }
            else
                die('HttpRequest is unable to determine the request URI');
        }

        return $this->_requestUri;
    }

    public function errorHandle($error, $msg = "") {
        
        
        $basepath = $this->getBasePathAbsolute();
        $file = $basepath . "/safe/controller/mainController.php";
        include_once($file);

        $controller = new mainController();
        $action = "actionError";
        
        $controller->action = "error";
        $controller->id = "main";
        
        Base::$error['error'] =$error;
        Base::$error['message'] =$msg;

        

        if(isset($_SERVER['argv'])){
          die("$error $msg\n");
        }
        

        if (is_callable(array($controller, $action)) == false) {
            header("HTTP/2.0 $error $msg");
            die("$error $msg");
        }
        
        header("HTTP/2.0 $error $msg");
        $controller->$action();
        die();
    }
    
    

}

?>
