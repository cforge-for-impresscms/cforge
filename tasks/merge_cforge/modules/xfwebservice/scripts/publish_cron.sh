#!/usr/bin/perl

use Mysql;
use Net::FTP;
use Sys::Syslog qw/:DEFAULT setlogsock/;

##############################################
# This script should be executed by cron as  #
# often as desired to publish files to a rce #
# server                                     #
##############################################

#CONFIGURE: These variables need to be set to the correct values.
$RCE_USER = 'user@company.com';
$RCE_PASS = 'pass';

$FTP_HOST = 'ftp.host.com';
$FTP_PREFIX = '/ftp/pub/'; # Include trailing slash.
$FTP_USER = 'shagreel';
$FTP_PASS = 'password';

$MYSQL_HOST = 'mysql.host.com';
$MYSQL_USER = 'user';
$MYSQL_PASS = 'pass';
$MYSQL_DB = 'db';

#this variable does not need to be changed#
$MYSQL_PUBLISH_TABLE = 'xoops_xf_webservice_publish';

##############################################
setlogsock('unix');
openlog($0, 'cons,pid', 'user') or die $!;

my $db = Mysql->connect($MYSQL_HOST, $MYSQL_DB, $MYSQL_USER, $MYSQL_PASS) || die "Could not connect to MySQL";
my $result = $db->query("SELECT id, unix_group_name, file_id, status FROM $MYSQL_PUBLISH_TABLE WHERE status = 'active'");
while(my ($id, $project, $file_id, $status) = $result->fetchrow){
	
	$sql = "SELECT g.group_name, p.name, r.name, f.filename"
		." FROM xoops_xf_groups AS g"
		.",xoops_xf_frs_package AS p"
		.",xoops_xf_frs_release AS r"
		.",xoops_xf_frs_file AS f"
		." WHERE g.unix_group_name='$project'"
		." AND g.group_id=p.group_id"
		." AND p.package_id=r.package_id"
		." AND r.release_id=f.release_id"
		." AND f.file_id=$file_id";
	$r = $db->query($sql);
	my($project_title, $package_name, $release_name, $file_name) = $r->fetchrow;
#print $project_title," ",$package_name," ",$release_name," ",$file_name,"\n";
	
	$sql = "SELECT target FROM xoops_xf_frs_target WHERE file_id=$file_id";
	$r = $db->query($sql);
	my @targets = $r->fetchcol(0);
	$targets = join ",", @targets;

	$ftp = Net::FTP->new($FTP_HOST) or syslog('warning', "Cannot connect to $FTP_HOST: $@");
	$ftp->login($FTP_USER,$FTP_PASS) or syslog('warning', "Cannot login: ".$ftp->message);
	$ftp->binary();
	$ftp->get($FTP_PREFIX.$project.'/'.$package_name.'/'.$release_name.'/'.$file_name,'/tmp/'.$file_name) or syslog('warning', "Cannot get $file_name: ".$ftp->message);

	$_ = `rcman -U $RCE_USER -P $RCE_PASS group-add $project-group`;
	if(0 == $?>>8){
		$_ = `rcman -U $RCE_USER -P $RCE_PASS channel-add "$project_title" $project`;
				unless(0 == $?>>8){
					$db->query("UPDATE $MYSQL_PUBLISH_TABLE set status='failed', error='$_' WHERE id=$id");
					syslog('warning',$_);
					next;
				}
		
		$_ = `rcman -U $RCE_USER -P $RCE_PASS group-addchannel $project-group $project`;
				unless(0 == $?>>8){
                    $db->query("UPDATE $MYSQL_PUBLISH_TABLE set status='failed', error='$_' WHERE id=$id");
					syslog('warning',$_);
                    next;
                }
		
		$_ = `rcman -U $RCE_USER -P $RCE_PASS act-add --key="$project-key"`;
                unless(0 == $?>>8){
                    $db->query("UPDATE $MYSQL_PUBLISH_TABLE set status='failed', error='$_' WHERE id=$id");
					syslog('warning',$_);
                    next;
                }
		
		$_ = `rcman -U $RCE_USER -P $RCE_PASS act-addgroup $project-key $project-group`;
                unless(0 == $?>>8){
					$db->query("UPDATE $MYSQL_PUBLISH_TABLE set status='failed', error='$_' WHERE id=$id");
					syslog('warning',$_);
					next;
                }
	}else{
		syslog('info',$_);	
	}
	$_ = `rcman -U $RCE_USER -P $RCE_PASS channel-addpkg --targets=$targets $project /tmp/$file_name`;
	if(0 == $?>>8){
		$db->query("UPDATE $MYSQL_PUBLISH_TABLE set status='succeeded' WHERE id=$id");
	}else{
		$db->query("UPDATE $MYSQL_PUBLISH_TABLE set status='failed', error='$_' WHERE id=$id");
		syslog('warning',$_);
	}
	unlink("/tmp/$file_name") or syslog('warning', "Cannot unlink /tmp/$file_name");
	undef $r;
}

undef $result;
undef $db;
closelog();

sleep 20;
exec "./publish_cron.sh" or syslog('err', "$0: Cannot exec myself");