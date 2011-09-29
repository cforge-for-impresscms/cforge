<?php
	/**
	* help.php
	*
	* @version   $Id: help.php,v 1.3 2004/01/15 20:24:53 devsupaul Exp $
	*/
	include_once("../../../mainfile.php");
	 
	$langfile = "help.php";
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/help/help_utils.php");
	 
	//meta tag information
	$metaTitle = ": "._XF_H_HELP;
	 
	include("../../../header.php");
	 
	$site = $icmsConfig['sitename'];
	 
	help_menu("help");
	 
	begin_help_content();
	 
	$title = "How To Use $site Help";
	$content = "<p>The $site context-sensitive help system allows you to access
		and view help files that are specific to the page you are currently viewing.
		Links to these pages are listed, in order of relevance, in the \"How Do I...\" block
		in the left-hand navigation bar on every page.
		<p>The organization of this help system allows it to be very versatile and easily
		modified.  It is a simple matter to edit the contents of the help files, as all
		the help system files are maintained with the source code of the site itself.
		Site admins have the ability to add and modify the links to help files that are
		displayed.
		<p>Once within the help system, you will see the help navigation bar along the top
		of every help page.  Each item in the bar indicates a section of the help system that
		you can view.  The currently displayed section will be indicated by bold text.
		<p>On some pages, an additional subnavigation bar will be displayed.  The contents
		of this bar indicate subsections within the currently displayed page.  Clicking on any
		one of these links will allow you to immediately jump to the section of interest.
		<br>
		<p>The help system is continuously under development.  Your suggestions to improving
		the quality of the help system are welcome.";
	 
	themesidebox_help($title, $content);
	 
	end_help_content();
	 
	 
	include("../../../footer.php");
?>