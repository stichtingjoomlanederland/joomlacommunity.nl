<?php
/**
 * @package     New_Article_Button
 * @subpackage  plg_system_newarticlebutton
 *
 * @copyright   Copyright (C) 2016 Niels van der Veer, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later
 */

defined('_JEXEC') or die;

/**
 * New Article Plugins
 *
 * @since  1.0.0
 */
class PlgSystemNewArticleButton extends JPlugin
{
	/**
	 * @var    JFactory::getApplication()
	 *
	 * @since  1.0.0
	 */
	protected $app;

	/**
	 * @var    bool  Enable or disable autoloading of the language file
	 *
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Add the button above the component buffer
	 *
	 * @return bool
	 *
	 * @since  1.0.0
	 */
	public function onBeforeRender()
	{
		// Get the current user
		$doc   = JFactory::getDocument();
		$input = $this->app->input;
		$user  = JFactory::getUser();

		// Do not execute on admin or if the user is a guest
		if ($this->app->isAdmin() || $user->guest)
		{
			return false;
		}

		// Only execute on certain layouts
		if ($input->get("option") != "com_content" || $input->get("view") != "category")
		{
			return false;
		}

		// Get buffer and category ID
		$catid  = $input->get("id");
		$buffer = $doc->getBuffer("component");

		// If the user if allowed to create add the button
		if ($user->authorise("core.create", "com_content.category." . $catid))
		{
			// Set edit variables
			$editUrl = JRoute::_('index.php?option=com_content&view=form&layout=edit&catid=' . $catid);

			// Get path for the button layout
			$path = JPluginHelper::getLayoutPath("system", "newarticlebutton",  "button");

			// Render the layout
			ob_start();
			include $path;
			$html = ob_get_clean();

			// Append button to the compnent buffer
			$doc->setBuffer($html . $buffer, "component");
		}

		return true;
	}
}
