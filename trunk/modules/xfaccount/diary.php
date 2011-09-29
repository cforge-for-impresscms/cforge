<?php
/**
  *
  * SourceForge User's Personal Page
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: diary.php,v 1.7 2004/01/14 19:04:29 devsupaul Exp $
  *
  */

include_once ("../../mainfile.php");

$langfile="my.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once (XOOPS_ROOT_PATH."/modules/xfaccount/account_util.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
$xoopsOption['template_main'] = 'xfaccount_diary.html';

if ( $xoopsUser ) {
	
	$submit = null;
	$diary_id = http_get('diary_id');
	$_summary = null;
	$_details = null;
	$_is_public = null;

	$update = null;
	$add = null;
	$is_public = null;
	$feedback = null;

	
	foreach ( $_POST as $k => $v ) {
	${$k} = $v;
	}
	
	if ($submit) {
		//make changes to the database
		if ($update) {
			//updating an existing diary entry
			$res = $xoopsDB->queryF("UPDATE ".$xoopsDB->prefix("xf_user_diary")." SET "
			                       ."summary='".$ts->makeTboxData4Save($summary)."',"
					       ."details='".$ts->makeTareaData4Save($details)."',"
					       ."is_public='$is_public' "
					       ."WHERE user_id='".$xoopsUser->getVar("uid")."' "
					       ."AND id='$diary_id'");

			if ($res) {
				$feedback .= ' '._XF_MY_DIARYUPDATED.' ';
			} else {
				echo $xoopsDB->error();
				$feedback .= ' '._XF_MY_NOTHINGUPDATED.' ';
			}
		} else if ($add) {
			//inserting a new diary entry

			$sql = "INSERT INTO ".$xoopsDB->prefix("xf_user_diary")." (user_id,date_posted,summary,details,is_public) VALUES ("
			      ."'".$xoopsUser->getVar("uid")."',"
						."'".time()."',"
						."'".$ts->makeTboxData4Save($summary)."',"
						."'".$ts->makeTareaData4Save($details)."',"
						."'$is_public')";

			$res = $xoopsDB->queryF($sql);
			if ($res) {
				$feedback .= ' '._XF_MY_ITEMADDED.' ';
				if ($is_public) {

					//send an email if users are monitoring
					$sql = "SELECT u.email "
					      ."FROM ".$xoopsDB->prefix("xf_user_diary_monitor")." udm,".$xoopsDB->prefix("users")." u "
								."WHERE udm.user_id=u.uid "
								."AND u.level <> 0 "
								."AND udm.monitored_user='".$xoopsUser->getVar("uid")."'";

					$result = $xoopsDB->query($sql);
					$rows = $xoopsDB->getRowsNum($result);

					if ($result && $rows > 0) {
						$bcc_arr = util_result_column_to_array($result);

						$message = myGetDiaryMessage($xoopsUser->getVar("uname"), $ts->makeTboxData4Edit($summary), $ts->makeTareaData4Edit($details), $xoopsUser->getVar("uid"));

						xoopsForgeMail ($xoopsForge['noreply'], $xoopsConfig['sitename'], $message['subject'], $message['body'], array($xoopsForge['noreply']), $bcc_arr);

						$feedback .= sprintf(_XF_MY_MAILSENT, $rows);

					} else {
						$feedback .= _XF_MY_MAILNOTSENT;
						echo $xoopsDB->error();
					}

				} else {
					//don't send an email to monitoring users
					//since this is a private note
				}
			} else {
				$feedback .= ' '._XF_MY_ERRORITEMADDED.' ';
				echo $xoopsDB->error();
			}
		}
	}


	if ($diary_id) {
		$sql = "SELECT * "
		      ."FROM ".$xoopsDB->prefix("xf_user_diary")." "
					."WHERE user_id='".$xoopsUser->getVar("uid")."' "
					."AND id='$diary_id'";

		$res = $xoopsDB->query($sql);

		if (!$res || $xoopsDB->getRowsNum($res) < 1) {
			$feedback .= ' Entry not found or does not belong to you ';
			$proc_str = "add";
			$info_str = _XF_MY_ADDENTRY;
		} else {
			$proc_str = "update";
			$info_str = _XF_MY_UPDATEENTRY;
			$_summary = unofficial_getDBResult($res,0,'summary');
			$_details = unofficial_getDBResult($res,0,'details');
			$_is_public = unofficial_getDBResult($res,0,'is_public');
			$_diary_id = unofficial_getDBResult($res,0,'id');
		}
	} else {
		$proc_str = "add";
		$info_str = _XF_MY_ADDENTRY;
	}

	$metaTitle=": "._XF_MY_DIARYNOTES;
	include ("../../header.php");
	$xoopsTpl->assign("account_header", account_header(_XF_MY_DIARYNOTES));


	$xoopsTpl->assign("info_str",$info_str);
	$xoopsTpl->assign("proc_str",$proc_str);
	$xoopsTpl->assign("diary_id",$diary_id);
	$xoopsTpl->assign("summary",$ts->makeTboxData4Edit($_summary));
	$xoopsTpl->assign("details",$ts->makeTareaData4Edit($_details));
	$xoopsTpl->assign("is_public",(($_is_public)?'CHECKED':''));
	
	$title = _XF_MY_EXISTINGDIARY;

	$xoopsTpl->assign("title",$title);
	$xoopsTpl->assign("feedback",$feedback);

	$content = '<table border="0" width="100%">
		<tr class="bg2">
		<td class="fg3"><b>Summary</b></td><td class="fg3">
		<b>Creation Date</b></td></tr>';


	$sql = "SELECT * "
	      ."FROM ".$xoopsDB->prefix("xf_user_diary")." "
				."WHERE user_id='".$xoopsUser->getVar("uid")."' "
				."ORDER BY id DESC";

	$result = $xoopsDB->query($sql);
	$rows = $xoopsDB->getRowsNum($result);
	if (!$result || $rows < 1)
	{
		$xoopsTpl->assign("no_diary",true);
		$xoopsTpl->assign("error",$xoopsDB->error());
	}
	else
	{
		$xoopsTpl->assign("no_diary",false);

		$diary_list = array();
		for ($i=0; $i<$rows; $i++)
		{
			$diary_list[$i]['id'] = unofficial_getDBResult($result,$i,'id');
			$diary_list[$i]['summary'] = $ts->makeTboxData4Show(unofficial_getDBResult($result,$i,'summary'));
			$diary_list[$i]['date_posted'] = date($sys_datefmt, unofficial_getDBResult($result,$i,'date_posted'));
		}
		$xoopsTpl->assign("diary_list",$diary_list);
	}

	$xoopsTpl->assign("title",$title);

	include("../../footer.php");

} else {

	redirect_header(XOOPS_URL."/user.php",2,_NOPERM);
	exit;

}

?>