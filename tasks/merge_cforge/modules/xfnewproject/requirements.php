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
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	$icmsOption['template_main'] = 'xfnewproject_requirements.html';
	 
	if (!$icmsUser)
		{
		redirect_header($_SERVER["HTTP_REFERER"], 2, _NOPERM . "called from ".__FILE__." line ".__LINE__ );
		exit;
	}
	 
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	 
	function includefile ($filename)
	{
		global $icmsConfig;
		 
		if (file_exists(ICMS_ROOT_PATH."/modules/xfmod/language/".$icmsConfig['language']."/".$filename) )
		{
			include(ICMS_ROOT_PATH."/modules/xfmod/language/".$icmsConfig['language']."/".$filename);
		}
		else
		{
			include(ICMS_ROOT_PATH."/modules/xfmod/language/english/".$filename);
		}
	}
	 
	$metaTitle = ": "._XF_REG_STEP1;
	include("../../header.php");
	 
	$icmsTpl->assign("title", _XF_REG_STEP1);
	$icmsTpl->assign("content", _XF_REG_WELCOME1);
	$icmsTpl->assign("back", _XF_REG_BACK);
	$icmsTpl->assign("continue", _XF_REG_CONTINUE);
	 
	include("../../footer.php");
?>