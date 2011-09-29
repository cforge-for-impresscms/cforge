<?php
	$title = "Newsportal - NNTP<->HTTP Gateway";
	 
	include_once "head.inc";
	include_once "config.inc";
	 
	require("$file_newsportal");
	flush();
	$ns = OpenNNTPconnection($server, $port);
	 
	if ($ns != false)
	{
	?>
	< pre >
	<?php
		 $head = readPlainHeader($ns, $group, $id);
		for($i = 0; $i < count($head); $i++)
		echo $head[$i]."\r\n";
		$body = readMessage($ns, $id, "");
		for($i = 0; $i < count($body); $i++)
		echo $body[$i]."\r\n";
	?>
	< /pre >
	<?php
	}
	closeNNTPconnection($ns);
	 
	include_once "tail.inc";
?>