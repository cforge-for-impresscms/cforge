#!/usr/bin/php -q
<?php

include "db.php";
include "header.php";

$time = time();

$last_month  = ( $time - (86400 * 30) );  

//get the number of projects a user is a member of
db_query("CREATE TEMPORARY TABLE projects SELECT COUNT(user_id) AS count, user_id AS uid FROM ".$dbprefix."_xf_user_group GROUP BY user_id");

//get the number of submitted tracker items
db_query("CREATE TEMPORARY TABLE trackers SELECT COUNT(artifact_id) AS count, submitted_by AS uid FROM ".$dbprefix."_xf_artifact WHERE open_date>".$last_month." GROUP BY submitted_by");

//get the number of news submissions
db_query("CREATE TEMPORARY TABLE news SELECT COUNT(id) as count, submitted_by AS uid FROM ".$dbprefix."_xf_news_bytes WHERE date>".$last_month." GROUP BY submitted_by");	

//get the number of sample submissions
db_query("CREATE TEMPORARY TABLE samples SELECT COUNT(sampleid) AS count, created_by AS uid FROM ".$dbprefix."_xf_sample_data WHERE createdate>".$last_month." GROUP BY created_by");

//get the number of doc submissions
db_query("CREATE TEMPORARY TABLE documents SELECT COUNT(docid) AS count, created_by AS uid FROM ".$dbprefix."_xf_doc_data WHERE createdate>".$last_month." GROUP BY created_by");

//get the number of cvs updates
db_query("CREATE TEMPORARY TABLE cvs SELECT COUNT(count) AS count, user_id AS uid FROM ".$dbprefix."_xf_cvs_commit_tracker GROUP BY user_id");

db_query("CREATE TABLE IF NOT EXISTS ".$dbprefix."_xf_user_stats (uid INT PRIMARY KEY, projects INT, cvs INT, trackers INT, news INT, samples INT, documents INT)");
db_query("DELETE FROM ".$dbprefix."_xf_user_stats");
db_query("INSERT INTO ".$dbprefix."_xf_user_stats SELECT u.uid"
			.", COALESCE(p.count,0) as projects"
			.", COALESCE(c.count,0) as cvs"
			.", COALESCE(t.count,0) as trackers"
			.", COALESCE(n.count,0) as news"
			.", COALESCE(s.count,0) as samples"
			.", COALESCE(d.count,0) as documents"
			." FROM ".$dbprefix."_users AS u"
			." LEFT JOIN projects AS p USING(uid)"
			." LEFT JOIN cvs AS c USING(uid)"
			." LEFT JOIN trackers AS t USING(uid)"
			." LEFT JOIN news AS n USING(uid)"
			." LEFT JOIN samples AS s USING(uid)"
			." LEFT JOIN documents AS d USING(uid)");
				
	
	
include "footer.php";

?>