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
defined('_JEXEC') or die('Restricted access'); ?>
<div class="row">
	<div class="col-md-6">
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_STICKIES' ); ?></div>
			<div class="panel-body">
				<!-- Enable Stickies -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_STICKIES_ENABLE', 'enable_stickies' ); ?>

				<!-- Max Stickies -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_MAX_STICKIES', 'max_stickies', 'input', '1' ); ?>
			</div>
		</fieldset>
	</div>

	<div class="col-md-6">
		<fieldset class="panel form-horizontal">
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_LAYOUT_LOVIES' ); ?></div>
			<div class="panel-body">
				<!-- Enable Lovies -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LOVIES_ENABLE', 'enable_lovies' ); ?>

				<!-- Lovies threshold -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_LOVIES_THRESHOLD', 'minimum_likes_lovies', 'input' ); ?>

				<!-- Max Stickies -->
				<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_MAX_LOVIES', 'max_lovies', 'input' ); ?>
			</div>
		</fieldset>
	</div>
</div>

