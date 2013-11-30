<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class WebSession {

    public function login($identity) {
        $id = $identity->getId();
        $vars = $identity->getUserVars();

        $this->registerIdentification($id, $identity->getName(), $vars);
    }

    public function logout() {
        session_destroy();
    }

    protected function registerIdentification($id, $name, $vars) {
        $this->setId($id);
        $this->setName($name);
        $this->loadIRegisterVars($vars);
    }

    protected function loadIRegisterVars($vars) {
        $names = array();
        if (is_array($vars)) {
            foreach ($vars as $name => $value) {
                $this->setRegisterVars($name, $value);
                $names[$name] = true;
            }
        }
    }

    public function setRegisterVars($key, $value, $defaultValue=null) {

        if ($value === $defaultValue)
            unset($_SESSION[$key]);
        else
            $_SESSION[$key] = $value;
    }

    public function getRegisterVars($key, $defaultValue=null) {

        return isset($_SESSION[$key]) ? $_SESSION[$key] : $defaultValue;
    }

    public function getIsLogued() {
        
        return isset($_SESSION['id']);
    }

    /**
     * @return mixed the unique identifier for the user. If null, it means the user is a guest.
     */
    public function getId() {
        return $this->getRegisterVars('id');
    }

    /**
     * @param mixed $value the unique identifier for the user. If null, it means the user is a guest.
     */
    public function setId($value) {
        $this->setRegisterVars('id', $value);
    }

    /**
     * Returns the unique identifier for the user (e.g. username).
     * This is the unique identifier that is mainly used for display purpose.
     * @return string the user name. If the user is not logged in, this will be {@link guestName}.
     */
    public function getName() {
        $name = '';
        if (($name = $this->getRegisterVars('name')) !== null)
            return $name;
    }

    /**
     * Sets the unique identifier for the user (e.g. username).
     * @param string $value the user name.
     * @see getName
     */
    public function setName($value) {
        $this->setRegisterVars('name', $value);
    }

}

?>
