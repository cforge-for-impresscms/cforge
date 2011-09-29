<?php
	 
	function maillist_create_mailing_list($listname, $owner_email, $password)
	{
		system(ICMS_ROOT_PATH."/modules/xfmod/bin/newmailinglist $listname $owner_email $password" , $retval);
		return $retval;
	}
	 
	function maillist_delete_mailing_list($listname)
	{
		system(ICMS_ROOT_PATH."/modules/xfmod/bin/rmmailinglist $listname" , $retval);
		return $retval;
	}
	 
	function maillist_validate_listname($name)
	{
		// no spaces
		if (strrpos($name, ' ') > 0)
		{
			return "There cannot be any spaces in the list name.";
		}
		// min and max length
		if (strlen($name) < 3)
		{
			return "Name is too short. It must be at least 3 characters.";
		}
		if (strlen($name) > 15)
		{
			return "Name is too long. It must be less than 16 characters.";
		}
		if (!ereg('^[a-z][a-z0-9_]+$', $name))
		{
			return "Illegal character in name.  You may only use letters, numbers, and the underscore character.";
		}
		 
	}
	 
	function maillist_add(&$project, $listname, $listdesc)
	{
		global $icmsDB, $icmsUser, $icmsForge;
		 
		if ((maillist_count($project->getID()) >= $icmsForge['max_maillists']) && (!$icmsUser->isAdmin()))
		{
			return "You may only create ".$icmsForge['max_maillists']." mailing lists for your project." ."  If you would like to create more please contact the site administrator";
		}
		$maillist_query_result = $icmsDB->query("SELECT u.uname,u.email " ."FROM ".$icmsDB->prefix("users")." u,".$icmsDB->prefix("xf_user_group")." ug " ."WHERE u.uid=ug.user_id " ."AND ug.group_id=".$project->getID()." " ."AND ug.admin_flags='A'");
		 
		if ($icmsDB->getRowsNum($maillist_query_result) > 0)
		{
			// The user has an email so we can make mailing lists
			$maillist_row = $icmsDB->fetchArray($maillist_query_result);
			 
			$retval = maillist_create_mailing_list($project->getUnixName()."-".$listname,
				$maillist_row['email'],
				$project->getUnixName()."-".$listname."-passwd");
			 
			if ($retval == 0)
			{
				$sql = "INSERT INTO ".$icmsDB->prefix("xf_maillists")." VALUES(0, ".$project->getID().", '$listname', '$listdesc')";
				$icmsDB->queryF($sql);
				maillist_send_confirmation($listname, $listdesc, $project->getUnixName(), $maillist_row['uname'], $maillist_row['email'], $project->isProject());
			}
			else
				{
				return "Mailman could not create the mailing list.";
			}
		}
		else
		{
			return "Could not find a valid admin user.";
		}
		 
	}
	 
	function maillist_delete(&$project, $id)
	{
		global $icmsDB;
		 
		$sql = "SELECT name FROM ".$icmsDB->prefix("xf_maillists")." WHERE id=$id";
		$result = $icmsDB->query($sql);
		list($suffix) = $icmsDB->fetchRow($result);
		if (!$suffix) return "Could not find list $id";
		$retval = maillist_delete_mailing_list($project->getUnixName()."-".$suffix);
		if ($retval == 0)
		{
			$sql = "DELETE FROM ".$icmsDB->prefix("xf_maillists")." WHERE id=$id";
			$icmsDB->queryF($sql);
		}
		else
		{
			return "Mailman could not remove the mailing list.";
		}
	}
	 
	function maillist_count($group_id)
	{
		global $icmsDB;
		 
		$sql = "SELECT count(id) FROM ".$icmsDB->prefix("xf_maillists")." WHERE group_id=$group_id";
		$result = $icmsDB->query($sql);
		list($count) = $icmsDB->fetchRow($result);
		return $count;
	}
	 
	function maillist_get_admin_path()
	{
		$rv = "/mailman/admin/";
		return $rv;
	}
	 
	function maillist_get_listinfo_path()
	{
		$rv = "/mailman/listinfo/";
		return $rv;
	}
	 
	function maillist_get_options_path()
	{
		$rv = "/mailman/options/";
		return $rv;
	}
	 
	function maillist_get_archive_path()
	{
		$rv = "/pipermail/";
		return $rv;
	}
	 
	function maillist_get_subscribe_path()
	{
		$rv = "/mailman/subscribe/";
		return $rv;
	}
	 
	function maillist_send_confirmation($listname, $listdesc, $unixname, $owner, $owner_email, $is_project = true)
	{
		global $icmsConfig;
		$site = $icmsConfig['sitename'];
		$prjtype = ($is_project?"project":"community");
		 
		$msg_body = $owner . ":\n\nThank you for registering your new $prjtype, $unixname" . ", with $site.\n\n" . "The following mailing list has been created for your $prjtype:\n\n";
		$msg_body .= "* " . $unixname . "-" . $listname . "@" . $_SERVER['SERVER_NAME'] . " - " . $listdesc . "\r\n";
		$msg_body .= "\r\nThe password for your mailing list is the name of the list followed by \"-passwd\", i.e. " . "for the " . $unixname . "-" . $listname . " mailing list, the password is " . $unixname . "-" . $listname . "-passwd.  " . "You will need your password to manage each mailing list.  " . "You are advised to change the password immediately.\n\n" . "You can configure your mailing list at the following web page:\n\n";
		$msg_body .= ICMS_URL . maillist_get_admin_path() . $unixname . "-" . $listname . " \n";
		$msg_body .= "\r\nThe web page for users of your mailing lists is:\n\n";
		$msg_body .= ICMS_URL . maillist_get_listinfo_path() . $unixname . "-" . $listname . " \n";
		$msg_body .= "\r\nYou can even customize this web page from the list configuration page.  " . "However, you do need to know HTML to be able to do this.\n\n" . "You may link you mailing list to an nntp forum.  This allows your users to view your " . "list in multiple ways.  For more information on how to do this see the How Do I section " . "about forums on the forge.novell.com website\n\n." . "There is also an email-based interface for users(not administrators) of your list; " . "You can get info about using a list by sending a message with just the word `help' as subject or in the body, to:\n\n" . $unixname . "-" . $listname . "-request@" . $_SERVER['SERVER_NAME'] . "\r\n" . "To unsubscribe a user: from the mailing list 'listinfo' web page, " . "click on or enter the user's email address as if you were that user.  " . "Where that user would put in their password to unsubscribe, put in " . "your admin password.  You can also use your password to change " . "member's options, including digestification, delivery disabling, etc.\n\n" . "Please address all questions to mailman-owner@" . $_SERVER['SERVER_NAME'] . ".\n\n";
		 
		$icmsMailer = $icmsMailer = getMailer();
		$icmsMailer->setToEmails($owner_email);
		$icmsMailer->setSubject("Mailing Lists Created for your $prjtype " . $unixname);
		$icmsMailer->setBody($msg_body);
		$icmsMailer->useMail();
		$icmsMailer->send();
		//xoops_mail($owner_email,"Mailing Lists Created for your $prjtype " . $unixname,$msg_body);
	}
	 
	function maillist_subscribe($user, $db, $mailserver, $list, $listid, $email, $pwd, $digest)
	{
		//xoops_mail($list."-request@".$mailserver, "subscribe ".$pwd." nodigest address=".$email, "");
		if ($digest)
		{
			$digest = 'digest';
		}
		else
		{
			$digest = 'nodigest';
		}
		$icmsMailer = $icmsMailer = getMailer();
		$icmsMailer->setToEmails($list."-request@".$mailserver);
		$icmsMailer->setSubject("subscribe ".$pwd." ".$digest." address=".$email);
		$icmsMailer->setBody("Please Subscribe Me");
		$icmsMailer->useMail();
		$icmsMailer->send();
		if ($user && $db)
		{
			$sql = "INSERT INTO " . $db->prefix("xf_maillist_site_subscriptions") . " VALUES('" . $user->getVar("uid") . "','" . $listid . "')";
			$result = $db->query($sql);
			if (! result)
			{
				return false;
			}
			else
				{
				return true;
			}
		}
		else
		{
			return true;
		}
	}
	 
	function maillist_unsubscribe($user, $db, $mailserver, $list, $listid, $email, $pwd)
	{
		//xoops_mail($list."-request@".$mailserver, "unsubscribe ".$pwd." ".$email, "");
		$icmsMailer = $icmsMailer = getMailer();
		$icmsMailer->setToEmails($list."-request@".$mailserver);
		$icmsMailer->setSubject("unsubscribe ".$pwd." ".$email);
		$icmsMailer->setBody("Please Unsubscribe Me");
		$icmsMailer->useMail();
		$icmsMailer->send();
		$sql = "DELETE FROM ".$db->prefix("xf_maillist_site_subscriptions")." WHERE uid='".$user->getVar("uid")."' AND list_id='".$listid."'";
		$result = $db->query($sql);
		if (! result)
		{
			return false;
		}
		else
		{
			return true;
		}
	}
	 
?>