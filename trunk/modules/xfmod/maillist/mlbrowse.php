<?php 

include_once("../../../mainfile.php");
$langfile="maillist.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vote_function.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/vars.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/news/news_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/trove.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/maillist/maillist_utils.php");

include_once(XOOPS_ROOT_PATH."/class/httpClient.php");
include_once(XOOPS_ROOT_PATH."/class/checkurl.php");

$pathinfo = checkURL($_SERVER['PATH_INFO']);
if(strrpos($pathinfo,"logout")==strlen($pathinfo)-6){
	$logout=true;
}
$mmsvr = "";
if ( file_exists( "mlserver.php" ) )
{
	include_once("mlserver.php");
	$mmsvr = getSvrName();
}
else
{
	$mmsvr = $_SERVER['SERVER_NAME'];
}

$mm_path = $pathinfo;
$my_path = "/modules/xfmod/maillist/mlbrowse.php".$pathinfo;

$http = new Net_HTTP_Client();
if ( ! $http->Connect( $mmsvr ) )
{
    echo "GNU Mailman is not available.";
}


// On the request, we need a Cookie: header.
// We need to retrieve the cookie that was set
// by a previous mailman request, if any.
/*
$admpathlen = strlen(maillist_get_admin_path());
$end = strpos($pathinfo,'/',$admpathlen);
if ( ! $end )
{
	$end = strlen($pathinfo);
}
$list = substr($pathinfo,$admpathlen,$end-$admpathlen);
$default_cookie_name = $list.":admin";
$dfclen = strlen($default_cookie_name);
$cookie_value = $_COOKIE[$default_cookie_name];
//echo $default_cookie_name."=".$cookie_value."<br>";
if ( ! empty($cookie_value) )
{
//	$ret = $http->addCookie($default_cookie_name,$cookie_value);
}
//*/
// All cookies that come from the browser need to be set here.
// The httpclient will only set one so I smash them all together
// and make it think there is only one.
list($firstname,$value)=each($_COOKIE);
$bigcookie=$value;
while(list($name,$value)=each($_COOKIE)){
	$bigcookie.="; ".$name."=".$value;
}
$http->addCookie($firstname,$bigcookie);

// Handle any get parameters.
$getparams = "";
if ( !empty( $_GET ) )
{
	$first = true;
	foreach( $_GET as $argname => $argval )
	{
		if ( 0 == strcmp($argname, "forge_group_id") )
		{
			continue;
		}
		$getparams .= ($first?"?":"&");
		$getparams .= $argname."=".$argval;
		$first = false;
	}
}
$pathinfo .= $getparams;

// Perform the proxy request.
if ( !empty( $_POST ) )
{
	$status = $http->Post($pathinfo,$_POST);
}
else
{
	$status = $http->Get($pathinfo);
}

if ( $status != 200 )
{
	include ("../../../header.php");
	echo "GNU Mailman is not available.  (error is ".$http->getStatusMessage().")";
	$http->Disconnect();
	include("../../../footer.php");
	exit();
}
$buf = $http->getBody();
$hdrs = $http->getHeaders();
$http->Disconnect();
// Retrive and serialize the session cookie.
if ($logout){
	setcookie($default_cookie_name);//,"",1,"/modules/xfmod/maillist/","",1);
}else if ( isset($hdrs['Set-Cookie'])  ){
	$result = explode("; ",$hdrs['Set-Cookie']);
	foreach($result as $cookie){
		list($name,$value) = explode("=",$cookie);
		if($name!="Path" && $name!="Version")//ugly but functional
			setcookie($name,$value);
	}
}
$mm_bg_color="99CCFF";
$mm_bg_color_lc="99ccff";
$mm_bg2_color_lc="99cccc";
$mm_bg3_color_lc="cccccc";
$forge_bg_color="F0F0F0";
$buf = str_replace($mm_path,$my_path,$buf);

$main_subsections = array("general","members","privacy","nondigest","digest","bounce","archive","gateway","autoreply","logout");
$adm_subsections = array("admindb","listinfo","edithtml");
// This doesn't seem like a very efficient way to proxy this HTML code.
//$buf = str_replace($mm_path,$my_path,$buf);
// Handle relative paths.
// On some pages they retreat 1 level when we need them to retreat two.
// On others we need them to retreat 2.  The first replace will
// make them retreat three so we undo it.
/*
$buf = str_replace("\"../admin/","\"../../admin/",$buf);
$buf = str_replace("\"../../../admin/","\"../../admin/",$buf);
foreach($main_subsections as $ss)
{
	$buf = str_replace("/".$ss,"/".$ss."?forge_group_id=".$forge_group_id,$buf);
}
foreach($adm_subsections as $ss)
{
	$buf = str_replace("\"../$ss/","\"../../$ss/",$buf);
	$buf = str_replace("\"../../../$ss/","\"../../$ss/",$buf);
	$buf = str_replace("/".$ss."/".$list,"/".$ss."/".$list."?forge_group_id=".$forge_group_id,$buf);
}
//*/
$buf = str_replace("\"../../../","\"/modules/xfmod/maillist/mlbrowse.php/mailman/",$buf);
$buf = str_replace("\"../../","\"/modules/xfmod/maillist/mlbrowse.php/mailman/",$buf);
$buf = str_replace("\"../","\"/modules/xfmod/maillist/mlbrowse.php/mailman/",$buf);
$buf = str_replace("?VARHELP","?forge_group_id=".$forge_group_id."&VARHELP",$buf);
$buf = str_replace($mm_bg_color,$forge_bg_color,$buf);
$buf = str_replace($mm_bg_color_lc,$forge_bg_color,$buf);
$buf = str_replace($mm_bg2_color_lc,$forge_bg_color,$buf);
$buf = str_replace($mm_bg3_color_lc,$forge_bg_color,$buf);
include ("../../../header.php");
echo $buf;
include("../../../footer.php");
?>