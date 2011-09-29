<?php
	include_once("../../mainfile.php");
	include_once(ICMS_ROOT_PATH."/modules/xfstats/blocks/stats.php");
	//include_once(ICMS_ROOT_PATH."/modules/xoopsmembers/blocks/members_new.php");
	 
	function topdownloads()
	{
		return b_stats_topdownloads();
		// themesidebox($block['title'], $block['content']);
	}
	 
	function mostactiveprojects()
	{
		return b_stats_mostactive();
		// themesidebox($block['title'], $block['content']);
	}
	 
	function mostactiveusers()
	{
		return b_stats_active_users();
		// themesidebox($block['title'], $block['content']);
	}
	function newprojects()
	{
		$block = b_stats_new_projects();
		return $block['content'];
	}
	 
	function newmembers()
	{
		// $block = b_xoopsmembers_new_show();
		// return $block['content'];
	}
?>