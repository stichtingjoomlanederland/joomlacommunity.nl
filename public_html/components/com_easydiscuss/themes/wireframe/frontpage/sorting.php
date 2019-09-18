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
<?php if ($view == 'index' && $this->config->get('layout_post_types')) { ?>
	<select data-index-status-filter>	
		<option value="" <?php echo $activeStatus == '' ? ' selected="true"' : '';?> data-link="<?php echo EDR::_($sortBaseUrl . '&sort='. $activeSort . '&status=');?>"><?php echo JText::_('COM_ED_POST_STATUS_FRONTEND_ALL');?></option>	
		<option value="onhold" <?php echo $activeStatus == 'onhold' ? ' selected="true"' : '';?> data-link="<?php echo EDR::_($sortBaseUrl . '&sort='. $activeSort .  '&status=onhold');?>"><?php echo JText::_('COM_ED_POST_STATUS_FRONTEND_ON_HOLD');?></option>
		<option value="accepted" <?php echo $activeStatus == 'accepted' ? ' selected="true"' : '';?> data-link="<?php echo EDR::_($sortBaseUrl . '&sort='. $activeSort .  '&status=accepted');?>"><?php echo JText::_('COM_ED_POST_STATUS_FRONTEND_ACCEPTED');?></option>
		<option value="workingon" <?php echo $activeStatus == 'workingon' ? ' selected="true"' : '';?> data-link="<?php echo EDR::_($sortBaseUrl . '&sort='. $activeSort .  '&status=workingon');?>"><?php echo JText::_('COM_ED_POST_STATUS_FRONTEND_WORKING_ON');?></option>
		<option value="rejected" <?php echo $activeStatus == 'rejected' ? ' selected="true"' : '';?> data-link="<?php echo EDR::_($sortBaseUrl . '&sort='. $activeSort .  '&status=rejected');?>"><?php echo JText::_('COM_ED_POST_STATUS_FRONTEND_REJECTED');?></option>
	</select>
<?php } ?>
<select data-index-sort-filter>
	<option value="latest" <?php echo $activeSort == 'latest' || $activeSort == '' ? ' selected="true"' : '';?> data-link="<?php echo EDR::_($sortBaseUrl . '&sort=latest' . '&status=' . $activeStatus);?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_LATEST');?></option>
	<option value="popular" <?php echo $activeSort == 'popular' ? ' selected="true"' : '';?> data-link="<?php echo EDR::_($sortBaseUrl . '&sort=popular' . '&status=' . $activeStatus);?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_POPULAR');?></option>
	<option value="title" <?php echo $activeSort == 'title' ? ' selected="true"' : '';?> data-link="<?php echo EDR::_($sortBaseUrl . '&sort=title' . '&status=' . $activeStatus);?>"><?php echo JText::_('COM_EASYDISCUSS_SORT_TITLE');?></option>
</select>