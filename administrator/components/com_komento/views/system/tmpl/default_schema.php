<?php
/**
* @package      Komento
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>

<div class="row">
	<div class="col-md-6">
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SCHEMA' ); ?></div>
			<div class="panel-body">
				<p><?php echo JText::sprintf( 'COM_KOMENTO_SETTINGS_SCHEMA_INFO', '<a href="http://schema.org/" target="_blank">schema.org</a>' ); ?></p>
				<!-- Enable Schema -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SCHEMA_ENABLED', 'enable_schema' ); ?>
			</div>
		</fieldset>
	</div>
</div>

