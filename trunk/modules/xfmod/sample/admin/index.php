<?php
/**
  *
  * SourceForge Documentaion Manager
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.9 2004/03/08 18:25:05 devsupaul Exp $
  *
  */


/*
        Docmentation Manager
        by Quentin Cregan, SourceForge 06/2000
*/
include_once("../../../../mainfile.php");

$langfile="sample.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/sample/sample_utils.php");
$xoopsOption['template_main'] = 'sample/admin/xfmod_index.html';
// get current information
project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if (!$perm->isDocEditor()) {
   redirect_header($GLOBALS["HTTP_REFERER"],2,_XF_G_PERMISSIONDENIED."<br />"._XF_SC_YOUARENOTCODEMANAGER);
   exit;
}

function main_page($project, $group_id) {
	global $xoopsTpl;

	$xoopsTpl->assign("sampleman_header",sampleman_header($project, $group_id, _XF_SC_CODEMANAGERADMIN,'admin'));
	$content = "";
	// Allow to enable/disable articles if a foundry
	if ( $project->isFoundry() )
	{
		global $xoopsDB;
		$content .= _XF_SC_COMM_ARTICLES.":  <b>";
		$sql = "SELECT sample_group"
		. " FROM " . $xoopsDB->prefix("xf_sample_groups")
		. " WHERE group_id='" . $group_id . "'"
		. " AND groupname='"._XF_SC_ARTICLES_KEY."'";
		$result = $xoopsDB->query($sql);
		if ( ! $result || $xoopsDB->getRowsNum($result) < 1 )
		{
			$content .= _XF_SC_ARTICLESDISABLED."</b> - <a href=\"".$_SERVER['PHP_SELF']."?group_id=".$group_id."&articles=y\">"._XF_SC_ARTICLESCLICKTOENABLE."</a><hr>\n";
		}
		else
		{
			$content .= _XF_SC_ARTICLESENABLED." (";
			$row = $xoopsDB->fetchArray($result);
			$sample_group_id = $row['sample_group'];
			$sql = "SELECT stateid"
			. " FROM " . $xoopsDB->prefix("xf_sample_states")
			. " WHERE name='active'";
			$result = $xoopsDB->query($sql);
			$row = $xoopsDB->fetchArray($result);
			$id = $row['stateid'];
			$sql = "SELECT sampleid"
			. " FROM " . $xoopsDB->prefix("xf_sample_data")
			. " WHERE sample_group='" . $sample_group_id . "'"
			. " AND stateid='".$id."'";
			$result = $xoopsDB->query($sql);
			$num_articles = $xoopsDB->getRowsNum($result);
			$content .= intval($num_articles) . " "._XF_SC_ARTICLESACTIVE.")</b> - <a href=\"".$_SERVER['PHP_SELF']."?group_id=".$group_id."&articles=n\">"._XF_SC_ARTICLESCLICKTODISABLE."</a>";
			if ( $num_articles > 0 )
			{
				$content .= "  ("._XF_SC_ARTICLESWILLBEUNAVAIL.")";
			}
			$content .= "<hr>\n";
		}
	}
		$content = "<h3>"._XF_SC_ACTIVECODE.":</h3>";
		$content .= display_samples('1',$group_id);
		$content .= "<br><h3>"._XF_SC_PENDINGCODE.":</h3>";
		$content .= display_samples('3',$group_id);
		$content .= "<br><h3>"._XF_SC_HIDDENCODE.":</h3>";
		$content .= display_samples('4',$group_id);
		$content .= "<br><h3>"._XF_SC_PRIVATECODE.":</h3>";
		$content .= display_samples('5',$group_id);
		$content .= "<br><h3>"._XF_SC_DELETEDCODE.":</h3>";
		$content .= display_samples('2',$group_id);
	return $content;

} //end function main_page($group_id);
	$content = "";
