<?php
/**
 * trove.php
 *
 * SourceForge: Breaking Down the Barriers to Open Source Development
 * Copyright 1999-2001 (c) VA Linux Systems
 * http://sourceforge.net
 *
 * @version   $Id: trove.php,v 1.5 2004/02/05 16:11:04 devsupaul Exp $
 */

// ################################## Trove Globals
$TROVE_COMMUNITY = 78465;//any number that will never be in the trove.
$TROVE_MAXPERROOT = 3;
$TROVE_BROWSELIMIT = 20;
$TROVE_HARDQUERYLIMIT = -1;

// ##################################

/**
 * trove_genfullpaths() - Regenerates full path entries for $node and all subnodes
 *
 * @param		int		The node
 * @param		string	The full path for this node
 * @param		int		The full path IDs
 */
function trove_genfullpaths($mynode,$myfullpath,$myfullpathids) {
  global $xoopsDB;
	
	// first generate own path
	$res_update = $xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_trove_cat")." SET "
	                              ."fullpath='".$myfullpath."',"
				      ."fullpath_ids='".$myfullpathids."' "
				      ."WHERE trove_cat_id=".$mynode);
																
	// now generate paths for all children by recursive call
	$res_child = $xoopsDB->query("SELECT trove_cat_id,fullname "
	                             ."FROM ".$xoopsDB->prefix("xf_trove_cat")." "
				     ."WHERE parent='".$mynode."'");

	while ($row_child = $xoopsDB->fetchArray($res_child)) {
		trove_genfullpaths($row_child['trove_cat_id'],
			                 $myfullpath." :: ".$row_child['fullname'],
			                 $myfullpathids." :: ".$row_child['trove_cat_id'] );
	}
}

// #########################################

/**
 * trove_setnode() - Adds a group to a trove node
 *
 * @param		int		The group ID
 * @param		int		The trove category ID
 * @param		int		The root node
 */
function trove_setnode($group_id,$trove_cat_id,$rootnode=0) {
  global $xoopsDB,$TROVE_COMMUNITY;
	// verify we were passed information
	if ((!$group_id) || (!$trove_cat_id))
	  return 1;

	// if not a community 
	if($TROVE_COMMUNITY!=$rootnode){
		//verify trove category exists
		$res_verifycat = $xoopsDB->query("SELECT trove_cat_id,fullpath_ids "
						 ."FROM ".$xoopsDB->prefix("xf_trove_cat")." "
																		 ."WHERE trove_cat_id='$trove_cat_id'");
	
		if ($xoopsDB->getRowsNum($res_verifycat) != 1)
		  return 1;
			
		$row_verifycat = $xoopsDB->fetchArray($res_verifycat);
	
		// if we didnt get a rootnode, find it
		if (!$rootnode) {
			$rootnode = trove_getrootcat($trove_cat_id);
		}
	
		// must first make sure that this is not a subnode of anything current
		$res_topnodes = $xoopsDB->query("SELECT tc.trove_cat_id AS trove_cat_id,tc.fullpath_ids AS fullpath_ids "
						."FROM ".$xoopsDB->prefix("xf_trove_cat")." tc,".$xoopsDB->prefix("xf_trove_group_link")." tgl "
						."WHERE tc.trove_cat_id=tgl.trove_cat_id "
						."AND tgl.group_id='$group_id' "
						."AND tc.root_parent='$rootnode'");
	
		while($row_topnodes = $xoopsDB->fetchArray($res_topnodes)) {
			$pathids = explode(' :: ',$row_topnodes['fullpath_ids']);
			for ($i=0; $i<count($pathids); $i++) {
				// anything here will invalidate this setnode
				if ($pathids[$i] == $trove_cat_id) {
					return 1;
				}
			}
		}
	
		// need to see if this one is more specific than another
		// if so, delete the other and proceed with this insertion
		$subnodeids = explode(' :: ',$row_verifycat['fullpath_ids']);
		$res_checksubs = $xoopsDB->query("SELECT trove_cat_id "
						 ."FROM ".$xoopsDB->prefix("xf_trove_group_link")." "
						 ."WHERE group_id='$group_id' "
						 ."AND trove_cat_root='$rootnode'");
	
		while ($row_checksubs = $xoopsDB->fetchArray($res_checksubs)) {
			// check against all subnodeids
			for ($i=0; $i < count($subnodeids); $i++) {
				if ($subnodeids[$i] == $row_checksubs['trove_cat_id']) {
					// then delete subnode
					$xoopsDB->queryF("DELETE FROM ".$xoopsDB->prefix("xf_trove_group_link")." "
							 ."WHERE group_id=".$group_id." AND trove_cat_id=".$subnodeids[$i]);
				}
			}
		}
	}else{
		//make sure the community exists
		$sql = "SELECT group_id,group_name FROM ".$xoopsDB->prefix("xf_groups")." WHERE type=2 AND group_id=".$trove_cat_id;
		$result = $xoopsDB->queryF($sql);
		$rows = $xoopsDB->getRowsNum($result);
		if(1!=$rows) return 1;
		
		//make sure this community has not allready been added
		$sql = "SELECT trove_cat_id,group_id FROM ".$xoopsDB->prefix("xf_trove_group_link")." WHERE trove_cat_id=".$trove_cat_id." AND group_id=".$group_id;
		$result = $xoopsDB->queryF($sql);
		$rows = $xoopsDB->getRowsNum($result);
		if($rows>0) return 1;
	}
	// if we got this far, must be ok
	$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_trove_group_link")." (trove_cat_id,trove_cat_version,group_id,trove_cat_root) VALUES ("
	                 ."'".$trove_cat_id."',"
			 ."'".time()."',"
			 ."'".$group_id."',"
			 ."'".$rootnode."')");
	return 0;
}

