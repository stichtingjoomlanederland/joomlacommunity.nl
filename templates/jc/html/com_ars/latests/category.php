<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

JLoader::import('joomla.utilities.date');
$released = new JDate($item->release->created);
$release_url = AKRouter::_('index.php?option=com_ars&view=release&id='.$item->release->id.'&Itemid=' . $Itemid);

// Correct title for Joomla core
if($item->vgroup_id == 1) {
	$item->title = 'Joomla';
}
?>

<div class="well" id="<?php echo $this->escape($item->alias) ?>">
	<div class="row">
		<div class="col-7">
			<h2><?php if($item->vgroup_id == 1):?><span class="icon-joomla"></span> <?php endif;?><?php echo $this->escape($item->title); ?> <?php echo $this->escape($item->release->version) ?> <small><?php echo JHTML::_('date', $released, JText::_('j F Y')) ?></small></h2>
			<?php echo $item->description ?>
		</div>
		<div class="col-5">
			<div class="panel-group" id="accordion<?php echo($item->release->id)?>">
			<?php foreach($item->release->files as $i): ?>
				<?php echo $this->loadAnyTemplate('site:com_ars/latests/item', array('Itemid' => $Itemid, 'item' => $i)); ?>
			<?php endforeach; ?>
			</div>
		</div>
	</div>
</div>
