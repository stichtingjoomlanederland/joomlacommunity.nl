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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_COMMENT_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_COMMENT_DESC' );?>
		</p>
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
				<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_COMMENT' ); ?></h6>
				<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_COMMENT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_COMMENT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_COMMENT_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_comment' , $this->config->get( 'main_comment' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_COMMENT_POST' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_COMMENT_POST' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_ENABLE_COMMENT_POST_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_commentpost' , $this->config->get( 'main_commentpost' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_TNC' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_TNC' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_COMMENT_TNC_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_comment_tnc' , $this->config->get( 'main_comment_tnc' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_TNC_TITLE' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_TNC_TITLE' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_COMMENT_TNC_TITLE_DESC'); ?>"
						>
							<textarea name="main_comment_tnctext" class="inputbox span12" cols="65" rows="15"><?php echo str_replace('<br />', "\n", $this->config->get('main_comment_tnctext' )); ?></textarea>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_PAGINATION' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_PAGINATION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_COMMENT_PAGINATION_DESC'); ?>"
						>
							<?php echo $this->renderCheckbox( 'main_comment_pagination' , $this->config->get( 'main_comment_pagination' ) );?>
						</div>
					</div>

					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_PAGINATION_COUNT' ); ?>
							</label>
						</div>
						<div class="span7"
							rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_COMMENT_PAGINATION_COUNT' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_COMMENT_PAGINATION_COUNT_DESC'); ?>"
						>
							<input type="text" class="input-mini center" name="main_comment_pagination_count" value="<?php echo $this->config->get( 'main_comment_pagination_count' );?>" />
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	<div class="span6">

	</div>
</div>
