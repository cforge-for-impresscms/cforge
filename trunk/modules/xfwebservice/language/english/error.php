<?php
/**
 * $Id: error.php,v 1.24 2004/04/08 17:53:16 danreese Exp $
 * (c) 2004 Novell, Inc.
 *
 * Error description resource strings.
 */

// 0xx: System and Help
define('_XFWEBSERVICE_ERROR_010', 'Command not found');
define('_XFWEBSERVICE_ERROR_011', 'Invalid syntax');
define('_XFWEBSERVICE_ERROR_012', 'Not authorized');
define('_XFWEBSERVICE_ERROR_013', 'Subsystem error');

// 10x: Auth->Login
define('_XFWEBSERVICE_ERROR_101', 'Invalid username or password');

// 11x: Auth->Logout

// 20x: Build->Debug

// 21x: Build->Start
define('_XFWEBSERVICE_ERROR_210', 'User has too many active builds');
define('_XFWEBSERVICE_ERROR_211', 'Could not connect to LDAP server');
define('_XFWEBSERVICE_ERROR_212', 'Could not determine CVS host');
define('_XFWEBSERVICE_ERROR_213', 'Build script not found');
define('_XFWEBSERVICE_ERROR_214', 'Could not create build record');

// 22x: Build->Status
define('_XFWEBSERVICE_ERROR_220', 'Could not retrieve build status');
define('_XFWEBSERVICE_ERROR_221', 'Invalid build ID');

// 23x: Build->Targets
define('_XFWEBSERVICE_ERROR_230', 'Could not determine targets');

// 30x: Publish->List
define('_XFWEBSERVICE_ERROR_300', 'Could not retrieve file list');

// 31x: Publish->Start
define('_XFWEBSERVICE_ERROR_310', 'File has already been published');
define('_XFWEBSERVICE_ERROR_311', 'Could not start publish');
define('_XFWEBSERVICE_ERROR_312', 'Invalid file ID');
define('_XFWEBSERVICE_ERROR_313', 'Could not record publish status');

// 32x: Publish->Status
define('_XFWEBSERVICE_ERROR_320', 'Could not retrieve publish status');
define('_XFWEBSERVICE_ERROR_321', 'Invalid publish ID');

?>