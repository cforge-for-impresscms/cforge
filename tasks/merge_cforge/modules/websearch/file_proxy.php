<?php
	include_once("../../mainfile.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/mime_lookup.php");
	 
	if ($sampleid && $sampleid > 0)
		{
		$icmsDB->queryF("UPDATE ".$icmsDB->prefix("xf_sample_dl_stats")." SET downloads=downloads+1 WHERE sampleid=".$sampleid);
		if (0 == $icmsDB->getAffectedRows())
		{
			$res = $icmsDB->queryF("INSERT INTO ".$icmsDB->prefix("xf_sample_dl_stats")." VALUES (0, ".$sampleid.", 1)");
		}
		$query = "SELECT title, description, data FROM ".$icmsDB->prefix("xf_sample_data")." WHERE sampleid=".$sampleid." AND ( stateid='1')";
		$sresult = $icmsDB->query($query);
		$query = "SELECT unix_group_name FROM ".$icmsDB->prefix("xf_groups")." WHERE group_id=".$group_id;
		$gresult = $icmsDB->query($query);
		if ($sresult && $icmsDB->getRowsNum($sresult) > 0 && $gresult && $icmsDB->getRowsNum($gresult) > 0)
			{
			$sample = $icmsDB->fetchArray($sresult);
			$group = $icmsDB->fetchArray($gresult);
			 
			$url = $sample['data'];
			if (!strstr($url, "://"))
			{
				$url = _XF_FTP_PATH."/".$group['unix_group_name']."/sample/".$sample['data'];
			}
			header("Content-Disposition: filename=\"".basename($url)."\"");
			header("Content-type: ".mime_lookup($url)."; name=\"".basename($url)."\"");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			//HTTP/1.1
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache\n");
			//HTTP/1.0
			@readfile($url, 'r');
			exit();
			 
			 
		}
	}
	else if($docid && $docid > 0)
	{
		$icmsDB->queryF("UPDATE ".$icmsDB->prefix("xf_doc_dl_stats")." SET downloads=downloads+1 WHERE docid=".$docid);
		if (0 == $icmsDB->getAffectedRows())
		{
			$res = $icmsDB->queryF("INSERT INTO ".$icmsDB->prefix("xf_doc_dl_stats")." VALUES (0, ".$docid.", 1)");
		}
		$query = "SELECT title, description, data FROM ".$icmsDB->prefix("xf_doc_data")." WHERE docid=".$docid." AND ( stateid='1')";
		$sresult = $icmsDB->query($query);
		$query = "SELECT unix_group_name FROM ".$icmsDB->prefix("xf_groups")." WHERE group_id=".$group_id;
		$gresult = $icmsDB->query($query);
		if ($sresult && $icmsDB->getRowsNum($sresult) > 0 && $gresult && $icmsDB->getRowsNum($gresult) > 0)
			{
			$doc = $icmsDB->fetchArray($sresult);
			$group = $icmsDB->fetchArray($gresult);
			 
			$url = $doc['data'];
			if (!strstr($url, "://"))
			{
				$url = _XF_FTP_PATH."/".$group['unix_group_name']."/docs/".$doc['data'];
			}
			header("Content-Disposition: filename=\"".basename($url)."\"");
			header("Content-type: ".mime_lookup($url)."; name=\"".basename($url)."\"");
			header("Cache-Control: no-store, no-cache, must-revalidate");
			//HTTP/1.1
			header("Cache-Control: post-check=0, pre-check=0", false);
			header("Pragma: no-cache\n");
			//HTTP/1.0
			@readfile($url, 'r');
			exit();
		}
	}
	 
	echo "The file you are looking for could not be found.  Please contact the administrator of this project for more information.";
	exit;
	 
?>