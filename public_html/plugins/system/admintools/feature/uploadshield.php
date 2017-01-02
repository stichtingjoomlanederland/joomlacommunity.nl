<?php
/**
 * @package   AdminTools
 * @copyright Copyright (c)2010-2016 Nicholas K. Dionysopoulos
 * @license   GNU General Public License version 3, or later
 */

defined('_JEXEC') or die;

class AtsystemFeatureUploadshield extends AtsystemFeatureAbstract
{
	protected $loadOrder = 370;

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

		return ($this->cparams->getValue('uploadshield', 1) == 1);
	}

	/**
	 * Scans all uploaded files for PHP tags. This prevents uploading PHP files or crafted
	 * images with raw PHP code in them which may lead to arbitrary code execution under
	 * several common circumstances. It will also block files with null bytes in their
	 * filenames or with double extensions which include PHP in them (e.g. .php.jpg).
	 */
	public function onAfterInitialise()
	{
		// Do we have uploaded files?
		$input = $this->input->files;

		$ref = new ReflectionProperty($input, 'data');
		$ref->setAccessible(true);
		$filesHash = $ref->getValue($input);

		if (empty($filesHash))
		{
			return;
		}

		$extraInfo = '';
		foreach ($filesHash as $key => $temp_descriptor)
		{
			if (is_array($temp_descriptor) && !array_key_exists('tmp_name', $temp_descriptor))
			{
				$descriptors = $temp_descriptor;
			}
			else
			{
				$descriptors[] = $temp_descriptor;
			}

			unset($temp_descriptor);

			foreach ($descriptors as $descriptor)
			{
				$files = array();

				if (is_array($descriptor['tmp_name']))
				{
					foreach ($descriptor['tmp_name'] as $key => $value)
					{
						$files[] = array(
							'name'     => $descriptor['name'][$key],
							'type'     => $descriptor['type'][$key],
							'tmp_name' => $descriptor['tmp_name'][$key],
							'error'    => $descriptor['error'][$key],
							'size'     => $descriptor['size'][$key],
						);
					}
				}
				else
				{
					$files[] = $descriptor;
				}

				foreach ($files as $fileDescriptor)
				{
					$tempNames = $fileDescriptor['tmp_name'];
					$intendedNames = $fileDescriptor['name'];

					if (!is_array($tempNames))
					{
						$tempNames = array($tempNames);
					}

					if (!is_array($intendedNames))
					{
						$intendedNames = array($intendedNames);
					}

					$len = count($tempNames);

					for ($i = 0; $i < $len; $i++)
					{
						$tempName = array_shift($tempNames);
						$intendedName = array_shift($intendedNames);

						$extraInfo = "File descriptor :\n";
						$extraInfo .= print_r($fileDescriptor, true);
						$extraInfo .= "\n";

						// 1. Null byte check
						if (strstr($intendedName, "\u0000"))
						{
							$this->exceptionsHandler->blockRequest('uploadshield', null, $extraInfo);

							return;
						}

						// 2. PHP-in-extension check
						$explodedName = explode('.', $intendedName);
						$explodedName = array_reverse($explodedName);

						// 2a. File extension is .php
						if ((count($explodedName) > 1) && (strtolower($explodedName[0]) == 'php'))
						{
							$this->exceptionsHandler->blockRequest('uploadshield', null, $extraInfo);

							return;
						}

						// 2a. File extension is php.xxx
						if ((count($explodedName) > 2) && (strtolower($explodedName[1]) == 'php'))
						{
							$this->exceptionsHandler->blockRequest('uploadshield', null, $extraInfo);

							return;
						}

						// 2b. File extensions is php.xxx.yyy
						if ((count($explodedName) > 3) && (strtolower($explodedName[2]) == 'php'))
						{
							$this->exceptionsHandler->blockRequest('uploadshield', null, $extraInfo);

							return;
						}

						// 3. Contents scanner
						$fp = @fopen($tempName, 'r');

						if ($fp !== false)
						{
							// Initialise
							$data = '';
							$extension = strtolower($explodedName[0]);
							$possibleFileForShortTagSyntax = in_array($extension, array(
								'inc', 'phps', 'class', 'php3', 'php4', 'txt', 'dat',  'tpl', 'tmpl'
							));

							// Process the file in 128Kb chunks
							while (!feof($fp))
							{
								// Read 128Kb and add it to the existing data (the last 4 bytes of the previous scan)
								$buffer = @fread($fp, 131072);
								$data .= $buffer;

								// Do we have a regular PHP tag?
								if (stristr($buffer, '<?php'))
								{
									$this->exceptionsHandler->blockRequest('uploadshield', null, $extraInfo);

									return;
								}

								// If we have text file which may have the short tag (<?) in it...
								if ($possibleFileForShortTagSyntax)
								{
									// ...do I have a short tag?
									if (strstr($buffer, '<?'))
									{
										$this->exceptionsHandler->blockRequest('uploadshield', null, $extraInfo);

										return;
									}
								}

								// Keep the last 4 bytes of data to make sure we can catch partial strings.
								$data = substr($data, -4);

								// WARNING: Do NOT try seek to an earlier position! Here's how it all works.
								//
								// We just need to keep the last four bytes in $data so we can append the next 128Kb.
								// This way if the start of the tag is in the previous block and the rest is in the next
								// 128Kb block we can still scan it. The value 4 is not random. <?php is 5 characters
								// and the longest string we're trying to detect. If it existed in this block we'd have
								// already found it and blocked it. Therefore the only possibility is that this block
								// ended in <, <?, <?p or <?ph with the rest of the string (?php, php, hp or p
								// respectively) being present in the next block. The longest of these partial strings
								// is "<?ph" which is FOUR characters.
								//
								// Do NOT seek to an earlier file position. It would be a rather silly thing to do.
							}

							fclose($fp);
						}
					}
				}
			}
		}
	}
}