/**
 * trove_getrootcat() - Get the root categegory
 *
 * @param		int		Trove category ID
 */
function trove_getrootcat($trove_cat_id) {
  global $xoopsDB;
	
	$parent = 1;
	$current_cat = $trove_cat_id;

	while ($parent > 0) {
		$res_par = $xoopsDB->query("SELECT parent "
		                           ."FROM ".$xoopsDB->prefix("xf_trove_cat")." "
			  		   ."WHERE trove_cat_id='$current_cat'");

		$row_par = $xoopsDB->fetchArray($res_par);
		
		$parent = $row_par["parent"];
		
		if ($parent == 0)
		  return $current_cat;
			
		$current_cat = $parent;
	}

	return 0;
}

/**
 * trove_getallroots() - Returns an associative array of all project roots
 */
function trove_getallroots($isFoundry=0) {
  global $xoopsDB;
	
  $res = $xoopsDB->query("SELECT trove_cat_id,fullname "
			 ."FROM ".$xoopsDB->prefix("xf_trove_cat")." "
			 ."WHERE parent=0");

	while ($row = $xoopsDB->fetchArray($res)) {
	  if ( $isFoundry )
	    {
	      if ( 0 != strcmp($row["fullname"],"Development Status") &&
		   0 != strcmp($row["fullname"],"License") )
		{
		  // Foundries/Communities don't have these categories
		  $tmpcatid = $row["trove_cat_id"];
		  $CATROOTS[$tmpcatid] = $row["fullname"];
		}
	    }
	  else
	    {
	      $tmpcatid = $row["trove_cat_id"];
	      $CATROOTS[$tmpcatid] = $row["fullname"];
	    }
	}
	return $CATROOTS;
}

/**
 * trove_catselectfull() - Returns full select output for a particular root
 *
 * @param		int	The node
 * @param		string	The category to pre-select
 * @param		string	THe select-box name
 */
function trove_catselectfull($node,$selected,$name) {
  global $xoopsDB;
	
	$content = "<BR><SELECT name='$name'>";
	$content .= "  <OPTION value='0'>"._XF_TRV_NONESELECTED;
	$res_cat = $xoopsDB->query("SELECT trove_cat_id,fullpath "
	                           ."FROM ".$xoopsDB->prefix("xf_trove_cat")." "
				   ."WHERE root_parent='$node' "
				   ."ORDER BY fullpath");

	while ($row_cat = $xoopsDB->fetchArray($res_cat)) {
		$content .= "  <OPTION value='".$row_cat['trove_cat_id']."'";
		if ($selected == $row_cat['trove_cat_id']) $content .= " selected";
		$content .= ">".$row_cat['fullpath']."\n";
	}
	$content .= "</SELECT>\n";
	
	return $content;
}

