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
	 
	$time = time();
	 
	$last_week = ($time -(86400 * 7));
	$this_week = ($time);
	 
	$last_year = date('Y', $last_week);
	$last_month = date('m', $last_week);
	$last_day = date('d', $last_week);
	 
	$this_year = date('Y', $this_week);
	$this_month = date('m', $this_week);
	$this_day = date('d', $this_week);
	 
	// create a table to put the aggregates in
	 
	$sql = "
		CREATE TABLE ".$dbprefix."_xf_project_counts_weekly_tmp(
		group_id int,
		type text,
		count float(8)
		)
		";
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	#forum messages
	// Changes made to computation of activity rating:
	// 1.  We have to ensure that the count is >= 1 or we get neg. infinity.
	// 2.  Make the weights meaningful.
	$sql = "INSERT INTO ".$dbprefix."_xf_project_counts_weekly_tmp " ."SELECT fgl.group_id,'forum',LOG(10 *(COUNT(f.msg_id)+1)) AS count " ."FROM ".$dbprefix."_xf_forum f,".$dbprefix."_xf_forum_group_list fgl " ."WHERE f.group_forum_id=fgl.group_forum_id " ."AND date > '$last_week' " ."AND date < '$this_week' " ."GROUP BY group_id";
	 
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	#project manager tasks
	$sql = "INSERT INTO ".$dbprefix."_xf_project_counts_weekly_tmp " ."SELECT pgl.group_id,'tasks',LOG(12 *(COUNT(pt.project_task_id)+1)) AS count " ."FROM ".$dbprefix."_xf_project_task pt,".$dbprefix."_xf_project_group_list pgl " ."WHERE pt.group_project_id=pgl.group_project_id " ."AND end_date > '$last_week' " ."AND end_date < '$this_week' " ."GROUP BY group_id";
	 
	// print "\r\n".$sql;
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	#bugs
	$sql = "INSERT INTO ".$dbprefix."_xf_project_counts_weekly_tmp " ."SELECT agl.group_id,'bugs',LOG(12 *(COUNT(*)+1)) AS count " ."FROM ".$dbprefix."_xf_artifact_group_list agl,".$dbprefix."_xf_artifact a " ."WHERE a.open_date > '$last_week' " ."AND a.open_date < '$this_week' " ."AND a.group_artifact_id=agl.group_artifact_id " ."AND agl.datatype='1' " ."GROUP BY agl.group_id";
	 
	#print "\r\n".$sql;
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	#support
	$sql = "INSERT INTO ".$dbprefix."_xf_project_counts_weekly_tmp " ."SELECT agl.group_id,'support',LOG(15 *(COUNT(*)+1)) AS count " ."FROM ".$dbprefix."_xf_artifact_group_list agl,".$dbprefix."_xf_artifact a " ."WHERE a.open_date > '$last_week' " ."AND a.open_date < '$this_week' " ."AND a.group_artifact_id=agl.group_artifact_id " ."AND agl.datatype='2' " ."GROUP BY agl.group_id";
	 
	#print "\r\n".$sql;
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	#patches
	$sql = "INSERT INTO ".$dbprefix."_xf_project_counts_weekly_tmp " ."SELECT agl.group_id,'patches',LOG(12 *(COUNT(*)+1)) AS count " ."FROM ".$dbprefix."_xf_artifact_group_list agl,".$dbprefix."_xf_artifact a " ."WHERE a.open_date > '$last_week' " ."AND a.open_date < '$this_week' " ."AND a.group_artifact_id=agl.group_artifact_id " ."AND agl.datatype='3' " ."GROUP BY agl.group_id";
	 
	#print "\r\n".$sql;
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	#bugs
	$sql = "INSERT INTO ".$dbprefix."_xf_project_counts_weekly_tmp " ."SELECT agl.group_id,'features',LOG(12 *(COUNT(*)+1)) AS count " ."FROM ".$dbprefix."_xf_artifact_group_list agl,".$dbprefix."_xf_artifact a " ."WHERE a.open_date > '$last_week' " ."AND a.open_date < '$this_week' " ."AND a.group_artifact_id=agl.group_artifact_id " ."AND agl.datatype='4' " ."GROUP BY agl.group_id";
	 
	#print "\r\n".$sql;
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	#Others
	$sql = "INSERT INTO ".$dbprefix."_xf_project_counts_weekly_tmp " ."SELECT agl.group_id,'artifacts',LOG(12 *(COUNT(*)+1)) AS count " ."FROM ".$dbprefix."_xf_artifact_group_list agl,".$dbprefix."_xf_artifact a " ."WHERE a.open_date > '$last_week' " ."AND a.open_date < '$this_week' " ."AND a.group_artifact_id=agl.group_artifact_id " ."AND agl.datatype='0' " ."GROUP BY agl.group_id";
	 
	#print "\r\n".$sql;
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	 
	#developers
	#$sql="INSERT INTO project_counts_weekly_tmp
	#SELECT group_id,'developers',log((5*count(*))) AS count FROM user_group GROUP BY group_id";
	#$rel = db_query($sql);
	#echo "<p>$sql<p>".db_error();
	#
	 
	 
	#file releases
	$sql = "INSERT INTO ".$dbprefix."_xf_project_counts_weekly_tmp " ."SELECT fp.group_id,'filereleases',LOG(15 *(COUNT(*)+1)) AS count " ."FROM ".$dbprefix."_xf_frs_release fr,".$dbprefix."_xf_frs_package fp " ."WHERE fp.package_id=fr.package_id " ."AND fr.release_date > '$last_week' " ."AND fr.release_date < '$this_week' " ."GROUP BY fp.group_id";
	 
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	#create a new table to insert the final records into
	$sql = "
		CREATE TABLE ".$dbprefix."_xf_project_metric_weekly_tmp1(
		ranking int(11) auto_increment,
		group_id int not null,
		value float(10),
		primary key(ranking)
		)
		";
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	#insert the rows into the table in order, adding a sequential rank #
	$sql = "INSERT INTO ".$dbprefix."_xf_project_metric_weekly_tmp1(group_id,value) " ."SELECT pcwt.group_id,SUM(pcwt.count) AS value " ."FROM ".$dbprefix."_xf_project_counts_weekly_tmp pcwt " ."WHERE pcwt.count > 0 " ."GROUP BY group_id ORDER BY value DESC";
	 
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	 
	// Get the low and high watermarks.
	$sql = "SELECT MAX(value) AS value FROM ".$dbprefix."_xf_project_metric_weekly_tmp1";
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	$high = db_result($rel, 0, 0);
	$sql = "SELECT MIN(value) AS value FROM ".$dbprefix."_xf_project_metric_weekly_tmp1";
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	$low = db_result($rel, 0, 0);
	 
	 
	// Changes made to activity algorithm:
	// The total number of rows has no meaning in determining a project's rating.
	 
	#numrows in the set
	//$sql = "SELECT COUNT(*) FROM ".$dbprefix."_xf_project_metric_weekly_tmp1";
	 
	//$rel = db_query($sql);
	//if(!$rel) {
	// echo "<p>$sql<p>".db_error();
	// echo db_error();
	//}
	 
	//$counts = db_result($rel,0,0);
	// print "\r\nCounts: ".$counts;
	 
	#drop the old metrics table
	$sql = "DELETE FROM ".$dbprefix."_xf_project_weekly_metric";
	 
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	// Changes made to activity algorithm:
	// We present a linear distribution of the numbers from the low to high
	// watermark.  Each value is represented as a percentage of the distance
	// between the low and high.  So the project with the lowest value will
	// have a rating of 0, and the highest will have a rating of 100.
	//
	// We could have done a normal distribution instead of a linear one, but
	// I'm not that ambitious.
	//
	// The old algorithm doesn't make sense anyway.  Here's why:
	// 1.  The set of possible values for $counts includes all non-negative integers,
	//     including 0.
	// 2.  The set of possible values for value includes all non-negative real numbers.
	// 3.  First, since $counts can be 0, the algorithm can produce undefined results.
	// 4.  Assuming $counts != 0, the range of the algorithm(f) is from negative infinity
	//     to 200, inclusive, broken down as follows:
	//     - For 0 <= value < 1, the range of(f) is 100 <(f) <= 200
	//     - For value == 1, the range of(f) is(f) == 100
	//     - For 1 < value <= infinity, the range of(f) is negative infinity <=(f) < 100
	//     whereas the expected range of(f) should be 0 <=(f) <= 100.  So we can see that
	//    (f) generates values outside of a meaningful range for the purpose of(f).  In
	//     addition, we can see that the domain/range mapping is inverse of what we expect.
	//     In other words, we expect that high values for value will equate to high results
	//     for(f), but the inverse is true, as low values generate high results, notably,
	//     a value of 1 generates a result of 100, the highest expected value.
	//     Likewise, high values in truth generate low results, most notably that an
	//     infinitely large value generates a result that is infinitely negative.
	//     This function(f) would work if the domain were 0 <= value <= 1, which it
	//     is not, as we can see by inspection of the mechanism for generating value.
	//     Furthermore, an increase in activity of a group generates larger numbers for
	//     value, which results in a smaller activity rating.
	$sql = "INSERT INTO ".$dbprefix."_xf_project_weekly_metric(ranking,percentile,group_id) " //      ."SELECT ranking,(100 -(100 *((value - 1) / $counts))),group_id "
	//  . "SELECT ranking,100*((value-$low)/($high-$low)),group_id "
	. "SELECT ranking,IF(100*((value-$low)/($high-$low))>0,100*((value-$low)/($high-$low)),0),group_id " ."FROM ".$dbprefix."_xf_project_metric_weekly_tmp1 " ."ORDER BY ranking ASC";
	 
	$rel = db_query($sql);
	if (!$rel)
	{
		echo "<p>$sql<p>".db_error();
		echo db_error();
	}
	 
	echo db_error();
	 
	 
	$rel = db_query("DROP TABLE ".$dbprefix."_xf_project_counts_weekly_tmp;");
	$rel = db_query("DROP TABLE ".$dbprefix."_xf_project_metric_weekly_tmp1;");
	 
	 
	include "footer.php";
?>