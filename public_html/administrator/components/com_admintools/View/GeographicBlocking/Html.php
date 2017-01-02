<?php
/**
 * @package   AdminTools
 * @copyright 2010-2016 Akeeba Ltd / Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\AdminTools\Admin\View\GeographicBlocking;

defined('_JEXEC') or die;

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
	}
}