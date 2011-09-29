<?php

include_once("../../../mainfile.php");
$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vars.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/news/news_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/trove.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");

$headtitle=": CVS";
include ("../../../header.php");
include_once(XOOPS_ROOT_PATH."/modules/xfmod/include/nxoopsLDAP.php");

$projname = ltrim($_SERVER['PATH_INFO'], "/ ");
$projname = rtrim($projname, "/ ");

$res = $xoopsDB->query("SELECT group_id FROM ".$xoopsDB->prefix("xf_groups")." WHERE unix_group_name='".$projname."'");
if(!$res || $xoopsDB->getRowsNum($res) < 1)
{
	$xoopsForgeErrorHandler->setSystemError("Invalid project id passed to the cvs page");
} 
else
{
	  $group_arr = $xoopsDB->fetchArray($res);
	  $group_id = $group_arr['group_id'];
}

$project =& group_get_object($group_id);
	
$lldap = new nxoopsLDAP();
if(!$lldap->bindAdmin())
{
	$lldap->cleanUp();
	echo "Failed to get project info from LDAP: " .
		$lldap->lastError() . "<br/>";
	exit();
}

$cvsserver = $lldap->getProjectCVSServer($projname);
if(!$cvsserver)
{
	$lldap->cleanUp();
	echo "Failed to get CVS Server for project: $projname - " .
		$lldap->lastError() . "<br/>";
	exit();
}

$lldap->cleanUp();
$cvsdns = $cvsserver->dnsName;
setcookie('cvsview_project', $projname);
setcookie('cvsview_cvsdns', $cvsdns);

$devname = $xoopsUser ? $xoopsUser->getVar("uname")
	: "your-user-name";


//project nav information
echo project_title($project);
echo project_tabs('CVS', $group_id);
	
echo '<table cellSpacing="0" cellPadding="8" width="100%" border="0"><tr><td valign="top">';

if($project->anonCVS())
{
	echo '<h3>Browse the CVS Tree</h3>';
	echo 'Browsing the CVS tree gives you a great view into the current status of this ';
	echo "project's code. You may also view the complete histories of any file in the repository.";
	echo '<ul><li><a href="'.XOOPS_URL.'/modules/xfmod/cvs/cvsbrowse.php/'.$projname.'/" style="text-decoration: none; font-weight: 700">';
	echo 'Browse CVS Repository</a></li></ul><br>';
}

echo '<br><h3>Secure CVS</h3>';
echo "<p>Before you can access this project's CVS repository, you must have SSH ";
echo '(Secure Shell) installed on your machine and the CVS tools you have installed ';
echo 'must be configured to use SSH when communicating with the CVS server. On Linux ';
echo 'this can be done by simply exporting the CVS_RSH environment variable:</p>';
echo '<p align="center"><strong>export CVS_RSH=ssh</strong></p>';

echo '
<br><h3>CVS Server Public Key Authentication</h3>
<p>All CVS operations performed on Novell Forge hosted projects are done over an SSH connection. 
When making an SSH connection to any server, by default the user is required to enter a password for authentication. 
This requirement makes it hard to automate CVS commands within build scripts. Novell Forge allows for SSH public-key authentication 
that alleviates this problem. With public-key authentication, the user generates a private/public key pair using an SSH-compatible 
key generation tool, adds the public key to their account profile and configures their SSH client software to use the private key 
for future connections to the CVS server. The private/public key pair is specific to the machine it is generated on and to the 
user who generated it, so you may find it desirable to configure all your development machines for public key authentication. 
Novell Forge facilitates this by allowing you to enter multiple public keys per user account.';
if($xoopsUser){
 echo "  You can upload you public keys from <a href='".XOOPS_URL."/modules/xfaccount/pubkeys.php'>your account page</a>.</p>";	
}else{
 echo "  You can upload you public keys from your account page after you log in.</p>";	
}

if($project->anonCVS())
{
	echo '<br><h3>Anonymous CVS Access</h3>';
	echo '<p align="left">If you are not a registered developer on this project, you can ';
	echo 'still check-out all of the files in the CVS repository for this project through ';
	echo 'anonymous access. This can be done with the following command:</p>';
	echo '<p align="left"><strong>cvs -z3 ';
	echo "-d:ext:anonymous@$cvsdns:/cvsroot/$projname co <i>modulename</i>&nbsp;&nbsp;</strong></p>";
	echo '<p align="left">The module you wish to check out must be specified as <em>modulename</em>. ';
	echo 'When prompted for the password, simply enter <STRONG>"anonymous"</strong>.</p>';
}

