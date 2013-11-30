<?php
/**
 * JTree
 *
 * This class implements the Tree structure and is based on linked list using a hash table.
 * Using hash table prevents all possible recursive references and
 * allows for more efficient garbage collection. A particularly sore point in PHP.
 *
 * I have used my implementation of Doubly Linked list as my base.
 * You can find more information on it here:
 * http://phptouch.com/2011/03/15/doubly-linked-list-in-php/
 *
 * I have heavily relied on the following 2 references for their algorithms.
 * Beginning Algorithims by Simon Harris and James Ross. Wrox publishing.
 * Data Structures and Algorithms in Java Fourth Edition by Michael T. Goodrich
 * and Roberto Tamassia. John Wiley & Sons.
 *
 * *********************LICENSE****************************************
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * *********************LICENSE****************************************
 * @package JTree
 * @author Jayesh Wadhwani
 * @copyright 2011 Jayesh Wadhwani.
 * @license  GNU GENERAL PUBLIC LICENSE 3.0
 * @version 1.0
 */
class JTree {
   /**
    * @var UID for the header node
   */
    private $_head;
 
   /**
    * @var size of list
    */
    private $_size;
    
   /**
   * @var hash table to store node objects
   */
    private $_list = array();
 
    /**
     * JTree::__construct()
     *
     * @return
     */
    public function __construct() {
        $this->_head = $this->createNode('HEAD');
        $this->_size = 0;
    }
 
    /**
    * JTree::getList()
    *
    * Retreives the hash table
    *
    * @return array
    */
    public function getTree() {
        return $this->_list;
    }
 
   /**
   * JTree::getNode()
   * Given a UID get the node object
   *
   * @param mixed $uid
   * @return JNode/Boolean
   */
    public function getNode($uid) {
        if(empty($uid)) {
            throw new Exception('A unique id is required.');
        }
        $ret = false;
      //look for the node in the hash table
      //return false if not found
        if(array_key_exists($uid,$this->_list) === true) {
            $ret = $this->_list[$uid];
        }
        return $ret;
    }
 
   /**
    * JTree::setChild()
    *
    * This is a helper function. Given a UID for a node
    * set it as the next UID for the node.
    *
    * @param mixed $uid
    * @param mixed $childUid
    * @return void
    */
    public function setChild($uid, $childUid) {
        if(empty($uid) || empty($childUid)) {
            throw new Exception('Both a from and a to UIDs are required.');
        }
      //get the node object for this node uid
        $node = $this->getNode($uid);
 
        if($node !== false) {
            $node->setChild($childUid);
        }
    }
 
   /**
    * JTree::setParent()
    *
    * This is a helper function to set the parent uid
    *
    * @param mixed $uid - UID of the object to be processed on
    * @param mixed $prevUid - put this as next in the above object
    * @return void
    */
    public function setParent($uid, $parentUid) {
        if(empty($uid) || empty($parentUid)) {
            throw new Exception('Both a from and a to UIDs are required.');
        }
        $node = $this->getNode($uid);
 
        if($node !== false) {
            $node->setParent($parentUid);
        }
    }
 
    /**
     * JTree::createNode()
     *
    * Create a node, store in hash table
    * return the reference uid
     * @param mixed $value
     * @param mixed $uid
     * @return string $uid
     */
    public function createNode($value, $uid = null) {
        if(!isset($value)) {
            throw new Exception('A value is required to create a node');
        }
 
        $node = new JNode($value, $uid);
        $uid = $node->getUid();
        $this->_list[$uid] = $node;
        return $uid;
    }
 
    /**
     * JTree::addChild()
     *
     * @param mixed $parentUid
     * @param mixed $childUid
     * @return
     */
    public function addChild($parentUid = null, $childUid) {
        if(empty($childUid)) {
            throw new Exception('A UID for the child is required.');
        }
      //if no parent assume it is the head
        if(empty($parentUid)) {
            $parentUid = $this->_head;
        }
        //parent points to child
        $this->setChild($parentUid, $childUid);
 
        //child points to parent
        $this->setParent($childUid, $parentUid);
 
        return $childUid;
    }
 
    /**
     * JTree::addFirst()
    * Add the first child right after the head
     *
     * @param mixed $uid
     * @return void
     */
    public function addFirst($uid) {
        if(empty($uid)) {
            throw new Exception('A unique ID is required.');
        }
        $this->addChild($this->_head, $uid);
    }
 
   /**
    * JTree::getChildren()
    *
    * This is a helper function to get the child node uids given the node uid
    *
    * @param mixed $uid
    * @return mixed
    */
    public function getChildren($uid) {
        if(empty($uid)) {
            throw new Exception('A unique ID is required.');
        }
 
        $node = $this->getNode($uid);
 
        if($node !== false) {
            return $node->getChildren();
        }
    }
 
   /**
    * JTree::getParent()
    *
    * This is a helper function to get the
    * parent node uid
    *
    * @param mixed $uid
    * @return string $uid
    */
    public function getParent($uid) {
        if(empty($uid)) {
            throw new Exception('A unique ID is required.');
        }
        $ret = false;
      $node = $this->getNode($uid);
 
        if($node !== false) {
            $ret = $node->getParent();
        }
      return $ret;
    }
 
    /**
     * JTree::getValue()
     *
     * @param mixed $uid
     * @return
     */
    public function getValue($uid) {
        if(empty($uid)) {
            throw new Exception('A unique ID is required.');
        }
 
        $node = $this->getNode($uid);
        return $node->getValue();
    }
}
?>