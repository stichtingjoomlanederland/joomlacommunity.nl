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
			$active = 'easyblog'; ?>
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
				<ul id="submenu" class="migrators">
					<?php if(Komento::joomlaVersion() <= '1.5') : ?>
					<li><a id="home" class="goback" href="<?php echo JRoute::_('index.php?option=com_komento');?>">&laquo; <?php echo JText::_( 'COM_KOMENTO_SETTINGS_TAB_BACK' ); ?></a></li>
					<?php endif; ?>
					<li><a id="easyblog"<?php echo $active == 'easyblog' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_EASYBLOG' ); ?></a></li>
					<li><a id="zoo"<?php echo $active == 'zoo' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_ZOO' ); ?></a></li>
					<li><a id="k2"<?php echo $active == 'k2' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_K2' ); ?></a></li>
					<li><a id="slicomments"<?php echo $active == 'slicomments' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_SLICOMMENTS' ); ?></a></li>
					<li><a id="jcomments"<?php echo $active == 'jcomments' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_JCOMMENTS' ); ?></a></li>
					<li><a id="jacomment"<?php echo $active == 'jacomment' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_JACOMMENT' ); ?></a></li>
					<li><a id="cjcomment"<?php echo $active == 'cjcomment' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_CJCOMMENT' ); ?></a></li>
					<li><a id="rscomments"<?php echo $active == 'rscomments' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_RSCOMMENTS' ); ?></a></li>
					<li><a id="custom"<?php echo $active == 'custom' ? ' class="active"' :'';?>><?php echo JText::_( 'COM_KOMENTO_MIGRATORS_TAB_CUSTOM' ); ?></a></li>
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
