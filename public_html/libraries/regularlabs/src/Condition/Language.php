<?php
/**
 * @package         Regular Labs Library
 * @version         20.2.16442
 * 
 * @author          Peter van Westen <info@regularlabs.com>
 * @link            http://www.regularlabs.com
 * @copyright       Copyright © 2020 Regular Labs All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

namespace RegularLabs\Library\Condition;

defined('_JEXEC') or die;

use Joomla\CMS\Factory as JFactory;

/**
 * Class Language
 * @package RegularLabs\Library\Condition
 */
class Language
	extends \RegularLabs\Library\Condition
{
	public function pass()
	{
		return $this->passSimple(JFactory::getLanguage()->getTag(), true);
	}
}
