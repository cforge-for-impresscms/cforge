<?php
	/**
	*
	* Project Registration: Terms of Service (legal)
	*
	* This page presents Terms of Service Agreement and requires user
	* subscription to it to continue registration.
	*
	* Next in sequence: projectinfo.php
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: tos.php,v 1.5 2004/07/20 19:56:32 devsupaul Exp $
	*
	*/
	 
	include_once ("../../mainfile.php");
	$icmsOption['template_main'] = 'xfnewproject_tos.html';
	 
	 
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
	 
	$metaTitle = ": "._XF_REG_STEP2;
	include("../../header.php");
	 
	$icmsTpl->assign("title", _XF_REG_STEP2);
	$icmsTpl->assign("content", "<div align='center'><textarea cols='80' rows='25' readonly>"._XF_REG_WELCOME2."</textarea></div><p/>"._XF_REG_AGREE."<p/>");
	$icmsTpl->assign("reg_iagree", _XF_REG_IAGREE);
	$icmsTpl->assign("back", _XF_REG_BACK);
	 
	include("../../footer.php");
?>