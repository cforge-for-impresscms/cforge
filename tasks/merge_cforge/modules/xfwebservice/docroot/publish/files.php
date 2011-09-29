<?php
	/**
	* $Id: files.php,v 1.3 2004/04/06 17:09:58 danreese Exp $
	* (c) 2004 Novell, Inc.
	*
	* Returns a list of files.
	*/
	header('Content-type: text/xml');
	 
	// Extract project short name.
	$name = $_GET['n'];
	if (empty($name))
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
	if (!$perm || !($perm->isAdmin() || $perm->isReleaseAdmin()))
		{
		// Not authorized.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 12;
		$args['contents'] = _XFWEBSERVICE_ERROR_012;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Retrieve files information.
	$sql = 'SELECT p.name as package_name,r.name as release_name,f.file_id,f.filename,t.target FROM ' .$icmsDB->prefix('xf_groups').' as g,' .$icmsDB->prefix('xf_frs_package').' as p,' .$icmsDB->prefix('xf_frs_release').' as r,' .$icmsDB->prefix('xf_frs_file').' as f,' .$icmsDB->prefix('xf_frs_target').' as t LEFT JOIN ' .$icmsDB->prefix('xf_webservice_publish').' as w ON (f.file_id = w.file_id)' ." WHERE g.unix_group_name='$name'" .' AND g.group_id=p.group_id AND p.package_id=r.package_id AND r.release_id=f.release_id AND f.file_id=t.file_id' ." AND (w.file_id IS NULL OR w.status = 'failed')" .' ORDER BY p.package_name,r.release_date DESC,r.release_name,f.filename,t.target';
	$result = $icmsDB->queryF($sql);
	if (!$result)
		{
		// Could not retrieve file list.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 300;
		$args['contents'] = _XFWEBSERVICE_ERROR_300.': '.$icmsDB->error();
		echo fill_template($tpl, $args);
		return;
	}
	elseif ($icmsDB->getRowsNum($result) < 1)
	{
		// No publishable files.
		$tpl = TEMPLATE_CONTEXT.'list.tpl';
		$args['name'] = $name;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Construct file list structure.
	while (list($pName, $rName, $fID, $fName, $target) = $icmsDB->fetchRow($result))
	{
		$list_structure[$pName][$rName][$fID]['name'] = $fName;
		$list_structure[$pName][$rName][$fID]['targets'][] = $target;
	}
	 
	$list_tpl = TEMPLATE_CONTEXT.'list.tpl';
	$file_tpl = TEMPLATE_CONTEXT.'file.tpl';
	$target_tpl = TEMPLATE_CONTEXT.'target.tpl';
	$contents = '';
	foreach ($list_structure as $pName => $pkg_structure)
	{
		$pkg_contents = '';
		foreach ($pkg_structure as $rName => $rel_structure)
		{
			$rel_contents = '';
			foreach ($rel_structure as $fID => $file_structure)
			{
				$file_contents = '';
				foreach ($file_structure['targets'] as $target)
				{
					$target_args['name'] = $target;
					$file_contents .= fill_template($target_tpl, $target_args);
				}
				$file_args['id'] = $fID;
				$file_args['name'] = $file_structure['name'];
				$file_args['contents'] = $file_contents;
				$rel_contents .= fill_template($file_tpl, $file_args);
			}
			$rel_args['name'] = $rName;
			$rel_args['contents'] = $rel_contents;
			$pkg_contents .= fill_template($list_tpl, $rel_args);
		}
		$pkg_args['name'] = $pName;
		$pkg_args['contents'] = $pkg_contents;
		$contents .= fill_template($list_tpl, $pkg_args);
	}
	 
	// Return file list.
	$args['name'] = $name;
	$args['contents'] = $contents;
	echo fill_template($list_tpl, $args);
?>