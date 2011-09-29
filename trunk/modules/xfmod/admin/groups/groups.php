<?php
if ( !eregi("admin.php", $_SERVER['PHP_SELF']) ) { die ("Access Denied"); }
if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) {

require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vars.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/account.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/canned_responses.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactTypes.class");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");
function GroupsMain()
{
  global $xoopsForge;

  site_admin_header();

	$abc_array = array('A','B','C','D','E','F','G','H','I','J','K','L','M','N','O','P','Q','R','S','T','U','V','W','X','Y','Z','0','1','2','3','4','5','6','7','8','9');

  echo "<li>Display Groups Beginning with: ";
	for ($i = 0; $i < count($abc_array); $i++) {
		echo "<a href='admin.php?fct=groups&op=GroupList&search=".$abc_array[$i]."%'>".$abc_array[$i]."</a>|";
	}

	echo "<form name='gpsrch' action='admin.php' method='POST'>"
	    ."Search <i>(groupid, group unix name, full name)</i>: "
			."<input type='text' name='search'>"
			."<input type='hidden' name='fct' value='groups'>"
			."<input type='hidden' name='op' value='GroupList'>"
			."<input type='hidden' name='substr' value='1'>"
			."<input type='submit' value=' Get '>"
			."</form>"
			."<p>"
			."<LI>Groups with <a href='admin.php?fct=groups&op=GroupApprove'><B>P</B> (pending) Status</A> <i>(New Project Approval)</i>";

	if ($xoopsForge['manapprove'] != 1) {
	  echo "<b style='color:#FF0000;'>Manual Project Approval is disabled</b>";
	}
	echo "<LI>Groups with <a href='admin.php?fct=groups&op=GroupList&search=%&status=I'><B>I</B> (incomplete) Status</A>"
	    ."<LI>Groups with <a href='admin.php?fct=groups&op=GroupList&search=%&status=D'><B>D</B> (deleted) Status</A>"
			."<LI><a href='admin.php?fct=groups&op=GroupList&search=%&is_public=0'>Private Groups </A>"
			."<LI><a href='admin.php?fct=groups&op=ManageResponses'>Manage Pre-defined responses</A>"
			."</ul>";

  site_admin_footer();
}

function GroupList ($search, $substr, $status, $is_public)
{
  global $xoopsDB;
  global $sys_datefmt;
  
  site_admin_header();

	echo "<H4>Admin Search Results</H4>";

	/*
	  Main code
  */

  if ($search == "") {
	  echo "<H4>Refusing to display whole DB</H4>"
		    ."That would display whole DB.";

		site_admin_footer();
		exit;
  }

	if ($substr) {
	  $search = "%$search%";
	}

	$crit_sql = "";
	$crit_desc = "";
	if ($status) {
		$crit_sql  .= " AND status='$status'";
		$crit_desc .= " status=$status";
	}

	if (isset($is_public)) {
		$crit_sql  .= " AND is_public='$is_public'";
		$crit_desc .= " is_public=$is_public";
	}

	$result = $xoopsDB->query("SELECT DISTINCT * "
	                     ."FROM ".$xoopsDB->prefix("xf_groups")." "
											 ."WHERE (group_id LIKE '$search' "
											 ."OR unix_group_name LIKE '$search' "
											 ."OR group_name LIKE '$search') "
											 ."$crit_sql");

	if ($crit_desc) {
		$crit_desc = "($crit_desc )";
	}

	echo "<p><b>Group search with criteria '<i>".$search."</i>' ".$crit_desc.": "
	    .$xoopsDB->getRowsNum($result)." matches.</b></p>";

	if ($xoopsDB->getRowsNum($result) < 1) {
		echo $xoopsDB->error();
	} else {

	 	echo "<TABLE cellspacing='1' cellpadding='5' width='100%' border='0'>"
		    ."<TR class='head'>"
        ."<TD align='center'><b>ID</b></td>"
        ."<td align='center'><b>Unix Name</b></td>"
        ."<TD align='center'><b>Full Name</b></td>"
        ."<TD align='center'><b>Registered</b></td>"
        ."<TD align='center'><b>Status</b></td>"
        ."</TR>";

		$i = 0;
		while ($row = $xoopsDB->fetchArray($result)) {

			$extra_status = "";
			if (!$row['is_public']) {
				$extra_status = "/PRV";
			}

			echo "<tr class='".($i++%2!=0?"odd":"even")."'>"
			    ."<td><a href='admin.php?fct=groups&op=GroupEdit&group_id=".$row['group_id']."'>".$row['group_id']."</a></td>"
					."<td>".format_name($row['unix_group_name'], $row['status'])."</td>"
					."<td>".$row['group_name']."</td>"
					."<td>".date($sys_datefmt, $row['register_time'])."</td>"
					."<td align='center'>".format_name($row['status'].$extra_status, $row['status'])."</td>"
					."</tr>";
		}
		echo "</table>";
	}

  site_admin_footer();
}

