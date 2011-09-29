<?php
include_once("../../mainfile.php");

$metaTitle=": Advanced Search";
include (XOOPS_ROOT_PATH."/header.php");

$pattern[0] = "/%3f/";
$pattern[1] = "/%3d/";
$pattern[2] = "/%26/";
$pattern[3] = "/\[ForgeDocumentation\]/";
$pattern[4] = "/\[ForgeFAQ\]/";
$pattern[5] = "/\[ForgeSampleCode\]/";
$pattern[6] = "/\[ForgeForumsNews\]/";
$pattern[7] = "/\[ndk_doc\]/";
$pattern[8] = "/\[dev_research\]/";
$pattern[9]= "/\[SampleCode\]/";
$pattern[10]= "/search\.novell\.com\/NSearch\/SearchServlet/";
$pattern[11]= "/\[DevForums\]/";
$pattern[12]= "/Novell Forge  (.*)Summary\[ForgeProjects\]/";
$pattern[13]= "/Novell Forge  (.*)Community\[ForgeProjects\]/";

$replace[0] = "?";
$replace[1] = "=";
$replace[2] = "&";
$replace[3] = "[Documentation]";
$replace[4] = "[FAQ]";
$replace[5] = "[Sample Code]";
$replace[6] = "[Forums/News]";
$replace[7] = "[Novell API Documentation]";
$replace[8] = "[AppNote]";
$replace[9]= "[Novell Sample Code]";
$replace[10]= "forge.novell.com/modules/websearch/proxy.php";
$replace[11]= "[Forums/News]";
$replace[12]= "\\1[Project]";
$replace[13]= "\\1[Community]";


$domain = "search.novell.com";
$file="/NSearch/SearchServlet?".$QUERY_STRING."*";

$fp = fsockopen($domain,80);
if($fp){
        fwrite($fp,"GET $file HTTP/1.0\r\n");
        fwrite($fp,"Host: $domain\r\n\r\n");

        while(!feof($fp)){
                $page .= fread($fp, 512);
        }
        fclose($fp);
        $pieces = explode("\r\n\r\n",$page, 2);
        $page = $pieces[1];
}else{
        echo "could not open $domain";
}
echo preg_replace($pattern, $replace, $page);
include (XOOPS_ROOT_PATH."/footer.php");
?>