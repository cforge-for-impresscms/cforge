<?php

$sql = "SELECT name,value FROM ".$xoopsDB->prefix("xf_config");
$result = $xoopsDB->query($sql);
while(list($name,$value) = $xoopsDB->fetchRow($result)){
	$xoopsForge[$name] = $value;
}

?>