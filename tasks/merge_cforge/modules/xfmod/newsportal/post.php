<?php
	include_once "config.inc";
	 
	// register parameters
	$newsgroups = $_REQUEST["newsgroups"];
	$mygroup = $_REQUEST["group"];
	$type = $_REQUEST["type"];
	$subject = $_REQUEST["subject"];
	$realname = $_REQUEST["realname"];
	$email = $_REQUEST["email"];
	$body = $_REQUEST["body"];
	$abspeichern = $_REQUEST["abspeichern"];
	$references = $_REQUEST["references"];
	$msg_id = $_REQUEST["msg_id"];
	 
	 
	if ((isset($post_server)) && ($post_server != ""))
	$server = $post_server;
	if ((isset($post_port)) && ($post_port != ""))
	$port = $post_port;
	 
	include_once "head.inc";
	include_once $file_newsportal;
	 
	if ($icmsUser == null)
	redirect_header($_SERVER['HTTP_REFERER'], 4, "You must be logged in to post to the forum from the web.");
	 
	if (!isset($references))
	{
		$references = false;
	}
	 
	if (!isset($type))
	{
		$type = "new";
	}
	 
	if ($type == "new")
	{
		$subject = "";
		$bodyzeile = "";
		$show = 1;
	}
	 
	if (!isset($mygroup)) $mygroup = $newsgroups;
	 
	// Is there a new article to be bost to the newsserver?
	if ($type == "post")
	{
		$show = 0;
		// error handling
		if (trim($body) == "")
		{
			$type = "retry";
			$error = $text_post["missing_message"];
		}
		if (trim($email) == "")
		{
			$type = "retry";
			$error = $text_post["missing_email"];
		}
		if (!validate_email(trim($email)))
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
		if ($type == "post")
		{
			if (!$readonly)
			{
				// post article to the newsserver
				$message = verschicken(quoted_printable_encode(stripslashes($subject)),
					$email."(".quoted_printable_encode($realname).")",
					$newsgroups, $references, $body);
				// Article sent without errors?
				if (substr($message, 0, 3) == "240")
				{
					$icmsUser->incrementPost();
				?>

<h1 align="center"><?php echo $text_post["message_posted"];?></h1>

<p><?php echo $text_post["message_posted2"];?></p>

<p><a href="<?php echo $file_thread.'?group_id='.$group_id.'&group='.urlencode($mygroup).'">'.$text_post["button_back"].'</a> '
.$text_post["button_back2"].' '.urlencode($mygroup) ?></p>
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
		//  $ns=OpenNNTPconnection($server,$port);
		$message = read_message($msg_id, 0, $mygroup);
		$head = $message->header;
		$body = explode("\r\n", $message->body[0]);
		closeNNTPconnection($ns);
		if ($head->name != "")
		{
			$bodyzeile = $head->name;
		}
		else
		{
			$bodyzeile = $head->from;
		}
		$bodyzeile = $bodyzeile." wrote:\n\n";
		for($i = 0; $i <= count($body)-1; $i++)
		{
			if ((isset($cutsignature)) && ($cutsignature == true) && ($body[$i] == '-- '))
			break;
			if (trim($body[$i]) != "")
			{
				$bodyzeile = $bodyzeile."> ".$body[$i]."\r\n";
			}
			else
			{
				$bodyzeile .= "\r\n";
			}
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
			 
			echo '<h1 align="center">'.$text_post["group_head"] .$text_post["group_tail"].'</h1>';
			 
			if (isset($error)) echo "<p>$error</p>";
		?>
<br />

<form action="<?php echo $file_post?>" method="post">

<table>
<tr>
<td align="right" valign="top"><strong>From:</strong></td>
<td align="left">
<?php echo $icmsUser->getVar('name')?$icmsUser->getVar('name'):$icmsUser->getVar('uname'); ?><BR>	
<input type="text" name="email" value="<?php echo $icmsUser->getVar('email'); ?>" size="40" maxlength="80">
<input type="hidden" name="realname" value="<?php echo $icmsUser->getVar('name')?$icmsUser->getVar('name'):$icmsUser->getVar('uname'); ?>">
</td>
</tr>
<tr>
<td align="right"><strong><?php echo $text_header["subject"] ?></strong></td>
<td><input type="text" name="subject" value="<?php echo htmlentities(stripslashes($subject));?>" size="40" maxlength="120" /></td>
</tr>
</table>
<br />

<table>
<tr><td><strong><?php echo $text_post["message"];?></strong><br />
<textarea id="body" name="body" rows="20" cols="79" wrap="physical"><?php if(isset($bodyzeile)) echo stripslashes($bodyzeile); ?></textarea>
</td></tr>
<tr><td>
<input type="submit" value="<?php echo $text_post["button_post"];?>" />
<input type="button" value="<?php echo $text_post["button_cancel"];?>" onClick="javascript:history.back();" />
		<?php
			echo "\r\n<script type='text/javascript'>\n";
			echo "<!--\n";
			echo "var sig='".$icmsUser->getVar("user_sig")."';\n";
			echo "//--></script>\n";
			if ($icmsUser->getVar("attachsig")) echo 'xoopsCodeSmilie("body",sig)';
		?>
<input type='button' value='<?php echo $text_post["add_signature"];?>' onclick='xoopsCodeSmilie("body",sig);'>
</td>
</tr>
</table>
<input type="hidden" name="type" value="post" />
<input type="hidden" name="newsgroups" value="<?php echo $newsgroups; ?>" />
<input type="hidden" name="references" value="<?php echo htmlentities($references); ?>" />
<input type="hidden" name="group" value="<?php echo $mygroup; ?>" />
<input type="hidden" name="group_id" value="<?php echo $group_id; ?>" />
</form>

<?php } } ?>

<?php include_once "tail.inc"; ?>