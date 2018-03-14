<?php
/**
* @package RSComments!
* @copyright (C) 2015 www.rsjoomla.com
* @license GPL, http://www.gnu.org/licenses/gpl-2.0.html
*/

defined('_JEXEC') or die('Restricted access'); ?>

<div class="alert" id="subscriber-message" style="display: none;"></div>

<div class="well">
	<div class="row-fluid">
		<div class="control-group">
			<div class="control-label">
				<label for="name"><?php echo JText::_('COM_RSCOMMENTS_NAME'); ?></label>
			</div>
			<div class="controls">
				<input id="subscriber-name" type="text" name="name" value="" size="40" class="input-xxlarge required" /> 
			</div>
		</div>
	</div>
	
	<div class="row-fluid">
		<div class="control-group">
			<div class="control-label">
				<label for="email"><?php echo JText::_('COM_RSCOMMENTS_EMAIL'); ?></label>
			</div>
			<div class="controls">
				<input id="subscriber-email" type="text" name="email" value="" size="40" class="input-xxlarge required validate-email" /> 
			</div>
		</div>
	</div>
</div>
<button type="button" id="rscomm_subscribe" onclick="rscomments_subscribe();" style="display:none">&nbsp;</button>