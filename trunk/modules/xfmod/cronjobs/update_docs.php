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
/**
 *  Update Documentation Feedback Count
 */
$rel = db_query("DELETE FROM ".$dbprefix."_xf_doc_feedback_agg;");
echo db_error();

$rel = db_query("SELECT df.docid "
               ."FROM ".$dbprefix."_xf_doc_feedback df "
               ."LEFT JOIN ".$dbprefix."_xf_doc_data dd USING (docid) "
               ."GROUP BY df.docid;");

while ($rel_arr = db_fetch_array($rel))
{
  $answer_yes = db_result(db_query("SELECT COUNT(*) AS count FROM ".$dbprefix."_xf_doc_feedback WHERE answer=2 AND docid=".$rel_arr['docid']),0,'count');
  $answer_no  = db_result(db_query("SELECT COUNT(*) AS count FROM ".$dbprefix."_xf_doc_feedback WHERE answer=1 AND docid=".$rel_arr['docid']),0,'count');
  $answer_na  = db_result(db_query("SELECT COUNT(*) AS count FROM ".$dbprefix."_xf_doc_feedback WHERE answer=0 AND docid=".$rel_arr['docid']),0,'count');

  $in = db_query("INSERT INTO ".$dbprefix."_xf_doc_feedback_agg VALUES ('".$rel_arr['docid']."','$answer_yes','$answer_no','$answer_na')");
  echo db_error();
}

db_query("VACUUM ANALYZE ".$dbprefix."_xf_doc_feedback_agg;");

include "footer.php";
?>