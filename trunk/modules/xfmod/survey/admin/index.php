<?php
/**
  *
  * SourceForge Survey Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.4 2003/12/09 15:04:02 devsupaul Exp $
  *
  */

include_once ("../../../../mainfile.php");
include_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_common.php");
$survey_page = SURVEY_ADMIN_PAGE;

$langfile="survey.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
$xoopsOption['template_main'] = 'survey/admin/xfmod_index.html';

project_check_access ($group_id);

// get current information
$group =& group_get_object($group_id);
$perm  =& $group->getPermission( $xoopsUser );

if(!$perm->isAdmin())
{
	$xoopsForgeErrorHandler->setSystemError(_XF_SUR_NOTALLOWED);
}

include (XOOPS_ROOT_PATH."/header.php");

$header = survey_header($group, "Survey Administration", 'is_admin_page');
$xoopsTpl->assign("survey_header",$header);

$xoopsTpl->assign("xoopsurl",XOOPS_URL);
$xoopsTpl->assign("sur_info1",_XF_SUR_INFO1);
$xoopsTpl->assign("sur_info2",_XF_SUR_INFO2);
$xoopsTpl->assign("sur_info3",_XF_SUR_INFO3);
$xoopsTpl->assign("sur_info4",_XF_SUR_INFO4);
$xoopsTpl->assign("sur_info5",_XF_SUR_INFO5);
$xoopsTpl->assign("sur_info6",_XF_SUR_INFO6);
$xoopsTpl->assign("group_id",$group_id);
$xoopsTpl->assign("sur_edit",_XF_SUR_EDITSURVEYS);

include (XOOPS_ROOT_PATH."/footer.php");

?>