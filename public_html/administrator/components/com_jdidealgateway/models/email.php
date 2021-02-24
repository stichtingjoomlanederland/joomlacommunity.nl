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
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\CMS\Table\Table;

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
	 * @since   2.0.0
	 * @throws  Exception
	 *
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
	 * @param   int     $emailId  The email ID to send
	 * @param   string  $email    The email address to send to
	 *
	 * @return  array  Contains message and status.
	 *
	 * @since   2.8.2
	 * @throws \PHPMailer\PHPMailer\Exception
	 */
	public function testEmail(int $emailId, string $email): array
	{
		$config   = Factory::getConfig();
		$from     = $config->get('mailfrom');
		$fromName = $config->get('fromname');
		$mail     = Factory::getMailer();

		$result          = [];
		$result['msg']   = '';
		$result['state'] = 'error';

		if (!$emailId || !$email)
		{
			$result['msg'] = Text::_('COM_ROPAYMENTS_NO_EMAILS_FOUND');

			if (!$email)
			{
				$result['msg'] = Text::_(
					'COM_ROPAYMENTS_MISSING_EMAIL_ADDRESS'
				);
			}

			return $result;
		}


		$emailTable = Table::getInstance('Email', 'Table');
		$emailTable->load($emailId);

		if (($body = $emailTable->get('body'))
			&& $mail->sendMail(
				$from,
				$fromName,
				$email,
				$emailTable->get('subject'),
				$body,
				true
			))
		{
			$result['msg']   = Text::_('COM_ROPAYMENTS_TESTEMAIL_SENT');
			$result['state'] = '';
		}

		return $result;
	}

	/**
	 * Method to get the data that should be injected in the form.
	 *
	 * @return  array  The data for the form.
	 *
	 * @since   2.0.0
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState(
			'com_jdidealgateway.edit.email.data', array()
		);

		if (0 === count($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
}
