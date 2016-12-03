<?php
/**
 * @package    Bootstrap3_Compatibility
 *
 * @copyright  Copyright (C) 2016 Niels van der Veer, Inc. All rights reserved.
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

require_once "html/bootstrap3compatibility.php";

/**
 * Plugin class for Compatibility with Bootstrap 3
 *
 * @since  1.0.0
 */
class PlgSystemBootstrap3Compatibility extends JPlugin
{
	/**
	 * Application object
	 *
	 * @var  JApplicationCms
	 */
	protected $app;

	/**
	 * Array containing information for loaded files
	 *
	 * @var  array
	 */
	protected static $loaded = array();

	/**
	 * Listener for onAfterInitialise event
	 *
	 * @return  void
	 */
	public function onAfterInitialise()
	{
		// Only for site
		if (!$this->app->isSite())
		{
			return;
		}

		// Register listeners for JHtml helpers
		if (!JHtml::isRegistered('bootstrap.startTabSet'))
		{
			JHtml::register('bootstrap.startTabSet', 'JHtmlBootstrap3Compatibility::startTabSet');
		}

		if (!JHtml::isRegistered('bootstrap.startAccordion'))
		{
			JHtml::register('bootstrap.startAccordion', 'JHtmlBootstrap3Compatibility::startAccordion');
		}

		if (!JHtml::isRegistered('bootstrap.addSlide'))
		{
			JHtml::register('bootstrap.addSlide', 'JHtmlBootstrap3Compatibility::addSlide');
		}

		if (!JHtml::isRegistered('calendar'))
		{
			JHtml::register('calendar', 'JHtmlBootstrap3Compatibility::calendar');
		}
	}
}