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

$availableComponents = Komento::getHelper( 'components' )->getAvailableComponents();
?>

<div class="row">
	<div class="col-md-6">

		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_MIGRATE_SETTINGS' ); ?></div>
			<div class="panel-body">
				<!-- Trigger method -->
				<tr>
					<td class="key"><span><?php echo JText::_( 'COM_KOMENTO_SETTINGS_MIGRATE_SETTINGS_COMPONENTS' ); ?></span></td>
					<td>
						<select id="componentSettings">
							<?php foreach($availableComponents as $availableComponent) { ?>
								<?php if ($availableComponent != $this->component) { ?>
									<option value="<?php echo $availableComponent; ?>"><?php echo Komento::loadApplication( $availableComponent )->getComponentName();; ?></option>
								<?php } ?>
							<?php } ?>
						</select>
					</td>
				</tr>
			</div>
			<div class="panel-foot">
				<a href="javascript:void(0);" data-current-component="<?php echo $this->component;?>" class="migrateSettingsButton button"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_MIGRATE_SETTINGS_START' ); ?></a>
			</div>
			<div class="col-md-6 hide" id="SettingsMigratorProgress">
				Success
			</div>
		</fieldset>
	</div>
</div>