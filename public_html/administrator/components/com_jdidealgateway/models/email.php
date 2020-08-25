<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2020 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;

defined('_JEXEC') or die;

/**
 * Email model.
 *
 * @package  JDiDEAL
 * @since    2.0.0
 */
class JdidealgatewayModelEmail extends AdminModel
{
	/**
	 * Get the form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success | False on failure.
	 *
	 * @throws  Exception
	 *
	 * @since   2.0.0
	 */
	public function getForm($data = array(), $loadData = true)
	{
		// Get the form.
		$form = $this->loadForm(
			'com_jdidealgateway.email',
			'email',
			array('control' => 'jform', 'load_data' => $loadData)
		);

		if (!is_object($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Send out a test e-mail.
	 *
	 * @return  array  Contains message and status.
	 *
	 * @throws  Exception
	 * @throws  RuntimeException
	 *
	 * @since   2.8.2
	 */
	public function testEmail(): array
	{
		$config   = Factory::getConfig();
		$from     = $config->get('mailfrom');
		$fromName = $config->get('fromname');
		$mail     = Factory::getMailer();
		$input    = Factory::getApplication()->input;

		$cids            = $input->get('cid', array(), 'array');
		$email           = $input->get('email', null, '');
		$result          = array();
		$result['msg']   = '';
		$result['state'] = 'error';

		if ($cids && $email)
		{
			$db    = $this->getDbo();
			$query = $db->getQuery(true)
				->select(
					array(
						$db->quoteName('subject'),
						$db->quoteName('body')
					)
				)
				->from($db->quoteName('#__jdidealgateway_emails'));

			foreach ($cids as $cid)
			{
				$query->clear('where')
					->where($db->quoteName('id') . ' = ' . (int) $cid);
				$db->setQuery($query);
				$details = $db->loadObject();

				if ($details->body)
				{
					$mail->clearAddresses();

					if ($mail->sendMail($from, $fromName, $email, $details->subject, $details->body, true))
					{
						$result['msg']   = Text::_('COM_ROPAYMENTS_TESTEMAIL_SENT');
						$result['state'] = '';
					}
				}
			}
		}
		else
		{
			$result['msg'] = Text::_('COM_ROPAYMENTS_NO_EMAILS_FOUND');

			if (!$email)
			{
				$result['msg'] = Text::_('COM_ROPAYMENTS_MISSING_EMAIL_ADDRESS');
			}
		}

		return $result;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The data for the form.
	 *
	 * @throws  Exception
	 *
	 * @since   2.0.0
	 */
	protected function loadFormData()
	{
		// Check the session for previously entered form data.
		$data = Factory::getApplication()->getUserState('com_jdidealgateway.edit.email.data', array());

		if (0 === count($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
}
