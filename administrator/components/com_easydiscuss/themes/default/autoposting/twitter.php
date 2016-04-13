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
<form name="adminForm" id="adminForm" action="index.php" method="post" class="adminForm">

	<div class="row">
		<div class="col-md-6">

			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_AUTOPOST_TWITTER_APP'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TWITTER_AUTOPOST_ENABLE'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.boolean', 'main_autopost_twitter', $this->config->get('main_autopost_twitter')); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOPOST_TWITTER_CONSUMER_KEY'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'main_autopost_twitter_id', $this->config->get('main_autopost_twitter_id')); ?>
								<div class="small">
									<a href="http://stackideas.com/docs/easydiscuss/administrators/autoposting/twitter-application" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS');?></a>
								</div>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOPOST_TWITTER_CONSUMER_SECRET'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textbox', 'main_autopost_twitter_secret', $this->config->get('main_autopost_twitter_secret')); ?>
								<div class="small">
									<a href="http://stackideas.com/docs/easydiscuss/administrators/autoposting/twitter-application" target="_blank"><?php echo JText::_('COM_EASYDISCUSS_WHAT_IS_THIS');?></a>
								</div>
							</div>
						</div>
						<div class="form-group">

							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_AUTOPOST_TWITTER_SIGN_IN'); ?>
							</div>

							<div class="col-md-7">
								<?php if ($associated) { ?>
								<a href="<?php echo JRoute::_('index.php?option=com_easydiscuss&controller=autoposting&task=revoke&type=twitter');?>" class="btn btn-danger">
									<?php echo JText::_('COM_EASYDISCUSS_AUTOPOST_REVOKE_ACCCESS');?>
								</a>
								<?php } else { ?>
								<a href="javascript:void(0)" data-twitter-login>
									<img src="<?php echo JURI::root();?>media/com_easydiscuss/images/twitter_signon.png" />
								</a>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-6">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_TWITTER_AUTOPOST_GENERAL'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-5 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_TWITTER_AUTOPOST_POST_MESSAGE'); ?>
							</div>
							<div class="col-md-7">
								<?php echo $this->html('form.textarea', 'main_autopost_twitter_message', $this->config->get('main_autopost_twitter_message'));?>

								<p class="small mt-10">
									<?php echo JText::_('COM_EASYDISCUSS_TWITTER_AUTOPOST_POST_MESSAGE_FOOTNOTE'); ?>
								</p>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="step" value="completed" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="type" value="twitter" />
	<input type="hidden" name="controller" value="autoposting" />
	<input type="hidden" name="option" value="com_easydiscuss" />
</form>
