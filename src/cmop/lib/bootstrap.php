<?php

session_name('designbymann');
session_start();
ini_set('post_max_size', 100000);
ini_set('upload_max_filesize', 100000);
			
require_once('data/dataIO.php');

$webBase = substr($_SERVER['PHP_SELF'], 0, -10);
$fileBase = str_replace(strrchr(dirname(__FILE__), DIRECTORY_SEPARATOR), '', dirname(__FILE__));

$dataDir = opendir($fileBase.'/data');
$files = $backups = array();
while (false !== ($filename = readdir($dataDir)))
	$files[] = $filename;
for ($n = count($files)-1; count($backups) < 10 && $n >= 0; $n--) {
	if (substr($files[$n],0,9) != 'db.backup') continue;
	$timestamp = (int)array_pop(explode('.', $files[$n]));
	$backups[] = array('filename' => $files[$n], 'date' => date('d M y g:i a', $timestamp));
}

if (!empty($_GET['q'])) {
	switch ($_GET['q']) {
		case 'login/reset':
			// make sure reset is allowed (no password or logged in already)
			$password = readPassword();
			if (empty($_SESSION['authenticated']) && $password)
				die();
			
			// confirm passwords match
			if (empty($_POST['pass']) || empty($_POST['pass_check']) || 
				strcmp($_POST['pass'], $_POST['pass_check']) != 0)
				display('<p class="error">passwords did not match</p>'.resetPasswordForm());
			
			resetPassword(htmlentities($_POST['pass']));
			
			// break omitted delibrately
			
		case 'login':
			$_SESSION = array();
			$password = readPassword();
			if ($password === FALSE)
				die('login error');
			if (!$password)
				display(resetPasswordForm());
			
			// authenticate 
			if (isset($_POST['pass'])) {
				if ($_POST['pass'] == $password) {
					$_SESSION['authenticated'] = true;
					die(header("Location: $webBase/"));
				} else {
					display('<p class="error">incorrect password</p>'.loginForm());
				}
			}
			
			display(loginForm());
			break;
			
		case 'admin/upload':
			if (!$_SESSION['authenticated']) die();
			if (move_uploaded_file($_FILES['newImage']['tmp_name'], dirname(__FILE__).'/../content/'.$_FILES['newImage']['name']))
				die("success");
			die("error");
			break;
			
		case 'admin/cmd':
			if (!$_SESSION['authenticated']) die();
			switch($_POST['cmd']) {
				case 'save':
					backupData();
					$data = json_decode(decode($_POST['data']));
					$data->settings = $_SESSION['data']->settings;
					writeData($data);
					// break omitted delibrately
					
				case 'refresh':
					$_SESSION['data'] = readData();
					die(header("Location: $webBase/"));
					break;
				
				case 'restore':
					if ($_POST['backup'] == '') {
						$_SESSION['data']->groups = array();
						$_SESSION['data']->info = array();
					}
					else {
						$_SESSION['data'] = readData($_POST['backup']);
					}
					die(header("Location: $webBase/"));
					break;
					
				case 'sign out':
					// break omitted delibrately
					
				default: 
					$_SESSION = array();
					die(header("Location: $webBase/"));
					
			}
			break;
		
		case 'admin/settings':
			$settings->title = stripslashes($_POST['title']);
			$settings->about = stripslashes($_POST['about']);
			$settings->layout = $_POST['layout'];
			$settings->menu = $_POST['menu'];
			$settings->effect = $_POST['effect'];
			$settings->color = $_POST['color'];
			$settings->intro = $_POST['intro'];
			$settings->gaAccount = $_POST['gaAccount'];
			$_SESSION['data']->settings = $settings;
			die(header("Location: $webBase/"));
			break;
			
			
		default: die();
		
	}
}

function display($content) {
	$page = '<style>label,.error{font-family:Arial,sans-serif;}label{color:silver}.error{color:red}</style>';
	die($page.$content);
}

function loginForm() {
	global $webBase;
	$form = 
		'<form method="post" action="'.$webBase.'/login">'.
			'<input id="auth" type="password" name="pass" />'.
		'</form>'.
		'<script>document.getElementById("auth").focus()</script>';
	return $form;
}

