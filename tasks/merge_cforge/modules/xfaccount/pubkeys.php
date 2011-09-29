<?php
/**
*
* SourceForge User's Personal Page
*
* SourceForge: Breaking Down the Barriers to Open Source Development
* Copyright 1999-2001 (c) VA Linux Systems
* http://sourceforge.net
*
* @version   $Id: pubkeys.php,v 1.8 2003/12/24 17:34:00 jcox Exp $
*
*/
 
include_once ("../../mainfile.php");
 
$langfile = "my.php";
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once (ICMS_ROOT_PATH."/modules/xfaccount/account_util.php");
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
require_once(ICMS_ROOT_PATH."/modules/xfmod/include/account.php");
include_once(ICMS_ROOT_PATH."/modules/xfmod/include/nxoopsLDAP.php");
$icmsOption['template_main'] = 'xfaccount_pubkeys.html';
 
// define variables
$feedback = null;
$func = null;
$title = null;
// update variables
foreach ($_POST as $key => $value )
{
	$ {
		$key }
	 = $value;
}
 
if (strlen($feedback) > 0)
{
	$icmsForgeErrorHandler->addMessage($feedback);
}
 
function getLDAPConnection()
{
	global $icmsConfig, $icmsForgeErrorHandler;
	 
	$lldap = new nxoopsLDAP;
	if (!$lldap->connect())
	{
		$icmsForgeErrorHandler->setSystemError("Failed to connect to LDAP server: " . $icmsConfig['ldapserver']);
	}
	 
	if (!$lldap->bindAdmin())
	{
		$ldaperr = $lldap->lastError();
		$lldap->cleanUp();
		$icmsForgeErrorHandler->setSystemError("Failed to bind to LDAP server: " .$ldaperr);
	}
	return $lldap;
}
 
if (!$icmsUser)
	{
	$icmsForgeErrorHandler->setSystemError(_NOPERM . "called from ".__FILE__." line ".__LINE__ );
}
 
$userName = $icmsUser->getVar("uname");
 
if ($func)
	{
	$doLDAPTrans = false;
	if ($func == "delete")
	{
		if (strlen($pkey) && $pkeytime > 0)
		{
			 
			$res = $icmsDB->queryF("DELETE FROM ".$icmsDB->prefix("xf_pubkeys")
			." WHERE uid='".$icmsUser->getVar("uid")
			."' AND time='".$pkeytime."'");
			 
			if (!$res)
			{
				$icmsForgeErrorHandler->addError("Failed to remove public key. " ."Error: ".$icmsDB->error());
			}
			else
				{
				$doLDAPTrans = true;
				$feedback = "Removed public key";
			}
		}
	}
	else if($func == "deleteall")
	{
		$res = $icmsDB->queryF("DELETE FROM ".$icmsDB->prefix("xf_pubkeys")
		." WHERE uid='".$icmsUser->getVar("uid")."'");
		 
		if (!$res)
		{
			$icmsForgeErrorHandler->addError("Failed to remove all public keys. " ."Error: ".$icmsDB->error());
		}
		else
			{
			$doLDAPTrans = true;
			$feedback = "Removed all public keys";
		}
	}
	else if($func == "add")
	{
		if (strlen($pkey) > 0)
		{
			$pkey = rtrim($pkey);
		}
		 
		if (strlen($pkey) > 0)
		{
			$sql = "INSERT INTO ".$icmsDB->prefix("xf_pubkeys")
			." (uid, time, pubkey) values (" .$icmsUser->getVar("uid")."," .time()."," ."'".$pkey."')";
			 
			$res = $icmsDB->queryF($sql);
			 
			if (!$res)
			{
				$icmsForgeErrorHandler->addError("Failed to add public key. " ."Error: ".$icmsDB->error()." SQL: ".$sql);
			}
			else
				{
				$doLDAPTrans = true;
				$feedback = "Added public key";
			}
		}
		else
			{
			$icmsForgeErrorHandler->addError("No public key specified for addition");
		}
	}
	 
	if ($doLDAPTrans)
	{
		$pubkeys = array();
		$res = $icmsDB->query("SELECT pubkey FROM ".$icmsDB->prefix("xf_pubkeys")
		." WHERE uid='".$icmsUser->getVar("uid")."'");
		 
		if ($res && $icmsDB->getRowsNum($res) > 0)
		{
			while ($therow = $icmsDB->fetchArray($res))
			{
				$pubkeys[] = $therow['pubkey'];
			}
		}
		 
		$lldap = getLDAPConnection();
		$rc = $lldap->setUserPubKeys($userName, $pubkeys);
		$lldap->cleanUp();
		if (!$rc)
		{
			$icmsForgeErrorHandler->addError("Failed to add Public Key transaction. " ."Error: ".$lldap->lastError());
		}
		else
			{
			redirect_header(ICMS_URL."/modules/xfaccount/pubkeys.php?feedback=$feedback", 0, "");
		}
	}
}
 
