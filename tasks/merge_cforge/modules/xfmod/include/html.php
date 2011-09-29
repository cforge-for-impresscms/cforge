<?php
	/**
	* Misc HTML functions
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: html.php,v 1.4 2004/01/08 20:15:15 devsupaul Exp $
	*/
	 
	/**
	* html_build_select_box_from_array() - Takes one array, with the first array being the "id"
	* or value and the array being the text you want displayed.
	*
	* @param  string The name you want assigned to this form element
	* @param  string The value of the item that should be checked
	*/
	function html_build_select_box_from_array($vals, $select_name, $checked_val = 'xzxz', $samevals = 0)
	{
		$return .= '
			<select name="'.$select_name.'">';
		 
		$rows = count($vals);
		 
		for($i = 0; $i < $rows; $i++)
		{
			if ($samevals)
			{
				$return .= "\r\n\t\t<option value=\"" . $vals[$i] . "\"";
				if ($vals[$i] == $checked_val)
				{
					$return .= ' SELECTED';
				}
			}
			else
			{
				$return .= "\r\n\t\t<option value=\"" . $i .'"';
				if ($i == $checked_val)
				{
					$return .= ' SELECTED';
				}
			}
			$return .= '>'.$vals[$i].'</OPTION>';
		}
		$return .= '
			</select>';
		 
		return $return;
	}
	 
	/**
	* html_build_select_box_from_arrays() - Takes two arrays, with the first array being the "id" or value and the other
	* array being the text you want displayed.
	*
	* The infamous '100 row' has to do with the SQL Table joins done throughout all this code.
	* There must be a related row in users, categories, et , and by default that
	* row is 100, so almost every pop-up box has 100 as the default
	* Most tables in the database should therefore have a row with an id of 100 in it so that joins are successful
	*
	* @param  array The ID or value
	* @param  array Text to be displayed
	* @param  string Name to assign to this form element
	* @param  string The item that should be checked
	* @param  bool Whether or not to show the '100 row'
	* @param  string What to call the '100 row' defaults to none
	*/
	function html_build_select_box_from_arrays($vals, $texts, $select_name, $checked_val = 'xzxz', $show_100 = true, $text_100 = _XF_G_NONE)
	{
		$return = '';
		$return .= '
			<select name="'.$select_name.'">';
		 
		//we don't always want the default 100 row shown
		if ($show_100)
		{
			$return .= '
				<option value="100">'. $text_100 .'</OPTION>';
		}
		 
		$rows = count($vals);
		if (count($texts) != $rows)
		{
			$return .= 'ERROR - uneven row counts';
		}
		 
		$checked_found = false;
		for($i = 0; $i < $rows; $i++)
		{
			//  uggh - sorry - don't show the 100 row
			//  if it was shown above, otherwise do show it
			if (($vals[$i] != '100') || ($vals[$i] == '100' && !$show_100))
			{
				$return .= '
					<option value="'.$vals[$i].'"';
				if ($vals[$i] == $checked_val)
				{
					$checked_found = true;
					$return .= ' SELECTED';
				}
				$return .= '>'.$texts[$i].'</OPTION>';
			}
		}
		//
		// If the passed in "checked value" was never "SELECTED"
		// we want to preserve that value UNLESS that value was 'xzxz', the default value
		//
		if (!$checked_found && $checked_val != 'xzxz' && $checked_val && $checked_val != 100)
		{
			$return .= '
				<option value="'.$checked_val.'" SELECTED>'._XF_G_NOCHANGE.'</OPTION>';
		}
		$return .= '
			</select>';
		return $return;
	}
	 
	/**
	* html_build_select_box() - Takes a result set, with the first column being the "id" or value and
	* the second column being the text you want displayed.
	*
	* @param  int  The result set
	* @param  string Text to be displayed
	* @param  string The item that should be checked
	* @param  bool Whether or not to show the '100 row'
	* @param  string What to call the '100 row'.  Defaults to none.
	*/
	function html_build_select_box($result, $name, $checked_val = "xzxz", $show_100 = true, $text_100 = _XF_G_NONE)
	{
		return html_build_select_box_from_arrays(util_result_column_to_array($result, 0), util_result_column_to_array($result, 1), $name, $checked_val, $show_100, $text_100);
	}
	 
	/**
	* html_build_multiple_select_box() - Takes a result set, with the first column being the "id" or value
	* and the second column being the text you want displayed.
	*
	* @param  int  The result set
	* @param  string Text to be displayed
	* @param  string The item that should be checked
	* @param  int  The size of this box
	* @param  bool Whether or not to show the '100 row'
	*/
	function html_build_multiple_select_box($result, $name, $checked_array, $size = '8', $show_100 = true)
	{
		global $icmsDB;
		$checked_count = count($checked_array);
		$return = '
			<select name="'.$name.'" MULTIPLE size="'.$size.'">';
		if ($show_100)
		{
			/*
			Put in the default NONE box
			*/
			$return .= '
				<option value="100"';
			for($j = 0; $j < $checked_count; $j++)
			{
				if ($checked_array[$j] == '100')
				{
					$return .= ' SELECTED';
				}
			}
			$return .= '>'._XF_G_NONE.'</OPTION>';
		}
		 
		$rows = $icmsDB->getRowsNum($result);
		 
		for($i = 0; $i < $rows; $i++)
		{
			if ((unofficial_getDBResult($result, $i, 0) != '100') || (unofficial_getDBResult($result, $i, 0) == '100' && !$show_100))
			{
				$return .= '
					<option value="'.unofficial_getDBResult($result, $i, 0).'"';
				/*
				Determine if it's checked
				*/
				$val = unofficial_getDBResult($result, $i, 0);
				for($j = 0; $j < $checked_count; $j++)
				{
					if ($val == $checked_array[$j])
					{
						$return .= ' SELECTED';
					}
				}
				$return .= '>'.$val.'-'. substr(unofficial_getDBResult($result, $i, 1), 0, 35). '</OPTION>';
			}
		}
		$return .= '
			</select>';
		return $return;
	}
	 
	/**
	* html_build_checkbox() - Render checkbox control
	*
	* @param name - name of control
	* @param value - value of control
	* @param checked - true if control should be checked
	* @return html code for checkbox control
	*/
	function html_build_checkbox($name, $value, $checked)
	{
		return '<input type="checkbox" name="'.$name.'"' .' value="'.$value.'"' .($checked ? 'checked' : '').'>';
	}
	 
	/**
	* get_priority_color() - Wrapper for html_get_priority_color().
	*
	* @see html_get_priority_color()
	*/
	function get_priority_color($index)
	{
		return html_get_priority_color($index);
	}
	 
	/**
	* html_get_priority_color() - Return the color value for the index that was passed in
	*(defined in $sys_urlroot/themes/<selected theme>/theme.php)
	*
	* @param  int  Index
	*/
	function html_get_priority_color($index)
	{
		global $bgpri;
		 
		/* make sure that index is of appropriate type and range */
		$index = (int)$index;
		if ($index < 1)
		{
			$index = 1;
		}
		else if($index > 9)
		{
			$index = 9;
		}
		return $bgpri[$index];
	}
	 
	/**
	* build_priority_select_box() - Wrapper for html_build_priority_select_box()
	*
	* @see html_build_priority_select_box()
	*/
	function build_priority_select_box($name = 'priority', $checked_val = '5', $nochange = false)
	{
		return html_build_priority_select_box($name, $checked_val, $nochange);
	}
	 
	/**
	* html_build_priority_select_box() - Return a select box of standard priorities.
	* The name of this select box is optional and so is the default checked value.
	*
	* @param  string Name of the select box
	* @param  string The value to be checked
	* @param  bool Whether to make 'No Change' selected.
	*/
	function html_build_priority_select_box($name = 'priority', $checked_val = '5', $nochange = false)
	{
		 
		$content = '<select name="'.$name.'">';
		if ($nochange)
		{
			$content .= '<option value="100"';
			if ($nochange)
			{
				$content .= ' SELECTED>No Change</OPTION>';
			}
			else
			{
				$content .= '>No Change</OPTION>';
			}
		}
		$content .= '<option value="1"';
		if ($checked_val == "1")
		{
			$content .= ' SELECTED';
		}
		 $content .= '>1 - '._XF_HTM_LOWEST.'</OPTION>';
		$content .= '<option value="2"';
		if ($checked_val == "2")
		{
			$content .= ' SELECTED';
		}
		 $content .= '>2</OPTION>';
		$content .= '<option value="3"';
		if ($checked_val == "3")
		{
			$content .= ' SELECTED';
		}
		 $content .= '>3</OPTION>';
		$content .= '<option value="4"';
		if ($checked_val == "4")
		{
			$content .= ' SELECTED';
		}
		 $content .= '>4</OPTION>';
		$content .= '<option value="5"';
		if ($checked_val == "5")
		{
			$content .= ' SELECTED';
		}
		 $content .= '>5 - '._XF_HTM_MEDIUM.'</OPTION>';
		$content .= '<option value="6"';
		if ($checked_val == "6")
		{
			$content .= ' SELECTED';
		}
		 $content .= '>6</OPTION>';
		$content .= '<option value="7"';
		if ($checked_val == "7")
		{
			$content .= ' SELECTED';
		}
		 $content .= '>7</OPTION>';
		$content .= '<option value="8"';
		if ($checked_val == "8")
		{
			$content .= ' SELECTED';
		}
		 $content .= '>8</OPTION>';
		$content .= '<option value="9"';
		if ($checked_val == "9")
		{
			$content .= ' SELECTED';
		}
		 $content .= '>9 - '._XF_HTM_HIGHEST.'</OPTION>';
		$content .= '</select>';
		 
		return $content;
		 
	}
	 
	/**
	* html_buildcheckboxarray() - Build an HTML checkbox array.
	*
	* @param  array Options array
	* @param  name Checkbox name
	* @param  array Array of boxes to be pre-checked
	*/
	function html_buildcheckboxarray($options, $name, $checked_array)
	{
		$option_count = count($options);
		$checked_count = count($checked_array);
		 
		for($i = 1; $i <= $option_count; $i++)
		{
			$content = '
				<BR><input type="checkbox" name="'.$name.'" value="'.$i.'"';
			for($j = 0; $j < $checked_count; $j++)
			{
				if ($i == $checked_array[$j])
				{
					$content .= ' CHECKED';
				}
			}
			$content .= '> '.$options[$i];
		}
		return $content;
	}
?>