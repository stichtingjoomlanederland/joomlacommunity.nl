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
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ADVANCE_INFO' ); ?></div>
			<div class="panel-body">
				<p class="warning"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ADVANCE_WARNING' ) ;?></p>
			</div>
		</fieldset>

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TRIGGERS' ); ?></div>
			<div class="panel-body">
				<!-- Trigger method -->
				<?php $options = array();
					$options[] = array( 'none', 'COM_KOMENTO_SETTINGS_TRIGGERS_METHOD_NONE' );
					$options[] = array( 'component', 'COM_KOMENTO_SETTINGS_TRIGGERS_METHOD_COMPONENT_PLUGIN' );
					$options[] = array( 'joomla', 'COM_KOMENTO_SETTINGS_TRIGGERS_METHOD_JOOMLA_PLUGIN' );

					echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_TRIGGERS_METHOD', 'trigger_method', 'dropdown', $options );
				?>
			</div>
		</fieldset>
	</div>
</div>