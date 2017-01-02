<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') or die;

use FOF30\Input\Input;
use FOF30\Model\DataModel;
use FOF30\Model\Model;
use JText;

class ImportAndExport extends Model
{
	public function exportData()
	{
		$return = array();

		$exportData = $this->input->get('exportdata', array(), 'array', 2);

		if (isset($exportData['wafconfig']) && $exportData['wafconfig'])
		{
			/** @var ConfigureWAF $configModel */
			$configModel = $this->container->factory->model('ConfigureWAF')->tmpInstance();
			$config      = $configModel->getConfig();

			// Let's unset two factor auth stuff
			unset($config['twofactorauth']);
			unset($config['twofactorauth_secret']);
			unset($config['twofactorauth_panic']);

			$return['wafconfig'] = $config;
		}

		if (isset($exportData['wafblacklist']) && $exportData['wafblacklist'])
		{
			/** @var WAFBlacklistedRequests $wblrq */
			$wblrq                  = $this->container->factory->model('WAFBlacklistedRequests')->tmpInstance();
			$return['wafblacklist'] = $wblrq->get(true);
		}

		if (isset($exportData['wafexceptions']) && $exportData['wafexceptions'])
		{
			/** @var ExceptionsFromWAF $efw */
			$efw                     = $this->container->factory->model('ExceptionsFromWAF')->tmpInstance();
			$return['wafexceptions'] = $efw->get(true);
		}

		if (isset($exportData['ipblacklist']) && $exportData['ipblacklist'])
		{
			/** @var BlacklistedAddresses $ipBls */
			$ipBls                 = $this->container->factory->model('BlacklistedAddresses')->tmpInstance();
			$return['ipblacklist'] = $ipBls->get(true);
		}

		if (isset($exportData['ipwhitelist']) && $exportData['ipwhitelist'])
		{
			/** @var WhitelistedAddresses $ipWls */
			$ipWls                 = $this->container->factory->model('WhitelistedAddresses')->tmpInstance();
			$return['ipwhitelist'] = $ipWls->get(true);
		}

		if (isset($exportData['badwords']) && $exportData['badwords'])
		{
			/** @var BadWords $badwords */
			$badwords           = $this->container->factory->model('BadWords')->tmpInstance();
			$return['badwords'] = $badwords->get(true);
		}

		if (isset($exportData['emailtemplates']) && $exportData['emailtemplates'])
		{
			/** @var WAFEmailTemplates $waftemplates */
			$waftemplates             = $this->container->factory->model('WAFEmailTemplates')->tmpInstance();
			$return['emailtemplates'] = $waftemplates->get(true);
		}

		return $return;
	}

