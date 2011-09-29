<?php
include_once ("../../../../mainfile.php");
if(!$file_id){ 
        redirect_header(XOOPS_URL."/",4,"Invalid File");
        exit;
}
if(isset($_POST['submit']) && isset($_POST['targets'])){
	foreach($targets as $target){
		$sql = "INSERT INTO ".$xoopsDB->prefix('xf_frs_target')." (file_id, target) VALUES ($file_id, '$target')";
		$xoopsDB->queryF($sql);			
	}
	$xml = callservice($file_id);
	if(preg_match("/<publish id=\"(\d+)\"/",$xml,$matches)){
		redirect_header("status.php?id=".$matches[1],0,"");
	}else if(preg_match("/<error id=\"\d+\">(.*)<\/error>/",$xml,$matches)){
		echo "There was an error trying to publish your file: ".$matches[1];
	}else{
		echo "display error: $xml";	
	}
}else{
	$targets = array();
	$targets['fedora-1-i386'] = "Fedora 1.0";
	$targets['mandrake-92-i586'] = "Mandrake 9.2";
	$targets['redhat-9-i386'] = "Redhat 9.0";
	$targets['suse-82-i586'] = "SuSE 8.2";
	$targets['suse-90-i586'] = "SuSE 9.0";

	$sql = "SELECT target FROM ".$xoopsDB->prefix('xf_frs_target')." WHERE file_id=$file_id";
	$result = $xoopsDB->query($sql);
	while(list($target) = $xoopsDB->fetchRow($result)){
		$file_targets[] = $target;
	}
	?>
	<form method='POST' action='publish.php'>
		Please select the Red Carpet distribution channels to which you would like to publish your file.<br><br>
		<input type='hidden' name='file_id' value='<?php echo $file_id ?>'>
		<select name='targets[]' multiple>
			<?php
			foreach($targets as $key => $value){
				echo "<option value='$key'>$value</option>";
			}
			?>
		</select><br><br>
		<input type='submit' name='submit' value='Publish'> <input type='button' value='Cancel' onClick='javascript: window.close()'>
	</form>
	<?php	
}	

function callservice($file_id){
    $host = "forge.novell.com";
    $port = 80;
    $path = "/api/publish/";
    
    $poststring = "c=start&f=$file_id&s=".session_id();
    $fp = fsockopen($host, $port, $errno, $errstr, $timeout = 30);
    
    if(!$fp){
     return "<error id=\"$errno\">$errstr</error>";
    }else{
      fputs($fp, "POST $path HTTP/1.1\r\n");
      fputs($fp, "Host: $host\r\n");
      fputs($fp, "Content-type: application/x-www-form-urlencoded\r\n");
      fputs($fp, "Content-length: ".strlen($poststring)."\r\n");
      fputs($fp, "Connection: close\r\n\r\n");
      fputs($fp, $poststring . "\r\n\r\n");
    
	  $return = "";
      while(!feof($fp)) {
      	$return .= fgets($fp, 4096);
      }
      //close fp - we are done with it
      fclose($fp);
    }
    $return = split("\r\n\r\n",$return,2);
	return $return[1];
}

?>