$metaTitle = ": "._XF_MY_MYPUBKEYS;

$mhandler = icms_gethandler('module');
$icmsModule = $xoopsModule = $mhandler->getByDirname('xfaccount');
global $icmsModule;

include ("../../header.php");
$icmsTpl->assign("account_header", account_header(_XF_MY_MYPUBKEYS));
 
$content = '
	<script language=javascript>
	<!--
	function verify(cdate)
	{
	msg = "Delete public key created on: ";
	msg += cdate;
	msg += "?";
	return confirm(msg);
	}
	 
	function verifyAll()
	{
	return confirm("Delete all public keys?");
	}
	-->
	</script>';
 
$content = '
	<h3>CVS Server Public Key Authentication</h3>
	<p>All CVS operations performed on Novell Forge hosted projects are done over an SSH connection.
	When making an SSH connection to any server, by default the user is required to enter a password for authentication.
	This requirement for interactivity on the part of the user can become annoying when doing frequent CVS operations.
	It also makes it impossible to automate CVS commands within build scripts. Novell Forge allows for SSH public-key authentication
	that alleviates this problem. With public-key authentication, the user generates a private/public key pair using an SSH-compatible
	key generation tool, adds the public key to their account profile and configures their SSH client software to use the private key
	for future connections to the CVS server. The private/public key pair is specific to the machine it is generated on and to the
	user who generated it, so you may find it desirable to configure all your development machines for public key authentication.
	Novell Forge facilitates this by allowing you to enter multiple public keys per user account.';
 
//$icmsForgeErrorHandler->displayFeedback();
 
$content .= '
	<p>
	<h4>Add a new public key</h4>
	<p>
	Copy and paste your public key in the field below.
	<p>
	<table>
	<form action="'. $_SERVER['PHP_SELF'] .'" method="post">
	<input type="hidden" name="func" value="add">
	<tr><td colspan="2"><strong>Public Key</strong><br/>
	<textarea name="pkey" rows="5" cols="60"></textarea>
	</td></tr>
	<tr><td colspan="2"><p>
	<input type="submit" name="submit" value="Add">
	</form></td></tr></table><p>';
 
$content .= '
	<h4>Existing Public Keys</4>
	<table border="0" width="100%" class="outer">
	<tr class="head">
	<td ><strong>Creation Date</strong></td><td  colspan="2">
	<strong>Public Key</strong></td></tr>';
 
 
 
$res = $icmsDB->query("SELECT time,pubkey FROM ".$icmsDB->prefix("xf_pubkeys")
." WHERE uid='".$icmsUser->getVar("uid")."'");
 
if (!$res || $icmsDB->getRowsNum($res) < 1)
{
	$content .= "<tr><td align='center' colspan='3'><strong>No public keys</strong></td></tr>";
}
else
{
	$i = 0;
	while ($therow = $icmsDB->fetchArray($res))
	{
		$keytime = $therow['time'];
		$pubkey = $therow['pubkey'];
		$fpkey = chunk_split($pubkey, 80, "<br/>");
		$cdate = date($sys_datefmt, $keytime);
		$content .= '<tr class="'.($i % 2 != 0 ? 'odd' : 'even').'"><td>' ."$cdate</td><td><br/>$fpkey<br/></td>";
		 
		$content .= "<td align='center'><form action='". $_SERVER['PHP_SELF'] ."' method='post'" .'onSubmit="return verify(\''.$cdate.'\')">' ."<input type='hidden' name='func' value='delete'>" ."<input type='hidden' name='pkey' value='".$pubkey."'>" ."<input type='hidden' name='pkeytime' value='".$keytime."'>" ."<input type=submit value='Delete'></form></td></tr>";
		 
		$i++;
	}
	 
	$content .= "<tr><td colspan='3'>&nbsp;&nbsp;</td></tr>" ."<tr><td align='center' colspan='3'><form action'".$_SERVER['PHP_SELF']."' method='post'" .'onSubmit="return verifyAll()">' ."<input type='hidden' name='func' value='deleteall'>" ."<input type=submit value='Delete all public keys'></form></td></tr>";
}
 
$content .= '</table>';
//themesidebox($title, $content);
 
$icmsTpl->assign("title", $title);
$icmsTpl->assign("content", $content);
include("../../footer.php");
 
//CloseTable();
include("../../footer.php");
 
?>