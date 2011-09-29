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
	*  Update forum count
	*/
	$res = db_query("DELETE FROM ".$dbprefix."_xf_forum_agg_msg_count;");
	echo db_error();
	 
	$sql = "INSERT INTO ".$dbprefix."_xf_forum_agg_msg_count " ."SELECT fgl.group_forum_id,count(f.msg_id) " ."FROM ".$dbprefix."_xf_forum_group_list fgl " ."LEFT JOIN ".$dbprefix."_xf_forum f USING(group_forum_id) " ."GROUP BY fgl.group_forum_id;";
	 
	$res = db_query($sql);
	echo db_error();
	 
	db_query("VACUUM ANALYZE ".$dbprefix."_xf_forum_agg_msg_count;");
	 
	include "footer.php";
?>