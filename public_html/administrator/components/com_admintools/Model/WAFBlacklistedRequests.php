<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\Model;

defined('_JEXEC') or die;

use FOF30\Container\Container;
use FOF30\Model\DataModel;
use JText;
use RuntimeException;

/**
 * Class WAFBlacklistedRequests
 *
 * @property   string  $option
 * @property   string  $view
 * @property   string  $task
 * @property   string  $query
 * @property   string  $query_type
 * @property   string  $query_content
 * @property   string  $verb
 *
 * @method  $this  fverb() fverb(string $v)
 * @method  $this  foption() foption(string $v)
 * @method  $this  fview() fview(string $v)
 * @method  $this  fquery() fquery(string $v)
 * @method  $this  fquery_content() fquery_content(string $v)
 */
class WAFBlacklistedRequests extends DataModel
{
	/**
	 * Public constructor.
	 *
	 * @see DataModel::__construct()
	 *
	 * @param   Container  $container  The configuration variables to this model
	 * @param   array      $config     Configuration values for this model
	 *
	 * @throws \FOF30\Model\DataModel\Exception\NoTableColumns
	 */
	public function __construct(Container $container, array $config)
	{
		$config['tableName']   = '#__admintools_wafblacklists';
		$config['idFieldName'] = 'id';

		parent::__construct($container, $config);
	}

	public function check()
	{
		if (empty($this->option) && empty($this->view) && empty($this->task) && empty($this->query))
		{
			throw new RuntimeException(JText::_('COM_ADMINTOOLS_LBL_WAFBLACKLISTEDREQUEST_ERR_ALLEMPTY'));
		}
	}

	/**
	 * Build the query to fetch data from the database
	 *
	 * @param   boolean $overrideLimits Should I override limits
	 *
	 * @return  \JDatabaseQuery  The database query to use
	 */
	public function buildQuery($overrideLimits = false)
	{
		$db = $this->container->db;

		$query = $db->getQuery(true)
		            ->select(array('*'))
		            ->from($db->quoteName('#__admintools_wafblacklists'));

		if ($verb = $this->getState('fverb'))
		{
			$query->where($db->qn('verb') . ' = ' . $db->q($verb));
		}

		$fltOption = $this->getState('foption', null, 'string');

		if ($fltOption)
		{
			$fltOption = '%' . $fltOption . '%';
			$query->where($db->quoteName('option') . ' LIKE ' . $db->quote($fltOption));
		}

		$fltView = $this->getState('fview', null, 'string');

		if ($fltView)
		{
			$fltView = '%' . $fltView . '%';
			$query->where($db->quoteName('view') . ' LIKE ' . $db->quote($fltView));
		}

		$fltQuery = $this->getState('fquery', null, 'string');

		if ($fltQuery)
		{
			$fltQuery = '%' . $fltQuery . '%';
			$query->where($db->quoteName('query') . ' LIKE ' . $db->quote($fltQuery));
		}

		if ($content = $this->getState('fquery_content'))
		{
			$query->where($db->qn('query_content') . ' LIKE ' . $db->q($db->escape($content), false));
		}

		if (!$overrideLimits)
		{
			$order = $this->getState('filter_order', null, 'cmd');

			if (!in_array($order, array_keys($this->knownFields)))
			{
				$order = 'id';
			}

			$dir = $this->getState('filter_order_Dir', 'ASC', 'cmd');
			$query->order($db->qn($order) . ' ' . $dir);
		}

		return $query;
	}
}