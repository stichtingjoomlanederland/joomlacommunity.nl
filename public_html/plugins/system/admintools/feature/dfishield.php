<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureDfishield extends AtsystemFeatureAbstract
{
	protected $loadOrder = 360;

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

		return ($this->cparams->getValue('dfishield', 1) == 1);
	}

	/**
	 * Simple Direct Files Inclusion block.
	 */
	public function onAfterInitialise()
	{
		$input  = JFactory::getApplication()->input;
		$option = $input->getCmd('option', '');
		$view   = $input->getCmd('view', '');
		$layout = $input->getCmd('layout', '');

		// Special case: JCE
		if (($option == 'com_jce') && ($view == 'editor') && ($layout == 'plugin'))
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

			if ($this->match_array_dfi($allVars))
			{
				$extraInfo = "Hash      : $hash\n";
				$extraInfo .= "Variables :\n";
				$extraInfo .= print_r($allVars, true);
				$extraInfo .= "\n";
				$this->exceptionsHandler->blockRequest('dfishield', null, $extraInfo);
			}
		}
	}

	private function match_array_dfi($array)
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

				// If there's a null byte in the key, break
				if (strstr($key, "\u0000"))
				{
					$result = true;
					break;
				}

				// If there's no value, treat the key as a value
				if (empty($value))
				{
					$value = $key;
				}

				// Scan the value
				if (is_array($value))
				{
					$result = $this->match_array_dfi($value);
				}
				else
				{
					// If there's a null byte, break
					if (strstr($value, "\u0000"))
					{
						$result = true;
						break;
					}

					// If the value starts with a /, ../ or [a-z]{1,2}:, block
					$value = str_replace('\\', '/', $value);
					if (preg_match('#^(/|\.\.|[a-z]{1,2}:\\\)#i', $value))
					{
						// Fix 2.0.1: Check that the file exists
						$result = @file_exists($value);

						if (!$result)
						{
							$sillyParts = explode('../', $value);
							$realParts = array();

							foreach ($sillyParts as $p)
							{
								if (!empty($p))
								{
									$realParts[] = $p;
								}
							}

							$path = implode('/', $realParts);
							$result = @file_exists($path);
						}
						break;
					}

					if ($result)
					{
						break;
					}
				}
			}
		}

		return $result;
	}
} 