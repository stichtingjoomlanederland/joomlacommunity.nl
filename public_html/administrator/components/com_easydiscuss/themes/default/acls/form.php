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
<form action="index.php" method="post" name="adminForm" id="adminForm" autocomplete="off">
	<div class="app-tabs">
	    <ul class="app-tabs-list g-list-unstyled">
	    	<?php $i = 0; ?>

	    	<?php foreach ($tabs as $tab) { ?>
		        <li class="tabItem <?php echo $i == 0 ? ' active' : '';?>">
		            <a href="#ed-<?php echo $tab->id;?>" data-ed-toggle="tab" data-ed-tab data-id="ed-<?php echo $tab->id;?>"><?php echo $tab->title;?></a>
		        </li>
		        <?php $i++; ?>
	        <?php } ?>
	    </ul>
	</div>

    <div class="tab-content">
        <?php $i = 0; ?>
        <?php foreach ($tabs as $tab) { ?>
        <div id="ed-<?php echo $tab->id;?>" class="tab-pane<?php echo ($i == 0) ? ' active in' : '';?>">
			<div class="row">
				<div class="col-md-8">
					<div class="panel">
			            <?php echo $this->html('panel.head', 'COM_EASYDISCUSS_ACL_RULE_SET_' . strtoupper($tab->id)); ?>

						<div class="panel-body">
							<div class="form-horizontal">
								<?php foreach ($ruleset->rules[$tab->id] as $rule) { ?>
								<div class="form-group">
				                    <div class="col-md-4 control-label">
				                        <?php echo $this->html('form.label', 'COM_EASYDISCUSS_ACL_OPTION_' . strtoupper($rule->action)); ?>
				                    </div>

				                    <div class="col-md-8" data-ed-acl-option>
				                        <?php echo $this->html('form.boolean', $rule->action, $rule->value, '', 'data-ed-acl-rule'); ?>

                                        <span style="margin-left: 10px;">
                                            <span class="acl-result-yes text-success <?php echo $rule->value ? '' : 't-hidden';?>" data-ed-acl-allowed>
                                                <i class="fa fa-check-circle"></i>&nbsp; 
                                                <?php echo JText::_('COM_EASYDISCUSS_ACL_OPTION_' . strtoupper($rule->action) . '_RESULT_YES'); ?>
                                            </span>
                                            <span class="acl-result-no text-danger <?php echo !$rule->value ? '' : 't-hidden';?>" data-ed-acl-disallowed>
                                                <i class="fa fa-times-circle"></i>&nbsp; <?php echo JText::_('COM_EASYDISCUSS_ACL_OPTION_' . strtoupper($rule->action) . '_RESULT_NO'); ?>
                                            </span>
                                        </span>
				                    </div>
								</div>
								<?php } ?>
							</div>
						</div>
					</div>
				</div>

				<div class="col-md-4">
				</div>
			</div>
        </div>
        <?php $i++;?>
        <?php } ?>
    </div>


	<?php echo $this->html('form.hidden', 'acl', ''); ?>
	<input type="hidden" name="cid" id="cid" value="<?php echo isset($ruleset->id) && !is_null($ruleset->id) ? $ruleset->id : '';?>" />
	<input type="hidden" name="name" value="<?php echo $ruleset->name ? $ruleset->name : ''; ?>" />
</form>
