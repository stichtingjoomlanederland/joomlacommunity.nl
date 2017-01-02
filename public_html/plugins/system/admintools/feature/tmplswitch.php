<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

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
		if (!$this->helper->isFrontend())
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
		$tmpl = JFactory::getApplication()->input->getCmd('tmpl', null);

		if (empty($tmpl))
		{
			return;
		}

		$whitelist = $this->cparams->getValue('tmplwhitelist', 'component,system');

		if (empty($whitelist))
		{
			$whitelist = 'component,system';
		}

		$temp = explode(',', $whitelist);
		$whitelist = array();

		foreach ($temp as $item)
		{
			$whitelist[] = trim($item);
		}

		$whitelist = array_merge(array('component', 'system'), $whitelist);

		if (!is_null($tmpl) && !in_array($tmpl, $whitelist))
		{
			if (!$this->exceptionsHandler->blockRequest('tmpl'))
			{
				return;
			}
		}
	}
} 