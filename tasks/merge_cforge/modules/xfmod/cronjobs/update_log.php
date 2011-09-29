#!/usr/bin/php -q
<?php
	 
	include "db.php";
	include "header.php";
	/*******************************************************************************
	*                                                                             *
	*                                                                             *
	*                                                                             *
	*                                                                             *
	*                                                                             *
	*                                                                             *
	*                                                                             *
	******************************************************************************/
	/**
	*  Update Log
	*/
	$in = db_query("INSERT INTO ".$dbprefix."_xf_cronjob_log(updatetime) VALUES('".time()."')");
	echo db_error();
	 
	include "footer.php";
?>