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
<script type="text/javascript">
Komento.ready(function($){
	$(".si_accordion > h3:first").addClass("active");
	$(".si_accordion > h3").siblings("div").hide();
	$(".si_accordion > h3:first + div").show();

	$(".si_accordion > h3").click(function(){
	  $(this).next("div").toggle().siblings("div").hide();
	  $(this).toggleClass("active");
	  $(this).siblings("h3").removeClass("active");
	});
})
</script>
<div class="si_accordion">
	<h3><div><?php echo JText::_('COM_KOMENTO_QUICKGUIDE_VERSION_TITLE'); ?></div></h3>
	<div class="user-guide" data-komento-version>
		<div class="checking_version">
			<?php echo JText::_( 'COM_KOMENTO_QUICKQUIDE_VERSION_CHECKING' ); ?>
		</div>
	</div>

	<h3><div><?php echo JText::_('COM_KOMENTO_QUICKGUIDE_STATS_TITLE'); ?></div></h3>
	<div class="user-guide">
		<?php echo $this->loadTemplate( 'stats' );?>
	</div>

	<h3><div><?php echo JText::_('COM_KOMENTO_QUICKGUIDE_ABOUT_TITLE'); ?></div></h3>
	<div class="user-guide">
		<?php echo $this->loadTemplate( 'about' );?>
	</div>
</div>
