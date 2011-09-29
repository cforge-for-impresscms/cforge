<?php
/**
 * $Id: status.php,v 1.22 2004/04/16 19:34:46 danreese Exp $
 * (c) 2004 Novell, Inc.
 *
 * Checks the status on a build process.
 */
header('Content-type: text/xml');

// Extract build ID.
$buildID = $_GET['b'];
if (empty($buildID))
{
	// Invalid syntax.
	$tpl = TEMPLATE_CONTEXT.'error.tpl';
	$args['id'] = 11;
	$args['contents'] = _XFWEBSERVICE_ERROR_011;
	echo fill_template($tpl, $args);
	return;
}

// Retrieve build information.
$sql = 'SELECT * FROM '.$xoopsDB->prefix('xf_webservice_build')." WHERE id=$buildID";
$result = $xoopsDB->queryF($sql);
if (!$result)
{
	// Could not retrieve build status.
	$tpl = TEMPLATE_CONTEXT.'error.tpl';
	$args['id'] = 220;
	$args['contents'] = _XFWEBSERVICE_ERROR_220.': '.$xoopsDB->error();
	echo fill_template($tpl, $args);
	return;
}
elseif ($xoopsDB->getRowsNum($result) < 1)
{
	// Invalid build ID.
	$tpl = TEMPLATE_CONTEXT.'error.tpl';
	$args['id'] = 221;
	$args['contents'] = _XFWEBSERVICE_ERROR_221.': '.$buildID;
	echo fill_template($tpl, $args);
	return;
}

// Check authorization.
$status_array = $xoopsDB->fetchArray($result);
$perm =& get_permissions(session_id(), $status_array['unix_group_name']);
if (!$perm || !($perm->isAdmin() || $perm->isReleaseAdmin() || $perm->isReleaseTechnician()))
{
	// Not authorized.
	$tpl = TEMPLATE_CONTEXT.'error.tpl';
	$args['id'] = 12;
	$args['contents'] = _XFWEBSERVICE_ERROR_012;
	echo fill_template($tpl, $args);
}
else
{
	// Return status.
	$args['id'] = $buildID;
	$args['project'] = $status_array['unix_group_name'];
	$args['modules'] = $status_array['cvs_modules'];
	$args['target'] = $status_array['target'];
	$args['status'] = $status_array['status'];
	$args['start'] = $status_array['start_time'];
	$args['end'] = $status_array['end_time'];
	$args['elapsed'] = ($args['end'] ? $args['end'] : time()) - $args['start'];
	if ($status_array['status'] == 'failed' && !empty($status_array['error']))
	{
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$error_args['id'] = 13;
		$error_args['contents'] = _XFWEBSERVICE_ERROR_013.': '.$status_array['error'];
		$args['contents'] = fill_template($tpl, $error_args);
	}
	$tpl = TEMPLATE_CONTEXT.'build.tpl';
	echo fill_template($tpl, $args);
}
?>