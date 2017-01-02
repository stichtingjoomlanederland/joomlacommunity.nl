<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureSessionshield extends AtsystemFeatureAbstract
{
	protected $loadOrder = 305;

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

		return ($this->cparams->getValue('sessionshield', 1) == 1);
	}

	/**
	 * Protect against session hijacking data
	 */
	public function onAfterInitialise()
	{
		$patterns = array(
			// pipe or :, O, :	integer : " identifier " : integer : {
			'@[\|:]O:\d{1,}:"[\w_][\w\d_]{0,}":\d{1,}:{@i',
			// pipe or :, a, :	integer :{
			'@[\|:]a:\d{1,}:{@i',
		);

		$hashes = array('get', 'post');

		foreach ($hashes as $hash)
		{
			$input = $this->input->$hash;
			$ref = new ReflectionProperty($input, 'data');
			$ref->setAccessible(true);
			$allVars = $ref->getValue($input);

			if (empty($allVars))
			{
				continue;
			}

			foreach ($patterns as $regex)
			{
				if ($this->match_array($regex, $allVars, true))
				{
					$extraInfo = "Hash      : $hash\n";
					$extraInfo .= "Variables :\n";
					$extraInfo .= print_r($allVars, true);
					$extraInfo .= "\n";
					$this->exceptionsHandler->blockRequest('sessionshield', null, $extraInfo);
				}
			}
		}
	}
} 