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

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Mollie\Api\Resources\SubscriptionCollection;

/**
 * Subscriptions model
 *
 * @package  JDiDEAL
 * @since    6.4.0
 */
class JdidealgatewayModelSubscriptions extends BaseDatabaseModel
{
	/**
	 * Get the subscription for a user.
	 *
	 * @param   string  $email      The user email
	 *
	 * @return  SubscriptionCollection  List of subscriptions.
	 *
	 * @since   6.4.0
	 * @throws  Exception
	 */
	public function getSubscriptions(string $email
	): SubscriptionCollection {

		$profileId = $this->getState('profile.id');

		if (!$profileId)
		{
			throw new InvalidArgumentException(Text::_('COM_ROPAYMENTS_MISSING_PROFILE_ID'));
		}

		BaseDatabaseModel::addIncludePath(
			JPATH_ADMINISTRATOR . '/components/com_jdidealgateway/models'
		);

		/** @var JdidealgatewayModelCustomer $model */
		$model = BaseDatabaseModel::getInstance(
			'Customer', 'JdidealgatewayModel', ['ignore_request' => true]
		);

		return $model->getSubscriptions(
			$email, $profileId
		);
	}

	/**
	 * Method to auto-populate the state.
	 *
	 * This method should only be called once per instantiation and is designed
	 * to be called on the first call to the getState() method unless the
	 * configuration flag to ignore the request is set.
	 *
	 * @return  void
	 *
	 * @note    Calling getState in this method will result in recursion.
	 * @since   6.4.0
	 */
	protected function populateState()
	{
		parent::populateState();

		$profileId = Factory::getApplication()->input->getInt('profile_id');

		$this->setState('profile.id', $profileId);
	}
}
