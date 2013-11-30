<?php
/**
 * JNode
 *
 * This is a simple class to construct a node
 * Please note that each node object will be
 * eventually stored in a hash table where the
 * hash will be a UID.
 *
 * Note that in comparison to thee Doubly Linked List implementation
 * the children are now stored in an array
 *
 * @package JTree  
 * @author Jayesh Wadhwani
 * @copyright Jayesh Wadhwani
 * @version 2011
 */
class JNode {
   /**
    * @var _value for the value field
   */
    private $_value;
   /**
    * @var _parent uid of the parent node
   */
    private $_parent;
   /**
    * @var _children collection of uids for the child nodes
   */
    private $_children = array();
   /**
    * @var _uid for this node
   */
    private $_uid;
 
    /**
     * JNode::__construct()
     *
     * @param mixed $value
     * @param mixed $uid
     * @return void
     */
    public function __construct($value = null,$uid = null) {
        if(!isset($value)) {
            throw new Exception('A value is required to create a node');
        }
        $this->setValue($value);
        $this->setUid($uid);
    }
 
    /**
     * JNode::setUid()
     *
     * @param mixed $uid
     * @return
     */
    public function setUid($uid = null) {
        //if uid not supplied...generate
        if(empty($uid)) {
            $this->_uid = uniqid();
        } else {
            $this->_uid = $uid;
        }
    }
 
    /**
     * JNode::getUid()
     *
     * @return string uid
     */
    public function getUid() {
        return $this->_uid;
    }
 
    /**
     * JNode::setValue()
     *
     * @param mixed $value
     * @return void
     */
    public function setValue($value) {
        $this->_value = $value;
    }
 
    /**
     * JNode::getValue()
     *
     * @return mixed
     */
    public function getValue() {
        return $this->_value;
    }
 
    /**
     * JNode::getParent()
     *
    * gets the uid of the parent node
    *
     * @return string uid
     */
    public function getParent() {
        return $this->_parent;
    }
 
    /**
     * JNode::setParent()
     *
     * @param mixed $parent
     * @return void
     */
    public function setParent($parent) {
        $this->_parent = $parent;
    }
 
    /**
     * JNode::getChildren()
     *
     * @return mixed
     */
    public function getChildren() {
        return $this->_children;
    }
 
    /**
     * JNode::setChild()
     *
    * A child node's uid is added to the childrens array
    *
     * @param mixed $child
     * @return void
     */
    public function setChild($child) {
        if(!empty($child)) {
            $this->_children[] = $child;
        }
    }
 
    /**
     * JNode::anyChildren()
     *
    * Checks if there are any children
    * returns ture if it does, false otherwise
    *
     * @return bool
     */
    public function anyChildren() {
        $ret = false;
 
        if(count($this->_children) > 0) {
            $ret = true;
        }
        return $ret;
    }
 
    /**
     * JNode::childrenCount()
     *
    * returns the number of children
    *
     * @return bool/int
     */
    public function childrenCount() {
      $ret = false;
     if(is_array($this->_children)){
      $ret = count($this->_children);
     }
     return $ret;
    }
}
?>