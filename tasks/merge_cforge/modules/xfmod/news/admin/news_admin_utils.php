<?php
	/**
	*
	* SourceForge News Facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: news_admin_utils.php,v 1.3 2003/12/09 15:03:56 devsupaul Exp $
	*
	*/
	function show_news_approve_form($sql_pending, $sql_rejected, $sql_approved)
	{
		global $icmsDB;
		 
		$content = "";
		// function to show single news item
		// factored out because called 3 time below
		function show_news_item($result, $i, $approved, $selectable)
		{
			 
			global $ts;
			 
			$content = "";
			 
			$content .= '<tr class="'.($i%2 > 0?'bg1':'bg3').'"><td>';
			if ($selectable)
			{
				$content .= '<input type="checkbox" ' .'name="news_id[]" value="' .unofficial_getDBResult($result, $i, 'id').'">';
			}
			$content .= date('Y-m-d', unofficial_getDBResult($result, $i, 'date')).'</td>
				<td>';
			$content .= '
				<a href="'.$_SERVER['PHP_SELF'].'?approve=1&id='.unofficial_getDBResult($result, $i, 'id').'">'.$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'summary')).'</a>
				</td>
				 
				<td>
				<a href="'.ICMS_URL.'/modules/xfmod/project/?' .unofficial_getDBResult($result, $i, 'unix_group_name').'">' .$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'group_name'))
			.'('.unofficial_getDBResult($result, $i, 'unix_group_name').')' .'</a>
				</td>
				</tr>';
			 
			return $content;
		}
		 
		$result = $icmsDB->query($sql_pending);
		$rows = $icmsDB->getRowsNum($result);
		 
		$content .= '<form action="'. $_SERVER['PHP_SELF'] .'" method="POST">';
		$content .= '<input type="hidden" name="mass_reject" value="1">';
		$content .= '<input type="hidden" name="post_changes" value="y">';
		 
		if ($rows < 1)
		{
			$content .= '<H4>'._XF_NWS_NOQUEUEDITEMSFOUND.'</H4>';
		}
		else
		{
			$content .= '<H4>'._XF_NWS_ITEMSNEEDAPPROVAL.'('._XF_NWS_TOTAL.': '.$rows.')</H4>';
			$content .= "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<td><strong>"._XF_G_DATE."</strong></td>" ."<td><strong>"._XF_NWS_TITLE."</strong></td>" ."<td><strong>"._XF_G_PROJECT."</strong></td>" ."</tr>";
			 
			for($i = 0; $i < $rows; $i++)
			{
				$content .= show_news_item($result, $i, false, true);
			}
			$content .= '</table>';
			$content .= '<br><input type="submit" name="submit" value="'._XF_NWS_REJECTSELECTED.'">';
		}
		$content .= '</form>';
		 
		 
		$result = $icmsDB->query($sql_rejected);
		$rows = $icmsDB->getRowsNum($result);
		 
		if ($rows < 1)
		{
			$content .= '
				<H4>'._XF_NWS_NOREJECTEDFOUND.'</H4>';
		}
		else
		{
			$content .= '<H4>'._XF_NWS_ITEMSWEREREJECTED.'('._XF_NWS_TOTAL.': '.$rows.')</H4>';
			$content .= "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<td><strong>"._XF_G_DATE."</strong></td>" ."<td><strong>"._XF_NWS_TITLE."</strong></td>" ."<td><strong>"._XF_G_PROJECT."</strong></td>" ."</tr>";
			 
			for($i = 0; $i < $rows; $i++)
			{
				$content .= show_news_item($result, $i, false, false);
			}
			$content .= '</table>';
		}
		 
		 
		$result = $icmsDB->query($sql_approved);
		$rows = $icmsDB->getRowsNum($result);
		 
		if ($rows < 1)
		{
			$content .= '<H4>'._XF_NWS_NOAPPROVEDFOUND.'</H4>';
		}
		else
		{
			$content .= '<H4>'._XF_NWS_ITEMSWEREAPPROVED.'('._XF_NWS_TOTAL.': '.$rows.')</H4>';
			$content .= "<table border='0' width='100%'>" ."<tr class='bg2'>" ."<td><strong>"._XF_G_DATE."</strong></td>" ."<td><strong>"._XF_NWS_TITLE."</strong></td>" ."<td><strong>"._XF_G_PROJECT."</strong></td>" ."</tr>";
			 
			for($i = 0; $i < $rows; $i++)
			{
				$content .= show_news_item($result, $i, true, false);
			}
			$content .= '</table>';
		}
		return $content;
	}
?>