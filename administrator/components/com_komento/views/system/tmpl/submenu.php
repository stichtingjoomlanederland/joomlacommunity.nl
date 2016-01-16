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
			$active = 'profile'; ?>
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
		<div class="submenu-box">
			<div class="submenu-pad">
				<ul id="submenu" class="system">
				    <?php if(Komento::joomlaVersion() <= '1.5') : ?>
		   			<li><a id="home" class="goback" href="<?php echo JRoute::_('index.php?option=com_komento');?>">&laquo; <?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_BACK' ); ?></a></li>
		   			<?php endif; ?>
					<li><a id="profile"<?php echo $active == 'profile' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_PROFILE' ); ?></a></li>
					<li><a id="schema"<?php echo $active == 'schema' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_SCHEMA' ); ?></a></li>
					<li><a id="database"<?php echo $active == 'database' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_DATABASE' ); ?></a></li>
					<li><a id="system"<?php echo $active == 'system' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_SYSTEM' ); ?></a></li>
					<li><a id="advance"><?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_ADVANCE' ); ?></a></li>
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
