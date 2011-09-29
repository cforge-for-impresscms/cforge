<?php
	/**
	*
	* utils.php - Misc utils common to all aspects of the site
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: utils.php,v 1.10 2004/01/14 19:29:49 devsupaul Exp $
	*
	*/
	//require_once(ICMS_ROOT_PATH."/modules/xfmod/include/phpmailer/class.phpmailer.php");
	 
	function util_unconvert_htmlspecialchars($string)
	{
		if (strlen($string) < 1)
		{
			return '';
		}
		else
		{
			$trans = get_html_translation_table(HTMLENTITIES, ENT_QUOTES);
			$trans = array_flip($trans);
			$str = strtr($string, $trans);
			return $str;
		}
	}
	/**
	* util_result_column_to_array() - Takes a result set and turns the optional column into an array
	*
	* @param  int  The result set ID
	* @param  int  The column
	* @resturns An array
	*
	*/
	function util_result_column_to_array($result, $col = 0)
	{
		global $icmsDB;
		/*
		Takes a result set and turns the optional column into
		an array
		*/
		$rows = $icmsDB->getRowsNum($result);
		 
		if ($rows > 0)
		{
			$arr = array();
			 
			for($i = 0; $i < $rows; $i++)
			{
				$arr[$i] = unofficial_getDBResult($result, $i, $col);
			}
		}
		else
		{
			$arr = array();
		}
		 
		return $arr;
	}
	/**
	* util_result_columns_to_assoc() - Takes a result set and turns the column pair into an associative array
	*
	* @param  string The result set ID
	* @param  int  The column key
	* @param  int  The optional column value
	* @returns An associative array
	*
	*/
	function util_result_columns_to_assoc($result, $col_key = 0, $col_val = 1)
	{
		global $icmsDB;
		$rows = $icmsDB->getRowsNum($result);
		 
		if ($rows > 0)
		{
			$arr = array();
			for($i = 0; $i < $rows; $i++)
			{
				$arr[unofficial_getDBResult($result, $i, $col_key)] = unofficial_getDBResult($result, $i, $col_val);
			}
		}
		else
		{
			$arr = array();
		}
		return $arr;
	}
	 
	/**
	* show_priority_colors_key() - Show the priority colors legend
	*
	*/
	function get_priority_colors_key()
	{
		 
		$content = "<p><strong>"._XF_PRIORITYCOLORS.":</strong><BR>" ."<table border='0'><th>";
		 
		for($i = 1; $i < 10; $i++)
		{
			$content .= "<td BGCOLOR='".get_priority_color($i)."'>".$i."</td>";
		}
		$content .= "</tr></table>";
		return $content;
	}
	 
	function show_priority_colors_key()
	{
		return get_priority_colors_key();
	}
	 
	/*
	* Validates an email adress
	*(Code taken from newsportal)
	*
	* $address: a string containing the email-address to be validated
	*
	* returns true if the address passes the tests, false otherwise.
	*/
	function validate_email($address)
	{
		global $icmsForge;
		if (!isset($icmsForge['validate_email']))
		$icmsForge['validate_email'] = 1;
		$return = true;
		if (($icmsForge['validate_email'] >= 1) && ($return == true))
		$return = (ereg('^[-!#$%&\'*+\\./0-9=?A-Z^_A-z{|}~]+'.'@'. '[-!#$%&\'*+\\/0-9=?A-Z^_A-z{|}~]+\.'. '[-!#$%&\'*+\\./0-9=?A-Z^_A-z{|}~]+$', $address));
		if (($icmsForge['validate_email'] >= 2) && ($return == true))
		{
			$addressarray = address_decode($address, "garantiertungueltig");
			$return = checkdnsrr($addressarray[0]["host"], "MX");
			if (!$return) $return = checkdnsrr($addressarray[0]["host"], "A");
		}
		return($return);
	}
	 
	/*
	* Split an internet-address string into its parts. An address string could
	* be for example:
	* - user@host.domain(Realname)
	* - "Realname" <user@host.domain>
	* - user@host.domain
	*
	* The address will be split into user, host(incl. domain) and realname
	*
	* $adrstring: The string containing the address in internet format
	* $defaulthost: The name of the host which should be returned if the
	*               address-string doesn't contain a hostname.
	*
	* returns an hash containing the fields "mailbox", "host" and "personal"
	*/
	function address_decode($adrstring, $defaulthost)
	{
		$parsestring = trim($adrstring);
		$len = strlen($parsestring);
		$at_pos = strpos($parsestring, '@');
		// find @
		$ka_pos = strpos($parsestring, "(");
		// find(
		$kz_pos = strpos($parsestring, ')');
		// find)
		$ha_pos = strpos($parsestring, '<');
		// find <
		$hz_pos = strpos($parsestring, '>');
		// find >
		$space_pos = strpos($parsestring, ')');
		// find ' '
		$email = "";
		$mailbox = "";
		$host = "";
		$personal = "";
		if ($space_pos != false)
		{
			if (($ka_pos != false) && ($kz_pos != false))
			{
				$personal = substr($parsestring, $ka_pos+1, $kz_pos-$ka_pos-1);
				$email = trim(substr($parsestring, 0, $ka_pos-1));
			}
		}
		else
		{
			$email = $adrstring;
		}
		if (($ha_pos != false) && ($hz_pos != false))
		{
			$email = trim(substr($parsestring, $ha_pos+1, $hz_pos-$ha_pos-1));
			$personal = substr($parsestring, 0, $ha_pos-1);
		}
		if ($at_pos != false)
		{
			$mailbox = substr($email, 0, strpos($email, '@'));
			$host = substr($email, strpos($email, '@')+1);
		}
		else
		{
			$mailbox = $email;
			$host = $defaulthost;
		}
		$personal = trim($personal);
		if (substr($personal, 0, 1) == '"') $personal = substr($personal, 1);
		if (substr($personal, strlen($personal)-1, 1) == '"')
		$personal = substr($personal, 0, strlen($personal)-1);
		$result["mailbox"] = trim($mailbox);
		$result["host"] = trim($host);
		if ($personal != "") $result["personal"] = $personal;
		$complete[] = $result;
		return($complete);
	}
	 
	 
	function checkXFAdminPermissions($error_on_failure = 'Access Denied')
	{
		global $icmsConfig, $icmsUser;
		 
		include_once($icmsConfig['root_path']."class/icmsModule.php");
		include_once($icmsConfig['root_path']."kernel/group.php");
		$icmsModule = icmsModule::getByDirname("xfmod");
		if (!$icmsModule)
		{
			redirect_header($icmsConfig['xoops_url']."/", 2, "SOME SERIOUS ERROR OCCURED!! Where is the 'xfmod' module?!?!?!");
			exit();
		}
		if ($icmsUser)
		{
			if (!IcmsGroup::hasAccessRight($icmsModule->mid(), $icmsUser->groups()))
			{
				redirect_header($icmsConfig['xoops_url']."/", 2, $error_on_failure);
				exit();
			}
		}
		else
		{
			if (!IcmsGroup::hasAccessRight($icmsModule->mid(), 0))
			{
				redirect_header($icmsConfig['xoops_url']."/", 2, $error_on_failure);
				exit();
			}
		}
	}
	 
	function xoopsForgeMail($from, $fromname, $subject, $body, $to_arr, $bcc_arr = false, $cc_arr = false)
	{
		$icmsMailer = $icmsMailer = getMailer();
		$icmsMailer->setToEmails($to_arr);
		if ($bcc_arr) $icmsMailer->setToEmails($bcc_arr);
		if ($cc_arr) $icmsMailer->setToEmails($cc_arr);
		$icmsMailer->setFromName($fromname);
		$icmsMailer->setFromEmail($from);
		$icmsMailer->setSubject($subject);
		$icmsMailer->setBody($body);
		$icmsMailer->useMail();
		return $icmsMailer->send();
	}
	 
	function ShowResultSet($result, $title = "Untitled", $linkify = false)
	{
		global $group_id, $icmsDB;
		 
		if ($result)
		{
			$rows = $icmsDB->getRowsNum($result);
			$cols = unofficial_getNumFields($result);
			 
			$content = "<table border='0' width='100%'>" ."<tr class='bg2'><td colspan='".$cols."'><B style='font-size:14px;text-align:left;'>".$title."</strong></td></tr>";
			 
			/*  Create  the  headers  */
			$content .= "<tr class='bg2'>";
			for($i = 0; $i < $cols; $i++)
			{
				 
				$content .= "<td><strong>".unofficial_getFieldName($result, $i)."</strong></td>";
			}
			$content .= "</tr>";
			 
			/*  Create the rows  */
			for($j = 0; $j < $rows; $j++)
			{
				$content .= "<th class='".($j%2 > 0?"bg1":"bg3")."'>";
				for($i = 0; $i < $cols; $i++)
				{
					if ($linkify && $i == 0)
					{
						$link = '<a href="'.$_SERVER['PHP_SELF'].'?';
						$linkend = '</a>';
						if ($linkify == "bug_cat")
						{
							$link .= 'group_id='.$group_id.'&bug_cat_mod=y&bug_cat_id='.unofficial_getDBResult($result, $j, 'bug_category_id').'">';
						}
						else if($linkify == "bug_group")
						{
							$link .= 'group_id='.$group_id.'&bug_group_mod=y&bug_group_id='.unofficial_getDBResult($result, $j, 'bug_group_id').'">';
						}
						else if($linkify == "patch_cat")
						{
							$link .= 'group_id='.$group_id.'&patch_cat_mod=y&patch_cat_id='.unofficial_getDBResult($result, $j, 'patch_category_id').'">';
						}
						else if($linkify == "support_cat")
						{
							$link .= 'group_id='.$group_id.'&support_cat_mod=y&support_cat_id='.unofficial_getDBResult($result, $j, 'support_category_id').'">';
						}
						else if($linkify == "pm_project")
						{
							$link .= 'group_id='.$group_id.'&project_cat_mod=y&project_cat_id='.unofficial_getDBResult($result, $j, 'group_project_id').'">';
						}
						else
						{
							$link = $linkend = '';
						}
					}
					else
					{
						$link = $linkend = '';
					}
					$content .= '<td>'.$link . unofficial_getDBResult($result, $j, $i) . $linkend.'</td>';
				}
				$content .= '</tr>';
			}
			$content .= '</table>';
		}
		else
		{
			$content .= $icmsDB->error();
		}
		return $content;
	}
	 
	/**
	* util_check_fileupload() - determines if a filename is appropriate for upload
	*
	* @param       string  The name of the file being uploaded
	*/
	function util_check_fileupload($filename)
	{
		 
		/* Empty file is a valid file.
		This is because this function should be called
		unconditionally at the top of submit action processing
		and many forms have optional file upload. */
		if ($filename == 'none' || $filename == '')
		{
			return "OK";
		}
		 
		/* This should be enough... */
		if (!is_uploaded_file($filename))
		{
			return "!is_uploaded_file";
		}
		/* ... but we'd rather be paranoic */
		if (strstr($filename, '..'))
		{
			return "strstr(filename, '..')";
		}
		if (!is_file($filename))
		{
			return "!is_file";
		}
		if (!file_exists($filename))
		{
			return "!file_exists";
		}
		return "OK";
	}
	 
	/**
	* GraphResult() - Takes a database result set and builds a graph.
	* The first column should be the name, and the second column should be the values
	* Be sure to include HTL_Graphs.php before using this function
	*
	* @author Tim Perdue tperdue@valinux.com
	* @param  int  The databse result set ID
	* @param  string The title of the graph
	*
	*/
	function GraphResult($result, $title)
	{
		global $icmsDB;
		 
		$rows = $icmsDB->getRowsNum($result);
		 
		if ((!$result) || ($rows < 1))
		{
			echo 'None Found.';
		}
		else
		{
			$names = array();
			$values = array();
			 
			for($j = 0; $j < $icmsDB->getRowsNum($result); $j++)
			{
				if (unofficial_getDBResult($result, $j, 0) != '' && unofficial_getDBResult($result, $j, 1) != '')
				{
					$names[$j] = unofficial_getDBResult($result, $j, 0);
					$values[$j] = unofficial_getDBResult($result, $j, 1);
				}
			}
			 
			/*
			This is another function detailed below
			*/
			GraphIt($names, $values, $title);
		}
	}
	 
	/**
	* GraphIt() - Build a graph
	*
	* @author Tim Perdue tperdue@valinux.com
	* @param  array An array of names
	* @param  array An array of values
	* @param  string The title of the graph
	*
	*/
	function GraphIt($name_string, $value_string, $title)
	{
		global $bgpri;
		 
		$counter = count($name_string);
		 
		/*
		Can choose any color you wish
		*/
		$bars = array();
		 
		for($i = 0; $i < $counter; $i++)
		{
			$bars[$i] = $bgpri[5];
		}
		 
		$counter = count($value_string);
		 
		/*
		Figure the max_value passed in, so scale can be determined
		*/
		 
		$max_value = 0;
		 
		for($i = 0; $i < $counter; $i++)
		{
			if ($value_string[$i] > $max_value)
			{
				$max_value = $value_string[$i];
			}
		}
		 
		if ($max_value < 1)
		{
			$max_value = 1;
		}
		 
		/*
		I want my graphs all to be 800 pixels wide, so that is my divisor
		*/
		 
		$scale = (400/$max_value);
		 
		/*
		I create a wrapper table around the graph that holds the title
		*/
		 
		echo "<table border='0'>" ."<tr class='bg2'>" ."<td><strong>$title</strong></td>" ."</tr>";
		 
		echo '<th><td>';
		/*
		Create an associate array to pass in. I leave most of it blank
		*/
		 
		$vals = array(
		'vlabel' => '',
			'hlabel' => '',
			'type' => '',
			'cellpadding' => '',
			'cellspacing' => '0',
			'border' => '',
			'width' => '',
			'background' => '',
			'vfcolor' => '',
			'hfcolor' => '',
			'vbgcolor' => '',
			'hbgcolor' => '',
			'vfstyle' => '',
			'hfstyle' => '',
			'noshowvals' => '',
			'scale' => $scale,
			'namebgcolor' => '',
			'valuebgcolor' => '',
			'namefcolor' => '',
			'valuefcolor' => '',
			'namefstyle' => '',
			'valuefstyle' => '',
			'doublefcolor' => '');
		 
		/*
		This is the actual call to the HTML_Graphs class
		*/
		 
		html_graph($name_string, $value_string, $bars, $vals);
		 
		echo '
			</td></th></table>
			<!-- end outer graph table -->';
	}
	 
	//A return value of false means there were no problems reported
	function VirusScan($filename)
	{
		global $icmsForge;
		if ($icmsForge['virusscan'] != 1)
		{
			return false;
		}
		$results = shell_exec(ICMS_ROOT_PATH."/modules/xfmod/bin/cscmdline -c ".ICMS_ROOT_PATH."/modules/xfmod/bin -s prv-teamsite1.provo.novell.com -v ".$filename);
		 
		preg_match_all("/\w+:\s+(\d)/", $results, $matches);
		// $matches[1][1] - the number of files scanned
		// $matches[1][2] - the number of infected files
		// $matches[1][3] - the number of repaired files
		// $matches[1][4] - the number of errors reported
		 
		if ($matches[1][4] > 0)
		{
			return _XF_FRS_VIRUSSCANFAILED;
		}
		if ($matches[1][1] < 1)
		{
			return _XF_FRS_VIRUSSCANFAILEDNOFILE;
		}
		if ($matches[1][2] > 0)
		{
			return _XF_FRS_VIRUSFOUND;
		}
		return false;
	}
	 
	function FileExtFilter($filename)
	{
		 
		return false;
	}
	 
	/*
	@return value : $_POST[$key] , _GET[$key] or null
	*/
	function util_http_track_vars($key, $default = 'null')
	{
		if (isset($_POST[$key]))
		$value = $_POST[$key];
		elseif(isset($_GET[$key]))
		$value = $_GET[$key];
		else
			$value = $default;
		 
		return $value;
	}
	 
	function http_get($key, $default = NULL)
	{
		$value = (isset($_GET[$key])) ? $_GET[$key] :
		 $default;
		return $value;
	}
	 
	function http_post($key, $default = NULL)
	{
		$value = (isset($_POST[$key])) ? $_POST[$key] :
		 $default;
		return $value;
	}
	 
?>