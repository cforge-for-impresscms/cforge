<?php
$modversion['name'] = _WEBSEARCH_NAME;
$modversion['version'] = 1.0;
$modversion['description'] = _WEBSEARCH_DESC;
$modversion['author'] = "Paul Jones (forge.novell.com)";
$modversion['credits'] = "The XOOPS Project";
$modversion['help'] = "";
$modversion['license'] = "GPL";
$modversion['official'] = 0;
$modversion['image'] = "xf_slogo.gif";
$modversion['dirname'] = "websearch";

// Admin things
$modversion['hasAdmin'] = 0;

// Menu
$modversion['hasMain'] = 1;

// Blocks
$modversion['blocks'][1]['file'] = "websearch.php";
$modversion['blocks'][1]['name'] = _WEBSEARCH_BNAME1;
$modversion['blocks'][1]['description'] = _WEBSEARCH_BDESC1;
$modversion['blocks'][1]['show_func'] = "b_websearch_show";
?>