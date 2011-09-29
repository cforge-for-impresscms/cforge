<?php
	/**
	* $Id: index.php,v 1.3 2004/02/26 20:21:53 danreese Exp $
	* (c) 2004 Novell, Inc.
	*
	* Handles authentication commands.
	*/
	include '../common.php';
	 
	$command = $_POST['c'];
	execute_command($command);
?>