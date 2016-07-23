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
defined('_JEXEC') or die('Restricted access');
?>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_COMMENT'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_COMMENT_POST'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_commentpost', $this->config->get('main_commentpost')); ?>
						</div>
					</div>

					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_ENABLE_COMMENT'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_comment', $this->config->get('main_comment')); ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_SETTINGS_COMMENT_PAGINATION'); ?>

			<div class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_COMMENT_PAGINATION'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'main_comment_pagination', $this->config->get('main_comment_pagination')); ?>
						</div>
					</div>
					<div class="form-group">
						<div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_COMMENT_PAGINATION_COUNT'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" class="form-control form-control-sm text-center" name="main_comment_pagination_count" value="<?php echo $this->config->get( 'main_comment_pagination_count' );?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>