<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.1.10
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?><input type="hidden" name="step" value="1">
<div class="acym__walkthrough cell grid-x" id="acym__walkthrough_0">
	<div class="cell text-center grid-x">
		<h1 class="cell acym__walkthrough__title__welcome"><?php echo acym_translation('ACYM_THANKS_FOR_INSTALLING_ACYM'); ?></h1>
		<h2 class="acym__walkthrough__title__subwelcome cell"><?php echo acym_translation('ACYM_WALK_THROUGH_STEPS_TO_GET_STARTED'); ?></h2>
	</div>
	<div class="cell large-3"></div>
	<div class="acym__content cell text-center grid-x large-6 small-12 acym__walkthrough__content text-center">
		<h2 class="acym__walkthrough__title cell"><?php echo acym_translation('ACYM_ACYMAILING_NEWS_AND_COUPON_CODE'); ?></h2>
		<h3 class="acym__walkthrough__sub-title cell"><?php echo acym_translation('ACYM_DO_YOU_WANT_NEWS'); ?></h3>
		<div class="cell grid-x margin-top-2">
			<label class="cell small-9 margin-auto grid-x cell text-center"><?php echo acym_translation('ACYM_CONTACT_EMAIL') ?></label>
		</div>
		<input type="email" value="<?php echo acym_escape($data['email']); ?>" class="cell small-9 margin-auto" placeholder="<?php echo acym_escape(acym_translation('ACYM_YOUR_EMAIL')); ?>">

		<div class="cell text-center margin-bottom-1">
			<button class="button acym__walk-through__content__save large-shrink" id="acym__subscribe__news"><?php echo acym_translation('ACYM_SURE_LETS_DO_IT'); ?></button>
			<button class="cell acym__color__dark-gray acym__walk-through-1__content__later small-shrink cursor-pointer" data-task="step0"><?php echo acym_translation('ACYM_NO_THANK_YOU'); ?></button>
		</div>
	</div>
</div>

