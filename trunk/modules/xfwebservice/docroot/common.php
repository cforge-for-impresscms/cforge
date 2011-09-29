<?php
/**
 * $Id: common.php,v 1.15 2004/03/04 20:56:21 danreese Exp $
 * (c) 2004 Novell, Inc.
 *
 * Common functionality.
 */
ini_set('session.name', 's');
ini_set('session.use_cookies', false);
include_once 'config.php';

function execute_command($command)
{
	$file = $command.'.php';
	if (!empty($command) && file_exists($file))
	{
		global $xoopsDB;
		include $file;
	}
	else
	{
		// Command not found.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = '10';
		$args['contents'] = _XFWEBSERVICE_ERROR_010;
		echo fill_template($tpl, $args);
	}
}

function fill_template($file, $vars = null)
{
	if ($vars) extract($vars);
	ob_start();
	include $file;
	$result = ob_get_contents();
	ob_end_clean();
	return $result;
}

function &get_permissions($sessionID, $groupName)
{
	global $xoopsDB, $xoopsUser;
	require_once XOOPS_ROOT_PATH.'/modules/xfmod/include/pre.php';

	// Use group name to lookup group.
	$sql = 'SELECT * FROM '.$xoopsDB->prefix('xf_groups')." WHERE unix_group_name='$groupName'";
	$res2 = $xoopsDB->queryF($sql);
	if (!$res2 || $xoopsDB->getRowsNum($res2) < 1)
	{
		return false;
	}
	$group_array = $xoopsDB->fetchArray($res2);
	$group =& group_get_object($group_array['group_id']);

	// Use group and user to lookup permissions.
	return permission_get_object($group, $xoopsUser);
}
?>