<?php
	/**
	* $Id: errors.php,v 1.4 2004/03/01 23:32:36 danreese Exp $
	* (c) 2004 Novell, Inc.
	*
	* Displays the full list of error codes and descriptions.
	*/
	header('Content-type: text/xml');
	 
	// Possible error codes are 001-999.
	$contents = '';
	$tpl = TEMPLATE_CONTEXT.'error.tpl';
	for ($id = 1; $id <= 999; $id++)
	{
		$c = '_XFWEBSERVICE_ERROR_'.str_pad($id, 3, '0', STR_PAD_LEFT);
		if (defined($c))
			{
			$args['id'] = $id;
			$args['contents'] = constant($c);
			$contents = $contents.fill_template($tpl, $args);
		}
	}
	 
	$tpl = TEMPLATE_CONTEXT.'list.tpl';
	$args['contents'] = $contents;
	echo fill_template($tpl, $args);
?>