/**
 * trove_catselectfull() - Returns full select output for a particular root
 *
 * @param		int	The node
 * @param		string	The category to pre-select
 * @param		string	THe select-box name
 */
function trove_communityselectfull($selected,$name) {
  global $xoopsDB;
	
    $sql = "SELECT group_id,group_name FROM ".$xoopsDB->prefix("xf_groups")." WHERE type=2";
    $result = $xoopsDB->queryF($sql);
    $rows = $xoopsDB->getRowsNum($result);
    if($result && $rows > 0){
		$content = "<SELECT name='$name'>";
		$content .= "  <OPTION value='0'>"._XF_TRV_NONESELECTED;

        for($i=0;$i<$rows;$i++){
            $row = $xoopsDB->fetchArray($result);
			$content .= "  <OPTION value='".$row['group_id']."'";
			if ($selected == $row['group_id']) $content .= " selected";
			$content .= ">".$row['group_name']."\n";
        }
		$content .= "</SELECT><BR>\n";
    }

    return $content;
}

/**
 * trove_getcatlisting() - Gets discriminator listing for a group
 *
 * @param		int		The group ID
 * @param		bool	Whether filters have already been applied
 * @param		bool	Whether to print category links
 */
function trove_getcatlisting($group_id,$a_filter,$a_cats, $nofilter=false) {
	global $discrim_url;
	global $expl_discrim;
	global $form_cat;
	global $xoopsDB;

	$group_obj =& group_get_object($group_id);

/*	$res_trovecat = $xoopsDB->query("SELECT tc.fullpath AS fullpath,tc.fullpath_ids AS fullpath_ids,tc.trove_cat_id AS trove_cat_id "
	                               ."FROM ".$xoopsDB->prefix("xf_trove_cat")." tc,".$xoopsDB->prefix("xf_trove_group_link")." tgl "
				       ."WHERE tc.trove_cat_id=tgl.trove_cat_id "
				       ."AND tgl.group_id='$group_id' "
				       ."ORDER BY tc.fullpath");
*/
	$res_trovecat = $xoopsDB->query("SELECT tc.fullpath AS fullpath,tc.fullpath_ids AS fullpath_ids,tgl.trove_cat_id AS trove_cat_id, tc.trove_cat_id AS nullisacommunity"
	                               ." FROM ".$xoopsDB->prefix("xf_trove_group_link")." tgl left join ".$xoopsDB->prefix("xf_trove_cat")." tc "
				       ."ON tgl.trove_cat_id=tc.trove_cat_id "
				       ."WHERE tgl.group_id='$group_id' "
				       ."ORDER BY tc.fullpath");
	$content="";
	if ($xoopsDB->getRowsNum($res_trovecat) < 1) {
		if ( $group_obj->isProject() )
		{
			$content .= _XF_TRV_NONYETCATEGORIZED.' '
				 .'<A href="'.XOOPS_URL.'/modules/xftrove/trove_list.php">Trove '
				 .'Software Map</A>.<p>';
		}
		else
		{
			$content .= _XF_TRV_NONYETCATEGORIZEDCOMM.' '
				 .'<A href="'.XOOPS_URL.'/modules/xftrove/trove_list.php">Trove '
				 .'Software Map</A>.<p>';
		}
	} 
	else {
		// first unset the vars were using here
		$proj_discrim_used='';
		$isfirstdiscrim = 1;
		$myfirsttime = 1;
		$content .= "Trove Categorization<br>";
		$content .= '<UL>';
		while ($row_trovecat = $xoopsDB->fetchArray($res_trovecat)) {
			while(is_null($row_trovecat['nullisacommunity']) && $row_trovecat){
				if($myfirsttime) $content .= '<LI> Community: ';
				$res_comm = $xoopsDB->query("SELECT group_name, unix_group_name FROM ".$xoopsDB->prefix('xf_groups')." WHERE group_id=".$row_trovecat['trove_cat_id']);
				$row_comm = $xoopsDB->fetchArray($res_comm);
				if(!$myfirsttime) $content .= ", ";
				$content .= "<a href='".XOOPS_URL."/modules/xfmod/community/?".$row_comm['unix_group_name']."'>".$row_comm['group_name']."</a>";			
				if ($a_filter) {
					if (in_array($row_trovecat['trove_cat_id'],$expl_discrim)) {
						$content .= ' <b>('._XF_TRV_NOWFILTERING.')</b> ';
					} else {
						$content .= ' <A href="'.XOOPS_URL.'/modules/xftrove/trove_list.php?form_cat='
							.$form_cat;
						if ($discrim_url) {
							$content .= $discrim_url.','.$row_trovecat['trove_cat_id'];
						} else {
							$content .= '&discrim='.$row_trovecat['trove_cat_id'];
						}
	
						$content .= '">';
						if(!$nofilter)
						{
							$content .= '['._XF_TRV_FILTER.'] ';
						}
						$content .= "</A>";
					}
				}
				$row_trovecat = $xoopsDB->fetchArray($res_trovecat);
				if(!$row_trovecat){
					$content .= '</UL>';
					return $content;
				}
				unset($myfirsttime);
			}
			$folders = explode(" :: ",$row_trovecat['fullpath']);
			$folders_ids = explode(" :: ",$row_trovecat['fullpath_ids']);
			$folders_len = count($folders);
			// if first in discrim print root category
			if (!$proj_discrim_used[$folders_ids[0]]) {
				if (!$isfirstdiscrim) $content .= '<BR>';
				$content .= '<LI> '.$folders[0].': ';
			}
	
			// filter links, to add discriminators
			// first check to see if filter is already applied
			$filterisalreadyapplied = 0;
			for ($i=0;$i<sizeof($expl_discrim);$i++) {
				if ($folders_ids[$folders_len-1] == $expl_discrim[$i]) $filterisalreadyapplied = 1;
				}
				// then print the stuff
				if ($proj_discrim_used[$folders_ids[0]]) $content .= ', ';
	
				if ($a_cats) $content .= '<A href="'.XOOPS_URL.'/modules/xftrove/trove_list.php?form_cat='
					.$folders_ids[$folders_len-1].$discrim_url.'">';
				$content .= $folders[$folders_len-1];
				if ($a_cats) $content .= '</A>';
	
				if ($a_filter) {
					if ($filterisalreadyapplied) {
						$content .= ' <b>('._XF_TRV_NOWFILTERING.')</b> ';
					} else {
						$content .= ' <A href="'.XOOPS_URL.'/modules/xftrove/trove_list.php?form_cat='
							.$form_cat;
						if ($discrim_url) {
							$content .= $discrim_url.','.$folders_ids[$folders_len-1];
						} else {
							$content .= '&discrim='.$folders_ids[$folders_len-1];
						}
	
						$content .= '">';
						if(!$nofilter)
						{
							$content .= '['._XF_TRV_FILTER.'] ';
						}
						$content .= "</A>";
					}
				}
			$proj_discrim_used[$folders_ids[0]] = 1;
			$isfirstdiscrim = 0;
		}
		$content .= '</UL>';
	}
	return $content;
}


