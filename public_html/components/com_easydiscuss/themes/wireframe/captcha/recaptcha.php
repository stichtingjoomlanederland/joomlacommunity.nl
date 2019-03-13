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
<div class="ed-recaptcha-wrapper">
	<?php if (!$invisible) { ?>
		<div id="recaptcha_<?php echo $recaptchaUid;?>" data-ed-recaptcha-item></div>
	<?php } ?>

	<?php if ($invisible) { ?>
		<div class="g-recaptcha" data-sitekey="<?php echo $key;?>" data-badge="inline" data-size="invisible" data-ed-recaptcha-invisible data-callback="getResponse"></div>
		<input type="hidden" name="g-recaptcha-response" value="" data-ed-recaptcha-response />
	<?php } ?>
</div>