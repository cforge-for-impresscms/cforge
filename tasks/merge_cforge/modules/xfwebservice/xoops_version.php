<?php
	/**
	* $Id: xoops_version.php,v 1.8 2004/04/08 22:19:05 danreese Exp $
	* (c) 2004 Novell, Inc.
	*
	* Web service module configuration.
	*/
	 
	// Basic Information
	$modversion['name'] = _XFWEBSERVICE_NAME;
	$modversion['version'] = 0.0207;
	$modversion['description'] = _XFWEBSERVICE_DESC;
	$modversion['author'] = 'Dan Reese (forge.novell.com)';
	$modversion['credits'] = 'The XOOPS Project';
	$modversion['help'] = '';
	$modversion['license'] = 'GPL';
	$modversion['official'] = 0;
	$modversion['image'] = 'xf_slogo.gif';
	$modversion['dirname'] = 'xfwebservice';
	 
	// Menu
	$modversion['hasAdmin'] = 0;
	$modversion['hasMain'] = 0;
	 
	// Database
	// NOTE: Tables shouldn't include prefix and SQL must be compatible with phpMyAdmin or phpPgAdmin.
	$modversion['sqlfile']['mysql'] = "sql/mysql.sql";
	$modversion['tables'][0] = "xf_webservice_build";
	$modversion['tables'][1] = "xf_webservice_publish";
?>