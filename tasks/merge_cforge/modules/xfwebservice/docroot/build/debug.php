<?php
	/**
	* $Id: debug.php,v 1.8 2004/03/01 23:27:35 danreese Exp $
	* (c) 2004 Novell, Inc.
	*
	* Provides a method to debug problems with the build master.
	*/
	 
	// Extract method name and parameters.
	$method = $_REQUEST['m'];
	$param_1 = $_REQUEST['p1'];
	$param_2 = $_REQUEST['p2'];
	if (empty($method))
		{
		// Invalid syntax.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 11;
		$args['contents'] = _XFWEBSERVICE_ERROR_011;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Check authorization.
	$perm = get_permissions(session_id(), 'xoopsforge');
	if (!$perm || !$perm->isSuperUser())
		{
		// Not authorized.
		$tpl = TEMPLATE_CONTEXT.'error.tpl';
		$args['id'] = 12;
		$args['contents'] = _XFWEBSERVICE_ERROR_012;
		echo fill_template($tpl, $args);
		return;
	}
	 
	// Prepare XML-RPC parameters.
	define("XMLRPC_DEBUG", 1);
	require_once '../xmlrpc.php';
	if (!empty($param_1))
		{
		$params[] = XMLRPC_prepare($param_1);
	}
	if (!empty($param_2))
		{
		$params[] = XMLRPC_prepare($param_2);
	}
	 
	// Send XML-RPC message.
	list($success, $response) = XMLRPC_request(BUILD_HOST, BUILD_PATH, $method, $params);
	 
	// Display full debug info.
	XMLRPC_debug_print();
?>