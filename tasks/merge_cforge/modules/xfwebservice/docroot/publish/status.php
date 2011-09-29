<?php
	/**
	* $Id: status.php,v 1.17 2004/03/04 20:56:19 danreese Exp $
	* (c) 2004 Novell, Inc.
	*
	* Checks the status on a publish process.
	*/
	header('Content-type: text/xml');
	 
	// Extract publish ID.
	$publishID = $_GET['p'];
	if (empty($publishID))
		{
		// Invalid syntax.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 11;
		$args['contents'] = _XFWEBSERVICE_ERROR_011;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Retrieve publish information.
	$sql = 'SELECT * FROM '.$icmsDB->prefix('xf_webservice_publish')." WHERE id=$publishID";
	$result = $icmsDB->queryF($sql);
	if (!$result)
		{
		// Could not retrieve publish status.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 320;
		$args['contents'] = _XFWEBSERVICE_ERROR_320.': '.$icmsDB->error();
		echo fill_template($tpl, $args);
		return;
	}
	elseif ($icmsDB->getRowsNum($result) < 1)
	{
		// Invalid publish ID.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 321;
		$args['contents'] = _XFWEBSERVICE_ERROR_321.': '.$publishID;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Check authorization.
	$status_array = $icmsDB->fetchArray($result);
	$perm = get_permissions(session_id(), $status_array['unix_group_name']);
	if (!$perm || !($perm->isAdmin() || $perm->isReleaseAdmin()))
		{
		// Not authorized.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 12;
		$args['contents'] = _XFWEBSERVICE_ERROR_012;
		echo fill_template($tpl, $args);
	}
	else
	{
		// Return status.
		$args['id'] = $publishID;
		$args['status'] = $status_array['status'];
		if ($status_array['status'] == 'failed' && !empty($status_array['error']))
			{
			$tpl = TEMPLATE_CONTEXT.'error.tpl';
			$error_args['id'] = 13;
			$error_args['contents'] = _XFWEBSERVICE_ERROR_013.': '.$status_array['error'];
			$args['contents'] = fill_template($tpl, $error_args);
		}
		$tpl = TEMPLATE_CONTEXT.'publish.tpl';
		echo fill_template($tpl, $args);
	}
?>