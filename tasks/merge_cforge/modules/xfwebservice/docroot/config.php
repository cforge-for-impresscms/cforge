<?php
	/**
	* $Id: config.php,v 1.8 2004/04/08 17:52:56 danreese Exp $
	* (c) 2004 Novell, Inc.
	*
	* Web service configuration information.
	*/
	 
	// Include Forge functionality and language strings.
	include_once '../../../../mainfile.php';
	 
	// Include language strings.
	define ('_XFWEBSERVICE_LANGUAGE_CONTEXT', '../../language/');
	if (file_exists(_XFWEBSERVICE_LANGUAGE_CONTEXT.$icmsConfig['language'].'/error.php'))
		{
		include_once _XFWEBSERVICE_LANGUAGE_CONTEXT.$icmsConfig['language'].'/error.php';
	}
	else
	{
		include_once _XFWEBSERVICE_LANGUAGE_CONTEXT.'english/error.php';
	}
	 
	// Define web service constants.
	define('TEMPLATE_CONTEXT', '../../template/');
	define('BUILD_PATH', '/RPC2');
	define('BUILD_QUOTA', 6);
	 
	// CONFIGURE: These need to be set to the correct values.
	define('BUILD_HOST', 'build.master.host.name:8080');
	define('BUILD_CVS_USER', 'cvsuser');
?>