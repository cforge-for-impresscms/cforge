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
	 
	$langfile = "my.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once (ICMS_ROOT_PATH."/modules/xfaccount/account_util.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
	$icmsOption['template_main'] = 'xfaccount_diary.html';
	 
	if (!empty($_POST)) foreach($_POST as $k => $v) ${$k} = StopXSS($v);
	if (!empty($_GET)) foreach($_GET as $k => $v) ${$k} = StopXSS($v);
	 
	if ($icmsUser )
	{
		 
		$submit = null;
		$_summary = null;
		$_details = null;
		$_is_public = null;
		 
		$update = null;
		$add = null;
		$is_public = null;
		$feedback = null;
		 
		 
		foreach ($_POST as $k => $v )
		{
			$ {
				$k }
			 = $v;
		}
		 
		if ($submit)
		{
			//make changes to the database
			if ($update)
			{
				//updating an existing diary entry
				$res = $icmsDB->queryF("UPDATE ".$icmsDB->prefix("xf_user_diary")." SET " ."summary='".$ts->makeTboxData4Save($summary)."'," ."details='".$ts->makeTareaData4Save($details)."'," ."is_public='$is_public' " ."WHERE user_id='".$icmsUser->getVar("uid")."' " ."AND id='$diary_id'");
				 
				if ($res)
				{
					$feedback .= ' '._XF_MY_DIARYUPDATED.' ';
				}
				else
				{
					echo $icmsDB->error();
					$feedback .= ' '._XF_MY_NOTHINGUPDATED.' ';
				}
			}
			else if ($add)
			{
				//inserting a new diary entry
				 
				$sql = "INSERT INTO ".$icmsDB->prefix("xf_user_diary")." (user_id,date_posted,summary,details,is_public) VALUES (" ."'".$icmsUser->getVar("uid")."'," ."'".time()."'," ."'".$ts->makeTboxData4Save($summary)."'," ."'".$ts->makeTareaData4Save($details)."'," ."'$is_public')";
				 
				$res = $icmsDB->queryF($sql);
				if ($res)
				{
					$feedback .= ' '._XF_MY_ITEMADDED.' ';
					if ($is_public)
					{
						 
						//send an email if users are monitoring
						$sql = "SELECT u.email " ."FROM ".$icmsDB->prefix("xf_user_diary_monitor")." udm,".$icmsDB->prefix("users")." u " ."WHERE udm.user_id=u.uid " ."AND u.level <> 0 " ."AND udm.monitored_user='".$icmsUser->getVar("uid")."'";
						 
						$result = $icmsDB->query($sql);
						$rows = $icmsDB->getRowsNum($result);
						 
						if ($result && $rows > 0)
						{
							$bcc_arr = util_result_column_to_array($result);
							 
							$message = myGetDiaryMessage($icmsUser->getVar("uname"), $ts->makeTboxData4Edit($summary), $ts->makeTareaData4Edit($details), $icmsUser->getVar("uid"));
							 
							xoopsForgeMail ($icmsForge['noreply'], $icmsConfig['sitename'], $message['subject'], $message['body'], array($icmsForge['noreply']), $bcc_arr);
							 
							$feedback .= sprintf(_XF_MY_MAILSENT, $rows);
							 
						}
						else
						{
							$feedback .= _XF_MY_MAILNOTSENT;
							echo $icmsDB->error();
						}
						 
					}
					else
					{
						//don't send an email to monitoring users
						//since this is a private note
					}
				}
				else
				{
					$feedback .= ' '._XF_MY_ERRORITEMADDED.' ';
					echo $icmsDB->error();
				}
			}
		}
		 
		 
		if ($diary_id)
		{
			$sql = "SELECT * " ."FROM ".$icmsDB->prefix("xf_user_diary")." " ."WHERE user_id='".$icmsUser->getVar("uid")."' " ."AND id='$diary_id'";
			 
			$res = $icmsDB->query($sql);
			 
			if (!$res || $icmsDB->getRowsNum($res) < 1)
			{
				$feedback .= ' Entry not found or does not belong to you ';
				$proc_str = "add";
				$info_str = _XF_MY_ADDENTRY;
			}
			else
			{
				$proc_str = "update";
				$info_str = _XF_MY_UPDATEENTRY;
				$_summary = unofficial_getDBResult($res, 0, 'summary');
				$_details = unofficial_getDBResult($res, 0, 'details');
				$_is_public = unofficial_getDBResult($res, 0, 'is_public');
				$_diary_id = unofficial_getDBResult($res, 0, 'id');
			}
		}
		else
		{
			$proc_str = "add";
			$info_str = _XF_MY_ADDENTRY;
		}
		 
		$metaTitle = ": "._XF_MY_DIARYNOTES;

$mhandler = icms_gethandler('module');
$icmsModule = $xoopsModule = $mhandler->getByDirname('xfaccount');
global $icmsModule;

		include ("../../header.php");
		$icmsTpl->assign("account_header", account_header(_XF_MY_DIARYNOTES));
		 
		 
		$icmsTpl->assign("info_str", $info_str);
		$icmsTpl->assign("proc_str", $proc_str);
		$icmsTpl->assign("diary_id", $diary_id);
		$icmsTpl->assign("summary", $ts->makeTboxData4Edit($_summary));
		$icmsTpl->assign("details", $ts->makeTareaData4Edit($_details));
		$icmsTpl->assign("is_public", (($_is_public)?'CHECKED':''));
		 
		$title = _XF_MY_EXISTINGDIARY;
		 
		$icmsTpl->assign("title", $title);
		$icmsTpl->assign("feedback", $feedback);
		 
		$content = '<table border="0" width="100%">
			<tr class="bg2">
			<td class="fg3"><strong>Summary</strong></td>
			</tr>
			<tr>
			<td class="fg3">
			<strong>Creation Date</strong></td></tr>';
		 
		 
		$sql = "SELECT * " ."FROM ".$icmsDB->prefix("xf_user_diary")." " ."WHERE user_id='".$icmsUser->getVar("uid")."' " ."ORDER BY id DESC";
		 
		$result = $icmsDB->query($sql);
		$rows = $icmsDB->getRowsNum($result);
		if (!$result || $rows < 1)
			{
			$icmsTpl->assign("no_diary", true);
			$icmsTpl->assign("error", $icmsDB->error());
		}
		else
		{
			$icmsTpl->assign("no_diary", false);
			 
			$diary_list = array();
			for ($i = 0; $i < $rows; $i++)
			{
				$diary_list[$i]['id'] = unofficial_getDBResult($result, $i, 'id');
				$diary_list[$i]['summary'] = $ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'summary'));
				$diary_list[$i]['date_posted'] = date($sys_datefmt, unofficial_getDBResult($result, $i, 'date_posted'));
			}
			$icmsTpl->assign("diary_list", $diary_list);
		}
		 
		$icmsTpl->assign("title", $title);
		 
		include("../../footer.php");
		 
	}
	else
	{
		 
		redirect_header(ICMS_URL."/user.php", 2, _NOPERM . "called from ".__FILE__." line ".__LINE__ );
		exit;
		 
	}
	 
?>