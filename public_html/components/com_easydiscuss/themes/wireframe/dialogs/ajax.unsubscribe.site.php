<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<p>
	<?php echo JText::_( 'COM_EASYDISCUSS_UNSUBSCRIBE_SITE_DESCRIPTION' );?>
</p>
<p style="margin-top: 30px;">
	<span class="label label-important"><?php echo JText::_( 'COM_EASYDISCUSS_NOTE' );?></span>: <?php echo JText::_( 'COM_EASYDISCUSS_UNSUBSCRIBE_SITE_UPDATE' ); ?>
	<a href="javascript:void(0);" onclick="disjax.loadingDialog();disjax.load('index', 'ajaxSubscribe', 'site', '$cid')"><?php echo JText::_( 'COM_EASYDISCUSS_UPDATE_SUBSCRIPTION_CLICK_HERE' );?></a>.
</p>
