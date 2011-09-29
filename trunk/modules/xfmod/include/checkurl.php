<?php

function checkURL($p_url)
{
	$ta = parse_url($p_url);
	if (!empty($ta[scheme]))
	{
		$ta[scheme].='://';
	}

	if (!empty($ta[pass]) and !empty($ta[user]))
	{
		$ta[user].=':';
		$ta[pass]=rawurlencode($ta[pass]).'@';
	}
	elseif (!empty($ta[user]))
	{
		$ta[user].='@';
	}

	if (!empty($ta[port]) and !empty($ta[host]))
	{
		$ta[host]=''.$ta[host].':';
	}
	elseif (!empty($ta[host]))
	{
		$ta[host]=$ta[host];
	}

	if (!empty($ta[path]))
	{
		$tu='';
		$tok=strtok($ta[path], "\\/");
		while (strlen($tok))
		{
			$tu.=rawurlencode($tok).'/';
			$tok=strtok("\\/");
		}
		$ta[path]='/'.trim($tu, '/');
	}

	if (!empty($ta[query]))
	{
		$ta[query]='?'.$ta[query];
	}

	if (!empty($ta[fragment]))
	{
		$ta[fragment]='#'.$ta[fragment];
	}

	return implode('', array($ta[scheme], $ta[user],
		$ta[pass], $ta[host], $ta[port], $ta[path],
		$ta[query], $ta[fragment]));
}



?>