<?php
include_once("../../mainfile.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/mime_lookup.php");

if ($sampleid && $sampleid > 0){
	$xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_sample_dl_stats")." SET downloads=downloads+1 WHERE sampleid=".$sampleid);
	if(0==$xoopsDB->getAffectedRows()){
		$res =$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_sample_dl_stats")." VALUES (0, ".$sampleid.", 1)");
	}
	$query = "SELECT title, description, data FROM ".$xoopsDB->prefix("xf_sample_data")." WHERE sampleid=".$sampleid." AND ( stateid='1')";
	$sresult = $xoopsDB->query($query);
	$query = "SELECT unix_group_name FROM ".$xoopsDB->prefix("xf_groups")." WHERE group_id=".$group_id;
	$gresult = $xoopsDB->query($query);
	if ($sresult && $xoopsDB->getRowsNum($sresult) > 0 && $gresult && $xoopsDB->getRowsNum($gresult) > 0){
		$sample = $xoopsDB->fetchArray($sresult);
		$group = $xoopsDB->fetchArray($gresult);
		
		$url = $sample['data'];
		if(!strstr($url,"://")){
			$url = _XF_FTP_PATH."/".$group['unix_group_name']."/sample/".$sample['data'];
		}
		header("Content-Disposition: filename=\"".basename($url)."\"");
		header("Content-type: ".mime_lookup($url)."; name=\"".basename($url)."\"");
		header("Cache-Control: no-store, no-cache, must-revalidate");    //HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache\n");                            //HTTP/1.0
		@readfile($url, 'r');
		exit();
		
		
	}
}else if($docid && $docid >0){
	$xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_doc_dl_stats")." SET downloads=downloads+1 WHERE docid=".$docid);
	if(0==$xoopsDB->getAffectedRows()){
		$res =$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_doc_dl_stats")." VALUES (0, ".$docid.", 1)");
	}
	$query = "SELECT title, description, data FROM ".$xoopsDB->prefix("xf_doc_data")." WHERE docid=".$docid." AND ( stateid='1')";
	$sresult = $xoopsDB->query($query);
	$query = "SELECT unix_group_name FROM ".$xoopsDB->prefix("xf_groups")." WHERE group_id=".$group_id;
	$gresult = $xoopsDB->query($query);
	if ($sresult && $xoopsDB->getRowsNum($sresult) > 0 && $gresult && $xoopsDB->getRowsNum($gresult) > 0){
		$doc = $xoopsDB->fetchArray($sresult);
		$group = $xoopsDB->fetchArray($gresult);
		
		$url = $doc['data'];
		if(!strstr($url,"://")){
			$url = _XF_FTP_PATH."/".$group['unix_group_name']."/docs/".$doc['data'];
		}
		header("Content-Disposition: filename=\"".basename($url)."\"");
		header("Content-type: ".mime_lookup($url)."; name=\"".basename($url)."\"");
		header("Cache-Control: no-store, no-cache, must-revalidate");    //HTTP/1.1
		header("Cache-Control: post-check=0, pre-check=0", false);
		header("Pragma: no-cache\n");                            //HTTP/1.0
		@readfile($url, 'r');
		exit();
	}
}

echo "The file you are looking for could not be found.  Please contact the administrator of this project for more information.";
exit;	
	
?>