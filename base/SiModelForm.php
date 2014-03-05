<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class SiModelForm{

    public $errorsModel = array();
    private $errorsAttrs = array();
    public $labelsModel = array();
    public $attributes = array();
    public $validate;
    static $validates_numericality_of = array();
    static $validates_uniqueness_of = array();
    static $validates_size_of = array();
    static $belongs_to = array();
    public $scenario;

    abstract public function validateRules();

    abstract public function attributeLabels();

    abstract public function beforeValidate();

    abstract public function afterValidate();

    public function __construct() {
        $this->validate = new Validate();      
    }

  

    public function validate() {
        $this->validate->scenario = 'create'; 
        $this->validate->tipoReg = 'create';
        $ARValid = true;


        if (isset($this->scenario) && !empty($this->scenario)) {
            $this->validate->scenario = $this->scenario;
        }
       
        

        if ($this->beforeValidate()) {
        
            $this->validate->rules = $this->validateRules();
            $this->validate->object = $this;
            $this->validate->className = get_class($this);
            $this->validate->data = $this->attributes;
            
          
            $this->labelsModel = $this->attributeLabels();
            $this->validate->labels = $this->labelsModel;
            $result = $this->validate->valideInputs();
            self::$validates_numericality_of = $this->validate->validates_numericality_of;
            self::$validates_uniqueness_of = $this->validate->validates_uniqueness_of;
            self::$validates_size_of = $this->validate->validates_size_of;

            //$ARValid = parent::is_valid();
            $this->errorsModel = $this->validate->getErrors();
            $this->errorsAttrs = $this->validate->getErrorAttrs();
        }
        if ($ARValid) {
            $this->afterValidate();
            return $result;
        }else
            return false;
    }

    public function set_attributes($attributes) {

        foreach ($attributes as $key => $value) {
             $this->$key = $value;
        }
        $this->attributes = $attributes;
      
    }

   

    public function getErrors() {
        return $this->errorsModel;
    }

    public function hasErrors($attribute) {

        return (in_array($attribute, $this->errorsAttrs)) ? true : false;
    }

    public function addError($fieldName, $error) {
        $this->labelsModel = $this->attributeLabels();
        $this->validate->labels = $this->labelsModel;
        $this->validate->addError($fieldName, $error);
        if (sizeof($this->errorsModel) == 0)
            $this->errorsModel = $this->validate->getErrors();
    }

    public function getAttributeLabel($attr) {
        $arrAttrs = $this->attributeLabels();
        return $arrAttrs[$attr];
    }

   

   

   

  

}

?>
