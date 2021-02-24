<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class EasyDiscussHoneypot extends EasyDiscuss
{
	/**
	 * Retrieves the honeypot key to be used in the form
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function getKey()
	{
		static $key = null;

		// If there is no key, generate one
		if (is_null($key)) {

			$key = $this->config->get('antispam_honeypot_key');

			if (!$key) {
				$key = $this->updateKey();
			}
		}

		return $key;
	}

	/**
	 * Generates a random word
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function generateKey($length = 8)
	{
		$string = '';
		$vowels = array("a","e","i","o","u");

		$consonants = array(
			'b', 'c', 'd', 'f', 'g', 'h', 'j', 'k', 'l', 'm',
			'n', 'p', 'r', 's', 't', 'v', 'w', 'x', 'y', 'z'
		);

		// Seed it
		srand((double) microtime() * 1000000);

		$max = $length/2;

		for ($i = 1; $i <= $max; $i++) {
			$string .= $consonants[rand(0,19)];
			$string .= $vowels[rand(0,4)];
		}

		return $string;
	}

	/**
	 * Determines if the spammer is trapped
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function isTrapped($type)
	{
		$enabled = $this->config->get('antispam_honeypot_' . $type);

		if (!$enabled) {
			return false;
		}

		$key = $this->getKey();

		$value = $this->input->get($key, '', 'default');

		if ($value) {
			$this->log($type);

			return true;
		}

		return false;
	}

	/**
	 * Generates a log for honeypot
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function log($type)
	{
		$data = $this->input->post->getArray();

		$table = ED::table('Honeypot');
		$table->type = $type;
		$table->key = $this->getKey();
		$table->data = json_encode($data);
		$table->created = JFactory::getDate()->toSql();
		$table->store();

		return $table;
	}


	/**
	 * Updates the honeypot key
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function updateKey()
	{
		$key = $this->generateKey();

		$data = array(
			'antispam_honeypot_key' => $key,
			'antispam_honeypot_lastupdate' => time()
		);

		$model = ED::model('Settings');
		$model->save($data);

		return $key;
	}
}
