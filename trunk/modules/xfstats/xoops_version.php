<?php
$modversion['name'] = "Statistics";
$modversion['version'] = 1.0;
$modversion['description'] = "Forge Statistics";
$modversion['author'] = "Paul Jones";
$modversion['credits'] = "The XOOPS Project";
$modversion['help'] = "";
$modversion['license'] = "GPL";
$modversion['official'] = 0;
$modversion['image'] = "images/xfstats.png";
$modversion['dirname'] = "xfstats";

// Admin things
$modversion['hasAdmin'] = 0;

// Menu
$modversion['hasMain'] = 1;

// templates
$modversion['templates'][1]['file'] = 'xfstats_index.html';
$modversion['templates'][1]['description'] = '';

// blocks
$modversion['blocks'][1]['file'] = "stats.php";
$modversion['blocks'][1]['name'] = "New Projects";
$modversion['blocks'][1]['description'] = "10 Newest projects.";
$modversion['blocks'][1]['show_func'] = "b_stats_new_projects";
$modversion['blocks'][1]['template'] = 'xfstats_block_stats_new_projects.html';

$modversion['blocks'][2]['file'] = "stats.php";
$modversion['blocks'][2]['name'] = "Most Active Projects";
$modversion['blocks'][2]['description'] = "Most active projects block.";
$modversion['blocks'][2]['show_func'] = "b_stats_mostactive";
$modversion['blocks'][2]['template'] = 'xfstats_block_stats_mostactive.html';

$modversion['blocks'][3]['file'] = "stats.php";
$modversion['blocks'][3]['name'] = "Most Active Users";
$modversion['blocks'][3]['description'] = "Most active users block.";
$modversion['blocks'][3]['show_func'] = "b_stats_active_users";
$modversion['blocks'][3]['template'] = 'xfstats_block_stats_active_users.html';

$modversion['blocks'][4]['file'] = "stats.php";
$modversion['blocks'][4]['name'] = "Top Downloaded Release";
$modversion['blocks'][4]['description'] = "Top downloaded release block.";
$modversion['blocks'][4]['show_func'] = "b_stats_topdownloads";
$modversion['blocks'][4]['template'] = 'xfstats_block_stats_topdownloads.html';


?>