<?php
	require_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/my.php");
	 
	function account_header($title = '')
	{
		 
		$acc_header = "
			<H2 style='text-align:left;'>".$title."</H2>
			<p><A href='".ICMS_URL."/modules/xfaccount/index.php'>"._XF_MY_MYACCOUNT."</a>
			| <A href='".ICMS_URL."/modules/xfaccount/diary.php'>"._XF_MY_DIARYNOTES."</a>
			| <A href='".ICMS_URL."/user.php'>"._XF_MY_PROFILE."</a>
			| <A href='".ICMS_URL."/viewpmsg.php'>"._XF_MY_INBOX."</a>
			| <A href='".ICMS_URL."/modules/xfjobs/editprofile.php'>"._XF_MY_SKILLPROFILE."</a>
			| <A href='".ICMS_URL."/modules/xfaccount/pubkeys.php'>"._XF_MY_MYPUBKEYS."</a></p>";
		 
		return $acc_header;
	}
	 
?>