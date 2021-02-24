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
<div class="ed-giphy-browser" data-giphy-browser>
	<div class="o-tabs o-tabs--ed t-justify-content--se" >
		<div class="o-tabs__item t-flex-grow--1 active" data-giphy-gifs-tab>
			<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('COM_ED_GIPHY_GIFS'); ?></a>
		</div>
		<div class="o-tabs__item t-flex-grow--1" data-giphy-stickers-tab>
			<a class="o-tabs__link" href="javascript:void(0);"><?php echo JText::_('COM_ED_GIPHY_STICKERS'); ?></a>
		</div>
	</div>
	<div class="ed-giphy" data-giphy-container>
		<div class="tab-content">
			<div class="ed-giphy-browser__input-search t-my--md">
				<input data-giphy-search type="text" class="o-form-control" placeholder="<?php echo JText::_('COM_ED_GIPHY_SEARCH'); ?>">
			</div>
			<div class="ed-giphy-browser__result-label t-mb--sm" data-giphy-trending><?php echo JText::_('COM_ED_GIPHY_TRENDING'); ?></div>
			<div class="tab-pane active">
				<div class="ed-giphy-list-container" data-gifs-list>
				</div>
			</div>
			<div class="tab-pane active">
				<div class="ed-giphy-list-container t-d--none" data-stickers-list>
				</div>
			</div>
		</div>

		<?php echo $this->html('loading.block'); ?>

		<div class="o-empty o-empty--height-no">
			<div class="o-card">
				<div class="o-card__body">
					<div class="">
						<div class="o-empty__text ed-giphy-browser__result-text" data-giphy-no-result><?php echo JText::_('COM_ED_GIPHY_NO_RESULT'); ?></div>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div class="ed-giphy-browser__result-footer">
		<div class="ed-powered-by-giphy"></div>
	</div>
</div>