function resetPasswordForm() {
	global $webBase;
	$form = 
		'<form method="post" action="'.$webBase.'/login/reset">'.
			'<input id="pass" type="password" name="pass" />'.
			'<label for="pass">Enter a new password.</label><br />'.
			'<input id="pass_check" type="password" name="pass_check" />'.
			'<label for="pass_check">Confirm new password.</label><br />'.
			'<input type="submit" value="Set New Password" />'.
		'</form>'.
		'<script>document.getElementById("auth").focus()</script>';
	return $form;
}

function resetPassword($password) {
	return file_put_contents('.admin_password', $password);
}

function readPassword() {
	return file_get_contents('.admin_password');
}

function decode($str) {
	$find = array(
		"%A1" => "&iexcl;",
		"%A2" => "&cent;",
		"%A3" => "&pound;",
		"%A4" => "&curren;",
		"%A5" => "&yen;",
		"%A6" => "&brvbar;",
		"%A7" => "&sect;",
		"%A8" => "&uml;",
		"%A9" => "&copy;",
		"%AA" => "&ordf;",
		"%AB" => "&laquo;",
		"%AC" => "&not;",
		"%AD" => "&shy;",
		"%AE" => "&reg;",
		"%AF" => "&macr;",
		"%B0" => "&deg;",
		"%B1" => "&plusmn;",
		"%B2" => "&sup2;",
		"%B3" => "&sup3;",
		"%B4" => "&acute;",
		"%B5" => "&micro;",
		"%B6" => "&para;",
		"%B7" => "&middot;",
		"%B8" => "&cedil;",
		"%B9" => "&sup1;",
		"%BA" => "&ordm;",
		"%BB" => "&raquo;",
		"%BC" => "&frac14;",
		"%BD" => "&frac12;",
		"%BE" => "&frac34;",
		"%BF" => "&iquest;",
		"%C0" => "&Agrave;",
		"%C1" => "&Aacute;",
		"%C2" => "&Acirc;",
		"%C3" => "&Atilde;",
		"%C4" => "&Auml;",
		"%C5" => "&Aring;",
		"%C6" => "&AElig;",
		"%C7" => "&Ccedil;",
		"%C8" => "&Egrave;",
		"%C9" => "&Eacute;",
		"%CA" => "&Ecirc;",
		"%CB" => "&Euml;",
		"%CC" => "&Igrave;",
		"%CD" => "&Iacute;",
		"%CE" => "&Icirc;",
		"%CF" => "&Iuml;",
		"%D0" => "&ETH;",
		"%D1" => "&Ntilde;",
		"%D2" => "&Ograve;",
		"%D3" => "&Oacute;",
		"%D4" => "&Ocirc;",
		"%D5" => "&Otilde;",
		"%D6" => "&Ouml;",
		"%D7" => "&times;",
		"%D8" => "&Oslash;",
		"%D9" => "&Ugrave;",
		"%DA" => "&Uacute;",
		"%DB" => "&Ucirc;",
		"%DC" => "&Uuml;",
		"%DD" => "&Yacute;",
		"%DE" => "&THORN;",
		"%DF" => "&szlig;",
		"%E0" => "&agrave;",
		"%E1" => "&aacute;",
		"%E2" => "&acirc;",
		"%E3" => "&atilde;",
		"%E4" => "&auml;",
		"%E5" => "&aring;",
		"%E6" => "&aelig;",
		"%E7" => "&ccedil;",
		"%E8" => "&egrave;",
		"%E9" => "&eacute;",
		"%EA" => "&ecirc;",
		"%EB" => "&euml;",
		"%EC" => "&igrave;",
		"%ED" => "&iacute;",
		"%EE" => "&icirc;",
		"%EF" => "&iuml;",
		"%F0" => "&eth;",
		"%F1" => "&ntilde;",
		"%F2" => "&ograve;",
		"%F3" => "&oacute;",
		"%F4" => "&ocirc;",
		"%F5" => "&otilde;",
		"%F6" => "&ouml;",
		"%F7" => "&divide;",
		"%F8" => "&oslash;",
		"%F9" => "&ugrave;",
		"%FA" => "&uacute;",
		"%FB" => "&ucirc;",
		"%FC" => "&uuml;",
		"%FD" => "&yacute;",
		"%FE" => "&thorn;",
		"%FF" => "&yuml;",
	);
	return urldecode(str_replace(array_keys($find), array_values($find), $str));
}
