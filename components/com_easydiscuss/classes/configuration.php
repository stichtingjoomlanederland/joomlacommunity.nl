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

// Load FoundryConfiguration
require_once( EASYDISCUSS_FOUNDRY_CONFIGURATION );
require_once( dirname( __FILE__ ) . '/compiler.php' );

class EasyDiscussConfiguration extends FD31_FoundryComponentConfiguration
{
	static $attached = false;
	static $instance = null;

	public function __construct()
	{
		$config 	= DiscussHelper::getConfig();

		// @legacy: If environment is set to production, change to static.
		$environment = $config->get( 'easydiscuss_environment' );

		if ($environment=='production')
		{
			$environment='static';
		}

		$this->fullName    = 'EasyDiscuss';
		$this->shortName   = 'ed';
		$this->environment = $environment;
		$this->mode        = $config->get( 'easydiscuss_mode' );		
		$this->version     = (string) DiscussHelper::getLocalVersion();
		$this->baseUrl     = DiscussHelper::getBaseUrl();
		$this->token       = DiscussHelper::getToken();
		$this->options     = array(
			"scriptVersioning" => (bool) $config->get( 'main_script_versioning' ),
			"responsive"       => (bool) $config->get( 'responsive' )
		);

		parent::__construct();
	}

	public static function getInstance()
	{
		if (is_null(self::$instance))
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

		switch ($this->environment) {

			case 'static':
			default:
				$this->scripts = array(
					'easydiscuss-' . $this->version . '.static'
				);
				break;

			case 'optimized':
				$this->scripts = array(
					'easydiscuss-' . $this->version . '.optimized'
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

		if ($this->environment!=="development") {

			// Get resources
			$compiler = new DiscussCompiler();
			$resource = $compiler->getResources();
			
			// Attach resources
			if (!empty($resource)) {
			
				$scriptTag = $this->createScriptTag($resource["uri"]);

				$document = JFactory::getDocument();
				$document->addCustomTag($scriptTag);
			}
		}
		
		self::$attached = true;		
	}
}