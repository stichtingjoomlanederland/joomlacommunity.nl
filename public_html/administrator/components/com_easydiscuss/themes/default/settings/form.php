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
<form method="post" action="<?php echo JRoute::_('index.php');?>" id="adminForm" enctype="multipart/form-data">

	<div class="btn-wrapper ed-settings-search t-hidden" data-search-wrapper>
		<input type="text" class="ed-settings-search__input" data-settings-search placeholder="<?php echo JText::_('COM_ED_SEARCH_FOR_SETTINGS');?>"/>

		<div class="hidden ed-settings-search__result" data-search-result style="">
		</div>
	</div>
	
	<div class="wrapper accordion">
		<div class="tab-box tab-box-alt">
			<div class="tabbable">

				<ul class="nav nav-tabs nav-tabs-icons">
					<?php foreach ($tabs as $tab) { ?>
					<li class="tabItem <?php echo $tab->active ? 'active' : '';?>">
						<a href="#<?php echo $tab->id;?>" data-id="<?php echo $tab->id;?>" data-ed-toggle="tab" data-form-tabs><?php echo $tab->title;?></a>
					</li>
					<?php } ?>
				</ul>

				<div class="tab-content">
					<?php foreach ($tabs as $tab) { ?>
					<div id="<?php echo $tab->id;?>" class="tab-pane <?php echo $tab->active ? 'active' : '';?>">
						<?php echo $tab->contents;?>
					</div>
					<?php } ?>
				</div>
			</div>
		</div>
	</div>

	<input type="hidden" name="layout" value="<?php echo $layout;?>" />
	<input type="hidden" name="active" value="<?php echo $activeTab;?>" data-ed-active-tab />

	<?php echo $this->html('form.action', 'settings'); ?>
</form>
