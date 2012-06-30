<?php
//$_SESSION = array();
//foreach(array_keys($_COOKIE) as $name) setcookie($name, '', 0, '/');
//r($_COOKIE);
//r($_SESSION);

if (!isset($_SESSION['data'])) {
	$_SESSION['data'] = readData();
}
$expiration = time()+86400; // 24 hours

function readData($cookieName = 'db') {
	if (isset($_COOKIE[$cookieName]))
		return unserialize($_COOKIE[$cookieName]);
	return unserialize(file_get_contents(dirname(__FILE__).'/db'));
}
function writeData($data) {
	global $expiration;
	$data = serialize($data);
	setcookie('db', $data, $expiration, '/'); // expires in 48 hours
	$_COOKIE['db'] = $data;
	//return fwrite(fopen(dataFileName(), 'w'), serialize($data));
}
function backupData() {
	global $expiration;
	setcookie('db_backup_'.time(), $_COOKIE['db'], $expiration, '/'); // expires in 48 hours
	$_COOKIE['db_backup_'.time()] = $_COOKIE;
	return true;
	//return fwrite(fopen(dataFileName().'.backup.'.time(), 'w'), file_get_contents(dataFileName()));
}

function r($data){
	header('Content-type: text/plain');
	die(print_r($data));
}

function dumpData(){
	r($_SESSION['data']);
}

// restore broken serialization
function __unserialize($sObject) {
    $__ret =preg_replace('!s:(\d+):"(.*?)";!e', "'s:'.strlen('$2').':\"$2\";'", $sObject );
    return unserialize($__ret);   
}
