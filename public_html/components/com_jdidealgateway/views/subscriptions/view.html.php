<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Access\Exception\NotAllowed;
use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\View\HtmlView;
use Mollie\Api\Resources\SubscriptionCollection;

/**
 * Subscription view.
 *
 * @package  JDiDEAL
 * @since    6.4.0
 */
class JdidealgatewayViewSubscriptions extends HtmlView
{
	/**
	 * List of subscriptions
	 *
	 * @var    SubscriptionCollection
	 * @since  6.4.0
	 */
	protected $subscriptions;

	/**
	 * Execute and display a template script.
	 *
	 * @param   string  $tpl  The name of the template file to parse; automatically searches through the template paths.
	 *
	 * @return  mixed  A string if successful, otherwise a JError object.
	 *
	 * @since   6.4.0
	 * @throws  Exception
	 */
	public function display($tpl = null)
	{
		$user = Factory::getUser();

		if ($user->guest === 1)
		{
			throw new NotAllowed(Text::_('JERROR_ALERTNOAUTHOR'));
		}

		/** @var JdidealgatewayModelSubscriptions $model */
		$model = $this->getModel();
		$this->subscriptions = $model->getSubscriptions($user->email);

		return parent::display($tpl);
	}
}
