#!/usr/bin/php -q
<?php

include "db.php";
include "header.php";

/*******************************************************************************
 *                                                                             *
 *                                                                             *
 *                                                                             *
 *                                                                             *
 *                                                                             *
 *                                                                             *
 *                                                                             *
 ******************************************************************************/

// Set debug = true to show debugging statements.
$debug = false;
//$debug = true;

// Set gentle = true to act like we are doing something without actually doing
// anything.
$gentle = false;
//$gentle = true;

//
// Look for inactive projects.
//
// Inactive projects have these three characteristics
// 1.  An activity percentile <= $min_activity_percentile
$min_activity_percentile = 0;
// 2.  No activity recorded in project history within the past
//     $history_activity_months months
$activity_months = 6;
// 3.  No recorded downloads of files for the project within the
//     past $activity_months months
//


function deactivate_project( $group_id, $timeframe )
{
  global $gentle, $debug, $dbprefix;
  if ( $gentle )
    {
      return;
    }
  if ( $debug )
    {
      echo "Deactivating project group id $group_id.\n";
    }
	// When we are going to deactivate a project,
	// we have to do three things:
	// 1.  Set the project state to N (inactive)
	// 2.  Set the project so it is not publicly visible
	// 3.  E-mail the project admin(s) and tell them
	//     which project was deactivated, what the
	//     criteria are for deactivation, and what
	//     they can do to activate it again.

	// Steps 1 and 2 - set state to N, is_public to 0
	$sql = "UPDATE ".$dbprefix."_xf_groups"
	. " SET status='N', is_public='0'"
	. " WHERE group_id='".$group_id."'";
  if ( $debug )
    {
      echo "Asking database $sql?\n";
    }
	$result = db_query($sql);

	// Step 3 - send e-mail to all admins
	$sql = "SELECT group_name, unix_group_name"
	. " FROM ".$dbprefix."_xf_groups"
	. " WHERE group_id='".$group_id."'";
	$result = db_query($sql);
	$row = db_fetch_array($result);
	$group_name = $row['group_name'];
	$unix_name = $row['unix_group_name'];
	$sql = "SELECT email"
	. " FROM ".$dbprefix."_users"
	. " WHERE level='5'";
	$result = db_query($sql);
	$row = db_fetch_array($result);
	$admin_email = $row['email'];
	
	$emailmsg = "The purpose of this e-mail is to notify you"
		. " that your project, ".$group_name.","
		. " has been changed to an inactive status."
		. "  You are receiving this e-mail because your"
		. " are shown to be an administrator of this project.<br>"
		. "This has been done simply because this project"
		. " does not appear to have had any activity within"
		. " the past ".$timeframe." months.  The criteria by"
		. " which this decision was made include:<ul>"
		. " <li>An activity rating of 0</li>"
		. " <li>No project activity recorded in project history"
		. " in the past ".$timeframe." months</li>"
		. " <li>No downloads of any project files in the"
		. " past ".$timeframe." months</li></ul>"
		. "You can easily reactivate your project by"
		. " returning to your project page"
		. " and clicking on the \"Reactivate This Project\""
		. " button.  The project will then become active again.<br>"
		. "You may e-mail the site administrator at "
	        . "<a href=\"mailto:".$admin_email."\">"
		. $admin_email . "</a> if you have any questions.";

	$sql2 = "SELECT u.email, u.name"
	. " FROM ".$dbprefix."_users u, ".$dbprefix."_xf_user_group ug"
	. " WHERE ug.group_id='".$group_id."' AND u.uid=ug.user_id";
	$result2 = db_query($sql2);
	while ( $row2 = db_fetch_array($result2) )
	{
	  if ( $debug )
	    {
	      echo "Sending mail to ".$row2['email']." from " .$admin_email."\n";
	    }
		mail( $row2['email'],
			"Important information about your project",
			$emailmsg,
			"Mime-Version: 1.0\r\n"
			."Content-type: text/html; charset=iso-8859-1\r\n"
			."From: administrator <".$admin_email.">\r\n"
			."To: ".$row2['name']." <".$row2['email'].">\r\n"
			."Reply-To: administrator <".$admin_email.">\r\n"
			."X-Priority: 3\r\n"
			."X-MSMail-Priority: High\r\n"
			."X-Mailer: PHP / ".phpversion()."\r\n" );
	}
}

if ( $debug )
{
  echo "(Running update_inactive_projects.php ".($gentle?"gently":"wickedly")
    . ".)\n\n";
  echo "Minimum activity percentile is $min_activity_percentile\n";
  echo "Months with no activity threshold is $activity_months\n";
}

