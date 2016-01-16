<?php
/**
 * @package		Foundry
 * @copyright	Copyright (C) 2012 StackIdeas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Foundry is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once( DISCUSS_FOUNDRY_CONFIGURATION );

class DiscussConfigurationHelper extends FoundryComponentConfiguration {

	static $instance = null;

	public function __construct() {

		$config = DiscussHelper::getConfig();

		$this->fullName    = "EasyDiscuss";
		$this->shortName   = "ed";
		$this->environment = $config->get('easydiscuss_environment');
		$this->mode        = $config->get('easydiscuss_mode');
		$this->version     = DiscussHelper::getLocalVersion();
		$this->baseUrl     = DiscussHelper::getAjaxURL();
		$this->token       = DiscussHelper::getToken();

		parent::__construct();
	}

	public static function getInstance()
	{
		if( is_null( self::$instance ) )
		{
			self::$instance	= new self();
		}

		return self::$instance;
	}

	public function update()
	{
		// We need to call parent's update method first
		// because they will automatically check for
		// url overrides, e.g. es_env, es_mode.
		parent::update();

		// TODO: Clean up script ordering
		return;

		switch ($this->environment) {

			case 'static':
			default:
				$this->scripts = array(
					'easydiscuss.static',
					'easydiscuss.extras'
				);
				break;

			case 'optimized':
				$this->scripts = array(
					'easydiscuss.optimized',
					'easydiscuss.extras'
				);
				break;

			case 'development':
				$this->scripts = array(
					'easydiscuss'
				);
				break;
		}
	}

	public function attach()
	{
		if (self::$attached) return;

		parent::attach();

		self::$attached = true;
	}
}
