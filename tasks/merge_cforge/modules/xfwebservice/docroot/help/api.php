<?php
	/**
	* $Id: api.php,v 1.3 2004/02/23 22:23:58 danreese Exp $
	* (c) 2004 Novell, Inc.
	*
	* Displays help commands.
	*/
	header('Content-type: text/xml');
	 
	// FIX: Not done.
	$tpl = TEMPLATE_CONTEXT.'success.tpl';
	echo fill_template($tpl);
?>