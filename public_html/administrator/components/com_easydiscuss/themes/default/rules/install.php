<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<form name="adminForm" id="adminForm" enctype="multipart/form-data" method="post">
<div class="row-fluid">
	<div class="span12 panel-title">
		<div class="panel">
			<div class="panel-body">
				<p><?php echo JText::_('COM_EASYDISCUSS_BADGES_RULE_INSTALL_INFO'); ?></p>
				<fieldset style="padding: 15px;">
					<legend><?php echo JText::_('COM_EASYDISCUSS_BADGES_INSTALL_RULE'); ?></legend>
					<input type="file" name="rule" size="50" />
					<button class="btn btn-sm btn-primary t-lg-mt--lg installUpload"><?php echo JText::_('COM_EASYDISCUSS_UPLOAD_AND_INSTALL')?> &raquo;</button>
				</fieldset>
			</div>
		</div>	
	</div>
</div>

<input type="hidden" name="option" value="com_easydiscuss" />
<input type="hidden" name="controller" value="rules" />
<input type="hidden" name="task" value="install" />
<?php echo JHTML::_('form.token'); ?>
</form>
