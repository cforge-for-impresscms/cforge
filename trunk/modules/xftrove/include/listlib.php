<?php
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/trove.php");

function displayProjectList($startswith,$offset,$range,$projlookup=false,$trovedetail,$descriptiondetail)
{
	// example query "select unix_group_name,group_name,short_description,group_id
	// from xoops_xf_groups where group_name like "%" and and is_public=1 type=1
	// and status='A' order by group_name asc limit 0,3";
	global $xoopsDB;
	
	$content = '';

	$startswith = trim($startswith);
	if(strlen($startswith) < 1)
	{
		$sql = "SELECT group_id,group_name,unix_group_name,short_description"
				." FROM ".$xoopsDB->prefix("xf_groups") 
				." WHERE type=1 "
				." AND is_public=1 "
				." AND status='A' "
				." ORDER BY group_name ASC";
	}
	else
	{
		$sql = "SELECT group_id,group_name,unix_group_name,short_description"
				." FROM ".$xoopsDB->prefix("xf_groups") 
				." WHERE (group_name regexp '$startswith'";
		if(strlen($startswith)>2){
				$sql .= " OR unix_group_name regexp '$startswith'"
						." OR short_description regexp '$startswith'";
		}
		$sql .= ")"
				." AND type=1 "
				." AND is_public=1 "
				." AND status='A' "
				." ORDER BY group_name ASC";
	}

	$result = $xoopsDB->query($sql, $range, $offset);
	$rows = $xoopsDB->getRowsNum($result);

	if(!$result || $rows < 1)
	{
		$content = "There are no projects containing the specified criteria.";
		return $content;
	}
	else
	{
		for($i = 0; $i < $rows; $i++)
		{
			$row_grp = $xoopsDB->fetchArray($result);
				$content .=  '<table border="0" cellpadding="0" width="100%">'
					 .'<tr valign="top"><td colspan="2">';

			if(!$projlookup) {
				$content .=  "<a href='".XOOPS_URL."/modules/xfmod/project/?"
					 .$row_grp['unix_group_name']."'>";
			} else {
                                $content .=  "<a href=\"#\" onClick=\"window.opener.document.addprojform.form_proj_name.value='"
					 .htmlspecialchars($row_grp['unix_group_name'])."';window.close();\">";
			}

			$content .= "<b>".htmlspecialchars($row_grp['group_name'])."</b></a>";
			if ($descriptiondetail) 
			{
				//echo " - " . htmlspecialchars($row_grp['short_description']);
				$description = strip_tags($row_grp['short_description']);
				if(strlen($description) > 256)
					$description = substr($description,0,255)."...";
				$content .= " - ".htmlspecialchars($description);
			}

			$content .= '<br></td></tr>';
			if($trovedetail)
			{
				$content .= '<tr valign="top"><td><br/>';
				$content .= trove_getcatlisting($row_grp['group_id'],1,0, true);
				$content .= '</td></tr>';
			}

			$content .= '</table><hr/>';
		}

		return $content;
	}
}

function displayTroveHeader($projlookup=false)
{
	if(!$projlookup) {
		return "<h2 style='text-align:left;'>Projects</h2><a href='trove_list.php'>Project Categories</a> | <a href='project_list.php'>Project List A-Z</a><br/><hr noshade/>";
	}
}

