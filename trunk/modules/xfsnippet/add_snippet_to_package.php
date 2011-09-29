<?php
/**
  *
  * SourceForge Code Snippets Repository
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: add_snippet_to_package.php,v 1.4 2004/01/26 18:57:01 devsupaul Exp $
  *
  */
include_once ("../../mainfile.php");

$langfile="snippet.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfsnippet/snippet_utils.php");

function handle_add_exit() {
	global $suppress_nav;
        if ($suppress_nav) {
            echo '';
        } else {
            snippet_footer();
			include("../../footer.php");
        }
	exit;
}

if ($xoopsUser) {

	include("../../header.php");
	snippet_header(_XF_SNP_SUBMITNEWSNIPPET);

	if (!$snippet_package_version_id) {
		//make sure the package id was passed in
		echo '<H4>Error - snippet_package_version_id missing</H4>';
		snippet_footer();
		include("../../footer.php");
		exit;
	}

	if($op=='delete'){
		include_once("delete.php");
	}

	if ($post_changes) {
		if($xoopsForge['snippetowner'] || 5==$xoopsUser->getVar('level')){
			if($snippetlist!=""){
				$moresnippets=explode(",",$snippetlist);
				$available=array_merge($available,$moresnippets);
			}
		}
		foreach($available as $snippet_version_id){
			/*
				check to see if they are the creator of this version
			*/
			$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_snippet_package_version")." "
			                         ."WHERE submitted_by='".$xoopsUser->getVar("uid")."' AND "
						                   ."snippet_package_version_id='$snippet_package_version_id'");

			if (!$result || $xoopsDB->getRowsNum($result) < 1) {
				$xoopsForgeErrorHandler->addError(_XF_SNP_ONLYCREATORCANADDTOPACKAGE);
				continue;
			}

			/*
				make sure the snippet_version_id exists
			*/
			$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_snippet_version")." WHERE snippet_version_id='$snippet_version_id'");

			if (!$result || $xoopsDB->getRowsNum($result) < 1) {
				$xoopsForgeErrorHandler->addError(_XF_SNP_SNIPPETDOESNOTEXIST);
				continue;
			}

			/*
				make sure the snippet_version_id isn't already in this package
			*/
			$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_snippet_package_item")." "
			                         ."WHERE snippet_package_version_id='$snippet_package_version_id' "
											         ."AND snippet_version_id='$snippet_version_id'");

			if ($result && $xoopsDB->getRowsNum($result) > 0) {
				$xoopsForgeErrorHandler->addError(_XF_SNP_SNIPPETALREADYADDEDTOPACKAGE);
				continue;
			}

			/*
				create the snippet version
			*/
			$sql = "INSERT INTO ".$xoopsDB->prefix("xf_snippet_package_item")." (snippet_package_version_id,snippet_version_id) "
			      ."VALUES ('$snippet_package_version_id','$snippet_version_id')";

			$result = $xoopsDB->queryF($sql);

			if (!$result) {
				$xoopsForgeErrorHandler->addError('The snippet could not be inserted into the database. '.$xoopsDB->error());
			} else {
				$xoopsForgeErrorHandler->addMessage(_XF_SNP_SNIPPETVERSIONADDED);
			}
		}
	}

	$result = $xoopsDB->query("SELECT sp.name,spv.version "
	                         ."FROM ".$xoopsDB->prefix("xf_snippet_package")." sp,".$xoopsDB->prefix("xf_snippet_package_version")." spv "
													 ."WHERE sp.snippet_package_id=spv.snippet_package_id "
													 ."AND spv.snippet_package_version_id='$snippet_package_version_id'");

	?>
	<TABLE border=0><TR><TD valign=top>
	<B><?php echo _XF_SNP_PACKAGE; ?>:</B><BR>
	<?php echo $ts->makeTboxData4Show(unofficial_getDBResult($result,0,'name')) . ' v' . $ts->makeTboxData4Show(unofficial_getDBResult($result,0,'version')); ?>
	<P>
	<?php echo _XF_SNP_CANUSEFORMREPEATEDLY; ?>
	<P>
	<FORM ACTION="<?php echo $_SERVER['PHP_SELF']; ?>" METHOD="POST">
	<INPUT TYPE="HIDDEN" NAME="post_changes" VALUE="y">
	<INPUT TYPE="HIDDEN" NAME="snippet_package_version_id" VALUE="<?php echo $snippet_package_version_id; ?>">
	<INPUT TYPE="HIDDEN" NAME="suppress_nav" VALUE="<?php echo $suppress_nav; ?>">
