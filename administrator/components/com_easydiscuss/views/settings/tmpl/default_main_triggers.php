<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row-fluid">
	<div class="span12 panel-title">
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_CONTENT_TRIGGERS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_CONTENT_TRIGGERS_DESC' );?>
		</p>
		<!-- <a href="javascript:void(0)" class="btn btn-success">Documentation</a> -->
	</div>
</div>


<div class="row-fluid ">
	<div class="span6">

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option12">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_EVENT_TRIGGERS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option12" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TRIGGER_POSTS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TRIGGER_POSTS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_TRIGGER_POSTS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_content_trigger_posts' , $this->config->get( 'main_content_trigger_posts' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TRIGGER_REPLIES' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TRIGGER_REPLIES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_TRIGGER_REPLIES_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_content_trigger_replies' , $this->config->get( 'main_content_trigger_replies' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TRIGGER_COMMENTS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_TRIGGER_COMMENTS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_TRIGGER_COMMENTS_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_content_trigger_comments' , $this->config->get( 'main_content_trigger_comments' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="span6">
	</div>
</div>
