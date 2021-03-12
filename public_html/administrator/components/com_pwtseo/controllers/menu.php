<?php
/**
 * @package    Pwtseo
 *
 * @author     Perfect Web Team <extensions@perfectwebteam.com>
 * @copyright  Copyright (C) 2016 - 2021 Perfect Web Team. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 * @link       https://extensions.perfectwebteam.com
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Filter\InputFilter;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\CMS\Table\Table;
use Joomla\Registry\Registry;

/**
 * The article controller
 *
 * @since  1.2.0
 */
class PWTSEOControllerMenu extends FormController
{
	/**
	 * Method to run batch operations.
	 *
	 * @param   BaseDatabaseModel $model The model of the component being processed.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.2.0
	 */
	public function batch($model = false)
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$filter = InputFilter::getInstance();

		Table::addIncludePath(JPATH_ROOT . '/administrator/components/com_menus/tables');
		/** @var MenusModelItem $model */
		BaseDatabaseModel::addIncludePath(JPATH_ROOT . '/administrator/components/com_menus/models');
		$model = BaseDatabaseModel::getInstance('Item', 'MenusModel', []);

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($db->quoteName('adv_open_graph'))
			->from($db->quoteName('#__plg_pwtseo'));

		$vars = $this->input->post->get('batch', [], 'array');
		$cid  = $this->input->post->get('cid', [], 'array');

		$advFields = $this->input->post->get('adv_open_graph', [], 'array');
		$advJson   = ['og_title' => [], 'og_content' => []];

		$structuredData = $this->input->post->get('structured_data', null, 'array');
		$structuredData = isset($structuredData['pwtseo']) && is_array($structuredData['pwtseo']['structureddata']) ?
			json_encode($structuredData['pwtseo']['structureddata']) : null;

		$internalFields = [
			'og:title'      => '', 'og:description' => '', 'og:image' => '', 'og:url' => '',
			'twitter:title' => '', 'twitter:description' => '', 'twitter:image' => '',
			'google:title'  => '', 'google:description' => '', 'google:image' => ''
		];

		foreach ($advFields as $advField)
		{
			if (isset($internalFields[$advField['key']]))
			{
				$internalFields[$advField['key']] = $advField['value'];
			}
			else
			{
				$advJson['og_title'][]   = $advField['key'];
				$advJson['og_content'][] = $advField['value'];
			}
		}

		$data = ['metadesc' => $filter->clean($vars['metadesc'], 'HTML')];

		$errors = false;

		foreach ($cid as $id)
		{
			$data['id'] = (int) $id;
			$modelData  = $model->getItem($id);
			$params     = new Registry($modelData->params);

			if (!isset($vars['override_metadesc']) || $vars['override_metadesc'] !== '1')
			{
				if ($params->get('menu-meta_description', '') !== '')
				{
					continue;
				}
			}

			$params->set('menu-meta_description', $data['metadesc']);
			$modelData->params = $params->toArray();

			if (!$model->save((array) $modelData))
			{
				$errors = true;
				Factory::getApplication()->enqueueMessage(Text::sprintf('COM_PWT_ERRORS_FAILED_TO_SAVE_METADESC', $id));
			}

			// Then we store the custom tags
			$query
				->clear('where')
				->clear('select')
				->select($db->quoteName('adv_open_graph'))
				->where($db->quoteName('context') . ' = ' . $db->quote('com_menus.item'))
				->where($db->quoteName('context_id') . ' = ' . $data['id']);

			if ($this->input->get('override_adv_open_graph', '0') === '1' || ((count($advJson) || count($internalFields)) && !$db->setQuery($query)->loadResult()))
			{
				// First we gotta check if we already have a record in the database
				$query
					->clear('select')
					->select($db->quoteName('id'));

				$obj = (object) array_merge(
					[
						'id'             => $db->setQuery($query)->loadResult(),
						'context'        => 'com_menus.item',
						'context_id'     => $data['id'],
						'adv_open_graph' => json_encode($advJson)
					],
					array_filter(
						[
							'facebook_title'       => $internalFields['og:title'],
							'facebook_description' => $internalFields['og:description'],
							'facebook_image'       => $internalFields['og:image'],
							'facebook_url'         => $internalFields['og:url'],
							'twitter_title'        => $internalFields['twitter:title'],
							'twitter_description'  => $internalFields['twitter:description'],
							'twitter_image'        => $internalFields['twitter:image'],
							'google_title'         => $internalFields['google:title'],
							'google_description'   => $internalFields['google:description'],
							'google_image'         => $internalFields['google:image']
						]
					)
				);

				if ($obj->id)
				{
					$db->updateObject('#__plg_pwtseo', $obj, ['context', 'context_id']);
				}
				else
				{
					$db->insertObject('#__plg_pwtseo', $obj);
				}
			}

			if ($structuredData || $vars['override_structured'] === '1')
			{
				$obj = (object) [
					'context'        => 'com_menus.item',
					'context_id'     => $data['id'],
					'structureddata' => $structuredData
				];

				// In this case we just want to clean all the articles
				if (!$structuredData || $vars['override_structured'] === '1')
				{
					$db->updateObject('#__plg_pwtseo', $obj, ['context', 'context_id'], true);
				}
				else
				{
					$query
						->clear('where')
						->clear('select')
						->select($db->quoteName(['id', 'structureddata']))
						->where($db->quoteName('context') . ' = ' . $db->quote('com_menus.item'))
						->where($db->quoteName('context_id') . ' = ' . $data['id']);

					$current = $db->setQuery($query, 0, 1)->loadObject();

					if ($current && $current->id)
					{
						$obj->id             = $current->id;
						$obj->structureddata = $current->structureddata ?: $obj->structureddata;

						$db->updateObject('#__plg_pwtseo', $obj, ['context', 'context_id'], true);
					}
					else
					{
						$db->insertObject('#__plg_pwtseo', $obj, ['context', 'context_id']);
					}
				}
			}
		}

		if (!$errors)
		{
			Factory::getApplication()->enqueueMessage(Text::_('COM_PWT_BATCH_APPLIED'));
		}

		$this->setRedirect(Route::_('index.php?option=com_pwtseo&view=menus' . $this->getRedirectToListAppend(), false));

		return true;
	}
}
