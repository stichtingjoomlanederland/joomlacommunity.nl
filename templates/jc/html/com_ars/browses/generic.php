<?php
/**
 * @package AkeebaReleaseSystem
 * @copyright Copyright (c)2010-2013 Nicholas K. Dionysopoulos
 * @license GNU General Public License version 3, or later
 */

defined('_JEXEC') or die();

?>

<?php if (!empty($title)): ?>
<div class="page-header">
	<h2><?php echo JText::_($title) ?></h2>
</div>
<?php endif; ?>

<?php if (empty($this->items)): ?>
<p class="muted ars-no-items">
	<?php echo JText::_('ARS_NO_CATEGORIES'); ?>
</p>
<?php else:?>
<div class="row">
	<?php $i=1;?>
	<?php foreach($this->vgroups as $vgroup): ?>
	<?php if ($vgroup->numitems[$renderSection] == 0) {
		continue;
	} 
	?>
	<div class="content-4">
		<div class="panel panel-downloads">
       		<div class="panel-heading"><?php echo $vgroup->title; ?></div>
       		<div class="list-group list-group-flush">
       			<?php foreach($this->items[$renderSection] as $id => $item): ?>
				<?php if($item->vgroup_id != $vgroup->id) continue;?>
				<?php echo $this->loadAnyTemplate('site:com_ars/browses/category', array('id' => $id, 'item' => $item, 'Itemid' => $this->Itemid)); ?>
				<?php endforeach; ?>
			</div>
		</div>
	</div>
	<?php if($i == 3):?>
	</div><div class="row">
	<?php 
		$i=0;
		endif;
	?>
	<?php $i++;?>
	<?php endforeach; ?>
	<?php endif; ?>
</div>

<script type="text/javascript">
    $(function () {
        $("[data-toggle='tooltip']").tooltip();
    });
</script>