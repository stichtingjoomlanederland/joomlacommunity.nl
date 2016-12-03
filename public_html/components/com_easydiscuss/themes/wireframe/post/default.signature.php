<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
$signature = trim($post->getOwner()->getSignature());
?>

<?php if ($this->config->get('main_signature_visibility') && !empty($signature) && !$post->isAnonymous()) { ?>
<div class="ed-post-signature">
	<?php if (ED::acl($post->getOwner()->id)->allowed('show_signature')) { ?>
			<div class="ed-signature"><?php echo $signature; ?>
            </div>
	<?php } ?>
</div>
<?php } ?>
