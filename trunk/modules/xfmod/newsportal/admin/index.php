<?php
/**
  *
  * Author: Paul Jones
  *
  *
  * @version   $Id: index.php,v 1.12 2004/03/22 18:34:48 devsupaul Exp $
  *
  */


include_once ("../../../../mainfile.php");

$langfile="forum.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");

if($xoopsForge['forum_type']!='newsportal')
	redirect_header(XOOPS_URL."/modules/xfmod/forum/?group_id=$group_id",4,"");

// get current information
project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if (!$perm->isForumAdmin()) {
  redirect_header($GLOBALS["HTTP_REFERER"],2,_XF_G_PERMISSIONDENIED."<br />"._XF_FRM_YOUARENOTFORUMADMIN);
  exit;
}

include_once ("../../../../header.php");

echo project_title($group);
echo project_tabs ('forums', $group_id);

echo "<a href='".$_SERVER['PHP_SELF']."?group_id=".$group_id."'><b>"._XF_G_ADMIN."</b></a><br>";
if($feedback) echo "<div class='errorMsg'>$feedback</div>";

$groupname=$_GET["groupname"];
$action=$_GET["action"];
switch($action){
	case "newgroup":

		include_once("utils.php");
		if(validate_forum_name($short_name)){
			$groupname=$xoopsForge['nntp_base'].".".$group->getUnixName().".".$short_name;
			$result = $xoopsDB->query("SELECT count(forum_name) FROM ".$xoopsDB->prefix("xf_forum_nntp_list")." WHERE forum_name='$groupname'");
			$row = $xoopsDB->fetchRow($result);
			if($row[0] == 0){
				$message = control_group($action, $groupname);
				if (substr($message,0,3)=="240") {
					$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_forum_nntp_list")." VALUES ($group_id, '$groupname', '$desc_name')");
					$feedback = "A request for a new forum was successfully sent.
							It should be available within an hour by both
							<a href='news://".$xoopsForge['nntp_server']."/$groupname'>NNTP</a>
							and <a href='".XOOPS_URL."/modules/xfmod/newsportal/thread.php?group_id=$group_id&group=$groupname'>HTTP</a>.
							<br><br>";
				}else{
					$feedback = $message."\n<BR>";
				}
			}else{
				$feedback = "The newsgroup $short_name already exists.  Please choose a different short name";
			}
		}else{
			$feedback = $GLOBALS['register_error'];
		}
		break;
	case "rmgroup":
		include_once("utils.php");
		if(!ereg("^".$xoopsForge['nntp_base'].".".$group->getUnixName(),$groupname)){
			$feedback = "You may only remove forums that belong to your group";
			break;
		}
		$xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_forum_nntp_list")." WHERE forum_name='$groupname' AND group_id='$group_id'");
		$message = control_group($action, $groupname);
		if (substr($message,0,3)=="240") {
			//insert the new group into the database also
			$feedback = "A request to remove the forum was successfully sent.  It should be removed within an hour.";
		}else{
			$feedback = $message."\n<BR>";
		}
		break;
	case "moddesc":
		$xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_forum_nntp_list")." SET forum_desc_name='$desc_name' WHERE forum_name='$groupname' AND group_id='$group_id'");
		break;
	default:

}

echo "<font color=red>".$feedback."</font>";

$result = $xoopsDB->query("SELECT count(forum_name) FROM ".$xoopsDB->prefix("xf_forum_nntp_list")." WHERE group_id='$group_id'");
$row = $xoopsDB->fetchRow($result);
if($row[0]>=$xoopsForge['max_forums'] && !$perm->isSuperUser()){
	echo "<p>You may only create ".$xoopsforge['max_forums']." newsgroups.  If you need another one contact the administrator of this site.</p>";
}else{
?>
<FORM action="<?php echo $_SERVER['PHP_SELF'];?>" method="GET">
<TABLE border=0>
<TR>
<TD>Discriptive Name</TD>
<TD><INPUT type="text" name="desc_name" size="30" maxlength="128"></TD>
<TD>ex. Developer Forum</TD>
</TR><TR>
<TD>Short Name</TD>
<TD><INPUT type="text" name="short_name" size="30" maxlength="40"></TD>
<TD>ex. devforum</TD>
</TR>
</TABLE>
<INPUT type="hidden" name="group_id" value="<?php echo $group_id; ?>">
<INPUT type="hidden" name="action" value="newgroup">
<INPUT type="submit" value="Add Forum">
</FORM><BR><BR>
<?php } ?>

<?php
$result = $xoopsDB->query("SELECT forum_name, forum_desc_name FROM ".$xoopsDB->prefix("xf_forum_nntp_list")." WHERE group_id='$group_id'");

if ($result && $xoopsDB->getRowsNum($result) > 0)
{
	?>
	<FORM action="<?php echo $_SERVER['PHP_SELF'];?>" method="GET">
	<SELECT name="groupname" size=1>
	<OPTION>
	<?php
	while($row = $xoopsDB->fetchArray($result)){
		echo "\n<OPTION value='".$row['forum_name']."'>".$row['forum_desc_name'];
	}
	?>
	</SELECT>
	<p/>
	<INPUT type="hidden" name="group_id" value="<?php echo $group_id; ?>">
	<INPUT type="hidden" name="action" value="rmgroup">
	<INPUT type="submit" value="Remove Forum">
	</FORM><BR><BR>
	<p/>
	<FORM action="<?php echo $_SERVER['PHP_SELF'];?>" method="GET">
	Change descriptive name from <SELECT name="groupname" size=1>
	<OPTION>
	<?php
	$result = $xoopsDB->query("SELECT forum_name, forum_desc_name FROM ".$xoopsDB->prefix("xf_forum_nntp_list")." WHERE group_id='$group_id'");
	while($row = $xoopsDB->fetchArray($result)){
		echo "\n<OPTION value='".$row['forum_name']."'>".$row['forum_desc_name'];
	}
	?>
	</SELECT>
	to <INPUT type="text" name="desc_name" size="30" maxlength="128">
	<INPUT type="hidden" name="group_id" value="<?php echo $group_id; ?>">
	<INPUT type="hidden" name="action" value="moddesc">
	<INPUT type="submit" value="Change">
	</FORM><BR><BR>

	<?php
}
    include_once ("../../../../footer.php");
?>