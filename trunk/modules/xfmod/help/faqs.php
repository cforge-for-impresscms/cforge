<?php 
/**
 * faqs.php
 *
 * @version   $Id: faqs.php,v 1.3 2004/01/15 20:24:53 devsupaul Exp $
 */
include_once("../../../mainfile.php");

$langfile="help.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/help/help_utils.php");

//meta tag information
$metaTitle=": "._XF_H_HELP;

include ("../../../header.php");

$site = $xoopsConfig['sitename'];

help_menu("faqs");

begin_help_content();

$title = "About FAQs";
$content = "<p>FAQ is an acronym for Frequently Asked Questions.  In the sense of $site, "
	. "a FAQ is a series of frequently asked questions and the answers to those questions. "
	. "The purpose of a FAQ is to provide a quick answer to questions that are commonly "
	. "asked, and to provide a quick feedback mechanism by which new answers to new "
	. "questions can easily be provided, thus keeping the FAQ up-to-date.";
themesidebox_help($title,$content,"about");
echo "<br><br>";

$title = "How Do I View FAQs?";
$content = "<p>Click the FAQ link in the main menu to access the general FAQ area of "
	. "$site.  All the FAQs are organized by category.  By using this page, you can "
	. "view all of the FAQs available in the site."
	. "<p>You can also go to a community page and click on the FAQ link in the community "
	. "menu.  This will take you to the FAQ, if any, for that specific community."
	. "<p>Once you find the FAQ you want, you can browse the questions and answers simply "
	. "by clicking on the question itself.";
themesidebox_help($title,$content,"viewing_faqs");
echo "<br><br>";

$title = "How Do I Administer FAQs?";
$content = "<p>Most FAQs are administered by site admins.  Some FAQs are administered "
	. "by an individual within a community that has community admin rights."
	. "<p>If you are a community admin, you can create a new FAQ by clicking the \""
	. _XF_G_ADMIN."\" link in FAQ area of the community.  You are presented with an "
	. "option to either create a new FAQ or to link to an existing FAQ."
	. "<p>To create a new FAQ, you simply fill in the title for the FAQ and submit.  "
	. "Once this process is completed, you can add question and answer pairs to your "
	. "FAQ via the community FAQ page."
	. "<p>To link to an existing FAQ, simply select the FAQ you wish to link to from "
	. "the list by clicking on the \"Link\" button next to the FAQ.";
themesidebox_help($title,$content,"administering_faqs");
echo "<br><br>";

end_help_content();

include("../../../footer.php");
?>