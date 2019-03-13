<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Checks for runtime errors, ie Backup Timeout, timeout on post-processing etc etc
 */
class AliceCoreDomainRuntimeerrors extends AliceCoreDomainAbstract
{
	public function __construct()
	{
		parent::__construct(30, 'runtimeerrors', JText::_('COM_AKEEBA_ALICE_ANALYZE_RUNTIME_ERRORS'));
	}
}
