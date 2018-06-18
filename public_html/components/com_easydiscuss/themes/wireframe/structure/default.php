<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php echo $jsToolbar; ?>

<script type="text/javascript">
document.addEventListener('DOMContentLoaded', function(){
	if (window.innerWidth < 640 && window.innerWidth > 481) {
		var wrapper = document.querySelectorAll('.ed-responsive');
		for(var i = 0; i < wrapper.length; i++) {
			wrapper[i].classList.add('w640');
		}
	}
	if (window.innerWidth < 480 ) {
		var wrapper = document.querySelectorAll('.ed-responsive');
		for(var i = 0; i < wrapper.length; i++) {
			wrapper[i].classList.add('w480');
		}
	}
});
</script>
<div id="ed" class="type-component
	ed-responsive
	<?php echo $categoryClass;?>
	<?php echo $suffix; ?>
	<?php echo 'view-' . $view; ?>
	<?php echo 'layout-' . $layout; ?>
	<?php echo $rtl ? ' is-rtl' : '';?>"
	data-ed-wrapper
>
	<?php if ($miniheader) { ?>
	<div id="es" class="es<?php echo (ES::responsive()->isMobile()) ? ' is-mobile' : ' is-desktop'; ?>">
		<?php echo $miniheader; ?>
	</div>
	<?php } ?>

	<?php echo $toolbar; ?>

	<?php echo $contents; ?>

	<?php if ($this->config->get('main_copyright_link_back')) { ?>
		<?php echo DISCUSS_POWERED_BY; ?>
	<?php } ?>

	<input type="hidden" class="easydiscuss-token" value="<?php echo ED::getToken();?>" data-ed-token />
	<input type="hidden" data-ed-ajax-url value="<?php echo $ajaxUrl;?>" />
</div>
