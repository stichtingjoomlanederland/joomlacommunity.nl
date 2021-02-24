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

require_once(ED_LIB . '/vendor/autoload.php');

use Nahid\JsonQ\Jsonq;

class EasyDiscussViewSettings extends EasyDiscussAdminView
{
	public function testParser()
	{
		$server = $this->input->get('server', '', 'default');
		$port = $this->input->get('port', '', 'default');
		$service = $this->input->get('service', '', 'default');
		$ssl = $this->input->get('ssl', true, 'bool');
		$user = $this->input->get('username', '', 'default');
		$pass = $this->input->get('password', '', 'default');
		$validate = $this->input->get('validate', '');

		// Variable check
		if (!$server || !$port || !$user || !$pass) {
			return $this->ajax->reject(JText::_( 'COM_EASYDISCUSS_EMAIL_PARSER_PLEASE_COMPLETE_INFO'));
		}

		$result	= ED::mailbox()->testConnect($server, $port, $service, $ssl, 'INBOX', $user, $pass);
		return $this->ajax->resolve($result);
	}

	/**
	 * Display dialog confirmation of restore the logo
	 *
	 * @since	4.1.15
	 * @access	public
	 */
	public function confirmRestoreLogo()
	{
		$theme = ED::themes();
		$contents = $theme->output('admin/dialogs/restore.logo.confirmation');

		return $this->ajax->resolve($contents);
	}

	/**
	 * Rebuilds the search for settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function rebuildSearch()
	{
		$str = $this->input->get('dataString', '', 'raw');

		$jsonObject = json_decode($str);

		foreach ($jsonObject->items as &$item) {

			$item->keywords = array();
			$item->keywords = ED::extractKeyWords($item->label);

			if (isset($item->description)) {
				$item->keywords = array_merge($item->keywords, ED::extractKeyWords($item->description));
			}

			if ($item->keywords) {
				$item->keywords = implode(' ', $item->keywords);
			}
		}

		$jsonString = json_encode($jsonObject);
		$cacheFile = ED_DEFAULTS . '/cache.json';

		JFile::write($cacheFile, $jsonString);

		ED::setMessage('Search cache file updated successfully', 'success');

		return $this->ajax->resolve();
	}

	/**
	 * Searches for a settings
	 *
	 * @since	5.0.0
	 * @access	public
	 */
	public function search()
	{
		$query = $this->input->get('text', '', 'word');
		$query = strtolower($query);

		$jsonString = file_get_contents(ED_DEFAULTS . '/cache.json');
		$jsonString = strtolower($jsonString);

		$jsonq = new Jsonq();
		$jsonq->json($jsonString);

		$result = @$jsonq->from('items')
				->where('keywords', 'contains', $query)
				->groupBy('page')
				->get();
				
		$theme = ED::themes();
		$theme->set('result', $result);
		$contents = $theme->output('admin/settings/search/result');

		return $this->ajax->resolve($contents);
	}
}
