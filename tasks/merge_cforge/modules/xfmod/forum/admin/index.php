<?php
	/**
	*
	* SourceForge Forums Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: index.php,v 1.6 2004/01/29 22:56:06 jcox Exp $
	*
	*/
	 
	 
	include_once("../../../../mainfile.php");
	 
	$langfile = "forum.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) $ {
		$k }
	 = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) $ {
		$k }
	 = StopXSS($v);
	 
	// novell forge support only nntp type.
	// back to the previeous version
	//if($icmsForge['forum_type']!='newsportal')
	// redirect_header(ICMS_URL."/modules/xfmod/forum/?group_id=$group_id",4,"");
	 
	// get current information
	project_check_access($group_id);
	 
	// get current information
	$group = group_get_object($group_id);
	$perm = $group->getPermission($icmsUser);
	 
	if (!$perm->isForumAdmin())
	{
		redirect_header($_SERVER["HTTP_REFERER"], 2, _XF_G_PERMISSIONDENIED."<br />"._XF_FRM_YOUARENOTFORUMADMIN);
		exit;
	}
	 
	include("../../../../header.php");
	 
	echo project_title($group);
	echo project_tabs('forums', $group_id);
	 
	echo "<a href='".$_SERVER['PHP_SELF']."?group_id=".$group_id."'><strong>"._XF_G_ADMIN."</strong></a>";
	echo "<p>".$feedback."</p>";
	 
	 
	//force the data to come in through a post so that people can not
	//supply groups to remove on the url line in a browser.  This is not
	//actually helping security much, but it stops the general user from
	//screwing things up on accident
	$groupname = $_POST["groupname"];
	$action = $_POST["action"];
	switch($action)
	{
		case "newgroup":
		if (validate_forum_name($short_name))
		{
			forum_create_forum($group_id, $short_name, $is_public, 1, $desc_name);
		}
		else
			{
			$feedback = $GLOBALS['register_error'];
		}
		break;
		/*
		include("utils.php");
		if (validate_forum_name($short_name)){
		$groupname=$icmsForge['nntp_base'].".".$group->getUnixName().".".$short_name;
		$result = $icmsDB->query("SELECT count(forum_name) FROM ".$icmsDB->prefix("xf_forum_nntp_list")." WHERE forum_name='$groupname'");
		$row = $icmsDB->fetchRow($result);
		if ($row[0] == 0){
		$icmsDB->queryF("INSERT INTO ".$icmsDB->prefix("xf_forum_nntp_list")." VALUES($group_id, '$groupname', '$desc_name')");
		$message = control_group($action, $groupname);
		if (substr($message,0,3)=="240") {
		//insert the new group into the database also
		$feedback = "A request for a new forum was successfully sent.
		It should be available within an hour by both
		<a href='news://".$icmsForge['nntp_server']."/$groupname'>NNTP</a>
		and <a href='".ICMS_URL."/modules/xfmod/newsportal/thread.php?group_id=$group_id&group=$groupname'>HTTP</a>.
		<br><br>";
		}else{
		$feedback = $message."\r\n<BR>";
		}
		}else{
		$feedback = "The newsgroup $short_name already exists.  Please choose a different short name";
		}
		}else{
		$feedback = $GLOBALS['register_error'];
		}
		break;
		*/
		case "rmgroup":
		include("utils.php");
		if (!ereg("^".$icmsForge['nntp_base'].".".$group->getUnixName(), $groupname))
		{
			$feedback = "You may only remove forums that belong to your group";
			break;
		}
		$icmsDB->queryF("DELETE FROM ".$icmsDB->prefix("xf_forum_nntp_list")." WHERE forum_name='$groupname'");
		$message = control_group($action, $groupname);
		if (substr($message, 0, 3) == "240")
		{
			//insert the new group into the database also
			$feedback = "A request to remove the forum was successfully sent.  It should be removed within an hour.";
		}
		else
			{
			$feedback = $message."\r\n<BR>";
		}
		break;
		case "moddesc":
		$icmsDB->queryF("UPDATE ".$icmsDB->prefix("xf_forum_group_list")." SET description='$desc_name' WHERE forum_name='$groupname'");
		break;
		default:
		 
	}
	 
	echo "<font color=red>".$feedback."</font>";
?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>?group_id=<?php echo $group_id;?>" method="POST">
<table border=0>
<th>
<td>Descriptive Name</td>
<td><input type="text" name="desc_name" size="30" maxlength="128"></td>
<td>ex. Developer Forum</td>
</th><th>
<td>Short Name</td>
<td><input type="text" name="short_name" size="30" maxlength="40"></td>
<td>ex. devforum</td>
</th>
</table>
<?php echo '<strong>'._XF_G_ISPUBLIC.'</strong><BR>
<input type="radio" name="is_public" value="1" CHECKED> '._YES.'<BR>
<input type="radio" name="is_public" value="0"> '._NO.'<p>
<p>'?>

<input type="hidden" name="action" value="newgroup">
<input type="submit" value="Add Forum">
</form>

<?php
	$result = $icmsDB->query("SELECT forum_name, description FROM ".$icmsDB->prefix("xf_forum_group_list")." WHERE group_id='$group_id'");
	if ($result && $icmsDB->getRowsNum($result) > 0)
	{
	?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>?group_id=<?php echo $group_id;?>" method="POST">
<select name="groupname" size=1>
<OPTION>
	<?php
		while ($row = $icmsDB->fetchArray($result))
		{
			echo "\r\n<option value='".$row['forum_name']."'>".$row['description'];
		}
	?>
</select>
<input type="hidden" name="action" value="rmgroup">
<input type="submit" value="Remove Forum">
</form>

<form action="<?php echo $_SERVER['PHP_SELF'];?>?group_id=<?php echo $group_id;?>" method="POST">
Change descriptive name from <select name="groupname" size=1>
<OPTION>
	<?php
		$result = $icmsDB->query("SELECT forum_name, description FROM ".$icmsDB->prefix("xf_forum_group_list")." WHERE group_id='$group_id'");
		while ($row = $icmsDB->fetchArray($result))
		{
			echo "\r\n<option value='".$row['forum_name']."'>".$row['description'];
		}
	?>
</select>
to <input type="text" name="desc_name" size="30" maxlength="128">
<input type="hidden" name="action" value="moddesc">
<input type="submit" value="Change">
</form>
	<?php
	}
	include("../../../../footer.php");
?>