function GroupApprove ($action, $list_of_groups, $response_text, $response_title, $add_to_can, $response_id)
{
	global $xoopsUser, $xoopsDB;
	$LIMIT = 50;

	if ($action == 'activate') {
		$groups = explode(',', $list_of_groups);
		array_walk($groups, 'activate_group');
	} else if ($action == 'delete') {
		$group =& group_get_object($_POST['group_id']);

		if (!$group->setStatus($xoopsUser, 'D')) {
			site_admin_header();
			  echo "Error during group rejection<br>".$group->getErrorMessage();
				site_admin_footer();
			  exit;
		}
		$group->addHistory('rejected', 'x');
		// Determine whether to send a canned or custom rejection letter and send it
		if( $response_id == 100 ) {
			$group->sendRejectionEmail(0, $response_text);
			if( $add_to_can ) {
				add_canned_response($response_title, $response_text);
			}
		} else {
			$group->sendRejectionEmail($response_id);
		}
	}

	// get current information
	$res_grp = $xoopsDB->queryF("SELECT * FROM ".$xoopsDB->prefix("xf_groups")." WHERE status='P'", $LIMIT);
	$rows = $xoopsDB->getRowsNum($res_grp);

	site_admin_header();

	if ($rows < 1) {
	  echo "<H4>None Found</H4>"
		    ."<p>No Pending Projects to Approve</p>";

	  site_admin_footer();
	  exit;
  }

  if ($rows > $LIMIT) {
	  echo "<p>Pending projects: $LIMIT+ ($LIMIT shown)</p>";
  } else {
	  echo "<p>Pending projects: $rows</p>";
  }

	while ($row_grp = $xoopsDB->fetchArray($res_grp)) {
	  ?>
	  <H4><?php echo $row_grp['group_name']; ?></H4>

	  <p>
	  [ <A href="admin.php?fct=groups&op=GroupEdit&group_id=<?php echo $row_grp['group_id']; ?>">Edit Project Details</A> |
	    <A href="<?php echo XOOPS_URL; ?>/modules/xfmod/project/admin/?group_id=<?php echo $row_grp['group_id']; ?>">Project Admin</A> |
	    <A href="admin.php?fct=groups&op=ListUsers&group_id=<?php echo $row_grp['group_id']; ?>">View/Edit Project Members</A> ]
	  <p>
	  <table><tr><td>
	  <FORM action="admin.php" method="POST">
	  <INPUT TYPE="HIDDEN" NAME="fct" VALUE="groups">
	  <INPUT TYPE="HIDDEN" NAME="op" VALUE="GroupApprove">
	  <INPUT TYPE="HIDDEN" NAME="action" VALUE="activate">
	  <INPUT TYPE="HIDDEN" NAME="list_of_groups" VALUE="<?php echo $row_grp['group_id']; ?>">
	  <INPUT type="submit" name="submit" value="Approve">
	  </FORM>
	  </td></tr>
	  <tr><td>
	  <FORM action="admin.php" method="POST">
	  <INPUT TYPE="HIDDEN" NAME="fct" VALUE="groups">
	  <INPUT TYPE="HIDDEN" NAME="op" VALUE="GroupApprove">
	  <INPUT TYPE="HIDDEN" NAME="action" VALUE="delete">
	  <INPUT TYPE="HIDDEN" NAME="group_id" VALUE="<?php print $row_grp['group_id']; ?>">
	  Canned responses<br>
	  <?php echo get_canned_responses(); ?> <a href="admin.php?fct=groups&op=ManageResponses">(manage responses)</a>
	  <br><br>
	  Custom response title and text<br>
	  <input type="text" name="response_title" size="30" max="25"><br>
	  <textarea name="response_text" rows="10" cols="50"></textarea>
	  <input type="checkbox" name="add_to_can" value="yes">Add this custom response to canned responses
	  <br>
	  <INPUT type="submit" name="submit" value="Reject">
	  </FORM>
	  </td></tr>
	  </table>

	  <P>
	  <B>License: <?php echo $row_grp['license']; ?></B>

	  <br>
	  <?php

	  // ########################## OTHER INFO

	  echo "<P><B>Other Information</B>";
	  echo "<P>Unix Group Name: ".$row_grp['unix_group_name'];

	  echo "<P>Submitted Description: <blockquote>".$row_grp['register_purpose']."</blockquote>";

	  if ($row_grp['license'] == "other") {
		  echo "<P>License Other: <blockquote>".$row_grp['license_other']."</blockquote>";
	  }

	  if ($row_grp[status_comment]) {
		  echo "<P>Pending reason: <font color=red>".$row_grp['status_comment']."</font>";
	  }

	  echo "<P><HR><P>";
  }

	site_admin_footer();
}

