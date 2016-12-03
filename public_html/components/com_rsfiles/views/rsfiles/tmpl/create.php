<?php
/**
* @package RSFiles!
* @copyright (C) 2010-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<div class="rsfiles-layout">
	<form action="<?php echo JRoute::_('index.php?option=com_rsfiles'); ?>" method="post" name="adminForm" id="adminForm">
		<div class="well">
			<div class="span12">
				<?php echo JText::_('COM_RSFILES_FOLDER'); ?> <input type="text" id="folder" name="jform[folder]" value="" size="40" />
			</div>
			<br />
			<div style="text-align: right;">
				<button type="submit" class="btn btn-primary" onclick="return validate_new_folder();"><?php echo JText::_('COM_RSFILES_CREATE'); ?></button> 
				<button type="button" class="btn" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('COM_RSFILES_CANCEL'); ?></button>
			</div>
		</div>
		
		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="hidden" name="task" value="rsfiles.create" />
		<input type="hidden" name="jform[parent]" value="<?php echo $this->folder; ?>" />
		<input type="hidden" name="jform[from]" value="<?php echo $this->app->input->get('from'); ?>" />
	</form>
</div>