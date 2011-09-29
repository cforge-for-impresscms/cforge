<?php
if ( !eregi("admin.php", $_SERVER['PHP_SELF']) ) { die ("Access Denied"); }

if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) {

  include_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
  include_once(XOOPS_ROOT_PATH."/class/mail/phpmailer/class.phpmailer.php");
  include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");

	function show_pref()
	{
		global $xoopsForge;
		$manapprove_radio = new XoopsFormRadioYN("Manual Approve Project Registration?", "manapprove", $xoopsForge['manapprove'], "Yes", "No");
		$devsurvey_select = new XoopsFormSelect("Survey to show on personal page", "devsurvey", $xoopsForge['devsurvey']);
		$surveys_list = getSurveysFromGroup(1, 1);
		$surveys_list[100] = _XF_G_NONE;		
		$devsurvey_select->addOptionArray($surveys_list);
		$noreply_text = new XoopsFormText("No-Reply Mail Address", "noreply", 50, 100, $xoopsForge['noreply']);
		$defaultproject_text = new XoopsFormText("ID of project to show when default module is \"xfmod\" (use \"0\" if none)", "defaultproject", 12, 20, $xoopsForge['defaultproject']);
		$sysnews_text = new XoopsFormText("Group ID of Side-wide News (Leave it at 2 if you don't know)", "sysnews", 12, 20, $xoopsForge['sysnews']);
		$virusscan_radio = new XoopsFormRadioYN("Scan file uploads for viruses?","virusscan",$xoopsForge['virusscan'], "Yes", "No");
		$snippetowner_radio = new XoopsFormRadioYN("Allow users to add snippets they do not own to thier snippet packages?","snippetowner",$xoopsForge['snippetowner'], "Yes", "No");
		$ftp_server = new XoopsFormText("FTP Server", "ftp_server", 50, 100, $xoopsForge['ftp_server']);
		$ftp_prefix = new XoopsFormText("Relative path to the FTP directory(ie. pub/forge)", "ftp_prefix", 50, 100, $xoopsForge['ftp_prefix']);
		$ftp_path = new XoopsFormText("Absolute path to the FTP directory (ie. /var/ftp/pub/forge)", "ftp_path", 50, 100, $xoopsForge['ftp_path']);
		$validate_email = new XoopsFormSelect("Email Validation", "validate_email", $xoopsForge['validate_email']);
		$validate_email->addOptionArray(array("None","Basic","MX Check"));

		$forum_type = new XoopsFormSelect("Which type of forum will you use?", "forum_type", $xoopsForge['forum_type']);
		$forum_type->addOptionArray(array("newsportal" => "NNTP","forum"=>"SQL"));
		
		$nntp_server = new XoopsFormText("NNTP Server", "nntp_server", 50, 100, $xoopsForge['nntp_server']);
		$nntp_base = new XoopsFormText("NNTP Base Forum Name", "nntp_base", 50, 100, $xoopsForge['nntp_base']);
		for($i=1;$i<15;$i++) $numbers[$i] = $i;
		$max_forums = new XoopsformSelect("Max Number of Forums Per Project", "max_forums", $xoopsForge['max_forums']);
		$max_forums->addOptionArray($numbers);
		$privkey_path = new XoopsFormText("Path to the Private Key for Creating News Groups", "privkey_path", 50, 100, $xoopsForge['privkey_path']);
		$openssl_path = new XoopsFormText("Path to the openssl binary", "openssl_path", 50, 100, $xoopsForge['openssl_path']);
		$uuencode_path = new XoopsFormText("Path to the uuencode binary", "uuencode_path", 50, 100, $xoopsForge['uuencode_path']);
		for($i=1;$i<15;$i++) $numbers[$i] = $i;
		$max_maillists = new XoopsformSelect("Max Number of Mailing Lists Per Project", "max_maillists", $xoopsForge['max_maillists']);
		$max_maillists->addOptionArray($numbers);
		
		$op_hidden = new XoopsFormHidden("op","save");
		$submit_button = new XoopsFormButton("", "button", "Save", "submit");
		
		$form = new XoopsThemeForm("XoopsForge Preferences", "pref_form", "admin.php?fct=config");
		$form->addElement($manapprove_radio);
		$form->addElement($devsurvey_select);
		$form->addElement($noreply_text);
		$form->addElement($defaultproject_text);
		$form->addElement($usemailer_select);
		$form->addElement($parammail1_text);
		$form->addElement($parammail2_text);
		$form->addElement($sysnews_text);
		$form->addElement($virusscan_radio);
		$form->addElement($snippetowner_radio);
		$form->addElement($ftp_server);
		$form->addElement($ftp_prefix);
		$form->addElement($ftp_path);
		$form->addElement($validate_email);
		$form->addElement($forum_type);
		$form->addElement($nntp_server);
		$form->addElement($nntp_base);
		$form->addElement($max_forums);
		$form->addElement($privkey_path);
		$form->addElement($openssl_path);
		$form->addElement($uuencode_path);
		$form->addElement($max_maillists);
		
		$form->addElement($op_hidden);
		$form->addElement($submit_button);
		/* Make sure that if you add a new item here that you also add it to the sql file for the install.
			You will then need to either reinstall the module or insert the new row into the database manually.
		*/
		$form->display();
	}

	function save_pref(){
		//ignore op and button values
		if (!xoopsfwrite())
		{
			return;
		}

		$myts =& MyTextSanitizer::getInstance();
		$error = "";

		if(!is_numeric($_POST['defaultproject']))
		{
			$error .= "<h4>Invalid Default Project ID</h4>";
		}

		if(!is_numeric($_POST['sysnews']))
		{
			$error .= "<h4>Invalid News Project ID</h4>";
		}

		if ( empty($_POST['noreply']) )
		{
		 	$error .= "<h4>Invalid No-Reply mail address</h4>";
		}

		if($error != "")
		{
			site_admin_header();
			echo $error;
			site_admin_footer();
			exit();
		}
		
		global $xoopsDB;
		foreach ( $_POST as $name => $value ){
			if($name != "op" && $name != "button")
				$xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_config")." SET value='$value' WHERE name='$name'");
		}

//		site_admin_header();
//		echo "Configuration Saved";
//		site_admin_footer();
//		exit;

		redirect_header("admin.php?fct=config",2,"Configuration Saved");
		exit;
	}

}
else
{
 	echo "Access Denied";
}

?>