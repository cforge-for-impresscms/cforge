<?php
if (!eregi("admin.php", $_SERVER['PHP_SELF'])) { die ("Access Denied"); }
if ( $xoopsUser->isAdmin($xoopsModule->mid()) ) {

include_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
include_once("admin/admin_utils.php");
include_once(XOOPS_ROOT_PATH."/modules/xfjobs/people_utils.php");

$post_changes = http_post('post_changes');

if ($post_changes) {
	/*
		Update the database
	*/
	if ($people_cat) {
		$sql = "INSERT INTO ".$xoopsDB->prefix("xf_people_job_category")." (name) VALUES ('".$cat_name."')";
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$feedback .= ' Error inserting value: '.$xoopsDB->error();
		}

		$feedback .= ' Category Inserted ';

	} else if ($people_skills) {
		$sql="INSERT INTO ".$xoopsDB->prefix("xf_people_skill")." (name) VALUES ('".$skill_name."')";
		$result = $xoopsDB->queryF($sql);
		if (!$result) {
			$feedback .= ' Error inserting value: '.$xoopsDB->error();
		}

		$feedback .= ' Skill Inserted ';
/*
		} else if ($people_cat_mod) {

			$sql="UPDATE people_category SET category_name='$cat_name' WHERE people_category_id='$people_cat_id' AND group_id='$group_id'";
			$result=db_query($sql);
			if (!$result || db_affected_rows($result) < 1) {
				$feedback .= ' Error modifying bug category ';
				echo db_error();
			} else {
				$feedback .= ' Bug Category Modified ';
			}

		} else if ($people_group_mod) {

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
$sql = "SELECT category_id,name FROM ".$xoopsDB->prefix("xf_people_job_category");
$result = $xoopsDB->query($sql);
echo "<P>";
if ($result && $xoopsDB->getRowsNum($result) > 0) {
	ShowResultSet($result,'Existing Categories','people_cat');
} else {
	echo '<B>No job categories</B>';
	echo $xoopsDB->error();
}
?>
<P>
<FORM ACTION="admin.php" METHOD="POST">
<INPUT TYPE="HIDDEN" NAME="fct" VALUE="people">
<INPUT TYPE="HIDDEN" NAME="people_cat" VALUE="y">
<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
<H4>New Category Name:</H4>
<INPUT TYPE="TEXT" NAME="cat_name" VALUE="" SIZE="15" MAXLENGTH="30"><BR>
<P>
<B><FONT COLOR="RED">Once you add a category, it cannot be deleted</FONT></B>
<P>
<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="SUBMIT">
</FORM>
<?php
	/*
	List of possible people_groups for this group
*/
$sql = "SELECT skill_id,name FROM ".$xoopsDB->prefix("xf_people_skill");
$result = $xoopsDB->query($sql);
echo "<P>";
if ($result && $xoopsDB->getRowsNum($result) > 0) {
	ShowResultSet($result,"Existing Skills","people_skills");
} else {
	echo "<B>No Skills Found</B>";
	echo $xoopsDB->error();
}
?>
<P>
<FORM ACTION="admin.php" METHOD="POST">
<INPUT TYPE="HIDDEN" NAME="fct" VALUE="people">
<INPUT TYPE="HIDDEN" NAME="people_skills" VALUE="y">
<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
<H4>New Skill Name:</H4>
<INPUT TYPE="TEXT" NAME="skill_name" VALUE="" SIZE="15" MAXLENGTH="30"><BR>
<P>
<B><FONT COLOR="RED">Once you add a skill, it cannot be deleted</FONT></B>
<P>
<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="SUBMIT">
</FORM>
<?php

site_admin_footer();

} else {
    	echo "Access Denied";
}
?>