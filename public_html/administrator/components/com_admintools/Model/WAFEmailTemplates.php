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

/**
 * Class WAFEmailTemplates
 *
 * @property  string  $reason
 * @property  string  $language
 * @property  string  $subject
 * @property  string  $template
 * @property  int     $email_num
 * @property  int     $email_numfreq
 * @property  string  $email_freq
 *
 *
 * @method  $this  reason()  reason(string $v)
 * @method  $this  language()  language(string $v)
 * @method  $this  subject()  subject(string $v)
 * @method  $this  template()  template(string $v)
 * @method  $this  email_num()  email_num(int $v)
 * @method  $this  email_numfreq()  email_numfreq(int $v)
 * @method  $this  email_req()  email_req(string $v)
 */
class WAFEmailTemplates extends DataModel
{
	public function __construct(Container $container, array $config)
	{
		$config['tableName']   = '#__admintools_waftemplates';
		$config['idFieldName'] = 'admintools_waftemplate_id';

		parent::__construct($container, $config);

		$this->addBehaviour('Filters');
	}
}