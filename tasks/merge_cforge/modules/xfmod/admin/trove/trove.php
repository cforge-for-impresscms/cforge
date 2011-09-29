<?php
	if (!eregi("admin.php", $_SERVER['PHP_SELF']))
	{
		die("Access Denied");
	}
	if ($icmsUser->isAdmin($icmsModule->mid()))
	{
		 
		/***********
		* Trove Administration
		*/
		function TroveAdd()
		{
			global $icmsDB;
			 
			site_admin_header();
			 
			echo "<H4>Add New Trove Category</H4>\n" ."<form action='admin.php' method='post'>\n" ."<p>Parent Category:" ."<br /><select name='parent'>";
			 
			// generate list of possible parents
			$res_cat = $icmsDB->query("SELECT shortname,fullname,trove_cat_id FROM ".$icmsDB->prefix("xf_trove_cat"));
			while ($row_cat = $icmsDB->fetchArray($res_cat))
			{
				echo "<option value='".$row_cat["trove_cat_id"]."'>".$row_cat['fullname']."\r\n";
			}
			 
			echo "</select>" ."<p>New category short name(no spaces, unix-like):" ."<br><input type='text' name='shortname'>" ."<p>New category full name(VARCHAR 80):" ."<br><input type='text' name='fullname'>" ."<p>New category description(VARCHAR 255):" ."<br><input type='text' size='80' name='description'>" ."<input type='hidden' name='fct' value='trove'>" ."<input type='hidden' name='op' value='TroveInsert'>" ."<br><input type='submit' name='submit' value='Add'>" ."</form>" .myTextForm(ICMS_URL."/modules/xfmod/admin.php?fct=trove", "Cancel");
			 
			site_admin_footer();
		}
		 
		function TroveInsert($shortname, $fullname, $description, $parent)
		{
			global $feedback, $icmsDB;
			 
			$newroot = trove_getrootcat($parent);
			 
			if ($shortname)
			{
				$res = $icmsDB->queryF("INSERT INTO ".$icmsDB->prefix("xf_trove_cat")."(shortname,fullname,description,parent,version,root_parent) VALUES(" ."'$shortname'," ."'$fullname'," ."'$description'," ."'$parent'," ."'".date("Ymd", time())."01'," ."'$newroot')");
				 
				if (!$res)
				{
					echo "Error In Trove Operation, ".$icmsDB->error();
					exit;
				}
			}
			 
			// update full paths now
			trove_genfullpaths($newroot, trove_getfullname($newroot), $newroot);
			TroveList();
			//redirect_header(ICMS_URL."/modules/xfmod/admin.php?fct=trove", 2, "Trove category added");
		}
		 
		function TroveEdit($trove_cat_id)
		{
			global $icmsDB;
			 
			$res_cat = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix("xf_trove_cat")." WHERE trove_cat_id=$trove_cat_id");
			if ($icmsDB->getRowsNum($res_cat) < 1)
			{
				redirect_header(ICMS_URL."/modules/xfmod/admin.php?fct=trove", 4, "No Such Category, That trove cat does not exist");
				exit;
			}
			$row_cat = $icmsDB->fetchArray($res_cat);
			 
			site_admin_header();
			 
			echo "<H4>Edit Trove Category</H4>" ."<form action='admin.php' method='post'>" ."<p>Parent Category:" ."<br><select name='parent'>";
			 
			// generate list of possible parents
			$res_parent = $icmsDB->query("SELECT shortname,fullname,trove_cat_id FROM ".$icmsDB->prefix("xf_trove_cat"));
			echo "<option value='0'".($row_cat["parent"] == 0?" selected":"").">[ ROOT ]\n";
			while ($row_parent = $icmsDB->fetchArray($res_parent))
			{
				echo "<option value='".$row_parent["trove_cat_id"]."'";
				 
				if ($row_cat["parent"] == $row_parent["trove_cat_id"])
				echo " selected";
				 
				echo ">".$row_parent["fullname"]."\r\n";
			}
			 
			echo "</select>" ."<input type='hidden' name='trove_cat_id' value='".$GLOBALS['trove_cat_id']."'>" ."<p>New category short name(no spaces, unix-like):" ."<br><input type='text' name='shortname' value='".$row_cat["shortname"]."'>" ."<p>New category full name(VARCHAR 80):" ."<br><input type='text' name='fullname' value='".$row_cat["fullname"]."'>" ."<p>New category description(VARCHAR 255):" ."<br><input type='text' name='description' size='80' value='".$row_cat["description"]."'>" ."<br>" ."<input type='hidden' name='fct' value='trove'>" ."<input type='hidden' name='op' value='TroveSave'>" ."<input type='submit' name='submit' value='Update'>" ."</form>" .myTextForm(ICMS_URL."/modules/xfmod/admin.php?fct=trove", "Cancel");
			 
			site_admin_footer();
		}
		 
		function TroveSave($trove_cat_id, $shortname, $fullname, $description, $parent)
		{
			global $feedback, $icmsDB;
			$newroot = trove_getrootcat($parent);
			 
			if ($shortname)
			{
				$sql = "UPDATE ".$icmsDB->prefix("xf_trove_cat")." SET " ."shortname='$shortname'," ."fullname='$fullname'," ."description='$description'," ."parent='$parent'," ."version='".date("Ymd", time())."01'," ."root_parent='$newroot' " ."WHERE trove_cat_id='$trove_cat_id'";
				 
				$res = $icmsDB->queryF($sql, NULL, NULL, NULL, __FILE__, __LINE__);
				 
				if (!$res)
				{
					echo $icmsDB->error();
					exit;
				}
			}
			// update full paths now
			trove_genfullpaths($newroot, trove_getfullname($newroot), $newroot);
			TroveList();
			//redirect_header(ICMS_URL."/modules/xfmod/admin.php?fct=trove", 2, "Trove category has been updated");
		}
		 
		function TroveList()
		{
			site_admin_header();
			 
			echo "<H4>Browse Trove Tree</H4>\n" ."[ <a href='admin.php?fct=trove&op=TroveAdd'>Add Trove Category</a> ]<br />\n";
			 
			TroveList_printnode(0, "root");
			 
			site_admin_footer();
		}
		 
		// GLOBAL FUNCTION
		 
		// print current node, then all subnodes
		function TroveList_printnode($nodeid, $text)
		{
			global $icmsDB;
			echo "<BR>";
			 
			if (!isset($GLOBALS['depth']))
			$GLOBALS['depth'] = 0;
			 
			for($i = 0; $i < $GLOBALS['depth']; $i++)
			{
				echo "&nbsp; &nbsp; ";
			}
			 
			echo "<img src='".ICMS_URL."/modules/xfmod/images/ic/cfolder15.png' width='15' height='13' alt=''>";
			echo "&nbsp; ".$text." ";
			echo "[ <A href='admin.php?fct=trove&op=TroveEdit&trove_cat_id=".$nodeid."'>Edit</a> ]";
			 
			$GLOBALS['depth']++;
			$res_child = $icmsDB->query("SELECT trove_cat_id,fullname " ."FROM ".$icmsDB->prefix("xf_trove_cat")." WHERE parent='$nodeid'");
			 
			while ($row_child = $icmsDB->fetchArray($res_child))
			{
				TroveList_printnode($row_child["trove_cat_id"], $row_child["fullname"]);
			}
			$GLOBALS['depth']--;
		}
		 
		 
	}
	else
	{
		echo "Access Denied";
	}
	 
?>