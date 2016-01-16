<?php
/**
* @version 1.0.0
* @package RSEvents!Pro 1.0.0
* @copyright (C) 2012 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>
<?php $open = !$links ? 'target="_blank"' : ''; ?>
<?php foreach ($list as $item) { ?>
<?php $events = modRseventsProCategories::getCount($item->id); ?>
<li> 
	<?php $levelup = $item->level - $startLevel - 1; ?>
	<h<?php echo $params->get('item_heading',4) + $levelup; ?>>
		<?php if ($remove) { ?>
			<?php if (empty($events)) { ?>
				<?php echo $item->title;?>
			<?php } else { ?>
				<a <?php echo $open; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&category='.rseventsproHelper::sef($item->id,$item->title),true,$itemid); ?>">
					<?php echo $item->title;?> <?php if ($counter && $events) { ?> (<?php echo JText::plural('MOD_RSEVENTSPRO_CATEGORIES_EVENTS_COUNT',$events); ?>) <?php } ?>
				</a>
			<?php } ?>
		<?php } else { ?>
			<a <?php echo $open; ?> href="<?php echo rseventsproHelper::route('index.php?option=com_rseventspro&category='.rseventsproHelper::sef($item->id,$item->title),true,$itemid); ?>">
				<?php echo $item->title;?> <?php if ($counter && $events) { ?> (<?php echo JText::plural('MOD_RSEVENTSPRO_CATEGORIES_EVENTS_COUNT',$events); ?>) <?php } ?>
			</a>
		<?php } ?>
	</h<?php echo $params->get('item_heading',4) + $levelup; ?>>

	<?php
		if($params->get('show_children', 0) && (($params->get('maxlevel', 0) == 0) || ($params->get('maxlevel') >= ($item->level - $startLevel))) && count($item->getChildren())) {
			echo '<ul class="level'.$item->level.'">';
			$temp = $list;
			$list = $item->getChildren();
			require JModuleHelper::getLayoutPath('mod_rseventspro_categories', $params->get('layout', 'default').'_items');
			$list = $temp;
			echo '</ul>';
		}
		?>
</li>
<?php } ?>