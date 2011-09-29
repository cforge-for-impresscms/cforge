<?php
	/**
	*
	* Project Registration: Project Information.
	*
	* This page is used to request data required for project registration:
	*  o Project Public Name
	*  o Project Registartion Purpose
	*  o Project License
	*  o Project Public Description
	*  o Project Unix Name
	* All these data are more or less strictly validated.
	*
	* This is last page in registartion sequence. Its successful subsmission
	* leads to creation of new group with Pending status, suitable for approval.
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001 (c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: projectinfo.php,v 1.9 2004/07/20 19:56:32 devsupaul Exp $
	*
	*/
	 
	include_once ("../../mainfile.php");
	 
	$type = "project";
	$utype = "Project";
	 
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vars.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/account.php");
	include_once ("../xfmod/language/english/register_step_1.php");
	include_once ("../xfmod/language/english/register_step_2.php");
	include_once ("../xfmod/language/english/register_step_3.php");
	include_once ("../xfmod/language/english/register_step_3a.php");
	include_once ("../xfmod/language/english/register_step_4.php");
	include_once ("../xfmod/language/english/register_step_5.php");
	$icmsOption['template_main'] = 'xfnewproject_projectinfo.html';
	 
	if (!$icmsUser)
		{
		redirect_header($_SERVER["HTTP_REFERER"], 2, _NOPERM . "called from ".__FILE__." line ".__LINE__ );
		exit;
	}
	 
	$submit = $_POST['submit'];
	 
	$metaTitle = ": "._XF_REG_STEP3;
	include("../../header.php");
	 
	if ($submit == 'Finish')
		{
		$feedback = '';
		$full_name = trim($_POST['full_name']);
		$purpose = trim($_POST['purpose']);
		$license = trim($_POST['license']);
		$license_other = trim($_POST['license_other']);
		$description = trim($_POST['description']);
		$unix_name = strtolower($_POST['unix_name']);
		 
		// Fierce validation
		if (strlen($full_name) < 3)
			{
			$feedback .= "<a href='#"._XF_REG_PrOJECTFULLNAME."'>"._XF_REG_INVALIDFULLNAME."</a><br/>";
		}
		if (strlen($purpose) < 20)
			{
			$feedback .= "<a href='#"._XF_REG_PROJECTPURPOSE."'>"._XF_REG_DESCRIBEREGISTRATION."</a><br/>";
		}
		if (!$license)
			{
			$feedback .= "<a href='#"._XF_REG_LICENSEFORTHISPROJECT."'>"._XF_REG_NOLICENSECHOSEN."</a><br/>";
		}
		if ($license != "other" && $license_other)
			{
			$feedback .= "<a href='#"._XF_REG_LICENSEFORTHISPROJECT."'>"._XF_REG_CONFLICTLICENSE."</a><br/>";
		}
		if ($license == "other" && strlen($license_other) < 50)
			{
			$feedback .= "<a href='#"._XF_REG_LICENSEFORTHISPROJECT."'>"._XF_REG_DESCRIBELICENSE."</a><br/>";
		}
		if (strlen($description) < 10)
			{
			$feedback .= "<a href='#"._XF_REG_PUBLICDESCRIPTION."'>"._XF_REG_DESCRIBEPROJECT."</a><br/>";
		}
		if (!account_groupnamevalid($unix_name))
			{
			$feedback .= "<a href='#"._XF_REG_PROJECTUNIXNAME."'>"._XF_REG_INVALIDUNIXNAME."</a><br/>";
		}
		if ($icmsDB->getRowsNum($icmsDB->query("SELECT group_id FROM " . $icmsDB->prefix("xf_groups")." WHERE unix_group_name='$unix_name'")) > 0)
		{
			$feedback .= "<a href='#"._XF_REG_PROJECTUNIXNAME."'>"._XF_REG_UNIXGROUPALREADYTAKEN."<br/>";
		}
		if (isset($_POST['use_cvs']))
			{
			$use_cvs = true;
			$anon_cvs = isset($_POST['anon_cvs']) ? true :
			 false;
		}
		else
		{
			$use_cvs = false;
			$anon_cvs = false;
		}
		if ($feedback == '')
			{
			$group = new Group();
			$res = $group->create(
			$icmsUser,
				$full_name,
				$unix_name,
				$description,
				$license,
				$license_other,
				$purpose,
				false,
				$use_cvs,
				$anon_cvs);
			 
			if (!$res)
				{
				$feedback .= $group->getErrorMessage();
				$icmsTpl->assign("feedback", $feedback);
			}
			else
				{
				if ($icmsForge['manapprove'] == 0)
					{
					if (!$group->approve($icmsUser))
						{
						$feedback .= $group->getErrorMessage();
						$icmsTpl->assign("feedback", $feedback);
					}
					//}
					//else
					//{
					$url = "<p>"._XF_REG_ISACTIVATED.":<br />";
					$url .= "<a href='".ICMS_URL."/modules/xfmod/project/?" . $group->getUnixName()."'>".ICMS_URL."/modules/xfmod/project/?" . $group->getUnixName()."</a><br />";
					$url .= _XF_REG_PROJECTSTATS."</p><p>"._XF_REG_THANKYOU."</p>";
					$icmsTpl->assign("url", $url);
					 
					include("../../footer.php");
					exit();
				}
				else // ($icmsForge['manapprove'] == 1)
				{
					$url = "<p>"._XF_REG_ISSUBMITTED."</p><p>"._XF_REG_THANKYOU."</p>";
					$icmsTpl->assign("url", $url);
					 
					include("../../footer.php");
					exit();
				}
			}
		}
		else
		{
			// Strip slashes from database-ready strings.
			if (get_magic_quotes_gpc() || get_magic_quotes_runtime())
				{
				$purpose = stripslashes($purpose);
				$license_other = stripslashes($license_other);
				$description = stripslashes($description);
			}
		}
		$icmsTpl->assign("feedback", $feedback);
	}
	 
	function includefile ($filename)
	{
		global $icmsConfig;
		 
		if (file_exists(ICMS_ROOT_PATH."/modules/xfmod/language/" . $icmsConfig['language']."/".$filename) )
		{
			include(ICMS_ROOT_PATH."/modules/xfmod/language/".$icmsConfig['language']."/".$filename);
		}
		else
		{
			include(ICMS_ROOT_PATH."/modules/xfmod/language/english/".$filename);
		}
	}
	 
	$icmsTpl->assign("title", _XF_REG_STEP3);
	$icmsTpl->assign("profullname", _XF_REG_PrOJECTFULLNAME);
	$icmsTpl->assign("reg_step1", _XF_STEP1);
	$icmsTpl->assign("full_name", $ts->makeTboxData4Edit($full_name));
	$icmsTpl->assign("propurpose", _XF_REG_PROJECTPURPOSE);
	$icmsTpl->assign("reg_step2", _XF_STEP2);
	$icmsTpl->assign("purpose", $ts->makeTareaData4Edit($purpose));
	$icmsTpl->assign("reg_step3", _XF_STEP3);
	$icmsTpl->assign("license", _XF_REG_LICENSES);
	$icmsTpl->assign("licenseforthisproject", _XF_REG_LICENSEFORTHISPROJECT);
	$icmsTpl->assign("yourlicense", _XF_REG_YOURLICENSE);
	 
	// Create SELECT based on $LICENSE array in xfmod/include/vars.php.
	$select = '<select name="license">';
	$select .= '<option value="">(select)'."\r\n";
	while (list($k, $v) = each($LICENSE))
	{
		$select .= "<option value=\"$k\"";
		if ($license == $k)
		{
			$select .= " selected";
		}
		$select .= ">$v\n";
	}
	$select .= '</select>';
	 
	$icmsTpl->assign("selectlicense", $select);
	$icmsTpl->assign("reg_step3a", _XF_STEP3A);
	$icmsTpl->assign("license_other", $ts->makeTareaData4Edit($license_other));
	$icmsTpl->assign("publicdescription", _XF_REG_PUBLICDESCRIPTION);
	$icmsTpl->assign("reg_step4", _XF_STEP4);
	$icmsTpl->assign("description", $ts->makeTareaData4Edit($description));
	$icmsTpl->assign("prounixname", _XF_REG_PROJECTUNIXNAME);
	$icmsTpl->assign("reg_step5", _XF_STEP5);
	$icmsTpl->assign("reg_unixname", _XF_REG_UNIXNAME);
	$icmsTpl->assign("unix_name", $unix_name);
	 
	$icmsTpl->assign("repositoryinfo", _XF_REP_INFO);
	$icmsTpl->assign("repository_options", array('subversion' => 'Subversion', 'cvs' => 'CVS', 'none' => 'No Source Control'));
	$icmsTpl->assign("repository", $_POST['repository']);
	 
	if (isset($_POST['reload']))
	{
		$icmsTpl->assign("reload", true);
		 
		if ($anon_cvs)
		{
			$icmsTpl->assign("anon_cvs", 'checked');
		}
		else
		{
			$icmsTpl->assign("anon_cvs", '');
		}
	}
	else
	{
		$icmsTpl->assign("use_cvs", 'checked');
		$icmsTpl->assign("anon_cvs", 'checked');
	}
	 
	$icmsTpl->assign("back", _XF_REG_BACK);
	$icmsTpl->assign("finish", _XF_REG_FINISH);
	 
	include("../../footer.php");
?>