function displayProjectListHeader($startswith,$contains,$range,$offset,$projlookup=false,$trovedetail,$descriptiondetail,$firsttimeonpage){
	$content = '';
	if(!$projlookup) {
		$content .= "<h2 style='text-align:left;'>Projects</h2><a href='trove_list.php'>Project Categories</a> | <a href='project_list.php'>Project List A-Z</a><br/><hr noshade/>";
		$plkup = "no";
	} else {
		$plkup = "yes";
	}
	$content .=  "<table width='100%'><tr>";
	$content .=  "<td><table cellspacing='5'><tr>"
			."<td><b>Starts With</b><br/> </td>"
			."<td><form method='get' action='project_list.php'>"
			."<input type='hidden' name='range' value='$range'>"
			."<input type='hidden' name='descriptiondetail' value='$descriptiondetail'>"
			."<input type='hidden' name='trovedetail' value='$trovedetail'>"
			."<input type='hidden' name='firsttimeonpage' value='$firsttimeonpage'>"
			."<input type='hidden' name='projlookup' value='$plkup'>"
			."<input type='text' name='startswith' value='$startswith'> <input type='submit' value='Search'></form></td>"
			."<tr>"
			."<td><b>Contains</b><br/> </td>"
			."<td><form method='get' action='project_list.php'>"
			."<input type='hidden' name='range' value='$range'>"
			."<input type='hidden' name='descriptiondetail' value='$descriptiondetail'>"
			."<input type='hidden' name='trovedetail' value='$trovedetail'>"
			."<input type='hidden' name='firsttimeonpage' value='$firsttimeonpage'>"
			."<input type='hidden' name='projlookup' value='$plkup'>"
			."<INPUT type='text' name='contains' value='$contains'> <input type='submit' value='Search'></form></td>"
			."</tr></table></td>";
	$content .=  "<td align=right valign=top>"
			."<form method='GET' name='rangeform' action='project_list.php'>"
			."<input type='hidden' name='projlookup' value='$plkup'>"
			."<input type='hidden' name='startswith' value='$startswith'>"
			."<input type='hidden' name='contains' value='$contains'>"
			."<input type='hidden' name='offset' value='$offset'>"
			."<input type='hidden' name='firsttimeonpage' value='$firsttimeonpage'>"
			."View <select name='range' size='1' onChange='javascript:rangeform.submit();'>";
	$options = array(5,10,20,30,50,75,100);
	foreach($options as $option){
		if($option==$range){
			$content .=  "<option selected>".$option;
		}else{
			$content .=  "<option>".$option;
		}
	}
	$content .=  "</select><br/>";
	$content .=  "Display Project Description <input name='descriptiondetail' type='checkbox' onClick='rangeform.submit();'";
	if($descriptiondetail)
	{
		$content .=  " checked";
	}
	$content .=  ">";

	$content .=  "<br>Display Trove Detail <input name='trovedetail' type='checkbox' onClick='rangeform.submit();'";
	if($trovedetail)
	{
		$content .=  " checked";
	}
	$content .=  "></form></TD></TR><TR><TD colspan=2>";
	
	$options = array("B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
	$content .=  "<a href='project_list.php?projlookup=$plkup&startswith=A&range=".$range."&trovedetail=".$trovedetail."&descriptiondetail=".$descriptiondetail."&firsttimeonpage=".$firsttimeonpage."'>A</a>";
	foreach($options as $option){
		$content .=  " | <a href='project_list.php?projlookup=$plkup&startswith=".$option."&range=".$range."&trovedetail=".$trovedetail."&descriptiondetail=".$descriptiondetail."&firsttimeonpage=".$firsttimeonpage."'>".$option."</a>";
	}
	$content .=  " | <a href='project_list.php?projlookup=$plkup&startswith=".urlencode("[0-9]")."&range=".$range."&trovedetail=".$trovedetail."&descriptiondetail=".$descriptiondetail."&firsttimeonpage=".$firsttimeonpage."'>0-9</a>";
	$content .=  " | <a href='project_list.php?projlookup=$plkup&startswith=".urlencode("[^0-9a-z]")."&range=".$range."&trovedetail=".$trovedetail."&descriptiondetail=".$descriptiondetail."&firsttimeonpage=".$firsttimeonpage."'>OTHER</a>";
	
	$content .=  "</td></tr></table><hr noshade/>";

	return $content;
}


function displayProjectListNav($startswith,$offset,$range,$projlookup,$trovedetail,$descriptiondetail){
	//example query "select count(group_id) from xoops_xf_groups where group_name like "%" and type=1 and status='A'";
	global $xoopsDB;
	if(!$projlookup) {
		$plkup = "no";
	} else {
		$plkup = "yes";
	}
	if(strlen($startswith) < 1)
	{
		$sql = "SELECT count(group_id) AS count FROM "
			.$xoopsDB->prefix("xf_groups") 
			." WHERE type=1 "
			." AND is_public=1 "
			." AND status='A' ";
	}
	else
	{
		$sql = "SELECT count(group_id) AS count FROM "
			.$xoopsDB->prefix("xf_groups") 
			." WHERE group_name regexp '".$startswith."'"
			." AND type=1 "
			." AND is_public=1 "
			." AND status='A' ";
	}
	$result = $xoopsDB->query($sql);
	$count = $xoopsDB->fetchArray($result);
	$count = $count['count'];
	if($offset>$count) return '';//if they screw with the query string and set the offset greater than the count dont display anything;
	$content = '';
	$content .=  "<p align='right'>";
	if($count<=$range && $offset==0){
		$range=$count;
		$offset_range = $offset+$range;
		$content .= "<i>(Results ".$offset++." - ".$offset_range." of ".$count.")</i>";
	}
	else {
		$display_range=$range;
		if($offset+$range>$count) 
			$display_range=$count-$offset;

		if($range!=0){//just to make sure a div by 0 does not occur.  Should never happen, but if it does, set it to the default of 10
			$lastoffset = $range*(int)($count/$range);
		}
		else {
			$lastoffset = 0;	
		}

		$prevoffset = $offset-$range;
		if($prevoffset<0)
			$prevoffset = 0;

		$nextoffset = $offset+$range;

		if($nextoffset > $lastoffset)
			$nextoffset = $lastoffset;
			$offset_display_range = $offset+$display_range;
			$content .= "<i>(Results ".$offset++." - ".$offset_display_range." of ".$count.")</i>";

		if($offset!=0) {
			$content .=  "<a href='project_list.php?projlookup=$plkup&offset=0&range=$range&trovedetail=$trovedetail&descriptiondetail=$descriptiondetail&firsttimeonpage=$firsttimeonpage&startswith=".urlencode($startswith)."'>&nbsp;&nbsp;first</a>&nbsp;&nbsp;&nbsp;";
			$content .=  "<a href='project_list.php?projlookup=$plkup&offset=$prevoffset&range=$range&trovedetail=$trovedetail&descriptiondetail=$descriptiondetail&firsttimeonpage=$firsttimeonpage&startswith=".urlencode($startswith)."'>prev</a>&nbsp;&nbsp;&nbsp;";
		}
		else {
			$content .=   "&nbsp;&nbsp;first&nbsp;&nbsp;&nbsp;prev&nbsp;&nbsp;&nbsp;";
		}
		$max=(int)($lastoffset/$range)+1;
		if($max>15) $max=15;
		for($i=0;$i<$max;$i++){
			if(($i*$range)!=($offset-1)){
				$content .=  "<a href='project_list.php?projlookup=$plkup&offset=".$i*$range."&range=$range&trovedetail=$trovedetail&descriptiondetail=$descriptiondetail&firsttimeonpage=$firsttimeonpage&startswith=".urlencode($startswith)."'>".($i+1)."</a>";
			}else{
				$content .=  "<b style='color: #c00'>".($i+1)."</b>";	
			}
			$content .=  "&nbsp;&nbsp;&nbsp;";
		}
		if($offset!=$lastoffset){
			$content .=  "<a href='project_list.php?projlookup=$plkup&offset=$nextoffset&range=$range&trovedetail=$trovedetail&descriptiondetail=$descriptiondetail&firsttimeonpage=$firsttimeonpage&startswith=".urlencode($startswith)."'>next</a>&nbsp;&nbsp;&nbsp;";
			$content .=  "<a href='project_list.php?projlookup=$plkup&offset=$lastoffset&range=$range&trovedetail=$trovedetail&descriptiondetail=$descriptiondetail&firsttimeonpage=$firsttimeonpage&startswith=".urlencode($startswith)."'>last</a>&nbsp;&nbsp;&nbsp;";
		}else{
			$content .=  "next&nbsp;&nbsp;&nbsp;last&nbsp;&nbsp;&nbsp;";	
		}
	}
	$content .=  "</p>";
	return $content;
}

?>
