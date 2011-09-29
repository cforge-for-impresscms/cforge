<?php
/**
  *
  * SourceForge Documentaion Manager
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: newarticle.php,v 1.7 2003/12/15 18:09:15 devsupaul Exp $
  *
  */


/*
	by Quentin Cregan, SourceForge 06/2000
*/
include_once("../../../mainfile.php");

$langfile="docman.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/docman/doc_utils.php");
$xoopsOption['template_main'] = 'community/xfmod_newarticle.html';
// get current information
project_check_access ($group_id,0);



if($group_id) {
// get current information
  $group =& group_get_object($group_id);
  $perm  =& $group->getPermission( $xoopsUser );
  
  	//group is private
	if (!$group->isPublic()) {
	  //if it's a private group, you must be a member of that group
	  if (!$group->isMemberOfGroup($xoopsUser) && !$perm->isSuperUser())
		{
		  redirect_header(XOOPS_URL."/",4,_XF_COMM_COMMMARKEDASPRIVATE);
		  exit;
		}
	}

  
  // Make sure articles are enabled
  global $xoopsDB;
  $sql = "SELECT doc_group"
     . " FROM " . $xoopsDB->prefix("xf_doc_groups")
     . " WHERE group_id='".$group_id."' AND groupname='"._XF_DOC_ARTICLES_KEY."'";
  $result = $xoopsDB->query($sql);
  if ( 1 > $xoopsDB->getRowsNum($result) )
    {
      $prjtype="community";
      redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />".sprintf(_XF_DOC_ARTICLESNOTENABLED, $prjtype).".");
      exit;
    }
  $row = $xoopsDB->fetchArray($result);
  $doc_group = $row['doc_group'];
  
  if ($op == "add"){
    
    if (!$doc_group) {
      //cannot add a doc unless an appropriate group is provided
      redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />"._XF_DOC_NOVALIDDOCGROUPSELECTED);
      exit;
    }
    
    if (!$title || !$description) {
      redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />"._XF_PM_MISSINGREQPARAMETERS);
      exit;
    }
    
    if (!$upload_instead && !$_FILES['file1']) {
      redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />"._XF_PM_MISSINGREQPARAMETERS);
      exit;
    }
    
    if ($xoopsUser) {
      $user_id = $xoopsUser->getVar("uid");
    } else {
      $user_id = 100;
    }
    $tmp_name = $_FILES['file1']['tmp_name'];
    if($error = VirusScan($tmp_name)){
      unlink($tmp_name);
      echo $error;
      //redirect_header($GLOBALS["HTTP_REFERER"],4,"Error<br />".$error);
      exit;
    }
    
    $filename = $_FILES['file1']['name'];
    $file_size = $_FILES['file1']['size'];
    if(!is_dir($xoopsForge['ftp_path']."/".$group->getUnixName())){ mkdir($xoopsForge['ftp_path']."/".$project->getUnixName(),0755);}
    if(!is_dir($xoopsForge['ftp_path']."/".$group->getUnixName()."/docs")){ mkdir($xoopsForge['ftp_path']."/".$project->getUnixName()."/docs",0755);}
    $file_url = $xoopsForge['ftp_path']."/".$group->getUnixName()."/docs/".$filename;
    if(move_uploaded_file($tmp_name,$file_url)){			
      chmod($file_url, 0644);
    }else{
    	echo "Unable to move $tmp_name to $file_url.<br>\n";
      return false;
    }			
    
//    docman_header($group,$group_id,_XF_DOC_NEWSUBMISSION,'admin');
	include (XOOPS_ROOT_PATH."/header.php");
	$xoopsTpl->assign("docman_header",docman_header($group,$group_id,_XF_DOC_NEWSUBMISSION));
    
    $query = "INSERT INTO ".$xoopsDB->prefix("xf_doc_data")." "
       ."(stateid,title,data,createdate,updatedate,created_by,doc_group,description) "
       ."VALUES ('3',"
       // state = 3 == pending
       ."'".$ts->makeTboxData4Save($title)."',"
       ."'".$file_url."',"
       //."'".$ts->makeTareaData4Save($data)."',"
       ."'".time()."',"
       ."'".time()."',"
       ."'".$user_id."',"
       ."'".$doc_group."',"
       ."'".$ts->makeTboxData4Save($description)."')";
    
    $res = $xoopsDB->queryF($query);
    //PROBLEM check the query
    $content = "";
    if (!$res) {
      $content .= "<p><b><font color='red'>"._XF_DOC_ERRORADDINGDOCUMENT.": ".$xoopsDB->error()."</font></b></p>";
    } else {
      $content .= "<p><b>"._XF_DOC_THANKYOUONSUBMISSION."</b>"
	."<p><a href='".XOOPS_URL."/modules/xfmod/docman/index.php?group_id=".$group_id."'>"._XF_G_BACK."</a>";
    }
    
    include (XOOPS_ROOT_PATH."/footer.php");
  }else{
    include (XOOPS_ROOT_PATH."/header.php");
    $xoopsTpl->assign("docman_header",docman_header($group,$group_id,_XF_DOC_ADDDOCUMENTATION));
    $content = "";
    if (get_group_count($group_id) > 0){
      if (!$xoopsUser) {
		$content .= "<p>"._XF_DOC_NOTLOGGEDINNOCREDIT."<p>";
      }
      
      $content .= '
			<p>
			'._XF_DOC_DISCLAIMER.': <a href="#" onclick="window.open(\'../../include/contract.php\',\'contract\', \'width=750, height=350, resizable=yes, scrollbars=yes, toolbar=no, directories=no, location=no, status=no\');">'._XF_DOC_DISCLAIMERLINK.'</a><br>
			<b> '._XF_DOC_DOCUMENTTITLE.': </b> '._XF_DOC_DOCUMENTTITLEDESC.'<br>
			<b> '._XF_DOC_DESCRIPTION.': </b> '._XF_DOC_DESCRIPTIONDESC.'<br>';
      $content .= "\n";
      $content .= "<form name=\"adddata\" action=\"newarticle.php\" method=\"POST\" enctype=\"multipart/form-data\">\n"
				. "<input type=\"hidden\" name=\"group_id\" value=\"".$group_id."\">\n"
				. "<input type=\"hidden\" name=\"op\" value=\"add\">\n"
				. "<table border=\"0\" width=\"75%\"><tr>\n"
				. "<td><b>"._XF_DOC_DOCUMENTTITLE.":</b></td>\n"
				. "<td><input type=\"text\" name=\"title\" size=\"40\" maxlength=\"255\"></td></tr>\n"
				. "<tr><td><b>"._XF_DOC_DESCRIPTION.":</b></td>\n"
				. "<td><input type=\"text\" name=\"description\" size=\"50\" maxlength=\"255\"></td></tr>\n"
				. "<tr><td><b>"._XF_DOC_FILE1.":</b></td>\n"
				. "<td><input type=\"file\" name=\"file1\" size=\"40\"><br><br></td></tr>\n";
      // Force article document group
      $content .= "<input type=\"hidden\" name=\"doc_group\" value=\"".$doc_group."\">";
      $content .= "</table>\n"
				. "<input type=\"submit\" name=\"submit\" value=\""._XF_G_SUBMIT."\">"
				. "</form>\n";
    }else{// end if (project has doc categories)
      $content .= "<p>"._XF_DOC_MUSTDEFINEDOCCATEGORY."<BR />";
      
      $perm =& $group->getPermission( $xoopsUser );
      
      // if an admin, prompt for adding a category
      if ( $perm->isDocEditor() || $perm->isAdmin() ) {
		$content .= "<p>[ <a href='".XOOPS_URL."/modules/xfmod/docman/admin/index.php?mode=editgroups&group_id=".$group_id."'>"._XF_DOC_ADDADOCGROUP."</a> ]";
      }
    }
	$xoopsTpl->assign("content",$content);
    include (XOOPS_ROOT_PATH."/footer.php");
  } // end else.
  
} else {
	
	$xoopsForgeErrorHandler->setError("The community you were looking for was not found.");
	redirect_header($GLOBALS["HTTP_REFERER"],2,"Error<br />No Group");
	exit;
}

?>