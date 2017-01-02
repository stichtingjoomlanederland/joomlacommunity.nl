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
 * @property   string  $word
 *
 * @method  $this  word()  word(string|array $v)
 */
class BadWords extends DataModel
{
	public function __construct(Container $container, array $config)
	{
		$config['tableName']   = '#__admintools_badwords';
		$config['idFieldName'] = 'id';

		parent::__construct($container, $config);

		$this->addBehaviour('Filters');
	}

	/**
	 * Check the data for validity. By default it only checks for fields declared as NOT NULL
	 *
	 * @return  static  Self, for chaining
	 *
	 * @throws RuntimeException  When the data bound to this record is invalid
	 */
	public function check()
	{
		if (!$this->word)
		{
			throw new RuntimeException(JText::_('COM_ADMINTOOLS_ERR_BADWORD_NEEDS_WORD'));
		}

		return parent::check();
	}
}