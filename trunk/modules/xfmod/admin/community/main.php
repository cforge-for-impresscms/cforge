<?php

// We are reusing much of the logic and code from project registration.
//
// Communities are created by site admins, so we don't need to fill in
// a justification for the community like we do for projects, nor do
// we need to select a license.  We still want to reuse the language
// from projects where possible.
//
$type="community";
$utype="Community";
$langfile="register.php";

function includefile ($filename)
{
  global $xoopsConfig;

  if ( file_exists(XOOPS_ROOT_PATH."/modules/xfmod/language/".$xoopsConfig['language']."/".$filename) ) {
    include(XOOPS_ROOT_PATH."/modules/xfmod/language/".$xoopsConfig['language']."/".$filename);
  } else {
    include(XOOPS_ROOT_PATH."/modules/xfmod/language/english/".$filename);
  }
}

if (!eregi("admin.php", $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if ( $xoopsUser->isAdmin($xoopsModule->mid()) )
{
	include_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/account.php");
	include_once("admin/admin_utils.php");

	site_admin_header();
	global $xoopsDB;

	$submit = util_http_track_vars('submit');
	if ($submit)
	{
		$full_name = util_http_track_vars('full_name');
		$description = util_http_track_vars('description');
		$unix_name = util_http_track_vars('unix_name');
		
    	$full_name = trim($full_name);
    	$description = trim($description);
    	$unix_name = strtolower($unix_name);


    /*
		Fierce validation
    */
    if (strlen($full_name) < 3)
	{
      $feedback .= _XF_REG_INVALIDFULLNAME;
    }
	else if (!account_groupnamevalid($unix_name))
	{
      $feedback .= _XF_REG_INVALIDUNIXNAME;
    }
	else if ($xoopsDB->getRowsNum($xoopsDB->query("SELECT group_id FROM ".
		$xoopsDB->prefix("xf_groups")." WHERE unix_group_name='$unix_name'")) > 0)
	{
      $feedback .= _XF_REG_UNIXGROUPALREADYTAKEN;
    }
	else if (strlen($description) < 10)
	{
      $feedback .= _XF_REG_DESCRIBEPROJECT;
    }
	else
	{
       $group = new Group();
       $res = $group->create(
 			    $xoopsUser,
 			    $full_name,
 			    $unix_name,
 			    $description,
			    "",
			    "",
			    "",
			    true
			    );

		if (!$res)
		{
 			$feedback .= $group->getErrorMessage();
 			echo $feedback;
      	}
		else
		{
			/*
 			if ($xoopsForge['manapprove'] == 1)
			{
 	  			echo "<p>"._XF_REG_ISSUBMITTED."</p>"
 	    			."<p>"._XF_REG_THANKYOU."</p>";

 			}
			else if ($xoopsForge['manapprove'] == 0)
			{
 	  			if (!$group->approve($xoopsUser))
				{
 	    			$feedback .= $group->getErrorMessage();
 	  			}
				else
				{
            		echo "<p>"._XF_REG_ISACTIVATED.":<br />";
	    			echo "<a href='".XOOPS_URL."/modules/xfmod/community/?".
						$group->getUnixName()."'>".XOOPS_URL."/modules/xfmod/community/?".
						$group->getUnixName()."</a><br />";
            			echo _XF_REG_PROJECTSTATS."</p>"
	      				."<p>"._XF_REG_THANKYOU."</p>";
 	  			}
 			}
			*/


			if (!$group->approve($xoopsUser))
			{
				$feedback .= $group->getErrorMessage();
			}
			else
			{
				echo "<p>"._XF_REG_ISACTIVATED.":<br />";
				echo "<a href='".XOOPS_URL."/modules/xfmod/community/?".
					$group->getUnixName()."'>".XOOPS_URL."/modules/xfmod/community/?".
					$group->getUnixName()."</a><br />";
					echo _XF_REG_PROJECTSTATS."</p>"
					."<p>"._XF_REG_THANKYOU."</p>";
			}



			echo "<B><font color=red>$feedback</font></b><p>";
      	}
	}
    $full_name = $description = $unix_name = "";
}


  // Existing communities - name, description, admins
?>
		 <hr><h4>Existing Communities</h4><p>
<?php
  $sql = "SELECT group_name, unix_group_name, short_description FROM "
		    .$xoopsDB->prefix("xf_groups")." WHERE type='2'";
  $result = $xoopsDB->query($sql);
  if ( $xoopsDB->getRowsNum($result) == 0 )
    {
      echo "No current communities<br>\n";
    }
  else
    {
      while ( $row = $xoopsDB->fetchArray($result) )
	{
	  echo "<b>Community Name</b>:  <a href=\"".XOOPS_URL."/modules/xfmod/community/?".$row['unix_group_name']."\">" . $row['group_name'] . "</a><br>&nbsp;&nbsp;Description:  " . $row['short_description'] . "<br>\n";
	}
    }



  // Create a new community
?>
      <hr><h4>Create A New Community</h4><br>
<?php echo _XF_REG_TOAPPLYFILLIN; ?>
</p>

<form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
<input type="hidden" name="fct" value="community">

<H4>1. <?php echo _XF_REG_PrOJECTFULLNAME; ?></H4>

<?php includefile ("register_step_1.php"); ?>

<?php echo _XF_REG_FULLNAME; ?>:
<BR>
<INPUT size="40" maxlength="40" type=text name="full_name" value="<?php echo $ts->makeTboxData4Edit($full_name); ?>">

<h4>2. <?php echo _XF_REG_PUBLICDESCRIPTION; ?></h4>

<?php includefile ("register_step_4.php"); ?>

<font size="-1">
<TEXTAREA name="description" wrap="virtual" cols="70" rows="5">
<?php echo $ts->makeTareaData4Edit($description); ?>
</TEXTAREA>
</font>

<H4>3. <?php echo _XF_REG_PROJECTUNIXNAME; ?></H4>

<?php includefile ("register_step_5.php"); ?>

<P><?php echo _XF_REG_UNIXNAME; ?>:
<BR>
<input type=text maxlength="15" SIZE="15" name="unix_name" value="<?php echo $ts->makeTboxData4Edit($unix_name); ?>">

<div align="center">
<input type=submit name="submit" value="<?php echo _XF_REG_IAGREE_COMM; ?>">
</div>

</form>

<?php

  site_admin_footer();

} else {
  echo "Access Denied";
}
?>