function GroupEdit ($group_id, $submit, $resend, $form_public, $form_status, $form_license, $group_type, $form_domain)
{
  global $LICENSE;

  $group =& group_get_object($group_id);

	if (!$group || !is_object($group)) {
	  $feedback .= 'Error creating group object<br> ';
	} else if ($group->isError()) {
	  $feedback .= $group->getErrorMessage();
	}

	// This function performs very update

	if ($submit) {
		$permanently_delete = $_POST['permanently_delete'];
		if ( $permanently_delete )
		{
			destroy_group($group);
		}
		else
		{
			do_update($group, $form_public, $form_status, $form_license, $group_type, $form_domain);
		}
  } else if ($resend) {
	  $group->sendApprovalEmail();
		$feedback .= 'Instruction email sent<br> ';
	}

  site_admin_header();

	echo "<H4>".$group->getPublicName()."</H4>"
	    ."<p>"
			."[ <A href='".XOOPS_URL."/modules/xfmod/project/admin/?group_id=".$group_id."'>Project Admin</A> ]"
			."<p>"
			."<FORM action='admin.php' method='POST'>"
			."<table>"
			."<tr>"
			."<td colspan='3'>"
			."Group Type:"
			.show_group_type_box('group_type', $group->getType())
			."Status:"
			.html_build_select_box_from_arrays(
			      array('I','A','N','P','H','D'),
				  	array('Incomplete (I)',
						     	'Active (A)',
						'Inactive (N)',
									'Pending (P)',
									'Holding (H)',
									'Deleted (D)'),
						'form_status', $group->getStatus(), false)
			."Public?:"
			.html_build_select_box_from_arrays(array(0,1),array('No','Yes'),'form_public', $group->isPublic(), false)
			."</td>"
			."</tr>"
			."<tr>"
			."<td>"
			."Unix Group Name:"
			."</td>"
			."<td>"
			.$group->getUnixName()
			."</td>"
			."</tr>"
			."<tr>"
			."<td>"
			."License:"
			."</td>"
			."<td>"
			."<SELECT name='form_license'>"
			."<OPTION value='none'>N/A"
			."<OPTION value='other'>Other";

	while (list($k,$v) = each($LICENSE)) {
	  echo "<OPTION value='$k'";
		if ($k == $group->getLicense())
		  echo " selected";

		echo ">$v\n";
	}

	echo "</SELECT>"
	    ."</td>"
			."</tr>"
			."<tr>"
			."<td>"
			."HTTP Domain:"
			."</td>"
			."<td>"
			."<INPUT size='40' type='text' name='form_domain' value='".$group->getDomain()."'>"
			."</td>"
			."</tr>"
			."<tr>"
			."<td>"
			."Registration Application:"
			."</td>"
			."<td>"
			.$group->getRegistrationPurpose()
			."</td>"
			."</tr>";

	if ($group->getLicense() == 'other') {
	  echo "<tr>"
		    ."<td>License Other:"
				."</td>"
				."<td>"
				.$group->getLicenseOther()
				."</td>"
				."</tr>";
	}
	if ( "D" == $group->getStatus() )
	{
		echo "<tr><td align=\"right\">Permanently Delete This Project</td>"
			. "<td><INPUT type=\"checkbox\" name=\"permanently_delete\" value=\"no\">"
			. "</td></tr>";
	}
	echo "</table>"
	    ."<INPUT type='hidden' name='fct' value='groups'>"
	    ."<INPUT type='hidden' name='op' value='GroupEdit'>"
	    ."<INPUT type='hidden' name='group_id' value='".$group_id."'>"
			."<BR><INPUT type='submit' name='submit' value='Update'>"
			."&nbsp;&nbsp;&nbsp; <INPUT type='submit' name='resend' value='Resend New Project Instruction Email'>"
			."</FORM>";

	echo show_grouphistory($group->getID());

	site_admin_footer();
}

