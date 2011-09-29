<?php

function b_websearch_show(){

	$block = array();
        $block['title'] = "Search";
        $block['content'] = "<form name='websearchform' method=get action='".XOOPS_URL."/modules/websearch/proxy.php'>\n";
	$block['content'] .="<div align='center'><SELECT name=collection>";
	$block['content'] .="<OPTION value='ForgeDocumentation;ForgeFAQ;ForgeForumsNews;ForgeProjects;ForgeSampleCode;ndk_doc;dev_research;SampleCode;DevForums;Cool Solutions'>All";
	$block['content'] .="<OPTION value='ForgeDocumentation;ndk_doc;dev_research'>Documentation";
	$block['content'] .="<OPTION value='ForgeFAQ'>FAQ";
	$block['content'] .="<OPTION value='ForgeForumsNews;DevForums'>Forums/News";
	$block['content'] .="<OPTION value='ForgeProjects' Selected>Projects";
	$block['content'] .="<OPTION value='ForgeSampleCode;SampleCode'>Sample Code";
	$block['content'] .="</SELECT></div>";
	$block['content'] .= "<input type='hidden' name='theme' value='forge'>";
	$block['content'] .= "<div align='center'><input type='text' name='query' style='width: 90%; ' /></div>\n";
	$block['content'] .= "<div align='right'><img src='".XOOPS_URL."/modules/websearch/images/button_search.gif' width='51' height='17' border='0' alt='Search' title='Search' onclick=javascript:document.forms['websearchform'].submit() /></div>";
	$block['content'] .= "</form>";
	$block['content'] .= "<div align='left'>&nbsp;&nbsp;<img src='".XOOPS_URL."/modules/websearch/images/n_arrows_grey.gif' width='7' height='7' border='0' alt=''>&nbsp;&nbsp;<a href='".XOOPS_URL."/modules/xftrove/project_list.php'>view project list a-z</a></div>";
	$block['content'] .= "<div align='left'>&nbsp;&nbsp;<img src='".XOOPS_URL."/modules/websearch/images/n_arrows_grey.gif' width='7' height='7' border='0' alt=''>&nbsp;&nbsp;<a href='".XOOPS_URL."/modules/xftrove/trove_list.php'>view project categories</a></div>";
	$block['content'] .= "<div align='left'>&nbsp;&nbsp;<img src='".XOOPS_URL."/modules/websearch/images/n_arrows_grey.gif' width='7' height='7' border='0' alt=''>&nbsp;&nbsp;<a href='".XOOPS_URL."/modules/websearch/proxy.php?theme=forge'>advanced search</a></div>";
	return $block;	
}

?>