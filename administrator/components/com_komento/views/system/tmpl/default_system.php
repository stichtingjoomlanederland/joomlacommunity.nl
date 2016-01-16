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
				<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM' ); ?></div>
				<div class="panel-body">

							<!-- System environment -->
							<?php $options = array();
								$options[] = array( 'static', JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_STATIC' ) );
								$options[] = array( 'optimized', JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_OPTIMIZED' ) );
								$options[] = array( 'development', JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_DEVELOPMENT' ) );
								echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT', 'komento_environment', 'dropdown', $options ); ?>

							<div class="form-group">
								<div class="col-md-7 col-md-offset-5">
									<p><strong><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_STATIC' ); ?></strong> - <?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_STATIC_DESC' ); ?></p>
									<p><strong><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_OPTIMIZED' ); ?></strong> - <?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_OPTIMIZED_DESC' ); ?></p>
									<p><strong><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_DEVELOPMENT' ); ?></strong> - <?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_ENVIRONMENT_DEVELOPMENT_DESC' ); ?></p>
								</div>
							</div>

							<!-- JavaScript compression -->
							<?php $options = array();
								$options[] = array( 'compressed', JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_COMPRESSED' ) );
								$options[] = array( 'uncompressed', JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_UNCOMPRESSED' ) );

								echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE', 'komento_mode', 'dropdown', $options ); ?>

							<div class="form-group">
								<div class="col-md-7 col-md-offset-5">
									<p><strong><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_COMPRESSED' ); ?></strong> - <?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_COMPRESSED_DESC' ); ?></p>
									<p><strong><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_UNCOMPRESSED' ); ?></strong> - <?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_UNCOMPRESSED_DESC' ); ?></p>
									<p><?php echo JText::_( 'COM_KOMENTO_SETTINGS_SYSTEM_MODE_DEVELOPMENT_DESC' ); ?></p>
								</div>
							</div>
				</div>
			</fieldset>
		</div>
</div>


