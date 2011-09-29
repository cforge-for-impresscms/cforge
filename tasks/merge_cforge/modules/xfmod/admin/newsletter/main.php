<?php
	if (!eregi("admin.php", $_SERVER['PHP_SELF']))
	{
		die("Access Denied");
	}
	$op = "default";
	include(ICMS_ROOT_PATH."/modules/xfmod/admin/newsletter/newsletter_form.php");
	include(ICMS_ROOT_PATH."/modules/xfmod/cache/newsletterconfig.php");
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	// if GET/POST is set, change $op
	if (isset($HTTP_POST_VARS['op']))
	{
		$op = $HTTP_POST_VARS['op'];
	}
	elseif(isset($HTTP_GET_VARS['op']))
	{
		$op = $HTTP_GET_VARS['op'];
	}
	 
	if ($icmsUser->isAdmin($icmsModule->mid()))
	{
		switch($op)
		{
			case "save":
			if (!is_writable(ICMS_ROOT_PATH."/modules/xfmod/cache/newsletterconfig.php"))
			{
				// attempt to chmod 666
				if (!chmod(ICMS_ROOT_PATH."/modules/xfmod/cache/newsletterconfig.php", 0666))
				{
					xoops_cp_header();
					printf(_MUSTWABLE, "<strong>".ICMS_ROOT_PATH."/modules/xfmod/cache/newsletterconfig.php</strong>");
					xoops_cp_footer();
					exit();
				}
			}
			save_pref($next_send_date_hour, $next_send_date_day, $next_send_date_month, $next_send_date_year, $next_send_interval_days, $autosend_active, $subject, $header_active, $header_body, $body_active, $body_body, $topdownloads_active, $topactive_projects_active, $spotlight_user_active, $spotlight_user_id, $spotlight_community_active, $spotlight_community_id, $spotlight_project_active, $spotlight_project_id, $newest_projects_active, $newest_communities_active, $footer_active, $footer_body);
			break;
			case "preview":
			xoops_cp_header();
			echo "Saved Newsletter Preferences Preview<BR><BR>";
			echo "<pre>".getMessage()."</pre>";
			echo "<br />";
			 
			$op_hidden = new XoopsFormHidden("op", "send");
			$submit_button = new XoopsFormButton("", "button", "Send Newsletter", "submit");
			 
			// form construction
			$form = new XoopsThemeForm("", "send_form", "admin.php?fct=newsletter");
			$form->addElement($op_hidden);
			$form->addElement($submit_button);
			$form->display();
			 
			xoops_cp_footer();
			break;
			case "send":
			xoops_cp_header();
			echo "The following newsletter has been sent<BR>--------------------------------<BR><BR>";
			send_mail();
			xoops_cp_footer();
			break;
			case "default":
			default:
			xoops_cp_header();
			show_pref();
			echo "<br />";
			xoops_cp_footer();
			break;
		}
	}
?>