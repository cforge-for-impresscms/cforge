<?php
// directories and files
$spooldir="spool";
$imgdir="img";
$file_newsportal="newsportal.php";
$file_groups="index.php";
$file_thread="thread.php";
$file_article="article.php";
$file_attachment="attachment.php";
$file_post="post.php";
$file_cancel="cancel.php";
$file_language="lang/english.lang";
$file_footer="";

// newsserver setup
$server="forums.novell.com";
$port=119;
// $post_server="";
// $post_port=119;
$maxfetch=0;
$initialfetch=0;
//$server_auth_user="";
//$server_auth_pass="";
//$server_auth_http=true;

// thread-layout
$thread_treestyle=7;
$thread_fontPre="";
$thread_fontPost="";
$thread_showDate=true;
$thread_date_format="d M y h:ia";
$thread_showSubject=true;
$thread_showAuthor=true;
$thread_maxSubject=80;
$maxarticles=300;
$maxarticles_extra=100;
$age_count=1;
$age_time[1]=172800;
$age_color[1]="red";
$age_img[1]="n_body_new.gif";
//$age_time[2]=259200;
//$age_color[2]="#999900";
//$age_time[3]=604800;
//$age_color[3]="#00bb00";
$thread_sorting=-1;
$articles_per_page=50;
$num_page_tabs=9;
$startpage="first";

// frames
// $frame_article="article";
// $frame_thread="thread";
// $frame_groups="_top";
// $frame_post="_top";
// $frame_threadframeset="_top";
$frame_externallink="_blank";

// article layout
$article_show["Subject"]=true;
$article_show["From"]=true;
$article_show["Newsgroups"]=true;
$article_show["Followup"]=true;
$article_show["Organization"]=true;
$article_show["Date"]=true;
$article_show["Message-ID"]=false;
$article_show["References"]=false;
$article_show["User-Agent"]=false;
$article_showthread=true;

// Attachments
$attachment_show=true;
$attachment_delete_alternative=false; // delete non-text mutipart/alternative
$attachment_uudecode=true;  // experimental!

// Security settings
$send_poster_host=true;
$readonly=false;
$testgroup=false;
$validate_email=1;
$block_xnoarchive=false;

// Misc
$title="Novell Forge";
$organization="Novell Forge";
$cutsignature=true;
$setcookies=false;
$compress_spoolfiles=false;
$cache_articles=false;  // experimental!

//include("extras/lang/english.lang"); // load the english language-definitions first because
//                         // some of the other definitions are incomplete
include($file_language);

//the two following variables must be set to contain email address that the
//news server will recongnize as valid to send and approve newgroup/rmgroup control messages
$control_from = "forge@novell.com";
$control_approve = "forge@novell.com";

?>