<?php
include_once("../../../mainfile.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");

$dl = util_http_track_vars('dl');
$sampleid = util_http_track_vars('sampleid');
$docid = util_http_track_vars('docid');

if ($dl && $dl > 0){
	$xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_frs_dlstats_file_agg")." SET downloads=downloads+1 WHERE file_id=".$dl);
	if($xoopsDB->getAffectedRows()==0){
		$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_frs_dlstats_file_agg")." (file_id, downloads) VALUES ($dl, 1)");
	}
	if($xoopsUser){
		$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_frs_dlnames")." VALUES ($dl, ".$xoopsUser->getVar("uid").", ".time().")");
	}
	$query = "SELECT file_url FROM ".$xoopsDB->prefix("xf_frs_file")." WHERE file_id=".$dl;
	$result = $xoopsDB->query($query);
	$url = unofficial_getDBResult($result,0,'file_url');
	if(strstr($url,"://")){
		redirect_header($url);
	}else{
		$query = "SELECT f.file_url, r.name as rname, p.name as pname"
					." FROM ".$xoopsDB->prefix("xf_frs_file")." AS f"
					." , ".$xoopsDB->prefix("xf_frs_release")." AS r"
					." , ".$xoopsDB->prefix("xf_frs_package")." as p"
					." WHERE f.file_id=".$dl
					." AND f.release_id=r.release_id"
					." AND r.package_id=p.package_id";
		$result = $xoopsDB->query($query);
		if ($result && $xoopsDB->getRowsNum($result) > 0){
			if(!$xoopsForge['ftp_server'] || $xoopsForge['ftp_server'] == 'localhost' || $xoopsForge['ftp_server'] == '127.0.0.1'){
				$url = "/".$xoopsForge['ftp_path']								//file system root
							."/".$project->getUnixName()						//project short name
							."/".unofficial_getDBResult($result,0,'pname')		//package name
							."/".unofficial_getDBResult($result,0,'rname')		//release name
							."/".unofficial_getDBResult($result,0,'file_url');	//file name
				Header("Content-Length: ".filesize($url));
				Header("Content-Type: application/x-download");
				Header("Content-Disposition: attachment; filename=".unofficial_getDBResult($result,0,'file_url'));
				readfile($url);
				exit();
			}else{
				if($_GET['private']){
					if(!$xoopsForge['ftp_internal_server']) $xoopsForge['ftp_internal_server'] = $xoopsForge['ftp_server'];
					$url = "ftp://".$xoopsForge['ftp_user']
								.":".$xoopsForge['ftp_password']
								."@".$xoopsForge['ftp_internal_server']
								."/".$xoopsForge['ftp_path']
								."/".$project->getUnixName()						//project short name
								."/".unofficial_getDBResult($result,0,'pname')		//package name
								."/".unofficial_getDBResult($result,0,'rname')		//release name
								."/".unofficial_getDBResult($result,0,'file_url');	//file name
					Header("Content-Type: application/x-download");
					Header("Content-Disposition: attachment; filename=".unofficial_getDBResult($result,0,'file_url'));
					readfile($url);
				}else{
					$url = "http://".$xoopsForge['ftp_server']
								."/".$xoopsForge['ftp_prefix']
								."/".$project->getUnixName()						//project short name
								."/".rawurlencode(unofficial_getDBResult($result,0,'pname'))		//package name
								."/".rawurlencode(unofficial_getDBResult($result,0,'rname'))		//release name
								."/".rawurlencode(unofficial_getDBResult($result,0,'file_url'));	//file name
					redirect_header($url);
				}
				exit();
			}
		}
	}
}else if ($sampleid && $sampleid > 0){
	$xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_sample_dl_stats")." SET downloads=downloads+1 WHERE sampleid=".$sampleid);
	if(0==$xoopsDB->getAffectedRows()){
		$res =$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_sample_dl_stats")." VALUES (0, ".$sampleid.", 1)");
	}
	$query = "SELECT data FROM ".$xoopsDB->prefix("xf_sample_data")." WHERE sampleid=".$sampleid." AND ( stateid='1'";// stateid = 1 == active
	if ($xoopsUser && $perm->isMember('user_id', $xoopsUser->getVar('uid'))) {
		$query .= " OR stateid='5' ";
	} //state 5 == 'private'
	$query .= ")";

	$result = $xoopsDB->query($query);
	if ($result && $xoopsDB->getRowsNum($result) > 0){
		$url = unofficial_getDBResult($result,0,'data');
		if(strstr($url,"://")){
			redirect_header($url);
		}else{
			if(!$xoopsForge['ftp_server'] || $xoopsForge['ftp_server'] == 'localhost' || $xoopsForge['ftp_server'] == '127.0.0.1'){
				$url = "/".$xoopsForge['ftp_path']
						."/".$project->getUnixName()
						."/sample"
						."/".unofficial_getDBResult($result,0,'data');
				Header("Content-Length: ".filesize($url));
				Header("Content-Type: application/x-download");
				Header("Content-Disposition: attachment; filename=".unofficial_getDBResult($result,0,'data'));
				readfile($url);
				exit();
			}else{
				if($_GET['private']){
					$url = "ftp://".$xoopsForge['ftp_user']
								.":".$xoopsForge['ftp_password']
								."@".$xoopsForge['ftp_internal_server']
								."/".$xoopsForge['ftp_path']
								."/".$project->getUnixName()
								."/sample"
								."/".unofficial_getDBResult($result,0,'data');
					Header("Content-Type: application/x-download");
					Header("Content-Disposition: attachment; filename=".unofficial_getDBResult($result,0,'data'));
					readfile($url);
				}else{
					$url = "http://".$xoopsForge['ftp_server']
								."/".$xoopsForge['ftp_prefix']
								."/".$project->getUnixName()
								."/sample"
								."/".rawurlencode(unofficial_getDBResult($result,0,'data'));
					redirect_header($url);
				}
				exit();
			}
		}
	}
}else if($docid && $docid >0){
	$xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_doc_dl_stats")." SET downloads=downloads+1 WHERE docid=".$docid);
	if(0==$xoopsDB->getAffectedRows()){
		$res =$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_doc_dl_stats")." VALUES (0, ".$sampleid.", 1)");
	}
	$query = "SELECT data FROM ".$xoopsDB->prefix("xf_doc_data")." WHERE docid=".$docid." AND ( stateid='1'";// stateid = 1 == active
	if ($xoopsUser && $perm->isMember('user_id', $xoopsUser->getVar('uid'))) {
		$query .= " OR stateid='5' ";
	} //state 5 == 'private'
	$query .= ")";

	$result = $xoopsDB->query($query);
	if ($result && $xoopsDB->getRowsNum($result) > 0){
		$url = unofficial_getDBResult($result,0,'data');
		if(strstr($url,"://")){
			redirect_header($url);
		}else{
			if(!$xoopsForge['ftp_server'] || $xoopsForge['ftp_server'] == 'localhost' || $xoopsForge['ftp_server'] == '127.0.0.1'){
				$url = "/".$xoopsForge['ftp_path']
						."/".$project->getUnixName()
						."/docs"
						."/".unofficial_getDBResult($result,0,'data');
				Header("Content-Length: ".filesize($url));
				Header("Content-Type: application/x-download");
				Header("Content-Disposition: attachment; filename=".unofficial_getDBResult($result,0,'data'));
				readfile($url);
				exit();
			}else{
				if($_GET['private']){
					$url = "ftp://".$xoopsForge['ftp_user']
								.":".$xoopsForge['ftp_password']
								."@".$xoopsForge['ftp_internal_server']
								."/".$xoopsForge['ftp_path']
								."/".$project->getUnixName()
								."/docs"
								."/".unofficial_getDBResult($result,0,'data');
					Header("Content-Type: application/x-download");
					Header("Content-Disposition: attachment; filename=".unofficial_getDBResult($result,0,'data'));
					readfile($url);
				}else{
					$url = "http://".$xoopsForge['ftp_server']
								."/".$xoopsForge['ftp_prefix']
								."/".$project->getUnixName()
								."/docs"
								."/".rawurlencode(unofficial_getDBResult($result,0,'data'));
					redirect_header($url);
				}
				exit();
			}
		}
	}
}

