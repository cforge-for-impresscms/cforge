<?php
if (!eregi("admin.php", $_SERVER['PHP_SELF'])) { die ("Access Denied"); }

include_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
include_once("admin/admin_utils.php");
include_once("admin/groups/groups.php");

$op = util_http_track_vars('op');

switch($op) {
		case "GroupList":
			$search = http_get('search');
			$substr = util_http_track_vars('substr');
			$status = http_get('status');
			$is_public = http_get('is_public');
			GroupList($search, $substr, $status, $is_public);
			break;

		case "GroupApprove":
			$action = util_http_track_vars('action');
			$list_of_groups = util_http_track_vars('list_of_groups');
			$response_text = util_http_track_vars('response_text');
			$response_title = util_http_track_vars('response_title');
			$add_to_can = util_http_track_vars('add_to_can');
			$response_id = util_http_track_vars('response_id');
			GroupApprove($action, $list_of_groups, $response_text, $response_title, $add_to_can, $response_id);
			break;

		case "GroupEdit":
			$group_id = util_http_track_vars('group_id');
			$submit = util_http_track_vars('submit');
			$resend = util_http_track_vars('resend');
			$form_public = util_http_track_vars('form_public');
			$form_status = util_http_track_vars('form_status');
			$form_license = util_http_track_vars('form_license');
			$group_type = util_http_track_vars('group_type');
			$form_domain = util_http_track_vars('form_domain');
			GroupEdit($group_id, $submit, $resend, $form_public, $form_status, $form_license, $group_type, $form_domain);
			break;

		case "ListUsers":
			$group_id = util_http_track_vars('group_id');
			$action = util_http_track_vars('action');
			$user_id = util_http_track_vars('user_id');
			ListUsers($group_id, $action, $user_id);
			break;

		case "ManageResponses":
			$action = util_http_track_vars('action');
			$action2 = util_http_track_vars('action2');
			$sure = util_http_track_vars('sure');
			$response_title = util_http_track_vars('response_title');
			$response_text = util_http_track_vars('response_text');
			$response_id = util_http_track_vars('response_id');
			ManageResponses($action, $action2, $sure, $response_title, $response_text, $response_id);
			break;

 		default:
			GroupsMain();
			break;
}
?>