<?php

include_once(XOOPS_ROOT_PATH."/class/xoopsobject.php");
include_once(XOOPS_ROOT_PATH."/kernel/group.php");
include_once(XOOPS_ROOT_PATH."/class/xoopsuser.php");

// LDAP Class for NFORGE support

if (!defined("nxoops_LDAP_INCLUDED"))
{
	define("nxoops_LDAP_INCLUDED", 1);
}

class nxoopsLDAPCVSServer
{
	var $dn;
	var $dnsName;
	var $projCount;

	function nxoopsLDAPCVSServer()
	{
		$this->dn = "";
		$this->dnsName = "";
		$this->projCount = 0;
	}
}

class nxoopsLDAPUser
{
	var $dn;
	var $uid;
	var $cn;
	var $email;
	var $groups;
	var $gecos;
	var $extid;

	function nxoopsLDAPUser()
	{
		$this->dn = null;
		$this->uid = null;
		$this->cn = null;
		$this->email = null;
		$this->groups = null;
		$gecos = null;
	}
}

class nxoopsLDAP
{
	var $conn;
	var $bound;
	var $error;

	function nxoopsLDAP()
	{
		$this->error = null;
		$this->conn = null;
		$this->bound = false;
	}

	function cleanUp()
	{
		if($this->conn)
		{
			ldap_close($this->conn);
			unset($this->conn);
			$this->conn = null;
		}
	}

	// Return the error string from the last operation
	function lastError()
	{
		return $this->error;
	}

	// Connect to the LDAP server specified in the configuration
	function connect()
	{
		global $xoopsConfig;
		$this->error = null;
		$server = ($xoopsConfig['ldapserverport'] == 389) ? "ldap" : "ldaps";
		$server .= "://" . $xoopsConfig['ldapserver'];
		$this->conn = ldap_connect($server, $xoopsConfig['ldapserverport']);
		if(!$this->conn)
		{
			$this->conn = null;
			$this->error = "Failed connection to " . $server;
			return false;
		}
		return true;
	}

	// Set the error member of this object with a given string prefix
	// and the current LDAP error string
	function returnLDAPFailure($prefString)
	{
		$this->error = $prefString . " LDAP Error: " .
			ldap_err2str(ldap_errno($this->conn));
		return false;
	}

	// Private
	// Authenticate a user specified by a given DN
	// using a given password.
	function doBind($userDN, $password)
	{
		global $xoopsConfig;
		$this->bound = false;
		$this->error = null;
		if(!$this->conn)
		{
			if(!$this->connect())
			{
				return false;
			}
		}
		ldap_bind($this->conn, $userDN, $password);
		if(ldap_errno($this->conn) != 0)
		{
			$userDN .= " LDAP Server: " . $xoopsConfig['ldapserver'];
			return $this->returnLDAPFailure("Bind DN: " . $userDN);
		}

		$this->bound = true;
		return true;
	}

	// Do authentication for the admin
	function bindAdmin()
	{
		global $xoopsConfig;
		return $this->doBind($xoopsConfig['ldapadmin'], $xoopsConfig['ldapadminpass']);
	}

	// Get an LDAP object associated with a given DN
	function findLDAPObject($dn, $attributes=null, $filter=null)
	{
		$this->error = null;

		if(!$filter)
		{
			$filter = "(objectclass=*)";
		}

		$sr = ldap_search($this->conn, $dn, $filter, $attributes);
		if(!$sr)
		{
			return $this->returnLDAPFailure("Search for CVS object: $dn");
		}

		if(ldap_count_entries($this->conn, $sr) < 1)
		{
			$this->error = "No entries found for $dn";
			return false;
		}

		$info = ldap_get_entries($this->conn, $sr);
		return $info;
	}

	// Get user information from LDAP
	function getUser($userName, $includeGroups=false)
	{
		global $xoopsConfig;
		$this->error = null;
		$userObj = null;
		$entryDN = $xoopsConfig['ldapusercont'];
		$filter = "(cn=".$userName.")";
		$justthese = array("uidnumber", "cn", "mail", "gecos", "webidsynchid");
		if($includeGroups)
		{
			$justthese[] = "groupmembership";
		}

		$info = $this->findLDAPObject($entryDN, $justthese, $filter);
		if(!$info)
		{
			$this->error = "User $userName not found";
			unset($justthese);
			return null;
		}

		$userObj = new nxoopsLDAPUser;
		$userObj->dn = $info[0]["dn"];
		$userObj->cn = $info[0]["cn"][0];
		$userObj->email = $info[0]["mail"][0];
		$userObj->extid = $info[0]["webidsynchid"][0];

		if(isset($info[0]["uidnumber"][0]))
		{
			$userObj->uid = $info[0]["uidnumber"][0];
			$userObj->gecos = $info[0]["gecos"][0];
		}
		
		if ($info[0]["groupmembership"]["count"] > 0)
		{
			$userObj->groups = array();
			for($i = 0; $i < $info[0]["groupmembership"]["count"]; $i++)
			{
				$userObj->groups[] = $info[0]["groupmembership"][$i];
			}
		}

		unset($info);
		return $userObj;
	}

