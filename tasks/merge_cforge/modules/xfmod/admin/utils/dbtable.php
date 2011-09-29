<?php
	/**
	*
	* Module to render generic HTML tables for Site Admin
	*
	* SourceForge: Breaking Down the Barriers to Open Source Development
	* Copyright 1999-2001(c) VA Linux Systems
	* http://sourceforge.net
	*
	* @version   $Id: dbtable.php,v 1.2 2003/12/09 15:03:38 devsupaul Exp $
	*
	*/
	if (!eregi("admin.php", $_SERVER['PHP_SELF']))
	{
		die("Access Denied");
	}
	if ($icmsUser->isAdmin($icmsModule->mid()))
	{
		 
		/**
		* admin_table_add() - present a form for adding a record to the specified table
		*
		* @param $table - the table to act on
		* @param $unit - the name of the "units" described by the table's records
		* @param $primary_key - the primary key of the table
		*/
		function admin_table_add($table, $unit, $primary_key, $baseurl)
		{
			global $icmsDB;
			 
			// This query may return no rows, but the field names are needed.
			$result = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix($table)." WHERE ".$primary_key."=0");
			 
			if ($result)
			{
				$cols = unofficial_getNumFields($result);
				 
				echo "Create a new ".$unit." below:" ."<form name='add' ACTION='".$baseurl."&func=postadd' METHOD='POST'>" ."<table>";
				 
				for($i = 0; $i < $cols; $i++)
				{
					$fieldname = unofficial_getFieldName($result, $i);
					 
					echo "<tr><td><strong>".$fieldname."</strong></td>" ."<td><input type='text' name='".$fieldname."' value=''></td></tr>";
				}
				echo "</table>" ."<input type='submit' value='Submit New ".ucwords($unit)."'></form>" .myTextForm($baseurl, "Cancel");
				 
			}
			else
			{
				echo $icmsDB->error();
			}
		}
		 
		/**
		* admin_table_postadd() - update the database based on a submitted change
		*
		* @param $table - the table to act on
		* @param $unit - the name of the "units" described by the table's records
		* @param $primary_key - the primary key of the table
		*/
		function admin_table_postadd($table, $unit, $primary_key, $baseurl)
		{
			global $HTTP_POST_VARS, $icmsDB;
			$ts = MyTextSanitizer::getInstance();
			 
			$sql = "INSERT INTO ".$icmsDB->prefix($table)."(" . join(',', array_keys($HTTP_POST_VARS))
			. ") VALUES('" . $ts->makeTboxData4Save(join("','", array_values($HTTP_POST_VARS)))
			. "')";
			 
			if ($icmsDB->queryF($sql))
			{
				echo ucfirst($unit).' successfully added.';
			}
			else
			{
				echo $icmsDB->error();
			}
		}
		 
		/**
		* admin_table_confirmdelete() - present a form to confirm requested record deletion
		*
		* @param $table - the table to act on
		* @param $unit - the name of the "units" described by the table's records
		* @param $primary_key - the primary key of the table
		* @param $id - the id of the record to act on
		*/
		function admin_table_confirmdelete($table, $unit, $primary_key, $id, $baseurl)
		{
			global $icmsDB;
			 
			$result = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix($table)." WHERE $primary_key=$id");
			 
			if ($result)
			{
				$cols = unofficial_getNumFields($result);
				 
				echo "Are you sure you want to delete this ".$unit."?" ."<UL>";
				 
				for($i = 0; $i < $cols; $i++)
				{
					echo "<LI><strong>".unofficial_getFieldName($result, $i)."</strong> ".unofficial_getDBResult($result, 0, $i)."</LI>";
				}
				echo "</UL>" .myTextForm($baseurl."&func=delete&id=".$id, "Delete")
				.myTextForm($baseurl, "Cancel");
				 
			}
			else
			{
				echo $icmsDB->error();
			}
		}
		 
		/**
		* admin_table_delete() - delete a record from the database after confirmation
		*
		* @param $table - the table to act on
		* @param $unit - the name of the "units" described by the table's records
		* @param $primary_key - the primary key of the table
		* @param $id - the id of the record to act on
		*/
		function admin_table_delete($table, $unit, $primary_key, $id, $baseurl)
		{
			global $icmsDB;
			 
			if ($icmsDB->queryF("DELETE FROM ".$icmsDB->prefix($table)." WHERE $primary_key=$id"))
			{
				echo ucfirst($unit).' successfully deleted.';
			}
			else
			{
				echo $icmsDB->error();
			}
		}
		 
		/**
		* admin_table_edit() - present a form for editing a record in the specified table
		*
		* @param $table - the table to act on
		* @param $unit - the name of the "units" described by the table's records
		* @param $primary_key - the primary key of the table
		* @param $id - the id of the record to act on
		*/
		function admin_table_edit($table, $unit, $primary_key, $id, $baseurl)
		{
			global $icmsDB;
			 
			$result = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix($table)." WHERE $primary_key=$id");
			 
			if ($result)
			{
				$cols = unofficial_getNumFields($result);
				 
				echo "Modify the ".$unit." below:" ."<form name='edit' ACTION='".$baseurl."&func=postedit&id=".$id."' METHOD='POST'>" ."<table>";
				 
				for($i = 0; $i < $cols; $i++)
				{
					$fieldname = unofficial_getFieldName($result, $i);
					$value = unofficial_getDBResult($result, 0, $i);
					 
					echo "<tr><td><strong>".$fieldname."</strong></td>";
					 
					if ($fieldname == $primary_key)
					{
						echo "<td>".$value."</td></tr>";
					}
					else
					{
						echo "<td><input type='text' name='".$fieldname."' value='".$value."'></td></tr>";
					}
				}
				echo "</table><input type='submit' value='Submit Changes'></form>" .myTextForm($baseurl, "Cancel");
				 
			}
			else
			{
				echo $icmsDB->error();
			}
		}
		 
		/**
		* admin_table_postedit() - update the database to reflect submitted modifications to a record
		*
		* @param $table - the table to act on
		* @param $unit - the name of the "units" described by the table's records
		* @param $primary_key - the primary key of the table
		* @param $id - the id of the record to act on
		*/
		function admin_table_postedit($table, $unit, $primary_key, $id, $baseurl)
		{
			global $HTTP_POST_VARS, $icmsDB;
			$ts = MyTextSanitizer::getInstance();
			 
			$sql = "UPDATE ".$icmsDB->prefix($table)." SET ";
			while (list($var, $val) = each($HTTP_POST_VARS))
			{
				if ($var != $primary_key)
				{
					$sql .= "$var='". $ts->makeTboxData4Save($val)."', ";
				}
			}
			$sql = ereg_replace(', $', ' ', $sql);
			$sql .= "WHERE $primary_key=$id";
			 
			if ($icmsDB->queryF($sql))
			{
				echo ucfirst($unit) . ' successfully modified.';
			}
			else
			{
				echo $icmsDB->error();
			}
		}
		 
		/**
		* admin_table_show() - display the specified table, sorted by the primary key, with links to add, edit, and delete
		*
		* @param $table - the table to act on
		* @param $unit - the name of the "units" described by the table's records
		* @param $primary_key - the primary key of the table
		*/
		function admin_table_show($table, $unit, $primary_key, $baseurl)
		{
			global $icmsDB;
			 
			$result = $icmsDB->query("SELECT * FROM ".$icmsDB->prefix($table)." ORDER BY $primary_key");
			 
			if ($result)
			{
				$rows = $icmsDB->getRowsNum($result);
				$cols = unofficial_getNumFields($result);
				 
				echo "<table border='0' width='100%'>" ."<tr>" ."<td colspan='".($cols + 1)."'><strong><FONT>". ucwords($unit)."s</FONT></strong>" ."[ <a href='".$baseurl."&func=add'>Add New</a> ]</td></tr>" ."<tr><td width='15%'></td>";
				 
				for($i = 0; $i < $cols; $i++)
				{
					echo "<td><strong>".unofficial_getFieldName($result, $i)."</strong></td>";
				}
				echo "</tr>";
				 
				for($j = 0; $j < $rows; $j++)
				{
					echo "<th class='".($j%2 != 0?"bg2":"bg3")."'>";
					 
					$id = unofficial_getDBResult($result, $j, 0);
					echo "<td>[ <a href='".$baseurl."&func=edit&id=".$id."'>edit</a> ] " ."[ <a href='".$baseurl."&func=confirmdelete&id=".$id."'>delete</a> ] </td>";
					 
					for($i = 0; $i < $cols; $i++)
					{
						echo "<td>". unofficial_getDBResult($result, $j, $i)."</td>";
					}
					echo "</tr>";
				}
				echo "</table>";
				 
			}
			else
			{
				echo $icmsDB->error();
			}
		}
		 
		 
	}
	else
	{
		echo "Access Denied";
	}
	 
?>