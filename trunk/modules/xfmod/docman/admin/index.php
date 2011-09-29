<?php
/**
  *
  * SourceForge Documentaion Manager
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.13 2004/03/24 21:17:17 devsupaul Exp $
  *
  */


/*
        Docmentation Manager
        by Quentin Cregan, SourceForge 06/2000
*/
include_once("../../../../mainfile.php");

$langfile="docman.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/docman/doc_utils.php");
$xoopsOption['template_main'] = 'docman/admin/xfmod_index.html';
// get current information
project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if (!$perm->isDocEditor()) {
   redirect_header($GLOBALS["HTTP_REFERER"],2,_XF_G_PERMISSIONDENIED."<br />"._XF_DOC_YOUARENOTDOCMANAGER);
   exit;
}

function main_page($project, $group_id) {
	global $xoopsTpl;

	$xoopsTpl->assign("docman_header",docman_header($project, $group_id, _XF_DOC_DOCUMENTMANAGERADMIN,'admin'));
	$content = "";
	// Allow to enable/disable articles if a foundry
	if ( $project->isFoundry() )
	{
		global $xoopsDB;
		$content .=  _XF_DOC_COMM_ARTICLES.":  <b>";
		$sql = "SELECT doc_group"
		. " FROM " . $xoopsDB->prefix("xf_doc_groups")
		. " WHERE group_id='" . $group_id . "'"
		. " AND groupname='"._XF_DOC_ARTICLES_KEY."'";
		$result = $xoopsDB->query($sql);
		if ( ! $result || $xoopsDB->getRowsNum($result) < 1 )
		{
			$content .=  _XF_DOC_ARTICLESDISABLED."</b> - <a href=\"".$_SERVER['PHP_SELF']."?group_id=".$group_id."&articles=y\">"._XF_DOC_ARTICLESCLICKTOENABLE."</a><hr>\n";
		}
		else
		{
			$content .=  _XF_DOC_ARTICLESENABLED." (";
			$row = $xoopsDB->fetchArray($result);
			$doc_group_id = $row['doc_group'];
			$sql = "SELECT stateid"
			. " FROM " . $xoopsDB->prefix("xf_doc_states")
			. " WHERE name='active'";
			$result = $xoopsDB->query($sql);
			$row = $xoopsDB->fetchArray($result);
			$id = $row['stateid'];
			$sql = "SELECT docid"
			. " FROM " . $xoopsDB->prefix("xf_doc_data")
			. " WHERE doc_group='" . $doc_group_id . "'"
			. " AND stateid='".$id."'";
			$result = $xoopsDB->query($sql);
			$num_articles = $xoopsDB->getRowsNum($result);
			$content .=  intval($num_articles) . " "._XF_DOC_ARTICLESACTIVE.")</b> - <a href=\"".$_SERVER['PHP_SELF']."?group_id=".$group_id."&articles=n\">"._XF_DOC_ARTICLESCLICKTODISABLE."</a>";
			if ( $num_articles > 0 )
			{
				$content .= "  ("._XF_DOC_ARTICLESWILLBEUNAVAIL.")";
			}
			$content .= "<hr>\n";
		}
	}
	$content .= "<h3>"._XF_DOC_ACTIVEDOCUMENTS.":</h3>";
	$content .= display_docs('1',$group_id);
	$content .= "<br><h3>"._XF_DOC_PENDINGDOCUMENTS.":</h3>";
	$content .= display_docs('3',$group_id);
	$content .= "<br><h3>"._XF_DOC_HIDDENDOCUMENTS.":</h3>";
	$content .= display_docs('4',$group_id);
	$content .= "<br><h3>"._XF_DOC_PRIVATEDOCUMENTS.":</h3>";
	$content .= display_docs('5',$group_id);
	$content .= "<br><h3>"._XF_DOC_DELETEDDOCUMENTS.":</h3>";
	$content .= display_docs('2',$group_id);
    return $content;

} //end function main_page($group_id);
	$content = "";
