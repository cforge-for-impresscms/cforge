<?php
  include_once "config.inc";
  require_once("../../../mainfile.php");
  require_once("../include/utils.php");
  
  // register parameters
  $group_id=$_REQUEST["group_id"];
  $group=$_REQUEST["group"];

  include_once "$file_newsportal";
  $ns=OpenNNTPconnection($server,$port);
  $msg_id=getLastArticle($ns, $group);
  $feeds=array();
  for($i=0;$i<10;$i++){
	  $message=read_message(($msg_id-$i),0,$group);
	  if (!$message) {
		break;
	  } else {
		$feeds[$i]['title']=$message->header->subject;
		$feeds[$i]['description']=$message->body[0];
		$feeds[$i]['author']=$message->header->from." (".$message->header->name.")";
		$feeds[$i]['link']=XOOPS_URL."/modules/xfmod/newsportal/article.php?group_id=$group_id&msg_id=".($msg_id-$i)."&group=$group";
		$feeds[$i]['pubDate']=date("r",$message->header->date);
		$feeds[$i]['comments']=$feeds[$i]['link'];
	  }
	  
  }
  $sql = "SELECT group_name, short_description FROM ".$xoopsDB->prefix("xf_groups")." WHERE group_id=".$group_id;
  $result = $xoopsDB->query($sql);
  list($project_name, $project_desc) = $xoopsDB->fetchRow($result);

  $sql = "SELECT forum_desc_name FROM ".$xoopsDB->prefix("xf_forum_nntp_list")." WHERE forum_name='".$group."'";
  $result = $xoopsDB->query($sql);
  list($forum_name) = $xoopsDB->fetchRow($result);
  header("Content-Type: text/xml");
  echo '<?xml version="1.0" ?>';
  echo '<rss version="2.0">';
  echo '<channel>';
  echo '<title>Novell Forge : Project - '.$project_name.' : Forum - '.$forum_name.'</title>';
  echo '<link>'.XOOPS_URL.'/modules/xfmod/newsportal/?group_id='.$group_id.'</link>';
  echo '<description>'.htmlspecialchars($project_desc).'</description>';
  echo '<copyright>Novell, Inc.   See terms at http://www.novell.com/company/legal/</copyright>';
  echo '<lastBuildDate>'.date("r",time()).'</lastBuildDate>';
  echo '<generator>Novell Forge - forge.novell.com</generator>';
	
  if(count($feeds)>0){
	  foreach($feeds as $feed){
		echo "<item>";
		foreach($feed as $key => $value){
			echo "<$key>".htmlspecialchars(utf8_encode($value))."</$key>";		
		}
		echo "</item>";
	  }
  }else{
	  echo '<item><title>No Available Articles</title><description>There are no articles in this forum yet.</description>';
	  echo '<link>'.XOOPS_URL.'modules/xfmod/newsportal/?group_id='.$group_id.'</link></item>';
  }
echo '</channel></rss>';
?>