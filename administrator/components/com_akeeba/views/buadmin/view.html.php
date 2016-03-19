<?php
/**
 * @package   AkeebaBackup
 * @copyright Copyright (c)2009-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 *
 * @since     1.3
 */

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

/**
 * Akeeba Backup Administrator view class
 *
 */
class AkeebaViewBuadmin extends F0FViewHtml
{

	protected $lists = null;

	protected $profiles = array();

	function  __construct($config = array())
	{
		parent::__construct($config);
		$this->lists = new JObject();
	}

	public function onEdit($tpl = null)
	{
		$model           = $this->getModel();
		$id              = $model->getId();
		$record          = Platform::getInstance()->get_statistics($id);
		$this->record    = $record;
		$this->record_id = $id;

		$this->setLayout('comment');
	}

	public function onBrowse($tpl = null)
	{
		$this->loadHelper('Utils');

		$task = 'default';

		if (AKEEBA_PRO && ($task == 'default'))
		{
			$bar = JToolBar::getInstance('toolbar');
			$bar->appendButton('Link', 'restore', JText::_('DISCOVER'), 'index.php?option=com_akeeba&view=discover');
			JToolBarHelper::publish('restore', JText::_('STATS_LABEL_RESTORE'));
		}

		if (($task == 'default'))
		{
			JToolBarHelper::editList('showcomment', JText::_('STATS_LOG_EDITCOMMENT'));

			$pModel                  = F0FModel::getTmpInstance('Profiles', 'AkeebaModel');
			$enginesPerPprofile      = $pModel->getPostProcessingEnginePerProfile();
			$this->enginesPerProfile = $enginesPerPprofile;
		}
		JToolBarHelper::spacer();

		// "Show warning first" download button. Joomlantastic!
		$confirmationText = AkeebaHelperEscape::escapeJS(JText::_('STATS_LOG_DOWNLOAD_CONFIRM'), "'\n");
		$baseURI          = JUri::base();
		$js               = <<<JS

;// This comment is intentionally put here to prevent badly written plugins from causing a Javascript error
// due to missing trailing semicolon and/or newline in their code.
function confirmDownloadButton()
{
	var answer = confirm('$confirmationText');
	if(answer) submitbutton('download');
}

function confirmDownload(id, part)
{
	var answer = confirm('$confirmationText');
	var newURL = '$baseURI';
	if(answer) {
		newURL += 'index.php?option=com_akeeba&view=buadmin&task=download&id='+id;
		if( part != '' ) newURL += '&part=' + part
		window.location = newURL;
	}
}

JS;

		$document = JFactory::getDocument();
		$document->addScriptDeclaration($js);

		$hash = 'akeebabuadmin';

		// ...ordering
		$app = JFactory::getApplication();
		$this->lists->set('order', $app->getUserStateFromRequest($hash . 'filter_order',
			'filter_order', 'backupstart'));
		$this->lists->set('order_Dir', $app->getUserStateFromRequest($hash . 'filter_order_Dir',
			'filter_order_Dir', 'DESC'));

		// ...filter state
		$this->lists->set('fltDescription', $app->getUserStateFromRequest($hash . 'filter_description',
			'description', null));
		$this->lists->set('fltFrom', $app->getUserStateFromRequest($hash . 'filter_from',
			'from', null));
		$this->lists->set('fltTo', $app->getUserStateFromRequest($hash . 'filter_to',
			'to', null));
		$this->lists->set('fltOrigin', $app->getUserStateFromRequest($hash . 'filter_origin',
			'origin', null));
		$this->lists->set('fltProfile', $app->getUserStateFromRequest($hash . 'filter_profile',
			'profile', null));

		$filters  = $this->_getFilters();
		$ordering = $this->_getOrdering();

		require_once JPATH_COMPONENT_ADMINISTRATOR . '/models/statistics.php';
		$model = new AkeebaModelStatistics();
		$list  = $model->getStatisticsListWithMeta(false, $filters, $ordering);

		$profilesModel = F0FModel::getTmpInstance('Profiles', 'AkeebaModel');
		$profilesModel->reset()->clearState()->clearInput();
        // Let's create an array indexed with the profile id for better handling
		$profiles = $profilesModel->getItemList(true, 'id');

		$profilesList = array(
			JHtml::_('select.option', '', '–' . JText::_('STATS_LABEL_PROFILEID') . '–')
		);

		if (!empty($profiles))
		{
			foreach ($profiles as $profile)
			{
				$profilesList[] = JHtml::_('select.option', $profile->id, '#' . $profile->id . '. ' . $profile->description);
			}
		}

		// Assign data to the view
		$this->profiles     = $profiles; // Profiles
		$this->profilesList = $profilesList; // Profiles list for select box
		$this->list         = $list; // Data
		$this->pagination   = $model->getPagination($filters); // Pagination object

		return true;
	}

	private function _getFilters()
	{
		$filters = array();

		if ($this->lists->fltDescription)
		{
			$filters[] = array(
				'field'   => 'description',
				'operand' => 'LIKE',
				'value'   => $this->lists->fltDescription
			);
		}

		if ($this->lists->fltFrom && $this->lists->fltTo)
		{
			$filters[] = array(
				'field'   => 'backupstart',
				'operand' => 'BETWEEN',
				'value'   => $this->lists->fltFrom,
				'value2'  => $this->lists->fltTo
			);
		}
		elseif ($this->lists->fltFrom)
		{
			$filters[] = array(
				'field'   => 'backupstart',
				'operand' => '>=',
				'value'   => $this->lists->fltFrom,
			);
		}
		elseif ($this->lists->fltTo)
		{
			JLoader::import('joomla.utilities.date');
			$to     = new JDate($this->lists->fltTo);
			$to     = date('Y-m-d') . ' 23:59:59';

			$filters[] = array(
				'field'   => 'backupstart',
				'operand' => '<=',
				'value'   => $to,
			);
		}
		if ($this->lists->fltOrigin)
		{
			$filters[] = array(
				'field'   => 'origin',
				'operand' => '=',
				'value'   => $this->lists->fltOrigin
			);
		}
		if ($this->lists->fltProfile)
		{
			$filters[] = array(
				'field'   => 'profile_id',
				'operand' => '=',
				'value'   => (int) $this->lists->fltProfile
			);
		}

		$session   = JFactory::getSession();
		$filters[] = array(
			'field'   => 'tag',
			'operand' => '<>',
			'value'   => 'restorepoint'
		);


		if (empty($filters))
		{
			$filters = null;
		}

		return $filters;
	}

	private function _getOrdering()
	{
		$order = array(
			'by'    => $this->lists->order,
			'order' => strtoupper($this->lists->order_Dir)
		);

		return $order;
	}
}