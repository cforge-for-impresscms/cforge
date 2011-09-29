<?php
/**
  *
  * Project Registration: Services&Requirements (informative)
  *
  * This page presents informative description of prerequisites and
  * requirements for hosting on SourceForge. This page doesn't require
  * any actions.
  *
  * Next in sequence: tos.php
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: requirements.php,v 1.3 2004/07/20 19:56:32 devsupaul Exp $
  *
  */

include_once ("../../mainfile.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
$xoopsOption['template_main'] = 'xfnewproject_requirements.html';

if (!$xoopsUser)
{
	redirect_header($GLOBALS["HTTP_REFERER"],2,_NOPERM);
	exit;
}

require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");

function includefile ($filename)
{
  global $xoopsConfig;

  if ( file_exists(XOOPS_ROOT_PATH."/modules/xfmod/language/".$xoopsConfig['language']."/".$filename) ) {
    include(XOOPS_ROOT_PATH."/modules/xfmod/language/".$xoopsConfig['language']."/".$filename);
  } else {
    include(XOOPS_ROOT_PATH."/modules/xfmod/language/english/".$filename);
  }
}

$metaTitle=": "._XF_REG_STEP1;
include("../../header.php");

$xoopsTpl->assign("title",_XF_REG_STEP1);
$xoopsTpl->assign("content",_XF_REG_WELCOME1);
$xoopsTpl->assign("back",_XF_REG_BACK);
$xoopsTpl->assign("continue",_XF_REG_CONTINUE);

include("../../footer.php");
?>
