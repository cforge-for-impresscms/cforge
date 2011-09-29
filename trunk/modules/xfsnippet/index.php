<?php
/**
  *
  * SourceForge Code Snippets Repository
  *
  * SourceForge: Breaking Down the Barriers to Open Source Development
  * Copyright 1999-2001 (c) VA Linux Systems
  * http://sourceforge.net
  *
  * @version   $Id: index.php,v 1.5 2004/01/30 20:39:22 jcox Exp $
  *
  */
include_once ("../../mainfile.php");
$langfile="snippet.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vars.php");
require_once(XOOPS_ROOT_PATH."/modules/xfsnippet/snippet_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/cache.php");
require_once(XOOPS_ROOT_PATH."/modules/xfsnippet/snippet_caching.php");
$xoopsOption['template_main'] = 'xfsnippet_index.html';

include("../../header.php");

$header = snippet_header();
$xoopsTpl -> assign("title", $header);
$content = cache_display('snippet_mainpage','snippet_mainpage()',3600);
$xoopsTpl -> assign("content", $content);

include("../../footer.php");
?>