//begin to seek out what this page has been called to do.
	if (strstr($mode,"docedit")) {

		$query = "SELECT * "
		        ."FROM ".$xoopsDB->prefix("xf_doc_data")." dd, ".$xoopsDB->prefix("xf_doc_groups")." dg "
						."WHERE docid='$docid' "
						."AND dg.doc_group=dd.doc_group "
						."AND dg.group_id='$group_id'";

		$result = $xoopsDB->query($query);
		$row = $xoopsDB->fetchArray($result);

		include_once (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("docman_header",docman_header($group, $group_id, _XF_DOC_EDITDOC,'admin'));

		$content .= '

			<form name="editdata" action="index.php?mode=docdoedit&group_id='.$group_id.'" method="POST">
			<table border="0" width="75%">
			<tr>
			  <td><b>'._XF_DOC_DOCUMENTTITLE.':</b></td>
			  <td><input type="text" name="doc_title" size="40" maxlength="255" value="'.$ts->htmlSpecialChars($row['title']).'"></td>
			</tr>
			<tr>
				<td><b>'._XF_DOC_DESCRIPTION.':</b></td>
				<td><input type="text" name="doc_description" size="40" maxlength="255" value="'.$ts->makeTboxData4Edit($row['description']).'"></td>
			</tr>
			<tr>';
		if(strstr($row['data'],"://")){
			$content .= '<td><b>'._XF_DOC_FILENAME.':</b></td>
			  <td><input type="text" name="data" value="'.$ts->makeTareaData4Edit($row['data']).'" size="40" maxlength="255"></td>';
		}else{
			$content .= '<td><b>'._XF_DOC_FILENAME.':</b></td>
			  <td>'.basename($ts->makeTareaData4Edit($row['data'])).'</td>';
		}
			$content .= '</tr>
			<tr>
			  <td><b>'._XF_DOC_GROUPDOCBELONGSIN.':</b></td>
        <td>';
		$content .= display_groups_option($group_id,$row['doc_group']);
		$content .= '
				</td>
			</tr>
			<tr>
		    <td><b>'._XF_DOC_DOCSTATE.':</b></td>
		    <td>';
		$res_states = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_doc_states"));
		$content .= html_build_select_box ($res_states, 'stateid', $row['stateid'], false);
		$content .= '
 			  </td>
			</tr>
		  </table>
		  <input type="hidden" name="docid" value="'.$row['docid'].'">
		  <input type="submit" value="'._XF_G_SUBMIT.'">
		  </form>';

		$content .= display_doc_feedback ($group_id, $row['docid'], 10);
		$xoopsTpl->assign("content",$content);
		include (XOOPS_ROOT_PATH."/footer.php");

	} elseif (strstr($mode,"groupdelete")) {
		include_once (XOOPS_ROOT_PATH."/header.php");
		$query = "SELECT docid"
			      ." FROM ".$xoopsDB->prefix("xf_doc_data")
			      ." WHERE doc_group='$doc_group'"
				  ." AND stateid!=2";

		$result = $xoopsDB->query($query);

		if ($xoopsDB->getRowsNum($result) < 1) {
			$query = "DELETE FROM ".$xoopsDB->prefix("xf_doc_groups")
						." WHERE doc_group='$doc_group'"
						." AND group_id='$group_id'";
			$xoopsDB->queryF($query);

			$query = "DELETE FROM ".$xoopsDB->prefix("xf_doc_data")
						." WHERE doc_group='$doc_group'"
						." AND group_id='$group_id'";
			$xoopsDB->queryF($query);

			$pagehead = _XF_DOC_GROUPDELETE;
			$xoopsTpl->assign("content","<p><b>"._XF_DOC_GROUPDELETED.". ("._XF_DOC_GROUPID." : ".$doc_group.")</b>");
		} else {
			$pagehead = _XF_DOC_GROUPDELETEFAILED;
			$xoopsTpl->assign("content",_XF_DOC_CANNOTDELETEGROUP);
		}
		$xoopsTpl->assign("docman_header",docman_header($group, $group_id, $pagehead,'admin'));
		include (XOOPS_ROOT_PATH."/footer.php");

	} elseif (strstr($mode,"groupedit")) {
	  include_once (XOOPS_ROOT_PATH."/header.php");
	  $xoopsTpl->assign("docman_header",docman_header($group, $group_id, _XF_DOC_GROUPEDIT,'admin'));
 			$query = "SELECT * "
				      ."FROM ".$xoopsDB->prefix("xf_doc_groups")." "
				      ."WHERE doc_group='$doc_group' "
							."AND group_id='$group_id'";

			$result = $xoopsDB->query($query);
			$row = $xoopsDB->fetchArray($result);
			$content = '
			<br />
			<b> '._XF_DOC_EDITAGROUP.':</b>

			<form name="editgroup" action="index.php?mode=groupdoedit&group_id='.$group_id.'" method="POST">
			<table>
			<tr><th>'._XF_DOC_NAME.':</th>
			<td><input type="text" name="groupname" value="'.$ts->makeTboxData4Edit($row['groupname']).'"></td></tr>
			<input type="hidden" name="doc_group" value="'.$row['doc_group'].'">
			<tr><td> <input type="submit" value="'._XF_G_SUBMIT.'"></td></tr></table>
			</form>
			';
			$xoopsTpl->assign("content",$content);
			include (XOOPS_ROOT_PATH."/footer.php");

	}elseif (strstr($mode,"groupdoedit")) {
		include_once (XOOPS_ROOT_PATH."/header.php");

		$query = "UPDATE ".$xoopsDB->prefix("xf_doc_groups")." SET "
		        ."groupname='".$ts->makeTboxData4Save($groupname)."' "
						."WHERE doc_group='$doc_group' "
						."AND group_id='$group_id'";

		$xoopsDB->queryF($query);
		$xoopsTpl->assign("feedback",_XF_DOC_DOCUMENTGROUPEDITED);
		$xoopsTpl->assign("content",main_page($group, $group_id));
		include_once (XOOPS_ROOT_PATH."/footer.php");
	} elseif (strstr($mode,"docdoedit")) {
		//Page security - checks someone isnt updating a doc
		//that isnt theirs.
		include_once (XOOPS_ROOT_PATH."/header.php");
		$query = "SELECT dd.docid, dd.data AS data "
		        ."FROM ".$xoopsDB->prefix("xf_doc_data")." dd, ".$xoopsDB->prefix("xf_doc_groups")." dg "
			      ."WHERE dd.doc_group=dg.doc_group "
			      ."AND dg.group_id='$group_id' "
			      ."AND dd.docid='$docid'";

		$result = $xoopsDB->query($query);

		if ($xoopsDB->getRowsNum($result) == 1) {
			$row = $xoopsDB->fetchArray($result);
			// data in DB stored in htmlspecialchars()-encoded form
			if($stateid==2){ //deleting a file
				if(!strstr($row['data'],"://")){
					if(!$frs) $frs = new FRS($group_id);
					if(!$frs->rmfile($group->getUnixName()."/docs/".$row['data'])){
						//$xoopsTpl->assign("feedback",$frs->getErrorMessage());
						//$xoopsTpl->assign("content",main_page($group, $group_id));
						//include_once (XOOPS_ROOT_PATH."/footer.php");
						//exit();
					}
				}
				$query = "UPDATE ".$xoopsDB->prefix("xf_doc_data")." SET "
						."title='".$ts->makeTboxData4Save($doc_title)."',";
				$query.="data='".$xoopsUser->getVar('name')." - ".$xoopsUser->getVar('uname')."',";
				$query.="updatedate='".time()."',"
								."doc_group='".$doc_group."',"
								."stateid='".$stateid."',"
								."description='".$ts->makeTboxData4Save($doc_description)."' "
								."WHERE docid='$docid'";
			}else{
				if(!$frs) $frs = new FRS($group_id);
				if($stateid==1){
					$frs->chmodpath($xoopsForge['ftp_path']."/".$group->getUnixName()."/docs/".$row['data'],0664);
				}else{
					$frs->chmodpath($xoopsForge['ftp_path']."/".$group->getUnixName()."/docs/".$row['data'],0660);
				}
				$query = "UPDATE ".$xoopsDB->prefix("xf_doc_data")." SET "
						."title='".$ts->addSlashes($doc_title)."',";
				if($data){
					$query.="data='".$ts->addSlashes($data)."',";
				}else if($group->getUnixName()."/docs/" != substr($row['data'],0,strlen($group->getUnixName()."/docs/"))){
					$query.="data='".$ts->addSlashes($row['data'])."',";
				}
				$query.="updatedate='".time()."',"
								."doc_group='".$doc_group."',"
								."stateid='".$stateid."',"
								."description='".$ts->addSlashes($doc_description)."' "
								."WHERE docid='$docid'";
			}
			$res = $xoopsDB->queryF($query);
			if (!$res) {
				//$xoopsTpl->assign("feedback",_XF_DOC_COULDNOTUPDATEDOCUMENT.'<br>');
				$feedback .= _XF_DOC_COULDNOTUPDATEDOCUMENT.'<br>';
			} else {
				//$xoopsTpl->assign("feedback",sprintf (_XF_DOC_DOCUMENTTITLEUPDATED, $ts->makeTboxData4Show($title)));
				$feedback .= sprintf (_XF_DOC_DOCUMENTTITLEUPDATED, $ts->addSlashes($title));
			}
			$xoopsTpl->assign("content",main_page($group, $group_id));
			$xoopsTpl->assign("feedback",$feedback);

		} else {
			$xoopsTpl->assign("docman_header",docman_header($group, $group_id, _XF_DOC_COULDNOTUPDATEDOCUMENT,'admin'));
			$xoopsTpl->assign("content","Unable to update - Document does not exist, or document's group not the same as that to which your account belongs.");
		}
		include (XOOPS_ROOT_PATH."/footer.php");
	} elseif (strstr($mode,"groupadd")) {
		include (XOOPS_ROOT_PATH."/header.php");
		$query = "INSERT INTO ".$xoopsDB->prefix("xf_doc_groups")." (groupname,group_id) "
		        ."values ('"
						."".$ts->makeTboxData4Save($groupname)."',"
						."'$group_id')";

		$xoopsDB->queryF($query);
		$xoopsTpl->assign("feedback",sprintf(_XF_DOC_GROUPGROUPNAMEADDED, $ts->makeTboxData4Show($groupname)));
		$xoopsTpl->assign("content",main_page($group, $group_id));
		include (XOOPS_ROOT_PATH."/footer.php");
	} elseif (strstr($mode,"editgroups")) {
		include_once (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("docman_header",docman_header($group, $group_id, _XF_DOC_GROUPEDIT,'admin'));
		$content = '
			<p><b> '._XF_DOC_ADDAGROUP.':</b>
			<form name="addgroup" action="index.php?mode=groupadd&group_id='.$group_id.'" method="POST">
			<table>
			<tr><td><b>'._XF_DOC_NEWGROUPNAME.':</b></td>  <td><input type="text" name="groupname"></td><td><input type="submit" value="'._XF_G_ADD.'"></td></tr></table>
			<p>'._XF_DOC_GROUPNAMEWILLBEUSEDASTITLE.'</p>
			</form>
		';
		$content .= display_groups($group_id);
		$xoopsTpl->assign("content",$content);
		include (XOOPS_ROOT_PATH."/footer.php");

	} elseif (strstr($mode,"editdocs")) {
		include_once (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("docman_header",docman_header($group, $group_id, _XF_DOC_EDITDOCUMENTSLIST,'admin'));
		$xoopsTpl->assign("content",main_page($group, $group_id));
		include (XOOPS_ROOT_PATH."/footer.php");

	} else if ( $articles != "" ) {
		global $xoopsDB;
		if ( $articles == "y" )
		{
			// Check for a refresh - don't recreate if a refresh
			$sql = "SELECT doc_group"
			. " FROM " . $xoopsDB->prefix("xf_doc_groups")
			. " WHERE groupname='"._XF_DOC_ARTICLES_KEY."'"
			. " AND group_id='".$group_id."'";
			$result = $xoopsDB->query($sql);
			if ( 1 > $xoopsDB->getRowsNum($result) )
			{
				$sql = "INSERT INTO "
				. $xoopsDB->prefix("xf_doc_groups")
				. " (groupname, group_id) VALUES"
				. " ('"._XF_DOC_ARTICLES_KEY."','".$group_id."')";
				$result = $xoopsDB->queryF($sql);
			}
		} else if ( $articles == "n" ) {
			$sql = "DELETE FROM "
			. $xoopsDB->prefix("xf_doc_groups")
			. " WHERE groupname='"._XF_DOC_ARTICLES_KEY."'"
			. " AND group_id='".$group_id."'";
			$result = $xoopsDB->queryF($sql);
		}
		include (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("content",main_page($group, $group_id));
		include (XOOPS_ROOT_PATH."/footer.php");
	} else {
		include_once (XOOPS_ROOT_PATH."/header.php");
		$xoopsTpl->assign("content",main_page($group, $group_id));
		include_once (XOOPS_ROOT_PATH."/footer.php");
	} //end else

?>