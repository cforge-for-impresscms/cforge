<?php
require_once(XOOPS_ROOT_PATH."/modules/xfmod/language/english/my.php");

function account_header($title='') {

	$acc_header = "
		<H2 style='text-align:left;'>".$title."</H2>
		<P><A href='".XOOPS_URL."/modules/xfaccount/index.php'>"._XF_MY_MYACCOUNT."</A> 
		 | <A href='".XOOPS_URL."/modules/xfaccount/diary.php'>"._XF_MY_DIARYNOTES."</A>
		 | <A href='".XOOPS_URL."/user.php'>"._XF_MY_PROFILE."</A>
		 | <A href='".XOOPS_URL."/viewpmsg.php'>"._XF_MY_INBOX."</A>
		 | <A href='".XOOPS_URL."/modules/xfjobs/editprofile.php'>"._XF_MY_SKILLPROFILE."</A>
		 | <A href='".XOOPS_URL."/modules/xfaccount/pubkeys.php'>"._XF_MY_MYPUBKEYS."</A></P>";
	
	return $acc_header;
}

?>