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
JHTML::_('behavior.modal' , 'a.modal' );
?>
<div class="row-fluid">
	<div class="span6">
		<div id="accordion2" class="accordion acl-accordion">
		<?php
			$first = true;
			foreach ($this->customFieldsAcl as $fieldsAcl) {
		?>
		<div class="accordion-group">
				<div class="accordion-heading">
					<a href="#collapse-<?php echo $fieldsAcl->action; ?>" data-parent="#accordion2" data-foundry-toggle="collapse" class="accordion-toggle" id="accordion-toggle-<?php echo $fieldsAcl->action; ?>">
						<?php echo JText::_('COM_EASYDISCUSS_CUSTOMFIELDS_ACL_'.$fieldsAcl->action.'_DESC'); ?>
						<i class="icon-chevron-down"></i>
					</a>
				</div>
				<div class="accordion-body collapse<?php echo $first ? ' in' : ''; ?>" id="collapse-<?php echo $fieldsAcl->action; ?>">
					<div class="accordion-inner">
						<div class="row-fluid">
							<div class="span6">
								<div class="acl-title"><?php echo JText::_( 'COM_EASYDISCUSS_GROUP' );?></div>
								<ul id="customFields_acl_group_<?php echo $fieldsAcl->action; ?>" class="permision-list unstyled">

									<?php if( isset($this->assignedGroupACL[$fieldsAcl->id]) && count($this->assignedGroupACL[$fieldsAcl->id]) ) { ?>
									<?php foreach($this->assignedGroupACL[$fieldsAcl->id] as $ruleItem) { ?>
										<?php if( $ruleItem->status ) { ?>

										<li id="acl_group_<?php echo $fieldsAcl->action; ?>_<?php echo $ruleItem->groupid; ?>">
											<span>
												<a href="javascript: admin.customFields.acl.remove('acl_group_<?php echo $fieldsAcl->action; ?>_<?php echo $ruleItem->groupid; ?>')">
													<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_ACL_DELETE'); ?>
												</a>
											</span> - <?php echo $ruleItem->groupname; ?>
											<input type="hidden" name="acl_group_<?php echo $fieldsAcl->action; ?>[]" value="<?php echo $ruleItem->groupid; ?>" />
										</li>
										<?php } ?>
									<?php } ?>
									<?php } ?>
								</ul>
							</div>
							<div class="span6">
								<div class="acl-title"><?php echo JText::_( 'COM_EASYDISCUSS_USER' );?></div>
								<ul id="customFields_acl_user_<?php echo $fieldsAcl->action; ?>" class="permision-list unstyled">
									<?php if( isset($this->assignedUserACL[$fieldsAcl->id]) && count($this->assignedUserACL[$fieldsAcl->id]) ) { ?>
									<?php foreach($this->assignedUserACL[$fieldsAcl->id] as $ruleItem) { ?>
										<?php if( $ruleItem->status ) { ?>
										<li id="acl_user_<?php echo $fieldsAcl->action; ?>_<?php echo $ruleItem->groupid; ?>">
											<span>
												<a href="javascript: admin.customFields.acl.remove('acl_user_<?php echo $fieldsAcl->action; ?>_<?php echo $ruleItem->groupid; ?>')">
													<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_ACL_DELETE'); ?>
												</a>
											</span> - <?php echo $ruleItem->groupname; ?>
											<input type="hidden" name="acl_user_<?php echo $fieldsAcl->action; ?>[]" value="<?php echo $ruleItem->groupid; ?>" />
										</li>
										<?php } ?>
									<?php } ?>
									<?php } ?>
								</ul>
							</div>
						</div>
					</div>
				</div>
			</div>
		<?php $first = false; ?>
		<?php } ?>
		</div>
	</div>
	<div class="span6">
		<div class="row-fluid">
			<div class="span6">
				<div id="customFields-acl-panel-group" class="panel-pane">
					<ul class="customFields-acl-panel-group-ul unstyled">
						<?php if( count($this->joomlaGroups) > 0) { ?>
							<?php foreach($this->joomlaGroups as $ruleItem) { ?>
								<li id="group-li-<?php echo $ruleItem->id; ?>" class="form-inline">
									<input type="checkbox" name="acl_panel_group[]" value="<?php echo $ruleItem->id; ?>" />
									<input type="hidden" id="acl_panel_group_<?php echo $ruleItem->id; ?>" value="<?php echo $ruleItem->name; ?>" />
									<label for="acl_panel_group_<?php echo $ruleItem->id;?>"><?php echo $ruleItem->name; ?></label>
								</li>
							<?php } ?>
						<?php } ?>
					</ul>
					<div class="mt-10">
						<input type="button" id="customFields-acl-assign-group" class="btn btn-success btn-small" value="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_ACL_ADD_TO_LIST' , true ); ?>">
					</div>
				</div>
			</div>
			<div class="span6">
				<div id="customFields-acl-panel-user" class="panel-pane" style="display: block; ">
					<ul id="customFields-acl-panel-user-ul" class="unstyled">
					</ul>
					<div>
					<a class="modal" rel="{handler: 'iframe'}" href="index.php?option=com_easydiscuss&view=users&tmpl=component&browse=1&browsefunction=selectUser&prefix=acl"><?php echo JText::_('COM_EASYDISCUSS_BROWSE_USERS');?></a>&nbsp; - &nbsp;
						<input type="button" id="customFields-acl-assign-user" class="btn btn-success btn-small" value="<?php echo JText::_('COM_EASYDISCUSS_CATEGORIES_ACL_ADD_TO_LIST' , true );?>">
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<input type="hidden" name="activerule" id="activerule" value="" />
