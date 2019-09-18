<?php
/**
 * @package         Regular Labs Library
 * @version         19.8.25552
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2019 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Library\Condition;

defined('_JEXEC') or die;

/**
 * Class ZooPagetype
 * @package RegularLabs\Library\Condition
 */
class ZooPagetype
	extends Zoo
{
	public function pass()
	{
		return $this->passByPageType('com_zoo', $this->selection, $this->include_type);
	}
}
