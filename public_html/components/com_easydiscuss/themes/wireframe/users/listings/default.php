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
<div class="ed-users-wrapper">
	<div class="l-stack">
		<h2 class="o-title"><?php echo JText::_('COM_ED_USERS_TITLE'); ?></h2>

		<form data-user-search-form name="discuss-users-search" method="get" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&view=users'); ?>">
			<div class="o-grid">
				<div class="o-grid__cell">
					<div class="o-input-group">
						<input type="text" name="search"
							value="<?php echo $this->html('string.escape', $search);?>"
							class="o-form-control" 
							placeholder="<?php echo JText::_('COM_EASYDISCUSS_USERS_SEARCH_PLACEHOLDER'); ?>" 
							aria-label="<?php echo JText::_('COM_EASYDISCUSS_USERS_SEARCH_PLACEHOLDER'); ?>" 
							aria-describedby="searchButton"
						>
						<button id="searchButton" class="o-btn o-btn--default-o" data-search-button><?php echo JText::_('COM_EASYDISCUSS_SEARCH_BUTTON'); ?></button>
					</div>
				</div>
			</div>

			<input type="hidden" name="option" value="com_easydiscuss" />
			<input type="hidden" name="view" value="users" />
		</form>

		<div class="ed-list <?php echo !$users && $search ? 'is-empty' : '';?>">
			<?php if ($users) { ?>
			<div class="l-stack">
				<?php foreach ($users as $user) { ?>
					<?php echo $this->html('card.user', $user); ?>
					<?php // echo $this->output('site/users/listings/item', array('user' => $user)); ?>
				<?php } ?>
			</div>
			<?php } ?>

			<?php echo $this->html('card.emptyCard', 'fa fa-users', JText::sprintf('COM_ED_NO_USERS_FOUND_BASED_ON_SEARCH', $search)); ?>
		</div>
		<div class="ed-pagination">
			<?php echo $pagination->getPagesLinks();?>
		</div>
	</div>
</div>