	public function importData()
	{
		$db = $this->container->db;

		$input  = new Input('files');
		$file   = $input->get('importfile', null, 'file', 2);
		$errors = array();

		// Sanity checks
		if (!$file)
		{
			throw new \Exception(JText::_('COM_ADMINTOOLS_IMPORTANDEXPORT_NOFILE'));
		}

		$data = file_get_contents($file['tmp_name']);

		if ($data === false)
		{
			throw new \Exception(JText::_('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_READING_FILE'));
		}

		$data = json_decode($data, true);

		if (!$data)
		{
			throw new \Exception(JText::_('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_READING_FILE'));
		}

		// Everything seems ok, let's start importing data

		if (isset($data['wafconfig']))
		{
			/** @var ConfigureWAF $config */
			$config = $this->container->factory->model('ConfigureWAF')->tmpInstance();
			$config->saveConfig($data['wafconfig']);
		}

		if (isset($data['wafblacklist']))
		{
			try
			{
				$db->truncateTable('#__admintools_wafblacklists');

				if ($data['wafblacklist'])
				{
					$insert = $db->getQuery(true)
					             ->insert($db->qn('#__admintools_wafblacklists'))
					             ->columns(array(
						             $db->qn('option'),
						             $db->qn('view'),
						             $db->qn('task'),
						             $db->qn('query'),
						             $db->qn('query_type'),
						             $db->qn('query_content'),
						             $db->qn('verb')
					             ));

					// I could have several records, let's create a single big query
					foreach ($data['wafblacklist'] as $row)
					{
						$insert->values(
							$db->q($row['option']) . ', ' . $db->q($row['view']) . ', ' . $db->q($row['task']) . ', ' .
							$db->q($row['query']) . ', ' . $db->q($row['query_type']) . ', ' .
							$db->q($row['query_content']) . ', ' . $db->q($row['verb'])
						);
					}

					$db->setQuery($insert)->execute();
				}
			}
			catch (\Exception $e)
			{
				$errors[] = JText::_('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_WAFBLACKLIST');
			}
		}

		if (isset($data['wafexceptions']))
		{
			try
			{
				$db->truncateTable('#__admintools_wafexceptions');

				if ($data['wafexceptions'])
				{
					$insert = $db->getQuery(true)
					             ->insert($db->qn('#__admintools_wafexceptions'))
					             ->columns(array(
						             $db->qn('option'), $db->qn('view'), $db->qn('query')
					             ));

					// I could have several records, let's create a single big query
					foreach ($data['wafexceptions'] as $row)
					{
						$insert->values(
							$db->q($row['option']).', '.$db->q($row['view']).', '.$db->q($row['query'])
						);
					}

					$db->setQuery($insert)->execute();
				}
			}
			catch (\Exception $e)
			{
				$errors[] = JText::_('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_WAFEXCEPTIONS');
			}
		}

		if (isset($data['ipblacklist']))
		{
			try
			{
				$db->truncateTable('#__admintools_ipblock');

				if ($data['ipblacklist'])
				{
					// I could have several records, let's use raw SQL queries
					foreach ($data['ipblacklist'] as $row)
					{
						$this->importBlackListRows($row);
					}

					$this->importBlackListRows();
				}
			}
			catch (\Exception $e)
			{
				$errors[] = JText::_('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_BLACKLIST');
			}
		}

		if (isset($data['ipwhitelist']))
		{
			try
			{
				$db->truncateTable('#__admintools_adminiplist');

				if ($data['ipwhitelist'])
				{
					// I could have several records, let's create a single big query
					$insert = $db->getQuery(true)
					             ->insert($db->qn('#__admintools_adminiplist'))
					             ->columns(array($db->qn('ip'), $db->qn('description')));

					foreach ($data['ipwhitelist'] as $row)
					{
						$insert->values($db->q($row['ip']) . ', ' . $db->q($row['description']));
					}

					$db->setQuery($insert)->execute();
				}
			}
			catch (\Exception $e)
			{
				$errors[] = JText::_('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_WHITELIST');
			}
		}

		if (isset($data['badwords']))
		{
			try
			{
				$db->truncateTable('#__admintools_badwords');

				if ($data['badwords'])
				{
					// I could have several records, let's create a single big query
					$insert = $db->getQuery(true)
					             ->insert($db->qn('#__admintools_badwords'))
					             ->columns(array($db->qn('word')));

					foreach ($data['badwords'] as $row)
					{
						$insert->values($db->q($row['word']));
					}

					$db->setQuery($insert)->execute();
				}
			}
			catch (\Exception $e)
			{
				$errors[] = JText::_('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_BADWORDS');
			}
		}

		if (isset($data['emailtemplates']))
		{
			try
			{
				$db->truncateTable('#__admintools_waftemplates');
			}
			catch (\Exception $e)
			{
				$errors[] = JText::_('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_EMAILTEMPLATES');
			}

			/** @var WAFEmailTemplates $wafTemplate */
			$wafTemplate = $this->container->factory->model('WAFEmailTemplates')->tmpInstance();

			// Most likely I will only have 10-12 templates max, so I can use the table instead of directly writing inside the db
			foreach ($data['emailtemplates'] as $row)
			{
				$wafTemplate->reset();
				$wafTemplate->admintools_waftemplate_id = null;

				// Let's leave primary key handling to the database
				unset($row['admintools_waftemplate_id']);
				unset($row['created_by']);
				unset($row['created_on']);
				unset($row['modified_by']);
				unset($row['modified_on']);

				// Calling the save method will trigger all the checks
				try
				{
					$wafTemplate->save($row);
				}
				catch (\Exception $e)
				{
					// There was an error, better stop here
					$errors[] = JText::_('COM_ADMINTOOLS_ERR_IMPORTANDEXPORT_EMAILTEMPLATES');
					break;
				}
			}
		}

		if ($errors)
		{
			throw new \Exception(implode('<br/>', $errors));
		}
	}

	/**
	 * Since we could have several thousands of records to import, we will perform a batch import
	 *
	 * @param null|array $data
	 */
	protected function importBlackListRows($data = null)
	{
		static $cache = array();

		// Let's enqueue the data
		if($data)
		{
			$cache[] = $data;
		}

		// Did we grow over the limit or are forced to flush it? If so let's build the actual query
		// and execute it
		if(count($cache) >= 100 || !$data)
		{
			$db = $this->container->db;

			$query = $db->getQuery(true)
						->insert($db->qn('#__admintools_ipblock'))
						->columns(array($db->qn('ip'), $db->qn('description')));

			foreach ($cache as $row)
			{
				$query->values($db->q($row['ip']).', '.$db->q($row['description']));
			}

			$db->setQuery($query)->execute();

			$cache = array();
		}
	}
}