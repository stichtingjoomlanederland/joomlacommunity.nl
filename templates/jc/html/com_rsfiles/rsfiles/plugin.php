<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
?>
<?php if (!empty($this->items)) : ?>
	<div class="list-group list-group-flush">
		<?php foreach ($this->items as $i => $item) : ?>
				<?php if ($item->type != 'folder') : ?>
					<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&layout=download&path='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" class="list-group-item">
						<i class="rsicon-file"></i> <?php echo (!empty($item->filename) ? $item->filename : $item->name); ?>
					</a>
				<?php else: ?>
					<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&folder='.rsfilesHelper::encode($item->fullpath).$this->itemid); ?>" class="list-group-item">
						<i class="rsicon-folder"></i> <?php echo (!empty($item->filename) ? $item->filename : $item->name); ?>
					</a>
				<?php endif; ?>
		<?php endforeach; ?>
	</div>
<?php endif; ?>