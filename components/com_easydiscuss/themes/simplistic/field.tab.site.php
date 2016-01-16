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

$view 	= JRequest::getVar( 'view', 'post' );
$app 	= JFactory::getApplication();

?>
<?php if( $view == 'ask' && $system->config->get( 'tab_site_question') || $view == 'post' && $system->config->get( 'tab_site_reply') || $app->isAdmin() ){ ?>
<li>
	<a href="#siteTab-<?php echo $composer->id; ?>" data-foundry-toggle="tab"><i class="icon-key"></i> <?php echo JText::_( 'COM_EASYDISCUSS_TAB_SITE_DETAILS' ); ?></a>
</li>
<?php } ?>
