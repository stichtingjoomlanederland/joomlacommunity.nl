<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2019 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<select data-index-sort-filter>
	<option value="latest" <?php echo $activeSort == 'latest' || $activeSort == '' ? ' selected="true"' : '';?> data-link="<?php echo EDR::_($sortBaseUrl . '&sort=latest');?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_LATEST');?></option>
	<option value="popular" <?php echo $activeSort == 'popular' ? ' selected="true"' : '';?> data-link="<?php echo EDR::_($sortBaseUrl . '&sort=popular' . '&label=');?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_POPULAR');?></option>
	<option value="title" <?php echo $activeSort == 'title' ? ' selected="true"' : '';?> data-link="<?php echo EDR::_($sortBaseUrl . '&sort=title' . '&label=');?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_TITLE');?></option>
</select>