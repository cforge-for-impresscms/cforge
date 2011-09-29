<?php
if (!eregi("admin.php", $_SERVER['PHP_SELF'])) { die ("Access Denied"); }

include_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
include_once("admin/admin_utils.php");
include_once("admin/utils/dbtable.php");
include_once("admin/utils/utils.php");

$op = util_http_track_vars('op');

$pattern = util_http_track_vars('pattern');
$submit = util_http_track_vars('submit');
$uid = util_http_track_vars('uid');
$uname = util_http_track_vars('uname');
$ok = util_http_track_vars('ok');

$unit = util_http_track_vars('unit');
$table = util_http_track_vars('table');
$pk = util_http_track_vars('pk');
$func = util_http_track_vars('func');
$id = util_http_track_vars('id');

switch($op) {
		case "SiteMailings":
			SiteMailings($pattern, $submit, $uid, $uname, $ok);
			break;

		case "EditTable":
			EditTable($unit, $table, $pk, $func, $id);
			break;

 		default:
			UtilsMain();
			break;
}
?>