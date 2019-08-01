<?php

/**
* 
   cd /root && php cron.nginx.banned.php
* 
**/

$unzip_path_array = Array('/bin/unzip', '/sbin/unzip', '/usr/bin/unzip', '/usr/sbin/unzip');
foreach($unzip_path_array as $unzip_path){
	if(file_exists($unzip_path)){
		$unzip_exe = $unzip_path;
	}
}
if(empty($unzip_exe)){
	shell_exec("sudo apt-get -y install zip unzip >log_install 2>log_install &");
	shell_exec("yum -y install zip unzip >log_install 2>log_install &");

}

############################# some value here ################################
$folder_banned_nginx = "/etc/nginx/banned.d/";
if(!file_exists($folder_banned_nginx)){
    mkdir($folder_banned_nginx, 0777);
}
$extension = ".zip";
$old_file = '/root/old_file_nginx_banned---v4'.$extension;
$temporary_unzip_folder = "/tmp/temporary_unzip_folder/";
$file_check_first = "/tmp/file_check_first_nginx_banned".$extension;
################################
shell_exec("rm -rf $temporary_unzip_folder");
mkdir($temporary_unzip_folder, 0777);
################################
$file_url = "https://github.com/rhmkds/nginx_ban_hosting_network/archive/master".$extension; ## change this with your cloned one
###############################################################################


### ### ### check new contents
$new_contents = shell_exec("curl -s -L --max-time 60 '$file_url' --insecure --compressed");
file_put_contents($file_check_first, $new_contents);
$check_result = shell_exec("cd $temporary_unzip_folder && $unzip_exe -o ".$file_check_first);
echo '$check_result: '.$check_result; echo PHP_EOL;

if(strpos($check_result, "/")!==false){
    echo '$compressed is valid: '.$file_check_first; echo PHP_EOL;
}else{
	echo '$compressed is NOT valid: '.$file_check_first; echo PHP_EOL;
	goto skipzip;
}
##############################



if(
	( md5_file($old_file) !== md5_file($file_check_first))
){
    $words = basename(__FILE__)." > I will deploy now!"; ## use __FILE__ name in case we have multiple deploy cron
	echo "$words"; echo PHP_EOL;
	file_put_contents("$words", "ok"); ## for easy checks on /root folder

    file_put_contents($old_file, $new_contents);
    $folders = glob($temporary_unzip_folder."/*");
    foreach($folders as $folder){
	if(strpos($folder, "-")!==false){
	    $folder_I_got = $folder;
	    break;
	}
    }
    echo 'folder_I_got: '.$folder_I_got . PHP_EOL;
    ## exit;
    $files = glob($folder_I_got."/*");
    foreach($files as $file){
	if(is_dir($file)){
	    continue;
	}
	echo 'copying file: '.$file . PHP_EOL;
	$target_file = $folder_banned_nginx."/".basename($file);
	echo 'target_file: '.$target_file . PHP_EOL;

	unlink( $target_file);
	copy($file, $target_file);
    }

    $my_allowed_conf = "allow 8.8.8.8; # your beloved ip".PHP_EOL.
    "";
    file_put_contents($folder_banned_nginx . "/my_allowed.conf", $my_allowed_conf);



    chmod($folder_banned_nginx, 0777);
    $nginx_t_file = "/tmp/nginx_t_file";
    shell_exec("sudo nginx -t >$nginx_t_file 2>$nginx_t_file");
    $nginx_t = file_get_contents($nginx_t_file);
    echo 'nginx_t: '.$nginx_t . PHP_EOL;

    if(strpos( strtolower($nginx_t), "success")!==false){
	echo shell_exec('sudo service nginx restart'); echo PHP_EOL;
	$words = 'reload_nginx_because_deploy=nginx_banned';
	file_put_contents($words, time()); ## for easy checks on /root folder
	echo($words); echo PHP_EOL;
    }else{
	$words = 'NOT reload_nginx nginx_banned error at nginx!';
	file_put_contents($words, time()); ## for easy checks on /root folder
	echo($words); echo PHP_EOL;
	shell_exec("rm -rf $folder_banned_nginx");
    }

	


}else{
    $words = basename(__FILE__)." > Not deploying now!"; 
    echo $words; echo PHP_EOL;
	file_put_contents("$words", "ok"); ## for easy checks on /root folder

}
######################################################
skipzip:;
