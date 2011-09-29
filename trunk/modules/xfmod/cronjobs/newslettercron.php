<?php                        
include("../../../mainfile.php");
include (XOOPS_ROOT_PATH."/modules/xfmod/admin/newsletter/newsletter.php");  
include (XOOPS_ROOT_PATH."/modules/xfmod/cache/newsletterconfig.php");

if ($xoopsNewsLetterConfig['autosend_active'] == "1")
{

 $currentTime = mktime();
 $scheduleTime = mktime($xoopsNewsLetterConfig['next_send_date_hour'],0,0,$xoopsNewsLetterConfig['next_send_date_month'],$xoopsNewsLetterConfig['next_send_date_day'],$xoopsNewsLetterConfig['next_send_date_year']);
 $diff =  $currentTime - $scheduleTime;
 if ($diff > 0)
 {  
   // for debug/testing:                           
   //echo "<BR>seconds until update : ".$diff;
   //echo "<BR>Current  : ".date("M d, Y h:i A",$currentTime);
   //echo "<BR>scheduled: ".date("M d, Y h:i A",$scheduleTime);
   //echo "<BR>Sending Mail<BR>";  
   
   send_mail(); 

   // increment next update to next interval after current date
   $new_time = $scheduleTime;    
   do
   {
   	$new_time = $new_time + ($xoopsNewsLetterConfig['next_send_interval_days']*24*60*60);
   }
   while(($new_time - $currentTime) < 0 ); 
   
   save_pref(date("h",$new_time), date("d",$new_time), date("m",$new_time), date("Y",$new_time), $xoopsNewsLetterConfig['next_send_interval_days'], $xoopsNewsLetterConfig['autosend_active'], $xoopsNewsLetterConfig['subject'], $xoopsNewsLetterConfig['header_active'],$xoopsNewsLetterConfig['header_body'], $xoopsNewsLetterConfig['body_active'], $xoopsNewsLetterConfig['body_body'], $xoopsNewsLetterConfig['topdownloads_active'], $xoopsNewsLetterConfig['topactive_projects_active'], $xoopsNewsLetterConfig['footer_active'], $xoopsNewsLetterConfig['footer_body']);
   
   //echo "Saved next send date: ".date("M d, Y h:i:s A",$new_time);
 }
 else
 {          
   //echo "<BR>seconds until update : ".$diff;
   //echo "<BR>Current  : ".date("M d, Y h:i A",$currentTime);
   //echo "<BR>scheduled: ".date("M d, Y h:i A",$scheduleTime);
   
   //echo "<BR>Next update at : ".date("M d, Y h:i A",($xoopsNewsLetterConfig['next_send_interval_days']*24*60*60) + $scheduleTime)."<BR>";
   //echo "<BR>waiting to work";
 }

}


?>