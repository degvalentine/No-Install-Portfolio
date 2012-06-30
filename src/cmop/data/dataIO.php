<?php
// some kind of automatic file cyclicing can be built in here

if (!isset($_SESSION['data'])) {
	$_SESSION['data'] = readData();
}

function dataFileName($file = null) {
	return dirname(__FILE__).'/'.($file?$file:'db');
}
function readData($file = null) {
	return unserialize(file_get_contents(dataFileName($file)));
}
function writeData($data) {
	return fwrite(fopen(dataFileName(), 'w'), serialize($data));
}
function backupData() {
	return fwrite(fopen(dataFileName().'.backup.'.time(), 'w'), file_get_contents(dataFileName()));
}

function r($data){
	header('Content-type: text/plain');
	die(print_r($data));
}

function dumpData(){
	r($_SESSION['data']);
}