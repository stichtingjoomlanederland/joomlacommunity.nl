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
<div id="ed" class="ed-mod ed-mod--search <?php echo $lib->getModuleWrapperClass();?>" data-mod-discuss-search>
	<div class="ed-mod-card">
		<div class="ed-mod-card__body">
			<form action="<?php echo EDR::_('view=index');?>" method="post" name="discuss-search">
				<div class="t-d--flex t-align-items--c sm:t-flex-direction--c sm:t-w--100">
					<div class="t-flex-grow--1 t-min-width--0 lg:t-pr--md sm:t-w--100">
						<div class="o-media">
							<div class="o-media__image sm:t-d--none">
								<?php echo $lib->html('user.avatar', $profile, array('size' => 'md')); ?>
							</div>
							<div class="o-media__body">
								<div class="o-input-group">
									<input type="text" value="" name="query" placeholder="<?php echo JText::_('MOD_EASYDISCUSS_SEARCH_PLACEHOLDER' , true);?>" class="o-form-control">
									<button class="o-btn o-btn--default-o" type="button" ><?php echo JText::_('MOD_EASYDISCUSS_SEARCH_BUTTON');?></button>
								</div>
								<input type="hidden" name="option" value="com_easydiscuss" />
								<input type="hidden" name="view" value="search" />
							</div>
						</div>
					</div>
					<?php if ($params->get('showaskbutton')) { ?>
						<div class="sm:t-mt--md lg:t-ml--auto sm:t-d--block sm:t-w--100">
							<a href="<?php echo EDR::getAskRoute(); ?>" class="o-btn o-btn--primary sm:t-d--block t-text--truncate"><?php echo JText::_('MOD_EASYDISCUSS_SEARCH_ASK_BUTTON');?></a>
						</div>
					<?php } ?>
				</div>
			</form>
		</div>
	</div>
</div>
<script type="text/javascript">
	// Injecting is-mobile to make sure the search form display correctly on small space.
	ed.require(['edq', 'easydiscuss'], function($, EasyDiscuss) {
		var wrapper = $('[data-mod-discuss-search]');
		var width = wrapper.width();
		var minWidth = 458;

		if (width < minWidth && !wrapper.hasClass("is-mobile")) {
			wrapper.addClass('is-mobile').removeClass('is-desktop');
		}
	});
</script>