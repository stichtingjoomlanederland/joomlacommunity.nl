<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="ed" class="ed-mod m-quick-question <?php echo $params->get('moduleclass_sfx');?>" >
	<div class="ed-mod__section">
		<form id="mod_easydiscuss_quickquestion" name="mod_easydiscuss_quickquestion" action="index.php" method="post" data-ed-module-form>
			<div>
				<div class="form-group">
					<?php echo $nestedCategories; ?>
				</div>

				<?php if (empty($user->id) && $acl->allowed('add_question')) { ?>
				<div class="form-group">
					<input class="form-control" type="text" id="poster_name" name="poster_name" value="" placeholder="<?php echo JText::_('MOD_EASYDISCUSS_QUICKQUESTION_NAME'); ?>" />
				</div>
				<div class="form-group">
					<input class="form-control" type="text" id="poster_email" name="poster_email" value="" placeholder="<?php echo JText::_('MOD_EASYDISCUSS_QUICKQUESTION_EMAIL'); ?>" />
				</div>
				<?php } ?>
				<div class="form-group">
					<input type="text" id="quick-question-title" name="title" placeholder="<?php echo JText::_('MOD_EASYDISCUSS_QUICKQUESTION_TITLE', true); ?>" class="form-control" />
				</div>
				<div class="form-group">
					<textarea id="quick-question-content" name="dc_content" class="form-control" placeholder="<?php echo JText::_('MOD_EASYDISCUSS_QUICKQUESTION_CONTENT', true); ?>"></textarea>
				</div>

				<?php if ($captcha->enabled()) { ?>
				<div class="ed-mod__recaptcha">
					<?php if (!$captcha->isInvisible()) { ?>
					<div class="ed-editor-widget__title">
						<?php echo JText::_('COM_EASYDISCUSS_CAPTCHA_TITLE'); ?>
					</div>
					<?php } ?>
					
					<?php
						ED::scripts()->clearScripts();
						echo $captcha->html(true);
						echo ED::scripts()->getScripts();
					?>
				</div>
				<?php } ?>

				<input type="submit" class="btn btn-primary" value="<?php echo JText::_('MOD_EASYDISCUSS_QUICKQUESTION_SUBMIT', true); ?>" data-ed-module-submit />
			</div>
			<?php echo JHTML::_('form.token'); ?>
			<input type="hidden" name="id" value="0" />
			<input type="hidden" name="option" value="com_easydiscuss" />
			<input type="hidden" name="controller" value="posts" />
			<input type="hidden" name="task" value="save" />
		</form>
	</div>
</div>

