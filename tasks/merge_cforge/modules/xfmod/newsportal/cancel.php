<?php
	/*
	with this script you can delete(cancel) articles.
	 
	DO NOT USE IT, IF YOU DON'T KNOW WHAT A CANCEL IS!
	 
	Especialy, don't use it in UseNet and protect it with a password(with
	.htaccess for example), or anybody can delete any article woldwide!
	*/
	 
	include_once "config.inc";
	 
	// register parameters
	$newsgroups = $_REQUEST["newsgroups"];
	$group = $_REQUEST["group"];
	$group_id = $_REQUEST["group_id"];
	$type = $_REQUEST["type"];
	$subject = $_REQUEST["subject"];
	$name = $_REQUEST["realname"];
	$email = $_REQUEST["email"];
	$body = $_REQUEST["body"];
	$abspeichern = $_REQUEST["abspeichern"];
	$references = $_REQUEST["references"];
	$msg_id = $_REQUEST["msg_id"];
	 
	include_once "head.inc";
	include_once $file_newsportal;
	// register parameters again
	$newsgroups = $_REQUEST["newsgroups"];
	$group = $_REQUEST["group"];
	$group_id = $_REQUEST["group_id"];
	$type = $_REQUEST["type"];
	$subject = $_REQUEST["subject"];
	$name = $_REQUEST["realname"];
	$email = $_REQUEST["email"];
	$body = $_REQUEST["body"];
	$abspeichern = $_REQUEST["abspeichern"];
	$references = $_REQUEST["references"];
	$msg_id = $_REQUEST["msg_id"];
	 
	if (!$perm->isForumAdmin())
	{
		redirect_header(ICMS_URL."/modules/xfmod/newsportal/thread.php?group_id=$group_id&group=$group", 4, "Invalid Permissions");
	}
	 
	if (!isset($type))
	{
		$type = "reply";
	}
	 
	if (!isset($group)) $group = $newsgroups;
	 
	// Is there a new article to be bost to the newsserver?
	if ($type == "cancel")
	{
		 
		$show = 0;
		// error handling
		if (trim($body) == "")
		{
			$type = "retry";
			$error = $text_post["missing_message"];
		}
		if (trim($forum_admin_email) == "")
		{
			$type = "retry";
			$error = $text_post["missing_email"];
		}
		if (!validate_email(trim($forum_admin_email)))
		{
			$type = "retry";
			$error = $text_post["error_wrong_email"];
		}
		if (trim($realname) == "")
		{
			$type = "retry";
			$error = $text_post["missing_name"];
		}
		if (trim($subject) == "")
		{
			$type = "retry";
			$error = $text_post["missing_subject"];
		}
		if ($type == "cancel")
		{
			if (!$readonly)
			{
				include_once("admin/utils.php");
				// post article to the newsserver
				$message = article_cancel(quoted_printable_encode(stripslashes($subject)),
					$forum_admin_email."(".quoted_printable_encode($name).")",
					$newsgroups, $references, $body, $cancelid);
				// Article sent without errors?
				if (substr($message, 0, 3) == "240")
				{
				?>

<h1 align="center">Delete Message</h1>

<p>The message was successfully deleted</p>

<p><a href="<?php echo $file_thread.'?group_id='.$group_id.'&group='.urlencode($group).'">'.$text_post["button_back"].'</a> '
.$text_post["button_back2"].' '.urlencode($group) ?></p>
				<?php
				}
				else
				{
					// article not accepted by the newsserver
					$type = "retry";
					$error = $text_post["error_newsserver"]."<br /><pre>$message</pre>";
				}
			}
			else
			{
				echo $text_post["error_readonly"];
			}
		}
	}
	 
	// A reply of an other article.
	if ($type == "reply")
	{
		$message = read_message($msg_id, 0, $group);
		$head = $message->header;
		$body = explode("\r\n", $message->body[0]);
		closeNNTPconnection($ns);
		$bodyzeile = "Reason for deletion:\n\n\n";
		if ($head->name != "")
		{
			$bodyzeile .= $head->name;
		}
		else
		{
			$bodyzeile .= $head->from;
		}
		$bodyzeile .= " posted a message containing the following:\n";
		$bodyzeile .= "---------------------------------------\n\n";
		for($i = 0; $i <= count($body)-1; $i++)
		{
			$bodyzeile .= $body[$i]."\r\n";
		}
		$subject = $head->subject;
		if (isset($head->followup) && ($head->followup != ""))
		{
			$newsgroups = $head->followup;
		}
		else
		{
			$newsgroups = $head->newsgroups;
		}
		splitSubject($subject);
		$subject = "Re: ".$subject;
		// Cut off old parts of a subject
		// for example: 'foo(was: bar)' becomes 'foo'.
		$subject = eregi_replace('(\(wa[sr]: .*\))$', '', $subject);
		$show = 1;
		$references = false;
		if (isset($head->references[0]))
		{
			for($i = 0; $i <= count($head->references)-1; $i++)
			{
				$references .= $head->references[$i]." ";
			}
		}
		$references .= $head->id;
	}
	 
	if ($type == "retry")
	{
		$show = 1;
		$bodyzeile = $body;
	}
	 
	if ($show == 1)
	{
		 
		if ($testgroup)
		{
			$testnewsgroups = testgroups($newsgroups);
		}
		else
		{
			$testnewsgroups = $newsgroups;
		}
		 
		if ($testnewsgroups == "")
		{
			echo $text_post["followup_not_allowed"];
			echo " ".$newsgroups;
		}
		else
		{
			$newsgroups = $testnewsgroups;
			 
			echo '<h1 align="center">Delete Message</h1>';
			 
			echo '<p><strong>Warning</strong> This will permenately remove the message from all connected news servers.</strong></p>';
			 
			if (isset($error)) echo "<p>$error</p>";
		?>

<br />

<form action="<?php echo $file_cancel?>" method="get">

<table>
<tr>
<td align="right" valign="top"><strong>From:</strong></td>
<td align="left">
<?php echo $icmsUser->getVar('name')?$icmsUser->getVar('name'):$icmsUser->getVar('uname'); ?><BR>	
<?php echo $icmsUser->getVar('email'); ?><br>
<input type="hidden" name="forum_admin_email" value="<?php echo $icmsUser->getVar('email'); ?>">
<input type="hidden" name="realname" value="<?php echo $icmsUser->getVar('name')?$icmsUser->getVar('name'):$icmsUser->getVar('uname'); ?>">
<input type="hidden" name="subject" value="cancel <?php echo htmlentities(stripslashes($subject));?>" />
</td>
</tr>
</table>

<br />

<table>
<tr><td><strong><?php echo $text_post["message"];?></strong><br />
<textarea name="body" rows="10" cols="79" wrap="physical">
<?php if(isset($bodyzeile)) echo stripslashes($bodyzeile); ?>
</textarea></td></tr>
<tr><td>
<input type="submit" value="Delete" />
</td>
</tr>
</table>
<input type="hidden" name="type" value="cancel" />
<input type="hidden" name="cancelid" value="<?php echo $head->id;?>" />
<input type="hidden" name="newsgroups" value="<?php echo $newsgroups; ?>" />
<input type="hidden" name="references" value="<?php echo htmlentities($references); ?>" />
<input type="hidden" name="group" value="<?php echo $group; ?>" />
<input type="hidden" name="group_id" value="<?php echo $group_id; ?>" />
</form>

<?php } } ?>

<?php include_once "tail.inc"; ?>