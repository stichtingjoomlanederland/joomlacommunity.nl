<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_finder
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Copy of version Joomla! 3.4.8
 */

defined('_JEXEC') or die;

JHtml::addIncludePath(JPATH_SITE . '/components/com_finder/helpers/html');

JHtml::_('jquery.framework');

// Load the smart search component language file.
$lang = JFactory::getLanguage();
$lang->load('com_finder', JPATH_SITE);

$suffix = $params->get('moduleclass_sfx');
$output = '<input type="text" name="q" class="search-word form-control input-sm" placeholder="' . $params->get('alt_label', JText::_('JSEARCH_FILTER_SUBMIT')) . '" value="' . htmlspecialchars(JFactory::getApplication()->input->get('q', '', 'string')) . '" />';

if ($params->get('show_label', 1))
{
	$label = '<label for="mod-finder-searchword" class="finder' . $suffix . '">' . $params->get('alt_label', JText::_('JSEARCH_FILTER_SUBMIT')) . '</label>';

	switch ($params->get('label_pos', 'left'))
	{
		case 'top' :
			$output = $label . '<br />' . $output;
			break;

		case 'bottom' :
			$output .= '<br />' . $label;
			break;

		case 'right' :
			$output .= $label;
			break;

		case 'left' :
		default :
			$output = $label . $output;
			break;
	}
}

if ($params->get('show_button'))
{
	$button = '<button class="btn btn-primary hasTooltip ' . $suffix . ' finder' . $suffix . '" type="submit" title="' . JText::_('MOD_FINDER_SEARCH_BUTTON') . '"><span class="icon-search icon-white"></span></button>';

	switch ($params->get('button_pos', 'left'))
	{
		case 'top' :
			$output = $button . '<br />' . $output;
			break;

		case 'bottom' :
			$output .= '<br />' . $button;
			break;

		case 'right' :
			$output .= $button;
			break;

		case 'left' :
		default :
			$output = $button . $output;
			break;
	}
}

$script = "
jQuery(document).ready(function() {
	var value, searchword = jQuery('.search-word');

		// Set the input value if not already set.
		if (!searchword.val())
		{
			searchword.val('" . JText::_('MOD_FINDER_SEARCH_VALUE', true) . "');
		}

		// Get the current value.
		value = searchword.val();

		// If the current value equals the default value, clear it.
		searchword.on('focus', function()
		{	var el = jQuery(this);
			if (el.val() === '" . JText::_('MOD_FINDER_SEARCH_VALUE', true) . "')
			{
				el.val('');
			}
		});

		// If the current value is empty, set the previous value.
		searchword.on('blur', function()
		{	var el = jQuery(this);
			if (!el.val())
			{
				el.val(value);
			}
		});

		jQuery('.site-search').on('submit', function(e){
			e.stopPropagation();
			var advanced = jQuery('#mod-finder-advanced');
			// Disable select boxes with no value selected.
			if ( advanced.length)
			{
				advanced.find('select').each(function(index, el) {
					var el = jQuery(el);
					if(!el.val()){
						el.attr('disabled', 'disabled');
					}
				});
			}
		});";
/*
 * This segment of code sets up the autocompleter.
 */
if ($params->get('show_autosuggest', 1))
{
	JHtml::_('script', 'media/jui/js/jquery.autocomplete.min.js', false, false, false, false, true);

	$script .= "
	var suggest = jQuery('.search-word').autocomplete({
		serviceUrl: '" . JRoute::_('index.php?option=com_finder&task=suggestions.suggest&format=json&tmpl=component', false) . "',
		paramName: 'q',
		minChars: 1,
		maxHeight: 400,
		width: 300,
		zIndex: 9999,
		deferRequestBy: 500
	});";
}

$script .= "});";

JFactory::getDocument()->addScriptDeclaration($script);
?>

<form action="<?php echo JRoute::_($route); ?>" method="get" class="navbar-form navbar-right site-search" role="search">
	<div class="finder<?php echo $suffix; ?>">
		<div class="form-group">
			<?php
			// Show the form fields.
			echo $output;
			?>
		</div>

		<?php $show_advanced = $params->get('show_advanced'); ?>
		<?php if ($show_advanced == 2) : ?>
			<br />
			<a href="<?php echo JRoute::_($route); ?>"><?php echo JText::_('COM_FINDER_ADVANCED_SEARCH'); ?></a>
		<?php elseif ($show_advanced == 1) : ?>
			<div id="mod-finder-advanced">
				<?php echo JHtml::_('filter.select', $query, $params); ?>
			</div>
		<?php endif; ?>
		<?php echo modFinderHelper::getGetFields($route, (int) $params->get('set_itemid')); ?>
	</div>
</form>
