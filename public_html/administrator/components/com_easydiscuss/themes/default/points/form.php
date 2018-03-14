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
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" data-ed-form>
<div class="row-fluid">
	<div class="span6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_POINTS_DETAILS'); ?>

			<div id="option01" class="panel-body">
				<div class="form-horizontal">
					<div class="form-group">
						 <div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_POINTS_TITLE'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" class="full-width inputbox" name="title" value="<?php echo $point->get( 'title' );?>" />
						</div>
					</div>

					<div class="form-group">
						 <div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_PUBLISHED'); ?>
						</div>
						<div class="col-md-7">
							<?php echo $this->html('form.boolean', 'published', $point->get('published', true));?>
						</div>
					</div>
					<div class="form-group">
						 <div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_POINTS_CREATION_DATE'); ?>
						</div>
						<div class="col-md-7">
							<?php echo ED::date()->format($point->created, JText::_('DATE_FORMAT_LC1')); ?>
						</div>
					</div>
					<div class="form-group">
						 <div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_POINTS_ACTION'); ?>
						</div>
						<div class="col-md-7">
							<select name="rule_id" onchange="showDescription( this.value );" class="form-control" >
								<option value="0"<?php echo !$point->get( 'rule_id' ) ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYDISCUSS_SELECT_RULE' );?></option>
							<?php foreach ($rules as $rule) { ?>
								<option value="<?php echo $rule->id;?>" <?php echo !$rule->availability && $point->get('rule_id') != $rule->id ? 'disabled' : ''; ?><?php echo $point->get( 'rule_id' ) == $rule->id ? ' selected="selected"' : '';?>><?php echo $rule->title; ?></option>
							<?php } ?>
							</select>
							<?php foreach ($rules as $rule) { ?>
							<div id="rule-<?php echo $rule->id;?>" class="rule-description" style="display:none;"><?php echo $rule->description;?></div>
							<?php } ?>
						</div>
					</div>
					<div class="form-group">
						 <div class="col-md-5 control-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_POINTS_GIVEN'); ?>
						</div>
						<div class="col-md-7">
							<input type="text" name="rule_limit" class="form-control form-control-sm text-center" style="text-align: center;" value="<?php echo $point->get( 'rule_limit'); ?>" />
						</div>
					</div>
				</div>
			</div>

		</div>

	</div>
	<div class="span6">

	</div>
</div>

<input type="hidden" name="id" value="<?php echo $point->id; ?>" />
<?php echo $this->html('form.hidden', 'points', 'points', 'save'); ?>
</form>
