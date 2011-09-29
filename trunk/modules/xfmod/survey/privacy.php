<?php
/**
  *
  * SourceForge Survey Facility
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: privacy.php,v 1.3 2003/12/09 15:04:01 devsupaul Exp $
  *
  */
include_once ("../../../mainfile.php");

$langfile="survey.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/survey/survey_utils.php");
include_once ("../language/english/survey_text_1.php");
$xoopsOption['template_main'] = 'survey/xfmod_privacy.html';

include (XOOPS_ROOT_PATH."/header.php");

$xoopsTpl->assign("privacy",_XF_SURVEYPRIVACY);

$xoopsTpl->assign("survey_text",_XF_SURVEY_TEXT);

include (XOOPS_ROOT_PATH."/footer.php");

?>