<?php
/**
 * Canned Responses functions library.
 *
 * SourceForge: Breaking Down the Barriers to Open Source Development
 * Copyright 1999-2001 (c) VA Linux Systems
 * http://sourceforge.net
 *
 * @version   $Id: canned_responses.php,v 1.2 2003/12/09 15:03:53 devsupaul Exp $
 */

/**
 * add_canned_response() - Add a new canned response
 *
 * @param		string	Canned response title
 * @param		string	Canned response text
 */
function add_canned_response($title, $text)
{
  global $xoopsDB, $feedback;
	
	if( !$xoopsDB->queryF("INSERT INTO ".$xoopsDB->prefix("xf_canned_responses")." (response_title, response_text) VALUES('$title','$text')") ) {
		$feedback .= $xoopsDB->error();
	}
}

/**
 * get_canned_responses() - Get an HTML select-box of canned responses
 */
function get_canned_responses()
{
	global $canned_response_res, $xoopsDB;

	if (!$canned_response_res) {
		$canned_response_res = $xoopsDB->query("SELECT response_id, response_title FROM ".$xoopsDB->prefix("xf_canned_responses"));
	}

	$html = "<SELECT name='response_id'>\n";
	$html .= "<option value='100'>"._XF_CRSELECTRESPONSE."</option>\n";
	while(list($response_id,$response_title) = $xoopsDB->fetchRow($canned_response_res))
	{
	  $html .= "<option value='".$response_id."'>".$response_title."</option>\n";
	}
	$html .= "</SELECT>";

	return $html;
}

?>