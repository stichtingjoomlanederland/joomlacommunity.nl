<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<style type="text/css">
:root {--si-root-font-size: <?php echo $this->config->get('layout_rem') ? '1rem' : '16px';?>; }
</style>

<div id="ed" class="type-component si-theme--<?php echo $this->config->get('layout_darkmode') ? 'dark' : 'light';?>
	ed-responsive
	<?php echo $categoryClass;?>
	<?php echo $suffix; ?>
	<?php echo 'view-' . $view; ?>
	<?php echo 'layout-' . $layout; ?>
	<?php echo $rtl ? ' is-rtl' : '';?>
	<?php echo $this->responsiveClass();?>
	"
	data-ed-wrapper
>
	<?php if ($heading) { ?>
	<div class="page-header">
		<h1>
			<?php echo $this->html('string.escape', $heading); ?>
		</h1>
	</div>
	<?php } ?>

	<?php echo ED::renderModule('easydiscuss-start'); ?>

	<?php echo $toolbar; ?>

	<?php if ($messageObject) { ?>
	<div class="o-alert o-alert--<?php echo $messageObject->type;?> t-mb--md">
		<?php echo $messageObject->message; ?>
	</div>
	<?php } ?>
	
	<div class="ed-container">
		<div class="ed-container__sidebar t-hidden">
		</div>
		<div class="ed-container__content">
			<?php echo $contents; ?>
		</div>
	</div>
	
	<div class="t-mt--lg">
		<?php echo ED::renderModule('easydiscuss-end'); ?>
	</div>

	<?php if ($this->config->get('main_copyright_link_back')) { ?>
		<?php echo DISCUSS_POWERED_BY; ?>
	<?php } ?>

	<input type="hidden" class="easydiscuss-token" value="<?php echo ED::getToken();?>" data-ed-token />
	<input type="hidden" data-ed-ajax-url value="<?php echo $ajaxUrl;?>" />
</div>
