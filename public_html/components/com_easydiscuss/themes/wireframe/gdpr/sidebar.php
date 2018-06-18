<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2018 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<?php foreach ($sections as $section) { ?>
<div class="gdpr-main-title">
	<?php echo $section->title; ?>
</div>

<ul class="gdpr-nav">
	<?php if ($section->tabs) { ?>
		<?php foreach ($section->tabs as $tab) { ?>
			<li class="<?php echo $active == $tab->key ? 'is-active' : '';?>">
				<a href="<?php echo $tab->getLink($isRoot); ?>"><?php echo $tab->title; ?></a>
			</li>
		<?php } ?>
	<?php } ?>
</ul>
<?php } ?>