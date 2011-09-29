<?php

function help_menu( $where )
{
	$helpmenu = array(
		"About" => "",
		"Accounts" => array("Purpose",
							"Creating",
							"Logging In",
							"Logging Out",
							"Account Administration"),
		"Projects" => array("About",
							"Finding Projects",
							"Viewing Projects",
							"Creating Projects",
							"Contributing to Projects",
							"Administering Projects",
							"Downloading Projects",
							"Forums",
							"Trackers",
							"Tasks",
							"Documents",
							"Mailing Lists",
							"Surveys",
							"News",
							"CVS",
							"Red Carpet"),
		"Communities" => array("About",
							   "Finding Communities",
							   "Viewing Communities",
							   "Creating Communities",
							   "Contributing to Communities",
							   "Administering Communities",
							   "Forums",
							   "FAQs",
							   "Documents",
							   "Articles",
							   "Mailing Lists",
							   "Surveys",
							   "News"),
		"Project Categories" => array("About",
								"List View",
								"Category View",
								"Categorizing Projects"),
		"Forums" => array("About",
						  "Viewing Forums",
						  "Posting to Forums",
						  "Creating Forums",
						  "Linking Forums and Mailing Lists"),
		"News" => array("About",
						"Viewing News",
						"Submitting News",
						"Administering News"),
		"Polls" => array("About",
						 "Viewing Polls",
						 "Voting in Polls",
						 "Creating Polls",
						 "Administering Polls",
						 "Poll Privacy"),
		"Code Snippets" => array("About",
								 "Viewing Code Snippets",
								 "Creating Code Snippets",
								 "Managing Code Snippets",
								 "Snippet Suggestions"),
		"FAQs" => array("About",
						"Viewing FAQs",
						"Administering FAQs"),
		"Jobs" => array("About",
						"Viewing Jobs",
						"Managing Your Skills Profile",
						"Applying for Jobs",
						"Posting Jobs",
						"Editing Jobs"),
		"Help" => "");

	// Render the main menu.
	$size = count($helpmenu);
	$i=1;
	$sm = "";
	echo "<h2>"._XF_H_HOWDOI."</h2>";
	echo "<table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td height='1' style='background-image:url(".XOOPS_URL."/modules/xfmod/images/dotlinebg_horiz.gif)'><img src='".XOOPS_URL."/modules/xfmod/images/spacer.gif' width='173' height='1' border='0' alt=''></td><td width='20'></td></tr></table>";
	echo "<table border='0' cellpadding='0' cellspacing='0' width='100%'><td><span id='projectnav'>";
	foreach ($helpmenu as $menu_item => $submenu)
	{
		if ( 0 == strcasecmp($where, $menu_item) )
		{
			echo "<B><span style='white-space: nowrap; color: #CC0000;'> <img src='".XOOPS_URL."/modules/xfmod/images/n_arrows_grey.gif' width='7' height='7' alt=''> ".$menu_item." </span></B>";
			$smsize = count($submenu);
			$j=1;
			$sm = "<ol>";
			foreach($submenu as $submenu_item)
			{
				$sm .= "<li><a href=\"".$_SERVER['PHP_SELF']."#".strtr(strtolower($submenu_item),' ','_')."\">$submenu_item</a></li>";
			}
			$sm .= "</ol>";
		}
		else
		{
			echo "<a href=\"".strtr(strtolower($menu_item),' ','_').".php\"><span style='white-space: nowrap;'> $menu_item </span></a>";
		}
		if ( $i++ != $size )
		{
			echo "<img src='".XOOPS_URL."/modules/xfmod/images/dotline_vert13px.gif' width='1' height='13' alt=''>";
		}
	}
	echo "</span></td><td width='20'></td></tr></table>";
	echo "<table border='0' cellpadding='0' cellspacing='0' width='100%'><tr><td height='1'></td></tr><tr><td height='1' style='background-image:url(".XOOPS_URL."/modules/xfmod/images/dotlinebg_horiz.gif)'><img src='".XOOPS_URL."/modules/xfmod/images/spacer.gif' width='173' height='1' border='0' alt=''></td><td width='20'></td></tr></table>";
	if ( $sm != "" )
	{
		echo $sm;
	}
}

function themesidebox_help( $title, $content, $a_name="" )
{
	if ( strlen($a_name) )
	{
		echo "<a name='".$a_name."'>";
	}
	themesidebox( $title, $content."<br><br><a href='#'>Go to the top of this page</a>");
}

function begin_help_content()
{
	echo "<br><br>"
		. "<table border='0' width='100%' cellpadding='0' cellspacing='0'>"
		. "<tr><td width='5%'>&nbsp;</td>"
		. "<td width='90%'><table border='0' width='100%' cellpadding='0' cellspacing='0'>"
		. "<tr><td id='centerCcolumn'>";
}

function end_help_content()
{
	echo "</td></tr></table>"
		. "</td><td width='5%'>&nbsp;</td></tr></table>";
}

function themesidebox( $title, $content ){
global $xoopsConfig;
$xoops_imageurl = XOOPS_THEME_URL.'/'.$xoopsConfig['theme_set'].'/';
?>

<table border="0" width="100%" cellspacing="0" cellpadding="0">
<tr>
	<td width="14"><img src="<?php echo $xoops_imageurl;?>h_arrow.gif" width="14" height="26" border="0" alt=""></td>
	<td width="100%" class="head3" valign="bottom">
  	<div class="blockTitle"><?php echo $title;?></div>
	</td>
</tr>
<tr>
	<td colspan="2" style="background-image:url(<?php echo $xoops_imageurl;?>dotlinebg_horiz.gif)"><img src="<?php echo $xoops_imageurl;?>spacer.gif" width="1" height="1" alt=""></td>
</tr>
<tr>
	<td width="14"><img src="<?php echo $xoops_imageurl;?>spacer.gif" width="14" height="7" alt=""></td>
	<td width="100%"><img src="<?php echo $xoops_imageurl;?>spacer.gif" width='1' height="7" alt=""></td>
</tr>
<tr>
	<td colspan="2">
		<div class="blockContent"><?php echo $content;?></div>
	</td>
</tr>
</table>

<?php
}

?>