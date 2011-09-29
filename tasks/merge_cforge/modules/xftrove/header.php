<?php
	// ------------------------------------------------------------------------- //
	//                XOOPS - PHP Content Management System                      //
	//                       <http://www.xoops.org/>                             //
	// ------------------------------------------------------------------------- //
	// Based on:             //
	// myPHPNUKE Web Portal System - http://myphpnuke.com/          //
	// PHP-NUKE Web Portal System - http://phpnuke.org/          //
	// Thatware - http://thatware.org/          //
	// ------------------------------------------------------------------------- //
	//  This program is free software; you can redistribute it and/or modify     //
	//  it under the terms of the GNU General Public License as published by     //
	//  the Free Software Foundation; either version 2 of the License, or        //
	//  (at your option) any later version.                                      //
	//                                                                           //
	//  This program is distributed in the hope that it will be useful,          //
	//  but WITHOUT ANY WARRANTY; without even the implied warranty of           //
	//  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the            //
	//  GNU General Public License for more details.                             //
	//                                                                           //
	//  You should have received a copy of the GNU General Public License        //
	//  along with this program; if not, write to the Free Software              //
	//  Foundation, Inc., 59 Temple Place, Suite 330, Boston, MA  02111-1307 USA //
	// ------------------------------------------------------------------------- //
	include("../../mainfile.php");
	 
	 
	global $icmsConfig, $icmsModule;
	 
	 
	if (file_exists($icmsConfig['root_path']."modules/xfmod/language/".$icmsConfig['language']."/main.php") )
	{
		include($icmsConfig['root_path']."modules/xfmod/language/".$icmsConfig['language']."/main.php");
	}
	else
	{
		include($icmsConfig['root_path']."modules/xfmod/language/english/main.php");
	}
?>