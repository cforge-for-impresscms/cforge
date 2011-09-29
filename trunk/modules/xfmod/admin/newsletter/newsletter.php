<?php
if ( (!eregi("admin.php", $_SERVER['PHP_SELF']) && !eregi("newslettercron.php", $_SERVER['PHP_SELF']))) {
		die ("Access Denied");
}
include_once("../../../mainfile.php");
	include_once(XOOPS_ROOT_PATH."/class/xoopsformloader.php");
	include_once(XOOPS_ROOT_PATH."/class/xoopslists.php");

function save_pref($next_send_date_hour, $next_send_date_day, $next_send_date_month, $next_send_date_year, $next_send_interval_days, $autosend_active, $subject, $header_active, $header_body, $body_active, $body_body, $topdownloads_active, $topactive_projects_active, $spotlight_user_active, $spotlight_user_id, $spotlight_community_active, $spotlight_community_id, $spotlight_project_active, $spotlight_project_id, $newest_projects_active, $newest_communities_active, $footer_active, $footer_body){
		global $xoopsNewsLetterConfig, $HTTP_COOKIE_VARS;
		if (!xoopsfwrite() && eregi("newslettercron.php", $_SERVER['PHP_SELF'])) {
			return;
		}

		$myts =& MyTextSanitizer::getInstance();
		$config = "<"."?php
/*********************************************************************/

// Auto Send Configuration
\$xoopsNewsLetterConfig['next_send_date_hour'] = \"".$next_send_date_hour."\";
\$xoopsNewsLetterConfig['next_send_date_day'] = \"".$next_send_date_day."\";
\$xoopsNewsLetterConfig['next_send_date_month'] = \"".$next_send_date_month."\";
\$xoopsNewsLetterConfig['next_send_date_year'] = \"".$next_send_date_year."\";
\$xoopsNewsLetterConfig['next_send_interval_days'] = \"".$next_send_interval_days."\";
\$xoopsNewsLetterConfig['autosend_active'] = \"".$autosend_active."\";


// Message Content Configuration
\$xoopsNewsLetterConfig['subject'] = \"".$subject."\";
\$xoopsNewsLetterConfig['header_active'] = \"".$header_active."\";
\$xoopsNewsLetterConfig['header_body'] = \"".$header_body."\";
\$xoopsNewsLetterConfig['body_active'] = \"".$body_active."\";
\$xoopsNewsLetterConfig['body_body'] = \"".$body_body."\";
\$xoopsNewsLetterConfig['topdownloads_active'] = \"".$topdownloads_active."\";
\$xoopsNewsLetterConfig['topactive_projects_active'] = \"".$topactive_projects_active."\";
\$xoopsNewsLetterConfig['spotlight_user_active'] = \"".$spotlight_user_active."\";
\$xoopsNewsLetterConfig['spotlight_user_id'] = \"".$spotlight_user_id."\";
\$xoopsNewsLetterConfig['spotlight_community_active'] = \"".$spotlight_community_active."\";
\$xoopsNewsLetterConfig['spotlight_community_id'] = \"".$spotlight_community_id."\";
\$xoopsNewsLetterConfig['spotlight_project_active'] = \"".$spotlight_project_active."\";
\$xoopsNewsLetterConfig['spotlight_project_id'] = \"".$spotlight_project_id."\";
\$xoopsNewsLetterConfig['newest_projects_active'] = \"".$newest_projects_active."\";
\$xoopsNewsLetterConfig['newest_communities_active'] = \"".$newest_communities_active."\";
\$xoopsNewsLetterConfig['footer_active'] = \"".$footer_active."\";
\$xoopsNewsLetterConfig['footer_body'] = \"".$footer_body."\";
?".">";
		$file=fopen(XOOPS_ROOT_PATH."/modules/xfmod/cache/newsletterconfig.php","w");
		if ( -1 != fwrite($file,$config) ) {
			// if the session cookie name has been changed, create session cookie with the new name
			if ( $old_sessioncookie != $sessioncookie ) {
				setcookie($sessioncookie, $HTTP_COOKIE_VARS[$xoopsNewsLetterConfig['sessioncookie']], time()+360000, "/",  "", 0);
				setcookie($xoopsNewsLetterConfig['sessioncookie']);
			}
		}
		fclose($file);
		redirect_header("admin.php?fct=newsletter&op=preview",2,_MD_AM_DBUPDATED);
		exit();
}

 function topdownloads (){
    global $xoopsDB;
    $message = "";
    $limit = 10;
    $sql = "SELECT g.group_name FROM "
            ." ".$xoopsDB->prefix("xf_frs_package")." AS p, "
            ." ".$xoopsDB->prefix("xf_frs_release")." AS r, "
            ." ".$xoopsDB->prefix("xf_frs_file")." AS f, "
            ." ".$xoopsDB->prefix("xf_frs_dlstats_file_agg")." AS d, "
            ." ".$xoopsDB->prefix("xf_groups")." AS g "
            ." WHERE p.package_id=r.package_id "
            ." AND r.release_id=f.release_id "
            ." AND f.file_id=d.file_id "
            ." AND p.group_id=g.group_id "
            ." ORDER BY d.downloads DESC LIMIT ".$limit;
    $result = $xoopsDB->query($sql);
    $message = $message."<BR>Top Downloads<BR>";
    $message = $message."--------------------<BR>";
    while ( $row = $xoopsDB->fetchArray($result) )
    {
       $message = $message."- ".$row['group_name']."<BR>";
    }
    $message = $message."--------------------<BR>";
    return $message;
}



