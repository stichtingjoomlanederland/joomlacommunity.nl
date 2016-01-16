<?php
/**
* @package		Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<div class="row-fluid">
	<div class="span12">
		<div class="span6">
			<fieldset class="adminform">
			<legend><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_LOGGING' ); ?></legend>
			<table class="admintable" cellspacing="1">
				<tbody>

					<!-- Log comment -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_LOGGING_COMMENT', 'activities_comment' ); ?>

					<!-- Log reply -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_LOGGING_REPLY', 'activities_reply' ); ?>

					<!-- Log like -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_LOGGING_LIKE', 'activities_like' ); ?>
				</tbody>
			</table>
			</fieldset>
		</div>

		<div class="span6">
		</div>
	</div>
</div>
