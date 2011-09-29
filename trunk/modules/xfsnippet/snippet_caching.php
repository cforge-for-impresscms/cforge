<?php
/**
 * snippet_caching.php
 *
 * SourceForge: Breaking Down the Barriers to Open Source Development
 * Copyright 1999-2001 (c) VA Linux Systems
 * http://sourceforge.net
 *
 * @version   $Id: snippet_caching.php,v 1.3 2004/01/30 20:56:55 jcox Exp $
 */

/**
 * snippet_mainpage() - Show the main page for the snippet library.
 */
function snippet_mainpage()
{
	global $xoopsDB,$SCRIPT_LICENSE;

	$return = '<P>'._XF_SNP_PURPOSE1.'</P><P>'._XF_SNP_PURPOSE2.'</P>';
/*	// sql queries
	$sql_type = $xoopsDB->query("SELECT type_id,name FROM ".$xoopsDB->prefix("xf_snippet_type")." ORDER BY name ASC");
	$SCRIPT_TYPE_ids = util_result_column_to_array($sql_type, 0);
	$SCRIPT_TYPE_val = util_result_column_to_array($sql_type, 1);
	$SCRIPT_TYPE_val[0] = 'Any';
	// sql queries
	$sql_category = $xoopsDB->query("SELECT type_id,name FROM ".$xoopsDB->prefix("xf_snippet_category")." ORDER BY name ASC");
	$SCRIPT_CATEGORY_ids = util_result_column_to_array($sql_category, 0);
	$SCRIPT_CATEGORY_val = util_result_column_to_array($sql_category, 1);
	$SCRIPT_CATEGORY_val[0] = 'Any';
	// sql queries
	$sql_language = $xoopsDB->query("SELECT type_id,name FROM ".$xoopsDB->prefix("xf_snippet_language")." ORDER BY name ASC");
	$SCRIPT_LANGUAGE_ids = util_result_column_to_array($sql_language, 0);
	$SCRIPT_LANGUAGE_val = util_result_column_to_array($sql_language, 1);
	$SCRIPT_LANGUAGE_val[0] = 'Any';
	// sql queries
	$SCRIPT_LICENSE[]='Any';
	
	$return.='
	<FORM ACTION="'.XOOPS_URL.'/modules/xfsnippet/browse.php" METHOD="GET">
	<TABLE>
	<TR>
	<TD colspan=2>
		<B>Contains:</B><br>
		<INPUT type="text" name="text" size=30>
	</TD>
	</TR>
	<TR>
	<TD><B>'._XF_SNP_LANGUAGE.':</B><BR>
		'.html_build_select_box_from_arrays($SCRIPT_LANGUAGE_ids,$SCRIPT_LANGUAGE_val,'lang',$SCRIPT_LANGUAGE_ids[0],false).'
		<BR>
  	<A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/?func=add&group_id=1011&atid=145">Suggest a Language</A>
	</TD>

	<TD><B>'._XF_SNP_CATEGORY.':</B><BR>
		'.html_build_select_box_from_arrays($SCRIPT_CATEGORY_ids,$SCRIPT_CATEGORY_val,'cat',$SCRIPT_CATEGORY_ids[0],false).'
    <BR>
    <A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/?func=add&group_id=1011&atid=145">Suggest a Category</A>
	</TD>
	</TR>
	<TR>
	<TD><B>'._XF_SNP_TYPE.':</B><BR>
		'.html_build_select_box_from_arrays($SCRIPT_TYPE_ids,$SCRIPT_TYPE_val,'type',$SCRIPT_TYPE_ids[0],false).'
		<BR>
		<A HREF="'.XOOPS_URL.'/modules/xfmod/tracker/?func=add&group_id=1011&atid=145">Suggest a Script Type</A>
	</TD>

	<TD><B>'._XF_SNP_LICENSE.':</B><BR>
		'.html_build_select_box_from_array($SCRIPT_LICENSE,'license',count($SCRIPT_LICENSE)-1).'<br>&nbsp;
	</TD>
	</TR>
	<TR><TD COLSPAN="2" ALIGN="MIDDLE">
		<INPUT TYPE="SUBMIT" NAME="SUBMIT" VALUE="'._XF_G_SEARCH.'">
	</TD></TR>
	</TABLE>
	</FORM>';
*/
	$return='<TABLE border=0><TR><TD>
				<FORM name="snippetsearchform" method="GET" action="'.XOOPS_URL.'/modules/websearch/proxy.php">
				Search <SELECT name="collection">
				<OPTION value="ForgeSampleCode;SampleCode">All
				<OPTION value="ForgeSampleCode" selected>Sample on Forge
				<OPTION value="SampleCode">Sample on NDK
				</SELECT><BR>
				<INPUT type="hidden" name="theme" value="forge">
				<INPUT type="text" name="query" size="25"><br>
				<div align=right><img src="images/button_search.gif" width="51" height="17" border="0" alt="Search" title="Search" onclick=javascript:document.forms["snippetsearchform"].submit() /></div>
				</FORM><BR></TD></TR></TABLE>';	
	
	$return.='
		<TABLE WIDTH="100%" BORDER="0">
		<TR><TD></TD></TR>
		<TR><TD valign=top>
			<B>'._XF_SNP_BROWSEBYLANGUAGE.':</B>
			<P>';
	
	$res_language = $xoopsDB->query("SELECT type_id,name FROM ".
		$xoopsDB->prefix("xf_snippet_language")." WHERE type_id>100");

//	$count = $xoopsDB->getRowsNum($res_language);
	while ($db = $xoopsDB->fetchArray($res_language)) 
	{
		$sql = "SELECT COUNT(*) FROM ".
			$xoopsDB->prefix("xf_snippet")." WHERE language=".
			$db['type_id'];

		$result = $xoopsDB->query ($sql);

		$return .= '
			<LI><A HREF="'. XOOPS_URL.
			'/modules/xfsnippet/browse.php?by=lang&lang='.
			$db['type_id'].'">'.$db['name'].'</A> ('.
			unofficial_getDBResult($result,0,0).')<BR>';
	}

	$return .= 	
		'</TD>
		<TD valign=top>
		<B>'._XF_SNP_BROWSEBYCATEGORY.':</B>
		<P>';

	$res_category = $xoopsDB->query("SELECT type_id,name FROM ".
		$xoopsDB->prefix("xf_snippet_category")." WHERE type_id>100");
	
//	$count = count($SCRIPT_CATEGORY);
	while ($db = $xoopsDB->fetchArray($res_category)) 
	{
		$sql="SELECT COUNT(*) FROM ".$xoopsDB->prefix("xf_snippet").
			" WHERE category=".$db['type_id'];

		$result = $xoopsDB->query ($sql);

		$return .= '
			<LI><A HREF="'.XOOPS_URL.
			'/modules/xfsnippet/browse.php?by=cat&cat='.
			$db['type_id'].'">'.$db['name'].'</A> ('.
			unofficial_getDBResult($result,0,0).')<BR>';
	}


	$return .=
		'</TD>
		</TR>
		</TABLE>';

	return $return;
}

?>
