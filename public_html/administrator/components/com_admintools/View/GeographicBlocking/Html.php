<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\GeographicBlocking;

defined('_JEXEC') or die;

use Akeeba\AdminTools\Admin\Model\ControlPanel;
use Akeeba\AdminTools\Admin\Model\GeographicBlocking;
use FOF30\View\DataView\Html as BaseView;

class Html extends BaseView
{
	/**
	 * GeoBlocked countries
	 *
	 * @var  array
	 */
	public $countries;

	/**
	 * GeoBlocked continents
	 *
	 * @var  array
	 */
	public $continents;

	/**
	 * All countries, as country code => human readable name
	 *
	 * @var  array
	 */
	public $allCountries;

	/**
	 * All continents, as continent code => human readable name
	 *
	 * @var  array
	 */
	public $allContinents;

	/**
	 * Is the necessary GeoIP plugin installed?
	 *
	 * @var  bool
	 */
	public $hasPlugin = false;

	/**
	 * Does the GeoIP database of the plugin need to be updated?
	 *
	 * @var  bool
	 */
	public $pluginNeedsUpdate = false;

	/**
	 * IP of the user, as reported by the server
	 *
	 * @var string
	 */
	public $myIP;

	/**
	 * Detected country for the current user
	 *
	 * @var string
	 */
	public $country;

	/**
	 * Detected continent for the current user
	 *
	 * @var string
	 */
	public $continent;

	protected function onBeforeMain($tpl = null)
	{
		/** @var GeographicBlocking $model */
		$model = $this->getModel();
		$model->getConfig();

		$this->continents        = $model->getContinents();
		$this->countries         = $model->getCountries();
		$this->allContinents     = $model->getAllContinents();
		$this->allCountries      = $model->getAllCountries();
		$this->hasPlugin         = $model->hasGeoIPPlugin();
		$this->pluginNeedsUpdate = $model->dbNeedsUpdate();

		/** @var ControlPanel $cpanelModel */
		$cpanelModel = $this->container->factory->model('ControlPanel');
		$this->myIP  = $cpanelModel->getVisitorIP();

		if ($this->hasPlugin && class_exists('AkeebaGeoipProvider'))
		{
			$geoip     = new \AkeebaGeoipProvider();
			$this->country   = $geoip->getCountryName($this->myIP);
			$this->continent = $geoip->getContinent($this->myIP);

			if (empty($this->country))
			{
				$this->country = '(unknown country)';
			}

			if (empty($this->continent))
			{
				$this->continent = '(unknown continent)';
			}
		}
	}
}
