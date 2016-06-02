<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

/** @var  \Akeeba\Backup\Admin\View\RemoteFiles\Html  $this */
?>

<h2><?php echo \JText::_('COM_AKEEBA_REMOTEFILES'); ?></h2>

<?php if(empty($this->actions)): ?>
<div class="alert alert-error">
	<h3 class="alert-heading">
		<?php echo \JText::_('COM_AKEEBA_REMOTEFILES_ERR_NOTSUPPORTED_HEADER'); ?>
	</h3>
	<p>
		<?php echo \JText::_('COM_AKEEBA_REMOTEFILES_ERR_NOTSUPPORTED'); ?>
	</p>
</div>
<?php endif; ?>

<?php if ( ! (empty($this->actions))): ?>
<div id="cpanel">
	<?php foreach($this->actions as $action): ?>
	<?php if($action['type'] == 'button'): ?>
	<button class="btn <?php echo $action['class']; ?>" onclick="window.location = '<?php echo addslashes($action['link']); ?>'; return false;">
		<span class="<?php echo $action['icon']; ?>"></span>
		<?php echo $this->escape($action['label']); ?>

	</button>
	<?php endif; ?>
	<?php endforeach; ?>
</div>
<div class="clearfix"></div>

<h3>
	<?php echo \JText::_('COM_AKEEBA_REMOTEFILES_LBL_DOWNLOADLOCALLY'); ?>
</h3>
<?php $items = 0 ?>
<?php foreach($this->actions as $action): ?>
<?php if($action['type'] == 'link'): ?>
	<?php $items++ ?>
	<a href="<?php echo $this->escape($action['link']); ?>" class="btn btn-mini">
		<span class="<?php echo $action['icon']; ?>"></span>
		<?php echo $this->escape($action['label']); ?>

	</a>
<?php endif; ?>
<?php endforeach; ?>

<?php if ( ! ($items)): ?>
<p class="alert">
	<?php echo \JText::_('COM_AKEEBA_REMOTEFILES_LBL_NOTSUPPORTSLOCALDL'); ?>
</p>
<?php endif; ?>

<?php endif; ?>