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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_POLLS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_POLLS_DESC' );?>
		</p>
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">

		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option06">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_POLLS' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option06" class="accordion-body collapse in">
				<div class="wbody">

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POLLS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POLLS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POLLS_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'main_polls' , $this->config->get( 'main_polls' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POLLS_REPLIES' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POLLS_REPLIES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POLLS_REPLIES_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'main_polls_replies' , $this->config->get( 'main_polls_replies' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POLLS_MULTIPLE_VOTES' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POLLS_MULTIPLE_VOTES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POLLS_MULTIPLE_VOTES_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'main_polls_multiple' , $this->config->get( 'main_polls_multiple' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POLLS_AVATARS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POLLS_AVATARS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POLLS_AVATARS_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'main_polls_avatars' , $this->config->get( 'main_polls_avatars' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POLLS_FOR_GUESTS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POLLS_FOR_GUESTS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POLLS_FOR_GUESTS_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'main_polls_guests' , $this->config->get( 'main_polls_guests' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_LOCK_POLLS' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_LOCK_POLLS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_LOCK_POLLS_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'main_polls_lock' , $this->config->get( 'main_polls_lock' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>

	</div>

	<div class="span6"></div>

</div>


