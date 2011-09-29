<?php
	/**
	* $Id: start.php,v 1.45 2004/04/20 20:08:53 danreese Exp $
	* (c) 2004 Novell, Inc.
	*
	* Starts a build process.
	*/
	header('Content-type: text/xml');
	$error = '';
	 
	// Extract project short name, CVS modules, and target.
	$name = $_POST['n'];
	$cvsmodules = $_POST['m'];
	$target = $_POST['t'];
	if (empty($name) || empty($cvsmodules) || empty($target))
		{
		// Invalid syntax.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 11;
		$args['contents'] = _XFWEBSERVICE_ERROR_011;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Check authorization.
	$perm = get_permissions(session_id(), $name);
	if (!$perm || !($perm->isAdmin() || $perm->isReleaseAdmin() || $perm->isReleaseTechnician()))
		{
		// Not authorized.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 12;
		$args['contents'] = _XFWEBSERVICE_ERROR_012;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Check build quota limits.
	$user = $perm->getUser();
	$userID = $user->getVar('uid');
	$sql = 'SELECT * FROM '.$icmsDB->prefix('xf_webservice_build')
	." WHERE user_id=$userID AND (status='pending' OR status='active')";
	$result = $icmsDB->queryF($sql);
	if (!$result || $icmsDB->getRowsNum($result) >= BUILD_QUOTA)
		{
		// User has too many active builds.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 210;
		$args['contents'] = _XFWEBSERVICE_ERROR_210;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Get CVS host machine.
	include_once ICMS_ROOT_PATH.'/modules/xfmod/include/nxoopsLDAP.php';
	$ldap = new nxoopsLDAP();
	if (!$ldap->connect() || !$ldap->bindAdmin())
		{
		$error = $ldap->lastError();
		$ldap->cleanUp();
		 
		// Could not connect to LDAP server.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 211;
		$args['contents'] = _XFWEBSERVICE_ERROR_211.': '.$error;
		$error = fill_template($tpl, $args);
	}
	else
	{
		$cvsserver = $ldap->getProjectCVSServer($name);
		if (!$cvsserver)
			{
			// Could not determine CVS host.
			$tpl = TEMPLATE_CONTEXT.'error.tpl';
			$args['id'] = 212;
			$args['contents'] = _XFWEBSERVICE_ERROR_212.': '.$ldap->lastError();
			$error = fill_template($tpl, $args);
		}
		else
			{
			$cvshost = $cvsserver->dnsName;
		}
		$ldap->cleanup();
	}
	 
	// Check existence of build scripts.
	if (!$error)
		{
		$dnsmapfile = ICMS_ROOT_PATH.'/modules/xfmod/cvs/dnsmap.php';
		if (file_exists($dnsmapfile))
			{
			include_once $dnsmapfile;
		}
		if (isset($xoopsDNSMap[$cvshost]))
			{
			$cvshost = $xoopsDNSMap[$cvshost];
		}
		foreach (explode(',', $cvsmodules) as $module)
		{
			$cvspath = "/cvs/viewcvs.cgi/$name/$module/$module-build/ximian-build.conf";
			$socket = fsockopen($cvshost, 8080, $errno, $errstr, 10);
			if (!$socket)
				{
				// Build script not found.
				$tpl = TEMPLATE_CONTEXT.'error.tpl';
				$args['id'] = 213;
				$args['contents'] = _XFWEBSERVICE_ERROR_213.": $errstr ($errno)";
				$error = fill_template($tpl, $args);
			}
			else
				{
				// NOTE: This timeout method is now deprecated, but our PHP version is old.
				socket_set_timeout($socket, 10); // 10 second timeout.
				fwrite($socket, "HEAD $cvspath HTTP/1.0\r\nHost: $host\r\n\r\n");
				if (!strpos(fgets($socket, 100), '200 OK'))
					{
					// Build script not found.
					$tpl = TEMPLATE_CONTEXT.'error.tpl';
					$args['id'] = 213;
					$args['contents'] = _XFWEBSERVICE_ERROR_213.": $module";
					$error = fill_template($tpl, $args);
				}
				fclose($socket);
			}
			if ($error) break;
		}
	}
	 
	// Add a build record to the database.
	$status = ($error ? 'failed' : 'pending');
	$start = time();
	$sql = 'INSERT INTO '.$icmsDB->prefix('xf_webservice_build')
	.' (user_id,unix_group_name,target,cvs_host,cvs_modules,start_time,status,error) VALUES' ." ($userID,'$name','$target','$cvshost','$cvsmodules',$start,'$status','$error')";
	if (!$icmsDB->queryF($sql))
		{
		// Could not create build record.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 214;
		$args['contents'] = _XFWEBSERVICE_ERROR_214.': '.$icmsDB->error();
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Return build ID.
	$tpl = TEMPLATE_CONTEXT.'build.tpl';
	$args['id'] = $icmsDB->getInsertId();
	$args['project'] = $name;
	$args['modules'] = $cvsmodules;
	$args['target'] = $target;
	$args['status'] = $status;
	$args['elapsed'] = 1;
	$args['start'] = $start;
	if ($error)
		{
		$args['contents'] = $error;
	}
	echo fill_template($tpl, $args);
?>