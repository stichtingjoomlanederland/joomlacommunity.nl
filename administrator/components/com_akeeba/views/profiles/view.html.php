<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 * @since     1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Platform;

/**
 * MVC View for Profiles management
 *
 */
class AkeebaViewProfiles extends F0FViewHtml
{
	function display($tpl = null)
	{
		// Load the util helper
		$this->loadHelper('utils');

		// Add a spacer, a help button and show the template
		JToolBarHelper::spacer();

		parent::display($tpl);
	}

	/**
	 * The default layout, shows a list of profiles
	 *
	 */
	function onBrowse($tpl = null)
	{
		// Get reference to profiles model
		$model = $this->getModel();

		// Get profile ID
		$profileid = Platform::getInstance()->get_active_profile();
		$this->profileid = $profileid;

		// Get profile name
		$model->setId($profileid);
		$profile_data = $model->getProfile();
		$this->profilename = $this->escape($profile_data->description);

		// Get Sort By fields
		$this->sortFields = array(
			'id'          => JText::_('JGRID_HEADING_ID'),
			'description' => JText::_('COM_AKEEBA_PROFILES_COLLABEL_DESCRIPTION'),
		);

		return parent::onBrowse($tpl);
	}
}