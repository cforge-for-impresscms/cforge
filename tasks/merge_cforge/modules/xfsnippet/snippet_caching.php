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
		global $icmsDB, $SCRIPT_LICENSE;
		 
		$return = '<p>'._XF_SNP_PURPOSE1.'</p><p>'._XF_SNP_PURPOSE2.'</p>';
		/* // sql queries
		$sql_type = $icmsDB->query("SELECT type_id,name FROM ".$icmsDB->prefix("xf_snippet_type")." ORDER BY name ASC");
		$SCRIPT_TYPE_ids = util_result_column_to_array($sql_type, 0);
		$SCRIPT_TYPE_val = util_result_column_to_array($sql_type, 1);
		$SCRIPT_TYPE_val[0] = 'Any';
		// sql queries
		$sql_category = $icmsDB->query("SELECT type_id,name FROM ".$icmsDB->prefix("xf_snippet_category")." ORDER BY name ASC");
		$SCRIPT_CATEGORY_ids = util_result_column_to_array($sql_category, 0);
		$SCRIPT_CATEGORY_val = util_result_column_to_array($sql_category, 1);
		$SCRIPT_CATEGORY_val[0] = 'Any';
		// sql queries
		$sql_language = $icmsDB->query("SELECT type_id,name FROM ".$icmsDB->prefix("xf_snippet_language")." ORDER BY name ASC");
		$SCRIPT_LANGUAGE_ids = util_result_column_to_array($sql_language, 0);
		$SCRIPT_LANGUAGE_val = util_result_column_to_array($sql_language, 1);
		$SCRIPT_LANGUAGE_val[0] = 'Any';
		// sql queries
		$SCRIPT_LICENSE[]='Any';
		 
		$return.='
		<form action="'.ICMS_URL.'/modules/xfsnippet/browse.php" METHOD="GET">
		<table>
		<th>
		<td colspan=2>
		<strong>Contains:</strong><br>
		<input type="text" name="text" size=30>
		</td>
		</th>
		<th>
		<td><strong>'._XF_SNP_LANGUAGE.':</strong><BR>
		'.html_build_select_box_from_arrays($SCRIPT_LANGUAGE_ids,$SCRIPT_LANGUAGE_val,'lang',$SCRIPT_LANGUAGE_ids[0],false).'
		<BR>
		<a href="'.ICMS_URL.'/modules/xfmod/tracker/?func=add&group_id=1011&atid=145">Suggest a Language</a>
		</td>
		 
		<td><strong>'._XF_SNP_CATEGORY.':</strong><BR>
		'.html_build_select_box_from_arrays($SCRIPT_CATEGORY_ids,$SCRIPT_CATEGORY_val,'cat',$SCRIPT_CATEGORY_ids[0],false).'
		<BR>
		<a href="'.ICMS_URL.'/modules/xfmod/tracker/?func=add&group_id=1011&atid=145">Suggest a Category</a>
		</td>
		</th>
		<th>
		<td><strong>'._XF_SNP_TYPE.':</strong><BR>
		'.html_build_select_box_from_arrays($SCRIPT_TYPE_ids,$SCRIPT_TYPE_val,'type',$SCRIPT_TYPE_ids[0],false).'
		<BR>
		<a href="'.ICMS_URL.'/modules/xfmod/tracker/?func=add&group_id=1011&atid=145">Suggest a Script Type</a>
		</td>
		 
		<td><strong>'._XF_SNP_LICENSE.':</strong><BR>
		'.html_build_select_box_from_array($SCRIPT_LICENSE,'license',count($SCRIPT_LICENSE)-1).'<br>&nbsp;
		</td>
		</th>
		<th><td colspan="2" align="MIDDLE">
		<input type="submit" name="submit" value="'._XF_G_SEARCH.'">
		</td></th>
		</table>
		</form>';
		*/
		$return = '<table border=0><th><td>
			<form name="snippetsearchform" method="GET" action="'.ICMS_URL.'/modules/websearch/proxy.php">
			Search <select name="collection">
			<option value="ForgeSampleCode;SampleCode">All
			<option value="ForgeSampleCode" selected>Sample on Forge
			<option value="SampleCode">Sample on NDK
			</select><BR>
			<input type="hidden" name="theme" value="forge">
			<input type="text" name="query" size="25"><br>
			<div align=right><img src="images/button_search.gif" width="51" height="17" border="0" alt="Search" title="Search" onclick=javascript:document.forms["snippetsearchform"].submit() /></div>
			</form><BR></td></th></table>';
		 
		$return .= '
			<table width="100%" border="0">
			<th><td></td></th>
			<th><td valign=top>
			<strong>'._XF_SNP_BROWSEBYLANGUAGE.':</strong>
			<p>';
		 
		$res_language = $icmsDB->query("SELECT type_id,name FROM ". $icmsDB->prefix("xf_snippet_language")." WHERE type_id>100");
		 
		// $count = $icmsDB->getRowsNum($res_language);
		while ($db = $icmsDB->fetchArray($res_language))
		{
			$sql = "SELECT COUNT(*) FROM ". $icmsDB->prefix("xf_snippet")." WHERE language=". $db['type_id'];
			 
			$result = $icmsDB->query ($sql);
			 
			$return .= '
				<LI><a href="'. ICMS_URL. '/modules/xfsnippet/browse.php?by=lang&lang='. $db['type_id'].'">'.$db['name'].'</a> ('. unofficial_getDBResult($result, 0, 0).')<BR>';
		}
		 
		$return .= '</td>
			<td valign=top>
			<strong>'._XF_SNP_BROWSEBYCATEGORY.':</strong>
			<p>';
		 
		$res_category = $icmsDB->query("SELECT type_id,name FROM ". $icmsDB->prefix("xf_snippet_category")." WHERE type_id>100");
		 
		// $count = count($SCRIPT_CATEGORY);
		while ($db = $icmsDB->fetchArray($res_category))
		{
			$sql = "SELECT COUNT(*) FROM ".$icmsDB->prefix("xf_snippet"). " WHERE category=".$db['type_id'];
			 
			$result = $icmsDB->query ($sql);
			 
			$return .= '
				<LI><a href="'.ICMS_URL. '/modules/xfsnippet/browse.php?by=cat&cat='. $db['type_id'].'">'.$db['name'].'</a> ('. unofficial_getDBResult($result, 0, 0).')<BR>';
		}
		 
		 
		$return .= '</td>
			</th>
			</table>';
		 
		return $return;
	}
	 
?>