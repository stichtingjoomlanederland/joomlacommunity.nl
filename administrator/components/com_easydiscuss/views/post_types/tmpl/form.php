<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

$this->postTypes = empty($this->postTypes) ? '' : $this->postTypes;
?>

<div class="adminform-body">
<form action="index.php" method="post" name="adminForm" id="adminForm">
	<ul class="nav nav-tabs">
		<li class="active"><a href="#si-option1" data-foundry-toggle="tab"><?php echo JText::_( 'COM_EASYDISCUSS_POST_TYPES_TAB_MAIN' ); ?></a></li>
	</ul>
	<div class="tab-content">
		<div class="row-fluid customFields">

			<div class="span6">
				<div class="widget accordion-group">
					<div class="whead accordion-heading">
						<a href="javascript:void(0);" data-foundry-toggle="collapse" data-target="#option01">
						<h6><?php echo JText::_('COM_EASYDISCUSS_POST_TYPES_TITLE'); ?></h6>
						<i class="icon-chevron-down"></i>
						</a>
					</div>
					<div id="option01" class="accordion-body collapse in">
						<div class="wbody">
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label><?php echo JText::_( 'COM_EASYDISCUSS_POST_TYPES_TITLE' ); ?></label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_POST_TYPES_TITLE' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_POST_TYPES_TITLE_DESC'); ?>"
									>
									<input type="text" data-customid="<?php echo $this->postTypes->id; ?>" class="input-full" name="title" maxlength="255" value="<?php echo $this->escape($this->postTypes->title);?>" />
								</div>
							</div>
						</div>
					</div>


					<div id="option01" class="accordion-body collapse in" <?php echo ( !$this->postTypes->id ) ? 'style="display:none;"' : ''; ?> >
						<div class="wbody">
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label><?php echo JText::_( 'COM_EASYDISCUSS_POST_TYPES_ALIAS' ); ?></label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_POST_TYPES_ALIAS' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_POST_TYPES_ALIAS_DESC'); ?>"
									>
									<input type="text" data-customid="<?php echo $this->postTypes->id; ?>" class="input-full" name="alias" maxlength="255" value="<?php echo $this->escape($this->postTypes->alias);?>" <?php echo ( $this->postTypes->id ) ? 'readonly="readonly"' : ''; ?> />
								</div>
							</div>
						</div>
					</div>

					<div id="option01" class="accordion-body collapse in">
						<div class="wbody">
							<div class="si-form-row">
								<div class="span5 form-row-label">
									<label><?php echo JText::_( 'COM_EASYDISCUSS_POST_TYPES_SUFFIX' ); ?></label>
								</div>
								<div class="span7"
									rel="ed-popover"
									data-placement="left"
									data-original-title="<?php echo JText::_( 'COM_EASYDISCUSS_POST_TYPES_SUFFIX' ); ?>"
									data-content="<?php echo JText::_('COM_EASYDISCUSS_POST_TYPES_SUFFIX_DESC'); ?>"
									>
									<input type="text" data-customid="<?php echo $this->postTypes->id; ?>" class="input-full" name="suffix" maxlength="255" value="<?php echo $this->escape($this->postTypes->suffix);?>" />
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
	</div>
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="id" value="<?php echo $this->postTypes->id ?>" />
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="controller" value="post_types" />
	<input type="hidden" name="task" value="" />

</form>
</div>
