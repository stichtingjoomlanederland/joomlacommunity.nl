<?php
/**
 * @package		ACL Manager for Joomla
 * @copyright 	Copyright (c) 2011-2016 Sander Potjer
 * @license 	GNU General Public License version 3 or later
 */

// No direct access.
defined('_JEXEC') or die;

// Initialise variable
$id 	= $this->state->get('filter.user_id');
$layout	= JFactory::getApplication()->input->get('layout',null);
?>

<!-- Begin assets -->
<?php foreach ($this->assets as $asset) : ?>
	<!-- Start row -->
	<tr class="row1 <?php echo $asset->class; ?>">
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
					<?php $assetcheck = AclmanagerHelper::assetAction($asset,$action->name,0,$id);?>
					<td class="rule">
						<a class="<?php echo($assetcheck->icon);?>">
						<?php if($layout == 'print'):?>
							<img src="components/com_aclmanager/assets/images/<?php echo($assetcheck->image);?>.png"/>
						<?php endif;?>
						</a>
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
			<td class="norule coreadmin"></td>
			<td class="norule coremanage"></td>
		<?php endif; ?>
		<!-- Actions -->
		<?php foreach ($this->actions as $action) : ?>
			<?php if(AclmanagerHelper::displayAction($action[0],$asset->level)):?>
				<?php if(strpos($asset->rules,'"'.$action[0].'"') === false):?>
					<td class="norule">&nbsp;</td>
				<?php else:?>
				<?php $assetcheck = AclmanagerHelper::assetAction($asset,$action[0],0,$id);?>
					<td class="rule">
						<a class="<?php echo($assetcheck->icon);?>">
						<?php if($layout == 'print'):?>
							<img src="components/com_aclmanager/assets/images/<?php echo($assetcheck->image);?>.png"/>
						<?php endif;?>
						</a>
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