<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

abstract class SiModel extends ActiveRecord\Model {

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

    public function __construct(array $attributes = array(), $guard_attributes = true, $instantiating_via_find = false, $new_record = true) {
        $this->validate = new Validate();
        parent::__construct($attributes, $guard_attributes, $instantiating_via_find, $new_record);
    }

    public function save($validate = true) {


        if ($validate) {
            if ($this->validate()) {
                return parent::save(false);
            }
        }else
            return parent::save($validate);
    }

    public function insert($validate = true) {

        if ($validate) {
            if ($this->validate()) {

                return parent::insert();
            }
        }else
            return parent::insert($validate);
    }

    public function update($validate = true) {

        if ($validate) {
            if ($this->validate()) {
                return parent::update();
            }
        }else
            return parent::update($validate);
    }

    public function validate() {


        $ARValid = false;


        if (isset($this->scenario) && !empty($this->scenario)) {
            $this->validate->scenario = $this->scenario;
        } else if (parent::is_new_record()) {
            $this->validate->scenario = 'create';
        } else {
            $this->validate->scenario = 'update';
        }


        if (parent::is_new_record()) {
            $this->validate->tipoReg = 'create';
        } else {
            $this->validate->tipoReg = 'update';
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



            $ARValid = parent::is_valid();
            $this->errorsModel = array_merge($this->validate->getErrors(), $this->errors->full_messages($this->labelsModel));
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
        parent::set_attributes($this->attributes);
    }

    public function escape($string) {

        return parent::connection()->escape($string);
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

    public function getPkValue() {
        $pkName = parent::table()->pk;
        $arrPk = parent::values_for_pk();
        return $arrPk[$pkName[0]];
    }

    public function getPkName() {
        $pkName = parent::table()->pk;
        $arrPk = parent::values_for_pk();
        return $pkName[0];
    }

    public static function find() {
        $args = func_get_args();
        $num_args = count($args);
       
        if ($num_args == 1) {
            if (is_object($args[0])) {
                $args[0] = $args[0]->getCriteria(parent::connection());
            }            
            return parent::find($args[0]);
        } elseif ($num_args == 2) {

            if (is_object($args[1])) {
                $args[1] = $args[1]->getCriteria(parent::connection());
            }
            return parent::find($args[0], $args[1]);
        }
        return parent::find();
    }

    public static function count() {
        
        $args = func_get_args();
        if (!empty($args)) {
            if (is_object($args[0])) {
                $args[0] = $args[0]->getCriteria(parent::connection());                
                return parent::count($args[0]);
            }else
            return parent::count($args[0]);
        }

        return parent::count();
    }

}

?>
