<?php
 
$sql = "SELECT name,value FROM ".$icmsDB->prefix("xf_config");
$result = $icmsDB->query($sql);
while (list($name, $value) = $icmsDB->fetchRow($result))
{
	$icmsForge[$name] = $value;
}
 
?>