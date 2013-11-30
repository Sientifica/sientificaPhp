<?php

/* Esta clase contiene contiene los metodos necesario para hacer validaciones previas a registros o modificaciones
 * @author: Ramiro Andrade
 * 
 */

class Validate {

    private $messages = array();
    private $errorFields = array();
    public $object;
    public $className;
    public $data;
    public $rules;
    public $labels;
    public $validates_numericality_of = array();
    public $validates_uniqueness_of = array();
    public $validates_size_of = array();
    public $scenario;
    public $tipoReg = 'create';

    const EXP_EMAIL="/^([\w-]+\.)*?[\w-]+@[\w-]+\.([\w-]+\.)*?[\w]+$/";
    const EXP_PHONE ='/^([0-9 \-]*)$/';
    const EXP_EMPTY = "/^([^\w]*)$/";
    const EXP_NUMERIC ='/^([0-9]*)$/';




    /* Esta funcion valida el formato de un email
      @author: Ramiro Andrade
     */
    
    private function validateUnique($fields){       
        $class = $this->className;  
        $arrFields = explode(",", $fields[0]);
        foreach ($arrFields as $fi) {
           
            if ($class::exists(array('conditions' => array("$fi=?", @$this->data[$fi])))) {

                $fieldName = (isset($this->labels[$fi])) ? $this->labels[$fi] : $fi;
                array_push($this->messages, "<strong>{$fieldName}:</strong> data with <strong>".@$this->data[$fi]."</strong> has already been taken.");
                array_push($this->errorFields, $fi);
            }
        }
    }

    private function validateEmail($fields) {


        $arrFields = explode(",", $fields[0]);
        foreach ($arrFields as $fi) {
            if (!preg_match(self::EXP_EMPTY, @$this->data[$fi])) {
                if (!preg_match(self::EXP_EMAIL, @$this->data[$fi])) {

                    $fieldName = (isset($this->labels[$fi])) ? $this->labels[$fi] : $fi;
                    array_push($this->messages, "<strong>{$fieldName}:</strong> is not a valid email");
                    array_push($this->errorFields, $fi);
                }
            }
        }
    }

    /* Esta funcion valida el formato de un numero telefonico
      @author: Ramiro Andrade
     */

    private function validatePhone($fields) {


        $arrFields = explode(",", $fields[0]);
        foreach ($arrFields as $fi) {
            if (!preg_match(self::EXP_PHONE, @$this->data[$fi])) {

                $fieldName = (isset($this->labels[$fi])) ? $this->labels[$fi] : $fi;
                array_push($this->messages, "<strong>{$fieldName}:</strong> not a valid phone number");
                array_push($this->errorFields, $fi);
            }
        }
    }

    /* Esta funcion valida si el valor de un attributo es igual a otro
      @author: Ramiro Andrade
     */

    private function validateCompare($fields) {

        $arrFields = explode(",", $fields[0]);


        foreach ($arrFields as $fi) {

            if ($this->data[$fi] != $this->data[$fields['to']]) {

                $fieldName = (isset($this->labels[$fi])) ? $this->labels[$fi] : $fi;
                array_push($this->messages, "<strong>{$fieldName}:</strong> must be equal to the value of <strong>{$this->labels[$fields['to']]}</strong>");
                array_push($this->errorFields, $fi);
                array_push($this->errorFields, $fields['to']);
            }
        }
    }

    /* Esta funcion valida el formato de un numero telefonico
      @author: Ramiro Andrade
     */

    /* private function validateNumeric($fields) {

      $arrFields = explode(",", $fields);
      foreach ($arrFields as $fi) {

      if (!preg_match(self::EXP_NUMERIC, @$this->data[$fi])) {

      $fieldName = (isset($this->labels[$fi])) ? $this->labels[$fi] : $fi;
      array_push($this->messages, "<strong>{$fieldName}:</strong> no es n&uacute;mero v&aacute;lido");
      array_push($this->errorFields, $fi);
      }
      }
      } */

