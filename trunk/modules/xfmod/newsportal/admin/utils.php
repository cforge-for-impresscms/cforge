<?php

include_once "../config.inc";
include_once("../$file_newsportal");

/*
 * cancel an article on the newsserver
 *
 * DO NOT USE THIS FUNCTION, IF YOU DON'T KNOW WHAT YOU ARE DOING!
 *
 * $von: The handler of the NNTP-Connection
 * $group: The group of the article
 * $id: the Number of the article inside the group or the message-id
 */
function article_cancel($subject,$from,$newsgroups,$ref,$body,$id) {
  global $server,$port,$send_poster_host,$organization,$text_error;
  global $file_footer;
  flush();
  $ns=OpenNNTPconnection($server,$port);
  if ($ns != false) {
    fputs($ns,"post\r\n");
    $weg=lieszeile($ns);
    fputs($ns,'Subject: '.quoted_printable_encode($subject)."\r\n");
    fputs($ns,'From: '.$from."\r\n");
    fputs($ns,'Newsgroups: '.$newsgroups."\r\n");
    fputs($ns,"Mime-Version: 1.0\r\n");
    fputs($ns,"Content-Type: text/plain; charset=ISO-8859-15\r\n");
    fputs($ns,"Content-Transfer-Encoding: 8bit\r\n");
    if ($send_poster_host)
      fputs($ns,'X-HTTP-Posting-Host: '.gethostbyaddr(getenv("REMOTE_ADDR"))."\r\n");
    if ($ref!=false) fputs($ns,'References: '.$ref."\r\n");
    if (isset($organization))
      fputs($ns,'Organization: '.quoted_printable_encode($organization)."\r\n");
    fputs($ns,"Control: cancel ".$id."\r\n");
    if ((isset($file_footer)) && ($file_footer!="")) {
      $footerfile=fopen($file_footer,"r");
      $body.="\n".fread($footerfile,filesize($file_footer));
      fclose($footerfile);
    }
    $body=str_replace("\n.\r","\n..\r",$body);
    $body=str_replace("\r",'',$body);
    $b=split("\n",$body);
    $body="";
    for ($i=0; $i<count($b); $i++) {
      if ((strpos(substr($b[$i],0,strpos($b[$i]," ")),">") != false ) | (strcmp(substr($b[$i],0,1),">") == 0)) {
        $body .= textwrap(stripSlashes($b[$i]),78,"\r\n")."\r\n";
      } else {
        $body .= textwrap(stripSlashes($b[$i]),74,"\r\n")."\r\n";
      }
    }
    fputs($ns,"\r\n".$body."\r\n.\r\n");
    $message=lieszeile($ns);
    closeNNTPconnection($ns);
  } else {
    $message=$text_error["post_failed"];
  }
  return $message;
}

/*
 * send a newgroup or rmgroup control message to the news server
 *
 * DO NOT USE THIS FUNCTION, IF YOU DON'T KNOW WHAT YOU ARE DOING!
 *
 * $von: The handler of the NNTP-Connection
 * $groupname: The full name of the newsgroup to be acted upon
 * $id: the Number of the article inside the group or the message-id
 */
function control_group($action, $groupname) {
  global $xoopsForge;
  global $server,$port,$send_poster_host,$organization,$text_error;
  global $control_from,$control_approve;
  global $file_footer;
  flush();
  $ns=OpenNNTPconnection($server,$port);
  if ($ns != false) {
    fputs($ns,"post\r\n");
    $weg=lieszeile($ns);
	
    $parts[] = 'From: '.$control_from;
    $parts[] = 'Newsgroups: '.$groupname;
    $parts[] = 'Subject: '.quoted_printable_encode("cmsg newgroup ".$groupname);
	$date = date("r");
	$parts[] = 'Date: '.$date;//RFC 822 formatted date
	$parts[] = 'Organization: '.$organization;
    $parts[] = 'Control: '.$action." ".$groupname;
    $parts[] = 'Approved: '.$control_approve;
	$message_id = '<'.time().'.'.rand(10000,99999).'@novell.com>';
	$parts[] = 'Message-ID: '.$message_id;

	$sign = "$message_id\n$action $groupname";//the data to sign must be small enough for the rsa key
	$command = "echo \"$sign\" | ".$xoopsForge['openssl_path']." rsautl -sign -inkey ".$xoopsForge['privkey_path']." | ".$xoopsForge['uuencode_path']." signature";
	$signature = shell_exec($command);	
	foreach($parts as $part){
		fputs($ns, $part."\r\n");
	}
	fputs($ns, "\r\n");//end the header and start the body
	fputs($ns, $signature);//send the signature as the body of the message
	fputs($ns, "\r\n.\r\n");			

    $message=lieszeile($ns);
    closeNNTPconnection($ns);
  } else {
    $message=$text_error["post_failed"];
  }
  return $message;
}

function validate_forum_name($name) {
	// no spaces
	if (strrpos($name,' ') > 0) {
		$GLOBALS['register_error'] = "There cannot be any spaces in the forum name.";	
		return false;
	}

	// min and max length
	if (strlen($name) < 2) {
		$GLOBALS['register_error'] = "Name is too short. It must be at least 2 characters.";
		return false;
	}
	if (strlen($name) > 40) {
		$GLOBALS['register_error'] = "Name is too long. It must be less than 40 characters.";
		return false;
	}
	
	//valid characters
	if (!ereg('^[a-z][-a-z0-9_]+$', $name)) {
		$GLOBALS['register_error'] = "The name may only contain Letters, Numbers, Dashes, or Underscores.";
		return false;
	}
		
	return true;
}

?>