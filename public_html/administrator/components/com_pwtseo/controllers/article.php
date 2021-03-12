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
use Joomla\CMS\HTML\HTMLHelper;
use Joomla\CMS\Language\Text;
use Joomla\CMS\MVC\Controller\FormController;
use Joomla\CMS\MVC\Model\BaseDatabaseModel;
use Joomla\CMS\Plugin\PluginHelper;
use Joomla\CMS\Router\Route;
use Joomla\CMS\Session\Session;
use Joomla\Registry\Registry;

/**
 * The article controller
 *
 * @since  1.0.2
 */
class PWTSEOControllerArticle extends FormController
{
	/**
	 * Method to run batch operations.
	 *
	 * @param   JModelLegacy  $model  The model of the component being processed.
	 *
	 * @return  boolean   True if successful, false otherwise and internal error is set.
	 *
	 * @since   1.0.2
	 * @throws  Exception
	 */
	public function batch($model = false)
	{
		Session::checkToken() or jexit(Text::_('JINVALID_TOKEN'));

		$filter = InputFilter::getInstance();

		/** @var ContentModelArticle $model */
		BaseDatabaseModel::addIncludePath(JPATH_ROOT . '/administrator/components/com_content/models', 'ContentModel');
		$model = BaseDatabaseModel::getInstance('Article', 'ContentModel', ['ignore_request' => true]);
		$model->setState('params', new Registry);

		$db    = Factory::getDbo();
		$query = $db->getQuery(true);

		$query
			->select($db->quoteName('adv_open_graph'))
			->from($db->quoteName('#__plg_pwtseo'));

		$vars = $this->input->post->get('batch', [], 'array');
		$cid  = $this->input->post->get('cid', [], 'array');

		$advFields = array_filter($this->input->post->get('adv_open_graph', [], 'array'));
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

			// First handle the meta description
			if ($data['metadesc'])
			{
				// Only save the new meta data when we either overriding, or we have content and the current model doesn't have any
				if ($vars['override_metadesc'] === '1' || ($data['metadesc'] !== '' && $model->getItem($id)->metadesc === ''))
				{
					if (!$model->save($data))
					{
						$errors = true;
						Factory::getApplication()->enqueueMessage(Text::sprintf('COM_PWT_ERRORS_FAILED_TO_SAVE_METADESC', $id));
					}
				}
			}

			// Then we store the custom tags
			$query
				->clear('where')
				->clear('select')
				->select($db->quoteName('adv_open_graph'))
				->where($db->quoteName('context') . ' = ' . $db->quote('com_content.article'))
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
						'context'        => 'com_content.article',
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
					'context'        => 'com_content.article',
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
						->where($db->quoteName('context') . ' = ' . $db->quote('com_content.article'))
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

		$this->setRedirect(Route::_('index.php?option=com_pwtseo&view=articles' . $this->getRedirectToListAppend(), false));

		return true;
	}

	/**
	 * Method to run auto fill the meta description and keywords for articles that don't have any
	 *
	 * @since   1.3.0
	 * @throws  Exception
	 */
	public function autofillmeta()
	{
		// The plugin holds the blacklist data and the metadesc length
		$pluginParams  = new Registry(PluginHelper::getPlugin('system', 'pwtseo')->params);
		$metadescCount = $pluginParams->get('count_max_metadesc', 160);

		$db = Factory::getDbo();

		$query = $db->getQuery(true)
			->select(
				$db->quoteName(
					[
						'id',
						'language',
						'introtext',
						'metakey',
						'metadesc'
					]
				)
			)
			->from($db->quoteName('#__content'))
			->where('(' . $db->quoteName('metakey') . ' = "" OR ' . $db->quoteName('metadesc') . ' = "")');

		$articles = $db->setQuery($query, 0, 200)->loadObjectList();

		// Build the global blacklist for re-use
		$blacklist = '';
		$params    = $pluginParams->toArray();

		foreach ($params as $key => $val)
		{
			if (stripos($key, 'blacklist_') === 0)
			{
				$blacklist .= ' ' . $val;
			}
		}

		$processed = 0;

		// Overwrite the meta data where applicable and save it
		foreach ($articles as $article)
		{
			if (!$article->metakey)
			{
				$article->metakey = implode(
					' ',
					PWTSEOHelper::getMostCommenWords(
						$article->introtext,
						$article->language === '*' ? $blacklist : $pluginParams->get('blacklist_' . $article->language, '')
					)
				);
			}

			if (!$article->metadesc)
			{
				$article->metadesc = rtrim(HTMLHelper::_('string.truncate', preg_replace('/{+?.*?}+?/i', ' ', $article->introtext), $metadescCount, true, false), '.');
			}

			try
			{
				$db->updateObject('#__content', $article, ['id']);
				$processed++;
			}
			catch (Exception $e)
			{
			}
		}

		Factory::getApplication()->enqueueMessage(Text::sprintf('COM_PWT_ARTICLES_APPLIED_METADATA', $processed));
		$this->setRedirect(Route::_('index.php?option=com_pwtseo&view=articles', false));
	}
}