//begin to seek out what this page has been called to do.
	if (strstr($mode,"sampleedit")) {
		include (XOOPS_ROOT_PATH."/header.php");
	        $xoopsTpl->assign("sampleman_header",sampleman_header($group, $group_id, _XF_SC_EDITCODE,'admin'));

		$query = "SELECT * "
		        ."FROM ".$xoopsDB->prefix("xf_sample_data")." dd, ".$xoopsDB->prefix("xf_sample_groups")." dg "
						."WHERE sampleid='$sampleid' "
						."AND dg.sample_group=dd.sample_group "
						."AND dg.group_id='$group_id'";

		$result = $xoopsDB->query($query);
		$row = $xoopsDB->fetchArray($result);

		$content .= '
			<form name="editdata" action="index.php?mode=sampledoedit&group_id='.$group_id.'" method="POST">
			<table border="0" width="75%">
			<tr>
			  <td><b>'._XF_SC_CODETITLE.':</b></td>
			  <td><input type="text" name="title" size="40" maxlength="255" value="'.$ts->makeTboxData4Edit($row['title']).'"></td>
			</tr>
			<tr>
	 		  <td><b>'._XF_SC_DESCRIPTION.':</b></td>
			  <td><input type="text" name="description" size="40" maxlength="255" value="'.$ts->makeTboxData4Edit($row['description']).'"></td>
			</tr>
			<tr>';
		if (strstr($row['data'],"://")) {
			$content .= '
			  <td><b>'._XF_SC_FILENAME.':</b></td>
			  <td><input type="text" name="data" value="'.$ts->makeTareaData4Edit($row['data']).'" size="40" maxlength="255"></td>';
		}
		else {
			$content .= '
			  <td><b>'._XF_SC_FILENAME.':</b></td>
			  <td>'.basename($ts->makeTareaData4Edit($row['data'])).'</td>';
		}
			$content .= '
			</tr>
			<tr>
			  <td><b>'._XF_SC_GROUPCODEBELONGSIN.':</b></td>
			  <td>';
		$content .= display_groups_option($group_id,$row['sample_group']);
		$content .= '
			  </td>
			</tr>
			<tr>
		          <td><b>'._XF_SC_CODESTATE.':</b></td>
		          <td>';
		$res_states = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_sample_states"));
		$content .= html_build_select_box ($res_states, 'stateid', $row['stateid'], false);
		$content .= '
 			  </td>
			</tr>
		  </table>
		  <input type="hidden" name="sampleid" value="'.$row['sampleid'].'">
		  <input type="submit" value="'._XF_G_SUBMIT.'">
		  </form>';

		$content .= display_sample_feedback ($group_id, $row['sampleid'], 10);
		$xoopsTpl->assign("content",$content);
		include (XOOPS_ROOT_PATH."/footer.php");

	}
	elseif (strstr($mode,"groupdelete")) {
		include (XOOPS_ROOT_PATH."/header.php");
	        $xoopsTpl->assign("sampleman_header",sampleman_header($group, $group_id, _XF_SC_EDITCODE,'admin'));

		$query = "SELECT sampleid "
			      ."FROM ".$xoopsDB->prefix("xf_sample_data")." "
			      ."WHERE sample_group='$sample_group'";

		$result = $xoopsDB->query($query);

		if ($xoopsDB->getRowsNum($result) < 1) {
			$query = "DELETE FROM ".$xoopsDB->prefix("xf_sample_groups")." "
			        ."WHERE sample_group='$sample_group' "
							."AND group_id='$group_id'";

			$xoopsDB->queryF($query);

			$pagehead = _XF_SC_GROUPDELETE;
			$xoopsTpl->assign("content","<p><b>"._XF_SC_GROUPDELETED.". ("._XF_SC_GROUPID." : ".$sample_group.")</b>");
		}
		else {
			$pagehead = _XF_SC_GROUPDELETEFAILED;
			$xoopsTpl->assign("content",_XF_SC_CANNOTDELETEGROUP);
		}

		include (XOOPS_ROOT_PATH."/footer.php");

	}
	elseif (strstr($mode,"groupedit")) {
		include (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("sampleman_header",sampleman_header($group, $group_id, _XF_SC_GROUPEDIT,'admin'));

			$query = "SELECT * "
				      ."FROM ".$xoopsDB->prefix("xf_sample_groups")." "
				      ."WHERE sample_group='$sample_group' "
							."AND group_id='$group_id'";

			$result = $xoopsDB->query($query);
			$row = $xoopsDB->fetchArray($result);
			$content = '
			<br />
			<b> '._XF_SC_EDITAGROUP.':</b>

			<form name="editgroup" action="index.php?mode=groupdoedit&group_id='.$group_id.'" method="POST">
			<table>
			<tr><th>'._XF_SC_NAME.':</th>
			<td><input type="text" name="groupname" value="'.$ts->makeTboxData4Edit($row['groupname']).'"></td></tr>
			<input type="hidden" name="sample_group" value="'.$row['sample_group'].'">
			<tr><td> <input type="submit" value="'._XF_G_SUBMIT.'"></td></tr></table>
			</form>';
			$xoopsTpl->assign("content",$content);
			include (XOOPS_ROOT_PATH."/footer.php");

	}elseif (strstr($mode,"groupdoedit")) {
		include (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("sampleman_header",sampleman_header($group, $group_id, _XF_SC_GROUPEDIT,'admin'));

		$query = "UPDATE ".$xoopsDB->prefix("xf_sample_groups")." SET "
		        ."groupname='".$ts->makeTboxData4Save($groupname)."' "
						."WHERE sample_group='$sample_group' "
						."AND group_id='$group_id'";

		$xoopsDB->queryF($query);
		$xoopsTpl->assign("feedback",_XF_SC_CODEGROUPEDITED);
		$xoopsTpl->assign("content",main_page($group, $group_id));
		include (XOOPS_ROOT_PATH."/footer.php");
	}elseif (strstr($mode,"sampledoedit")) {
		//Page security - checks someone isnt updating a sample
		//that isnt theirs.
		include (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("sampleman_header",sampleman_header($group, $group_id, _XF_SC_GROUPEDIT,'admin'));

		$query = "SELECT dd.sampleid, dd.data AS data "
		        ."FROM ".$xoopsDB->prefix("xf_sample_data")." dd, ".$xoopsDB->prefix("xf_sample_groups")." dg "
			      ."WHERE dd.sample_group=dg.sample_group "
			      ."AND dg.group_id='$group_id' "
			      ."AND dd.sampleid='$sampleid'";

		$result = $xoopsDB->query($query);

		if ($xoopsDB->getRowsNum($result) == 1) {
			$row = $xoopsDB->fetchArray($result);
			// data in DB stored in htmlspecialchars()-encoded form
			if($stateid==2){ //deleting a file
				if(!strstr($row['data'],"://")){
					if(!$frs) $frs = new FRS($group_id);
					if(!$frs->rmfile($group->getUnixName()."/sample/".$row['data'])){
						$xoopsTpl->assign("feedback",$frs->getErrorMessage());
						$xoopsTpl->assign("content",main_page($group, $group_id));
						include_once (XOOPS_ROOT_PATH."/footer.php");
						exit();
					}
				}
				$query = "UPDATE ".$xoopsDB->prefix("xf_sample_data")." SET "
						."title='".$ts->makeTboxData4Save($title)."',";
				$query.="data='".$xoopsUser->getVar('name')." - ".$xoopsUser->getVar('uname')."',";
				$query.="updatedate='".time()."',"
								."sample_group='".$sample_group."',"
								."stateid='".$stateid."',"
								."description='".$ts->makeTboxData4Save($description)."' "
								."WHERE sampleid='$sampleid'";
			}else{
				if(!$frs) $frs = new FRS($group_id);
				if($stateid==1){
					$frs->chmodpath($xoopsForge['ftp_path']."/".$group->getUnixName()."/sample/".$row['data'],0664);
				}else{
					$frs->chmodpath($xoopsForge['ftp_path']."/".$group->getUnixName()."/sample/".$row['data'],0660);
				}
				$query = "UPDATE ".$xoopsDB->prefix("xf_sample_data")." SET "
						."title='".$ts->makeTboxData4Save($title)."',";
				if($data){
					$query.="data='".$ts->makeTareaData4Save($data)."',";
				}else if($xoopsForge['ftp_path'] != substr($row['data'],0,strlen($xoopsForge['ftp_path']))){
					$query.="data='".$ts->makeTareaData4Save($row['data'])."',";
				}
				$query.="updatedate='".time()."',"
								."sample_group='".$sample_group."',"
								."stateid='".$stateid."',"
								."description='".$ts->makeTboxData4Save($description)."' "
								."WHERE sampleid='$sampleid'";
			}
			$res = $xoopsDB->queryF($query);
			if (!$res) {
				$xoopsTpl->assign("feedback",_XF_SC_COULDNOTUPDATECODE.'<br>');
			}else {
				$xoopsTpl->assign("feedback", sprintf (_XF_SC_CODETITLEUPDATED, $ts->makeTboxData4Show($title)));
			}
			$xoopsTpl->assign("content",main_page($group, $group_id));

		}else{
			$xoopsTpl->assign("sampleman_header",sampleman_header($group, $group_id, _XF_DOC_COULDNOTUPDATECODE,'admin'));
			$xoopsTpl->assign("content","Unable to update - Sample code does not exist, or sample's group is not the same as that to which your account belongs.");
		}
		include (XOOPS_ROOT_PATH."/footer.php");
	}elseif (strstr($mode,"groupadd")) {
		include (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("sampleman_header",sampleman_header($group, $group_id, _XF_SC_GROUPEDIT,'admin'));

		$query = "INSERT INTO ".$xoopsDB->prefix("xf_sample_groups")." (groupname,group_id) "
		        ."values ('"
						."".$ts->makeTboxData4Save($groupname)."',"
						."'$group_id')";

		$xoopsDB->queryF($query);
		$xoopsTpl->assign("feedback",sprintf(_XF_SC_GROUPGROUPNAMEADDED, $ts->makeTboxData4Show($groupname)));
		$xoopsTpl->assign("content",main_page($group, $group_id));
		include (XOOPS_ROOT_PATH."/footer.php");
	} elseif (strstr($mode,"editgroups")) {
		include (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("sampleman_header",sampleman_header($group, $group_id, _XF_SC_GROUPEDIT,'admin'));

		$content = '
			<p><b> '._XF_SC_ADDAGROUP.':</b>
			<form name="addgroup" action="index.php?mode=groupadd&group_id='.$group_id.'" method="POST">
			<table>
			<tr><td><b>'._XF_SC_NEWGROUPNAME.':</b></td>  <td><input type="text" name="groupname"></td><td><input type="submit" value="'._XF_G_ADD.'"></td></tr></table>
			<p>'._XF_SC_GROUPNAMEWILLBEUSEDASTITLE.'</p>
			</form>';
		$content .= display_groups($group_id);
		$xoopsTpl->assign("content",$content);
		include (XOOPS_ROOT_PATH."/footer.php");

	} elseif (strstr($mode,"editsamples")) {
		include (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("sampleman_header",sampleman_header($group, $group_id, _XF_SC_EDITCODELIST,'admin'));
		$xoopsTpl->assign("content",main_page($group, $group_id));
		include (XOOPS_ROOT_PATH."/footer.php");

	} else if ( $articles != "" ) {
		global $xoopsDB;
		if ( $articles == "y" )	{
			// Check for a refresh - don't recreate if a refresh
			$sql = "SELECT sample_group"
			. " FROM " . $xoopsDB->prefix("xf_sample_groups")
			. " WHERE groupname='"._XF_SC_ARTICLES_KEY."'"
			. " AND group_id='".$group_id."'";
			$result = $xoopsDB->query($sql);
			if ( 1 > $xoopsDB->getRowsNum($result) ) {
				$sql = "INSERT INTO "
				. $xoopsDB->prefix("xf_sample_groups")
				. " (groupname, group_id) VALUES"
				. " ('"._XF_SC_ARTICLES_KEY."','".$group_id."')";
				$result = $xoopsDB->queryF($sql);
			}
		} else if ( $articles == "n" ) {
			$sql = "DELETE FROM "
			. $xoopsDB->prefix("xf_sample_groups")
			. " WHERE groupname='"._XF_SC_ARTICLES_KEY."'"
			. " AND group_id='".$group_id."'";
			$result = $xoopsDB->queryF($sql);
		}
		include (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("content",main_page($group, $group_id));
		include (XOOPS_ROOT_PATH."/footer.php");
	} else {
		include (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("content",main_page($group, $group_id));
		include (XOOPS_ROOT_PATH."/footer.php");
	} //end else

?>