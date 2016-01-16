<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

$signature 	= trim( $signature );
?>
<?php if( $system->config->get( 'main_signature_visibility' ) && !empty( $signature ) ){ ?>
	<?php if( DiscussHelper::getHelper('ACL')->allowed('show_signature') ){ ?>
	<div class="discuss-action-options">
		<div class="discuss-signature fs-11"><?php echo DiscussHelper::bbcodeHtmlSwitcher( $signature, 'signature', false ); ?></div>
	</div>
	<?php } ?>
<?php } ?>
