<?php
	$tag = "";
	$item = array();
	 
	function openTag($parser, $name, $attrs)
	{
		global $tag;
		$tag = $name;
	}
	 
	function closeTag($parser, $name)
	{
		if ($name == "ITEM")
		{
			displayItem();
		}
	}
	 
	function cdata($parser, $cdata)
	{
		global $tag, $item;
		$item[$tag] = $cdata;
	}
	 
	function displayItem()
	{
		global $item;
		 
		echo "<p><a href='".$item['URL']."'>".$item['TITLE']."</a> ".$item['RELEVANCE']."%";
		echo "<br/>".$item['DESCRIPTION']."</p>";
		$item = array();
	}
	 
	function parseXML($file)
	{
		 
		$xml_parser = xml_parser_create();
		xml_set_element_handler($xml_parser, "openTag", "closeTag");
		xml_set_character_data_handler($xml_parser, "cdata");
		 
		if (!($fp = fopen($file, "r")))
		{
			die("could not open XML input");
		}
		 
		while ($data = fread($fp, 4096))
		{
			$data = eregi_replace(">"."[[:space:]]+"."<", "><", $data);//strip white space
			if (!xml_parse($xml_parser, $data, feof($fp)))
			{
				die(sprintf("XML error: %s at line %d",
					xml_error_string(xml_get_error_code($xml_parser)),
					xml_get_current_line_number($xml_parser)));
			}
		}
		xml_parser_free($xml_parser);
	}
	 
?>