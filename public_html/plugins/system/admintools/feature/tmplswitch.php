<?php
/**
 * @package   admintools
 * @copyright Copyright (c)2010-2021 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

use Joomla\CMS\Factory;

defined('_JEXEC') || die;

class AtsystemFeatureTmplswitch extends AtsystemFeatureAbstract
{
	protected $loadOrder = 390;

	/**
	 * Is this feature enabled?
	 *
	 * @return bool
	 */
	public function isEnabled()
	{
		if (!$this->container->platform->isFrontend())
		{
			return false;
		}

		if ($this->skipFiltering)
		{
			return false;
		}

		return ($this->cparams->getValue('tmpl', 0) == 1);
	}

	/**
	 * Disable template switching in the URL
	 */
	public function onAfterInitialise()
	{
		$tmpl = Factory::getApplication()->input->getCmd('tmpl', null);

		if (empty($tmpl))
		{
			return;
		}

		$whitelist = $this->cparams->getValue('tmplwhitelist', 'component,system');

		if (empty($whitelist))
		{
			$whitelist = 'component,system';
		}

		$temp      = explode(',', $whitelist);
		$whitelist = [];

		foreach ($temp as $item)
		{
			$whitelist[] = trim($item);
		}

		$whitelist = array_merge(['component', 'system'], $whitelist);

		if (!is_null($tmpl) && !in_array($tmpl, $whitelist))
		{
			$this->exceptionsHandler->blockRequest('tmpl');
		}
	}
}
