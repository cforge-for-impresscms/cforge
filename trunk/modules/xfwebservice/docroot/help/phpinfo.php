<?php
/**
 * $Id: phpinfo.php,v 1.4 2004/03/01 23:27:34 danreese Exp $
 * (c) 2004 Novell, Inc.
 *
 * Displays information about PHP.
 */

// Check authorization.
$perm =& get_permissions(session_id(), 'xoopsforge');
if (!$perm || !$perm->isSuperUser())
{
	// Not authorized.
	$tpl = TEMPLATE_CONTEXT.'error.tpl';
	$args['id'] = 12;
	$args['contents'] = _XFWEBSERVICE_ERROR_012;
	echo fill_template($tpl, $args);
	return;
}

// Return PHP information.
echo phpinfo();
?>