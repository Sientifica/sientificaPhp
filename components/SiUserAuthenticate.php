<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class SiUserAuthenticateException extends Exception {
    
}

class SiUserAuthenticate {
    const NOERROR = 0;
    const NICK_INVALID =1;
    const PASSWORD_INVALID =2;
    const NOIDENTIFY =3;

    public $id;
    public $nickname;
    public $password;
    public $errorCode = self::NOIDENTIFY;
    private $userVars = array();

    public function __construct($nickname, $password) {
        $this->nickname = $nickname;
        $this->password = $password;
    }

    public function Authentication() {

        throw new SiUserAuthenticateException("El metodo Authentication() debe ser implementado");
    }

    public function getId() {
        return $this->id;
    }
    
    public function getName()
	{
		return $this->nickname;
	}

    public function getUserVars() {
        return $this->userVars;
    }

    public function setUserVars($vars) {
        $this->userVars = $vars;
    }

    public function setVar($name, $value) {
        $this->userVars[$name] = $value;
    }

    public function getVar($name, $defaultValue=null) {
        return isset($this->userVars[$name]) ? $this->userVars[$name] : $defaultValue;
    }

    public function clearVar($name) {
        unset($this->userVars[$name]);
    }

}

?>
