<?php
	/**
	*
	* SourceForge Trove Software Map
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: trove_list.php,v 1.5 2004/01/26 18:56:53 devsupaul Exp $
	*
	*/
	include ("header.php");
	 
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vars.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/trove.php");
	require_once(ICMS_ROOT_PATH."/modules/xftrove/include/listlib.php");
	 
	$icmsOption['template_main'] = 'xftrove_trove_list.html';
	 
	$metaTitle = ": Category Search";
	 
	include_once(ICMS_ROOT_PATH."/header.php");
	 
	$icmsTpl->assign("displayTroveHeader", displayTroveHeader());
	 
	$form_cat = (isset($_GET['form_cat'])) ? trim(StopXSS($_GET['form_cat'])) :
	 ((isset($_POST['form_cat'])) ? trim(StopXSS($_POST['form_cat'])):'');
	$discrim = (isset($_GET['discrim'])) ? trim(StopXSS($_GET['discrim'])) :
	 ((isset($_POST['discrim'])) ? trim(StopXSS($_POST['discrim'])):'');
	$discrim_desc = (isset($_GET['discrim_desc'])) ? trim(StopXSS($_GET['discrim_desc'])) :
	 ((isset($_POST['discrim_desc'])) ? trim(StopXSS($_POST['discrim_desc'])):'');
	$discrim_url = (isset($_GET['discrim_url'])) ? trim(StopXSS($_GET['discrim_url'])) :
	 ((isset($_POST['discrim_url'])) ? trim(StopXSS($_POST['discrim_url'])):'');
	$discrim_queryalias = (isset($_GET['discrim_queryalias'])) ? trim(StopXSS($_GET['discrim_queryalias'])) :
	 ((isset($_POST['discrim_queryalias'])) ? trim(StopXSS($_POST['discrim_queryalias'])):'');
	$discrim_queryand = (isset($_GET['discrim_queryand'])) ? trim(StopXSS($_GET['discrim_queryand'])) :
	 ((isset($_POST['discrim_queryand'])) ? trim(StopXSS($_POST['discrim_queryand'])):'');
	$page = (isset($_GET['page'])) ? trim(StopXSS($_GET['page'])) :
	 ((isset($_POST['page'])) ? trim(StopXSS($_POST['page'])):'');
	 
	//echo'<HR NoShade>';
	 
	// assign default. 18 is 'topic'
	if (!$form_cat)
		$form_cat = 18;
	 
	$form_cat = intval($form_cat);
	if ($form_cat != $TROVE_COMMUNITY)
	{
		// get info about current folder
		$res_trove_cat = $icmsDB->query("SELECT * " ."FROM ".$icmsDB->prefix("xf_trove_cat")." " ."WHERE trove_cat_id='$form_cat'");
		 
		if ($icmsDB->getRowsNum($res_trove_cat) < 1)
			{
			$feedback = 'Invalid Trove Category<br/>That Trove category does not exist: '.$icmsDB->error();
		}
		$row_trove_cat = $icmsDB->fetchArray($res_trove_cat);
	}
	 
	// #####################################
	// this section limits search and requeries if there are discrim elements
	 
	unset ($discrim_url);
	unset ($discrim_desc);
	 
	if ($discrim)
		{
		unset ($discrim_queryalias);
		unset ($discrim_queryand);
		unset ($discrim_url_b);
		 
		// commas are ANDs
		$expl_discrim = explode(',', $discrim);
		 
		// need one link for each "get out of this limit" links
		$discrim_url = '&discrim=';
		 
		$lims = sizeof($expl_discrim);
		if ($lims > 6)
		{
			$lims = 6;
		}
		 
		// one per argument
		for ($i = 0; $i < $lims; $i++)
		{
			// make sure these are all ints, no url trickery
			$expl_discrim[$i] = intval($expl_discrim[$i]);
			 
			// need one aliased table for everything
			$discrim_queryalias .= ','.$icmsDB->prefix("xf_trove_group_link").' trove_group_link_'.$i.' ';
			 
			// need additional AND entries for aliased tables
			$discrim_queryand .= 'AND trove_group_link_'.$i.'.trove_cat_id=' .$expl_discrim[$i].' AND trove_group_link_'.$i.'.group_id=' .''.$icmsDB->prefix("xf_trove_agg").'.group_id ';
			 
			// must build query string for all urls
			if ($i == 0)
			{
				$discrim_url .= $expl_discrim[$i];
			}
			else
			{
				$discrim_url .= ','.$expl_discrim[$i];
			}
			// must also do this for EACH "get out of this limit" links
			// convoluted logic to build urls for these, but works quickly
			for ($j = 0; $j < sizeof($expl_discrim); $j++)
			{
				if ($i != $j)
				{
					if (!$discrim_url_b[$j])
					{
						$discrim_url_b[$j] = '&discrim='.$expl_discrim[$i];
					}
					else
					{
						$discrim_url_b[$j] .= ','.$expl_discrim[$i];
					}
				}
			}
			 
		}
		 
		// build text for top of page on what viewier is seeing
		$discrim_desc = '';
		//'<FONT size="-1"><FONT color="#FF0000">Now limiting view to projects in the following categories:</FONT>';
		 
		for ($i = 0; $i < sizeof($expl_discrim); $i++)
		{
			$discrim_desc .= '<BR> &nbsp; &nbsp; &nbsp; ' .trove_getfullpath($expl_discrim[$i])
			.' <A href="'.ICMS_URL.'/modules/xftrove/trove_list.php?form_cat='.$form_cat .$discrim_url_b[$i].'">[Remove This Filter]' .'</a>';
		}
		$discrim_desc .= "<HR></FONT>\n";
		 
	} // if ($discrim)
	 
	$icmsTpl->assign("discrim_desc", $discrim_desc);
	 
	// ######## two column table for key on right
	// first print all parent cats and current cat
	 
	$content = '';
	 
	if ($form_cat != $TROVE_COMMUNITY)
	{
		$folders = explode(" :: ", $row_trove_cat['fullpath']);
		$folders_ids = explode(" :: ", $row_trove_cat['fullpath_ids']);
		$folders_len = count($folders);
		for ($i = 0; $i < $folders_len; $i++)
		{
			for ($sp = 0; $sp < ($i * 2); $sp++)
			{
				$content .= " &nbsp; ";
			}
			$content .= '<img src="'.ICMS_URL.'/modules/xfmod/images/ic/ofolder15.png" width="15" height="13" alt="">';
			$content .= "&nbsp; ";
			// no anchor for current cat
			if ($folders_ids[$i] != $form_cat)
			{
				$content .= '<A href="'.ICMS_URL.'/modules/xftrove/trove_list.php?form_cat=' .$folders_ids[$i].$discrim_url.'">';
			}
			else
			{
				$content .= '<strong>';
			}
			$content .= $folders[$i];
			if ($folders_ids[$i] != $form_cat)
			{
				$content .= '</a>';
			}
			else
			{
				$content .= '</strong>';
			}
			$content .= "<BR>\n";
		}
		 
		// print subcategories
		$res_sub = $icmsDB->query("SELECT tc.trove_cat_id AS trove_cat_id,tc.fullname AS fullname,tts.subprojects AS subprojects " ."FROM ".$icmsDB->prefix("xf_trove_cat")." tc LEFT JOIN ".$icmsDB->prefix("xf_trove_treesums")." tts USING (trove_cat_id) " ."WHERE (tts.limit_1=0 OR tts.limit_1 IS NULL) " ."AND tc.parent='$form_cat' " ."ORDER BY fullname");
		//echo $icmsDB->error();
		 
		while ($row_sub = $icmsDB->fetchArray($res_sub))
		{
			for ($sp = 0; $sp < ($folders_len * 2); $sp++)
			{
				$content .= " &nbsp; ";
			}
			$content .= '<a href="trove_list.php?form_cat='.$row_sub['trove_cat_id'].$discrim_url.'">';
			$content .= '<img src="'.ICMS_URL.'/modules/xfmod/images/ic/cfolder15.png" width="15" height="13" alt="">';
			$content .= '&nbsp; '.$row_sub['fullname'].'</a> <I>(' .($row_sub['subprojects']?$row_sub['subprojects']:'0')
			.')</I><BR>';
		}
	}
	else
	{
		 
		$content .= '<img src="'.ICMS_URL.'/modules/xfmod/images/ic/ofolder15.png" width="15" height="13" alt="">';
		$content .= "&nbsp; <strong>Community</strong><br>";
		 
		$sql = "SELECT group_name, group_id FROM ".$icmsDB->prefix("xf_groups")." WHERE type=2 ";
		$result = $icmsDB->query($sql, $limit);
		//echo $icmsDB->error();
		 
		while ($row_sub = $icmsDB->fetchArray($result))
		{
			$content .= " &nbsp; &nbsp; ";
			$content .= '<a href="trove_list.php?form_cat='.$TROVE_COMMUNITY.'&comm='.$row_sub['group_id'].$discrim_url.'">';
			$content .= '<img src="'.ICMS_URL.'/modules/xfmod/images/ic/cfolder15.png" width="15" height="13" alt="">';
			$content .= '&nbsp; '.$row_sub['group_name'].'</a>';
			$count_res = $icmsDB->query("SELECT count(trove_cat_id) as count from ".$icmsDB->prefix("xf_trove_group_link")." where trove_cat_id=".$row_sub['group_id']);
			$count_row = $icmsDB->fetchArray($count_res);
			$content .= '<I>('.$count_row['count'].')</I>';
			$content .= '<BR>';
		}
		 
	}
	 
	$icmsTpl->assign("content", $content);
	 
	// ########### right column: root level
	 
	// here we print list of root level categories, and use open folder for current
	$res_rootcat = $icmsDB->query("SELECT trove_cat_id,fullname " ."FROM ".$icmsDB->prefix("xf_trove_cat")." " ."WHERE parent=0 " ."ORDER BY fullname");
	//echo $icmsDB->error();
	 
	$content2 = '';
	 
	while ($row_rootcat = $icmsDB->fetchArray($res_rootcat))
	{
		// print open folder if current, otherwise closed
		// also make anchor if not current
		$content2 .= '<BR>';
		if (($row_rootcat['trove_cat_id'] == $row_trove_cat['root_parent'])
			|| ($row_rootcat['trove_cat_id'] == $row_trove_cat['trove_cat_id']))
		{
			$content2 .= '<img src="'.ICMS_URL.'/modules/xfmod/images/ic/ofolder15.png" width="15" height="13" alt="">';
			$content2 .= '&nbsp; <strong>'.$row_rootcat['fullname']."</strong>\n";
		}
		else
		{
			$content2 .= '<A href="'.ICMS_URL.'/modules/xftrove/trove_list.php?form_cat=' .$row_rootcat['trove_cat_id'].$discrim_url.'">';
			$content2 .= '<img src="'.ICMS_URL.'/modules/xfmod/images/ic/cfolder15.png" width="15" height="13" alt="">';
			$content2 .= '&nbsp; '.$row_rootcat['fullname']."\r\n";
			$content2 .= '</a>';
		}
	}
	 
	$icmsTpl->assign("content2", $content2);
	 
	$content3 = '';
	if ($form_cat == $TROVE_COMMUNITY)
	{
		$content3 .= '<img src="'.ICMS_URL.'/modules/xfmod/images/ic/ofolder15.png" width="15" height="13" alt="">';
		$content3 .= "&nbsp; <strong>Community</strong>\n";
	}
	else
	{
		$content3 .= '<A href="'.ICMS_URL.'/modules/xftrove/trove_list.php?form_cat='.$TROVE_COMMUNITY.$discrim_url.'">';
		$content3 .= '<img src="'.ICMS_URL.'/modules/xfmod/images/ic/cfolder15.png" width="15" height="13" alt="">';
		$content3 .= "&nbsp; Community\n";
		$content3 .= '</a>';
	}
	 
	$icmsTpl->assign("content3", $content3);
	 
	// one listing for each project
	if ($form_cat == $TROVE_COMMUNITY)
	{
		$res_grp = $icmsDB->query("SELECT * " ."FROM ".$icmsDB->prefix("xf_trove_agg")." " ."$discrim_queryalias " ."WHERE ".$icmsDB->prefix("xf_trove_agg").".trove_cat_id='$comm' " ."$discrim_queryand " ."ORDER BY ".$icmsDB->prefix("xf_trove_agg").".trove_cat_id ASC " .",".$icmsDB->prefix("xf_trove_agg").".ranking ASC" , $TROVE_HARDQUERYLIMIT, 0);
	}
	else
	{
		$q_getallprojects = "
			SELECT * " ."FROM ".$icmsDB->prefix("xf_trove_agg")." " ."$discrim_queryalias " ."WHERE ".$icmsDB->prefix("xf_trove_agg").".trove_cat_id='$form_cat' " ."$discrim_queryand " ."ORDER BY ".$icmsDB->prefix("xf_trove_agg").".trove_cat_id ASC " .",".$icmsDB->prefix("xf_trove_agg").".ranking ASC
			";
		//icms_debug_info( 'q_getallprojects', $q_getallprojects );
		$res_grp = $icmsDB->query($q_getallprojects , $TROVE_HARDQUERYLIMIT, 0);
	}
	 
	//echo $icmsDB->error();
	$querytotalcount = $icmsDB->getRowsNum($res_grp);
	 
	// #################################################################
	// limit/offset display
	 
	// no funny stuff with get vars
	$page = intval($page);
	if (!$page)
	{
		$page = 1;
	}
	 
	// store this as a var so it can be printed later as well
	$html_limit = '';
	if ($querytotalcount == $TROVE_HARDQUERYLIMIT)
		{
		$html_limit .= 'More than ';
	}
	 
	$html_limit .= '<strong>'.$querytotalcount.'</strong> projects in result set.';
	 
	// only display pages stuff if there is more to display
	if ($querytotalcount > $TROVE_BROWSELIMIT)
	{
		$html_limit .= ' Displaying '.$TROVE_BROWSELIMIT.' per page. Projects sorted by activity ranking.<BR>';
		 
		// display all the numbers
		for ($i = 1; $i <= ceil($querytotalcount/$TROVE_BROWSELIMIT); $i++)
		{
			$html_limit .= ' ';
			if ($page != $i)
			{
				$html_limit .= '<A href="'.ICMS_URL.'/modules/xftrove/trove_list.php?form_cat='.$form_cat;
				$html_limit .= $discrim_url.'&page='.$i;
				if ($comm)
				{
					$html_limit .= '&comm='.$comm;
				}
				$html_limit .= '">';
			}
			 else $html_limit .= '<strong>';
			$html_limit .= '&lt;'.$i.'&gt;';
			if ($page != $i)
			{
				$html_limit .= '</a>';
			}
			 else $html_limit .= '</strong>';
			$html_limit .= ' ';
		}
	}
	 
	$icmsTpl->assign("html_limit", $html_limit);
	 
	$content4 = '';
	// #################################################################
	// print actual project listings
	// note that the for loop starts at 1, not 0
	for ($i_proj = 1; $i_proj <= $querytotalcount; $i_proj++)
	{
		$row_grp = $icmsDB->fetchArray($res_grp);
		 
		// check to see if row is in page range
		if (($i_proj > (($page-1) * $TROVE_BROWSELIMIT)) && ($i_proj <= ($page * $TROVE_BROWSELIMIT)))
		{
			$viewthisrow = 1;
		}
		else
		{
			$viewthisrow = 0;
		}
		 
		if ($row_grp && $viewthisrow)
		{
			$content4 .= '<table border="0" cellpadding="0" width="100%"><th valign="top"><td colspan="2"><FONT face="arial, helvetica" size="3">';
			$content4 .= "$i_proj. <a href='".ICMS_URL."/modules/xfmod/project/?".$row_grp['unix_group_name']."'><strong>" .htmlspecialchars($row_grp['group_name'])."</strong></a> ";
			if ($row_grp['short_description'])
			{
				$content4 .= "- " . ($row_grp['short_description']);
			}
			 
			$content4 .= '<BR>&nbsp;';
			// extra description
			$content4 .= '</td></th><th valign="top"><td><FONT face="arial, helvetica" size="3">';
			// list all trove categories
			$content4 .= trove_getcatlisting($row_grp['group_id'], 1, 0);
			 
			$content4 .= '</td>'."\r\n".'<td align="right"><FONT face="arial, helvetica" size="3">'; // now the right side of the display
			$content4 .= 'Activity Percentile: <strong>'. number_format($row_grp['percentile'], 2) .'</strong>';
			$content4 .= '<BR>Activity Ranking: <strong>'. number_format($row_grp['ranking'], 2) .'</strong>';
			$content4 .= '<BR>Register Date: <strong>'.date($sys_datefmt, $row_grp['register_time']).'</strong>';
			$content4 .= '</td></th>';
			/*
			if ($row_grp['jobs_count']) {
			print '<tr><td colspan="2" align="center">'
			.'<a href="/people/?group_id='.$row_grp['group_id'].'">[This project needs help]</a></td></td>';
			}
			*/
			$content4 .= '</table>';
			$content4 .= '<HR>';
		} // end if for row and range chacking
	}
	 
	$icmsTpl->assign("content4", $content4);
	 
	// print bottom navigation if there are more projects to display
	if ($querytotalcount > $TROVE_BROWSELIMIT)
	{
		//print $html_limit;
	}
	 
	// print '<p><FONT size="-1">This listing was produced by the following query: '
	// .$query_projlist.'</FONT>';
	 
	include_once(ICMS_ROOT_PATH."/footer.php");
	 
?>