<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
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
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TAG'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'title', $tag->title); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TAG_ALIAS'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'alias', $tag->alias); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_PUBLISHED'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'published', $tag->published); ?>
							</div>
						</div>
						<?php if ($tag->id) { ?>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_MERGE_TO'); ?>
							</div>
							<div class="col-md-7">
								<?php echo JHTML::_('select.genericlist', $tagList, 'mergeTo', 'class="form-control"', 'value', 'text', 0 );; ?>
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

	<?php echo $this->html('form.token'); ?>

	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="controller" value="tags" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="tagid" value="<?php echo $tag->id;?>" />
	<input type="hidden" name="savenew" id="savenew" value="0" />
</form>