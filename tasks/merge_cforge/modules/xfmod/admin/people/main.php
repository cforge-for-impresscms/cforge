<?php
	if (!eregi("admin.php", $_SERVER['PHP_SELF']))
	{
		die("Access Denied");
	}
	if ($icmsUser->isAdmin($icmsModule->mid()))
	{
		 
		include_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
		include_once("admin/admin_utils.php");
		include_once(ICMS_ROOT_PATH."/modules/xfjobs/people_utils.php");
		 
		$post_changes = http_post('post_changes');
		 
		if ($post_changes)
		{
			/*
			Update the database
			*/
			if ($people_cat)
			{
				$sql = "INSERT INTO ".$icmsDB->prefix("xf_people_job_category")."(name) VALUES('".$cat_name."')";
				$result = $icmsDB->queryF($sql);
				if (!$result)
				{
					$feedback .= ' Error inserting value: '.$icmsDB->error();
				}
				 
				$feedback .= ' Category Inserted ';
				 
			}
			else if($people_skills)
			{
				$sql = "INSERT INTO ".$icmsDB->prefix("xf_people_skill")."(name) VALUES('".$skill_name."')";
				$result = $icmsDB->queryF($sql);
				if (!$result)
				{
					$feedback .= ' Error inserting value: '.$icmsDB->error();
				}
				 
				$feedback .= ' Skill Inserted ';
				/*
				} else if($people_cat_mod) {
				 
				$sql="UPDATE people_category SET category_name='$cat_name' WHERE people_category_id='$people_cat_id' AND group_id='$group_id'";
				$result=db_query($sql);
				if (!$result || db_affected_rows($result) < 1) {
				$feedback .= ' Error modifying bug category ';
				echo db_error();
				} else {
				$feedback .= ' Bug Category Modified ';
				}
				 
				} else if($people_group_mod) {
				 
				$sql="UPDATE people_group SET group_name = '$group_name' WHERE people_group_id='$people_group_id' AND group_id='$group_id'";
				$result=db_query($sql);
				if (!$result || db_affected_rows($result) < 1) {
				$feedback .= ' Error modifying bug cateogry ';
				echo db_error();
				} else {
				$feedback .= ' Bug Category Modified ';
				}
				*/
			}
		}
		 
		site_admin_header();
		echo "<H4>Help Wanted Board</H4>";
		/*
		Show UI forms
		*/
		 
		/*
		List of possible categories for this group
		*/
		$sql = "SELECT category_id,name FROM ".$icmsDB->prefix("xf_people_job_category");
		$result = $icmsDB->query($sql);
		echo "<p>";
		if ($result && $icmsDB->getRowsNum($result) > 0)
		{
			ShowResultSet($result, 'Existing Categories', 'people_cat');
		}
		else
		{
			echo '<strong>No job categories</strong>';
			echo $icmsDB->error();
		}
	?>
<p>
<form action="admin.php" method="POST">
<input type="hidden" name="fct" value="people">
<input type="hidden" name="people_cat" value="y">
<input type="hidden" name="post_changes" value="y">
<H4>New Category Name:</H4>
<input type="text" name="cat_name" value="" size="15" maxlength="30"><BR>
<p>
<strong><FONT COLOR="RED">Once you add a category, it cannot be deleted</FONT></strong>
<p>
<input type="submit" name="submit" value="submit">
</form>
	<?php
		/*
		List of possible people_groups for this group
		*/
		$sql = "SELECT skill_id,name FROM ".$icmsDB->prefix("xf_people_skill");
		$result = $icmsDB->query($sql);
		echo "<p>";
		if ($result && $icmsDB->getRowsNum($result) > 0)
		{
			ShowResultSet($result, "Existing Skills", "people_skills");
		}
		else
		{
			echo "<strong>No Skills Found</strong>";
			echo $icmsDB->error();
		}
	?>
<p>
<form action="admin.php" method="POST">
<input type="hidden" name="fct" value="people">
<input type="hidden" name="people_skills" value="y">
<input type="hidden" name="post_changes" value="y">
<H4>New Skill Name:</H4>
<input type="text" name="skill_name" value="" size="15" maxlength="30"><BR>
<p>
<strong><FONT COLOR="RED">Once you add a skill, it cannot be deleted</FONT></strong>
<p>
<input type="submit" name="submit" value="submit">
</form>
	<?php
		 
		site_admin_footer();
		 
	}
	else
	{
		echo "Access Denied";
	}
?>