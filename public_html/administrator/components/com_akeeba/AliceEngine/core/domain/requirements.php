<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Checks system requirements ie PHP version, Database version and type, memory limits etc etc
 */
class AliceCoreDomainRequirements extends AliceCoreDomainAbstract
{
	public function __construct()
	{
		parent::__construct(20, 'requirements', JText::_('COM_AKEEBA_ALICE_ANALYZE_REQUIREMENTS'));
	}
}
