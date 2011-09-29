<?php
	/**
	*
	* Record a Logo Impression
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: xflogo.php,v 1.2 2003/12/09 15:03:36 devsupaul Exp $
	*
	*/
	include("../../mainfile.php");
	require_once("include/pre.php");
	 
	// output image
	header("Content-Type: image/png");
	header("Cache-Control: no-cache, must-revalidate");
	header("Pragma: no-cache");
	 
	if (!$group_id)
	{
		echo 'xxxxx NO GROUP ID xxxxxxx';
		exit;
	}
	 
	echo readfile(ICMS_ROOT_PATH.'modules/xfmod/images/xflogo-155-1.gif');
?>