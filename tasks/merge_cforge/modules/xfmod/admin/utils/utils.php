<?php
	if (!eregi("admin.php", $_SERVER['PHP_SELF']))
	{
		die("Access Denied");
	}
	if ($icmsUser->isAdmin($icmsModule->mid()))
	{
		 
		function UtilsMain()
		{
			site_admin_header();
			 
			echo "<H4>Site Utilities</H4>" ."<p>" ."<UL>" ."<LI><A href='admin.php?fct=utils&op=SiteMailings'>XoopsForge Site Mailings Maintanance</a>" ."<LI><a href='admin.php?fct=utils&op=EditTable&unit=file+type&table=xf_frs_filetype&pk=type_id'>Add, Delete, or Edit File Types</a>" ."<LI><a href='admin.php?fct=utils&op=EditTable&unit=processor&table=xf_frs_processor&pk=processor_id'>Add, Delete, or Edit Processors</a>" ."<LI><a href='admin.php?fct=utils&op=EditTable&unit=script+type&table=xf_snippet_type&pk=type_id'>Add, Delete, or Edit Snippet Types</a>" ."<LI><a href='admin.php?fct=utils&op=EditTable&unit=script+language&table=xf_snippet_language&pk=type_id'>Add, Delete, or Edit Snippet Languages</a>" ."<LI><a href='admin.php?fct=utils&op=EditTable&unit=script+category&table=xf_snippet_category&pk=type_id'>Add, Delete, or Edit Snippet Categories</a>" ."</UL>";
			 
			site_admin_footer();
		}
		 
		function SiteMailings($pattern, $submit, $uid, $uname, $ok)
		{
			global $icmsDB;
			 
			if ($submit && $uid && $uname)
			{
				if (!$ok)
				{
					 
					/*
					Show form for unsubscription type selection
					*/
					site_admin_header();
					 
					echo "<H4>Unsubscribe user: ".$uname."</h4>" ."<p>" ."Are you sure you want to remove this user from all" ."automated mailings(like forum and file release notifications)?" ."</p>" ."<table><tr><td>" .myTextForm("admin.php?fct=utils&op=SiteMailings&pattern=".urlencode($pattern)."&submit=1&uid=".$uid."&uname=".$uname."&ok=1", _YES)
					."</td><td>" .myTextForm("admin.php?fct=utils&op=SiteMailings&pattern=".urlencode($pattern), _NO)
					."</td></tr></table>";
					 
					site_admin_footer();
					exit();
				}
				else
				{
					/*
					Perform unsubscription
					*/
					$res = $icmsDB->queryF("DELETE FROM ".$icmsDB->prefix("xf_filemodule_monitor")." WHERE user_id=".$uid);
					 
					if (!$res)
					{
						$feedback .= "Could not unsubscribe user from 'Filemodule Monitor' list<br />";
					}
					else
					{
						$feedback .= "User unsubscribed from 'Filemodule Monitor' list<br />";
					}
					 
					$res = $icmsDB->queryF("DELETE FROM ".$icmsDB->prefix("xf_forum_monitored_forums")." WHERE user_id=".$uid);
					if (!$res)
					{
						$feedback .= "Could not unsubscribe user from 'Forum Monitor' list";
					}
					else
					{
						$feedback .= "User unsubscribed from 'Forum Monitor' list";
					}
				}
			}
			 
			site_admin_header();
			 
			echo "<H4>Site Mailings Subscription Maintenance</H4>" ."<p>" ."Use a field below to find users which match a given pattern with " ."the Forge username, real name, or email address " ."(substring match is preformed, use '%' in the middle of pattern " ."to specify 0 or more arbitrary characters). Click on the username " ."to unsubscribe user from site mailings(new form will appear)." ."</p>" ."<form action='admin.php?fct=utils&op=SiteMailings' method='POST'>" ."Pattern: <input type='text' name='pattern' value='".$pattern."'><br />" ."<input type='submit' name='submit' value='Show users matching pattern'>" ."</form>";
			 
			if ($pattern)
			{
				$res = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("users")." " ."WHERE level <> 0 AND(uname LIKE '%$pattern%' " ."OR name LIKE '%$pattern%' " ."OR email LIKE '%$pattern%')");
				 
				echo "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<td><strong>UID</strong></td>" ."<td><strong>Username</strong></td>" ."<td><strong>Email</strong></td>" ."</tr>";
				 
				$i = 0;
				while ($row = $icmsDB->fetchArray($res))
				{
					echo "<th class='".($i++%2 > 0?'bg1':'bg3')."'>" ."<td>".$row['uid']."</td>" ."<td><a href='admin.php?fct=utils&op=SiteMailings&pattern=".urlencode($pattern)."&submit=1&uid=".$row['uid']."&uname=".$row['uname']."'>".$row['uname']."</a></td>" ."<td>".$row['email']."</td>" ."</th>";
				}
				 
				echo "</table>";
			}
			site_admin_footer();
		}
		 
		function EditTable($unit, $table, $pk, $func, $id)
		{
			site_admin_header();
			 
			echo "<H4>Edit ".ucwords($unit)."s</H4>" ."<p>";
			 
			$baseurl = "admin.php?fct=utils&op=EditTable&table=".$table."&pk=".$pk."&unit=".urlencode($unit);
			 
			switch($func)
			{
				case "add" :
				{
					admin_table_add($table, $unit, $pk, $baseurl);
					break;
				}
				case "postadd" :
				{
					admin_table_postadd($table, $unit, $pk, $baseurl);
					break;
				}
				case "confirmdelete" :
				{
					admin_table_confirmdelete($table, $unit, $pk, $id, $baseurl);
					break;
				}
				case "delete" :
				{
					admin_table_delete($table, $unit, $pk, $id, $baseurl);
					break;
				}
				case "edit" :
				{
					admin_table_edit($table, $unit, $pk, $id, $baseurl);
					break;
				}
				case "postedit" :
				{
					admin_table_postedit($table, $unit, $pk, $id, $baseurl);
					break;
				}
			}
			 
			echo admin_table_show($table, $unit, $pk, $baseurl);
			 
			site_admin_footer();
		}
		 
	}
	else
	{
		echo "Access Denied";
	}
	 
?>