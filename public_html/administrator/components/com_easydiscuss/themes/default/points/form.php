<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data" data-ed-form>
<div class="row">
	<div class="col-md-6">
		<div class="panel">
			<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_POINTS_DETAILS'); ?>

			<div id="option01" class="panel-body">
				<div class="o-form-horizontal">
					<?php echo $this->html('forms.textbox', 'title', 'COM_EASYDISCUSS_POINTS_TITLE', $point->title); ?>
					<?php echo $this->html('forms.toggle', 'published', 'COM_EASYDISCUSS_PUBLISHED', $point->published); ?>

					<div class="o-form-group">
						 <div class="col-md-5 o-form-label">
							<?php echo $this->html('form.label', 'COM_EASYDISCUSS_POINTS_ACTION'); ?>
						</div>
						<div class="col-md-7">
							<select name="rule_id" onchange="showDescription( this.value );" class="o-form-select" data-ed-select>
								<option value="0"<?php echo !$point->get( 'rule_id' ) ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_SELECT_RULE');?></option>
							<?php foreach ($rules as $rule) { ?>
								<option value="<?php echo $rule->id;?>" <?php echo !$rule->availability && $point->get('rule_id') != $rule->id ? 'disabled' : ''; ?><?php echo $point->get( 'rule_id' ) == $rule->id ? ' selected="selected"' : '';?>><?php echo $rule->title; ?></option>
							<?php } ?>
							</select>
							<?php foreach ($rules as $rule) { ?>
							<div id="rule-<?php echo $rule->id;?>" class="rule-description" style="display:none;"><?php echo $rule->description;?></div>
							<?php } ?>
						</div>
					</div>

					<?php echo $this->html('forms.textbox', 'rule_limit', 'COM_EASYDISCUSS_POINTS_GIVEN', $point->rule_limit, array('size' => '7', 'postfix' => 'Points', 'class' => 'text-center')); ?>
				</div>
			</div>

		</div>

	</div>
	<div class="col-md-6">
	</div>
</div>

<input type="hidden" name="id" value="<?php echo $point->id; ?>" />
<?php echo $this->html('form.action', 'points', 'points', 'save'); ?>
</form>
