<?php
/**
 * @package    JDiDEAL
 *
 * @author     Roland Dalmulder <contact@rolandd.com>
 * @copyright  Copyright (C) 2009 - 2021 RolandD Cyber Produksi. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://rolandd.com
 */

use Joomla\CMS\Factory;
use Joomla\CMS\Filesystem\File;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Model\AdminModel;
use Joomla\Registry\Registry;
use Joomla\String\StringHelper;

defined('_JEXEC') or die;

/**
 * Profile model.
 *
 * @package  JDiDEAL
 * @since    4.0.0
 */
class JdidealgatewayModelProfile extends AdminModel
{
	/**
	 * Form context
	 *
	 * @var    string
	 * @since  4.0.0
	 */
	private $context = 'com_jdidealgateway.profile';

	/**
	 * Get the form.
	 *
	 * @param   array    $data      Data for the form.
	 * @param   boolean  $loadData  True if the form is to load its own data (default case), false if not.
	 *
	 * @return  mixed  A JForm object on success | False on failure.
	 *
	 * @since   4.0.0
	 * @throws  Exception
	 *
	 */
	public function getForm($data = [], $loadData = true)
	{
		$form = $this->loadForm(
			'com_jdidealgateway.profile',
			'profile',
			['control' => 'jform', 'load_data' => $loadData]
		);

		if (!is_object($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Method to get the record form.
	 *
	 * @param   string  $provider  The name of the payment provider to load the form for.
	 *
	 * @return  mixed  A JForm object on success, false on failure.
	 *
	 * @since   4.0.0
	 * @throws  Exception
	 */
	public function getPspForm(string $provider)
	{
		$form = $this->loadForm(
			$this->context . '.' . $provider,
			$provider,
			['control' => 'jform', 'load_data' => false]
		);

		if (!is_object($form))
		{
			return false;
		}

		return $form;
	}

	/**
	 * Check if the security files exist.
	 *
	 * @return  array  Array with results of file check.
	 *
	 * @since   4.0.0
	 */
	public function getFilesExist(): array
	{
		$filesExists         = [];
		$certificatePath     = JPATH_LIBRARIES . '/Jdideal/Psp/Advanced/certificates';
		$filesExists['cert'] = File::exists($certificatePath . '/cert.cer');
		$filesExists['priv'] = File::exists($certificatePath . '/priv.pem');

		return $filesExists;
	}

	/**
	 * Save the configuration.
	 *
	 * @param   array  $data  The form data.
	 *
	 * @return  boolean  True on success or false on failure.
	 *
	 * @since   4.0.0
	 * @throws  Exception
	 */
	public function save($data): bool
	{
		$app = Factory::getApplication();

		// Get the PSP form data
		$formData = $app->input->post->get('jform', [], 'array');
		$data     = array_merge($data, $formData);

		// Trim text fields
		$trimFields = [
			'merchant_id',
			'shainkey',
			'shaoutkey',
			'hash',
			'merchantId',
			'IDEAL_PrivatekeyPass',
			'IDEAL_MerchantID',
			'IDEAL_SubID',
			'secret_key',
			'merchant_key',
			'sharedSecret',
			'hashkey',
			'subId',
			'apiKey',
			'partner_id',
			'profile_key',
			'signingKey',
			'apiKey',
			'password',
			'keyversion',
			'merchant_key',
			'shop_id',
			'rtlo'
		];

		foreach ($trimFields as $index => $trimField)
		{
			if (array_key_exists($trimField, $formData))
			{
				$formData[$trimField] = trim($formData[$trimField]);
			}
		}

		// Store the settings as a JSON string
		$params              = new Registry($formData);
		$data['paymentInfo'] = $params->toString();

		// Make sure the COMPLUS field is selected for Internetkassa
		if ($data['psp'] === 'ogone' || $data['psp'] === 'abn-internetkassa')
		{
			if (!in_array('COMPLUS', $data['dynamic_parameters'], true))
			{
				Factory::getApplication()->enqueueMessage(Text::_('COM_ROPAYMENTS_MISSING_COMPLUS_PARAMETER'), 'error');

				return false;
			}
		}

		// Clean the iDEAL Advanced description
		if ($data['psp'] === 'advanced')
		{
			$data['IDEAL_DESCRIPTION'] = str_ireplace(array('&'), '', $data['IDEAL_DESCRIPTION']);
		}

		// Alter the title for save as copy
		if ($app->input->get('task') === 'save2copy')
		{
			$origTable = clone $this->getTable();
			$origTable->load($app->input->getInt('id'));

			if ($data['name'] === $origTable->get('name'))
			{
				[$title, $alias] = $this->generateNewTitle(null, $data['alias'], $data['name']);
				$data['name']  = $title;
				$data['alias'] = $alias;
			}
			else
			{
				if ($data['alias'] == $origTable->get('alias'))
				{
					$data['alias'] = '';
				}
			}

			// Set the new ordering value
			$data['ordering'] = $origTable->get('ordering') + 1;

			// Unset the ID so a new item is created
			unset($data['id']);
		}

		// Save the profile
		if (!parent::save($data))
		{
			return false;
		}

		// Check if any security files are uploaded
		$files = $app->input->files->get('jform');

		if ($files)
		{
			foreach ($files as $type => $names)
			{
				foreach ($names as $name => $info)
				{
					$cert = false;

					switch ($name)
					{
						case 'cert_upload':
							if ($info['error'] === 0)
							{
								// Check if the filename is correct
								$cert = true;

								if ($info['name'] !== 'cert.cer')
								{
									$app->enqueueMessage(Text::_('COM_ROPAYMENTS_NAME_CERT_INVALID'), 'error');
									$cert = false;
								}
							}
							break;
						case 'priv_upload':
							if ($info['error'] === 0)
							{
								// Check if the filename is correct
								$cert = true;

								if ($info['name'] !== 'priv.pem')
								{
									$app->enqueueMessage(Text::_('COM_ROPAYMENTS_NAME_CERT_INVALID'), 'error');
									$cert = false;
								}
							}
							break;
					}

					if ($cert)
					{
						$folder = JPATH_LIBRARIES . '/Jdideal/Psp/Advanced/certificates';

						if (File::upload($info['tmp_name'], $folder . '/' . $info['name']))
						{
							$app->enqueueMessage(Text::_('COM_ROPAYMENTS_NAME_CERT_UPLOADED'));
						}
					}
				}
			}
		}

		return true;
	}

	/**
	 * Method to change the title & alias.
	 *
	 * @param   integer  $categoryId  The id of the category.
	 * @param   string   $alias       The alias.
	 * @param   string   $title       The title.
	 *
	 * @return  array  Contains the modified title and alias.
	 *
	 * @since   4.5.0
	 * @throws  Exception
	 */
	protected function generateNewTitle($categoryId, $alias, $title): array
	{
		// Alter the title & alias
		$table = $this->getTable();

		while ($table->load(array('alias' => $alias)))
		{
			$title = StringHelper::increment($title);
			$alias = StringHelper::increment($alias, 'dash');
		}

		return array($title, $alias);
	}

	/**
	 * Method to get the data that should be injected in the form..
	 *
	 * @return  array  The data for the form.
	 *
	 * @since   4.0.0
	 * @throws  Exception
	 */
	protected function loadFormData()
	{
		$data = Factory::getApplication()->getUserState('com_jdidealgateway.edit.profile.data', []);

		if (0 === count($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}

	/**
	 * Method to get a single record.
	 *
	 * @param   integer  $pk  The id of the primary key.
	 *
	 * @return  mixed    Object on success, false on failure.
	 *
	 * @since   4.0.0
	 * @throws  Exception
	 */
	public function getItem($pk = null)
	{
		$item     = parent::getItem($pk);
		$forcePsp = Factory::getApplication()->getUserState('profile.psp', false);

		if ($forcePsp)
		{
			$item->psp = $forcePsp;
		}

		// Get the payment information
		$settings          = new Registry($item->paymentInfo);
		$item->paymentInfo = $settings->toObject();

		// Get the email fields
		$item->status_mismatch        = $settings->get('status_mismatch');
		$item->admin_order_payment    = $settings->get('admin_order_payment');
		$item->admin_status_failed    = $settings->get('admin_status_failed');
		$item->inform_email           = $settings->get('inform_email');
		$item->jdidealgateway_emailto = $settings->get('jdidealgateway_emailto');
		$item->customer_change_status = $settings->get('customer_change_status');

		return $item;
	}
}
