<?php
	/**
	* $Id: start.php,v 1.16 2004/04/21 21:08:49 danreese Exp $
	* (c) 2004 Novell, Inc.
	*
	* Starts a publish process.
	*/
	header('Content-type: text/xml');
	 
	// Extract file ID.
	$fileID = $_POST['f'];
	if (empty($fileID))
		{
		// Invalid syntax.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 11;
		$args['contents'] = _XFWEBSERVICE_ERROR_011;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Get the project for the file.
	$sql = 'SELECT g.unix_group_name FROM ' .$icmsDB->prefix('xf_frs_file').' as f,' .$icmsDB->prefix('xf_frs_release').' as r,' .$icmsDB->prefix('xf_frs_package').' as p,' .$icmsDB->prefix('xf_groups').' as g' ." WHERE f.file_id=$fileID" .' AND f.release_id=r.release_id AND r.package_id=p.package_id AND p.group_id=g.group_id';
	$result = $icmsDB->queryF($sql);
	if (!$result)
		{
		// Could not start publish.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 311;
		$args['contents'] = _XFWEBSERVICE_ERROR_311.': '.$icmsDB->error();
		echo fill_template($tpl, $args);
		return;
	}
	elseif ($icmsDB->getRowsNum($result) < 1)
	{
		// Invalid file ID.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 312;
		$args['contents'] = _XFWEBSERVICE_ERROR_312.': '.$fileID;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Check authorization.
	list ($name) = $icmsDB->fetchRow($result);
	$perm = get_permissions(session_id(), $name);
	if (!$perm || !($perm->isAdmin() || $perm->isReleaseAdmin()))
		{
		// Not authorized.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 12;
		$args['contents'] = _XFWEBSERVICE_ERROR_012;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Only allow files to be published once.
	$sql = 'SELECT * FROM '.$icmsDB->prefix('xf_webservice_publish')." WHERE file_id=$fileID and status!='failed'";
	$result = $icmsDB->queryF($sql);
	if (!$result || $icmsDB->getRowsNum($result) >= 1)
		{
		// File has already been published.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 310;
		$args['contents'] = _XFWEBSERVICE_ERROR_310.($result ? '' : ' (query failed)');
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Add a build status record to the database.
	$user = $perm->getUser();
	$userID = $user->getVar("uid");
	$sql = 'INSERT INTO '.$icmsDB->prefix('xf_webservice_publish')." (user_id,unix_group_name,time,file_id) VALUES ($userID,'$name',".time().",$fileID)";
	if (!$icmsDB->queryF($sql))
		{
		// Could not record publish status.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 313;
		$args['contents'] = _XFWEBSERVICE_ERROR_313.': '.$icmsDB->error();
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Return publish ID.
	$tpl = TEMPLATE_CONTEXT.'publish.tpl';
	$args['id'] = $icmsDB->getInsertId();
	$args['status'] = 'active';
	echo fill_template($tpl, $args);
?>