<?php
/**
 * $Id: logout.php,v 1.6 2004/03/01 23:27:34 danreese Exp $
 * (c) 2004 Novell, Inc.
 *
 * Ends a user session.
 */
header('Content-type: text/xml');

// Delete session and clear entry from online users table.
session_destroy();
if (is_object($xoopsUser))
{
	$online_handler =& xoops_gethandler('online');
	$online_handler->destroy($xoopsUser->getVar('uid'));
}

// Return success.
echo fill_template(TEMPLATE_CONTEXT.'success.tpl');
?>