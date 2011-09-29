<?php
	if ((!eregi("admin.php", $_SERVER['PHP_SELF']) && !eregi("newslettercron.php", $_SERVER['PHP_SELF'])))
	{
		die("Access Denied");
	}
	include_once("../../../mainfile.php");
	include_once(ICMS_ROOT_PATH."/class/xoopsformloader.php");
	include_once(ICMS_ROOT_PATH."/class/icmslists.php");
	 
	function save_pref($next_send_date_hour, $next_send_date_day, $next_send_date_month, $next_send_date_year, $next_send_interval_days, $autosend_active, $subject, $header_active, $header_body, $body_active, $body_body, $topdownloads_active, $topactive_projects_active, $spotlight_user_active, $spotlight_user_id, $spotlight_community_active, $spotlight_community_id, $spotlight_project_active, $spotlight_project_id, $newest_projects_active, $newest_communities_active, $footer_active, $footer_body)
	{
		global $icmsNewsLetterConfig, $HTTP_COOKIE_VARS;
		if (!xoopsfwrite() && eregi("newslettercron.php", $_SERVER['PHP_SELF']))
		{
			return;
		}
		 
		$myts = MyTextSanitizer::getInstance();
		$config = "<"."?php
			/*********************************************************************/
			 
			// Auto Send Configuration
			\$icmsNewsLetterConfig['next_send_date_hour'] = \"".$next_send_date_hour."\";
			\$icmsNewsLetterConfig['next_send_date_day'] = \"".$next_send_date_day."\";
			\$icmsNewsLetterConfig['next_send_date_month'] = \"".$next_send_date_month."\";
			\$icmsNewsLetterConfig['next_send_date_year'] = \"".$next_send_date_year."\";
			\$icmsNewsLetterConfig['next_send_interval_days'] = \"".$next_send_interval_days."\";
			\$icmsNewsLetterConfig['autosend_active'] = \"".$autosend_active."\";
			 
			 
			// Message Content Configuration
			\$icmsNewsLetterConfig['subject'] = \"".$subject."\";
			\$icmsNewsLetterConfig['header_active'] = \"".$header_active."\";
			\$icmsNewsLetterConfig['header_body'] = \"".$header_body."\";
			\$icmsNewsLetterConfig['body_active'] = \"".$body_active."\";
			\$icmsNewsLetterConfig['body_body'] = \"".$body_body."\";
			\$icmsNewsLetterConfig['topdownloads_active'] = \"".$topdownloads_active."\";
			\$icmsNewsLetterConfig['topactive_projects_active'] = \"".$topactive_projects_active."\";
			\$icmsNewsLetterConfig['spotlight_user_active'] = \"".$spotlight_user_active."\";
			\$icmsNewsLetterConfig['spotlight_user_id'] = \"".$spotlight_user_id."\";
			\$icmsNewsLetterConfig['spotlight_community_active'] = \"".$spotlight_community_active."\";
			\$icmsNewsLetterConfig['spotlight_community_id'] = \"".$spotlight_community_id."\";
			\$icmsNewsLetterConfig['spotlight_project_active'] = \"".$spotlight_project_active."\";
			\$icmsNewsLetterConfig['spotlight_project_id'] = \"".$spotlight_project_id."\";
			\$icmsNewsLetterConfig['newest_projects_active'] = \"".$newest_projects_active."\";
			\$icmsNewsLetterConfig['newest_communities_active'] = \"".$newest_communities_active."\";
			\$icmsNewsLetterConfig['footer_active'] = \"".$footer_active."\";
			\$icmsNewsLetterConfig['footer_body'] = \"".$footer_body."\";
			?".">";
		$file = fopen(ICMS_ROOT_PATH."/modules/xfmod/cache/newsletterconfig.php", "w");
		if (-1 != fwrite($file, $config))
		{
			// if the session cookie name has been changed, create session cookie with the new name
			if ($old_sessioncookie != $sessioncookie)
			{
				setcookie($sessioncookie, $HTTP_COOKIE_VARS[$icmsNewsLetterConfig['sessioncookie']], time()+360000, "/", "", 0);
				setcookie($icmsNewsLetterConfig['sessioncookie']);
			}
		}
		fclose($file);
		redirect_header("admin.php?fct=newsletter&op=preview", 2, _MD_AM_DBUPDATED);
		exit();
	}
	 
	function topdownloads()
	{
		global $icmsDB;
		$message = "";
		$limit = 10;
		$sql = "SELECT g.group_name FROM " ." ".$icmsDB->prefix("xf_frs_package")." AS p, " ." ".$icmsDB->prefix("xf_frs_release")." AS r, " ." ".$icmsDB->prefix("xf_frs_file")." AS f, " ." ".$icmsDB->prefix("xf_frs_dlstats_file_agg")." AS d, " ." ".$icmsDB->prefix("xf_groups")." AS g " ." WHERE p.package_id=r.package_id " ." AND r.release_id=f.release_id " ." AND f.file_id=d.file_id " ." AND p.group_id=g.group_id " ." ORDER BY d.downloads DESC LIMIT ".$limit;
		$result = $icmsDB->query($sql);
		$message = $message."<BR>Top Downloads<BR>";
		$message = $message."--------------------<BR>";
		while ($row = $icmsDB->fetchArray($result))
		{
			$message = $message."- ".$row['group_name']."<BR>";
		}
		$message = $message."--------------------<BR>";
		return $message;
	}
	 
	 
	 
	function topactive_projects()
	{
		global $icmsDB;
		$message = "";
		$limit = 10;
		$sql = "SELECT group_name,grp.group_id,metric.group_id FROM " .$icmsDB->prefix("xf_project_weekly_metric")." as metric " ."left join ".$icmsDB->prefix("xf_groups")." as grp " ."on grp.group_id=metric.group_id " ."ORDER BY ranking LIMIT ".$limit;
		$result = $icmsDB->query($sql);
		$message = $message."<BR>Top 10 Active Projects<BR>";
		$message = $message."--------------------<BR>";
		while ($row = $icmsDB->fetchArray($result))
		{
			$message = $message."- ".$row['group_name']."<BR>";
		}
		$message = $message."--------------------<BR>";
		return $message;
	}
	 
	function spotlight_user($user_id)
	{
		global $icmsDB;
		$message = "";
		$limit = 10;
		 
		$sql = "SELECT u.name name, u.uname uname, u.user_avatar, u.user_regdate, u.bio, u.user_intrest, ur.rank_title, up.resume " ."FROM ".$icmsDB->prefix("users")." u, ".$icmsDB->prefix("ranks")." ur, ".$icmsDB->prefix("xf_user_profile")." up " ."WHERE u.uid='".$user_id."' " ."AND ur.rank_id=u.rank " ."AND up.user_id=u.uid";
		echo "sql:<br>".$sql;
		$result = $icmsDB->query($sql);
		$message = $message."<BR>User Spotlight<BR>";
		$message = $message."--------------------<BR>";
		while ($row = $icmsDB->fetchArray($result))
		{
			$message = $message."User: ".$row['name']."<BR>" ;
			$message = $message."Name: ".$row['uname']."<BR>" ;
			//$message = $message."Avatar: ".$row['user_avatar']."<BR>" ;
			$message = $message."Biography: ".$row['bio']."<BR>" ;
			$message = $message."Interests: ".$row['user_intrest']."<BR>" ;
			$message = $message."Rank: ".$row['rank_title']."<BR>" ;
			$message = $message."Resume: ".$row['resume']."<BR>";
		}
		$message = $message."--------------------<BR>";
		return $message;
	}
	 
	function spotlight_community($community_id)
	{
		global $icmsDB;
		$message = "";
		$sql = "SELECT group_name, homepage, short_description " ."FROM ".$icmsDB->prefix("xf_groups")." " ."WHERE group_id='".$community_id."' " ."AND is_public='1' " ."AND status='A' " ."AND type='2'";
		$result = $icmsDB->query($sql);
		$message = $message."<BR>Community Spotlight<BR>";
		$message = $message."--------------------<BR>";
		 
		while ($row = $icmsDB->fetchArray($result))
		{
			$message = $message."Community Name: ".$row['group_name']."<BR>" ;
			$message = $message."Home Page: ".$row['homepage']."<BR>" ;
			$message = $message."Description: ".$row['short_description']."<BR>" ;
		}
		$message = $message."--------------------<BR>";
		return $message;
	}
	 
	 
	function spotlight_project($project_id)
	{
		global $icmsDB;
		 
		$sql = "SELECT group_name, homepage, short_description " ."FROM ".$icmsDB->prefix("xf_groups")." " ."WHERE group_id='".$project_id."' " ."AND is_public='1' " ."AND status='A' " ."AND type='1'";
		 
		$result = $icmsDB->query($sql);
		$message = $message."<BR>Project Spotlight<BR>";
		$message = $message."--------------------<BR>";
		 
		while ($row = $icmsDB->fetchArray($result))
		{
			$message = $message."Project Name: ".$row['group_name']."<BR>" ;
			$message = $message."Home Page: http://".$row['homepage']."<BR>" ;
			$message = $message."Description : ".$row['short_description']."<BR>" ;
		}
		$message = $message."--------------------<BR>";
		return $message;
	}
	 
	function newest_projects()
	{
		global $icmsDB;
		$message = "";
		$sql = "SELECT group_name, homepage, short_description " ."FROM ".$icmsDB->prefix("xf_groups")." " ."WHERE is_public='1' " ."AND status='A' " ."AND type='1' " ."ORDER BY register_time DESC LIMIT 10";
		$result = $icmsDB->query($sql);
		 
		$message = $message."<BR>Ten Newest Projects<br>";
		$message = $message."--------------------<BR>";
		 
		while ($row = $icmsDB->fetchArray($result))
		{
			$message = $message."- ".$row['group_name']."<BR>" ;
		}
		$message = $message."--------------------<BR>";
		return $message;
	}
	 
	function newest_communities()
	{
		global $icmsDB;
		$message = "";
		$sql = "SELECT group_name, homepage, short_description " ."FROM ".$icmsDB->prefix("xf_groups")." " ."WHERE is_public='1' " ."AND status='A' " ."AND type='2' " ."ORDER BY register_time DESC LIMIT 10";
		$result = $icmsDB->query($sql);
		 
		$message = $message."<BR>Ten Newest Communities<BR>";
		$message = $message."--------------------<BR>";
		 
		while ($row = $icmsDB->fetchArray($result))
		{
			$message = $message."- ".$row['group_name']."<BR>" ;
		}
		 
		$message = $message."--------------------<BR>";
		return $message;
	}
	 
	function getAllGroupsList($criteria = array(), $sort = "group_id", $order = "ASC")
	{
		global $icmsDB;
		$ret = array();
		$where_query = "";
		if (is_array($criteria) && count($criteria) > 0)
		{
			$where_query = " WHERE";
			foreach($criteria as $c)
			{
				$where_query .= " $c AND";
			}
			 
			$where_query = substr($where_query, 0, -4);
		}
		$sql = "SELECT group_id, group_name FROM ".$icmsDB->prefix("xf_groups")."$where_query ORDER BY $sort $order";
		$result = $icmsDB->query($sql);
		while ($myrow = $icmsDB->fetchArray($result))
		{
			$ret[$myrow['group_id']] = $myrow['group_name'];
		}
		return $ret;
	}
	 
	function getMessage()
	{
		global $icmsNewsLetterConfig;
		 
		$message = "";
		 
		if ($icmsNewsLetterConfig['header_active'] == "1")
		$message = $message.$icmsNewsLetterConfig['header_body']."\r\n";
		if ($icmsNewsLetterConfig['body_active'])
		$message = $message.$icmsNewsLetterConfig['body_body']."\r\n";
		if ($icmsNewsLetterConfig['topdownloads_active'])
		{
			$message = $message.topdownloads()."\r\n";
		}
		if ($icmsNewsLetterConfig['topactive_projects_active'])
		{
			$message = $message.topactive_projects()."\r\n";
		}
		if ($icmsNewsLetterConfig['spotlight_user_active'])
		{
			$message = $message.spotlight_user($icmsNewsLetterConfig['spotlight_user_id'])."\r\n";
		}
		if ($icmsNewsLetterConfig['spotlight_community_active'])
		{
			$message = $message.spotlight_community($icmsNewsLetterConfig['spotlight_community_id'])."\r\n";
		}
		if ($icmsNewsLetterConfig['spotlight_project_active'])
		{
			$message = $message.spotlight_project($icmsNewsLetterConfig['spotlight_project_id'])."\r\n";
		}
		if ($icmsNewsLetterConfig['newest_projects_active'])
		{
			$message = $message.newest_projects()."\r\n";
		}
		if ($icmsNewsLetterConfig['newest_communities_active'])
		{
			$message = $message.newest_communities()."\r\n";
		}
		 
		if ($icmsNewsLetterConfig['footer_active'])
		$message = $message.$icmsNewsLetterConfig['footer_body']."\r\n";
		 
		$message = str_replace('{ICMS_URL}', ICMS_URL, $message);
		 
		return $message;
		 
	}
	 
	function send_mail()
	{
		global $icmsNewsLetterConfig;
		 
		$to = "newsletter@".$_SERVER['HTTP_HOST'];
		$subject = $icmsNewsLetterConfig['subject'];
		$body = getMessage();
		 
		// For debug/tesing without sending
		// echo "<PRE>TO: ".$to."\r\nSUBJECT: ".$subject."\r\n".$body."</PRE>";
		 
		xoops_mail($to, $subject, $body);
	}
	 
?>