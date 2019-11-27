<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2011 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<div id="ed" class="ed-mod m-search <?php echo $params->get('moduleclass_sfx') ?>">
	<form action="<?php echo EDR::_('index.php?option=com_easydiscuss&view=index');?>" method="post" name="discuss-search">
		<div class="input-group t-mb--lg">
			<input type="text" value="" name="query" placeholder="<?php echo JText::_('MOD_EASYDISCUSS_SEARCH_PLACEHOLDER' , true);?>" class="form-control">
			<span class="input-group-btn">
				<button class="btn btn-default"><?php echo JText::_('MOD_EASYDISCUSS_SEARCH_BUTTON');?></button>
			</span>
		</div>
		
		<input type="hidden" name="option" value="com_easydiscuss" />
		<input type="hidden" name="view" value="search" />

		<?php if ($params->get('showaskbutton')) { ?>
			<a class="btn btn-primary btn-block t-lg-mt--lg" href="<?php echo EDR::getAskRoute(); ?>"><?php echo JText::_('MOD_EASYDISCUSS_SEARCH_ASK_BUTTON');?></a>
		<?php } ?>
	</form>
</div>