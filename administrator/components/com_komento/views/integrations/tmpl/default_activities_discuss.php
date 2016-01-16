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
			<div class="panel-head"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_DISCUSS' ); ?></div>
			<div class="panel-body">
				<?php if( Komento::getHelper( 'components' )->isInstalled( 'com_easydiscuss' ) ) { ?>
					<p><?php echo JText::_( 'COM_KOMENTO_SETTINGS_ACTIVITIES_DISCUSS_INSTRUCTIONS' ); ?></p>
					<!-- Enable Discuss Points -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_DISCUSS_POINTS', 'enable_discuss_points' ); ?>

					<!-- Enable Discuss Log -->
					<?php echo $this->renderSetting( 'COM_KOMENTO_SETTINGS_ACTIVITIES_ENABLE_DISCUSS_LOG', 'enable_discuss_log' ); ?>
				<?php } else { ?>
					<img src="<?php echo JURI::root(); ?>administrator/components/com_komento/assets/images/easydiscuss.png" />
					<?php echo JText::_( 'COM_KOMENTO_WHAT_IS_EASYDISCUSS' ); ?>
					<a target="_blank" href="http://www.stackideas.com/easydiscuss.html"><?php echo JText::_( 'COM_KOMENTO_GET_EASYDISCUSS' ); ?></a>
				<?php } ?>
			</div>
		</fieldset>
	</div>
</div>
