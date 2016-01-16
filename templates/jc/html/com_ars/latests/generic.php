<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

?>
<?php if (empty($this->items)): ?>
<p class="muted ars-no-items">
	<?php echo JText::_('ARS_NO_CATEGORIES'); ?>
</p>
<?php else:?>

<?php foreach($this->vgroups as $vgroup): ?>
<?php if ($vgroup->numitems[$renderSection] == 0) {
	continue;
} ?>

<?php if ($this->cparams->get('show_page_heading', 1)): ?>
<h1><?php echo $this->escape($this->cparams->get('page_heading')); ?></h1>
<?php elseif($vgroup->title): ?>
<h1><?php echo $vgroup->title; ?></h1>
<?php endif; ?>
<?php if ($vgroup->description): ?>
<p class="lead"><?php echo strip_tags($vgroup->description); ?></p>
<?php endif; ?>

<?php foreach($this->items[$renderSection] as $id => $item): ?>
<?php if($item->vgroup_id != $vgroup->id) continue;?>
<?php if (!empty($item->release) && !empty($item->release->files)): ?>
<?php echo $this->loadAnyTemplate('site:com_ars/latest/category', array('id' => $id, 'item' => $item, 'Itemid' => $this->Itemid)); ?>
<?php endif; ?>
<?php endforeach; ?>

<?php endforeach; ?>
<?php endif; ?>
