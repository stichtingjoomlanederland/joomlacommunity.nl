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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_VOTING_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_VOTING_DESC' );?>
		</p>
	</div>
</div>


<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option08">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_VOTING' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>
			<div id="option08" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_SELF_POST_VOTE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_SELF_POST_VOTE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_SELF_POST_VOTE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_allowselfvote' , $this->config->get( 'main_allowselfvote' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_VOTE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_POST_VOTE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_POST_VOTE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_allowvote' , $this->config->get( 'main_allowvote' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_QUESTION_POST_VOTE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_QUESTION_POST_VOTE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_QUESTION_POST_VOTE_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_allowquestionvote' , $this->config->get( 'main_allowquestionvote' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_GUEST_TO_VIEW_WHO_VOTED' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_GUEST_TO_VIEW_WHO_VOTED' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ALLOW_GUEST_TO_VIEW_WHO_VOTED_NOTICE'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_allowguestview_whovoted' , $this->config->get( 'main_allowguestview_whovoted' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_GUEST_TO_VOTE_QUESTION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_GUEST_TO_VOTE_QUESTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ALLOW_GUEST_TO_VOTE_QUESTION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_allowguest_vote_question' , $this->config->get( 'main_allowguest_vote_question' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_GUEST_TO_VOTE_REPLY' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ALLOW_GUEST_TO_VOTE_REPLY' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ALLOW_GUEST_TO_VOTE_REPLY_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_allowguest_vote_reply' , $this->config->get( 'main_allowguest_vote_reply' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="span6">
	</div>
</div>
