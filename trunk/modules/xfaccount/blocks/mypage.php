<?php

function b_myprojects_show(){
	global $xoopsDB,$xoopsUser;
	$block = array();
	
	$myprojects_sql = "SELECT g.group_name,g.group_id,g.unix_group_name,g.status,g.type,ug.admin_flags "
	." FROM ".$xoopsDB->prefix("xf_groups")." g,".$xoopsDB->prefix("xf_user_group")." ug "
	." WHERE g.group_id=ug.group_id "
	." AND ug.user_id='".$xoopsUser->getVar("uid")."' "
	." AND g.status='A'"
	." ORDER BY g.group_name LIMIT 5";
				
	$myprojects_result = $xoopsDB->query($myprojects_sql);
	$myprojects_rows = $xoopsDB->getRowsNum($myprojects_result);
	/*
		PROJECT LIST
	*/

	if (!$myprojects_result || $myprojects_rows < 1) {
		$block["no_projects"]=true;
	}
	else{
		$has_projects = 0;
		$has_communities = 0;
		for ($i=0; $i<$myprojects_rows; $i++){
			$prj_list[$i] = $xoopsDB->fetchArray($myprojects_result);
			if($prj_list[$i]['type'] == 2){
				$has_communities=1;
			}
			else{
				$has_projects=1;
			}
		}
		$block["prj_list"]=$prj_list;
		if ( $has_projects ){
			if ( $has_communities ){
					$block["prj_comm_block_title"]="My Projects/Communities";//_XF_MY_MYPRJCOMM
			}
			else{
				$block["prj_comm_block_title"]="My Projects";//_XF_MY_MYPROJECTS
			}
		}
		else{
			$block["prj_comm_block_title"]="My Communities";//_XF_MY_MYCOMM;
		}
	}
	return $block;
}

/*
	Filemodules that are actively monitored
*/
function b_myfiles_show(){
	global $xoopsDB,$xoopsUser;
        $ts = &MyTextSanitizer::getInstance();
	$block = array();

	$sql = "SELECT g.group_name,g.unix_group_name,g.group_id,p.name,f.filemodule_id "
	      ."FROM ".$xoopsDB->prefix("xf_groups")." g,".$xoopsDB->prefix("xf_filemodule_monitor")." f,".$xoopsDB->prefix("xf_frs_package")." p "
				."WHERE g.group_id=p.group_id AND g.status = 'A' "
		    ."AND p.package_id=f.filemodule_id "
		    ."AND f.user_id='".$xoopsUser->getVar("uid")."' ORDER BY group_name DESC LIMIT 5";
	$result = $xoopsDB->query($sql);
	$rows = $xoopsDB->getRowsNum($result);
	$content="";

	if (!$result || $rows < 1) {
		$content="<TR><TD>You are not monitoring any File Modules.</TD></TR>";
	}
	else
	{
		$last_group=0;
		while ($row = $xoopsDB->fetchArray($result))
		{
			if ($row['group_id'] != $last_group)
			{
				$content .= "<TR><TD><B><A HREF='".$XOOPS_URL."/modules/xfmod/project/?"
                   .$row['unix_group_name']."'>"
                   .$ts->makeTboxData4Show($row['group_name'])."</A></TD></TR>";
			}
			$content .= "<TR><TD id=''><A HREF='".$XOOPS_URL."/modules/xfmod/project/showfiles.php?group_id=".$row['group_id']."'>"
                   .$ts->makeTboxData4Show($row['name'])."</A></TD></TR>";

			$last_group=$row['group_id'];
		}
	}
	$block["files_content"]=$content;
	return $block;	
}
?>