function ListUsers ($group_id, $action, $user_id)
{
  global $xoopsDB;

  $group =& group_get_object($group_id);
	if (!$group || !is_object($group)) {
    $feedback .= 'Error creating group object<br> ';
  } else if ($group->isError()) {
    $feedback .= $group->getErrorMessage();
  }

  // Administrative functions
  /*
	  Add a user to this group
  */
  if ($action == 'add_to_group') {
    $xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_user_group")." (user_id,group_id) VALUES ($user_id, $group_id)");
    $feedback .= " User Added To Group ";
  }

  site_admin_header();

  /*
	  Show list of users
  */
  echo "<p>User List for Group: ";
  /*
	  Show list for one group
  */
  echo "<b>".$group->getPublicName()."</b>"
	    ."\n<p>";

  $result = $xoopsDB->query("SELECT u.uid AS user_id,u.uname AS user_name "
                      ."FROM ".$xoopsDB->prefix("users")." u,".$xoopsDB->prefix("xf_user_group")." ug "
                      ."WHERE u.uid=ug.user_id "
                      ."AND ug.group_id=".$group_id." "
                      ."ORDER BY u.uname");

  echo "<TABLE width='100%' cellspacing='0' cellpadding='0' BORDER='1'>";

  while ($user = $xoopsDB->fetchArray($result)) {
	  echo "<TR>"
        ."<TD><B>".$user[user_id]."</B></TD>"
        ."<TD>".$user[user_name]."</TD>"
        ."<TD>[ <A HREF='".XOOPS_URL."/modules/xfjobs/viewprofile.php?user_id=".$user[user_id]."'>View Profile</A> ]</TD>"
        ."</TR>";
  }
  echo "</TABLE>";

  /*
      	Show a form so a user can be added to this group
  */
  ?>
  <hr>
  <P>
  <form action="admin.php" method="post">
  <input type="HIDDEN" name="fct" VALUE="groups">
  <input type="HIDDEN" name="op" VALUE="ListUsers">
  <input type="HIDDEN" name="action" VALUE="add_to_group">
  <input name="user_id" type="TEXT" value="">
  <p>
  Add User to Group (<?php echo $group->getPublicName(); ?>):
  <br>
  <input type="HIDDEN" name="group_id" VALUE="<?php echo $group_id; ?>">
  <p>
  <input type="submit" name="Submit" value="Submit">
  </form>

  <?php

  site_admin_footer();
}

function ManageResponses($action, $action2, $sure, $response_title, $response_text, $response_id) {
  global $xoopsDB;

  site_admin_header();

	$canned_response_res = false;
	?>
	<B>Manage Canned Responses</B><br />
  <a name="form">&nbsp;</a>
  <form method="post" action="admin.php#form">
  Existing Responses: <?php echo get_canned_responses(); ?>
  <input name="fct" type="hidden" value="groups">
  <input name="op" type="hidden" value="ManageResponses">
  <input name="action" type="submit" value="Edit">
  <input name="action" type="submit" value="Delete">
  <input type="checkbox" name="sure" value="yes"> Yes, I'm sure
  </form>

  <br><br>

  <?php

  if( $action == "Edit" ) {
	  // Edit Response
	  check_select_value($response_id, $action);
	  if( $action2 ) {
		  $xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_canned_responses")." SET response_title='$response_title', response_text='$response_text' WHERE response_id='$response_id'");
		  echo(" <b>Edited Response</b> ");
	  } else {
		  $res = $xoopsDB->queryF("SELECT * FROM ".$xoopsDB->prefix("xf_canned_responses")." WHERE response_id='$response_id'");
		  $row = $xoopsDB->fetchArray($res);
		  $response_title = $row['response_title'];
		  $response_text  = $row['response_text'];

			?>

      Edit Response:<br>
      <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
      Response Title: <input type="text" name="response_title" size="30" maxlength="25" value="<?php echo $response_title; ?>"><br>
      Response Text:<br>
      <textarea name="response_text" cols="50" rows="10"><?php echo $response_text; ?></textarea>
      <input name="fct" type="hidden" value="groups">
      <input name="op" type="hidden" value="ManageResponses">
      <input type="hidden" name="response_id" value="<?php echo $response_id; ?>">
      <input type="hidden" name="action2" value="go">
      <input type="submit" name="action" value="Edit">
      </form>

      <?php
 	  }
	} else if ( $action == "Delete" ) {
	  // Delete Response
		check_select_value($response_id, $action);
		if( $sure == "yes" ) {
		  $xoopsDB->queryF("DELETE FROM canned_responses WHERE response_id='$response_id'");
		  echo " <b>Deleted Response</b> ";
	  } else {
		  print("If you're aren't sure then why did you click 'Delete'?<br>");
		  print("<i>By the way, I didn't delete... just in case...</i><br>\n");
	  }
	} else if ( $action == "Create" ) {
	  // New Response
		add_canned_response($response_title, $response_text);
		echo " <b>Added Response</b> ";
	} else {
	  ?>

		Create New Response:<br>
    <form method="post" action="<?php echo $_SERVER['PHP_SELF']; ?>">
    Response Title: <input type="text" name="response_title" size="30" maxlength="25"><br>
    Response Text:<br>
    <textarea name="response_text" cols="50" rows="10"></textarea>
    <br>
    <input type="submit" name="action" value="Create">
    </form>

    <?php
  }

	site_admin_footer();
}

