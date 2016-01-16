<?php
/**
* @package      Komento
* @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

$active	= JRequest::getString( 'active' , '' ); ?>

<script type="text/javascript">
Komento.ready(function($){
	$(document).ready(function() {
		<?php if(!empty($active)) { ?>
			$$('ul#submenu li a#<?php echo $active; ?>').fireEvent('click');
		<?php } else {
			$active = 'main'; ?>
			if($('.tab[style="display: block;"]').length == 0) {
				$$('ul#submenu li a.active').fireEvent('click');
			}
		<?php } ?>
	});
});
</script>

<div id="submenu-box">
	<div class="t">
		<div class="t">
			<div class="t"></div>
		</div>
	</div>
	<div class="m">

		<div class="kmt-component-select">
			<?php
				$app		= JFactory::getApplication();
				$component	= $app->getUserStateFromRequest( 'com_komento.integrations.component' , 'component' , 'com_content' );

				// Get a list of components
				$components	= array();
				$result		= Komento::getHelper( 'components' )->getAvailableComponents();

				// @task: Translate each component with human readable name.
				foreach( $result as $item )
				{
					$components[] = JHTML::_( 'select.option', $item, Komento::loadApplication( $item )->getComponentName() );
				}

				echo JText::_( 'COM_KOMENTO_SETTINGS_SELECT_COMPONENT' ) . ': ' . JHTML::_( 'select.genericlist' , $components , 'test' , 'class="inputbox" onchange="changeComponent(this.value)"' , 'value' , 'text' , $component );
			?>
		</div>

		<div class="submenu-box">
			<div class="submenu-pad">
				<ul id="submenu" class="integrations">
					<?php if(Komento::joomlaVersion() <= '1.5') : ?>
					<li><a id="home" class="goback" href="<?php echo JRoute::_('index.php?option=com_komento');?>">&laquo; <?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_BACK' ); ?></a></li>
					<?php endif; ?>
					<li><a id="main"<?php echo $active == 'main' || $active == '' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_WORKFLOW' ); ?></a></li>
					<li><a id="antispam"<?php echo $active == 'antispam' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ANTISPAM' ); ?></a></li>
					<li><a id="layout"<?php echo $active == 'layout' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_LAYOUT' ); ?></a></li>
					<li><a id="notifications"<?php echo $active == 'notifications' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_NOTIFICATIONS' ); ?></a></li>
					<li><a id="activities"<?php echo $active == 'activities' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ACTIVITIES' ); ?></a></li>
					<!-- START: Pro Version Only -->
					<li><a id="attachment"<?php echo $active == 'attachment' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ATTACHMENT' ); ?></a></li>
					<!-- END: Pro Version Only -->
					<li><a id="advance"<?php echo $active == 'advance' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ADVANCE' ); ?></a></li>
				</ul>
				<div class="clr"></div>
			</div>
		</div>
		<div class="clr"></div>
	</div>
	<div class="b">
		<div class="b">
			<div class="b"></div>
		</div>
	</div>
</div>