	// Add PosixAccount Extension to user object
	function addPosixToUser($userName, $userDN, $uidNumber)
	{
		global $xoopsConfig;
		$this->error = null;
		$entry = array();
		$entry['objectclass'][0] = "posixaccount";
		$entry['gidnumber'] = 1000;
		$entry['uidnumber'] = $uidNumber;
		$entry['homedirectory'] = "/home/nforgeusers";
		$entry['gecos'] = $userName;
		$entry['loginshell'] = "/bin/cvssh";
		$res = ldap_mod_add($this->conn, $userDN, $entry);

		if(!$res)
		{
			return $this->returnLDAPFailure("Adding posixAccount to user: " .
				$userDN);
		}

		return true;
	}	

	// Ensure the user has the posixAccount information
	// attached
	function ensurePosixUser($userName)
	{
		global $xoopsConfig;
		$userName = strtolower($userName);
		$userObj = $this->getUser($userName);
		if(!$userObj)
		{
			return false;
		}

		if(strlen($userObj->uid) > 0)
		{
			// uidnumber is already there, so lets' assume
			// the object already has the posix extension
			return true;
		}

		if(strlen($userObj->extid) < 1)
		{
			// We don't have an ID to use for the uidNumber
			$this->error = "Error: No webidsynchid returned for " .
				$userObj->dn;
			return false;
		}

		return $this->addPosixToUser($userName, $userObj->dn, $userObj->extid);
	}

	// Add or remove a group/user relationship
	function modUserGroupRelation($userObj, $grpName, $doDel=false)
	{
		global $xoopsConfig;
		$this->error = null;
		$userName = $userObj->getVar("uname", "S");
		$ldapUser = $this->getUser($userName);
		if(!$ldapUser)
		{
			return false;
		}
		$userDN = $ldapUser->dn;
		$projBase = "ou=projects,ou=nforge,".$xoopsConfig['ldaproot'];
		$grpDN = "cn=$grpName,$projBase";
		$entry['groupmembership'] = $grpDN;
		$entry['securityequals'] = $grpDN;
		$res = false;
		$lop = "Add";
		
		if($doDel)
		{
			$lop = "Remove";
			$res = ldap_mod_del($this->conn, $userDN, $entry);
		}
		else
		{
			$res = ldap_mod_add($this->conn, $userDN, $entry);
		}
		
		if(!$res)
		{
			$this->returnLDAPFailure($lop . " Group User DN: " . $userDN);
			return false;
		}

		unset($entry);
		$entry['member'] = $userDN;
		$entry['equivalentToMe'] = $userDN;
		
		if($doDel)
		{
			$lop = "Remove user " . $userDN . " from grp " . $grpDN;
			$res = ldap_mod_del($this->conn, $grpDN, $entry);
		}
		else
		{
			$lop = "Adding user " . $userDN . " to grp " . $grpDN;
			$res = ldap_mod_add($this->conn, $grpDN, $entry);
		}

		if(!$res)
		{
			$this->returnLDAPFailure($lop);
			return false;
		}

		return true;
	}

	// Add a user to a group
	function addUserToGroup($userObj, $grpName)
	{
		return $this->modUserGroupRelation($userObj, $grpName, false);
	}

	// Remove a user from a group
	function removeUserFromGroup($userObj, $grpName)
	{
		return $this->modUserGroupRelation($userObj, $grpName, true);
	}

