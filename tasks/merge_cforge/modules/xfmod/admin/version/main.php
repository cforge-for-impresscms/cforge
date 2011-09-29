<?php
	if (!eregi("admin.php", $_SERVER['PHP_SELF']))
	{
		die("Access Denied");
	}
	if ($icmsUser->isAdmin($icmsModule->mid()))
	{
		 
		include_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
		include_once("admin/admin_utils.php");
		 
		site_admin_header();
		echo "<H4>Latest XoopsForge Version</H4>";
		 
		$data = "";
		flush();
		$fp = @fopen("http://cforge.sourceforge.net/modules/xfmod/xoopsforge_version.php", "r");
		if ($fp)
		{
			$data = fgets($fp, 1024);
			fclose($fp);
		}
		 
		if (!strstr($data, "|"))
		{
			echo "Could not contact cforge.sourceforge.net. To use this feature, you must have compiled in fopen wrappers when setting up PHP.";
		}
		else
		{
			$ver_arr = explode("|", $data);
		?>
  <table align="center" border="0" cellspacing="0" cellpadding="3" class="box-table"><br/>
  <tr>
    <td valign="middle"><strong>Latest Version:</strong></th>
    <td valign="middle"><?php echo $ver_arr[0]; ?></td>
  </tr>
  <tr>
    <td valign="middle"><strong>Release Date:</strong></th>
    <td valign="middle"><?php echo $ver_arr[1]; ?></td>
  </tr>
  <tr>
    <td valign="middle"><strong>Download Locations:</strong></th>
    <td valign="middle"><?php
  $cnt = count($ver_arr);
  for($x=2; $x<$cnt; $x++) {
    $url = explode(",",$ver_arr[$x]);
    echo "<a href='".$url[1]."'>".$url[0]."</a>\n<br />";
  }
  ?>
  </td>
  </tr>
  </table>
		<?php
		}
		 
		site_admin_footer();
		 
	}
	else
	{
		echo "Access Denied";
	}
?>