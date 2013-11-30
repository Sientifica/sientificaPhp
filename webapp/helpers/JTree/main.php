<?php
include("JNode.php");
include("JTree.php");
include("JTreeRecursiveIterator.php");
include("JTreeIterator.php");

//create a new tree object
$jt = new JTree();
$categories = array();
$categories[] = array('id' => 1, 'weather_condition' => 'weather', 'parent_id' => 0);
$categories[] = array('id' => 2, 'weather_condition' => 'Earthquakes', 'parent_id' => 1);
$categories[] = array('id' => 3, 'weather_condition' => 'Major', 'parent_id' => 2);
$categories[] = array('id' => 4, 'weather_condition' => 'Minor', 'parent_id' => 2);
$categories[] = array('id' => 5, 'weather_condition' => 'Fires', 'parent_id' => 1);
$categories[] = array('id' => 6, 'weather_condition' => 'Rain', 'parent_id' => 1);
$categories[] = array('id' => 7, 'weather_condition' => 'Flooding', 'parent_id' => 6);
$categories[] = array('id' => 8, 'weather_condition' => 'Washout', 'parent_id' => 6);
$categories[] = array('id' => 9, 'weather_condition' => 'Hurricanes', 'parent_id' => 1);
//iterate building the tree
foreach($categories as $category) {
    $uid = $jt->createNode($category['weather_condition'],$category['id']);
    $parentId = null;
 
    if(!empty($category['parent_id'])) {
        $parentId = $category['parent_id'];
    }
 
    $jt->addChild($parentId, $uid);
}
 
$it = new JTreeRecursiveIterator($jt, new JTreeIterator($jt->getTree()), true);
 
//iterate to create the ul list
foreach($it as $k => $v) {}
?>