	// Enumerate the cvs server in the ou=cvsservers,ou=nforge container.
	// Find the one the has the lowest project count and return it.
	function getAvailCVSServer($allServers=false)
	{
		global $xoopsConfig;
		$this->error = null;
		$justthese = array("nforgednsname", "nforgeprojectcount");
		$base = "ou=cvsservers,ou=nforge,".$xoopsConfig['ldaproot'];
		$sr = ldap_list($this->conn, $base, "(objectclass=NFORGECVSServer)",
			$justthese);

		if(!$sr)
		{
			return $this->returnLDAPFailure("Search for CVS Servers");
		}

		if(ldap_count_entries($this->conn, $sr) < 1)
		{
			$this->error = "No CVS Servers found";
			unset($justthese);
			unset($sr);
			return false;
		}

		$info = ldap_get_entries($this->conn, $sr);
		if($allServers === true)
		{
			$servers = array();
			for($i = 0; $i < $info['count']; $i++)
			{
				$cvsServer = new nxoopsLDAPCVSServer;
				$cvsServer->dn = $info[$i]["dn"];
				$cvsServer->dnsName = $info[$i]["nforgednsname"][0];
				$cvsServer->projCount = $info[$i]["nforgeprojectcount"][0];
				$servers[] = $cvsServer;
			}

			unset($justthese);
			unset($sr);
			unset($info);
			return $servers;
		}

		$cvsServer = new nxoopsLDAPCVSServer;
		$cvsServer->projCount = 0x7FFFFFFF;
		for ($i = 0; $i < $info["count"]; $i++)
		{
			$projCount = $info[$i]["nforgeprojectcount"][0];
			if(!$projCount)
				$projCount = 0;
			
			if($cvsServer->projCount > $projCount)
			{
				$cvsServer->dn = $info[$i]["dn"];
				$cvsServer->dnsName = $info[$i]["nforgednsname"][0];
				$cvsServer->projCount = $projCount;
			}
		}

		unset($justthese);
		unset($sr);
		unset($info);
		return $cvsServer;
	}

	// Get a CVS Server LDAP object based on a given DNS name
	function getCVSServer($cvsServerDNSName)
	{
		global $xoopsConfig;
		$this->error = NULL;
		$justthese = array("nforgednsname", "nforgeprojectcount");
		$filter = "(&(objectclass=NFORGECVSServer)(NFORGEDNSName=";
		$filter .= $cvsServerDNSName . "))";
		$base = "ou=cvsservers,ou=nforge,".$xoopsConfig['ldaproot'];

		$sr = ldap_list($this->conn, $base, $filter, $justthese);
		if(!$sr)
		{
			return $this->returnLDAPFailure("Didn't find CVS Server: " . $cvsServerDNSName);
		}

		if(ldap_count_entries($this->conn, $sr) < 1)
		{
			$this->error = "Didn't find CVS Server: " . $cvsServerDNSName;
			unset($justthese);
			unset($sr);
			return false;
		}

		$info = ldap_get_entries($this->conn, $sr);

		$cvsServer = new nxoopsLDAPCVSServer;
		$cvsServer->dn = $info[$i]["dn"];
		$cvsServer->dnsName = $info[$i]["nforgednsname"][0];
		$cvsServer->projCount = $info[$i]["nforgeprojectcount"][0];

		if(!$cvsServer->projCount)
			$cvsServer->projCount = 0;

		unset($justthese);
		unset($sr);
		unset($info);
		return $cvsServer;
	}

	// Get the CVS Server object associated with a given project name
	function getProjectCVSServer($projectName)
	{
		global $xoopsConfig;
		$cvsserver = null;

		// Find the LDAP object for the given project
		$entryDN = "cn=$projectName,ou=projects,ou=nforge,".$xoopsConfig['ldaproot'];

		$justthese = array("nforgecvsserverref");
		$info = $this->findLDAPObject($entryDN, $justthese);
		if(!$info)
		{
			$this->error = "Project $projectName not found";
			unset($justthese);
			return false;
		}

		$entryDN = $info[0]["nforgecvsserverref"][0];
		if(!$entryDN)
		{
			$this->error = "Project $projectName has no CVS server";
			return null;
		}

		unset($info);
		
		// Find the LDAP object for the CVS server of the project.
		$justthese = array("nforgednsname", "nforgeprojectcount");
		$info = $this->findLDAPObject($entryDN, $justthese);
		if(!$info)
		{
			$this->error = "Project $projName has invalid CVS Server DN: ";
			$this->error .= $entryDN;
			return null;
		}

		$cvsserver->dn = $info[0]["dn"];
		$cvsserver->dnsName = $info[0]["nforgednsname"][0];
		$cvsserver->projCount = $info[0]["nforgeprojectcount"][0];

		if(!$cvsserver->projCount)
			$cvsserver->projCount = 0;
		
		unset($info);
		unset($justthese);
		return $cvsserver;
	}

