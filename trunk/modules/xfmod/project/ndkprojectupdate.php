<?php

//  1) connect to webpress
//  2) iterate through list of updated ndk components
//  3) modify projects on forge to reflect changes on ndk

include_once ("../../../mainfile.php");
//$langfile="project.php";
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/pre.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/forum/forum_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/project/admin/project_admin_utils.php");
require_once(XOOPS_ROOT_PATH."/modules/xfmod/include/project_summary.php");
//include_once(XOOPS_ROOT_PATH."/class/nxoopsLDAP.php");
include_once ("ndk.php");

global $xoopsDB, $xoopsUser, $xoopsForge;
if (is_object($xoopsUser)) {
	if ( !$xoopsUser->isAdmin() ) {
		redirect_header(XOOPS_URL."/",1,_NOPERM);
		exit();
	}
}
else {
	redirect_header(XOOPS_URL."/",1,_NOPERM);
	exit();
}

$ndk_license = "Novell Developer Kit License Agreement and Separate Limited Warranty

    * License Agreement
    * Separate Limited Warranty

License Agreement

   1. PURPOSE. Novell desires to license Novell Developer Kit software and documentation (the \"Software\") to You under the terms and conditions of this Novell Developer Kit License Agreement and the separate Limited Warranty. Your representation that you are a current DeveloperNet program participant in compliance with all program requirements, and Your payment of any required license fees, and Your compliance with this Agreement are material consideration for the rights extended to you by Novell hereunder.
   2. DEFINITIONS. The following terms have the meanings assigned to the below:

      \"Derivative Software\" means the Binary code Software and/or Binary code that results from your compilation of modified or unmodified Source code Software. Derivative Software may not include Early Access Release Materials or Internal Tools.

      \"Developer Product\" means Your own computer product that incorporates Derivative Software and does not substantially duplicate the capabilities or compete with the Derivative Software or the Software.

      \"Early Access Release Materials\" means Software and/or Documentation Novell identifies as \"beta\", \"pre-release,\" \"futures,\" or as an \"early access release.\"

      \"Internal Tools\" means compatibility criteria, test suites, test tools, end user products, and other programs designed to aid in the development of, but not be incorporated in, Derivative Software Products.

   3. LICENSES. The Software and Documentation are protected by U.S. copyright laws and international copyright treaties. In addition, the possession and use of the Software and Documentation is subject to the restrictions contained in this Agreement. The Software contains various software programs with different license rights. Novell grants You the non-exclusive, non-transferable right to: a) internally use the Early Access Release Materials and Internal Tools in support of Your efforts to develop Derivative Software hereunder (but You may not include any portion of Early Access Release Materials in Derivative Software); b) use, modify, and compile Source code Software for the purpose of creating Developer Products; and, c) reproduce and distribute Derivative Software as part of a Developer Product.
   4. RESTRICTED SOFTWARE. Notwithstanding anything to the contrary in this Agreement, portions of the Software and/or Documentation may be subject to restrictions set forth in terms that accompany those portions. You agree to abide by such restrictions. If such restrictions are unacceptable to You, You may return the Software for a refund.
   5. THIRD PARTY SOFTWARE PRODUCTS. As a service to You, certain third party software products may be bundled with the Novell Developer Kit. Your rights with respect to such products are defined by terms and conditions supplied by their vendors.
   6. RESERVATION OF RIGHTS. Novell reserves all rights not expressly granted to You. Without limiting the generality of the foregoing You: a) acknowledge that the source code of the binary code Software represents and embodies trade secrets of Novell and its licensors; b) agree not to disassemble, decompile, or otherwise reverse engineer the binary code Software to discover the source code and/or trade secrets embodied in the source code; c) acknowledge that Novell has not authorized You to rent, lease, and/or time share the Software; and, d) while certain DeveloperNet test tools and test suites may be provided as part of the Novell Developer Kit, YOU AGREE AND ACKNOWLEDGE THAT ONLY NOVELL CAN ISSUE AND PUBLISH TEST BULLETINS OR AUTHORIZE USE OF THE \"YES\" OR OTHER NOVELL LOGOS.
   7. EARLY ACCESS MATERIALS. Novell does not represent or warrant that it will make the Early Access Materials generally available to the public or that any target dates will be met. Novell may change or cancel its plans at any time. You acknowledge that any Early Access Release product is of pre-release quality, has not been fully tested, and may contain errors; You assume the entire risk arising out of the use of the Early Access Release Software and any information provided with the Early Access Release Software.
   8. SUPPORT. Novell shall have no obligation to provide support to You or to users of Developer Product(s).
   9. TERM AND TERMINATION. This License will become effective on the date you acquire the Novell Developer Kit and will remain in force until terminated. You may terminate this License at any time by destroying the Documentation and the Software together with all copies and adaptations. This License shall also automatically terminate if you breach any of the terms or conditions. You agree to destroy the original and all adaptations or copies of the Software and Documentation, or to return them to Novell upon termination of this License. Your right to use any Early Access Release Software will terminate upon the earlier of (i) first commercial shipment by Novell of the Early Access Release product, (ii) other termination of this Agreement, or (iii) time-based or other disabling of the Early Access Release product. Upon termination of this Agreement, end users of the Developer Product may continue to use the Developer Product under the terms of their license from You. You will have the right to maintain one copy of each version of the source code Software and/or the Developer Product but only for the purpose and to the extent reasonably necessary for you to provide support and maintenance to end users.
  10. AUDIT. Upon reasonable notice, Novell may at its own expense audit your site(s) to ensure compliance with this Agreement. Novell will notify you in writing of any deficiency in compliance and will provide a recommended plan for resolving outstanding issues. Your failure to cure such deficiencies within 30 days after receipt of the written notification will constitute a material breach and will be grounds for immediate termination of this Agreement.
  11. DEVELOPER INDEMNIFICATION. You agree to indemnify, defend and hold Novell harmless from all damages, liabilities and expenses incurred by Novell as a result of any claim, or judgment against Novell by any third party arising out of, or connected in any manner with, distribution or use of software created by You through the use of Software licensed to You under this Agreement or arising out of breach of this License Agreement. If Novell receives notice of such a claim, Novell will promptly notify You in writing and allow You sole control of the defense of any such claim or action and all negotiations for its settlement and compromise, provided You give adequate assurances that You will diligently pursue resolution of the claim.
  12. TRANSFER. This Agreement may not be transferred or assigned without Novell's prior written approval.
  13. GOVERNING LAW. Except as otherwise restricted by law, this License shall be governed by, and interpreted in accordance with, the laws of the State of Utah of the United States of America, without regard to Utah law governing conflicts of law. This License Agreement shall be treated as though it were executed in Utah County, Utah. Any action relating to this License agreement shall be brought in a Utah court of competent jurisdiction.
  14. ENTIRE AGREEMENT. This Agreement sets forth the entire understanding and License between you and Novell and may be amended only in a writing signed by both parties. NO VENDOR, DISTRIBUTOR, DEALER, RETAILER, SALES PERSON OR OTHER PERSON IS AUTHORIZED TO MODIFY THIS LICENSE OR TO MAKE ANY WARRANTY, REPRESENTATION OR PROMISE WHICH IS DIFFERENT THAN, OR IN ADDITION TO, THE REPRESENTATIONS OR PROMISES OF THIS LICENSE.
  15. WAIVER. No waiver of any right under this Agreement shall be effective unless in writing, signed by a duly authorized representative of the party to be bound. No waiver of any past or present right arising from any breach or failure to perform shall be deemed to be a waiver of any future right arising under this Agreement.
  16. SEVERABILITY. If any provision in this License is invalid or unenforceable, that provision shall be construed, limited, modified or, if necessary, severed, to the extent necessary, to eliminate its invalidity or unenforceability, and the other provisions of this Agreement shall remain unaffected.
  17. EXPORT. Regardless of any disclosure made by you to Novell of an ultimate destination of the Program, you will not export or transfer, whether directly or indirectly, the Software, or any portion thereof, or any system containing such Software or portion thereof, to anyone outside the United States (including further export if you took delivery of the Software outside the United States) without first complying strictly and fully with all export controls that may be imposed on the Software by the United States Government or any country or organization of nations within whose jurisdiction you operate or do business. In particular, you assure Novell that, absent any required prior authorization from the Bureau of Export Administration, U.S. Department of Commerce, 14th and Constitution Avenue, Washington DC 20230, you will not export or re-export (as defined in Section 734.2(b) of the Export Administration Regulations, as amended (\"Regulations\")) the Program or any technical data or other confidential information, or direct product of any of the foregoing to any country in Country Groups D:1 or E:2 as defined in the supplement No. 1 to Section 740 of the Regulations, or such other countries as come under restriction by action of the United States Government, or to nationals from or residing in the foregoing countries, without first obtaining permission from the appropriate United States Government authorities.
  18. U.S. GOVERNMENT RESTRICTED RIGHTS. Use, duplication, or disclosure by the United States Government is subject to restrictions as set forth in FAR ? 52.227-14 (June 1987) Alternate III(g)(3) (June 1987), FAR ? 52.227-19 (June 1987), or DFARS ? 52.227-7013 (c)(1)(ii) (June 1988), as applicable. Contractor/Manufacturer is Novell, Inc., 1800 S. Novell Place, Provo, Utah 84606.
  19. OTHER. Those terms which by their nature extend beyond termination of this Agreement shall survive and remain in effect until all obligations are satisfied. The application of the United Nations Convention of Contracts for the International Sale of Goods is expressly excluded.

Separate Limited Warranty

MEDIA. Novell warrants the physical media of the Novell Developer Kit software and documentation (referred to in this limited warranty as the \"Software\") against physical defects for a period of 90 days from installation or licensing, whichever is later. Your sole remedy for defective media is replacement.

SOFTWARE. Except as provided in this Limited Warranty, Novell warrants that if the Software fails to conform substantially to the specifications in the documentation accompanying the Software and if the nonconformity is reported in writing by you to Novell within 90 days from the date that the Software is licensed, Novell will either exercise reasonable efforts to remedy the nonconformity or offer to refund any license fees paid by you upon return of all copies of the Software and documentation to Novell.

THE SOFTWARE IS ONLY COMPATIBLE WITH CERTAIN COMPUTERS AND OPERATING SYSTEMS. THE SOFTWARE IS NOT WARRANTED FOR NON-COMPATIBLE SYSTEMS. CALL NOVELL CUSTOMER SUPPORT OR YOUR DEALER FOR INFORMATION ABOUT COMPATIBILITY.

DISCLAIMER. EXCEPT AS OTHERWISE RESTRICTED BY LAW, NOVELL MAKES NO WARRANTY, REPRESENTATION OR PROMISE NOT EXPRESSLY SET FORTH IN THIS LIMITED WARRANTY. NOVELL DISCLAIMS AND EXCLUDES ANY AND ALL IMPLIED WARRANTIES OF MERCHANTABILITY, TITLE OR FITNESS FOR A PARTICULAR PURPOSE. NOVELL DOES NOT WARRANT THAT THE SOFTWARE OR ASSOCIATED DOCUMENTATION WILL SATISFY YOUR REQUIREMENTS OR THAT THE SOFTWARE AND DOCUMENTATION ARE WITHOUT DEFECT OR ERROR OR THAT THE OPERATION OF THE SOFTWARE WILL BE UNINTERRUPTED. Some states do not allow limitations on how long an implied warranty lasts, so the above limitation may not apply to you. This warranty gives you specific legal rights which vary from state to state.

THE SOFTWARE IS NOT DESIGNED FOR USE FOR ON-LINE CONTROL IN HAZARDOUS ENVIRONMENTS REQUIRING FAIL-SAFE PERFORMANCE, SUCH AS OPERATION OF NUCLEAR FACILITIES, AIRCRAFT COMMUNICATION OR CONTROL SYSTEMS, LIFE SUPPORT MACHINES, OR WEAPONS SYSTEMS, IN WHICH SOFTWARE FAILURE COULD LEAD DIRECTLY TO PERSONAL INJURY OR SEVERE ENVIRONMENT DAMAGE.

LIMITATION OF LIABILITY. EXCEPT AS OTHERWISE RESTRICTED BY LAW, NOVELL'S AGGREGATE LIABILITY ARISING FROM OR RELATING TO YOUR USE OF THE SOFTWARE, ASSOCIATED DOCUMENTATION OR ANY SERVICES PROVIDED BY NOVELL AND/OR ITS AGENTS IS LIMITED TO THE TOTAL OF ALL PAYMENTS MADE BY OR FOR YOU FOR THE SOFTWARE AND DOCUMENTATION. NEITHER NOVELL NOR ANY OF ITS LICENSORS, EMPLOYEES, OR AGENTS SHALL IN ANY CASE BE LIABLE FOR ANY SPECIAL, INCIDENTAL, CONSEQUENTIAL, INDIRECT OR PUNITIVE DAMAGES EVEN IF ADVISED OF THE POSSIBILITY OF THOSE DAMAGES. NEITHER NOVELL NOR ANY OF ITS LICENSORS, EMPLOYEES, OR AGENTS IS RESPONSIBLE FOR LOST PROFITS OR REVENUE, LOSS OF USE OF SOFTWARE, LOSS OF DATA, COSTS OF RE-CREATING LOST DATA, OR THE COST OF ANY SUBSTITUTE EQUIPMENT OR PROGRAM. Some states do not allow the exclusion or limitation of incidental or consequential damages, so the above limitation or exclusion may not apply to you.
Copyright Novell, Inc. 2003, (lc#3.13.02)";



//consume xml file which will have only ndk components with changes...

$ndk;
// This function will run through the ndk.xml file and fill $ndk with related elements to create or update
parse();

isset($_POST['op']) ? $op = $_POST['op'] : $op = '';

if ($op == 'save') {
	global $ndk, $project;

	$release_name = $_POST['release_name'];
	$release_time = $_POST['release_time'];
	$group_name = $_POST['group_name'];

        if (!($group_name == "dirxml" || $group_name == "dirxmllin" || $group_name == "dirxmlsol" || 
	      $group_name == "cldap" || $group_name == "cldaplin" || $group_name == "cldapsol" || $group_name == "cldapaix" || $group_name == "cldaphpux" || 
	      $group_name == "jldap" || $group_name == "jldapunx" || $group_name == "jvm117b" || $group_name == "jvm13" || $group_name == "jvm141" || $group_name == "jvm142" ||
	      $group_name == "odbc" || $group_name == "odbcrw" || $group_name == "php" || $group_name == "php2")) {

		echo $group_name.' = not a special case';
		$group_id = $_POST['group_id'];

		// needed only for unix_name check in frs.class
		$project =& group_get_object($group_id);

		// GET GROUP INFO
		///$res = $xoopsDB->query("SELECT * "
		//."FROM ".$xoopsDB->prefix("xf_groups")." "
		//			  ."WHERE group_id='".$group_id."'");
		//$rows = $xoopsDB->getRowsNum($res);
		//$group_name = unofficial_getDBResult($res,$i,'unix_group_name');

		while (list($key,$val) = each($ndk)) {

			if($group_name == $val->shortname) {
				echo "<h2>".$group_name."</h2>";
				// GET PACKAGE INFO
				$pres = $xoopsDB->query("SELECT * "
				."FROM ".$xoopsDB->prefix("xf_frs_package")." "
							  ."WHERE group_id='".$group_id."' AND name LIKE '%NDK%'");
				$prows = $xoopsDB->getRowsNum($pres);

				for ($i=0; $i<$prows; $i++) {
					$package_name = unofficial_getDBResult($pres,$i,'name');
									
					$package_id = unofficial_getDBResult($pres,$i,'package_id');
					echo "<p/>package info from db = groupid - ".$group_id." | packageid - ".$package_id." - ".$package_name."<br/>";
	
					// ADD NEW RELEASE TO PROJECT PACKAGES
					$frs = new FRS($group_id);
					$release_id = $frs->frsAddRelease($release_name, $package_id);
					$frs->frsChangeRelease($release_date, $release_name, 0, 1, $val->getWhatsNew(), $val->getWhatsNewArchive(), $package_id, $package_id, $release_id, $val->getDependencies());

					echo "Adding ".$releas_name." release<br/>";
					$tempdown = $val->getDownloads();
					while(list($key2,$val2) = each($tempdown)) {
						// ALL COMPONENT
						if (((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.')) && strpos($package_name,'Complete'))
						  || (strpos($val2->getName(),'_doc.') && strpos($package_name,'Documentation'))
						  || (strpos($val2->getName(),'_sample.') && strpos($package_name,'Sample'))
						  || (!((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.'))) && !strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'_sample.') && strpos($package_name,'Binaries'))
						) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');

									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe' AND release_id='".$rel_id."'");

									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											//$file_url = substr_replace(unofficial_getDBResult($fres,$k,'file_url'),"2004/jun/" , 61, 0);
											//$file_url = substr_replace($val2->getPath(),"2004/jun/" , 79, 0);
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , strrpos($val2->getPath(), "filename=")+9, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification of file update</font><br/>";
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							$frs->frsAddFile($file_date, $val2->getName(), $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$val2->getName()." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}
/*						// DOCUMENTATION
						else if (strpos($val2->getName(),'_doc.') && strpos($package_name,'Documentation')) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');
		
									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe' AND release_id='".$rel_id."'");
		
									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											//$file_url = substr_replace(unofficial_getDBResult($fres,$k,'file_url'),"2004/jun/" , 61, 0);
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , 79, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification</font><br/>";								
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							$frs->frsAddFile($file_date, $val2->getName(), $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$val2->getName()." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}
						// SAMPLE CODE
						else if (strpos($val2->getName(),'_sample.') && strpos($package_name,'Sample')) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');
		
									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe' AND release_id='".$rel_id."'");
		
									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											//$file_url = substr_replace(unofficial_getDBResult($fres,$k,'file_url'),"2004/jun/" , 61, 0);
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , 82, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification</font><br/>";
									}
								}
							}						
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							$frs->frsAddFile($file_date, $val2->getName(), $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$val2->getName()." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}
						// BINARIES
						else if (!((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.'))) && !strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'_sample.') && strpos($package_name,'Binaries')) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');
		
									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe' AND release_id='".$rel_id."'");
									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											//$file_url = substr_replace(unofficial_getDBResult($fres,$k,'file_url'),"2004/jun/" , 61, 0);
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , 84, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										//$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification</font><br/>";	
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							$frs->frsAddFile($file_date, $val2->getName(), $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$val2->getName()." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}*/
					}
				}
	
				// DELETE DOC AND SAMPLE CODE DOWNLOAD FROM DOC AND SAMPLE CODE PROJECT TABS
				$query = "DELETE FROM ".$xoopsDB->prefix("xf_doc_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);
	
				$query = "DELETE FROM ".$xoopsDB->prefix("xf_sample_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);
					
				// SUBMIT NEWS
				$new_id = forum_create_forum($xoopsForge['sysnews'], "Updated ".$val->getName(), 1, 0,'',0);
				$sql = "INSERT INTO ".$xoopsDB->prefix("xf_news_bytes")." "
							      ."(group_id,submitted_by,is_approved,date,forum_id,summary,details) "
										."VALUES ("
										."'$group_id',"
										."'".$xoopsUser->getVar("uid")."',"
										."'1',"
										."'".time()."',"
										."'$new_id',"
										."'Updated ".$val->getName()."',"
										."'".$val->getWhatsNew()."')";
				$xoopsDB->queryF($sql);
				echo "<font color='green'><p/>added news of project update</font><p/>";
			}
		}
	}
	else if($group_name == "cldap" || $group_name == "cldaplin" || $group_name == "cldapsol" || $group_name == "cldapaix" || $group_name == "cldaphpux") {
		// needed only for unix_name check in frs.class
		echo "special case for cldap";
		$cldap = '1108';
		$project =& group_get_object($cldap);

		while (list($key,$val) = each($ndk)) {

			if($group_name == $val->shortname) {
				echo "<h2>".$group_name."</h2>";
				// GET PACKAGE INFO
				$pres = $xoopsDB->query("SELECT * "
				."FROM ".$xoopsDB->prefix("xf_frs_package")." "
							  ."WHERE group_id='".$cldap."' AND name LIKE '%NDK%'");
				$prows = $xoopsDB->getRowsNum($pres);

				for ($i=0; $i<$prows; $i++) {
					$package_name = unofficial_getDBResult($pres,$i,'name');
									
					$package_id = unofficial_getDBResult($pres,$i,'package_id');
					echo "<p/>package info from db = groupid - ".$cldap." | packageid - ".$package_id." - ".$package_name."<br/>";

					// ADD NEW RELEASE TO PROJECT PACKAGES IF IT DOESN'T ALREADY EXIST
					$frs = new FRS($cldap);
					
					$relres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
						."WHERE name = '".$release_name."' AND package_id='".$package_id."'");
						
					$relrows = $xoopsDB->getRowsNum($relres);
					//if release doesn't exist, create it
					if($relrows == 0) {					
						$release_id = $frs->frsAddRelease($release_name, $package_id);
						$frs->frsChangeRelease($release_date, $release_name, 0, 1, $val->getWhatsNew(), $val->getWhatsNewArchive(), $package_id, $package_id, $release_id, $val->getDependencies());
						echo "Adding ".$release_name." release<br/>";
					}
					else {
						$release_id = unofficial_getDBResult($relres,0,'release_id');
						echo "Using existing ".$release_name." release<br/>";
					}

					$tempdown = $val->getDownloads();
					while(list($key2,$val2) = each($tempdown)) {
						// ALL COMPONENT
						if ((strpos($val2->getName(),'lin_all.') && strpos($package_name,'Complete Download - Linux')) 
						 || (strpos($val2->getName(),'lin_doc.') && strpos($package_name,'Documentation - Linux')) 
						 || (strpos($val2->getName(),'lin_sample.') && strpos($package_name,'Sample Code - Linux')) 
						 || (strpos($val2->getName(),'sol_all.') && strpos($package_name,'Complete Download - Solaris'))
						 || (strpos($val2->getName(),'sol_doc.') && strpos($package_name,'Documentation - Solaris'))
	                                         || (strpos($val2->getName(),'sol_sample.') && strpos($package_name,'Sample Code - Solaris'))
						 || (strpos($val2->getName(),'hpux_all.') && strpos($package_name,'Complete Download - HP')) 
						 || (strpos($val2->getName(),'hpux_doc.') && strpos($package_name,'Documentation - HP')) 
						 || (strpos($val2->getName(),'hpux_sample.') && strpos($package_name,'Sample Code - HP')) 
						 || (strpos($val2->getName(),'aix_all.') && strpos($package_name,'Complete Download - AIX'))
						 || (strpos($val2->getName(),'aix_doc.') && strpos($package_name,'Documentation - AIX'))
	                                         || (strpos($val2->getName(),'aix_sample.') && strpos($package_name,'Sample Code - AIX'))					
						 || (strpos($val2->getName(),'_all.') && !strpos($val2->getName(),'lin') && !strpos($val2->getName(),'sol') && !strpos($val2->getName(),'hpux') && !strpos($val2->getName(),'aix') && strpos($package_name,'Complete Download - NetWare'))
						 || (strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'lin') && !strpos($val2->getName(),'sol') && !strpos($val2->getName(),'hpux') && !strpos($val2->getName(),'aix') && strpos($package_name,'Documentation - NetWare'))
	                                         || (strpos($val2->getName(),'_sample.') && !strpos($val2->getName(),'lin') && !strpos($val2->getName(),'sol') && !strpos($val2->getName(),'hpux') && !strpos($val2->getName(),'aix') && strpos($package_name,'Sample Code - NetWare'))					
						 || (!strpos($val2->getName(),'_all.') && !strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'_sample.') && ((strpos($val2->getName(),'lin') && strpos($package_name,'Binaries - Linux')) || (strpos($val2->getName(),'sol') && strpos($package_name,'Binaries - Solaris')) || (strpos($val2->getName(),'hpux') && strpos($package_name,'Binaries - HP')) || (strpos($val2->getName(),'aix') && strpos($package_name,'Binaries - AIX')) || (!strpos($val2->getName(),'lin') && !strpos($val2->getName(),'sol') && !strpos($val2->getName(),'hupx') && !strpos($val2->getName(),'aix') && strpos($package_name,'Binaries - NetWare'))))			
						) {
						
						if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');

									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe%' AND release_id='".$rel_id."'");

									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , strrpos($val2->getPath(), "filename=")+9, 0);											
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification of file update</font><br/>";
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							$frs->frsAddFile($file_date, $val2->getName(), $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$val2->getName()." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}
					}
				}

				// DELETE DOC AND SAMPLE CODE DOWNLOAD FROM DOC AND SAMPLE CODE PROJECT TABS
				$query = "DELETE FROM ".$xoopsDB->prefix("xf_doc_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);

				$query = "DELETE FROM ".$xoopsDB->prefix("xf_sample_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);
					
				// SUBMIT NEWS
				$new_id = forum_create_forum($xoopsForge['sysnews'], "Updated ".$val->getName(), 1, 0,'',0);
				$sql = "INSERT INTO ".$xoopsDB->prefix("xf_news_bytes")." "
							      ."(group_id,submitted_by,is_approved,date,forum_id,summary,details) "
										."VALUES ("
										."'".$cldap."',"
										."'".$xoopsUser->getVar("uid")."',"
										."'1',"
										."'".time()."',"
										."'$new_id',"
										."'Updated ".$val->getName()."',"
										."'".$val->getWhatsNew()."')";
				$xoopsDB->queryF($sql);
				echo "<font color='green'><p/>added news of project update</font><p/>";
			}
		}
	}
	else if($group_name == "jvm117b" || $group_name == "jvm13" || $group_name == "jvm141" || $group_name == "jvm142") {
		// needed only for unix_name check in frs.class
		echo "special case for jvm";
		$jvm = '1123';
		$project =& group_get_object($jvm);

		while (list($key,$val) = each($ndk)) {

			if($group_name == $val->shortname) {
				echo "<h2>".$group_name."</h2>";
				// GET PACKAGE INFO
				$pres = $xoopsDB->query("SELECT * "
				."FROM ".$xoopsDB->prefix("xf_frs_package")." "
							  ."WHERE group_id='".$jvm."' AND name LIKE '%NDK%'");
				$prows = $xoopsDB->getRowsNum($pres);

				for ($i=0; $i<$prows; $i++) {
					$package_name = unofficial_getDBResult($pres,$i,'name');
									
					$package_id = unofficial_getDBResult($pres,$i,'package_id');
					echo "<p/>package info from db = groupid - ".$jvm." | packageid - ".$package_id." - ".$package_name."<br/>";

					// ADD NEW RELEASE TO PROJECT PACKAGES IF IT DOESN'T ALREADY EXIST
					$frs = new FRS($jvm);
					$relres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
						."WHERE name = '".$release_name."' AND package_id='".$package_id."'");
						
					$relrows = $xoopsDB->getRowsNum($relres);
					//if release doesn't exist, create it
					if($relrows == 0) {					
						$release_id = $frs->frsAddRelease($release_name, $package_id);
						$frs->frsChangeRelease($release_date, $release_name, 0, 1, $val->getWhatsNew(), $val->getWhatsNewArchive(), $package_id, $package_id, $release_id, $val->getDependencies());
						echo "Adding ".$release_name." release<br/>";
					}
					else {
						$release_id = unofficial_getDBResult($relres,0,'release_id');
						echo "Using existing ".$release_name." release<br/>";
					}

					$tempdown = $val->getDownloads();
					while(list($key2,$val2) = each($tempdown)) {
						// ALL COMPONENT
						if (((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.')) && strpos($package_name,'Complete'))
						  || (strpos($val2->getName(),'_doc.') && strpos($package_name,'Documentation'))
						  || (strpos($val2->getName(),'_sample.') && strpos($package_name,'Sample')) 
						  || (!((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.'))) && !strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'_sample.') && strpos($package_name,'Binaries'))
						) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');

									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe%' AND release_id='".$rel_id."'");

									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										if(unofficial_getDBResult($fres,$k,'filename') == $val2->getName()) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , strrpos($val2->getPath(), "filename=")+9, 0);											
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
											$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
											$frs->frsSendNotice($group_id, $release_id, $package_id);
											echo "<font color='blue'>send notification of file update</font><br/>";
										}
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							$frs->frsAddFile($file_date, $val2->getName(), $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$val2->getName()." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}
					}
				}

				// DELETE DOC AND SAMPLE CODE DOWNLOAD FROM DOC AND SAMPLE CODE PROJECT TABS
				$query = "DELETE FROM ".$xoopsDB->prefix("xf_doc_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);

				$query = "DELETE FROM ".$xoopsDB->prefix("xf_sample_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);
					
				// SUBMIT NEWS
				$new_id = forum_create_forum($xoopsForge['sysnews'], "Updated ".$val->getName(), 1, 0,'',0);
				$sql = "INSERT INTO ".$xoopsDB->prefix("xf_news_bytes")." "
							      ."(group_id,submitted_by,is_approved,date,forum_id,summary,details) "
										."VALUES ("
										."'".$jvm."',"
										."'".$xoopsUser->getVar("uid")."',"
										."'1',"
										."'".time()."',"
										."'$new_id',"
										."'Updated ".$val->getName()."',"
										."'".$val->getWhatsNew()."')";
				$xoopsDB->queryF($sql);
				echo "<font color='green'><p/>added news of project update</font><p/>";
			}
		}
	}
	else if($group_name == "dirxml" || $group_name == "dirxmllin" || $group_name == "dirxmlsol") {
		// needed only for unix_name check in frs.class
		echo "special case for dirxml";
		$dirxml = '1048';
		$project =& group_get_object($dirxml);
		
		while (list($key,$val) = each($ndk)) {

			if($group_name == $val->shortname) {
				echo "<h2>".$group_name."</h2>";
				// GET PACKAGE INFO
				$pres = $xoopsDB->query("SELECT * "
				."FROM ".$xoopsDB->prefix("xf_frs_package")." "
							  ."WHERE group_id='".$dirxml."' AND name LIKE '%NDK%'");
				$prows = $xoopsDB->getRowsNum($pres);

				for ($i=0; $i<$prows; $i++) {
					$package_name = unofficial_getDBResult($pres,$i,'name');
									
					$package_id = unofficial_getDBResult($pres,$i,'package_id');
					echo "<p/>package info from db = groupid - ".$dirxml." | packageid - ".$package_id." - ".$package_name."<br/>";

					// ADD NEW RELEASE TO PROJECT PACKAGES IF IT DOESN'T ALREADY EXIST
					$frs = new FRS($dirxml);
					
					$relres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
						."WHERE name = '".$release_name."' AND package_id='".$package_id."'");
						
					$relrows = $xoopsDB->getRowsNum($relres);
					//if release doesn't exist, create it
					if($relrows == 0) {					
						$release_id = $frs->frsAddRelease($release_name, $package_id);
						$frs->frsChangeRelease($release_date, $release_name, 0, 1, $val->getWhatsNew(), $val->getWhatsNewArchive(), $package_id, $package_id, $release_id, $val->getDependencies());
						echo "Adding ".$release_name." release<br/>";
					}
					else {
						$release_id = unofficial_getDBResult($relres,0,'release_id');
						echo "Using existing ".$release_name." release<br/>";
					}

					$tempdown = $val->getDownloads();
					while(list($key2,$val2) = each($tempdown)) {
						// ALL COMPONENT
						if (((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.')) && strpos($package_name,'Complete'))
						  || (strpos($val2->getName(),'_doc.') && strpos($package_name,'Documentation'))
						  || (strpos($val2->getName(),'_sample.') && strpos($package_name,'Sample')) 
						  || (!((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.'))) && !strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'_sample.') && strpos($package_name,'Binaries'))
						) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');

									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe%' AND release_id='".$rel_id."'");

									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , strrpos($val2->getPath(), "filename=")+9, 0);											
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification of file update</font><br/>";
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							
							if(strstr($val2->getName(),"lin")) {
								$file_name = $val2->getName()." (Linux)";
							}
							else if (strstr($val2->getName(),"sol")) {
								$file_name = $val2->getName()." (Solaris)";
							}
							else {
								$file_name = $val2->getName()." (NetWare and NT)";
							}							
							
							$frs->frsAddFile($file_date, $file_name, $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$file_name." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						} /*
						// DOCUMENTATION
						else if (strpos($val2->getName(),'_doc.') && strpos($package_name,'Documentation')) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');
		
									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe%' AND release_id='".$rel_id."'");
		
									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											//$file_url = substr_replace(unofficial_getDBResult($fres,$k,'file_url'),"2004/jun/" , 61, 0);
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , 79, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification of file update</font><br/>";
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							
							if(strstr($val2->getName(),"lin")) {
								$file_name = $val2->getName()." (Linux)";
							}
							else if (strstr($val2->getName(),"sol")) {
								$file_name = $val2->getName()." (Solaris)";
							}
							else {
								$file_name = $val2->getName()." (NetWare and NT)";
							}							
							
							$frs->frsAddFile($file_date, $file_name, $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$file_name." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}
						// SAMPLE CODE
						else if (strpos($val2->getName(),'_sample.') && strpos($package_name,'Sample')) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');
		
									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe%' AND release_id='".$rel_id."'");
		
									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											//$file_url = substr_replace(unofficial_getDBResult($fres,$k,'file_url'),"2004/jun/" , 61, 0);
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , 82, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification of file update</font><br/>";
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							
							if(strstr($val2->getName(),"lin")) {
								$file_name = $val2->getName()." (Linux)";
							}
							else if (strstr($val2->getName(),"sol")) {
								$file_name = $val2->getName()." (Solaris)";
							}
							else {
								$file_name = $val2->getName()." (NetWare and NT)";
							}							
							
							$frs->frsAddFile($file_date, $file_name, $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$file_name." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}
						// BINARIES
						else if (!((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.'))) && !strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'_sample.') && strpos($package_name,'Binaries')) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');
		
									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe%' AND release_id='".$rel_id."'");
									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											//$file_url = substr_replace(unofficial_getDBResult($fres,$k,'file_url'),"2004/jun/" , 61, 0);
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , 84, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification of file update</font><br/>";
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							
							if(strstr($val2->getName(),"lin")) {
								$file_name = $val2->getName()." (Linux)";
							}
							else if (strstr($val2->getName(),"sol")) {
								$file_name = $val2->getName()." (Solaris)";
							}
							else {
								$file_name = $val2->getName()." (NetWare and NT)";
							}							
							
							$frs->frsAddFile($file_date, $file_name, $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$file_name." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}*/
					}
				}
	
				// DELETE DOC AND SAMPLE CODE DOWNLOAD FROM DOC AND SAMPLE CODE PROJECT TABS
				$query = "DELETE FROM ".$xoopsDB->prefix("xf_doc_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);
	
				$query = "DELETE FROM ".$xoopsDB->prefix("xf_sample_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);
					
				// SUBMIT NEWS
				$new_id = forum_create_forum($xoopsForge['sysnews'], "Updated ".$val->getName(), 1, 0,'',0);
				$sql = "INSERT INTO ".$xoopsDB->prefix("xf_news_bytes")." "
							      ."(group_id,submitted_by,is_approved,date,forum_id,summary,details) "
										."VALUES ("
										."'".$dirxml."',"
										."'".$xoopsUser->getVar("uid")."',"
										."'1',"
										."'".time()."',"
										."'$new_id',"
										."'Updated ".$val->getName()."',"
										."'".$val->getWhatsNew()."')";
				$xoopsDB->queryF($sql);
				echo "<font color='green'><p/>added news of project update</font><p/>";
			}
		}
	}
	else if($group_name == "jldap" || $group_name == "jldapunx") {
		// needed only for unix_name check in frs.class
		echo "special case for jldap";
		$jldap = '1107';
		$project =& group_get_object($jldap);
		
		while (list($key,$val) = each($ndk)) {

			if($group_name == $val->shortname) {
				echo "<h2>".$group_name."</h2>";
				// GET PACKAGE INFO
				$pres = $xoopsDB->query("SELECT * "
				."FROM ".$xoopsDB->prefix("xf_frs_package")." "
							  ."WHERE group_id='".$jldap."' AND name LIKE '%NDK%'");
				$prows = $xoopsDB->getRowsNum($pres);

				for ($i=0; $i<$prows; $i++) {
					$package_name = unofficial_getDBResult($pres,$i,'name');
									
					$package_id = unofficial_getDBResult($pres,$i,'package_id');
					echo "<p/>package info from db = groupid - ".$jldap." | packageid - ".$package_id." - ".$package_name."<br/>";

					// ADD NEW RELEASE TO PROJECT PACKAGES IF IT DOESN'T ALREADY EXIST
					$frs = new FRS($jldap);
					
					$relres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
						."WHERE name = '".$release_name."' AND package_id='".$package_id."'");
						
					$relrows = $xoopsDB->getRowsNum($relres);
					//if release doesn't exist, create it
					if($relrows == 0) {					
						$release_id = $frs->frsAddRelease($release_name, $package_id);
						$frs->frsChangeRelease($release_date, $release_name, 0, 1, $val->getWhatsNew(), $val->getWhatsNewArchive(), $package_id, $package_id, $release_id, $val->getDependencies());
						echo "Adding ".$release_name." release<br/>";
					}
					else {
						$release_id = unofficial_getDBResult($relres,0,'release_id');
						echo "Using existing ".$release_name." release<br/>";
					}

					$tempdown = $val->getDownloads();
					while(list($key2,$val2) = each($tempdown)) {
						// ALL COMPONENT
						if (((strpos($val2->getName(),'unx_all.') && strpos($package_name,'Complete Download - UNIX')) || (strpos($val2->getName(),'_all.') && !strpos($val2->getName(),'unx') && strpos($package_name,'Complete Download - NetWare')))
						  || ((strpos($val2->getName(),'unx_doc.') && strpos($package_name,'Documentation - UNIX')) || (strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'unx') && strpos($package_name,'Documentation - NetWare')))
						  || ((strpos($val2->getName(),'unx_sample.') && strpos($package_name,'Sample Code - UNIX')) || (strpos($val2->getName(),'_sample.') && !strpos($val2->getName(),'unx') && strpos($package_name,'Sample Code - NetWare')))
						  || ((!strpos($val2->getName(),'unx_all.') && !strpos($val2->getName(),'unx_doc.') && !strpos($val2->getName(),'unx_sample.') && strpos($val2->getName(),'unx') && strpos($package_name,'Binaries - UNIX')) || (!strpos($val2->getName(),'_all.') && !strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'_sample.') && !strpos($val2->getName(),'unx') && strpos($package_name,'Binaries - NetWare')))
						) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');

									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe%' AND release_id='".$rel_id."'");

									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , strrpos($val2->getPath(), "filename=")+9, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										//$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification of file update</font><br/>";
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							$frs->frsAddFile($file_date, $val2->getName(), $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$val2->getName()." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}/*
						// DOCUMENTATION 
						else if ((strpos($val2->getName(),'unx_doc.') && strpos($package_name,'Documentation - UNIX')) || (strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'unx') && strpos($package_name,'Documentation - NetWare'))) {
						//else if (strpos($val2->getName(),'unx_doc.') && strpos($package_name,'Documentation - UNIX')) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');
		
									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe%' AND release_id='".$rel_id."'");
		
									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											//$file_url = substr_replace(unofficial_getDBResult($fres,$k,'file_url'),"2004/jun/" , 61, 0);
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , 79, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										//$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification of file update</font><br/>";
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							$frs->frsAddFile($file_date, $val2->getName(), $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$val2->getName()." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}
						// SAMPLE CODE
						else if ((strpos($val2->getName(),'unx_sample.') && strpos($package_name,'Sample Code - UNIX')) || (strpos($val2->getName(),'_sample.') && !strpos($val2->getName(),'unx') && strpos($package_name,'Sample Code - NetWare'))) {
						//else if (strpos($val2->getName(),'unx_sample.') && strpos($package_name,'Sample Code - UNIX')) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');
		
									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe%' AND release_id='".$rel_id."'");
		
									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											//$file_url = substr_replace(unofficial_getDBResult($fres,$k,'file_url'),"2004/jun/" , 61, 0);
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , 82, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										//$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification of file update</font><br/>";
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							$frs->frsAddFile($file_date, $val2->getName(), $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$val2->getName()." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}
						// BINARIES 
						else if ((!strpos($val2->getName(),'unx_all.') && !strpos($val2->getName(),'unx_doc.') && !strpos($val2->getName(),'unx_sample.') && strpos($val2->getName(),'unx') && strpos($package_name,'Binaries - UNIX')) || (!strpos($val2->getName(),'_all.') && !strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'_sample.') && !strpos($val2->getName(),'unx') && strpos($package_name,'Binaries - NetWare'))) {
						//else if (!((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.'))) && !strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'_sample.') && strpos($package_name,'Binaries - UNIX')) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');
		
									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe%' AND release_id='".$rel_id."'");
									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										//if(!strstr(unofficial_getDBResult($fres,$k,'file_url'),"archive")) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											//$file_url = substr_replace(unofficial_getDBResult($fres,$k,'file_url'),"2004/jun/" , 61, 0);
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , 84, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
										//}
										$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
										//$frs->frsSendNotice($group_id, $release_id, $package_id);
										echo "<font color='blue'>send notification of file update</font><br/>";
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							$frs->frsAddFile($file_date, $val2->getName(), $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$val2->getName()." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}*/
					}
				}
	
				// DELETE DOC AND SAMPLE CODE DOWNLOAD FROM DOC AND SAMPLE CODE PROJECT TABS
				$query = "DELETE FROM ".$xoopsDB->prefix("xf_doc_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);
	
				$query = "DELETE FROM ".$xoopsDB->prefix("xf_sample_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);
					
				// SUBMIT NEWS
				$new_id = forum_create_forum($xoopsForge['sysnews'], "Updated ".$val->getName(), 1, 0,'',0);
				$sql = "INSERT INTO ".$xoopsDB->prefix("xf_news_bytes")." "
							      ."(group_id,submitted_by,is_approved,date,forum_id,summary,details) "
										."VALUES ("
										."'".$jldap."',"
										."'".$xoopsUser->getVar("uid")."',"
										."'1',"
										."'".time()."',"
										."'$new_id',"
										."'Updated ".$val->getName()."',"
										."'".$val->getWhatsNew()."')";
				$xoopsDB->queryF($sql);
				echo "<font color='green'><p/>added news of project update</font><p/>";
			}
		}
	}
	else if($group_name == "odbc" || $group_name == "odbcrw") {
		// needed only for unix_name check in frs.class
		echo "special case for odbc";
		$odbc = '1075';
		$project =& group_get_object($odbc);

		while (list($key,$val) = each($ndk)) {

			if($group_name == $val->shortname) {
				echo "<h2>".$group_name."</h2>";
				// GET PACKAGE INFO
				$pres = $xoopsDB->query("SELECT * "
				."FROM ".$xoopsDB->prefix("xf_frs_package")." "
							  ."WHERE group_id='".$odbc."' AND name LIKE '%NDK%'");
				$prows = $xoopsDB->getRowsNum($pres);

				for ($i=0; $i<$prows; $i++) {
					$package_name = unofficial_getDBResult($pres,$i,'name');
									
					$package_id = unofficial_getDBResult($pres,$i,'package_id');
					echo "<p/>package info from db = groupid - ".$odbc." | packageid - ".$package_id." - ".$package_name."<br/>";

					// ADD NEW RELEASE TO PROJECT PACKAGES IF IT DOESN'T ALREADY EXIST
					$frs = new FRS($odbc);
					$relres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
						."WHERE name = '".$release_name."' AND package_id='".$package_id."'");
						
					$relrows = $xoopsDB->getRowsNum($relres);
					//if release doesn't exist, create it
					if($relrows == 0) {					
						$release_id = $frs->frsAddRelease($release_name, $package_id);
						$frs->frsChangeRelease($release_date, $release_name, 0, 1, $val->getWhatsNew(), $val->getWhatsNewArchive(), $package_id, $package_id, $release_id, $val->getDependencies());
						echo "Adding ".$release_name." release<br/>";
					}
					else {
						$release_id = unofficial_getDBResult($relres,0,'release_id');
						echo "Using existing ".$release_name." release<br/>";
					}

					$tempdown = $val->getDownloads();
					while(list($key2,$val2) = each($tempdown)) {
						// ALL COMPONENT
						if (((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.')) && strpos($package_name,'Complete'))
						  || (strpos($val2->getName(),'_doc.') && strpos($package_name,'Documentation'))
						  || (strpos($val2->getName(),'_sample.') && strpos($package_name,'Sample')) 
						  || (!((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.'))) && !strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'_sample.') && strpos($package_name,'Binaries'))
						) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');

									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe%' AND release_id='".$rel_id."'");

									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										if(unofficial_getDBResult($fres,$k,'filename') == $val2->getName()) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											//$file_url = substr_replace(unofficial_getDBResult($fres,$k,'file_url'),"2004/jun/" , 61, 0);
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , strrpos($val2->getPath(), "filename=")+9, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
											$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
											$frs->frsSendNotice($group_id, $release_id, $package_id);
											echo "<font color='blue'>send notification of file update</font><br/>";
										}
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							$frs->frsAddFile($file_date, $val2->getName(), $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$val2->getName()." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}
					}
				}

				// DELETE DOC AND SAMPLE CODE DOWNLOAD FROM DOC AND SAMPLE CODE PROJECT TABS
				$query = "DELETE FROM ".$xoopsDB->prefix("xf_doc_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);

				$query = "DELETE FROM ".$xoopsDB->prefix("xf_sample_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);
					
				// SUBMIT NEWS
				$new_id = forum_create_forum($xoopsForge['sysnews'], "Updated ".$val->getName(), 1, 0,'',0);
				$sql = "INSERT INTO ".$xoopsDB->prefix("xf_news_bytes")." "
							      ."(group_id,submitted_by,is_approved,date,forum_id,summary,details) "
										."VALUES ("
										."'".$odbc."',"
										."'".$xoopsUser->getVar("uid")."',"
										."'1',"
										."'".time()."',"
										."'$new_id',"
										."'Updated ".$val->getName()."',"
										."'".$val->getWhatsNew()."')";
				$xoopsDB->queryF($sql);
				echo "<font color='green'><p/>added news of project update</font><p/>";
			}
		}
	}
	else if($group_name == "php" || $group_name == "php2") {
		// needed only for unix_name check in frs.class
		echo "special case for php";
		$php = '1078';
		$project =& group_get_object($php);

		while (list($key,$val) = each($ndk)) {

			if($group_name == $val->shortname) {
				echo "<h2>".$group_name."</h2>";
				// GET PACKAGE INFO
				$pres = $xoopsDB->query("SELECT * "
				."FROM ".$xoopsDB->prefix("xf_frs_package")." "
							  ."WHERE group_id='".$php."' AND name LIKE '%NDK%'");
				$prows = $xoopsDB->getRowsNum($pres);

				for ($i=0; $i<$prows; $i++) {
					$package_name = unofficial_getDBResult($pres,$i,'name');
									
					$package_id = unofficial_getDBResult($pres,$i,'package_id');
					echo "<p/>package info from db = groupid - ".$php." | packageid - ".$package_id." - ".$package_name."<br/>";

					// ADD NEW RELEASE TO PROJECT PACKAGES IF IT DOESN'T ALREADY EXIST
					$frs = new FRS($php);
					$relres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
						."WHERE name = '".$release_name."' AND package_id='".$package_id."'");
						
					$relrows = $xoopsDB->getRowsNum($relres);
					//if release doesn't exist, create it
					if($relrows == 0) {					
						$release_id = $frs->frsAddRelease($release_name, $package_id);
						$frs->frsChangeRelease($release_date, $release_name, 0, 1, $val->getWhatsNew(), $val->getWhatsNewArchive(), $package_id, $package_id, $release_id, $val->getDependencies());
						echo "Adding ".$release_name." release<br/>";
					}
					else {
						$release_id = unofficial_getDBResult($relres,0,'release_id');
						echo "Using existing ".$release_name." release<br/>";
					}

					$tempdown = $val->getDownloads();
					while(list($key2,$val2) = each($tempdown)) {
						// ALL COMPONENT
						if (((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.')) && strpos($package_name,'Complete'))
						  || (strpos($val2->getName(),'_doc.') && strpos($package_name,'Documentation'))
						  || (strpos($val2->getName(),'_sample.') && strpos($package_name,'Sample')) 
						  || (!((strpos($val2->getName(),'_al.') || strpos($val2->getName(),'_all.'))) && !strpos($val2->getName(),'_doc.') && !strpos($val2->getName(),'_sample.') && strpos($package_name,'Binaries'))
						) {
							if($val2->getUpdate() == 'Y') {
							// if Y, then update the file path of the last file like this to /2004/jun
								$rres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_release")." "
									."WHERE package_id='".$package_id."' ORDER BY 'release_date' DESC");
											
								$rrows = $xoopsDB->getRowsNum($rres);
								//var_dump("<h2>YES</h2>");
								if($rrows !=0) {
									$rel_id = unofficial_getDBResult($rres,1,'release_id');

									$fres = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_frs_file")." "
									."WHERE filename LIKE '%.exe%' AND release_id='".$rel_id."'");

									$frows = $xoopsDB->getRowsNum($fres);
									for ($k=0; $k<$frows; $k++) {
										if(rtrim(substr(unofficial_getDBResult($fres,$k,'filename'),0,strpos(unofficial_getDBResult($fres,$k,'filename'),"("))) == $val2->getName()) {
											$file_id = unofficial_getDBResult($fres,$k,'file_id');
											$file_url = substr_replace($val2->getPath(),"2004/jun/" , strrpos($val2->getPath(), "filename=")+9, 0);
											$file_name = unofficial_getDBResult($fres,$k,'filename');
											$file_size = unofficial_getDBResult($fres,$k,'file_size');
											$release_time = unofficial_getDBResult($fres,$k,'release_time');
											echo "<font color='red'>update file from previous release = ".$file_id." | ".$file_url." | ".$file_name." | ".$file_size." | ".$release_time."</font><br/>";	
											$frs->frsChangeFile($file_id, $file_url, $file_name, $file_size, date("Y-n-d",$release_time), $rel_id, $package_id);
											$frs->frsSendNotice($group_id, $release_id, $package_id);
											echo "<font color='blue'>send notification of file update</font><br/>";
										}
									}
								}
							}
							$date_list = split("-",$val2->getModified(),3);
							$file_date = mktime(0,0,0,$date_list[1],$date_list[2],$date_list[0]);
							if(strstr($val2->getName(),"php2")) {
								$file_name = $val2->getName()." (for Apache 2.0)";
							}
							else {
								$file_name = $val2->getName()." (for Apache 1.3.x)";
							}
							$frs->frsAddFile($file_date, $file_name, $val2->getPath(), $val2->getSize(), time(), $release_id, $package_id);
							echo "add new file from xml = <b>".$val2->getModified()." | ".$file_name." | ".$val2->getPath()." | ".$val2->getSize()." | ".time()." | ".$release_id." | ".$package_id."</b><br/>";
						}
					}
				}

				// DELETE DOC AND SAMPLE CODE DOWNLOAD FROM DOC AND SAMPLE CODE PROJECT TABS
				$query = "DELETE FROM ".$xoopsDB->prefix("xf_doc_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);

				$query = "DELETE FROM ".$xoopsDB->prefix("xf_sample_data")." "
					      ."WHERE title LIKE '%download%'";
					$xoopsDB->queryF($query);
					
				// SUBMIT NEWS
				$new_id = forum_create_forum($xoopsForge['sysnews'], "Updated ".$val->getName(), 1, 0,'',0);
				$sql = "INSERT INTO ".$xoopsDB->prefix("xf_news_bytes")." "
							      ."(group_id,submitted_by,is_approved,date,forum_id,summary,details) "
										."VALUES ("
										."'".$php."',"
										."'".$xoopsUser->getVar("uid")."',"
										."'1',"
										."'".time()."',"
										."'$new_id',"
										."'Updated ".$val->getName()."',"
										."'".$val->getWhatsNew()."')";
				$xoopsDB->queryF($sql);
				echo "<font color='green'><p/>added news of project update</font><p/>";
			}
		}
	}
	else {
		echo "<font color='red'><h2>Cannot recognize the component/project you are trying to update.</h2></font>";
	}
}
else if ($op == 'add') {
	global $ndk, $ndk_license;

	$unix_name = strtolower($_POST['group_name']);

	if(!account_groupnamevalid($unix_name)) {
		echo $unix_name." is not a valid unix name.";
		exit;
	}
	if($xoopsDB->getRowsNum($xoopsDB->query("SELECT group_id FROM " .
		$xoopsDB->prefix("xf_groups")." WHERE unix_group_name='$unix_name'")) > 0) {
		echo $unix_name." is already taken.";
		exit;
	}

	while (list($key,$val) = each($ndk)) {

		if($unix_name == $val->shortname) {

			$full_name = $val->getName();
			$description = $val->getDescription();
			$purpose = $val->getDescription();
			$support_status = $val->getSupportStatus();
	
			$group = new Group();
			$res = $group->create(
				$xoopsUser,
				$full_name,
				$unix_name,
				$description,
				$license,
				$ndk_license,
				$purpose,
				false,
				false,
				false);
				
			// add support status to trove
			// add project to ndk community
		}
	}
}
else {
	global $ndk;

	while(list($key,$val) = each($ndk)) {

		$result = $xoopsDB->query("SELECT * FROM ".$xoopsDB->prefix("xf_groups")." WHERE unix_group_name='".$val->getShortName()."'");
		$rows = $xoopsDB->getRowsNum($result);

		//update existing projects
		// dirxml, cldap, jldap are special case meta index pages
		if($rows > 0 && !($val->getShortName() == 'dirxml' || $val->getShortName() == 'cldap' || $val->getShortName() == 'jldap')) {
			$group_id = unofficial_getDBResult($result,0,'group_id');
			echo "<form method='POST' action=''><input type='hidden' name='group_id' value='".$group_id."'><input type='hidden' name='group_name' value='".$val->getShortName()."'>";
			echo "<h2>".$val->getName()."</h2><font color='red'>update existing project for Release &nbsp;&nbsp;<input type='text' name='release_name' value='June 2004' size='10'> <input type='text' name='release_date' value='2004-06-09' size='10'></font><input type='hidden' name='op' value='save'> <input type='submit' value='add ".$val->getShortName()."'>";
			echo "<br/>";
			echo "<h4>Downloads <!--input type='button' value='add all files'--></h4>";
			$tempdown = $val->getDownloads();
			while(list($key2,$val2) = each($tempdown)) {
				echo "<table width='100%'><tr><td><table border='1' width='100%' bgcolor='#EEEEEE'>";
				echo "<tr><td>";
				while(list($key3,$val3) = each($val2)) {
					if ($key3 != "path") {
						echo "<b>".$key3."</b>: ".$val3."&nbsp;&nbsp; ";
					}
					else {
						echo "</tr><tr><td><b>".$key3."</b>: ".$val3;
					}
					/*if (strpos($val3,'_al.') || strpos($val3,'_all.')) {
						echo "<input type='hidden' name='all_".$key3."[]' value='".$val3."'>";
					}
					else if (strpos($val3,'_doc.')) {
						echo "<input type='hidden' name='doc_".$key3."[]' value='".$val3."'>";
					}
					else if (strpos($val3,'_sample.')) {
						echo "<input type='hidden' name='sample_".$key3."[]' value='".$val3."'>";
					}
					else {
						echo "<input type='hidden' name='bin_".$key3."[]' value='".$val3."'>";
					}*/
				}
				echo "</td></tr>";
				echo "</table></td><td width='10%'><!--input type='button' value='add file'--></td></tr></table>";
			}
			echo "<br/>";
			echo "<h4>NDK Description = Forge Description  <!--input type='button' value='add'--></h4><textarea cols='110' rows='10'>".$val->getDescription()."</textarea>";
			echo "<br/>";
			echo "<h4>NDK Whats New = Forge Notes on Release Page and News  <!--input type='button' value='add'--></h4><textarea cols='110' rows='10'>".$val->getWhatsNew()."</textarea>";
			echo "<br/>";
			echo "<h4>NDK Whats New Archive = Forge Change Log on Release Page  <!--input type='button' value='add'--></h4><textarea cols='110' rows='10'>".$val->getWhatsNewArchive()."</textarea>";
			echo "<br/>";
			echo "<h4>NDK Dependencies = Forge Dependencies on Release Page  <!--input type='button' value='add'--></h4><textarea cols='110' rows='10'>".$val->getDependencies()."</textarea>";
			echo "<br/>";
			echo "<h4>NDK Support Status = Forge Trove  <!--input type='button' value='add'--></h4>".$val->getSupportStatus();
			echo "<br/>";
			echo "</form>";
		}
		else {
			if($val->getShortName() == "dirxml" || $val->getShortName() == "dirxmllin" || $val->getShortName() == "dirxmlsol" || 
			   $val->getShortName() == "cldap" || $val->getShortName() == "cldaplin" || $val->getShortName() == "cldapsol" || $val->getShortName() == "cldapaix" || $val->getShortName() == "cldaphpux" || 
			   $val->getShortName() == "jldap" || $val->getShortName() == "jldapunx" || $val->getShortName() == "jvm117b" || $val->getShortName() == "jvm13" || $val->getShortName() == "jvm141" || $val->getShortName() == "jvm142" ||
			   $val->getShortName() == "odbc" || $val->getShortName() == "odbcrw" || $val->getShortName() == "php" || $val->getShortName() == "php2"
			   ) {
				echo "<form method='POST' action=''><input type='hidden' name='group_name' value='".$val->getShortName()."'>";
				echo "<h2>".$val->getName()."</h2><font color='red'>Special Case &nbsp;&nbsp;<input type='text' name='release_name' value='June 2004' size='10'> <input type='text' name='release_date' value='2004-06-09' size='10'></font><input type='hidden' name='op' value='save'> <input type='submit' value='add ".$val->getShortName()."'>";
				echo "<br/>";
				echo "<h4>Downloads <!--input type='button' value='add all files'--></h4>";
				$tempdown = $val->getDownloads();
				while(list($key2,$val2) = each($tempdown)) {
					echo "<table width='100%'><tr><td><table border='1' width='100%' bgcolor='#EEEEEE'>";
					echo "<tr><td>";
					while(list($key3,$val3) = each($val2)) {
						if ($key3 != "path") {
							echo "<b>".$key3."</b>: ".$val3."&nbsp;&nbsp; ";
						}
						else {
							echo "</tr><tr><td><b>".$key3."</b>: ".$val3;
						}
					}
					echo "</td></tr>";
					echo "</table></td><td width='10%'><!--input type='button' value='add file'--></td></tr></table>";
				}
				echo "<br/>";
				echo "<h4>NDK Description = Forge Description  <!--input type='button' value='add'--></h4><textarea cols='110' rows='10'>".$val->getDescription()."</textarea>";
				echo "<br/>";
				echo "<h4>NDK Whats New = Forge Notes on Release Page and News  <!--input type='button' value='add'--></h4><textarea cols='110' rows='10'>".$val->getWhatsNew()."</textarea>";
				echo "<br/>";
				echo "<h4>NDK Whats New Archive = Forge Change Log on Release Page  <!--input type='button' value='add'--></h4><textarea cols='110' rows='10'>".$val->getWhatsNewArchive()."</textarea>";
				echo "<br/>";
				echo "<h4>NDK Dependencies = Forge Dependencies on Release Page  <!--input type='button' value='add'--></h4><textarea cols='110' rows='10'>".$val->getDependencies()."</textarea>";
				echo "<br/>";
				echo "<h4>NDK Support Status = Forge Trove  <!--input type='button' value='add'--></h4>".$val->getSupportStatus();
				echo "<br/>";
				echo "</form>";
			}
			else {
				echo "<font color='red'><h2>New Component -> Add ".$val->getShortName()."</h2></font>";
			}
		}
	}
}

	// parse the ndk.xml file
	function parse() {
		$xml_parser = xml_parser_create();
		//xml_set_object($xml_parser, &$this);	
		xml_parser_set_option($xml_parser, XML_OPTION_CASE_FOLDING, true);

		xml_set_element_handler($xml_parser, "start_tag", "end_tag"); 
		xml_set_character_data_handler($xml_parser, "tag_contents"); 

		if (!($xml = fopen("ndk", "r"))) {
		    die("could not open ndk.xml, check to make sure this file exists");
		}    
		while ($data = fread($xml, 4096)) {

			if (!xml_parse($xml_parser, $data, feof($xml))) 
			{
				die(sprintf("XML error: %s at line %d", xml_error_string(xml_get_error_code($xml_parser)), xml_get_current_line_number($xml_parser))); 
			}
		}

		xml_parser_free($xml_parser);
	}

	// START XML TAG
	function start_tag($parser, $name, $attribs) {
	   global $xoopsDB, $ndk, $tempndk, $tempdownload, $val;
	   if ($name == "PROJECT") {   
		   if (is_array($attribs)) {
			   while(list($key,$val) = each($attribs)) {
				   if($key == 'SHORTNAME') {
					    // do not update from the ndk to forge;
					   if ($val == 'ldapcsharp' || $val == 'psqlsdk' || $val == 'apache' || $val == 'mysql' || 
					       $val == 'rsync' || $val == 'postgres' || $val == 'cwpdkc' || $val == 'uddiaix' ||
					       $val == 'uddisol' || $val == 'uddiwin' || $val == 'uddinw' || $val == 'uddilin' ||
					       $val == 'gwsdk' || $val == 'bns-index' || $val == 'dirxml-index' || $val == 'jbrokermq' ||
					       $val == 'jbrokertm' || $val == 'jbrokerweb' || $val == 'jbrokerorb' || $val == 'jbrokercon' ||
					       $val == 'exworkbench' || $val == 'exappserver' || $val == 'exdirector' || $val == 'excomposer' ||
					       $val == 'general' || $val == 'w32sdkc' || $val == 'njsdk' || $val == 'nwsdkc' || $val == 'edirsdk' ||
					       $val == 'tomcat' || $val == 'j2eeclientlin' || $val == 'j2eeclientwin' || $val == 'j2eeclientsol' ||
					       $val == 'gwsdk' || $val == 'gwover' || $val == 'maccli' || $val == '' || $val == 'bns_cmdover' ||
					       $val == 'cwpdk') {
					       // gwodma, extend, ncslibv1
						 $tempndk = new NDK('null');
						 continue;
					   }
					   else {	 
						 $new = NULL;
						 $res = $xoopsDB->query("SELECT group_id "
							      ."FROM ".$xoopsDB->prefix("xf_groups")." "
									  ."WHERE unix_group_name='".$val."'");
	
						 $group_id = unofficial_getDBResult($res,0,'group_id');
						 if ($group_id) {
							 //echo "<h2>Update Project: ".$val." | ".$group_id."</h2>";
							 //$new = 0;
						 }
						 else {
							 //echo "<h2>New Project: ".$val."</h2>";
							 //$new = 1;
						 }
	
						 $tempndk = new NDK($val);
					   }	 	
				   }
			   }
		   }
	   }
           if ($name == "ATTRIBUTE") {
		   if (is_array($attribs)) {
			   while(list($key,$val) = each($attribs)) {
				if($key == 'RECORDTYPE' && $val == 'Name') {
					//echo "<h4>Name</h4>";
					$tempndk->attribute = "Name";
				 }
				 else if($key == 'RECORDTYPE' && $val == 'Description') {
					//echo "<h4>Description</h4>";
					$tempndk->attribute = "Description";
				 }
				 else if($key == 'RECORDTYPE' && $val == 'Whats New') {
					//echo "<h4>Whats New</h4>";
					$tempndk->attribute = "Whats New";
  				 }
				 else if($key == 'RECORDTYPE' && $val == 'Whats New Archive') { 
					//echo "<h4>Whats New Archive</h4>";
					$tempndk->attribute = "Whats New Archive";
  				 }
				 else if($key == 'RECORDTYPE' && $val == 'Dependencies') {
					//echo "<h4>Dependencies</h4>";
					$tempndk->attribute = "Dependencies";
				 }
				 else if($key == 'RECORDTYPE' && $val == 'Support Status') {
					//echo "<h4>Support Status</h4>";
					$tempndk->attribute = "Support Status";
				 }				 
			   }
		   }
           }
           if ($name == "FILE") {
		   if (is_array($attribs)) {
			   //echo "<h4>File</h4>";
			   while(list($key,$val) = each($attribs)) {
				if($key == 'NAME') {
					$tempdownload = new DOWNLOAD($val);
					if(strpos($val,'_al.') || strpos($val,'_all.')) {
						//echo "All : ";
						$tempdownload->type = 'Complete';
						if(strstr($val,".exe") || strstr($val,"dirxml")) {
							$tempdownload->path = "http://developer.novell.com/ndkservlets/ndkdownload?logentry=forgeall&filename=".$val;
						}
						else {
							$tempdownload->path = "http://developer.novell.com/ndkservlets/ndkdownload?logentry=forgeall&filename=zips/".$val;
						}						
					}
					else if(strpos($val,'_doc.')) {
						//echo "Documentation : ";
						$tempdownload->type = 'Documentation';
						if(strstr($val,".exe") || strstr($val,"dirxml")) {
    							$tempdownload->path = "http://developer.novell.com/ndkservlets/ndkdownload?logentry=forgedoc&filename=".$val;
						}
						else {
							$tempdownload->path = "http://developer.novell.com/ndkservlets/ndkdownload?logentry=forgedoc&filename=zips/".$val;
						}						
					}
					else if(strpos($val,'_sample.')) {
						//echo "Sample : ";
						$tempdownload->type = 'Sample';
						if(strstr($val,".exe") || strstr($val,"dirxml")) {
							$tempdownload->path = "http://developer.novell.com/ndkservlets/ndkdownload?logentry=forgesample&filename=".$val;
						}
						else {
							$tempdownload->path = "http://developer.novell.com/ndkservlets/ndkdownload?logentry=forgesample&filename=zips/".$val;
						}
					}
					else {
						//echo "Binary : ";
						$tempdownload->type = 'Binary';
						if(strstr($val,".exe") || strstr($val,"dirxml")) {
							$tempdownload->path = "http://developer.novell.com/ndkservlets/ndkdownload?logentry=forgesoftware&filename=".$val;
						}
						else {
							$tempdownload->path = "http://developer.novell.com/ndkservlets/ndkdownload?logentry=forgesoftware&filename=zips/".$val;
						}
					}
					//echo $val."<br/>"; 
				 }
				 else if($key == 'SIZE') {
					//echo "Size: ".$val."<br/>";
					$tempdownload->size = $val;
				 }
				 else if($key == 'MODIFIED') {
					//echo "Modified: ".$val."<br/>";
					$tempdownload->modified = date("Y-n-d",strtotime($val));
				 }
				 else if($key == 'POSSIBLEUPDATE') {
					//echo "Update: ".$val."<br/>";
					$tempdownload->update = $val;
				 }				 
				 /*else if($key == 'PATH') {
					//echo "Path: ".$val."<br/>";
					$tempdownload->path = $val;
				 }*/
			   }
		     }
               }
	}

	// END XML TAG
	function end_tag($parser, $name) {
	   global $ndk, $tempndk, $tempdownload;
	   if ($name == "PROJECT") { 
		   if ($tempndk->getShortName() != 'null') {
			   $ndk[] = $tempndk;
		   }
	   }
	   if ($name == "FILE") {
		   $tempndk->setDownloads($tempdownload);
	   }
	}

	// HAVE XML CONTENT
	function tag_contents($parser, $data) {
	   global $tempndk;
	   // echo $data;
	   if ($tempndk->attribute == "Name") { $tempndk->setName($data); }
	   elseif ($tempndk->attribute == "Description") { $tempndk->setDescription($data); }
	   elseif ($tempndk->attribute == "Dependencies") { $tempndk->setDependencies($data); }
	   elseif ($tempndk->attribute == "Whats New") { $tempndk->setWhatsNew($data); }
	   elseif ($tempndk->attribute == "Whats New Archive") { $tempndk->setWhatsNewArchive($data); }
	   elseif ($tempndk->attribute == "Support Status") { $tempndk->setSupportStatus($data); }
        }

?>
