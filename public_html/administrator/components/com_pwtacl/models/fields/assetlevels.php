<?php
/**
 * @package    PwtAcl
 *
 * @author     Sander Potjer - Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2011 - 2018 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com/pwt-acl
 */

// No direct access.
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

/**
 * Levels Field class.
 *
 * @since  3.2
 */
class JFormFieldAssetLevels extends JFormFieldList
{
	/**
	 * The form field type.
	 *
	 * @var     string
	 * @since   3.2
	 */
	protected $type = 'AssetLevels';

	/**
	 * Method to get the field options.
	 *
	 * @return  array  The field option objects
	 * @since   3.2
	 */
	protected function getOptions()
	{
		return array_merge(parent::getOptions(), PwtaclHelper::getLevelsOptions());
	}
}
