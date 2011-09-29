<?php
/**
 * account.php - A library of common account management functions.
 *
 * SourceForge: Breaking Down the Barriers to Open Source Development
 * Copyright 1999-2001 (c) VA Linux Systems
 * http://sourceforge.net
 *
 * @version   $Id: account.php,v 1.3 2004/01/16 18:44:21 devsupaul Exp $
 *
 */

/**
 * account_namevalid() - Validates a login username
 *
 * @param		string	The username string
 * @returns		true on success/false on failure
 *
 */
function account_namevalid($name) {
	// no spaces
	if (strrpos($name,' ') > 0) {
		$GLOBALS['register_error'] = "There cannot be any spaces in the login name.";	
		return 0;
	}

	// min and max length
	if (strlen($name) < 3) {
		$GLOBALS['register_error'] = "Name is too short. It must be at least 3 characters.";
		return 0;
	}
	if (strlen($name) > 15) {
		$GLOBALS['register_error'] = "Name is too long. It must be less than 16 characters.";
		return 0;
	}

	if (!ereg('^[a-z][-a-z0-9_]+$', $name)) {
		$GLOBALS['register_error'] = "Illegal character in name.";
		return 0;
	}

	// illegal names
	if (eregi("^((root)|(bin)|(daemon)|(adm)|(lp)|(sync)|(shutdown)|(halt)|(mail)|(news)"
		. "|(uucp)|(operator)|(games)|(httpd)|(nobody)|(dummy)|(forgedev)"
		. "|(www)|(cvs)|(shell)|(ftp)|(irc)|(debian)|(ns)|(download))$",$name)) {
		$GLOBALS['register_error'] = "Name is reserved.";
		return 0;
	}
	if (eregi("^(anoncvs_)",$name)) {
		$GLOBALS['register_error'] = "Name is reserved for CVS.";
		return 0;
	}
		
	return 1;
}

/**
 * account_groupnamevalid() - Validates an account group name
 *
 * @param		string	The group name string
 * @returns		true on success/false on failure
 *
 */
function account_groupnamevalid($name) 
{
	if (!account_namevalid($name))
		return 0;
	
	// illegal names
	if (eregi("^((www[0-9]?)|(cvs[0-9]?)|(shell[0-9]?)|(ftp[0-9]?)|(irc[0-9]?)|(news[0-9]?)"
		. "|(mail[0-9]?)|(ns[0-9]?)|(download[0-9]?)|(pub)|(users)|(compile)|(lists)"
		. "|(slayer)|(orbital)|(tokyojoe)|(webdev)|(projects)|(cvs)|(slayer)|(monitor)|(mirrors?))$",$name)) {
		$GLOBALS['register_error'] = "Name is reserved for DNS purposes.";
		return 0;
	}

	/*
	if (eregi("_",$name)) {
		$GLOBALS['register_error'] = "Group name cannot contain underscore for DNS reasons.";
		return 0;
	}
	*/
	return 1;
}
?>