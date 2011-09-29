#!/usr/bin/perl

use Mysql;
use Net::FTP;
use Frontier::Client;
use Sys::Syslog qw/:DEFAULT setlogsock/;

##############################################
# This script should be executed by cron as  #
# often as desired to move built packages to #
# the given FTP location.                    #
##############################################
#find bb -type f | xargs grep "die"
#CONFIGURE: These variables need to be set to the correct values.
$BB_SHARE = '/build/share/mount/'; # Include trailing slash.

$FTP_HOST = 'ftp.host.name';
$FTP_PREFIX = '/ftp/pub/'; # Include trailing slash.
$FTP_USER = 'ftpuser'; 
$FTP_PASS = 'ftppassword';

$MYSQL_HOST = 'mysql.host.name';
$MYSQL_USER = 'mysqluser';
$MYSQL_PASS = 'mysqlpassword';
$MYSQL_DB = 'forgedatabase';

$BUILD_HOST = 'build.master.host.name:8080';
$BUILD_AUTH_HOST = 'build.master.host.name:8090';
$BUILD_USERNAME = 'builduser';
$BUILD_PASSWORD = 'buildpassword';
$BUILD_RED_CARPET_HOST = 'https://red.carpet.host.name';
$BUILD_ACTIVATION_EMAIL = 'activation@email.tld';
$BUILD_ACTIVATION_KEY = 'activation-key';
$BUILD_CVS_USER = 'cvsuser';

#these variables do not need to be changed
$MYSQL_BUILD_TABLE = 'xoops_xf_webservice_build';
$MYSQL_BUILD_MODULE_TABLE = 'xoops_xf_webservice_build_module';
$BUILD_PATH = '/RPC2';

##############################################
setlogsock('unix');
openlog($0, 'cons,pid', 'user') or die $!;
local $db = Mysql->connect($MYSQL_HOST, $MYSQL_DB, $MYSQL_USER, $MYSQL_PASS) || syslog('warning', "Could not connect to MySQL");

#process active jobs to finalize them if they have finished
do_active_jobs();

#process pending jobs and try to schedual them to build
do_pending_jobs();

#clean up the build node
opendir(BUILDS, $BB_SHARE);
@build_dirs = readdir BUILDS;
foreach $build_dir (@build_dirs){
	next if $build_dir =~ /^\.{1,2}$/;
	$filedate = -M $BB_SHARE.$build_dir;
	if($filedate>=1){
		cleanup($BB_SHARE.$build_dir);
	}
}

closelog();

undef $db;	

sleep 20;
exec "./build_cron.sh" or syslog('err', "$0: Cannot exec myself");

sub do_active_jobs {
	my $result = $db->query("SELECT job_id, unix_group_name, target, status FROM $MYSQL_BUILD_TABLE WHERE status = 'active'");
	my $ftp = undef;
	while(my ($id, $project, $target, $status) = $result->fetchrow){
		my $build_dir = $BB_SHARE.$id.'/';
		next unless my $size = -s $build_dir.'logs/.done';
	
		unless(defined $ftp){
		    $ftp = Net::FTP->new($FTP_HOST)  or syslog('warning', "Cannot connect to $FTP_HOST: $@", $id);
		    $ftp->login($FTP_USER,$FTP_PASS) or syslog('warning', "Cannot login: ".$ftp->message, $id);
		    $ftp->binary();
		}
		#create info and add to it as we go
		$info  = "build: $id\n";
		$info .= "finished: ".localtime(time)."\n";
		$info .= "\n[File List]\n";
	
		#create the need directory structure for the given project/target
		$ftp->mkdir($FTP_PREFIX.$project.'/autobuild/'.$target, 1) or build_log('warning',"Cannot create directory $project/autobuild/$target: ".$ftp->message, $id);
	
		#tar the log files and copy them to the ftp host
		my $tar_file = 'build-logs.tar.gz';
		$output = `tar -czf $build_dir$tar_file --directory=$build_dir logs 2>&1`;
		build_log('warning', "Could not tar log files: $output", $id) if $output;
	
	
		$ftp->put($build_dir.$tar_file, $FTP_PREFIX.$project.'/autobuild/'.$target.'/'.$tar_file) or build_log('warning', "Cannot put file $tar_file: ".$ftp->message, $id);
	    createforgeentries($project, $tar_file, $id, $target);
		$info .= "$tar_file\n";
	
		#if the build was successful copy the rpms to the ftp host
		if(length('suceeded\n')==$size){
			opendir(RPM, $build_dir);
			@rpms = grep /.*\.rpm$/, readdir RPM;
			foreach $rpm (@rpms){
				$ftp->put($build_dir.$rpm,$FTP_PREFIX.$project.'/autobuild/'.$target.'/'.$rpm) or build_log('warning', "Cannot put file $rpm: ".$ftp->message, $id);
				$db->query("UPDATE $MYSQL_BUILD_TABLE SET end_time=".time().", status='succeeded' WHERE job_id=$id");
				createforgeentries($project, $rpm, $id, $target);
				$info .= "$rpm\n";
			}
		}else{
			$db->query("UPDATE $MYSQL_BUILD_TABLE SET end_time=".time().", status='failed' WHERE job_id=$id");
		}
		#copy the info as a file to the ftp host
		open INFO, ">$build_dir"."build-info.txt";
		print INFO $info;
		print INFO "build-info.txt";
		close INFO;
		$ftp->put($build_dir.'build-info.txt',$FTP_PREFIX.$project.'/autobuild/'.$target.'/build-info.txt') or build_log('warning', "Cannot put file build-info.txt: ".$ftp->message, $id);
		createforgeentries($project, 'build-info.txt', $id, $target);
	}
	undef $result;
}

