<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' );
?>

<?php if ($this->config->file_path == 1) { ?>
	<ol class="breadcrumb well">
		<?php if (empty($this->navigation)) { ?>
		<li class="active"><?php echo JText::_('COM_RSFILES_HOME'); ?></li>
		<?php } else { ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_rsfiles'.$this->itemid); ?>"><?php echo JText::_('COM_RSFILES_HOME'); ?></a>
		</li>
		<?php end($this->navigation); ?>
		<?php $last_item_key = key($this->navigation); ?>
		<?php foreach ($this->navigation as $key => $element) { ?>
		<?php if ($key != $last_item_key) { ?>
		<li>
			<a href="<?php echo JRoute::_('index.php?option=com_rsfiles&folder='.rsfilesHelper::encode($element->fullpath).$this->itemid); ?>"><?php echo $element->name; ?></a>
		</li>
		<?php } else { ?>
		<li class="active">
			<?php echo $element->name; ?>
		</li>
		<?php } ?>
		<?php } ?>
		<?php } ?>
	</ol>
<?php } ?>