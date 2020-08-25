<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;
/**
 * RO Payments payment Button.
 *
 * @since  1.0.0
 */
class PlgButtonJdidealpaymentbutton extends JPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  1.0.0
	 */
	protected $autoloadLanguage = true;

	/**
	 * Application
	 *
	 * @var    JApplicationSite
	 * @since  1.0.0
	 */
	protected $app;

	/**
	 * Display the button.
	 *
	 * @param   string  $name  The name of the button to display.
	 *
	 * @return  object|void The button to show or nothing if we are on front-end.
	 *
	 * @since   1.0.0
	 */
	public function onDisplay($name)
	{
		// Do not show the button on the front-end as we have no front-end support
		if ($this->app->isClient('site'))
		{
			return;
		}

		$link = 'index.php?option=com_jdidealgateway&amp;view=jdidealgateway&amp;layout=button&amp;tmpl=component&amp;'
			. JSession::getFormToken() . '=1&amp;editor=' . $name;

		$button = new JObject;
		$button->set('modal', true);
		$button->set('class', 'btn');
		$button->set('link', $link);
		$button->set('text', JText::_('PLG_JDIDEALPAYMENTBUTTON_BUTTON'));
		$button->set('name', 'jdideal');
		$button->set('options', "{handler: 'iframe', size: {x: 500, y: 350}}");

		return $button;
	}
}
