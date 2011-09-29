<?php
	$modversion['name'] = _XF_ACCOUNT_NAME;
	$modversion['version'] = 0.0207;
	$modversion['description'] = _XF_ACCOUNT_DESC;
	$modversion['author'] = "Arjen van Efferen (http://xoopsforge.mediacom4.net)";
	$modversion['credits'] = "The XOOPS Project";
	$modversion['help'] = "";
	$modversion['license'] = "GPL";
	$modversion['official'] = 0;
	$modversion['image'] = "images/xfaccount.png";
	$modversion['dirname'] = "xfaccount";
	 
	$modversion['templates'][1]['file'] = 'xfaccount_index.html';
	$modversion['templates'][1]['description'] = '';
	$modversion['templates'][2]['file'] = 'xfaccount_header.html';
	$modversion['templates'][2]['description'] = '';
	$modversion['templates'][3]['file'] = 'xfaccount_diary.html';
	$modversion['templates'][3]['description'] = '';
	$modversion['templates'][4]['file'] = 'xfaccount_pubkeys.html';
	$modversion['templates'][4]['description'] = '';
	 
	// Blocks
	$modversion['blocks'][1]['file'] = "mypage.php";
	$modversion['blocks'][1]['name'] = "My Projects/Communities";
	$modversion['blocks'][1]['description'] = "List of projects and communities a user is a member of";
	$modversion['blocks'][1]['show_func'] = "b_myprojects_show";
	$modversion['blocks'][1]['template'] = 'xfaccount_block_myprojects.html';
	 
	$modversion['blocks'][2]['file'] = "mypage.php";
	$modversion['blocks'][2]['name'] = "Monitored File Modules";
	$modversion['blocks'][2]['description'] = "List of file modules a user monitors";
	$modversion['blocks'][2]['show_func'] = "b_myfiles_show";
	$modversion['blocks'][2]['template'] = 'xfaccount_block_myfiles.html';
	 
	// Admin things
	$modversion['hasAdmin'] = 0;
	 
	// Menu
	$modversion['hasMain'] = 1;
?>