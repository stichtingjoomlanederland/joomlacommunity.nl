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
			<?php echo JText::_('COM_RSFILES_REPORT_MESSAGE'); ?> <br />
			<textarea name="jform[report]" id="report" class="input-large" rows="7" cols="40"></textarea>
			<div class="clearfix"></div>
			<div style="text-align: right;">
				<button type="submit" class="btn btn-primary" onclick="return validate_report();"><?php echo JText::_('COM_RSFILES_SEND'); ?></button> 
				<button type="button" class="btn" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('COM_RSFILES_CANCEL'); ?></button>
			</div>
		</div>
		
		<?php echo JHTML::_( 'form.token' ); ?>
		<input type="hidden" name="task" value="rsfiles.report" />
		<input type="hidden" name="jform[path]" value="<?php echo urldecode($this->app->input->getString('path','')); ?>" />
	</form>
</div>