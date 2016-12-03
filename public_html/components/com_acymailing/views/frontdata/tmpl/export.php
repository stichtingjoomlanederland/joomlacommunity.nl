<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	5.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2016 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><fieldset id="acy_data_export_menu">
	<div class="toolbar" id="acytoolbar" style="float: right;">
		<table><tr>
		<td id="acybutton_data_export"><a onclick="javascript:submitbutton('doexport'); return false;" href="#" ><span class="icon-32-acyexport" title="<?php echo JText::_('ACY_EXPORT'); ?>"></span><?php echo JText::_('ACY_EXPORT'); ?></a></td>
		<?php $tmpl = JRequest::getCmd('tmpl');
		if(empty($tmpl) || $tmpl != 'component'){ ?>
		<td id="acybutton_data_cancel"><a href="<?php echo JRoute::_('index.php?option=com_acymailing&ctrl=frontsubscriber')?>" ><span class="icon-32-cancel" title="<?php echo JText::_('ACY_CANCEL'); ?>"></span><?php echo JText::_('ACY_CANCEL'); ?></a></td>
		<?php } ?>
		</tr></table>
	</div>
	<div class="acyheader" style="float: left;"><h1><?php echo JText::_('ACY_EXPORT'); ?></h1></div>
</fieldset>
<?php
include(ACYMAILING_BACK.'views'.DS.'data'.DS.'tmpl'.DS.'export.php');