sub do_pending_jobs {
	my $result = $db->query("SELECT id, unix_group_name, cvs_host, cvs_modules, target, status FROM $MYSQL_BUILD_TABLE WHERE status = 'pending'  ORDER BY id ASC");

	while(my ($id, $project, $cvshost, $cvsmodules, $target, $status) = $result->fetchrow){
		my $authrpc = Frontier::Client->new(url => $BUILD_AUTH_HOST.$BUILD_PATH);
		my $key = $authrpc->call("authenticate",$BUILD_USERNAME,$BUILD_PASSWORD);
		
		
		my $buildrpc = Frontier::Client->new(url => $BUILD_HOST.$BUILD_PATH);
		
		@mods = split /,/, $cvsmodules;
		@modules = ();
		foreach (@mods){
			push @modules ,{'name'=>$_.'-build',
									'cvsroot'=>$BUILD_CVS_USER.'@'.$cvshost.':/cvsroot/'.$project,
									'cvsmodule'=>$_};
		}
		my $extra_args = {	'target'=>$target,
										'modules'=>\@modules,
										'rcd'=>{	'var'=> {	'require-signatures'=>'false',
																	'enable-premium'=>'true',
																	'require-verified-certificates'=>'false',
																	'host'=>$BUILD_RED_CARPET_HOST},
																	'activate'=>{$BUILD_ACTIVATION_EMAIL => [$BUILD_ACTIVATION_KEY]},
																	'subscribe'=> [	{'name'=>'[[target]]','update'=>'true'},
																							{'name'=>'ximian-release','update'=>'true'},
																							{'name'=>'mono','update'=>'false'}  ]  },
										'env'=>{ 'var' => {	'CVS_RSH' => '/usr/bin/bb_ssh',
																	'BB_REPODIR' => '/nfs/release/source_repository',
																	'XIMIANCVS_USER' => 'distro',
																	'GNOMECVS_USER' => 'gnomeweb',
																	'SGICVS_USER' => 'cvs',
																	'LANG' => 'C',
																	'HOME' => '/home/distro' }  }  };
										
		
		
		eval{ $job_id = $buildrpc->call("build_simple", $BUILD_USERNAME, $key, $extra_args) };
		if ($@){
			last if $@ =~ /fault code 4/;
			$@ =~ s/[`|']/"/gs;
			$db->query("UPDATE $MYSQL_BUILD_TABLE SET end_time=".time().", status='failed', error = '$@' WHERE id = $id");
		}else{
			$db->query("UPDATE $MYSQL_BUILD_TABLE SET job_id=$job_id,  status='active' WHERE id = $id");
		}
	}
	undef $result;
}

sub cleanup {
    my $dir = shift;
	local *DIR;

	opendir DIR, $dir or die "opendir $dir: $!";
	my $found = 0;
	while ($_ = readdir DIR) {
	        next if /^\.{1,2}$/;
	        my $path = "$dir/$_";
		unlink $path if -f $path;
		cleanup($path) if -d $path;
	}
	closedir DIR;
	rmdir $dir or syslog('warning', "Cannot rmdir $dir - $!");
}

sub createforgeentries{
	my $project = shift;
	my $rpm = shift;
	my $build_id = shift;
	my $target = shift;

	my $result;

	my $group_id;
	my $package_id;
	my $release_id;
	my $file_id;

	my $time = time;
	my $file_size = -s $BB_SHARE.$build_id.'/'.$rpm;
	unless($file_size) {$file_size=0};

	$result=$db->query("SELECT group_id FROM xoops_xf_groups WHERE unix_group_name='$project'");
	($group_id) = $result->fetchrow;
	
	$result=$db->query("SELECT package_id FROM xoops_xf_frs_package WHERE group_id=$group_id and name='autobuild'");
	unless( ($package_id) = $result->fetchrow){
		$result=$db->query("INSERT into xoops_xf_frs_package (group_id, name, status_id) VALUES ($group_id, 'autobuild', 1)");
		$package_id = $result->insertid;
	}
        
	$result=$db->query("SELECT release_id FROM xoops_xf_frs_release WHERE package_id=$package_id and name='$target'");
        unless( ($release_id) = $result->fetchrow){
		$result=$db->query("INSERT into xoops_xf_frs_release (package_id, name, status_id, release_date) VALUES ($package_id, '$target', 1, $time)");
                $release_id = $result->insertid;
        }

	$result=$db->query("SELECT file_id FROM xoops_xf_frs_file WHERE release_id=$release_id and filename='$rpm'");
	unless( ($file_id) = $result->fetchrow){
		$result=$db->query("INSERT into xoops_xf_frs_file (filename, file_url, release_id, release_time, file_size, post_date) VALUES ('$rpm', '$rpm', $release_id, $time, $file_size, $time)");
		$file_id = $result->insertid;
		$result=$db->query("INSERT into xoops_xf_frs_dlstats_file_agg (file_id) VALUES ($file_id)");
	}else{
		$result=$db->query("UPDATE xoops_xf_frs_file SET release_time=$time, file_size=$file_size, post_date=$time WHERE file_id=$file_id");
	}
	
	if($rpm =~ /.*\.rpm$/ && $rpm !~ /.*src\.rpm$/){
		$result=$db->query("SELECT count(file_id) FROM xoops_xf_frs_target WHERE file_id=$file_id AND target='$target'");
		my($num_rows) = $result->fetchrow;
		if(0 == $num_rows){
			$result=$db->query("INSERT into xoops_xf_frs_target (file_id, target) VALUES ($file_id, '$target')");
		}
	}
	undef $result;
}

sub build_log{
	my $level = shift;
	my $message = shift;
	my $job_id = shift;
	
	syslog($level, $message);
	$db->query("UPDATE $MYSQL_BUILD_TABLE SET error='$message' WHERE job_id=$job_id");
}
