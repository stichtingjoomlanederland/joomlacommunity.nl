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
?>
<h2 class="ed-page-title"><?php echo JText::_('COM_EASYDISCUSS_MEMBERS'); ?></h2>

<form data-user-search-form name="discuss-users-search" method="GET" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=users'); ?>" style="margin:0;">
	<div class="input-group t-lg-mb--lg">	
		<input type="text" style="position: inherit;" class="form-control"
			placeholder="<?php echo JText::_('COM_EASYDISCUSS_USERS_SEARCH_PLACEHOLDER');?>"
			name="search"
			data-user-search-text
			value="<?php echo $this->html('string.escape', $search);?>"
		/>
		<span class="input-group-btn">
			<a data-search-button href="javascript:void(0);" class="btn btn-default"><?php echo JText::_( 'COM_EASYDISCUSS_SEARCH_BUTTON' );?></a>
		</span>
	</div>
	<input type="hidden" name="option" value="com_easydiscuss" />
	<input type="hidden" name="view" value="users" />
</form>

<div class="ed-list">
	<?php if ($users) { ?>
		<?php foreach ($users as $user) { ?>
			<?php echo $this->output('site/users/item', array('user' => $user)); ?>
		<?php } ?>
	<?php } ?>

	<?php if (!$users && $search) { ?> 
	<div class="is-empty">
		<div class="o-empty">
			<div class="o-empty__content">
				<i class="o-empty__icon fa fa-search"></i>
				<div class="o-empty__text t-lg-mt--xl">
					<?php echo JText::sprintf('COM_ED_NO_USERS_FOUND_BASED_ON_SEARCH', $search);?>
				</div>
			</div>
		</div>
	</div>
	<?php } ?>
</div>

<div class="ed-pagination">
	<?php echo $pagination->getPagesLinks();?>
</div>

