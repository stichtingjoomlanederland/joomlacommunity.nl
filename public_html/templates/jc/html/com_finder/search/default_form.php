<?php
/**
 * @package     Joomla.Site
 * @subpackage  com_finder
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

use Joomla\CMS\Factory;
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Router\Route;

defined('_JEXEC') or die;

if ($this->params->get('show_advanced', 1) || $this->params->get('show_autosuggest', 1))
{
	HTMLHelper::_('jquery.framework');

	$script = "
jQuery(function() {";
	if ($this->params->get('show_advanced', 1))
	{
		/*
		* This segment of code disables select boxes that have no value when the
		* form is submitted so that the URL doesn't get blown up with null values.
		*/
		$script .= "
	jQuery('#finder-search').on('submit', function(e){
		e.stopPropagation();
		// Disable select boxes with no value selected.
		jQuery('#advancedSearch').find('select').each(function(index, el) {
			var el = jQuery(el);
			if(!el.val()){
				el.attr('disabled', 'disabled');
			}
		});
	});";
	}
	/*
	* This segment of code sets up the autocompleter.
	*/
	if ($this->params->get('show_autosuggest', 1))
	{
		HTMLHelper::_('script', 'media/jui/js/jquery.autocomplete.min.js', false, false, false, false, true);

		$script .= "
	var suggest = jQuery('#q').autocomplete({
		serviceUrl: '" . Route::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component') . "',
		paramName: 'q',
		minChars: 1,
		maxHeight: 400,
		width: 300,
		zIndex: 9999,
		deferRequestBy: 500
	});";
	}

	$script .= "
});";

	Factory::getDocument()->addScriptDeclaration($script);
}
?>

<form id="finder-search" action="<?php echo JRoute::_($this->query->toUri()); ?>" method="get" class="form-inline"><?php
	echo $this->getFields();

	/*
	 * DISABLED UNTIL WEIRD VALUES CAN BE TRACKED DOWN.
	 */
	if (false && $this->state->get('list.ordering') !== 'relevance_dsc') : ?>
        <input type="hidden" name="o" value="<?php echo $this->escape($this->state->get('list.ordering')); ?>" /><?php
	endif; ?>

    <fieldset class="word form-inline">
        <input type="text" name="q" id="q" size="50" value="<?php echo $this->escape($this->query->input); ?>"
               class="inputbox form-control"/><?php

		if ($this->escape($this->query->input) != '' || $this->params->get('allow_empty_search')):
			?>
            <button name="Search" type="submit" class="btn btn-primary"><span
                class="icon-search icon-white"></span> <?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></button><?php
		else:
			?>
            <button name="Search" type="submit" class="btn btn-primary disabled"><span
                class="icon-search icon-white"></span> <?php echo Text::_('JSEARCH_FILTER_SUBMIT'); ?></button><?php
		endif; ?>
    </fieldset>

    <div id="advancedSearch">
		<?php
		if ($this->params->get('show_advanced_tips', 1)) :
			?>
            <div class="advanced-search-tip"><?php
				echo Text::_('COM_FINDER_ADVANCED_TIPS');
				?></div>
            <hr/><?php
		endif;
		?>
        <div id="finder-filter-window"><?php
			echo HTMLHelper::_('filter.select', $this->query, $this->params);
			?></div>
    </div>
</form>
