<?php
/**
 * $Id: targets.php,v 1.1 2004/03/12 16:38:24 danreese Exp $
 * (c) 2004 Novell, Inc.
 *
 * Returns a list of targets.
 */
header('Content-type: text/xml');

// Send XML-RPC message.
require_once '../xmlrpc.php';
list($success, $targets) = XMLRPC_request(BUILD_HOST, BUILD_PATH, 'targets');
if (!$success)
{
	// Could not determine targets.
	$tpl = TEMPLATE_CONTEXT.'error.tpl';
	$args['id'] = 230;
	$args['contents'] = _XFWEBSERVICE_ERROR_230.': '.$targets['faultString'].' ('.$targets['faultCode'].')';
	echo fill_template($tpl, $args);
	return;
}

// Return target list.
$target_tpl = TEMPLATE_CONTEXT.'target.tpl';
$contents = '';
foreach ($targets as $target)
{
	$contents .= fill_template($target_tpl, array('name' => $target));
}

// Return file list.
$tpl = TEMPLATE_CONTEXT.'list.tpl';
$args['contents'] = $contents;
echo fill_template($tpl, $args);
?>