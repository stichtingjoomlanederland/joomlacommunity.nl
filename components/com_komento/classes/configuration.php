<?php
/**
 * @package		Komento
 * @copyright	Copyright (C) 2012 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * Komento is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

// Load FoundryConfiguration
require_once(KOMENTO_FOUNDRY_CONFIGURATION);
require_once( dirname( __FILE__ ) . '/compiler.php' );

class KomentoConfiguration extends FD31_FoundryComponentConfiguration
{
	static $attached = false;
	static $instance = null;

	public function __construct()
	{
		$konfig = Komento::getKonfig();
		$config = Komento::getConfig();

		// @legacy: If environment is set to production, change to static.
		$environment = $konfig->get('komento_environment');
		if ($environment=='production') {
			$environment='static';
		}

		$this->fullName		= 'Komento';
		$this->shortName	= 'kmt';
		$this->environment	= $environment;
		$this->mode			= $konfig->get('komento_mode');
		$this->version		= (string) Komento::getHelper( 'Version' )->getLocalVersion();
		$this->baseUrl		= Komento::getHelper( 'Document' )->getBaseUrl();
		$this->token		= Komento::_( 'getToken' );

		$newConfig = clone $config->toObject();
		$newKonfig = clone $konfig->toObject();

		unset( $newConfig->antispam_recaptcha_private_key );
		unset( $newConfig->antispam_recaptcha_public_key );
		unset( $newConfig->antispam_akismet_key );
		unset( $newConfig->layout_phpbb_path );
		unset( $newConfig->layout_phpbb_url );
		unset( $newKonfig->layout_phpbb_path );
		unset( $newKonfig->layout_phpbb_url );

		$this->options     = array(
			"responsive"	=> (bool) $config->get('enable_responsive'),
			"jversion"		=> Komento::joomlaVersion(),
			"spinner"		=> JURI::root() . 'media/com_komento/images/loader.gif',
			"view"			=> JRequest::getString( 'view', '' ),
			"guest"			=> Komento::getProfile()->guest ? 1 : 0,
			"config"		=> $newConfig,
			"konfig"		=> $newKonfig,
			"acl"			=> Komento::getACL(),
			"element"		=> new stdClass()
		);

		parent::__construct();
	}

	public static function getInstance()
	{
		if (is_null(self::$instance)) {
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
					'komento-' . $this->version . '.static'
				);
				break;

			case 'optimized':
				$this->scripts = array(
					'komento-' . $this->version . '.optimized'
				);
				break;

			case 'development':
				$this->scripts = array(
					'komento'
				);
				break;
		}
	}

	public function attach()
	{
		if (self::$attached) return;

		parent::attach();

		if ($this->environment !== 'development')
		{
			// Get resources
			$compiler = new KomentoCompiler();
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
