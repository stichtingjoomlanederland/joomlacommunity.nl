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
		<h2><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SITEDETAILS_TITLE' );?></h2>
		<p style="margin: 0 0 15px;">
			<?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SITEDETAILS_DESC' );?>
		</p>
	</div>
</div>

<div class="row-fluid ">
	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SITEDETAILS' ); ?></h6>
					<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option01" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SITEDETAILS_ENABLE_QUESTION' ); ?>
							</label>
						</div>
						<div class="span7" rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SITEDETAILS_ENABLE_QUESTION' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SITEDETAILS_ENABLE_QUESTION_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'tab_site_question' , $this->config->get( 'tab_site_question' ) );?>
						</div>
					</div>
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SITEDETAILS_ENABLE_REPLIES' ); ?>
							</label>
						</div>
						<div class="span7" rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SITEDETAILS_ENABLE_REPLIES' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SITEDETAILS_ENABLE_REPLIES_DESC'); ?>">
							<?php echo $this->renderCheckbox( 'tab_site_reply' , $this->config->get( 'tab_site_reply' ) );?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="span6">
		<div class="widget accordion-group">
			<div class="whead accordion-heading">
				<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option02">
					<h6><?php echo JText::_( 'COM_EASYDISCUSS_SETTINGS_SITEDETAILS_ACCESS' ); ?></h6>
					<i class="icon-chevron-down"></i>
				</a>
			</div>

			<div id="option02" class="accordion-body collapse in">
				<div class="wbody">
					<div class="si-form-row">
						<div class="span5 form-row-label">
							<label>
								<?php echo JText::_( 'COM_EASYDISCUSS_SITEDETAILS_VIEW_ACCESS' ); ?>
							</label>
						</div>
						<div class="span7" rel="ed-popover"
							data-placement="left"
							data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_SITEDETAILS_VIEW_ACCESS' ); ?>"
							data-content="<?php echo JText::_('COM_EASYDISCUSS_SITEDETAILS_VIEW_ACCESS_DESC'); ?>">
							<?php
							$access 	= explode( ',' , trim( $this->config->get( 'tab_site_access' ) ) );
							?>
							<select name="tab_site_access[]" multiple="multiple" style="height:150px;">
							<?php foreach( $this->joomlaGroups as $group ){ ?>
								<option value="<?php echo $group->id;?>"<?php echo in_array( $group->id , $access ) ? ' selected="selected"' : '';?>><?php echo $group->name; ?></option>
							<?php }?>
							</select>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


