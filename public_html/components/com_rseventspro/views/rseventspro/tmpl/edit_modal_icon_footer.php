<?php
/**
* @package RSEvents!Pro
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/
defined( '_JEXEC' ) or die( 'Restricted access' ); ?>

<label for="aspectratio" class="pull-left" style="display: none" id="aspectratiolabel">
	<input type="checkbox" id="aspectratio" name="aspectratio" value="1" style="margin:0;" /> <?php echo JText::_('COM_RSEVENTSPRO_FREE_ASPECT_RATIO'); ?>
</label>
<button class="btn btn-primary" type="button" id="rsepro-crop-icon-btn" style="display: none;"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CROP'); ?></button>
<button class="btn btn-danger" type="button" id="rsepro-delete-icon-btn" style="display: none;"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_DELETE'); ?></button>
<button class="btn" data-dismiss="modal" aria-hidden="true"><?php echo JText::_('COM_RSEVENTSPRO_GLOBAL_CANCEL'); ?></button>