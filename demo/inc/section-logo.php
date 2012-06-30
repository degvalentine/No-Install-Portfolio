<div id="logo">
	<img id="logo" alt="<?php echo $_SESSION['data']->settings->title; ?>" 
		src="<?php echo $_SESSION['data']->logo; ?>" 
		<?php if (empty($_SESSION['data']->logo)): ?>
		style="display:none"
		<?php endif; ?>
	/>
</div>