<?php
	/**
	* $Id: login.php,v 1.8 2004/03/01 23:27:34 danreese Exp $
	* (c) 2004 Novell, Inc.
	*
	* Authenticates a user and creates a session for them.
	*/
	header('Content-type: text/xml');
	 
	// Extract username and password.
	$username = $_POST['u'];
	$password = $_POST['p'];
	if (empty($username) || empty($password))
		{
		// Invalid syntax.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 11;
		$args['contents'] = _XFWEBSERVICE_ERROR_011;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Verify user credentials.
	$member_handler = xoops_gethandler('member');
	$myts = MyTextsanitizer::getInstance();
	$user = $member_handler->loginUser(addslashes($myts->stripSlashesGPC($username)), addslashes($myts->stripSlashesGPC($password)));
	if (false == $user)
		{
		// Invalid username or password.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 101;
		$args['contents'] = _XFWEBSERVICE_ERROR_101;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Do some Xoops session processing.
	$_SESSION['xoopsUserId'] = $user->getVar('uid');
	$_SESSION['xoopsUserGroups'] = $user->getGroups();
	 
	// Return session ID.
	$tpl = TEMPLATE_CONTEXT.'session.tpl';
	$args['id'] = session_id();
	echo fill_template($tpl, $args);
?>