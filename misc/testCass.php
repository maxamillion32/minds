<?php

require_once(dirname(dirname(__FILE__)) . '/engine/start.php');


$db = new minds\core\data\call('entities', 'minds', array('10.0.9.10'));
foreach($db->get("",1000) as $k => $v){
var_dump($k);

}
