<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<form action="index.php" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
	<div class="row">
		<div class="col-md-8">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_BADGE_DETAILS'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-4 control-label">
								<label><?php echo JText::_('COM_EASYDISCUSS_BADGE_TITLE');?></label>
							</div>
							<div class="col-md-8">
								<input type="text" class="full-width inputbox" name="title" value="<?php echo $badge->get('title');?>" />
							</div>
						</div>

						<div class="form-group">
							<div class="col-md-4 control-label">
								<?php echo JText::_('COM_EASYDISCUSS_BADGE_DESCRIPTION');?>
							</div>
							<div class="col-md-12">
								<?php echo $editor->display('description', $badge->description, '100%', '300', 10, 10, array('zemanta', 'readmore', 'pagebreak', 'article', 'image')); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>

		<div class="col-md-4">
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_BADGE_CONFIGURATION'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-4 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_BADGE_PUBLISHED'); ?>
							</div>
							<div class="col-md-8">
								<?php echo $this->html('form.boolean', 'published', $badge->published); ?>
							</div>
						</div>
						<div class="form-group">
							<div class="col-md-4 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_BADGE_ACHIEVE_TYPE'); ?>
							</div>
							<div class="col-md-8">
								<select name="achieve_type" data-ed-badges-achieve-type class="form-control">
									<option value="frequency" <?php echo $badge->get('achieve_type') == 'frequency' ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_BADGE_ACHIEVE_TYPE_FREQUENCY'); ?></option>
									<option value="points" <?php echo $badge->get('achieve_type') == 'points' ? 'selected="selected"' : ''; ?>><?php echo JText::_('COM_EASYDISCUSS_BADGE_ACHIEVE_TYPE_POINTS'); ?></option>
								</select>
							</div>
						</div>

						<div class="form-group <?php echo $badge->get('achieve_type') == 'frequency' || !$badge->get('achieve_type') ? 'hidden' : ''; ?>" data-ed-badges-points>
							<div class="col-md-4 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_BADGE_POINTS_THRESHOLD'); ?>
							</div>
							<div class="col-md-8">
								<input type="text" name="points_threshold" class="form-control form-control-sm text-center" style="text-align: center;" value="<?php echo $badge->get('points_threshold'); ?>" />
							</div>
						</div>

						<div class="form-group <?php echo $badge->get('achieve_type') == 'frequency' || !$badge->get('achieve_type') ? 'hidden' : ''; ?>" data-ed-badges-points>
							<div class="col-md-4 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_BADGE_POINTS_HOW_TO_ACHIEVE'); ?>
							</div>
							<div class="col-md-8">
								<select name="badge_achieve_rule" class="form-control">
								<?php foreach($rules as $rule) { ?>
									<option value="<?php echo $rule->command;?>"<?php echo $badge->get('badge_achieve_rule') == $rule->command ? ' selected="selected"' : '';?>><?php echo $rule->title; ?></option>
								<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group <?php echo $badge->get('achieve_type') == 'frequency' || !$badge->get('achieve_type') ? 'hidden' : ''; ?>" data-ed-badges-points>
							<div class="col-md-4 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_BADGE_POINTS_HOW_TO_REMOVE'); ?>
							</div>
							<div class="col-md-8">
								<select name="badge_remove_rule" class="form-control">
								<option value="0" <?php echo $badge->get('badge_remove_rule') == '0' ? 'selected="selected"' : ''; ?>>
									<?php echo JText::_('None'); ?>
								</option>
								<?php foreach($rules as $rule) { ?>
									<option value="<?php echo $rule->command;?>"<?php echo $badge->get('badge_remove_rule') == $rule->command ? ' selected="selected"' : '';?>><?php echo $rule->title; ?></option>
								<?php } ?>
								</select>
							</div>
						</div>

						<div class="form-group <?php echo $badge->get('achieve_type') == 'points' ? 'hidden' : ''; ?>" data-ed-badges-frequency>
							<div class="col-md-4 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_BADGE_ACTION'); ?>
							</div>
							<div class="col-md-8">
								<select name="rule_id" onchange="showDescription(this.value);" class="form-control">
									<option value="0"<?php echo !$badge->get('rule_id') ? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_SELECT_RULE');?></option>
									<option value="-1"<?php echo $badge->get('rule_id') == '-1'? ' selected="selected"' : '';?>><?php echo JText::_('COM_EASYDISCUSS_MANUAL_ASSIGNMENT');?></option>
								<?php foreach($rules as $rule){ ?>
									<option value="<?php echo $rule->id;?>"<?php echo $badge->get('rule_id') == $rule->id ? ' selected="selected"' : '';?>><?php echo $rule->title; ?></option>
								<?php } ?>
								</select>
								<?php foreach($rules as $rule){ ?>
								<div id="rule-<?php echo $rule->id;?>" class="rule-description" style="display:none;"><?php echo $rule->description;?></div>
								<?php } ?>
							</div>
						</div>
						<div class="form-group <?php echo $badge->get('achieve_type') == 'points' ? 'hidden' : ''; ?>" data-ed-badges-frequency>
							<div class="col-md-4 control-label">
								<?php echo $this->html('form.label', 'COM_EASYDISCUSS_BADGE_ACTION_THRESHOLD'); ?>
							</div>
							<div class="col-md-8">
								<input type="text" name="rule_limit" class="form-control form-control-sm text-center" style="text-align: center;" value="<?php echo $badge->get('rule_limit'); ?>" />
							</div>
						</div>
					</div>
				</div>

			</div>
			<div class="panel">
				<?php echo $this->html('panel.head', 'COM_EASYDISCUSS_BADGE'); ?>

				<div class="panel-body">
					<div class="form-horizontal">
						<div class="form-group">
							<div class="col-md-12">
								<p><?php echo JText::_('COM_EASYDISCUSS_UPLOAD_BADGE_DESC');?></p>
								<code class="pa-5" style="display:block;white-space: pre-line;"><?php echo DISCUSS_BADGES_PATH; ?></code>
							</div>
						</div>

						<div class="form-group">
							<?php if ($badges) { ?>
							<ul class="g-list-inline pull-left clearfix t-lg-ml--md">
								<?php foreach ($badges as $item) { ?>
									<li class="badge-item center t-lg-mr--md t-lg-mb--md <?php echo $badge->avatar == $item ? ' selected-badge' : '';?>">
										<label for="<?php echo $badge;?>">
											<div><img src="<?php echo DISCUSS_BADGES_URI . '/' . $item;?>" width="48" /></div>
											<input type="radio" value="<?php echo $item;?>" name="avatar" id="<?php echo $item;?>"<?php echo $badge->avatar == $badge ? ' checked="checked"' : '';?> />
										</label>
									</li>
								<?php } ?>
							</ul>
							<?php } ?>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="id" value="<?php echo $badge->id; ?>" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="controller" value="badges" />
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="savenew" id="savenew" value="0" />
	<?php echo JHTML::_('form.token'); ?>
</form>
