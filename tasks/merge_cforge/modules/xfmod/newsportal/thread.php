<?php
	include_once "config.inc";
	// register parameters
	$mygroup = $_REQUEST["group"];
	$first = $_REQUEST["first"];
	$last = $_REQUEST["last"];
	 
	$title .= ' - '.$mygroup;
	include_once "head.inc";
	 
?>

<a name="top"/>
<h1 align="center"><?php echo $mygroup; ?></h1>

<p align="center">
<?php
	if (!$readonly)
	if ($icmsUser != null)
	echo '[<a href="'.$file_post.'?group_id='.$group_id.'&newsgroups='.urlencode($mygroup).'&amp;type=new">'.$text_thread["button_write"]."</a>] ";
	else
		echo '[<a href="'.ICMS_URL.'/user.php?icms_redirect='.$_SERVER['PHP_SELF'].'?'.urlencode($_SERVER['QUERY_STRING']).'">Log in to post</a>]';
	//  echo '[<a href="'.$file_groups.'">'.$text_thread["button_grouplist"].'</a>]';
?>
</p>

<?php
	include_once("$file_newsportal");
	$ns = OpenNNTPconnection($server, $port);
	flush();
	if ($ns != false)
	{
		if ($first > $maxarticles || $last > $maxarticles) $old_articles = true;
		else $old_articles = false;
		 
		$total_count = $article_count = getNumArticles($ns, $mygroup);
		if ($old_articles)
		{
			$headers = readOverview($ns, $mygroup, 1, false, $first, $last);
		}
		else
			{
			$headers = readOverview($ns, $mygroup, 1);
			$article_count = count($headers);
		}
		if ($articles_per_page != 0)
		{
			if ((!isset($first)) || (!isset($last)))
			{
				if ($startpage == "first")
				{
					$first = 1;
					$last = $articles_per_page;
				}
				else
				{
					$first = $article_count -(($article_count -1) % $articles_per_page);
					$last = $article_count;
				}
			}
			$page_menu = getPageSelectMenu($mygroup, $total_count, $first);
			echo '<p align="center">'.$page_menu.'</p>';
		}
		else
		{
			$first = 0;
			$last = $article_count;
		}
		if ($old_articles) showHeaders($headers, $mygroup, 30, 80);
		else showHeaders($headers, $mygroup, $first, $last);
		 
		if ($articles_per_page != 0)
		{
			echo '<p align="center">'.$page_menu.'</p>';
		}
		 
		closeNNTPconnection($ns);
	}
?>

<p align="right"><a href="#top"><?php echo $text_thread["button_top"];?></a></p>

<?php include_once "tail.inc"; ?>