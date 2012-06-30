<?php require('lib/bootstrap.php'); ?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?php echo $_SESSION['data']->settings->title; ?></title>
	<meta name="description" content="<?php echo $_SESSION['data']->settings->about; ?>" />
	
	<script src="lib/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
	<script src="lib/jquery/jquery.media.js" type="text/javascript"></script>
	<!-- <script src="lib/swfobject.js" type="text/javascript"></script> -->
	<script src="lib/home.js" type="text/javascript"></script>
	<link href="styles/home.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript">
		var settings = <?php echo json_encode($_SESSION['data']->settings); ?>;
		var groups = <?php echo json_encode($_SESSION['data']->groups); ?>;
		var webBase = "<?php echo $webBase; ?>";
		var gid = <?php echo $_SESSION['data']->nextGid; ?>;
	</script>

	<?php if (!empty($_SESSION['authenticated'])): ?>
	<script src="lib/admin.js" type="text/javascript"></script>
	<script src="lib/jquery/jquery-ui-1.8.sortable.min.js" type="text/javascript"></script>
	<script src="lib/jquery/jquery.ajaxupload.js" type="text/javascript"></script>
	<script src="lib/jquery/jquery.jeegoocontext.min.js" type="text/javascript"></script>
	<script src="lib/jquery/jquery.json-2.2.min.js" type="text/javascript"></script>
	<script src="lib/jquery/jwysiwyg/jquery.wysiwyg.js" type="text/javascript"></script>
	<script type="text/javascript">
		var backups = <?php echo json_encode($backups); ?>;
		var logo = <?php echo json_encode($_SESSION['data']->logo); ?>;
	</script>
	<link href="styles/admin.css" rel="stylesheet" type="text/css" />
	<link href="lib/jquery/jwysiwyg/jquery.wysiwyg.css" rel="stylesheet" type="text/css" />
	<?php endif; ?>
	
	<?php if (!empty($_SESSION['data']->settings->gaAccount)) include "inc/tracking.php"; ?>
</head>

<body class="<?php echo $_SESSION['data']->settings->color; ?>">
	<span style="position:absolute;top:-1000px">&nbsp;</span>
	<?php include "inc/layout-{$_SESSION['data']->settings->layout}.php"; ?>
	<?php if (!empty($_SESSION['authenticated'])) include('inc/admin-form.php'); ?>
</body>
</html>