function topactive_projects (){
    global $xoopsDB;
    $message = "";
    $limit = 10;
    $sql = "SELECT group_name,grp.group_id,metric.group_id FROM "
    	    .$xoopsDB->prefix("xf_project_weekly_metric")." as metric "
    	    ."left join ".$xoopsDB->prefix("xf_groups")." as grp "
    	    ."on grp.group_id=metric.group_id "
    	    ."ORDER BY ranking LIMIT ".$limit;
    $result = $xoopsDB->query($sql);
    $message = $message."<BR>Top 10 Active Projects<BR>";
    $message = $message."--------------------<BR>";
    while ( $row = $xoopsDB->fetchArray($result) )
    {
       $message = $message."- ".$row['group_name']."<BR>";
    }
    $message = $message."--------------------<BR>";
    return $message;
}

function spotlight_user ($user_id){
    global $xoopsDB;
    $message = "";
    $limit = 10;

    $sql = "SELECT u.name name, u.uname uname, u.user_avatar, u.user_regdate, u.bio, u.user_intrest, ur.rank_title, up.resume "
    	   ."FROM ".$xoopsDB->prefix("users")." u, ".$xoopsDB->prefix("ranks")." ur, ".$xoopsDB->prefix("xf_user_profile")." up "
    	   ."WHERE u.uid='".$user_id."' "
    	   ."AND ur.rank_id=u.rank "
    	   ."AND up.user_id=u.uid";
 echo "sql:<br>".$sql;
    $result = $xoopsDB->query($sql);
    $message = $message."<BR>User Spotlight<BR>";
    $message = $message."--------------------<BR>";
    while ( $row = $xoopsDB->fetchArray($result) )
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

function spotlight_community ($community_id){
    global $xoopsDB;
    $message = "";
    $sql = "SELECT group_name, homepage, short_description "
    	   ."FROM ".$xoopsDB->prefix("xf_groups")." "
    	   ."WHERE group_id='".$community_id."' "
    	   ."AND is_public='1' "
    	   ."AND status='A' "
    	   ."AND type='2'";
    $result = $xoopsDB->query($sql);
    $message = $message."<BR>Community Spotlight<BR>";
    $message = $message."--------------------<BR>";

    while ( $row = $xoopsDB->fetchArray($result) )
    {
       $message = $message."Community Name: ".$row['group_name']."<BR>" ;
       $message = $message."Home Page: ".$row['homepage']."<BR>" ;
       $message = $message."Description: ".$row['short_description']."<BR>" ;
    }
    $message = $message."--------------------<BR>";
    return $message;
}


function spotlight_project ($project_id){
    global $xoopsDB;

    $sql = "SELECT group_name, homepage, short_description "
    	   ."FROM ".$xoopsDB->prefix("xf_groups")." "
    	   ."WHERE group_id='".$project_id."' "
    	   ."AND is_public='1' "
    	   ."AND status='A' "
    	   ."AND type='1'";

    $result = $xoopsDB->query($sql);
    $message = $message."<BR>Project Spotlight<BR>";
    $message = $message."--------------------<BR>";

    while ( $row = $xoopsDB->fetchArray($result) )
    {
       $message = $message."Project Name: ".$row['group_name']."<BR>" ;
       $message = $message."Home Page: http://".$row['homepage']."<BR>" ;
       $message = $message."Description : ".$row['short_description']."<BR>" ;
    }
    $message = $message."--------------------<BR>";
    return $message;
}

function newest_projects (){
    global $xoopsDB;
    $message = "";
    $sql = "SELECT group_name, homepage, short_description "
    	   ."FROM ".$xoopsDB->prefix("xf_groups")." "
    	   ."WHERE is_public='1' "
    	   ."AND status='A' "
    	   ."AND type='1' "
	   ."ORDER BY register_time DESC LIMIT 10";
    $result = $xoopsDB->query($sql);

    $message = $message."<BR>Ten Newest Projects<br>";
    $message = $message."--------------------<BR>";

    while ( $row = $xoopsDB->fetchArray($result) )
    {
       $message = $message."- ".$row['group_name']."<BR>" ;
    }
    $message = $message."--------------------<BR>";
    return $message;
}

function newest_communities (){
    global $xoopsDB;
    $message = "";
    $sql = "SELECT group_name, homepage, short_description "
    	   ."FROM ".$xoopsDB->prefix("xf_groups")." "
    	   ."WHERE is_public='1' "
    	   ."AND status='A' "
    	   ."AND type='2' "
	   ."ORDER BY register_time DESC LIMIT 10";
    $result = $xoopsDB->query($sql);

    $message = $message."<BR>Ten Newest Communities<BR>";
    $message = $message."--------------------<BR>";

    while ( $row = $xoopsDB->fetchArray($result) )
    {
       $message = $message."- ".$row['group_name']."<BR>" ;
    }

    $message = $message."--------------------<BR>";
    return $message;
}

function getAllGroupsList($criteria=array(), $sort="group_id", $order="ASC")
{
	global $xoopsDB;
	$ret = array();
	$where_query = "";
	if ( is_array($criteria) && count($criteria) > 0 )
	{
	   $where_query = " WHERE";
	   foreach ( $criteria as $c )
	   {
	      $where_query .= " $c AND";
	   }

	   $where_query = substr($where_query, 0, -4);
	}
	$sql = "SELECT group_id, group_name FROM ".$xoopsDB->prefix("xf_groups")."$where_query ORDER BY $sort $order";
	$result = $xoopsDB->query($sql);
	while ( $myrow = $xoopsDB->fetchArray($result) ) {
		$ret[$myrow['group_id']] = $myrow['group_name'];
	}
	return $ret;
}

  function getMessage()
  {
		global $xoopsNewsLetterConfig;

		$message = "";

     		if ($xoopsNewsLetterConfig['header_active'] == "1")
	 	  $message = $message.$xoopsNewsLetterConfig['header_body']."\n";
     		if ($xoopsNewsLetterConfig['body_active'])
       		  $message = $message.$xoopsNewsLetterConfig['body_body']."\n\n";
     		if ($xoopsNewsLetterConfig['topdownloads_active'])
       		{
       		  $message = $message.topdownloads()."\n";
   		}
		if ($xoopsNewsLetterConfig['topactive_projects_active'])
       		{
	          $message = $message.topactive_projects()."\n";
 		}
		if ($xoopsNewsLetterConfig['spotlight_user_active'])
       		{
	          $message = $message.spotlight_user($xoopsNewsLetterConfig['spotlight_user_id'])."\n";
 		}
		if ($xoopsNewsLetterConfig['spotlight_community_active'])
       		{
	          $message = $message.spotlight_community($xoopsNewsLetterConfig['spotlight_community_id'])."\n";
 		}
		if ($xoopsNewsLetterConfig['spotlight_project_active'])
       		{
	          $message = $message.spotlight_project($xoopsNewsLetterConfig['spotlight_project_id'])."\n";
 		}
		if ($xoopsNewsLetterConfig['newest_projects_active'])
       		{
	          $message = $message.newest_projects()."\n";
 		}
 		if ($xoopsNewsLetterConfig['newest_communities_active'])
       		{
	          $message = $message.newest_communities()."\n";
 		}

		if ($xoopsNewsLetterConfig['footer_active'])
       		  $message = $message.$xoopsNewsLetterConfig['footer_body']."\n\n";

		$message = str_replace('{XOOPS_URL}',XOOPS_URL, $message);

		return $message;

  }

  function send_mail()
  {
    global $xoopsNewsLetterConfig;

    $to = "newsletter@".$_SERVER['HTTP_HOST'];
    $subject = $xoopsNewsLetterConfig['subject'];
    $body = getMessage();

    // For debug/tesing without sending
    // echo "<PRE>TO: ".$to."\nSUBJECT: ".$subject."\n\n".$body."</PRE>";

     xoops_mail( $to, $subject, $body );
  }

  ?>