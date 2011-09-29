<?php
/**
 * MySQL database connection/querying layer
 *
 * SourceForge: Breaking Down the Barriers to Open Source Development
 * Copyright 1999-2001 (c) VA Linux Systems
 * http://sourceforge.net
 *
 * @version   $Id: database-mysql.php,v 1.2 2003/12/09 15:03:53 devsupaul Exp $
 */

function unofficial_getDBResult($qhandle, $row, $field)
{
	return @mysql_result($qhandle, $row, $field);
}

/**
 *  db_affected_rows() - Returns the number of rows changed in the last query
 *
 *  @param		string	Query result set handle
 */
function unofficial_getAffectedRows($qhandle)
{
	return @mysql_affected_rows();
}

function unofficial_ResetResult($qhandle,$row=0)
{
	return mysql_data_seek($qhandle,$row);
}

/**
 *  db_numfields() - Returns the number of fields in this result set
 *
 *  @param		string	Query result set handle
 */
function unofficial_getNumFields($lhandle)
{
	return @mysql_numfields($lhandle);
}

/**
 *  db_fieldname() - Returns the number of rows changed in the last query
 *
 *  @param		string	Query result set handle
 *  @param		int		Column number
 */
function unofficial_getFieldName($lhandle,$fnumber)
{
	   return @mysql_fieldname($lhandle,$fnumber);
}
?>