	// Add a pending project action to a given CVS Server
	function addCVSServerAction($cvsServerDN, $identifier, $tranType, $tranbuf=null)
	{
		global $xoopsDB;
		
		$tranType = strtoupper($tranType);
		$this->error = null;
		switch($tranType)
		{
			case "A":
				$action = "ADD";
				break;
			case 'M':
				$action = "MOD";
				break;
			case 'D':
				$action = "DEL";
				break;
			case 'P':
				$action = "PUB";
				break;
			case 'E':
				$action = "EML";
				break;

			default:
				$this->error = "Invalid Argument action = $action";
				return false;
		}

		$actionLine = $identifier."?".$action."?".microtime();
		if($tranType == 'P' or $tranType == 'E')
		{
			$actionLine .= "?" . $tranbuf;
		} 

		$entry['nforgependingprojectactions'] = $actionLine;
		ldap_mod_add($this->conn, $cvsServerDN, $entry);
		if(ldap_errno($this->conn) != 0)
		{
			$emsg = "Add Pending action. cvsserver:$cvsServerDN ";
			$emsg .= "action:$identifier?$action";
			return $this->returnLDAPFailure($emsg);
		}

		$count = unofficial_getDBResult($xoopsDB->query($sql),0,'count');

		return true;
	} 

	// Increment or decrement a project count on a CVS server object
	function incDecCVSServerProjCount($cvsServerDN, $doDec=false)
	{
		$this->error = null;
		$justthese = array("nforgeprojectcount");
		$info = $this->findLDAPObject($cvsServerDN, $justthese);
		if(!$info)
		{
			$this->error = "Didn't find CVS Server: " . $cvsServerDN;
			unset($justthese);
			return false;
		}
		$projCount = $info[0]["nforgeprojectcount"][0];
		if(!$projCount)
			$projCount = 0;

		if($doDec)
			$projCount--;
		else
			$projCount++;

		$cc = true;

		// Does ldap_mod_replace work if attribute doesn't exist?
		$entry['nforgeprojectcount'] = $projCount;
		ldap_mod_replace($this->conn, $cvsServerDN, $entry);
		if(ldap_errno($this->conn) != 0)
		{
			$cc = false;
			$this->error = $prefString . " LDAP Error: " .
				ldap_err2str(ldap_errno($this->conn));
		}

		unset($justthese);
		unset($entry);
		return $cc;
	}

	// Increment the project count on a CVS server object
	function incCVSServerProjCount($cvsServerDN)
	{
		return $this->incDecCVSServerProjCount($cvsServerDN, false);
	}

	// decrement the project count on a CVS server object
	function decCVSServerProjCount($cvsServerDN)
	{
		return $this->incDecCVSServerProjCount($cvsServerDN, true);
	}

	// Set up a project to be add to a CVS server
	function createCVSProject($cvsServerDN, $projectName)
	{
		return $this->addCVSServerAction($cvsServerDN, $projectName, "A");
	}

	// Set up a project to be removed from a CVS server
	function removeCVSProject($cvsServerDN, $projectName)
	{
		return $this->addCVSServerAction($cvsServerDN, $projectName, "D");
	}

	// Set up a project to be modified (anon access flag) from a CVS server
	function modifyCVSProject($cvsServerDN, $projectName)
	{
		return $this->addCVSServerAction($cvsServerDN, $projectName, "M");
	}

	// Create an LDAP project object.
	function createProject($projectName, $gidNumber, $anonAllowed)
	{
		global $xoopsConfig;
		$this->error = null;
		$projectExists = false;

		// See if the project already exists
		$entryDN = "cn=$projectName,ou=projects,ou=nforge,".$xoopsConfig['ldaproot'];

		$justthese = array("nforgecvsserverref");
		$info = $this->findLDAPObject($entryDN, $justthese);
		if($info)
		{
			unset($justthese);
			$entryDN = $info[0]["nforgecvsserverref"][0];
			if($entryDN)
			{
				return $this->modifyCVSProject($entryDN, $projectName);
			}
		
			// At this point we know the project exists, but it is not
			// associated with a CVS server object.
			$projectExists = true;
		}

		$cvsserver = $this->getAvailCVSServer();
		if(!$cvsserver)
		{
			return false;
		}

		if(!$this->incCVSServerProjCount($cvsserver->dn))
		{
			return false;
		}
		
		$entryDN = "cn=$projectName,ou=projects,ou=nforge,".$xoopsConfig['ldaproot'];

		$res = true;
		$entry = array();
		if($projectExists)
		{
			$entry['nforgecvsserverref'] = $cvsserver->dn;
			$res = ldap_mod_add($this->conn, $entryDN, $entry);
		}
		else
		{
			$entry['objectclass'][0] = "top";
			$entry['objectclass'][1] = "groupofnames";
			$entry['objectclass'][2] = "posixgroup";
			$entry['objectclass'][3] = "nforgeproject";
			$entry['gidnumber'] = $gidNumber;
			$entry['cn'] = $projectName;
			$entry['nforgecvsserverref'] = $cvsserver->dn;
			$entry['nforgeanonymousallowed'] = ($anonAllowed) ? "TRUE" : "FALSE";
			$res = ldap_add($this->conn, $entryDN, $entry);
		}

		if(!$res)
		{
			$error = $prefString . " LDAP Error: " . ldap_err2str(ldap_errno());
			$this->decCVSServerProjCount($cvsserver->dn);
			$this->error = $error;
			return false;
		}

		if(!$this->createCVSProject($cvsserver->dn, $projectName))
		{
			return false;
		}

		return $cvsserver;
	}

