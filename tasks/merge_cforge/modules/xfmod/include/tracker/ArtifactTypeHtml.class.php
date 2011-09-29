<?php
	/**
	*
	* SourceForge Generic Tracker facility
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: ArtifactTypeHtml.class,v 1.3 2003/11/26 16:27:35 jcox Exp $
	*
	*/
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/tracker/ArtifactType.class.php");
	 
	class ArtifactTypeHtml extends ArtifactType {
		 
		/**
		*  ArtifactType() - constructor
		*
		*  @param $Group object
		*  @param $artifact_type_id - the id # assigned to this artifact type in the db
		*/
		function ArtifactTypeHtml(&$Group, $artifact_type_id = false)
		{
			return $this->ArtifactType($Group, $artifact_type_id);
		}

		function header()
		{
			global $icmsUser, $feedback;
			 
			$group_id = $this->Group->getID();
			 
			$content['title'] = project_title($this->Group);
			//echo "<B style='font-size:16px;align:left;'>".$this->getName()."</strong><br />";
			$tabselect = "tracker";
			if ($this->getName() == "Patches")
			$tabselect = "patch";
			if ($this->getName() == "Bugs")
			$tabselect = "bugs";
			if ($this->getName() == "Support Requests")
			$tabselect = "support";
			if ($this->getName() == "Feature Requests")
			$tabselect = "feature";
			 
			$content['tabs'] = project_tabs($tabselect, $group_id);
			 
			$nav = '<P/>';
			 
			if ($this->userIsAdmin())
			{
				$nav .= "<strong><a href='".ICMS_URL."/modules/xfmod/tracker/admin/?group_id=".$group_id."'>"._XF_G_ADMIN."</a> | </strong>";
			}

			$nav .= "<strong>" ."<a href='".ICMS_URL."/modules/xfmod/tracker/?func=browse&group_id=".$group_id."&atid=".$this->getID()."'>"._XF_G_BROWSE."</a>" ." | " ."<a href='".ICMS_URL."/modules/xfmod/tracker/?func=add&group_id=".$group_id."&atid=".$this->getID()."'>"._XF_TRK_ATHSUBMITNEW."</a></strong><BR />";

			//if($feedback)
			//  $content .= "<p><strong style='color:#FF0000;'>".$feedback."</strong></p>";
			 
			$content['nav'] = $nav;
			 
			//echo '<HR NoShade size="1" size="90%">';
			return $content;
		}
		 
		function footer()
		{
			//global $icmsTheme;
			//CloseTable();
			//include(ICMS_ROOT_PATH."/footer.php");
		}
		 
		function adminHeader()
		{
			global $feedback;
			 
			$adminheader = $this->header();
			$group_id = $this->Group->getID();
			//$nav = "<strong>"._XF_TRK_ATHADMINFUNCTIONS.": <a href='".ICMS_URL."/modules/xfmod/tracker/admin/?group_id=".$group_id."'>"._XF_TRK_ATHADDBROWSETYPES."</a>";
			$nav = " | <strong><a href='".ICMS_URL."/modules/xfmod/tracker/admin/?group_id=".$group_id."&atid=".$this->getID()."'>".sprintf(_XF_TRK_ATHEDITUPDATEOPTIONS, $this->getName())."</a></strong>";
			 
			$adminheader['nav'] .= $nav;
			 
			return $adminheader;
		}
		 
		function categoryBox($name = 'category_id', $checked = 'xzxz', $text_100 = _XF_G_NONE)
		{
			return html_build_select_box($this->getCategories(), $name, $checked, true, $text_100);
		}
		 
		function artifactGroupBox($name = 'artifact_group_id', $checked = 'xzxz', $text_100 = _XF_G_NONE)
		{
			return html_build_select_box($this->getGroups(), $name, $checked, true, $text_100);
		}
		 
		function technicianBox($name = 'assigned_to', $checked = 'xzxz', $show_100 = true, $text_100 = _XF_G_NONE)
		{
			return html_build_select_box($this->getTechnicians(), $name, $checked, $show_100, $text_100);
		}
		 
		function cannedResponseBox($name = 'canned_response', $checked = 'xzxz')
		{
			return html_build_select_box($this->getCannedResponses(), $name, $checked);
		}
		 
		function statusBox($name = 'status_id', $checked = 'xzxz', $show_100 = false, $text_100 = _XF_G_NONE)
		{
			return html_build_select_box($this->getStatuses(), $name, $checked, $show_100, $text_100);
		}
		 
		function resolutionBox($name = 'resolution_id', $checked = 'xzxz', $show_100 = false, $text_100 = _XF_G_NONE)
		{
			return html_build_select_box($this->getResolutions(), $name, $checked, $show_100, $text_100);
		}
		 
		function showBrowseList($result, $offset, $set = 'open')
		{
			global $sys_datefmt, $PHP_SELF, $icmsDB, $ts;
			$group_id = $this->Group->getID();
			 
			$IS_ADMIN = $this->userIsAdmin();
			 
			$content = '';
			 
			if ($IS_ADMIN)
			{
				$content .= "<form name='artifactList' ACTION='".$PHP_SELF."?group_id=".$group_id."&atid=".$this->getID()."' METHOD='POST'>" ."<input type='hidden' name='func' value='massupdate'>";
			}
			 
			$content .= "<table border='2' width='100%' cellpadding='5' cellspacing='1'>" ."<tr class='bg2'>";
			if ($IS_ADMIN)
			{
				$content .= "<th width='1%' align='center'><input name='allbox' type='checkbox' onClick='checkAll();' title='"._XF_TRK_ATHSELECTALL."'></tr>";
			}
			$content .= "<th align='center'><strong>"._XF_TRK_ATHREQID."</strong></tr>" ."<th align='center'><strong>"._XF_G_SUMMARY."</strong></tr>";
			 
			if ($this->useResolution())
			{
				$content .= "<th align='center'><strong>"._XF_TRK_ATHRESOLUTION."</strong></tr>";
			}
			 
			$content .= "<th align='center'><strong>"._XF_G_DATE."</strong></tr>" ."<th align='center'><strong>"._XF_G_ASSIGNEDTO."</strong></tr>" ."<th align='center'><strong>"._XF_G_SUBMITTEDBY."</strong></tr>" ."</tr>";
			 
			$then = (time() - $this->getDuePeriod());
			$rows = $icmsDB->getRowsNum($result);
			 
			for($i = 0; $i < $rows; $i++)
			{
				$content .= "<th BGCOLOR='".get_priority_color(unofficial_getDBResult($result, $i, 'priority'))."'>";
				if ($IS_ADMIN)
				{
					$content .= "<td width='1%'>" ."<input name='artifact_id_list[]' type='checkbox' onClick='checkOne(this);' value='".unofficial_getDBResult($result, $i, 'artifact_id')."'>" ."</td>";
				}
				$content .= "<td nowrap>".unofficial_getDBResult($result, $i, 'artifact_id')."</td>" ."<td><a href='".$PHP_SELF."?func=detail&aid=".unofficial_getDBResult($result, $i, 'artifact_id')
				."&group_id=".$group_id."&atid=".$this->getID()."'>" .$ts->makeTboxData4Show(unofficial_getDBResult($result, $i, 'summary'))."</a></td>";
				 
				if ($this->useResolution())
				{
					$content .= "<td>".unofficial_getDBResult($result, $i, 'resolution_name')."</td>";
				}
				 
				$content .= "<td>".(($set != 'closed' && unofficial_getDBResult($result, $i, 'date') < $then)?"<strong>* ":"&nbsp; ").date($sys_datefmt, unofficial_getDBResult($result, $i, 'date')) ."</td>" ."<td>".unofficial_getDBResult($result, $i, 'assigned_to')."</td>" ."<td>".unofficial_getDBResult($result, $i, 'submitted_by')."</td></tr>";
			}
			 
			/*
			Show extra rows for <-- Prev / Next -->
			*/
			if (($offset > 0) || ($rows >= 50))
			{
				$content .= "<tr><td colspan='2'>";
				if ($offset > 0)
				{
					$content .= '<a href="'.$PHP_SELF.'?func=browse&group_id='.$group_id.'&atid='.$this->getID().'&set='.$set.'&offset='.($offset-50).'"><strong><-- '._XF_G_PREVIOUS.' 50</strong></a>';
				}
				else
				{
					$content .= '&nbsp;';
				}
				$content .= '</td><td>&nbsp;</td><td colspan="2">';
				 
				if ($rows >= 50)
				{
					$content .= '<a href="'.$PHP_SELF.'?func=browse&group_id='.$group_id.'&atid='.$this->getID().'&set='.$set.'&offset='.($offset+50).'"><strong>'._XF_G_NEXT.' 50 --></strong></a>';
				}
				else
				{
					$content .= '&nbsp;';
				}
				$content .= '</td></tr>';
			}
			 
			/*
			Mass Update Code
			*/
			if ($IS_ADMIN)
			{
				$content .= '<script language="JavaScript">
					<!--
					function checkAll() {
					 
					al = document.artifactList;
					var i = 0;
					for(i = 0; i < al.elements.length; i++) {
					var e = al.elements[i];
					if (al.elements[i].name == \'artifact_id_list[]\') {
					al.elements[i].checked = al.allbox.checked;
					}
					}
					}
					 
					function checkOne(item) {
					 
					if (!item.checked && document.artifactList.allbox.checked) {
					document.artifactList.allbox.checked=0;
					return;
					}
					if (item.checked) {
					al = document.artifactList;
					var cnt = 0;
					var pnt = 0;
					for(var i = 0; i < al.elements.length; i++) {
					var e = al.elements[i];
					if (e.name == \'artifact_id_list[]\') {
					pnt++;
					if (e.checked) {
					cnt++;
					}
					}
					}
					if (cnt == pnt) {
					document.artifactList.allbox.checked=1;
					}
					}
					}
					//-->
					</script>
					<tr><td colspan="6">
					 
					<FONT COLOR="#FF0000"><strong>'._XF_G_ADMIN.':</strong></FONT> '._XF_TRK_ATHIFAPPLYTOALL.'
					<br />
					<table width="100%" border="2">
					 
					<tr>
					<td><strong>'._XF_TRK_ATHCATEGORY.':</strong><BR>'. $this->categoryBox('category_id', 'xzxz', _XF_G_NOCHANGE) .'</td>
					<td><strong>'._XF_TRK_ATHGROUP.':</strong><BR>'. $this->artifactGroupBox('artifact_group_id', 'xzxz', _XF_G_NOCHANGE) .'</td>
					</tr>
					 
					<tr>
					<td><strong>'._XF_G_PRIORITY.':</strong><BR>';
				$content .= build_priority_select_box('priority', 'xzxz', _XF_G_NOCHANGE);
				$content .= '</td><td>';
				if ($this->useResolution())
				{
					$content .= '
						<strong>'._XF_TRK_ATHRESOLUTION.':</strong><BR>';
					$content .= $this->resolutionBox('resolution_id', 'xzxz', true, _XF_G_NOCHANGE);
				}
				else
				{
					$content .= '&nbsp;
						<input type="hidden" name="resolution_id" value="100">';
				}
				 
				$content .= '</td>
					</tr>
					 
					<tr>
					<td><strong>'._XF_G_ASSIGNEDTO.':</strong><BR>'. $this->technicianBox('assigned_to', 'xzxz', true, _XF_G_NOCHANGE) .'</td>
					<td><strong>'._XF_TRK_ATHSTATUS.':</strong><BR>'. $this->statusBox('status_id', 'xzxz', true, _XF_G_NOCHANGE) .'</td>
					</tr>
					 
					<tr><td colspan="2"><strong>'._XF_TRK_ATHCANNEDRESP.':</strong><BR>'. $this->cannedResponseBox('canned_response') .'</td></tr>
					 
					<tr><td colspan="3" align="MIDDLE"><input type="submit" name="submit" value="'._XF_TRK_ATHMASSUPDATE.'"></td></tr>
					 
					</table>
					</form>
					</td></tr>';
			}
			$content .= '</table>';
			return $content;
		}
	}
	 
?>