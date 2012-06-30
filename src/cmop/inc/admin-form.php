<div id="adminCmd">
	<form id="adminForm" method="post" action="admin/cmd">
		<input type="submit" id="configBtn" value="Settings" /> &bull; 
		<input type="submit" name="cmd" value="save" /> &bull; 
		<input type="submit" name="cmd" value="refresh" /> &bull; 
		<input type="submit" name="cmd" value="restore" /><select name="backup"></select> &bull; 
		<input type="submit" name="cmd" value="sign out" />
	</form>
	<div id="config">
	<form id="configForm" method="post" action="admin/settings">
		<fieldset><legend>Site Info</legend>
			<input name="title" type="text" class="labeled" title="title" value="<?php echo $_SESSION['data']->settings->title; ?>" />
			<input name="about" type="text" class="labeled" title="about" style="width:250px" value="<?php echo $_SESSION['data']->settings->about; ?>" />
			<br />
			<span class="help" style="margin-left:165px"><a href="http://en.wikipedia.org/wiki/Meta_element#The_description_attribute">[What's this?]</a></span>
			
		</fieldset>
		
		<fieldset><legend>Page Layout</legend>
			<div class="input-block">
				<input name="layout" type="radio" value="1" id="layout-1" <?php if($_SESSION['data']->settings->layout == '1') echo 'checked' ?> />
				<label for="layout-1"><img src="img/icon-layout-1.png" /></label>
			</div>
			<div class="input-block">
				<input name="layout" type="radio" value="2" id="layout-2" <?php if($_SESSION['data']->settings->layout == '2') echo 'checked' ?> />
				<label for="layout-2"><img src="img/icon-layout-2.png" /></label>
			</div>
			<br />
			<div class="input-block">
				<input name="layout" type="radio" value="3" id="layout-3" <?php if($_SESSION['data']->settings->layout == '3') echo 'checked' ?> />
				<label for="layout-3"><img src="img/icon-layout-3.png" /></label>
			</div>
			<div class="input-block">
				<input name="layout" type="radio" value="4" id="layout-4" <?php if($_SESSION['data']->settings->layout == '4') echo 'checked' ?> />
				<label for="layout-4"><img src="img/icon-layout-4.png" /></label>
			</div>
		</fieldset>
		
		<fieldset><legend>Menu Style</legend>
			<div class="input-block">
				<input name="menu" type="radio" value="inline" id="menu-inline" <?php if($_SESSION['data']->settings->menu == 'inline') echo 'checked' ?> />
				<label for="menu-inline"><img src="img/icon-menu-inline.png" /></label>
			</div>
			<div class="input-block">
				<input name="menu" type="radio" value="tiered" id="menu-tiered" <?php if($_SESSION['data']->settings->menu == 'tiered') echo 'checked' ?> />
				<label for="menu-tiered"><img src="img/icon-menu-tiered.png" /></label>
			</div>
			<div class="input-block">
				<div>
					<input name="effect" type="radio" value="none" id="effect-none" <?php if($_SESSION['data']->settings->effect == 'none') echo 'checked' ?> />
					<label for="effect-none">no effect</label>
				</div>
				<div>
					<input name="effect" type="radio" value="fade" id="effect-fade" <?php if($_SESSION['data']->settings->effect == 'fade') echo 'checked' ?> />
					<label for="effect-fade">fade</label>
				</div>
				<div>
					<input name="effect" type="radio" value="slide" id="effect-slide" <?php if($_SESSION['data']->settings->effect == 'slide') echo 'checked' ?> />
					<label for="effect-slide">slide</label>
				</div>
			</div>
		</fieldset>
		
		<fieldset><legend>Color Theme</legend>
			<div class="input-block">
				<input name="color" type="radio" value="white" id="white" <?php if($_SESSION['data']->settings->color == 'white') echo 'checked' ?> />
				<label for="white">White</label>
			</div>
			<div class="input-block">
				<input name="color" type="radio" value="black" id="black" <?php if($_SESSION['data']->settings->color == 'black') echo 'checked' ?> />
				<label for="black">Black</label>
			</div>
		</fieldset>
		
		<fieldset><legend>Intro (what do you want viewers to see when first visiting your site?)</legend>
			<div class="input-block">
				<input name="intro" type="radio" value="none" id="intro-none" <?php if($_SESSION['data']->settings->intro == 'none') echo 'checked' ?> />
				<label for="intro-none">nothing</label>
			</div>
			<div class="input-block">
				<input name="intro" type="radio" value="first" id="intro-first" <?php if($_SESSION['data']->settings->intro == 'first') echo 'checked' ?> />
				<label for="intro-first">first portfolio image</label>
			</div>
			<div class="input-block">
				<input name="intro" type="radio" value="cycle" id="intro-cycle" <?php if($_SESSION['data']->settings->intro == 'cycle') echo 'checked' ?> />
				<label for="intro-cycle">cycle through portfolio</label>
			</div>
		</fieldset>
		
		<fieldset><legend>Google Analytics Account Number</legend>
			<input name="gaAccount" class="labeled" title="UA-########-#" type="text" value="<?php echo $_SESSION['data']->settings->gaAccount ?>" />
			<span class="help"><a href="http://www.google.com/analytics/">[What's this?]</a></span>
		</fieldset>
		
		<fieldset>
			<input type="submit" value="Apply Settings" />
			(unsaved changes to portfolio will be lost)
		</fieldset>
	</form>
	</div>
</div>