	// Delete an LDAP Project object
	function deleteProject($projectName, $cvsServerDNSName)
	{
		global $xoopsConfig;
		$this->error = null;
		$cvsserver = $this->getCVSServer($cvsServerDNSName);
		if(!$cvsserver)
		{
			return false;
		}

		if(!$this->decCVSServerProjCount($cvsserver->dn))
		{
			return false;
		}
	
		$this->removeCVSProject($cvsserver->dn, $projectName);
		$entryDN = "cn=$projectName,ou=projects,ou=nforge,".$xoopsConfig['ldaproot'];

		if(! ldap_delete($this->conn, $entryDN) )
		{
			return $this->returnLDAPFailure("Failed to delete project: " . $entryDN);
		}

		return true;
	}

	// Set up project so anon access will be modified on the
	// CVS server
	function modifyProject($projectName, $cvsServerDNSName)
	{
		$this->error = null;
		$cvsserver = $this->getCVSServer($cvsServerDNSName);
		if(!$cvsserver)
		{
			return false;
		}

		return $this->modifyCVSProject($cvsserver->dn, $projectName);
	}

	// Set the anonymous flag on a project
	function setAnonAllowed($projectName, $anonFlag=true)
	{
		global $xoopsConfig;
		$entryDN = "cn=$projectName,ou=projects,ou=nforge,".$xoopsConfig['ldaproot'];
		$entry = array();
		$entry['nforgeanonymousallowed'] = ($anonFlag) ? "TRUE" : "FALSE";

		// Modify the anon flag on the project
		if(!ldap_mod_replace($this->conn, $entryDN, $entry))
		{
			return $this->returnLDAPFailure("Change anonymous flag for " .
				" project: $entryDN");
		}

		// Get the cvs server DN from the project
		$justthese = array("nforgecvsserverref");
		$info = $this->findLDAPObject($entryDN, $justthese);
		if(!$info)
		{
			$this->error = "Project $projectName not found";
			unset($justthese);
			return false;
		}

		// Get the CVS server DN
		$entryDN = $info[0]["nforgecvsserverref"][0];
		if(!$entryDN)
		{
			$this->error = "Project $projectName has no CVS server";
			return null;
		}

		unset($info);
		return $this->modifyCVSProject($entryDN, $projectName);
	}

	// Set a user's public keys
	function setUserPubKeys($userName,	// The user name keys are associated with
		$pubKeys)			// Public keys for the given user (array of strings)
	{
		global $xoopsConfig;
		$this->error = null;
		$userName = strtolower($userName);
		$keybuf = "";

		for($i = 0; $i < count($pubKeys); $i++)
		{
			$keybuf .= $pubKeys[$i]."\n";
		}

		// Get all of the CVS servers for the Forge system.
		$cvsservers = $this->getAvailCVSServer(true);
		for($i = 0; $i < count($cvsservers); $i++)
		{
			// Put a public key transaction in the CVS server's
			// transaction queue.
			$this->addCVSServerAction($cvsservers[$i]->dn, $userName, "P", $keybuf);
		}

		return true;
	}

	// Set email notification for projects
	function setProjNotify($projName,	// The name of the project to set email on
		$emailAddrs)			// Email addresses for the given project (array of strings)
	{
		global $xoopsConfig;
		$this->error = null;
		$projName = strtolower($projName);
		$tranbuf = "";

		for($i = 0; $i < count($emailAddrs); $i++)
		{
			$tranbuf .= $emailAddrs[$i]."\n";
		}

		// Get all of the CVS servers for the Forge system.
		$cvsservers = $this->getAvailCVSServer(true);

		for($i = 0; $i < count($cvsservers); $i++)
		{
			// Put a public key transaction in the CVS server's
			// transaction queue.
			$this->addCVSServerAction($cvsservers[$i]->dn, $projName, "E", $tranbuf);
		}

		return true;
	}

}
			
?>