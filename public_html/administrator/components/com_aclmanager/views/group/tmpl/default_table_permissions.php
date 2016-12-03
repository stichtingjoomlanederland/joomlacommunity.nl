<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Initialise variable
$id 	= $this->state->get('filter.group_id');
$layout	= JFactory::getApplication()->input->get('layout',null);
$hidden = false;
?>

<!-- Begin assets -->
<?php foreach ($this->assets as $asset) : ?>
	<?php if(($asset->id == 1) && ($hidden == false)) {
		$hidden = true;
	} else {
		$hidden = false;
	}
	?>
	<!-- Start row -->
	<tr class="row1 <?php echo $asset->class; ?><?php if($hidden):?> hidden<?php endif;?>">
		<!-- Title -->
		<td class="norule title" colspan="<?php if (($asset->level == 0) || ($asset->third)):?>1<?php else :?><?php echo((AclmanagerHelper::loginActions($this->actions))+1);?><?php endif; ?>">
			<div class="icons">
				<?php echo str_repeat('<span class="gi">|&mdash;</span>', ($asset->level)) ?>
				<span class="type icon-16-<?php echo $asset->icon; ?>"><img src="components/com_aclmanager/assets/images/icon-16-<?php echo $asset->icon; ?>.png"/></span>
			</div>
			<div class="title">
				<?php echo $asset->url; ?>
			</div>
		</td>
		<!-- Optional space -->
		<?php if (($asset->level != 0) && ((!$asset->third) && ($asset->level != 1))):?>
			<td class="norule coreadmin"></td>
			<td class="norule coremanage"></td>
		<?php endif; ?>
		<!-- Third party actions -->
		<?php if(($asset->third) && ($asset->level != 0)):?>
		<td class="norule center extra" colspan="<?php echo(AclmanagerHelper::loginActions($this->actions));?>">
		<?php $actions	= JAccess::getActions($asset->component, $asset->type);?>
		<?php if($layout == 'print'):?>
			<?php echo JHtml::_('sliders.start','aclmanager-print-'.$asset->id, array('useCookie'=>0,'startOffset'=>0)); ?>
		<?php else:?>
			<?php echo JHtml::_('sliders.start','aclmanager-'.$asset->id, array('useCookie'=>1,'startOffset'=>-1)); ?>
		<?php endif;?>
		<?php echo JHtml::_('sliders.panel',JText::_('COM_ACLMANAGER_TABLE_ADDITIONAL_ACTIONS'), 'thirdactions'); ?>
			<table class="extra">
			<?php foreach ($actions as $action): ?>
				<?php if(AclmanagerHelper::additionalAction($action)): ?>
				<tr>
					<td width="80%">
						<span class="hasTip" title="<?php echo htmlspecialchars(JText::_($action->title).'::'.JText::_($action->description), ENT_COMPAT, 'UTF-8'); ?>"><?php echo JText::_($action->title); ?></span>
					</td>
					<?php $assetcheck = AclmanagerHelper::assetAction($asset,$action->name,$id,0);?>
					<td class="rule <?php echo($assetcheck->class);?>" data-assetid="<?php echo($asset->id);?>" data-action="<?php echo(JFilterOutput::stringURLSafe($action->name));?>" data-parentid="<?php echo($asset->parent);?>" data-groupid="<?php echo($id);?>" data-su="<?php echo($assetcheck->su);?>" data-third="1">
						<input type="hidden" id="jformrules_<?php echo($asset->id);?>_<?php echo(JFilterOutput::stringURLSafe($action->name));?>_<?php echo($id);?>" name="jform[rules][<?php echo($asset->id);?>][<?php echo($action->name);?>][<?php echo($id);?>]" data-assetcheck="<?php echo($assetcheck->check);?>"  value="<?php echo($assetcheck->value);?>" />
						<?php if($asset->config):?>
						<a class="<?php echo($assetcheck->icon);?>">
						<?php if($layout == 'print'):?>
							<img src="components/com_aclmanager/assets/images/<?php echo($assetcheck->image);?>.png"/>
						<?php endif;?>
						</a>
						<?php else:?>
							<img src="components/com_aclmanager/assets/images/<?php echo($assetcheck->image);?>.png"/>
						<?php endif;?>
					</td>
				</tr>
				<?php endif;?>
			<?php endforeach;?>
			</table>
		<?php echo JHtml::_('sliders.end'); ?>
		</td>
		<?php endif;?>
		<!-- Optional space -->
		<?php if (($asset->third) && ($asset->level >= 2)):?>
			<td class="norule center coreadmin"></td>
			<td class="norule center coremanage"></td>
		<?php endif; ?>
		<!-- Actions -->
		<?php foreach ($this->actions as $action) : ?>
			<?php if(AclmanagerHelper::displayAction($action[0],$asset->level)):?>
				<?php if(strpos($asset->rules,'"'.$action[0].'"') === false):?>
					<td class="norule">&nbsp;</td>
				<?php else:?>
				<?php $assetcheck = AclmanagerHelper::assetAction($asset,$action[0],$id,0);?>
					<td class="rule <?php echo($assetcheck->class);?>" data-assetid="<?php echo($asset->id);?>" data-action="<?php echo($action[2]);?>" data-parentid="<?php echo($asset->parent);?>" data-groupid="<?php echo($id);?>" data-su="<?php echo($assetcheck->su);?>">
						<input type="hidden" id="jformrules_<?php echo($asset->id);?>_<?php echo($action[2]);?>_<?php echo($id);?>" name="jform[rules][<?php echo($asset->id);?>][<?php echo($action[0]);?>][<?php echo($id);?>]" data-assetcheck="<?php echo($assetcheck->check);?>"  value="<?php echo($assetcheck->value);?>" />
						<?php if($asset->config):?>
						<a class="<?php echo($assetcheck->icon);?>">
						<?php if($layout == 'print'):?>
							<img src="components/com_aclmanager/assets/images/<?php echo($assetcheck->image);?>.png"/>
						<?php endif;?>
						</a>
						<?php else:?>
							<img src="components/com_aclmanager/assets/images/<?php echo($assetcheck->image);?>.png"/>
						<?php endif;?>
					</td>
				<?php endif;?>
			<?php endif; ?>
		<?php endforeach; ?>
		<!-- Id -->
		<td class="norule center assetid">
			<?php echo $asset->id; ?>
		</td>
	</tr>
	<!-- End row -->
<?php endforeach; ?>
<!-- End assets -->