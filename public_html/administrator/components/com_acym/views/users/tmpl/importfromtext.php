<?php
defined('_JEXEC') or die('Restricted access');
?><div id="acym__users__import__from_text" class="grid-x padding-vertical-2 padding-horizontal-2">
	<h6 class="cell margin-bottom-1 text-center"><?php echo acym_translation('ACYM_IMPORT_TEXT_DESC'); ?></h6>
	<div class="cell large-2"></div>
	<div class="cell large-8 grid-x">
		<textarea rows="10" name="acym__users__import__from_text__textarea" class="cell">
name,email
Sloan,sloan@example.com
John,john@example.com
		</textarea>
		<div class="cell grid-x text-right">
			<div class="medium-auto cell"></div>
			<button type="button" class="button cell medium-shrink acym__import__submit" data-from="textarea"><?php echo acym_translation('ACYM_IMPORT'); ?></button>
		</div>
	</div>
	<div class="cell large-2"></div>
</div>