/**
 * trove_getfullname() - Returns cat fullname
 *
 * @param		int		The node
 */
function trove_getfullname($node) {
  global $xoopsDB;
	
	$res = $xoopsDB->query("SELECT fullname FROM ".$xoopsDB->prefix("xf_trove_cat")." WHERE trove_cat_id='$node'");
	$row = $xoopsDB->fetchArray($res);
	return $row['fullname'];
}

/**
 * trove_getfullpath() - Returns a full path for a trove category
 *
 * @param		int		The node
 */
function trove_getfullpath($node) {
  global $xoopsDB;
	$currentcat = $node;
	$first = 1;
	$return = '';

	while ($currentcat > 0) {
		$res = $xoopsDB->query("SELECT trove_cat_id,parent,fullname "
		                       ."FROM ".$xoopsDB->prefix("xf_trove_cat")." "
				       ."WHERE trove_cat_id='$currentcat'");
													 
		$row = $xoopsDB->fetchArray($res);
		$return = $row["fullname"] . ($first ? "" : " :: ") . $return;
		$currentcat = $row["parent"];
		$first = 0;
	}
	if(!$return){
		//could not be found in the trove so it must be a community
		$res = $xoopsDB->query("select group_name from ".$xoopsDB->prefix("xf_groups")." where group_id=".$node);
		$row = $xoopsDB->fetchArray($res);
		if($row['group_name']) $return = "Community :: ".$row['group_name'];
	}

	return $return;
}

?>