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

$sections = array(
	'sql' => 'INSTALLATION_INITIALIZING_DB',
	'admin' => 'INSTALLATION_INITIALIZING_ADMIN',
	'site' => 'INSTALLATION_INITIALIZING_SITE',
	'languages' => 'INSTALLATION_INITIALIZING_LANGUAGES',
	'media' => 'INSTALLATION_INITIALIZING_MEDIA',
	'syncdb' => 'INSTALLATION_INITIALIZING_DB_SYNCHRONIZATION',
	'postinstall' => 'INSTALLATION_POST_INSTALLATION_CLEANUP'
);
?>
<?php foreach ($sections as $key => $value) { ?>
<li class="pp-install-logs__item" data-progress-<?php echo $key;?>>
	<div class="pp-install-logs__title">
		<?php echo t($value);?>
	</div>

	<?php include(__DIR__ . '/log.state.php'); ?>
</li>
<?php } ?>