<?php
//get all my snippets
$sql ="SELECT sv.snippet_version_id, s.name, sv.version"
		." FROM ".$xoopsDB->prefix("xf_snippet")." as s"
		.",".$xoopsDB->prefix("xf_snippet_version")." as sv"
		." WHERE s.snippet_id=sv.snippet_id "
		." AND s.created_by=".$xoopsUser->getVar('uid');
$result = $xoopsDB->query($sql);

while($row = $xoopsDB->fetchArray($result)){
	$mysnippets[]=$row;
}
//get all the snippets in this package
$sql = "SELECT spi.snippet_version_id, s.name, sv.version "
		."FROM ".$xoopsDB->prefix("xf_snippet")." s,".$xoopsDB->prefix("xf_snippet_version")." sv,".$xoopsDB->prefix("xf_snippet_package_item")." spi "
		."WHERE s.snippet_id=sv.snippet_id "
		."AND sv.snippet_version_id=spi.snippet_version_id "
		."AND spi.snippet_package_version_id='$snippet_package_version_id'";
$result = $xoopsDB->query($sql);

while($row = $xoopsDB->fetchArray($result)){
	$mypackage[]=$row;
	if(false!==($key=array_search($row,$mysnippets))){
		unset($mysnippets[$key]);
	}
}
?>
	<select name="available[]" size="10" multiple>
<?php
foreach($mysnippets as $mysnippet){
	echo "<option value='".$mysnippet['snippet_version_id']."'>".$mysnippet['name']." v".$mysnippet['version']."</option>";
}
?>
	</select>
<?php
if($xoopsForge['snippetowner'] || 5==$xoopsUser->getVar('level')){
	echo '<br><br>You may also enter a comma delemated list of snippet id numbers.  Browse the snippets to find each snippet id number.<br>';
	echo '<INPUT TYPE="text" name="snippetlist" size=30>';
}
?>

		<BR><BR>
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="<?php echo _XF_SNP_ADDSNIPPET; ?>">
		<br><br>
	</FORM>
	</TD>
	<TD> &nbsp; </TD>
	<TD> &nbsp; </TD>
	<TD valign=top>
<?php $xoopsForgeErrorHandler->displayFeedback(); ?>
	</TD>
	</TR></TABLE>
	<?php
$title = _XF_SNP_SNIPPETSINPACKAGE;
$content = "<table border='0' width='100%'>"
			."<TR><TD></TD><TD><B>Name</B></TD><TD><B>Version</B></TD></TR>";

for ($i=0; $i<count($mypackage); $i++) {
	$content .= '<TR class="'.($i%2>0?'bg1':'bg3').'">'
				.'<TD ALIGN="MIDDLE">'
				.'<A HREF="'.XOOPS_URL.'/modules/xfsnippet/add_snippet_to_package.php?op=delete&type=frompackage&snippet_version_id='.$mypackage[$i]['snippet_version_id'].'&snippet_package_version_id='.$snippet_package_version_id.'">'
				.'<img src="'.XOOPS_URL.'/modules/xfmod/images/ic/trash.png" width="16" height="16" border="0" alt="delete"></A>'
				.'</TD><TD>'
				.$ts->makeTboxData4Show($mypackage[$i]['name'])
				.'</TD><TD>'
				.$ts->makeTboxData4Show($mypackage[$i]['version'])
				."</TD></TR>";

//			$last_group = unofficial_getDBResult($result,$i,'group_id');
}
$content .= "</table>";

themesidebox($title, $content);

  snippet_footer();
  include("../../footer.php");
  exit;
} else {

  redirect_header(XOOPS_URL."/user.php", 2,_NOPERM);
  exit;

}
//this needs to be removed and have real themimg added instead.
function themesidebox($title, $content) {
 echo"<table width='100%' border='0' cellspacing='1' cellpadding='5'><tr>"
    ."<td colspan='1'><div class='sidboxtitle'>$title</div></td></tr><tr>"
    ."<td><font>$content</font></td></tr></table>";
 }

?>