echo '<br><h3>Developer CVS Access</h3>';

echo "<p align='left'>If you are a registered developer of the $projname".
	" project, you may use all of the functionality provided by CVS.<br/>".
	" For the full documentation on CVS, you can visit their WEB site at".
	' <a href="http://www.cvshome.org/">http://www.cvshome.org.</a><br/>'.
	"<p align='left'>Keep in mind that whenever you issue any CVS".
	" command on your project's CVS repository, you will be required".
	" to enter your site password.</p>".
	"<p align='left'>The most common things you will be doing with".
	" CVS are importing your project, checking it out, updating".
	" your local copy with the repository and checking in.".
	"<p align='left'>If your project's files are not yet in CVS,".
	" you must start with the CVS import. To do this,".
	" you should change to the topmost directory of your project's".
	" files and enter something like the following:".
	"</P><P align='left'><strong>";

echo "cvs -z3 -d:ext:$devname@$cvsdns:/cvsroot/$projname import".
	' -m <em>"Init msg" rootdirname vendor starttag</em></strong>';

echo '<p align="left">In the above command, we are telling CVS to '.
	" create the directory <em>./rootdirname</em> in the project's".
	" CVS repository and copy everything from the current directory and".
	" any sub-directories into it.".
	' The <em>"Init msg", vendor</em> and <em>starttag</em> can be any'.
	' text you like. The <em>"Init msg"</em> is used to specify a message'.
	" that will be associated with the import. <em>vendor</em> is used to".
	" specify the company that owns the project. <em>starttag</em> is the".
	" CVS tag name that will be associated with the import</p>";

echo "<p align='left'>Once the project has been imported, you can do".
	" the check-out, update and check-in operations anytime you".
	" deem it appropriate.</p>";

 
echo "<p align='left'>Now you can get your own private copy of all ".
	"the project files in CVS by doing a check-out. You can do this".
	" with the following command:</p><p align='left'><strong>".
	"cvs -z3 -d:ext:$devname@$cvsdns:/cvsroot/$projname co <em>modulename</em></strong></p>".
	"<p align='left'><em>modulename</em> would be replace with the value you".
	" used for the <em>./rootdirname</em> parameter when doing the import.</p>";

echo "<p align='left'>The CVS update command is used for downloading all changes".
	" that have been made to files in the CVS repository to your local copy.".
	" If another developer has changed a file that you have also changed in".
	" your local copy, the changes will be merged together. Sometimes a merge".
	" is required but cannot be completed. This condition produces a conflict.".
	" If there are any conflicts during the update process, they will appear in".
	" the output of the command. Under almost all conditions, an update operation".
	" must be performed before a check-in operation. If you check-in before doing".
	" an update, you run the risk of overwriting someone else's changes!".
	" To perform a CVS update, you should change to the root directory of your".
	" local copy of the project files and issue the following command:".
	"</p><p align='left'><strong>".
	"cvs -z3 -d:ext:$devname@$cvsdns:/cvsroot/$projname update</strong></p>";


echo "<p align='left'>At some point in time, you will want to check-in the changes".
	" that you have made to your local copy of the project files. This is".
	" a multi-phased process that should be as follows:".
	"<ul><li>Do a CVS update operation (described above).</li>".
	"<li>Fix any problems that may have surfaced from the update,".
	" such as failed merges(conflicts), etc...</li>".
	"<li>Do the actual CVS check-in operation</li></ul>".
	"<p align='left'>When you are ready to do the CVS check-in, change to any directory".
	" in your local copy of the project files and issue the following command:".
	"</p><p align='left'><strong>".
	"cvs -z3 -d:ext:$devname@$cvsdns:/cvsroot/$projname commit</strong></p>".
	"<p align='left'>This will check-in all changes in the current directory".
	" and any of it's sub-directories.</p>";

echo "<p align='left'>Again, CVS has much more functionality than what is".
	" described here. We encorage you to check out the documentation on".
	' their site: <a href="http://www.cvshome.org" target="developer">CVS Home</a>.</p>';

echo '<br><h3>Secure CVS on Windows with TortoiseCVS</h3>';
echo '<p align="left">TortoiseCVS gives you the ability to perform CVS functions 
		directly from Windows Explorer.  You can check out, update, commit, see diffs
		and so forth by right clicking on the file or folder.  You can download 
		TortoiseCVS from their website as <a href="http://www.tortoisecvs.org/index.shtml" target="developer">
		http://www.tortoisecvs.org/index.shtml</a>.  It is easy to install and use, and it 
		works very well.</p></td>';


echo '</TR></TABLE>';

include("../../../footer.php");
?>