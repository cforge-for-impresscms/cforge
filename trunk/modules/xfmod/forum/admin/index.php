<?php
/**
  *
  * SourceForge Forums Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.6 2004/01/29 22:56:06 jcox Exp $
  *
  */


include_once ("../../../../mainfile.php");

$langfile="forum.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");

// novell forge support only nntp type.
// back to the previeous version
//if($xoopsForge['forum_type']!='newsportal')
//	redirect_header(XOOPS_URL."/modules/xfmod/forum/?group_id=$group_id",4,"");

// get current information
project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if (!$perm->isForumAdmin()) {
  redirect_header($GLOBALS["HTTP_REFERER"],2,_XF_G_PERMISSIONDENIED."<br />"._XF_FRM_YOUARENOTFORUMADMIN);
  exit;
}

include ("../../../../header.php");

echo project_title($group);
echo project_tabs ('forums', $group_id);

echo "<a href='".$_SERVER['PHP_SELF']."?group_id=".$group_id."'><b>"._XF_G_ADMIN."</b></a>";
echo "<p>".$feedback."</p>";


//force the data to come in through a post so that people can not
//supply groups to remove on the url line in a browser.  This is not
//actually helping security much, but it stops the general user from
//screwing things up on accident
$groupname=$_POST["groupname"];
$action=$_POST["action"];
switch($action){
	case "newgroup":
		if(validate_forum_name($short_name)) {
		     forum_create_forum($group_id,$short_name,$is_public,1,$desc_name);
		}else{
			$feedback = $GLOBALS['register_error'];
		}
		break;
/*
		include("utils.php");
		if(validate_forum_name($short_name)){
			$groupname=$xoopsForge['nntp_base'].".".$group->getUnixName().".".$short_name;
			$result = $xoopsDB->query("SELECT count(forum_name) FROM ".$xoopsDB->prefix("xf_forum_nntp_list")." WHERE forum_name='$groupname'");
			$row = $xoopsDB->fetchRow($result);
			if($row[0] == 0){
				$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_forum_nntp_list")." VALUES ($group_id, '$groupname', '$desc_name')");
				$message = control_group($action, $groupname);
				if (substr($message,0,3)=="240") {
					//insert the new group into the database also
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
*/
	case "rmgroup":
		include("utils.php");
		if(!ereg("^".$xoopsForge['nntp_base'].".".$group->getUnixName(),$groupname)){
			$feedback = "You may only remove forums that belong to your group";
			break;
		}
		$xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_forum_nntp_list")." WHERE forum_name='$groupname'");
		$message = control_group($action, $groupname);
		if (substr($message,0,3)=="240") {
			//insert the new group into the database also
			$feedback = "A request to remove the forum was successfully sent.  It should be removed within an hour.";
		}else{
			$feedback = $message."\n<BR>";
		}
		break;
	case "moddesc":
		$xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_forum_group_list")." SET description='$desc_name' WHERE forum_name='$groupname'");
		break;
	default:

}

echo "<font color=red>".$feedback."</font>";
?>
<FORM action="<?php echo $_SERVER['PHP_SELF'];?>?group_id=<?php echo $group_id;?>" method="POST">
<TABLE border=0>
<TR>
<TD>Descriptive Name</TD>
<TD><INPUT type="text" name="desc_name" size="30" maxlength="128"></TD>
<TD>ex. Developer Forum</TD>
</TR><TR>
<TD>Short Name</TD>
<TD><INPUT type="text" name="short_name" size="30" maxlength="40"></TD>
<TD>ex. devforum</TD>
</TR>
</TABLE>
<?php echo '<B>'._XF_G_ISPUBLIC.'</B><BR>
<INPUT TYPE="RADIO" NAME="is_public" VALUE="1" CHECKED> '._YES.'<BR>
<INPUT TYPE="RADIO" NAME="is_public" VALUE="0"> '._NO.'<P>
<P>'?>

<INPUT type="hidden" name="action" value="newgroup">
<INPUT type="submit" value="Add Forum">
</FORM>

<?php
$result = $xoopsDB->query("SELECT forum_name, description FROM ".$xoopsDB->prefix("xf_forum_group_list")." WHERE group_id='$group_id'");
if ($result && $xoopsDB->getRowsNum($result) > 0)
{
	?>
	<FORM action="<?php echo $_SERVER['PHP_SELF'];?>?group_id=<?php echo $group_id;?>" method="POST">
	<SELECT name="groupname" size=1>
	<OPTION>
	<?php
	while($row = $xoopsDB->fetchArray($result)){
		echo "\n<OPTION value='".$row['forum_name']."'>".$row['description'];
	}
	?>
	</SELECT>
	<INPUT type="hidden" name="action" value="rmgroup">
	<INPUT type="submit" value="Remove Forum">
	</FORM>

	<FORM action="<?php echo $_SERVER['PHP_SELF'];?>?group_id=<?php echo $group_id;?>" method="POST">
	Change descriptive name from <SELECT name="groupname" size=1>
	<OPTION>
	<?php
	$result = $xoopsDB->query("SELECT forum_name, description FROM ".$xoopsDB->prefix("xf_forum_group_list")." WHERE group_id='$group_id'");
	while($row = $xoopsDB->fetchArray($result)){
		echo "\n<OPTION value='".$row['forum_name']."'>".$row['description'];
	}
	?>
	</SELECT>
	to <INPUT type="text" name="desc_name" size="30" maxlength="128">
	<INPUT type="hidden" name="action" value="moddesc">
	<INPUT type="submit" value="Change">
	</FORM>
<?php
}
    include ("../../../../footer.php");
?>