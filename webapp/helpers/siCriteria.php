<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

class siCriteria {

    private $conditions = array();
    public $condition = null;
    public $params = array();
    public $group = null;
    public $select = null;
    public $order = null;
    public $join = null;
    public $limit = null;
    public $offset = null;
    public $criteria;

    public function __construct() {
        
    }

    public function compare($field, $value, $like = false) {
        if (!empty($value)) {
            if ($like)
                $this->conditions["$field LIKE ?"] = "%" . $value . "%";
            else
                $this->conditions["$field = ?"] = $value;
        }
    }

    public function betweenCondition($field, $start, $end, $operator = "AND") {

        $this->condition = " {$field} BETWEEN  :START {$operator} :END ";
        $params = array(":START" => $start, ":END" => $end);
        $this->params = array_merge($this->params, $params);
    }

    public function getCriteria($model) {

        $arraCriteria = array();

        $keys = array();
        $values = array();
        foreach ($this->conditions as $key => $value) {
            $keys[] = $key;
            $values[] = $value;
        }

        foreach ($this->params as $key => $value) {
            $this->condition = str_replace($key, $model->escape($value), $this->condition);
        }

        if (!is_null($this->condition))
            $keys[] = $this->condition;

        $condition[0] = implode(" AND ", $keys);

        $i = 1;
        foreach ($values as $val) {
            $condition[$i] = $val;
            $i++;
        }
        $arraCriteria['conditions'] = $condition;

        if (!is_null($this->select))
            $arraCriteria['select'] = $this->select;

        if (!is_null($this->group))
            $arraCriteria['group'] = $this->group;

        if (!is_null($this->limit))
            $arraCriteria['limit'] = $this->limit;

        if (!is_null($this->offset))
            $arraCriteria['offset'] = $this->offset;

        if (!is_null($this->join))
            $arraCriteria['joins'] = $this->join;
        
        if (!is_null($this->order))
            $arraCriteria['order'] = $this->order;


        return $arraCriteria;
    }

}

?>
