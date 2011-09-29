<?php
	 
	include_once("../../mainfile.php");
	require_once(ICMS_ROOT_PATH."/modules/xfmod/include/pre.php");
	$unixname = strtolower($QUERY_STRING);
	$query = "SELECT unix_group_name,group_name,short_description,register_time FROM ".$icmsDB->prefix("xf_groups")." WHERE is_public=1 AND status='A' AND unix_group_name='".$unixname."'";
	$result = $icmsDB->query($query);
	if ($result && $icmsDB->getRowsNum($result) > 0)
	{
		$row = $icmsDB->fetchArray($result);
	?>
		<html>	
			<head>
				<title><?php echo $row['group_name']; ?></title>
				<meta name="Description" content="<?php echo $row['short_description']; ?>">
				<meta name="Keywords" content="<?php echo $row['unix_group_name']; ?>">
			</head>
			<body><?php
				echo $row['unix_group_name']."<br/>";
				echo $row['group_name']."<br/>";
				echo $row['short_description']."<br/>";
			?></body>
		</html>
	<?php
	}
	exit;
?>