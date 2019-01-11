<?php
/**
 * @package   akeebabackup
 * @copyright Copyright (c)2006-2019 Nicholas K. Dionysopoulos / Akeeba Ltd
 * @license   GNU General Public License version 3, or later
 */

namespace Akeeba\Backup\Admin\Model;

// Protect from unauthorized access
defined('_JEXEC') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;
use FOF30\Model\Model;

/**
 * Model for the archive re-upload to remote storage feature
 */
class Upload extends Model
{
	/**
	 * Upload an archive part to remote storage
	 *
	 * @return bool|int
	 */
	public function upload()
	{
		$id   = $this->getState('id', -1);
		$part = $this->getState('part', -1);
		$frag = $this->getState('frag', -1);

		// Calculate the filenames
		$stat           = Platform::getInstance()->get_statistics($id);
		$local_filename = $stat['absolute_path'];
		$basename       = basename($local_filename);
		$extension      = strtolower(str_replace(".", "", strrchr($basename, ".")));

		$new_extension = $extension;

		if ($part > 0)
		{
			$new_extension = substr($extension, 0, 1) . sprintf('%02u', $part);
		}

		$local_filename = substr($local_filename, 0, -strlen($extension)) . $new_extension;

		// Load the Configuration object
		$savedFactory = $this->container->platform->getSessionVar('upload_factory', null, 'akeeba');

		if ($savedFactory && ($frag > 0))
		{
			Factory::unserialize($savedFactory);
		}
		else
		{
			Platform::getInstance()->load_configuration($stat['profile_id']);
		}

		// Load the post-processing engine
		$config      = Factory::getConfiguration();
		$engine_name = $config->get('akeeba.advanced.postproc_engine');
		$engine      = Factory::getPostprocEngine($engine_name);

		// Start uploading
		$result = $engine->processPart($local_filename);

		// Can't use switch because true == -1 but true !== -1 and we need the latter comparison
		if ($result === true)
		{
			$part++;
			$frag = 0;
		}
		elseif (abs($result) == 1)
		{
			$frag++;
			$this->container->platform->setSessionVar('upload_factory', Factory::serialize(), 'akeeba');
		}
		elseif ($result === false)
		{
			$warning = $engine->getWarning();
			$error   = $engine->getError();

			$this->container->platform->setSessionVar('upload_factory', null, 'akeeba');
			$part = -1;
			$frag = -1;

			throw new \RuntimeException(empty($warning) ? $error : $warning);
		}
		else
		{
			throw new \LogicException("Unexpected result from " . get_class($engine) . ": " . print_r($result, true));
		}

		$remote_filename = $config->get('akeeba.advanced.postproc_engine', '') . '://';
		$remote_filename .= $engine->remote_path;

		if ($part >= 0)
		{
			if ($part >= $stat['multipart'])
			{
				// Update stats with remote filename
				$data = array(
					'remote_filename' => $remote_filename
				);

				Platform::getInstance()->set_or_update_statistics($id, $data, $engine);
			}
		}

		$this->setState('id', $id);
		$this->setState('part', $part);
		$this->setState('frag', $frag);
		$this->setState('stat', $stat);
		$this->setState('remotename', $remote_filename);

		return $result;
	}
}
