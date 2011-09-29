<?php
	/**
	* software_map.php
	*
	* @version   $Id: software_map.php,v 1.3 2004/01/15 20:24:53 devsupaul Exp $
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "help.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/help/help_utils.php");
	 
	include_once(ICMS_ROOT_PATH."/modules/xoopspoll/language/english/blocks.php");
	include_once(ICMS_ROOT_PATH."/modules/xoopspoll/language/english/main.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/survey.php");
	include_once(ICMS_ROOT_PATH."/modules/xfmod/language/english/forum.php");
	 
	//meta tag information
	$metaTitle = ": "._XF_H_HELP;
	 
	include("../../../header.php");
	 
	$site = $icmsConfig['sitename'];
	 
	help_menu("software map");
	 
	begin_help_content();
	 
	$title = "About The Software Map";
	$content = "<p>The Software Map is used to present all of the projects and communities " . "in the site in an organized and easily searchable fashion.  Using the Software " . "Map, it is easy to search for projects or communities based on certain topics, " . "areas of interest, or by name." . "<p>Projects or communities do not automatically appear in the software map.  " . "A project or community administrator must first categorize their project or " . "community.  So the Software Map is more precisely a presentation of all " . "categorized projects and communities.";
	themesidebox_help($title, $content, "about");
	echo "<br><br>\n";
	 
	$title = "How Do I Use The Software Map List View?";
	$content = "<p>The Software Map List View presents a list of all the projects and " . "communities in the site, by default.  There are several ways to locate projects " . "and communities in the List View." . "<ul><li>You can search for a project or community by name by entering search " . "criteria in the \"Starts With\" box and clicking \"Search\"" . "<li>You can search for a project or community by content by entering search " . "criteria in the \"Contains\" box and clicking \"Search\"" . "<li>You can use the filter list to only view projects and communities whose name " . "begins with a specific character</ul>" . "<p>To use the List View, click the \"List\" link at the top of the Software Map " . "page.";
	themesidebox_help($title, $content, "list_view");
	echo "<br><br>\n";
	 
	$title = "How Do I Use The Software Map Map View?";
	$content = "<p>The Software Map Map View presents a hierarchical view of the projects " . "and communities in the site, organized by some criteria.  You can view select " . "which category to browse by in the list on the right side of the page, then " . "further narrow the selection set by selecting the subcategory on the left that " . "you are interested in.  You can also browse projects according to the community " . "or communities that they pertain to by selecting \"Community\" as the search " . "category on the right side." . "<p>To use the Map View, click the \"Map\" link at the top of the Software Map " . "page.  The Map View is the default view.";
	themesidebox_help($title, $content, "map_view");
	echo "<br><br>\n";
	 
	$title = "How Do I Categorize My Project?";
	$content = "<p>One of the first tasks to complete after your project is approved is that " . "of completing your project categorization.  If you fail to complete this task, it " . "will be extremely difficult for other users of the site to find your project " . "information; completing the task, however, makes it very easy for other users to " . "find your project page." . "<p>To begin this task, you must first navigate to the administrative page of your " . "project by clicking the \""._XF_G_ADMIN."\" link in your project page.  The task " . "you want to undertake is that of editing your trove categorization.  When you " . "select this task, you will see a page that allows you to select the appropriate " . "subcategory for every category in the Software Map:  Development Status, Intended " . "Audience, License, etc.  You can also select up to three communities with which " . "to associate your project." . "<p>A scheduled task runs periodically to update the software map, so it is not " . "reasonable to expect to see your newly categorized project appear immediately in " . "the software map.  Check back in a couple of days, after which time you should " . "find that your project appears in the Software Map as categorized.";
	themesidebox_help($title, $content, "categorizing_projects");
	echo "<br><br>\n";
	 
	end_help_content();
	 
	include("../../../footer.php");
?>