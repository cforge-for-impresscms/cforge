<?php
	 
	// We are reusing much of the logic and code from project registration.
	//
	// Communities are created by site admins, so we don't need to fill in
	// a justification for the community like we do for projects, nor do
	// we need to select a license.  We still want to reuse the language
	// from projects where possible.
	//
	if (!empty($_POST)) foreach($_POST as $k => $v) $ {
		$k }
	 = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) $ {
		$k }
	 = StopXSS($v);
	 
	$type = "community";
	$utype = "Community";
	$langfile = "register.php";
	 
	function includefile($filename)
	{
		global $icmsConfig;
		 
		if (file_exists(ICMS_ROOT_PATH."/modules/xfmod/language/".$icmsConfig['language']."/".$filename))
		{
			include(ICMS_ROOT_PATH."/modules/xfmod/language/".$icmsConfig['language']."/".$filename);
		}
		else
		{
			include(ICMS_ROOT_PATH."/modules/xfmod/language/english/".$filename);
		}
	}
	 
	if (!eregi("admin.php", $_SERVER['PHP_SELF']))
	{
		die("Access Denied");
	}
	if ($icmsUser->isAdmin($icmsModule->mid()))
	{
		include_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
		require_once(ICMS_ROOT_PATH."/modules/xfmod/include/account.php");
		include_once("admin/admin_utils.php");
		 
		site_admin_header();
		global $icmsDB;
		 
		//$submit = util_http_track_vars('submit');
		if ($submit)
		{
			//$full_name = util_http_track_vars('full_name');
			//$description = util_http_track_vars('description');
			//$unix_name = util_http_track_vars('unix_name');
			 
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
			else if(!account_groupnamevalid($unix_name))
			{
				$feedback .= _XF_REG_INVALIDUNIXNAME;
			}
			else if($icmsDB->getRowsNum($icmsDB->query("SELECT group_id FROM ". $icmsDB->prefix("xf_groups")." WHERE unix_group_name='$unix_name'")) > 0)
			{
				$feedback .= _XF_REG_UNIXGROUPALREADYTAKEN;
			}
			else if(strlen($description) < 10)
			{
				$feedback .= _XF_REG_DESCRIBEPROJECT;
			}
			else
				{
				$group = new Group();
				$res = $group->create(
				$icmsUser,
					$full_name,
					$unix_name,
					$description,
					"",
					"",
					"",
					true );
				 
				if (!$res)
				{
					$feedback .= $group->getErrorMessage();
					echo $feedback;
				}
				else
					{
					/*
					if ($icmsForge['manapprove'] == 1)
					{
					echo "<p>"._XF_REG_ISSUBMITTED."</p>"
					."<p>"._XF_REG_THANKYOU."</p>";
					 
					}
					else if($icmsForge['manapprove'] == 0)
					{
					if (!$group->approve($icmsUser))
					{
					$feedback .= $group->getErrorMessage();
					}
					else
					{
					echo "<p>"._XF_REG_ISACTIVATED.":<br />";
					echo "<a href='".ICMS_URL."/modules/xfmod/community/?".
					$group->getUnixName()."'>".ICMS_URL."/modules/xfmod/community/?".
					$group->getUnixName()."</a><br />";
					echo _XF_REG_PROJECTSTATS."</p>"
					."<p>"._XF_REG_THANKYOU."</p>";
					}
					}
					*/
					 
					 
					if (!$group->approve($icmsUser))
					{
						$feedback .= $group->getErrorMessage();
					}
					else
						{
						echo "<p>"._XF_REG_ISACTIVATED.":<br />";
						echo "<a href='".ICMS_URL."/modules/xfmod/community/?". $group->getUnixName()."'>".ICMS_URL."/modules/xfmod/community/?". $group->getUnixName()."</a><br />";
						echo _XF_REG_PROJECTSTATS."</p>" ."<p>"._XF_REG_THANKYOU."</p>";
					}
					 
					 
					 
					echo "<strong><font color=red>$feedback</font></strong><p>";
				}
			}
			$full_name = $description = $unix_name = "";
		}
		 
		 
		// Existing communities - name, description, admins
	?>
<hr><h4>Existing Communities</h4><p>
	<?php
		$sql = "SELECT group_name, unix_group_name, short_description FROM " .$icmsDB->prefix("xf_groups")." WHERE type='2'";
		$result = $icmsDB->query($sql);
		if ($icmsDB->getRowsNum($result) == 0)
		{
			echo "No current communities<br>\n";
		}
		else
			{
			while ($row = $icmsDB->fetchArray($result))
			{
				echo "<strong>Community Name</strong>:  <a href=\"".ICMS_URL."/modules/xfmod/community/?".$row['unix_group_name']."\">" . $row['group_name'] . "</a><br>&nbsp;&nbsp;Description:  " . $row['short_description'] . "<br>\n";
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

<?php includefile("register_step_1.php"); ?>

<?php echo _XF_REG_FULLNAME; ?>:
<BR>
<input size="40" maxlength="40" type=text name="full_name" value="<?php echo $ts->makeTboxData4Edit($full_name); ?>">

<h4>2. <?php echo _XF_REG_PUBLICDESCRIPTION; ?></h4>

<?php includefile("register_step_4.php"); ?>

<font size="-1">
<TEXTAREA name="description" wrap="virtual" cols="70" rows="5">
<?php echo $ts->makeTareaData4Edit($description); ?>
</textarea>
</font>

<H4>3. <?php echo _XF_REG_PROJECTUNIXNAME; ?></H4>

<?php includefile("register_step_5.php"); ?>

<p><?php echo _XF_REG_UNIXNAME; ?>:
<BR>
<input type=text maxlength="15" size="15" name="unix_name" value="<?php echo $ts->makeTboxData4Edit($unix_name); ?>">

<div align="center">
<input type=submit name="submit" value="<?php echo _XF_REG_IAGREE_COMM; ?>">
</div>

</form>

	<?php
		 
		site_admin_footer();
		 
	}
	else
	{
		echo "Access Denied";
	}
?>