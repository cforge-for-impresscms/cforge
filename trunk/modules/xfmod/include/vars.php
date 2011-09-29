<?php

/**
  * Static variable array definitions.
  *
  * Copyright (c) 1999-2001 VA Linux Systems
  * Copyright (c) 2003-2004 Novell, Inc.
  *
  * @version   $Id: vars.php,v 1.5 2004/07/08 18:35:12 danreese Exp $
  */
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/cache.php");
include_once(cache_include('license','get_license_list()',86400));

// NOTE: These values are taken from http://opensource.org/licenses/.
//       Note that the trove database should match this list.

function get_license_list()
{ 
	global $xoopsDB;

	$sql = "SELECT trove_cat_id FROM ".$xoopsDB->prefix("xf_trove_cat")." WHERE fullname='License'";
	$result=$xoopsDB->query($sql);
	$row=$xoopsDB->fetchArray($result);
	$licenseID=$row['trove_cat_id'];

	$sql = "SELECT trove_cat_id, parent, shortname, fullname, description"
		." FROM ".$xoopsDB->prefix("xf_trove_cat")
		." WHERE fullpath_ids LIKE '$licenseID ::%'";
/*
	$sql = "SELECT trove_cat_id, parent, shortname, fullname, description"
		." FROM xoops_xf_trove_cat"
		." WHERE fullpath_ids LIKE '$licenseID ::%'";
*/
	$result = $xoopsDB->query($sql);

	$all = array();
	while ($row = $xoopsDB->fetchArray($result))
	{
		$all[] = $row;	
	}

	$count = count($all);
	$remove = array();
	for ($i = 0; $i < $count; $i++)
	{
		for ($j = 0; $j < $count; $j++)
		{
			if ($all[$i]['trove_cat_id'] == $all[$j]['parent'])
			{
				$remove[] = $i;
				continue 2;
			}
		}
	}
	foreach ($remove as $isaparent)
	{
		unset ($all[$isaparent]);	
	}
	$return = '<?php';

	// Associative array of full names.
	$return .= "\n".'$LICENSE=array()'.";\n";
	foreach($all as $license)
	{
		$return .= '$LICENSE[\''.$license['shortname'].'\']="'.$license['fullname']."\";\n";	
	}

	// Associative array of descriptions.
	$return .= "\n".'$LICENSE_DESCRIPTION=array()'.";\n";
	foreach($all as $license)
	{
		$return .= '$LICENSE_DESCRIPTION[\''.$license['shortname'].'\']="'.$license['description']."\";\n";	
	}

	// Array of full names.
	$return .= "\n".'$SCRIPT_LICENSE=array()'.";\n";
	foreach($all as $license)
	{
		$return .= '$SCRIPT_LICENSE[]="'.$license['fullname']."\";\n";	
	}

	$return .= '?>';
	return $return;
}
?>