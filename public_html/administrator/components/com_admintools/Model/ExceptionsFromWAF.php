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
use JDatabaseQuery;
use JText;
use RuntimeException;

/**
 * @property   string  $option
 * @property   string  $view
 * @property   string  $query
 *
 * @method  $this  foption()  foption(string $v)
 * @method  $this  fview()  fview(string $v)
 * @method  $this  fquery()  fquery(string $v)
 */
class ExceptionsFromWAF extends DataModel
{
	public function __construct(Container $container, array $config)
	{
		$config['tableName']   = '#__admintools_wafexceptions';
		$config['idFieldName'] = 'id';

		parent::__construct($container, $config);
	}

	/**
	 * Check the data for validity.
	 *
	 * @return  static  Self, for chaining
	 *
	 * @throws RuntimeException  When the data bound to this record is invalid
	 */
	public function check()
	{
		if (!$this->option && !$this->view && !$this->query)
		{
			throw new RuntimeException(JText::_('COM_ADMINTOOLS_ERR_EXCEPTIONSFROMWAF_ALLNULL'));
		}

		return parent::check();
	}

	/**
	 * Build the query to fetch data from the database
	 *
	 * @param   boolean $overrideLimits Should I override limits
	 *
	 * @return  JDatabaseQuery  The database query to use
	 */
	public function buildQuery($overrideLimits = false)
	{
		$db = $this->getDbo();

		$query = parent::buildQuery($overrideLimits);

		$fltOption = $this->getState('foption', null, 'string');

		if ($fltOption)
		{
			$fltOption = '%' . $fltOption . '%';
			$query->where($db->qn('option') . ' LIKE ' . $db->q($fltOption));
		}

		$fltView = $this->getState('fview', null, 'string');

		if ($fltView)
		{
			$fltView = '%' . $fltView . '%';
			$query->where($db->qn('view') . ' LIKE ' . $db->q($fltView));
		}

		$fltQuery = $this->getState('fquery', null, 'string');

		if ($fltQuery)
		{
			$fltQuery = '%' . $fltQuery . '%';
			$query->where($db->qn('query') . ' LIKE ' . $db->q($fltQuery));
		}

		return $query;
	}
}