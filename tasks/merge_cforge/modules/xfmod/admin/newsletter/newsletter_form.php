<?php
	if (!eregi("admin.php", $_SERVER['PHP_SELF']))
	{
		die("Access Denied");
	}
	// check if the user is authorised
	if ($icmsUser->isAdmin($icmsModule->mid()))
	{
		include_once(ICMS_ROOT_PATH."/class/xoopsformloader.php");
		include_once(ICMS_ROOT_PATH."/class/icmslists.php");
		include_once("newsletter.php");
		 
		function show_pref()
		{
			global $icmsNewsLetterConfig;
			 
			// Elements
			$send_date_hour = array($icmsNewsLetterConfig['next_send_date_hour'] => $icmsNewsLetterConfig['next_send_date_hour'],
				"01" => "01",
				"02" => "02",
				"03" => "03",
				"04" => "04",
				"05" => "05",
				"06" => "06",
				"07" => "07",
				"08" => "08",
				"09" => "09",
				"10" => "10",
				"11" => "11",
				"12" => "12",
				"13" => "13",
				"14" => "14",
				"15" => "15",
				"16" => "16",
				"17" => "17",
				"18" => "18",
				"19" => "19",
				"20" => "20",
				"21" => "21",
				"22" => "22",
				"23" => "23",
				"24" => "24");
			$send_date_day = array($icmsNewsLetterConfig['next_send_date_day'] => $icmsNewsLetterConfig['next_send_date_day'],
				"01" => "01",
				"02" => "02",
				"03" => "03",
				"04" => "04",
				"05" => "05",
				"06" => "06",
				"07" => "07",
				"08" => "08",
				"09" => "09",
				"10" => "10",
				"11" => "11",
				"12" => "12",
				"13" => "13",
				"14" => "14",
				"15" => "15",
				"16" => "16",
				"17" => "17",
				"18" => "18",
				"19" => "19",
				"20" => "20",
				"21" => "21",
				"22" => "22",
				"23" => "23",
				"24" => "24",
				"25" => "25",
				"26" => "26",
				"27" => "27",
				"28" => "28",
				"29" => "29",
				"30" => "30",
				"31" => "31");
			$send_date_month = array($icmsNewsLetterConfig['next_send_date_month'] => $icmsNewsLetterConfig['next_send_date_month'],
				"01" => "01",
				"02" => "02",
				"03" => "03",
				"04" => "04",
				"05" => "05",
				"06" => "06",
				"07" => "07",
				"08" => "08",
				"09" => "09",
				"10" => "10",
				"11" => "11",
				"12" => "12");
			 
			$send_date_year = array($icmsNewsLetterConfig['next_send_date_year'] => $icmsNewsLetterConfig['next_send_date_year'],
				"".date("Y") => date("Y"),
				"".date("Y")+1 => date("Y")+1,
				"".date("Y")+2 => date("Y")+2);
			 
			$send_interval_days = array($icmsNewsLetterConfig['next_send_interval_days'] => $icmsNewsLetterConfig['next_send_interval_days'],
				"01" => "01",
				"02" => "02",
				"03" => "03",
				"04" => "04",
				"05" => "05",
				"06" => "06",
				"07" => "07",
				"14" => "14",
				"30" => "30",
				"60" => "60",
				"90" => "90");
			 
			$next_send_date_hour = new XoopsFormSelect("Hour", "next_send_date_hour", $icmsNewsLetterConfig['next_send_date_hour']);
			$next_send_date_hour->addOptionArray($send_date_hour);
			$next_send_date_day = new XoopsFormSelect("Day", "next_send_date_day", $icmsNewsLetterConfig['next_send_date_day']);
			$next_send_date_day->addOptionArray($send_date_day);
			$next_send_date_month = new XoopsFormSelect("Month", "next_send_date_month", $icmsNewsLetterConfig['next_send_date_month']);
			$next_send_date_month->addOptionArray($send_date_month);
			$next_send_date_year = new XoopsFormSelect("Year", "next_send_date_year", $icmsNewsLetterConfig['next_send_date_year']);
			$next_send_date_year->addOptionArray($send_date_year);
			$next_date = date("M d, Y h:i A", mktime($icmsNewsLetterConfig['next_send_date_hour'], 0, 0, $icmsNewsLetterConfig['next_send_date_month'], $icmsNewsLetterConfig['next_send_date_day'], $icmsNewsLetterConfig['next_send_date_year']));
			$next_send_date_tray = new XoopsFormElementTray("Next Send Date<BR>(".$next_date.")", "&nbsp;");
			$next_send_date_tray->addElement($next_send_date_hour);
			$next_send_date_tray->addElement($next_send_date_day);
			$next_send_date_tray->addElement($next_send_date_month);
			$next_send_date_tray->addElement($next_send_date_year);
			 
			$next_send_interval_days = new XoopsFormSelect("Interval(Days)", "next_send_interval_days", $icmsNewsLetterConfig['next_send_interval_days']);
			$next_send_interval_days->addOptionArray($send_interval_days);
			$autosend_active = new XoopsFormCheckBox("", "autosend_active", $icmsNewsLetterConfig['autosend_active']);
			$autosend_active->addOption(1, "Auto Send - Active?<BR>");
			$send_interval_tray = new XoopsFormElementTray("Auto Send Interval<BR>(executed by cronjob)", "&nbsp;");
			$send_interval_tray->addElement($next_send_interval_days);
			$send_interval_tray->addElement($autosend_active);
			 
			$subject = new XoopsFormText("Newsletter Subject", "subject", 30, 60, $icmsNewsLetterConfig['subject']);
			$subject_tray = new XoopsFormElementTray("Subject", "&nbsp;");
			$subject_tray->addElement($subject_body);
			 
			$header_active = new XoopsFormCheckBox("", "header_active", $icmsNewsLetterConfig['header_active']);
			$header_active->addOption(1, "Header - Active?<BR>");
			$header_body = new XoopsFormTextArea("<BR>", "header_body", $icmsNewsLetterConfig['header_body'], 10);
			$header_tray = new XoopsFormElementTray("Header", "&nbsp;");
			$header_tray->addElement($header_active);
			$header_tray->addElement($header_body);
			 
			$body_active = new XoopsFormCheckBox("", "body_active", $icmsNewsLetterConfig['body_active']);
			$body_active->addOption(1, "Body - Active?<BR>");
			$body_body = new XoopsFormTextArea("<BR>", "body_body", $icmsNewsLetterConfig['body_body'], 10);
			$body_tray = new XoopsFormElementTray("Body", "&nbsp;");
			$body_tray->addElement($body_active);
			$body_tray->addElement($body_body);
			 
			$topdownloads_active = new XoopsFormCheckBox("", "topdownloads_active", $icmsNewsLetterConfig['topdownloads_active']);
			$topdownloads_active->addOption(1, "Top Downloads - Active?<BR>");
			$topdownloads_tray = new XoopsFormElementTray("Top Downloads", "&nbsp;");
			$topdownloads_tray->addElement($topdownloads_active);
			 
			$topactive_projects_active = new XoopsFormCheckBox("", "topactive_projects_active", $icmsNewsLetterConfig['topactive_projects_active']);
			$topactive_projects_active->addOption(1, "Top Active Projects - Active?<BR>");
			$topactive_projects_tray = new XoopsFormElementTray("Top Active Projects", "&nbsp;");
			$topactive_projects_tray->addElement($topactive_projects_active);
			 
			$spotlight_user_active = new XoopsFormCheckBox("", "spotlight_user_active", $icmsNewsLetterConfig['spotlight_user_active']);
			$spotlight_user_active->addOption(1, "Spotlight User - Active?<BR>");
			$user_select = new XoopsFormSelect("User to Spotlight", "spotlight_user_id");
			$user_select->addOptionArray(icmsUser::getAllUsersList(array(), $orderby = "uname ASC"));
			$spotlight_user_tray = new XoopsFormElementTray("Spotlight User", "&nbsp;");
			$spotlight_user_tray->addElement($spotlight_user_active);
			$spotlight_user_tray->addElement($user_select);
			 
			$spotlight_community_active = new XoopsFormCheckBox("", "spotlight_community_active", $icmsNewsLetterConfig['spotlight_community_active']);
			$spotlight_community_active->addOption(1, "Spotlight Community - Active?<BR>");
			$community_select = new XoopsFormSelect("Community to Spotlight", "spotlight_community_id");
			$community_criteria = array("type = '2'");
			$community_select->addOptionArray(getAllGroupsList($community_criteria));
			$spotlight_community_tray = new XoopsFormElementTray("Spotlight Community", "&nbsp;");
			$spotlight_community_tray->addElement($spotlight_community_active);
			$spotlight_community_tray->addElement($community_select);
			 
			$spotlight_project_active = new XoopsFormCheckBox("", "spotlight_project_active", $icmsNewsLetterConfig['spotlight_project_active']);
			$spotlight_project_active->addOption(1, "Spotlight Project - Active?<BR>");
			$spotlight_project_tray = new XoopsFormElementTray("Spotlight Project", "&nbsp;");
			$project_select = new XoopsFormSelect("Project to Spotlight", "spotlight_project_id");
			$project_criteria = array("type = '1'");
			$project_select->addOptionArray(getAllGroupsList($project_criteria));
			$spotlight_project_tray = new XoopsFormElementTray("Spotlight Project", "&nbsp;");
			$spotlight_project_tray->addElement($spotlight_project_active);
			$spotlight_project_tray->addElement($project_select);
			 
			$newest_projects_active = new XoopsFormCheckBox("", "newest_projects_active", $icmsNewsLetterConfig['newest_projects_active']);
			$newest_projects_active->addOption(1, "Newest Projects - Active?<BR>");
			$newest_projects_tray = new XoopsFormElementTray("Newest Projects", "&nbsp;");
			$newest_projects_tray->addElement($newest_projects_active);
			 
			$newest_communities_active = new XoopsFormCheckBox("", "newest_communities_active", $icmsNewsLetterConfig['newest_communities_active']);
			$newest_communities_active->addOption(1, "Newest Communities - Active?<BR>");
			$newest_communities_tray = new XoopsFormElementTray("Newest Communities", "&nbsp;");
			$newest_communities_tray->addElement($newest_communities_active);
			 
			$footer_active = new XoopsFormCheckBox("", "footer_active", $icmsNewsLetterConfig['footer_active']);
			$footer_active->addOption(1, "Footer - Active?<BR>");
			$footer_body = new XoopsFormTextArea("<BR>", "footer_body", $icmsNewsLetterConfig['footer_body'], 10);
			$footer_tray = new XoopsFormElementTray("Footer<BR><BR><strong>Useful Tags:</strong><BR>{ICMS_URL} will print URL link", "&nbsp;");
			$footer_tray->addElement($footer_active);
			$footer_tray->addElement($footer_body);
			 
			$op_hidden = new XoopsFormHidden("op", "save");
			$submit_button = new XoopsFormButton("", "button", "Save/Preview", "submit");
			 
			// form construction
			$form = new XoopsThemeForm("Newsletter Configuration", "pref_form", "admin.php?fct=newsletter");
			$form->addElement($next_send_date_tray);
			$form->addElement($send_interval_tray);
			$form->addElement($subject);
			$form->addElement($header_tray);
			$form->addElement($body_tray);
			$form->addElement($topdownloads_tray);
			$form->addElement($topactive_projects_tray);
			$form->addElement($spotlight_user_tray);
			$form->addElement($spotlight_community_tray);
			$form->addElement($spotlight_project_tray);
			$form->addElement($newest_projects_tray);
			$form->addElement($newest_communities_tray);
			$form->addElement($footer_tray);
			 
			$form->addElement($op_hidden);
			$form->addElement($submit_button);
			$form->display();
		}
		 
	}
	else
	{
		 
	}
	 
	 
?>