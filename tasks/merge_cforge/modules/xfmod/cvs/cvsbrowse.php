<?php
	include_once("../../../mainfile.php");
	$langfile = "project.php";
	 
	include_once(ICMS_ROOT_PATH."/modules/xfmod/include/nxoopsLDAP.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/include/httpClient.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/include/checkurl.php");
	 
	$pathinfo = checkURL($_SERVER['PATH_INFO']);
	 
	if (file_exists("dnsmap.php"))
	{
		include_once("dnsmap.php");
	}
	 
	$pos = strrpos($pathinfo, '/');
	if ($pos === false || ($pos+1) < strlen($pathinfo))
	{
		$pathinfo .= '/';
	}
	 
	// If get parameters are in the URL
	// then ensure they are put back on
	$pathinfo .= "?" . $_SERVER['QUERY_STRING'];
	 
	// Parse URL to get project name
	$projname = trim($_SERVER['PATH_INFO'], "/");
	$projcomps = explode("/", $projname);
	$projname = $projcomps[0];
	 
	if (strchr($projname, "*"))
	{
		$projname = $projcomps[1];
	}
	 
	//if(isset($_COOKIE['cvsview_project']))
	//{
	//$projname = $_COOKIE['cvsview_project'];
	//}
	 
	//if(!isset($_COOKIE['cvsview_cvsdns']))
	//{
	$lldap = new nxoopsLDAP();
	if (!$lldap->bindAdmin())
	{
		$lldap->cleanUp();
		echo "Failed to get project info from LDAP: " . $lldap->lastError() . "<br/>";
		exit();
	}
	 
	$cvsserver = $lldap->getProjectCVSServer($projname);
	if (!$cvsserver)
	{
		$lldap->cleanUp();
		echo "Failed to get CVS Server for project: $projname - " . $lldap->lastError() . "<br/>";
		exit();
	}
	 
	$cvsdns = $cvsserver->dnsName;
	$lldap->cleanUp();
	if (isset($icmsDNSMap[$cvsdns]))
	{
		$cvsdns = $icmsDNSMap[$cvsdns];
	}
	//}
	//else
	//{
	// $cvsdns = $_COOKIE['cvsview_cvsdns'];
	//}
	 
	//setcookie('cvsview_project', $projname, time()+3600, "/");
	//setcookie('cvsview_cvsdns', $cvsdns, time()+3600, "/");
	 
	$cvshost = "http://" . $cvsdns .":8080";
	$cvsscript = "/cvs/viewcvs.cgi";
	$cvssvr = $cvshost.$cvsscript;
	 
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vars.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/news/news_utils.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/trove.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
	 
	$http = new Net_HTTP_Client();
	if (!$http->Connect($cvsdns, 8080))
	{
		echo "<strong>ViewCVS is not available on server $cvsdns</strong><br/>";
		exit();
	}
	 
	$relurl = "/cvs/viewcvs.cgi" . $pathinfo;
	$status = $http->Get($relurl);
	if ($status != 200)
	{
		echo "<strong>Problem contacting server " . $cvsdns . " : " . $http->getStatusMessage() . "</strong><br/>";
		$http->Disconnect();
		exit();
	}
	else
	{
		//$projname = ltrim($_SERVER['PATH_INFO'], "/ ");
		//$projname = rtrim($projname, "/ ");
		 
		$buf = $http->getBody();
		$cheaders = $http->getHeaders();
		$contype = $cheaders['Content-Type'];
		 
		if (0 !== strncasecmp($contype, "text/html", 9))
		{
			header("Content-type: " . $contype);
			echo $buf;
		}
		else
		{
			$res = $icmsDB->query("SELECT group_id FROM ".$icmsDB->prefix("xf_groups")." WHERE unix_group_name='".$projname."'");
			if (!$res || $icmsDB->getRowsNum($res) < 1)
			{
				$icmsForgeErrorHandler->setSystemError("Invalid project id passed to the cvs page");
			}
			else
				{
				$group_arr = $icmsDB->fetchArray($res);
				$group_id = $group_arr['group_id'];
			}
			 
			$project = group_get_object($group_id);
			 
			include("../../../header.php");
			 
			//project nav information
			echo project_title($project);
			echo project_tabs('CVS', $group_id);
			 
			$patterns = array("/<a.*\[Development\]<\/a>/");
			$replaces = array("");
			$buf = preg_replace($patterns, $replaces, $buf);
			 
			$replace = ICMS_URL . $_SERVER['SCRIPT_NAME'];
			$buf = str_replace($cvssvr, $replace, $buf);
			$buf = str_replace($cvsscript, $replace, $buf);
			 
			echo $buf;
			include("../../../footer.php");
		}
	}
	$http->Disconnect();
	 
?>