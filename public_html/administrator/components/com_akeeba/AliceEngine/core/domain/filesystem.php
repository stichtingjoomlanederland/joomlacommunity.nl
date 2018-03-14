<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2018 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
 */

// Protection against direct access
defined('AKEEBAENGINE') or die();

/**
 * Checks for runtime errors, ie Backup Timeout, timeout on post-processing etc etc
 */
class AliceCoreDomainFilesystem extends AliceCoreDomainAbstract
{
	public function __construct()
	{
		parent::__construct(40, 'filesystem', JText::_('COM_AKEEBA_ALICE_ANALYZE_FILESYSTEM'));
	}
}
