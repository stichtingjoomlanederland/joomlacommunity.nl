<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureBadwords extends AtsystemFeatureAbstract
{
	protected $loadOrder = 380;

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

		return ($this->cparams->getValue('antispam', 0) == 1);
	}

	/**
	 * The simplest anti-spam solution imaginable. Just blocks a request if a prohibited word is found.
	 */
	public function onAfterInitialise()
	{
		$db = $this->db;
		$sql = $db->getQuery(true)
			->select($db->qn('word'))
			->from($db->qn('#__admintools_badwords'))
			->group($db->qn('word'));
		$db->setQuery($sql);

		try
		{
			$badwords = $db->loadColumn();
		}
		catch (Exception $e)
		{
			// Do nothing if the query fails
			$badwords = null;
		}

		if (empty($badwords))
		{
			return;
		}

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

			foreach ($badwords as $word)
			{
				$regex = '#\b' . $word . '\b#i';

				if ($this->match_array($regex, $allVars, true))
				{
					$extraInfo = "Hash      : $hash\n";
					$extraInfo .= "Variables :\n";
					$extraInfo .= print_r($allVars, true);
					$extraInfo .= "\n";
					$this->exceptionsHandler->blockRequest('antispam', null, $extraInfo);
				}
			}
		}
	}
} 