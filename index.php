<!-- @todo HTML5 and validation -->
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	
	<title>C M O P</title>
	
	<!-- @todo 
	<meta name="description" content="Your portfolio - online now. No hassle. Easy, powerful, etc..." />
	-->
	
	<script src="lib/jquery/jquery-1.4.2.min.js" type="text/javascript"></script>
	<script src="lib/swfobject.js" type="text/javascript"></script>
	<script type="text/javascript">
		// embed video
		$(function(){
			var flashvars = {
				'file':'../../bin/video.flv',
				'image':'./bin/image.png',
				'plugins':'./lib/jwplayer-5.2/plugins/audiodescription/v4/audiodescription.swf',
				'audiodescription.file':'./bin/audio2.mp3',
				'dock':'false',
			};
			var params = {'allowfullscreen':'true'};
			swfobject.embedSWF('lib/jwplayer-5.2/player.swf', 'video', '763', '615', '7', null, flashvars, params);
		});
		
		// Google Analytics tracking
		var _gaq = _gaq || [];
		_gaq.push(['_setAccount', 'UA-']);
		_gaq.push(['_trackPageview']);
		(function() {
			var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
			ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
			var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		})();
	</script>
	<style type="text/css">
		body {font-family:Verdana, Arial, sans-serif; padding:15px;}
		h1 {color:#939598; font-size:16px;}
		h2 {font-size:22px; position:absolute; left:250px; width:600px;}
		p, #menu a {color:#939598; font-size:small;}
		#menu {overflow:auto; margin-bottom:20px; width:250px;}
		#menu a {display:block; text-decoration:none; padding:2px 0;}
		#menu a:hover {color:black;}
	</style>
</head>

<body>
	<h1>create my own portfolio</h1>
	<h2>Wonderfully simple, minimalistic websites.<br />
		Built for creative professionals by creative professionals.
	</h2>
	<div id="menu">
		<a href="demo">interactive demo</a>
		<a href="docs">documentation</a>
		<a href="#buynow"><img src="http://www.paypal.com/en_US/i/btn/btn_buynow_LG.gif" /></a>
	</div>
	<div id="video"></div>
	<p>watch a portfolio come together in less than two minutes</p>
</body>
</html>
