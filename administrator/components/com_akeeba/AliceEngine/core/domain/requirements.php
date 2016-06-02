<?php
/**
 * Akeeba Engine
 * The modular PHP5 site backup engine
 *
 * @copyright Copyright (c)2006-2016 Nicholas K. Dionysopoulos
 * @license   GNU GPL version 3 or, at your option, any later version
 * @package   akeebaengine
 *
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