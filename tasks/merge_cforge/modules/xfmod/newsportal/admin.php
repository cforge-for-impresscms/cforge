<?php
	include_once "../config.inc";
	include_once("../$file_newsportal");
	$title .= "News Portal Admin";
	include_once "../head.inc";
	 
	$groupname = $_REQUEST["groupname"];
	$action = $_REQUEST["action"];
	if ($groupname && $action)
	{
		$message = control_group($action, $groupname);
		if (substr($message, 0, 3) == "240")
		{
			echo "The newsgroup was successfully ";
			echo($action == "newgroup")?"added.":
			"removed.";
		}
		else
			{
			echo $message."\r\n<BR>";
		}
	}
?>
<form action="admin.php" method="POST">
Action: <select name="action">
<option value="newgroup">newgroup
<option value="rmgroup">rmgroup
</select>
<br>
Group Name: <input type="text" name="groupname">
<br>
<input type="submit" value="Submit">
</form>

<?php include_once "../tail.inc"; ?>