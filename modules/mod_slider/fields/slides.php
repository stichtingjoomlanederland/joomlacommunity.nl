<?php
/**
 * @package     Slider
 * @subpackage  mod_slider
 *
 * @copyright   Copyright (C) 2015 Perfect Web Team, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

class JFormFieldSlides extends JFormField
{
	protected $type = 'Slides';

	protected function getInput()
	{
		$html = array();
		$attr = '';

		// Initialize some field attributes.
		$attr .= $this->required ? ' required aria-required="true"' : '';
		$attr .= ' multiple';

		// Initialize JavaScript field attributes.
		$attr .= $this->onchange ? ' onchange="' . $this->onchange . '"' : '';

		// Get the field options.
		$options = (array) $this->getOptions();

		// Create a regular list.
		$html[] = JHtml::_('select.genericlist', $options, $this->name, trim($attr), 'value', 'text', $this->value, $this->id);

		return implode($html);
	}

	protected function getOptions()
	{
		// Get connection.
		$db    = JFactory::getDbo();
	    $query = $db->getQuery(true);

		// Make query
		$query->select("*");
		$query->from("#__slider");

		// Set query
		$db->setQuery($query);

		// Get result
		$slides  = $db->loadObjectList();
		$options = array();

		foreach ($slides as $slide)
		{
			$tmp = array(
				'value'    => $slide->id,
				'text'     => $slide->title
			);

			$options[] = (object) $tmp;
		}

		// Return option
		return $options;
	}
}