    public function validateActiveRecord($fields) {

        $keys = array_keys($fields);
        $values = array_values($fields);
        $arrayTmp = array();
        $arrayFin = array();
        $arrFields = explode(",", $fields[0]);

        foreach ($arrFields as $fi) {
            $arrayTmp = array($fi);

            for ($i = 2; $i < sizeof($keys); $i++) {
                ${$keys[$i]} = $values[$i];
                $arrayTmp = array_merge($arrayTmp, compact($keys[$i], $arrayTmp));
            }
            array_push($arrayFin, $arrayTmp);
        }
        return $arrayFin;
    }

    /* Esta funcion valida si un campo es vacio
      @author: Ramiro Andrade
     */

    private function textField($fields) {
        //echo $fields['on'];
        $arrFields = explode(",", $fields[0]);
        foreach ($arrFields as $fi) {
            if(is_array(@$this->data[$fi]) || is_object(@$this->data[$fi])){
               if (count(@$this->data[$fi])==0) {
                $fieldName = (isset($this->labels[$fi])) ? $this->labels[$fi] : $fi;
                array_push($this->messages, "<strong>{$fieldName}:</strong> Cannot be empty");
                array_push($this->errorFields, $fi);
            }
            }else{
            if (preg_match(self::EXP_EMPTY, @$this->data[$fi])) {
                $fieldName = (isset($this->labels[$fi])) ? $this->labels[$fi] : $fi;
                array_push($this->messages, "<strong>{$fieldName}:</strong> Cannot be empty");
                array_push($this->errorFields, $fi);
            }
           }
        }
    }

    /* Esta funcion genera una validacion global de varios tipos de validacion
      @author: Ramiro Andrade
     */

    public function valideInputs() {

        if (sizeof($this->rules) > 0) {
            foreach ($this->rules as $validation) {


                $on = (isset($validation['on'])) ? $validation['on'] : $this->tipoReg;

                switch ($validation[1]) {
                    case 'unique':
                        if ($on == $this->scenario)
                            $this->validateUnique ($validation);
                        break;
                    case 'required':
                        if ($on == $this->scenario)
                            $this->textField($validation);
                        break;
                    case 'email':
                        if ($on == $this->scenario)
                            $this->validateEmail($validation);
                        break;
                    case 'phone':
                        if ($on == $this->scenario)
                            $this->validatePhone($validation);
                        break;
                    case 'compare':
                        if ($on == $this->scenario)
                            $this->validateCompare($validation);
                        break;
                    case 'numeric':
                        if ($on == $this->scenario) {
                            $array = $this->validateActiveRecord($validation);
                            $this->validates_numericality_of = $array;
                        }
                        break;                    
                    case 'length':
                        if ($on == $this->scenario) {
                            $array = $this->validateActiveRecord($validation);
                            $this->validates_size_of = $array;
                        }
                        break;
                    case 'captcha':
                        if ($on == $this->scenario) {
                            $this->captcha($validation);
                        }
                        break;
                }
            }

            if (sizeof($this->messages) > 0) {
                return false;
            }
        }
        return true;
    }

    private function captcha($fields) {
        $arrFields = explode(",", $fields[0]);
        foreach ($arrFields as $fi) {
            $img = new Securimage();
            if (!$img->check(@$this->data[$fi])) {
                $fieldName = (isset($this->labels[$fi])) ? $this->labels[$fi] : $fi;
                array_push($this->messages, "<strong>{$fieldName}:</strong> Cannot be empty");
                array_push($this->errorFields, $fi);
            }
        }
    }

    public function getErrors() {
        return $this->messages;
    }

    public function getErrorAttrs() {
        return $this->errorFields;
    }

    public function addError($fieldName, $error) {
        $name = (isset($this->labels[$fieldName])) ? $this->labels[$fieldName] : $fieldName;
        array_push($this->messages, "<strong>{$name}:</strong> {$error}");
        array_push($this->errorFields, $fieldName);
    }

}

?>
