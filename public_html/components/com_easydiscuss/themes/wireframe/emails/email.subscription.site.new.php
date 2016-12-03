<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* Komento is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/

defined('_JEXEC') or die('Restricted access');
?>

<?php echo JText::_('COM_EASYDISCUSS_EMAILTEMPLATE_NEW_SUBSCRIPTION_GOOD_DAY'); ?>

<?php echo JText::sprintf('COM_EASYDISCUSS_EMAILTEMPLATE_NEW_SUBSCRIPTION', $postTitle, $postAuthor); ?>

<?php echo JText::_('COM_EASYDISCUSS_EMAILTEMPLATE_NEW_SUBSCRIPTION_POST_LINK'); ?><?php echo $postLink; ?>

<?php echo JText::_('COM_EASYDISCUSS_EMAILTEMPLATE_NEW_SUBSCRIPTION_NICE_DAY'); ?>

