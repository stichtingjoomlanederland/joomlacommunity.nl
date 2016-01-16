<?php
/**
 * @package		Foundry
 * @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Foundry is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

class FD31_FoundryModule
{
	public $name      = null;
	public $type      = null;
	public $adapter   = null;
	public $compiler  = null;
	public $added    = false;

	private $manifest = null;
	private $data     = null;

	public function __construct($compiler, $adapterName, $moduleName, $moduleType)
	{
		$this->name     = $moduleName;
		$this->type     = $moduleType;
		$this->adapter  = $adapterName;
		$this->compiler = $compiler;
	}

	private function getAdapter() {

		return $this->compiler->getAdapter($this->adapter);
	}

	public function getData() {

		if (!empty($this->data)) {
			return $this->data;
		}

		$adapterMethod = 'get' . ucfirst($this->type);

		$this->data = $this->getAdapter()->$adapterMethod($this->name);

		return $this->data;
	}

	public function getManifest() {

		if (!empty($this->manifest)) {
			return $this->manifest;
		}

		$this->manifest = $this->getAdapter()->getManifest($this->name);

		return $this->manifest;
	}
}
