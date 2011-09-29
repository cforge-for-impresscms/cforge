<?php
include_once "../config.inc";
include_once("../$file_newsportal");
$title.= "News Portal Admin";
include_once "../head.inc";

$groupname=$_REQUEST["groupname"];
$action=$_REQUEST["action"];
if($groupname && $action){
	$message = control_group($action, $groupname);
    if (substr($message,0,3)=="240") {
		echo "The newsgroup was successfully ";
		echo ($action=="newgroup")?"added.":"removed.";
	}else{
		echo $message."\n<BR>";
	}
}
?>
<FORM action="admin.php" method="POST">
Action: <SELECT name="action">
<OPTION value="newgroup">newgroup
<OPTION value="rmgroup">rmgroup
</SELECT>
<br>
Group Name: <INPUT type="text" name="groupname">
<br>
<INPUT type="submit" value="Submit">
</FORM>

<?php include_once "../tail.inc"; ?>