$now = localtime(time(),true);
$history_expiry_date = 0;
$history_activity_years = 0;
if ( $activity_months == 0 )
{
  $history_expiry_date = time();
}
else
{
  if ( $activity_months >= 12 )
    {
      $history_activity_years = (int)($activity_months/12);
      $activity_months = $activity_months%12;
    }	
  if ( $activity_months-$now['tm_mon'] <= 0 )
    {
      $history_expiry_date = mktime( $now['tm_hour'], $now['tm_min'], $now['tm_sec'], 12-($activity_months-$now['tm_mon']), $now['tm_mday'], $now['tm_year']-($history_activity_years+1) );
    }
  else
    {
      $history_expiry_date = mktime( $now['tm_hour'], $now['tm_min'], $now['tm_sec'], $now['tm_mon']-$activity_months, $now['tm_mday'], $now['tm_year']-$history_activity_years );
    }
}

if ( $debug )
{
  echo "Now time is " . time() . ", expiry date is " . $history_expiry_date
    . "\n";
}

// Look for projects at or below the minimum activity percentile.  We want ones that aren't already inactive.
$sql = "SELECT pwm.group_id FROM ".$dbprefix."_xf_project_weekly_metric pwm, ".$dbprefix."_xf_groups g"
	." WHERE pwm.percentile <= '".$min_activity_percentile."'"
	." AND g.group_id=pwm.group_id"
	." AND g.status!='N'"
	." AND g.is_public='1'";
if ( $debug )
{
  echo "Asking database:  $sql?\n";
}
$result1 = db_query($sql);

// If there were any, we now have the group id of the project.
// We will use this information to ask further questions about
// whether the project is inactive.
while ( $row = db_fetch_array($result1) )
{
	// Now ask whether this project has any recent history entries.
	// What we are looking for any entry newer than our threshold date.
	$sql = "SELECT date FROM ".$dbprefix."_xf_group_history WHERE group_id='".$row['group_id']."' and date>'".$history_expiry_date."'";
  if ( $debug )
    {
      echo "Group id ".$row['group_id']." has percentile <= $min_activity_percentile.\n";
      echo "Asking database:  $sql?\n";
    }
	$result2 = db_query($sql);
	if ( 0 == db_numrows($result2) ) 
	{
	  if ( $debug )
	    {
	      echo "Group id ".$row['group_id']." has no recent history.\n";
	    }
		// No recent history entries found.
		// Now we will determine if there have been any 
		// recent downloads.  This is a bit more tricky.
		$base_sql = "SELECT dfa.month,dfa.day FROM ".$dbprefix."_xf_frs_dlstats_file_agg dfa, ".$dbprefix."_xf_frs_package p, ".$dbprefix."_xf_frs_release r, ".$dbprefix."_xf_frs_file f where p.group_id='".$row['group_id']."' and r.package_id=p.package_id and f.release_id=r.release_id and dfa.file_id=f.file_id and dfa.downloads>0";
		// Look first for downloads in the months
		// between now and $activity_months ago
		$first_month = $now['tm_mon'].$activity_months;
		if ( $first_month < 1 )
		{
			$first_month = 1;
		}
		$sql = $base_sql. " and dfa.month>'".$first_month."' and dfa.month<'".$now['tm_mon']."'";
		$result3 = db_query($sql);
		if ( 0 < db_numrows($result3) )
		{
			break;
		}
		// Now look for downloads within this month before today
		$sql = $base_sql . " and dfa.month='".$now['tm_mon']."' and dfa.day<='".$now['tm_mday']."'";
		$result3 = db_query($sql);
		if ( 0 < db_numrows($result3) )
		{
			break;
		}

		if ( ($now['tm_mon'] - $activity_months) < 1 )
		{
			$real_first_month =
				12 - ($now['tm_mon'] - $activity_months);
			if ( $real_first_month < 12 )
			{
				// Look for downloads after the real first
				// month last year
				$sql = $base_sql . " and dfa.month>'".$real_first_month."'";
				$result3 = db_query($sql);
				if ( 0 < db_numrows($result3) )
				{
					break;
				}
			}
			// Look for downloads in the real first month
			// after today
			$sql = $base_sql . " and dfa.month='".$real_first_month."' and dfa.day>='".$now['tm_mday']."'";
			$result3 = db_query($sql);
			if ( 0 < db_numrows($result3) )
			{
				break;
			}
		}
		else
		{
			// Now look for downloads in the first month
			// after today
			$sql = $base_sql . " and dfa.month='".$first_month."' and dfa.day>='".$now['tm_mday']."'";
			$result3 = db_query($sql);
			if ( 0 < db_numrows($result3) )
			{
				break;
			}
		}
		// If we get this far, there was no match anywhere.
		if ( $debug )
		  {
		    echo "No recent downloads.  Deactivate.\n";
		  }
		deactivate_project($row['group_id'],$activity_months);
	}
}

include "footer.php";
?>