function check_select_value($value, $type)
{
	if( $value == "100" ) {
		echo "<span style='color: Red'><b>You can't $type \"None\", bozo!</b></span><br />\n";
	}
}

function activate_group($group_id) {
	global $feedback, $xoopsUser;
	$group =& group_get_object($group_id);

	if (!$group || !is_object($group)) {
		$feedback .= 'Error creating group object<br> ';
		return false;
	} else if ($group->isError()) {
		$feedback .= $group->getErrorMessage();
		return false;
	}
	$feedback .= '<BR>Approving Group: '.$group->getUnixName().' ';
	if (!$group->approve($xoopsUser)) {
		$feedback .= $group->getErrorMessage();
		return false;
	}

	return true;
}

function format_name($name, $status) {
	if ($status == 'D') {
		return "<b><strike>$name</strike></b>";
	} else if ($status == 'S') {
		return "<b><u>$name</u></b>";
	} else if ($status == 'H') {
		return "<b><u>$name</u></b>";
	} else if ($status == 'P') {
		return "<b><i>$name</i></b>";
	} else if ($status == 'I') {
		return "<b><i>$name</i></b>";
	}

	return $name;
}

function do_update(&$group, $is_public, $status, $license, $group_type, $http_domain) {
	global $feedback, $xoopsUser;

	if($group->getStatus() != $status){
		if (!$group->setStatus($xoopsUser, $status)) {
			$feedback .= $group->getErrorMessage();
			echo "here the error status is $status ";
			return false;
		}
	}

  if (!$group->updateAdmin($xoopsUser, $is_public, $license, $group_type, $http_domain)) {
		$feedback .= $group->getErrorMessage();
		return false;
  }

	$feedback .= ' Updated<br> ';

	return true;
}