Header("HTTP/1.0 404 Not Found");
echo "The file you are looking for could not be found.  Please contact the administrator of this project and ask them to release the file again.";
exit();
?><?
/**
  *
  * SourceForge Documentaion Manager
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: download.php,v 1.11 2004/05/17 17:12:24 devsupaul Exp $
  *
  */

/*
        by Quentin Cregan, SourceForge 06/2000
*/

/*  The following has been kept for reference only.
	  $row = $xoopsDB->fetchArray($result);
	  $filename = trim($row['data']);

	  $mime_type = mime_lookup($filename);
	  if ( "text/html" != $mime_type &&
	       "text/plain" != $mime_type )
	    {
	      // Make the sampleument available for download
	      header("Content-Disposition: filename=\"".basename($filename)."\"");
	      header("Content-type: ".$mime_type."; name=\"".basename($filename)."\"");
	      header("Content-Length: ".filesize($filename)."\n");
	      header("Expires: Mon, 04 May 1977 04:23:32 GMT");
	      header("Last-Modified: ".gmdate("D, d M Y H:i:s")." GMT");
	      header("Cache-Control: no-store, no-cache, must-revalidate");    //HTTP/1.1
	      header("Cache-Control: post-check=0, pre-check=0", false);
	      header("Pragma: no-cache\n");                            //HTTP/1.0
	      @readfile($filename, 'r');
	      exit();
	    }
	  else
	    {
	      // Display the sampleument in a web page
	      if ($answer)
		{
		  if (!$suggestion) {
		    $suggestion = '';
		  }
		  if ($xoopsUser) {
		    $user_id = $xoopsUser->getVar("uid");
		  } else {
		    $user_id = 100;
		  }

		  $res = $xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_sample_feedback")." "
					  ."(sampleid,user_id,answer,suggestion,entered) VALUES "
					  ."('$sampleid','$user_id','$answer','".$ts->makeTareaData4Save($suggestion)."',".time().")");

		  redirect_header($GLOBALS["HTTP_REFERER"],2,_XF_DOC_THANKYOUFORFEEDBACK);
		  exit;
		}

	      $query = "SELECT * "
		 ."FROM ".$xoopsDB->prefix("xf_sample_data")." "
		 ."WHERE sampleid='$sampleid' "
		 ."AND stateid='1'";
	      // stateid = 1 == active
	      $result = $xoopsDB->query($query);

	      if ($xoopsDB->getRowsNum($result) < 1) {
		redirect_header($GLOBALS["HTTP_REFERER"],4,_XF_DOC_DOCUMENTUNAVAILABLE);
		exit;
	      } else {
		$row = $xoopsDB->fetchArray($result);
	      }
	      $project =& group_get_object($group_id);
	      sampleman_header($project,$group_id,$row['title']);

	      echo "<table border='0' width='100%'><tr><td>";
	      // data in DB stored in htmlspecialchars()-encoded form

	      $samplefile = fopen(trim($filename),"r");
	      if ( !$samplefile )
		{
		  echo "Unable to open sampleument:  $php_errormsg<br>\n";
		  exit();
		}
	      $data = "";
	      $size = filesize($filename);
	      while ( !feof ($samplefile) )
		{
		  $data .= fgets($samplefile,$size);
		}
	      fclose($samplefile);

	      echo $ts->makeTareaData4Show($data, 1, 1, 1);
	      echo "</td><td width='1'>&nbsp;"
		."</td><td width='200' valign='top'>";
	      $title = _XF_DOC_FEEDBACK;
	      $content = "<form action='".$_SERVER['PHP_SELF']."' method='post'>"
		 ._XF_DOC_FEEDBACKWILLHELPUS
		 ."<p>"
		 ._XF_DOC_DIDARTICLEANSWERYOURQUESTION."<br>"
		 ."<input type='hidden' name='sampleid' value='".$sampleid."'>"
		 ."<input type='Radio' name='answer' value='2' checked> "._YES." <br>"
		 ."<input type='Radio' name='answer' value='1'> "._NO." <br>"
		 ."<input type='Radio' name='answer' value='0'> "._XF_DOC_DIDNOTAPPLY." <p>"
		 ._XF_DOC_SUGGESTION.":<br>"
		 ."<textarea name='suggestion' cols='15' rows='5' wrap='PHYSICAL'></textarea>"
		 ."<p>"
		 ."<input type='submit' name='submit' value='"._XF_G_SUBMIT."'>"
		 ."</form>";
	      themesidebox($title, $content);
	      echo "</td></tr></table>";
	      sampleman_footer();

	    }
*/
?>