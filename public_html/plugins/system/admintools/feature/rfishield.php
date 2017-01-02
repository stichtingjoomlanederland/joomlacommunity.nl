<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureRfishield extends AtsystemFeatureAbstract
{
	protected $loadOrder = 350;

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

		return ($this->cparams->getValue('rfishield', 1) == 1);
	}

	/**
	 * Simple Remote Files Inclusion block. If any query string parameter contains a reference to an http[s]:// or ftp[s]://
	 * address it will be scanned. If the remote file looks like a PHP script, we block access.
	 */
	public function onAfterInitialise()
	{
		$hashes = array('get', 'post');
		$regex = '#(http|ftp){1,1}(s){0,1}://.*#i';

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

			if ($this->match_array_and_scan($regex, $allVars))
			{
				$extraInfo = "Hash      : $hash\n";
				$extraInfo .= "Variables :\n";
				$extraInfo .= print_r($allVars, true);
				$extraInfo .= "\n";
				$this->exceptionsHandler->blockRequest('rfishield', null, $extraInfo);
			}
		}
	}

	private function match_array_and_scan($regex, $array)
	{
		$result = false;

		if (is_array($array))
		{
			foreach ($array as $key => $value)
			{
				if (!empty($this->exceptions) && in_array($key, $this->exceptions))
				{
					continue;
				}

				if (is_array($value))
				{
					$result = $this->match_array_and_scan($regex, $value);
				}
				else
				{
					$result = preg_match($regex, $value);
				}

				if ($result)
				{
					// Can we fetch the file directly?
					$fContents = @file_get_contents($value);

					if (!empty($fContents))
					{
						$result = (strstr($fContents, '<?php') !== false);

						if ($result)
						{
							break;
						}
					}
					else
					{
						$result = false;
					}
				}
			}
		}
		elseif (is_string($array))
		{
			$result = preg_match($regex, $array);

			if ($result)
			{
				// Can we fetch the file directly?
				$fContents = @file_get_contents($array);

				if (!empty($fContents))
				{
					$result = (strstr($fContents, '<?php') !== false);

					if ($result)
					{
						return $result;
					}
				}
				else
				{
					$result = false;
				}
			}
		}

		return $result;
	}
} 