function destroy_group(&$group)
{
	$group_id = $group->getID();

	// OK, this is not a trivial process.  We delete
	// from the database in order of importance.
	//
	$df = "DELETE FROM ";
	$sel = "SELECT ";
	$cond = " WHERE group_id='$group_id'";
	$failure = "Failed to remove group $group_id:  ";
	global $xoopsDB;
	// First, we want to disassociate all users from this group.
	// del from xf_user_group on group_id
	$sql = $df.$xoopsDB->prefix("xf_user_group").$cond;
	$xoopsDB->queryF($sql);
	//
	// Next we want to remove all the jobs for this group.
	// select job_id from xf_people_job on group_id
	$sql = $sel."job_id FROM ".$xoopsDB->prefix("xf_people_job").$cond;
	$result = $xoopsDB->query($sql);
	if ( $result )
	{
		// for each job_id, del from xf_people_job_inventory on job_id
		while ( $row = $xoopsDB->fetchArray($result) )
		{
			$id = $row['job_id'];
			$sql = $df.$xoopsDB->prefix("xf_people_job_inventory")." WHERE job_id='$id'";
			$xoopsDB->queryF($sql);
		}
		// del from xf_people_job on group_id
		$sql = $df.$xoopsDB->prefix("xf_people_job").$cond;
	}
	//
	// Next we want to remove the trove mappings.
	// del from xf_trove_group_link on group_id
	$sql = $df.$xoopsDB->prefix("xf_trove_group_link").$cond;
	$xoopsDB->queryF($sql);
	//
	// Next we want to get rid of the group itself.
	// del from xf_groups on group_id
	$sql = $df.$xoopsDB->prefix("xf_groups").$cond;
	$xoopsDB->queryF($sql);
	//
	// Next remove any links having to do with foundries.
	// select foundry_id from xf_foundry_projects on group_id
	$sql = $sel."foundry_id FROM ".$xoopsDB->prefix("xf_foundry_projects").$cond;
	$result = $xoopsDB->query($sql);
	if ( $result )
	{
		while ( $row = $xoopsDB->fetchArray($result) )
		{
			$fid = $row['foundry_id'];
			$sql = $df.$xoopsDB->prefix("xf_foundry_data")." WHERE foundry_id='$fid'";
			$xoopsDB->queryF($sql);
			$sql = $df.$xoopsDB->prefix("xf_foundry_faqs")." WHERE foundry_id='$fid'";
			$xoopsDB->queryF($sql);
			$sql = $df.$xoopsDB->prefix("xf_foundry_news")." WHERE foundry_id='$fid'";
			$xoopsDB->queryF($sql);
		}
		$sql = $df.$xoopsDB->prefix("xf_foundry_projects").$cond;
		$xoopsDB->queryF($sql);
	}
	// for each foundry_id:
	//   del from xf_foundry_data on foundry_id
	//   del from xf_foundry_faqs on foundry_id
	//   del from xf_foundry_news on foundry_id
	// del from xf_foundry_projects on group_id
	//
	// Next remove any surveys that had to do with this group.
	// del from xf_surveys on group_id
	$sql = $df.$xoopsDB->prefix("xf_surveys").$cond;
	$xoopsDB->queryF($sql);
	// del from xf_survey_questions on group_id
	$sql = $df.$xoopsDB->prefix("xf_survey_questions").$cond;
	$xoopsDB->queryF($sql);
	// del from xf_survey_responses on group_id
	$sql = $df.$xoopsDB->prefix("xf_survey_responses").$cond;
	$xoopsDB->queryF($sql);
	//
	// Next remove any news bytes associated with this group.
	// del from xf_news_bytes on group_id
	$sql = $df.$xoopsDB->prefix("xf_news_bytes").$cond;
	$xoopsDB->queryF($sql);
	//
	// Next remove all forum data associated with this group.
	// select group_forum_id from xf_forum_group_list on group_id
	$sql = $sel."group_forum_id FROM ".$xoopsDB->prefix("xf_forum_group_list").$cond;
	$result = $xoopsDB->query($sql);
	if ( $result )
	{
	// for each group_forum_id:
		while ( $row = $xoopsDB->fetchArray($result) )
		{
			$id = $row['group_forum_id'];
	//   del from xf_news_bytes on forum_id (=group_forum_id)
			$sql = $df.$xoopsDB->prefix("xf_news_bytes")." WHERE forum_id='$id'";
			$xoopsDB->queryF($sql);
	//   del from xf_forum_monitored_forums on forum_id (=group_forum_id)
			$sql = $df.$xoopsDB->prefix("xf_forum_monitored_forums")." WHERE forum_id='$id'";
			$xoopsDB->queryF($sql);
	//   del from xf_forum on group_forum_id
			$sql = $df.$xoopsDB->prefix("xf_forum")." WHERE group_forum_id='$id'";
			$xoopsDB->queryF($sql);
	//   del from xf_forum_agg_msg_count on group_forum_id
			$sql = $df.$xoopsDB->prefix("xf_forum_agg_msg_count")." WHERE group_forum_id='$id'";
			$xoopsDB->queryF($sql);
		}
	// del from xf_forum_group_list on group_id
		$sql = $df.$xoopsDB->prefix("xf_forum_group_list").$cond;
		$xoopsDB->queryF($sql);
	}
	// select forum_id from xf_forum_ext_group_list on group_id
	$sql = $sel."forum_id FROM ".$xoopsDB->prefix("xf_forum_ext_group_list").$cond;
	$result = $xoopsDB->query($sql);
	if ( $result )
	{
	// for each forum_id:
		while ( $row = $xoopsDB->fetchArray($result) )
		{
			$id = $row['forum_id'];
	//   del from xf_forum_monitored_forums on forum_id
			$sql = $df.$xoopsDB->prefix("xf_forum_monitored_forums")." WHERE forum_id='$id'";
			$xoopsDB->queryF($sql);
		}
	// del from xf_forum_ext_group_list on group_id
		$sql = $df.$xoopsDB->prefix("xf_forum_ext_group_list").$cond;
		$xoopsDB->queryF($sql);
	}
	//
	// Next remove all artifact (tracking) data associated with this group.
	// select group_artifact_id from xf_artifact_group_list on group_id
	$sql = $sel."group_artifact_id FROM ".$xoopsDB->prefix("xf_artifact_group_list").$cond;
	$result = $xoopsDB->query($sql);
	if ( $result )
	{
	// for each group_artifact_id:
		while ( $row = $xoopsDB->fetchArray($result) )
		{
			$id = $row['group_artifact_id'];
	//   select artifact_id from xf_artifact on group_artifact_id
			$sql = $sel."artifact_id FROM ".$xoopsDB->prefix("xf_artifact")." WHERE group_artifact_id='$id'";
			$result2 = $xoopsDB->query($sql);
			if ( $result2 )
			{
	//   for each artifact_id:
				while ( $row2 = $xoopsDB->fetchArray($result2) )
				{
					$id2 = $row['artifact_id'];
	//     del from xf_artifact_monitor on artifact_id
					$sql = $df.$xoopsDB->prefix("xf_artifact_monitor")." WHERE artifact_id='$id2'";
					$xoopsDB->queryF($sql);
	//     del from xf_artifact_file on artifact_id
					$sql = $df.$xoopsDB->prefix("xf_artifact_file")." WHERE artifact_id='$id2'";
					$xoopsDB->queryF($sql);
	//     del from xf_artifact_history on artifact_id
					$sql = $df.$xoopsDB->prefix("xf_artifact_history")." WHERE artifact_id='$id2'";
					$xoopsDB->queryF($sql);
	//     del from xf_artifact_message on artifact_id
					$sql = $df.$xoopsDB->prefix("xf_artifact_message")." WHERE artifact_id='$id2'";
					$xoopsDB->queryF($sql);
				}
	//   del from xf_artifact on group_artifact_id
				$sql = $df.$xoopsDB->prefix("xf_artifact")." WHERE group_artifact_id='$id'";
				$xoopsDB->queryF($sql);
			}
	//   del from xf_artifact_perm on group_artifact_id
			$sql = $df.$xoopsDB->prefix("xf_artifact_perm")." WHERE group_artifact_id='$id'";
			$xoopsDB->queryF($sql);
	//   del from xf_artifact_group_list on group_artifact_id
			$sql = $df.$xoopsDB->prefix("xf_artifact_group_list")." WHERE group_artifact_id='$id'";
			$xoopsDB->queryF($sql);
	//   del from xf_artifact_group on group_artifact_id
			$sql = $df.$xoopsDB->prefix("xf_artifact_group")." WHERE group_artifact_id='$id'";
			$xoopsDB->queryF($sql);
	//   del from xf_artifact_category on group_artifact_id
			$sql = $df.$xoopsDB->prefix("xf_artifact_category")." WHERE group_artifact_id='$id'";
			$xoopsDB->queryF($sql);
	//   del from xf_artifact_counts on group_artifact_id
			$sql = $df.$xoopsDB->prefix("xf_artifact_counts")." WHERE group_artifact_id='$id'";
			$xoopsDB->queryF($sql);
	//   del from xf_artifact_canned_responses on group_artifact_id
			$sql = $df.$xoopsDB->prefix("xf_artifact_canned_responses")." WHERE group_artifact_id='$id'";
			$xoopsDB->queryF($sql);
		}
	// del from xf_artifact_group_list on group_id
		$sql = $df.$xoopsDB->prefix("xf_artifact_group_list").$cond;
		$xoopsDB->queryF($sql);
	}
	//
	// Next remove all task data associated with this group.
	// select group_project_id from xf_project_group_list on group_id
	$sql = $sel."group_project_id FROM ".$xoopsDB->prefix("xf_project_group_list").$cond;
	$result = $xoopsDB->query($sql);
	if ( $result )
	{
	// for each group_project_id:
		while ( $row = $xoopsDB->fetchArray($result) )
		{
			$id = $row['group_project_id'];
	//   select project_task_id from xf_project_task on group_project_id
			$sql = $sel."project_task_id FROM ".$xoopsDB->prefix("xf_project_task")." WHERE group_project_id='$id'";
			$result2 = $xoopsDB->query($sql);
			if ( $result2 )
			{
	//   for each project_task_id:
				while ( $row2 = $xoopsDB->fetchArray($result2) )
				{
					$id2 = $row2['project_task_id'];
	//     del from xf_project_assigned_to on project_task_id
					$sql = $df.$xoopsDB->prefix("xf_project_assigned_to")." WHERE project_task_id='$id2'";
					$xoopsDB->queryF($sql);
	//     del from xf_project_dependencies on project_task_id
					$sql = $df.$xoopsDB->prefix("xf_project_dependencies")." WHERE project_task_id='$id2'";
					$xoopsDB->queryF($sql);
	//     del from xf_project_history on project_task_id
					$sql = $df.$xoopsDB->prefix("xf_project_history")." WHERE project_task_id='$id2'";
					$xoopsDB->queryF($sql);
				}
	//   del from xf_project_task on group_project_id
				$sql = $df.$xoopsDB->prefix("xf_project_task")." WHERE group_project_id='$id'";
				$xoopsDB->queryF($sql);
			}
		}
	// del from xf_project_group_list on group_id
		$sql = $df.$xoopsDB->prefix("xf_project_group_list").$cond;
		$xoopsDB->queryF($sql);
	}
	//
	// Next remove all file releases associated with this group.
	// select package_id from xf_frs_package on group_id
	$sql = $sel."package_id FROM ".$xoopsDB->prefix("xf_frs_package").$cond;
	$result = $xoopsDB->query($sql);
	if ( $result )
	{
	// for each package_id:
		while ( $row = $xoopsDB->fetchArray($result) )
		{
			$id = $row['package_id'];
	//   select release_id from xf_frs_release on package_id
			$sql = $sel."release_id FROM ".$xoopsDB->prefix("xf_frs_release")." WHERE package_id='$id'";
			$result2 = $xoopsDB->query($sql);
			if ( $result2 )
			{
	//   for each release_id:
				while ( $row2 = $xoopsDB->fetchArray($result2) )
				{
					$id2 = $row2['release_id'];
	//     select file_url from xf_frs_file on release_id
					$sql = $sel."file_url, file_id FROM ".$xoopsDB->prefix("xf_frs_file")." WHERE release_id='$id2'";
					$result3 = $xoopsDB->query($sql);
					if ( $result3 )
					{
	//     for each file_url:
						while ( $row3 = $xoopsDB->fetchArray($result3) )
						{
	//       delete file from filesystem
							unlink(trim($row3['file_url']));
							$sql = $df.$xoopsDB->prefix("xf_frs_dlstats_file_agg")." WHERE file_id='".$row['file_id']."'";
							$xoopsDB->queryF($sql);
						}
	//     del from xf_frs_file on release_id
						$sql = $df.$xoopsDB->prefix("xf_frs_file")." WHERE release_id='$id2'";
						$xoopsDB->queryF($sql);
					}
				}
	//   del from xf_frs_release on package_id
				$sql = $df.$xoopsDB->prefix("xf_frs_release")." WHERE package_id='$id'";
				$xoopsDB->queryF($sql);
			}
		}
	// del from xf_frs_package on group_id
		$sql = $df.$xoopsDB->prefix("xf_frs_package").$cond;
		$xoopsDB->queryF($sql);
	}
	//
	// Next remove all documents associated with this group.
	// select doc_group from xf_doc_groups on group_id
	$sql = $sel."doc_group FROM ".$xoopsDB->prefix("xf_doc_groups").$cond;
	$result = $xoopsDB->query($sql);
	if ( $result )
	{
	// for each doc_group:
		while ( $row = $xoopsDB->fetchArray($result) )
		{
			$dg = $row['doc_group'];
	//   select data from xf_doc_data on doc_group
			$sql = $sel."data FROM ".$xoopsDB->prefix("xf_doc_data")." WHERE doc_group='$dg'";
			$result2 = $xoopsDB->query($sql);
			if ( $result2 )
			{
	//   for each data:
				while ( $row2 = $xoopsDB->fetchArray($result2) )
				{
	//     delete file from filesystem
					unlink(trim($row2['data']));
				}
	//   del from xf_doc_data on doc_group
				$sql = $df.$xoopsDB->prefix("xf_doc_data")." WHERE doc_group='$dg'";
				$xoopsDB->queryF($sql);
			}
	//   del from xf_doc_feedback on doc_group
			$sql = $df.$xoopsDB->prefix("xf_doc_feedback")." WHERE doc_group='$dg'";
			$xoopsDB->queryF($sql);
	//   del from xf_doc_feedback_agg on doc_group
			$sql = $df.$xoopsDB->prefix("xf_doc_feedback_agg")." WHERE doc_group='$dg'";
			$xoopsDB->queryF($sql);
		}
	// del from xf_doc_groups on group_id
		$sql = $df.$xoopsDB->prefix("xf_doc_groups").$cond;
		$xoopsDB->queryF($sql);
	}
	//
	// Next remove project metric information for this group.
	// del from xf_project_weekly_metric on group_id
	$sql = $df.$xoopsDB->prefix("xf_project_weekly_metric").$cond;
	$xoopsDB->queryF($sql);
	//
	// Next remove project history for this group.
	// del from xf_group_history on group_id
	$sql = $df.$xoopsDB->prefix("xf_group_history").$cond;
	$xoopsDB->queryF($sql);
	//
	// Finally remove activity log entries for this group.
	// del from xf_activity_log on group_id
	$sql = $df.$xoopsDB->prefix("xf_activity_log").$cond;
	$xoopsDB->queryF($sql);
	//
	// Whew!  Done.

	return true;
}


} else {
    	echo "Access Denied";
}

?>