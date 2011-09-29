<?php
/**
  *
  * Project Registration: Project Information.
  *
  * This page is used to request data required for project registration:
  *	 o Project Public Name
  *	 o Project Registartion Purpose
  *	 o Project License
  *	 o Project Public Description
  *	 o Project Unix Name
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

$type="project";
$utype="Project";

require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vars.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/account.php");
include_once ("../xfmod/language/english/register_step_1.php");
include_once ("../xfmod/language/english/register_step_2.php");
include_once ("../xfmod/language/english/register_step_3.php");
include_once ("../xfmod/language/english/register_step_3a.php");
include_once ("../xfmod/language/english/register_step_4.php");
include_once ("../xfmod/language/english/register_step_5.php");
$xoopsOption['template_main'] = 'xfnewproject_projectinfo.html';

if (!$xoopsUser)
{
	redirect_header($GLOBALS["HTTP_REFERER"],2,_NOPERM);
	exit;
}

$submit = $_POST['submit'];

$metaTitle=": "._XF_REG_STEP3;
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
	if ($xoopsDB->getRowsNum($xoopsDB->query("SELECT group_id FROM " .
		$xoopsDB->prefix("xf_groups")." WHERE unix_group_name='$unix_name'")) > 0)
	{
		$feedback .= "<a href='#"._XF_REG_PROJECTUNIXNAME."'>"._XF_REG_UNIXGROUPALREADYTAKEN."<br/>";
	}
	if (isset($_POST['use_cvs']))
	{
		$use_cvs = true;
		$anon_cvs = isset($_POST['anon_cvs']) ? true : false;
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
			$xoopsUser,
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
			$xoopsTpl->assign("feedback",$feedback);
		}
		else
		{
			if ($xoopsForge['manapprove'] == 0)
			{
				if (!$group->approve($xoopsUser))
				{
					$feedback .= $group->getErrorMessage();
					$xoopsTpl->assign("feedback",$feedback);
				}
  	    		//}
			//else
			//{
            		        $url = "<p>"._XF_REG_ISACTIVATED.":<br />";
  			    	$url .= "<a href='".XOOPS_URL."/modules/xfmod/project/?" .
						$group->getUnixName()."'>".XOOPS_URL."/modules/xfmod/project/?" .
						$group->getUnixName()."</a><br />";
				$url .= _XF_REG_PROJECTSTATS."</p><p>"._XF_REG_THANKYOU."</p>";
				$xoopsTpl->assign("url",$url);

			    	include("../../footer.php");
			    	exit();
			}
			else // ($xoopsForge['manapprove'] == 1)
			{
				$url = "<p>"._XF_REG_ISSUBMITTED."</p><p>"._XF_REG_THANKYOU."</p>";
				$xoopsTpl->assign("url",$url);	
				
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
	$xoopsTpl->assign("feedback",$feedback);
}

function includefile ($filename)
{
	global $xoopsConfig;

	if ( file_exists(XOOPS_ROOT_PATH."/modules/xfmod/language/" .
		$xoopsConfig['language']."/".$filename) )
	{
    	include(XOOPS_ROOT_PATH."/modules/xfmod/language/".$xoopsConfig['language']."/".$filename);
	}
	else
	{
    	include(XOOPS_ROOT_PATH."/modules/xfmod/language/english/".$filename);
	}
}

$xoopsTpl->assign("title",_XF_REG_STEP3);
$xoopsTpl->assign("profullname",_XF_REG_PrOJECTFULLNAME);
$xoopsTpl->assign("reg_step1",_XF_STEP1);
$xoopsTpl->assign("full_name",$ts->makeTboxData4Edit($full_name));
$xoopsTpl->assign("propurpose",_XF_REG_PROJECTPURPOSE);
$xoopsTpl->assign("reg_step2",_XF_STEP2);
$xoopsTpl->assign("purpose",$ts->makeTareaData4Edit($purpose));
$xoopsTpl->assign("reg_step3",_XF_STEP3);
$xoopsTpl->assign("license",_XF_REG_LICENSES);
$xoopsTpl->assign("licenseforthisproject",_XF_REG_LICENSEFORTHISPROJECT);
$xoopsTpl->assign("yourlicense",_XF_REG_YOURLICENSE);

// Create SELECT based on $LICENSE array in xfmod/include/vars.php.
$select = '<select name="license">';
$select .= '<option value="">(select)'."\n";
while (list($k,$v) = each($LICENSE)) {
	$select .= "<option value=\"$k\"";
	if ($license == $k) {
		$select .= " selected";
	}
	$select .= ">$v\n";
}
$select .= '</select>';

$xoopsTpl->assign("selectlicense",$select);
$xoopsTpl->assign("reg_step3a",_XF_STEP3A);
$xoopsTpl->assign("license_other",$ts->makeTareaData4Edit($license_other));
$xoopsTpl->assign("publicdescription",_XF_REG_PUBLICDESCRIPTION);
$xoopsTpl->assign("reg_step4",_XF_STEP4);
$xoopsTpl->assign("description",$ts->makeTareaData4Edit($description));
$xoopsTpl->assign("prounixname",_XF_REG_PROJECTUNIXNAME);
$xoopsTpl->assign("reg_step5",_XF_STEP5);
$xoopsTpl->assign("reg_unixname",_XF_REG_UNIXNAME);
$xoopsTpl->assign("unix_name",$unix_name);

$xoopsTpl->assign("repositoryinfo",_XF_REP_INFO);
$xoopsTpl->assign("repository_options", array('subversion' => 'Subversion', 'cvs' => 'CVS', 'none' => 'No Source Control'));
$xoopsTpl->assign("repository", $_POST['repository']);

if(isset($_POST['reload'])) {
	$xoopsTpl->assign("reload",true);
	
	if($anon_cvs) {
		$xoopsTpl->assign("anon_cvs",'checked');
	}
	else {
		$xoopsTpl->assign("anon_cvs",'');
	}
}
else {
	$xoopsTpl->assign("use_cvs",'checked');
	$xoopsTpl->assign("anon_cvs",'checked');
}

$xoopsTpl->assign("back",_XF_REG_BACK);
$xoopsTpl->assign("finish",_XF_REG_FINISH);

include("../../footer.php");
?>

