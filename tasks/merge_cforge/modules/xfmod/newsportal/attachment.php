<?php
	require_once("../include/utils.php");
	header("Expires: ".gmdate("D, d M Y H:i:s", time()+(3600 * 24))." GMT");
	$url = explode("/", $PATH_INFO);
	$group = $url[1];
	$msg_id = $url[2];
	$attachment = $url[3];
	include_once "config.inc";
	require_once("$file_newsportal");
	if (!isset($attachment))
	$attachment = 0;
	$message = read_message($msg_id, $attachment, $group);
	if (!$message)
	{
		header("HTTP/1.0 404 Not Found");
		echo "The Attachment doesn't exists";
	}
	else
	{
		header("Content-type: ".$message->header->content_type[$attachment]);
		show_article("", $msg_id, $attachment, $message);
	}
?>