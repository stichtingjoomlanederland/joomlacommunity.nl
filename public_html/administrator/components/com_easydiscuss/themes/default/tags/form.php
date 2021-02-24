<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<div class="row">
		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_PROPERTIES'); ?>

				<div class="panel-body">
					<div class="o-form-horizontal">
						<?php echo $this->html('forms.textbox', 'title', 'COM_EASYDISCUSS_TAG', $this->html('string.escape', $tag->title)); ?>
						<?php echo $this->html('forms.textbox', 'alias', 'COM_EASYDISCUSS_TAG_ALIAS', $this->html('string.escape', $tag->alias)); ?>
						<?php echo $this->html('forms.toggle', 'published', 'COM_EASYDISCUSS_PUBLISHED', $tag->published); ?>

						<?php if ($tag->id) { ?>
						<div class="o-form-group">
							<div class="col-md-5 o-form-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MERGE_TO'); ?>
							</div>
							<div class="col-md-7">
								<?php echo JHTML::_('select.genericlist', $tagList, 'mergeTo', 'class="o-form-select" data-ed-select', 'value', 'text', 0 );; ?>
							</div>
						</div>
						<?php } ?>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
		</div>
	</div>

	<?php echo $this->html('form.action', 'tags', '', 'save'); ?>

	<input type="hidden" name="tagid" value="<?php echo $tag->id;?>" />
	<input type="hidden" name="savenew" id="savenew" value="0" />
</form>