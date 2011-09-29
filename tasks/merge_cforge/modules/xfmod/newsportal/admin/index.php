<?php
	/**
	*
	* Author: Paul Jones
	*
	*
	* @version   $Id: index.php,v 1.12 2004/03/22 18:34:48 devsupaul Exp $
	*
	*/
	 
	 
	include_once("../../../../mainfile.php");
	 
	$langfile = "forum.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) $ {
		$k }
	 = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) $ {
		$k }
	 = StopXSS($v);
	 
	if ($icmsForge['forum_type'] != 'newsportal')
	redirect_header(ICMS_URL."/modules/xfmod/forum/?group_id=$group_id", 4, "");
	 
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
	 
	include_once("../../../../header.php");
	 
	echo project_title($group);
	echo project_tabs('forums', $group_id);
	 
	echo "<a href='".$_SERVER['PHP_SELF']."?group_id=".$group_id."'><strong>"._XF_G_ADMIN."</strong></a><br>";
	if ($feedback) echo "<div class='errorMsg'>$feedback</div>";
	 
	$groupname = $_GET["groupname"];
	$action = $_GET["action"];
	switch($action)
	{
		case "newgroup":
		 
		include_once("utils.php");
		if (validate_forum_name($short_name))
		{
			$groupname = $icmsForge['nntp_base'].".".$group->getUnixName().".".$short_name;
			$result = $icmsDB->query("SELECT count(forum_name) FROM ".$icmsDB->prefix("xf_forum_nntp_list")." WHERE forum_name='$groupname'");
			$row = $icmsDB->fetchRow($result);
			if ($row[0] == 0)
			{
				$message = control_group($action, $groupname);
				if (substr($message, 0, 3) == "240")
				{
					$icmsDB->queryF("INSERT INTO ".$icmsDB->prefix("xf_forum_nntp_list")." VALUES($group_id, '$groupname', '$desc_name')");
					$feedback = "A request for a new forum was successfully sent.
						It should be available within an hour by both
						<a href='news://".$icmsForge['nntp_server']."/$groupname'>NNTP</a>
						and <a href='".ICMS_URL."/modules/xfmod/newsportal/thread.php?group_id=$group_id&group=$groupname'>HTTP</a>.
						<br><br>";
				}
				else
					{
					$feedback = $message."\r\n<BR>";
				}
			}
			else
				{
				$feedback = "The newsgroup $short_name already exists.  Please choose a different short name";
			}
		}
		else
			{
			$feedback = $GLOBALS['register_error'];
		}
		break;
		case "rmgroup":
		include_once("utils.php");
		if (!ereg("^".$icmsForge['nntp_base'].".".$group->getUnixName(), $groupname))
		{
			$feedback = "You may only remove forums that belong to your group";
			break;
		}
		$icmsDB->queryF("DELETE FROM ".$icmsDB->prefix("xf_forum_nntp_list")." WHERE forum_name='$groupname' AND group_id='$group_id'");
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
		$icmsDB->queryF("UPDATE ".$icmsDB->prefix("xf_forum_nntp_list")." SET forum_desc_name='$desc_name' WHERE forum_name='$groupname' AND group_id='$group_id'");
		break;
		default:
		 
	}
	 
	echo "<font color=red>".$feedback."</font>";
	 
	$result = $icmsDB->query("SELECT count(forum_name) FROM ".$icmsDB->prefix("xf_forum_nntp_list")." WHERE group_id='$group_id'");
	$row = $icmsDB->fetchRow($result);
	if ($row[0] >= $icmsForge['max_forums'] && !$perm->isSuperUser())
	{
		echo "<p>You may only create ".$icmsforge['max_forums']." newsgroups.  If you need another one contact the administrator of this site.</p>";
	}
	else
		{
	?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="GET">
<table border=0>
<th>
<td>Discriptive Name</td>
<td><input type="text" name="desc_name" size="30" maxlength="128"></td>
<td>ex. Developer Forum</td>
</th><th>
<td>Short Name</td>
<td><input type="text" name="short_name" size="30" maxlength="40"></td>
<td>ex. devforum</td>
</th>
</table>
<input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
<input type="hidden" name="action" value="newgroup">
<input type="submit" value="Add Forum">
</form><BR><BR>
<?php } ?>

	<?php
		$result = $icmsDB->query("SELECT forum_name, forum_desc_name FROM ".$icmsDB->prefix("xf_forum_nntp_list")." WHERE group_id='$group_id'");
		 
		if ($result && $icmsDB->getRowsNum($result) > 0)
		{
		?>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="GET">
<select name="groupname" size=1>
<OPTION>
		<?php
			while ($row = $icmsDB->fetchArray($result))
			{
				echo "\r\n<option value='".$row['forum_name']."'>".$row['forum_desc_name'];
			}
		?>
</select>
<p/>
<input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
<input type="hidden" name="action" value="rmgroup">
<input type="submit" value="Remove Forum">
</form><BR><BR>
<p/>
<form action="<?php echo $_SERVER['PHP_SELF'];?>" method="GET">
Change descriptive name from <select name="groupname" size=1>
<OPTION>
		<?php
			$result = $icmsDB->query("SELECT forum_name, forum_desc_name FROM ".$icmsDB->prefix("xf_forum_nntp_list")." WHERE group_id='$group_id'");
			while ($row = $icmsDB->fetchArray($result))
			{
				echo "\r\n<option value='".$row['forum_name']."'>".$row['forum_desc_name'];
			}
		?>
</select>
to <input type="text" name="desc_name" size="30" maxlength="128">
<input type="hidden" name="group_id" value="<?php echo $group_id; ?>">
<input type="hidden" name="action" value="moddesc">
<input type="submit" value="Change">
</form><BR><BR>

		<?php
		}
		include_once("../../../../footer.php");
	?>