<?php
/**
 * JTreeIterator
 *
 * The Tree structure would be incomplete if I did not include a
 * iterator. There is nothing special about this iterator and its implementation
 * is pretty standard.
 * I have extended the arrayIterator because I am using an array for my hash table. 
 * Note that I have not implemented the next and rewind methods as I do not need to
 * special with these. So the parent(ArrayIterator) methods will be called by default.
 *
 * @package   
 * @author  Jayesh Wadhwani
 * @copyright Jayesh Wadhwani
 * @version 2011
 */
class JTreeIterator extends ArrayIterator implements RecursiveIterator {
   /**
    * @var _list this is the hash table
   */
    private $_list = array();
   /**
    * @var _next this is for the children
   */
    private $_next = array();
   /**
    * @var _position the iterator position
   */
    private $_position;
 
    /**
     * JTreeIterator::__construct()
     *
     * @param mixed $list - the hash table
     * @param mixed $tree -
     * @return JTreeIterator
   */
    public function __construct(array $list, array $tree = null) {
        $this->_list = $list;
 
        if(is_null($tree)) {
            reset($this->_list);
            $next = current($this->_list);
            $this->_next = $next->getChildren();
        } else {
            $this->_next = $tree;
        }
 
        parent::__construct($this->_next);
    }
 
    /**
     * JTreeIterator::current()
     *
     * @return
   */
    public function current() {
      //get the object uid from the hash table
      //then get the object
        $current = parent::current();
        $nObj = $this->_list[$current];
        return $nObj;
    }
 
    /**
     * JTreeIterator::key()
     *
     * @return
     */
    public function key() {
        $key = parent::key();
        $key = $this->_next[$key];
        return $key;
    }
 
    /**
     * JTreeIterator::hasChildren()
     *
     * @return mixed
     */
    public function hasChildren() {
        $next = $this->_list[$this->key()];
        $next = $next->anyChildren();
        return $next;
    }
 
    /**
     * JTreeIterator::getChildren()
     *
     * @return JTreeIterator
     */
    public function getChildren() {
        $childObj = $this->_list[$this->key()];
        $children = $childObj->getChildren();
        return new JTreeIterator($this->_list, $children);
    }
}
?>