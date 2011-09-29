<?php
/**
 * logger.php
 *
 * SourceForge: Breaking Down the Barriers to Open Source Development
 * Copyright 1999-2001 (c) VA Linux Systems
 * http://sourceforge.net
 *
 * @version   $Id: logger.php,v 1.1.1.1 2003/08/01 19:13:48 devsupaul Exp $
 */

/*
	Determine group
*/
if (isset($group_id) && $group_id) {
	$log_group = $group_id;
} else if (isset($form_grp) && $form_grp) {
	$log_group = $form_grp;
} else {
  $log_group = 0;
}

$sql = "INSERT INTO ".$xoopsDB->prefix("xf_activity_log")." (day,hour,group_id,browser,ver,platform,time,page,type) "
      ."VALUES (".date('Ymd', mktime()).",'".date('H', mktime())."','$log_group','".browser_get_agent()."','".browser_get_version()."','".browser_get_platform()."','".time()."','".$_SERVER['PHP_SELF']."','0')";

$res_logger = $xoopsDB->queryF($sql);

//
//	temp hack
//
$sys_db_is_dirty = false;

if (!$res_logger) {
	echo "An error occured in the logger.";
	echo $xoopsDB->error();
	exit;
}

?>