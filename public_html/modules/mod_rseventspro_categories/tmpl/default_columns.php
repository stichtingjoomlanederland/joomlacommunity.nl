<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

// no direct access
defined('_JEXEC') or die('Restricted access'); ?>

<?php $open = !$links ? 'target="_blank"' : ''; ?>
<?php $blocks = modRseventsProCategories::getCategoriesBlocks($params); ?>
<?php foreach ($blocks as $block => $items) { ?>
<li class="rsepro-block">
	<ul class="row-fluid">
		<?php foreach ($items as $item) { ?>
			<?php if ($counter || $remove) $events = modRseventsProCategories::getCount($item->id, $params); ?>
			<li class="span<?php echo 12 / $columns; ?>">
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
			</li>
		<?php } ?>
	</ul>
</li>
<?php } ?>