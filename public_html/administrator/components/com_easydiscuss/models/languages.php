<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once dirname( __FILE__ ) . '/model.php';

class EasyDiscussModelLanguages extends EasyDiscussAdminModel
{
	public $data = null;
	
	/**
	 * Retrieve languages
	 *
	 * @since	4.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function getLanguages()
	{
		if (!$this->data) {
			$db = $this->db;

			$query = $this->_buildQuery();

			$db->setQuery($query);
			$this->data = $db->loadObjectList();
		}

		return $this->data;
	}

	/**
	 * Purges non installed languages
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function purge()
	{
		$db = $this->db;
		$query 	= 'DELETE FROM ' . $db->quoteName('#__discuss_languages');
		$query .= ' WHERE ' . $db->quoteName('state') . ' = ' . ED_LANGUAGES_NOT_INSTALLED;

		$db->setQuery($query);

		return $db->Query();
	}

	public function _buildQuery()
	{
		$db = $this->db;

		$query 	= 'SELECT * FROM ' . $db->quoteName('#__discuss_languages');

		return $query;
	}


	/**
	 * Method to return the total number of rows
	 *
	 * @access public
	 * @return integer
	 */
	public function getTotal()
	{
		// Load total number of rows
		if( empty($this->_total) )
		{
			$this->_total	= $this->_getListCount( $this->_buildQuery() );
		}

		return $this->_total;
	}

	/**
	 * Method to get a pagination object for the events
	 *
	 * @access public
	 * @return integer
	 */
	function &getPagination()
	{
		// Lets load the content if it doesn't already exist
		if ( empty( $this->_pagination ) )
		{
			jimport('joomla.html.pagination');
			$this->_pagination = new JPagination( $this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
		}

		return $this->_pagination;
	}

	/**
	 * Determines if the language rows has been populated
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function initialized()
	{
		$db = $this->db;

		$query = 'SELECT COUNT(1) FROM ' . $db->quoteName('#__discuss_languages');
		$db->setQuery($query);

		$initialized = $db->loadResult() > 0;

		return $initialized;
	}

	/**
	 * Discover new languages
	 *
	 * @since	4.0
	 * @access	public
	 */
	public function discover()
	{
		$config = ED::config();
		$key = $config->get('main_apikey');
		
		$connector = ED::connector();
		$connector->addUrl(ED_LANGUAGES_SERVER);
		$connector->addQuery('key', $key);
		$connector->setMethod('POST');
		$connector->execute();

		$contents = $connector->getResult(ED_LANGUAGES_SERVER);

		if (!$contents) {
			$result = new stdClass();
			$result->message = 'No language found';
			return $result;
		}

		// Decode the result
		$result	= json_decode($contents);

		if ($result->code != 200) {
			$this->setError($result->message);
			return false;
		}

		foreach ($result->languages as $language) {

			// If it does, load it instead of overwriting it.
			$table = ED::table('Language');
			$exists = $table->load(array('locale' => $language->locale));

			// We do not want to bind the id
			unset($language->id);

			// Since this is the retrieval, the state should always be disabled
			if (!$exists) {
				$table->state = ED_LANGUAGES_NOT_INSTALLED;
			}

			// Then check if the language needs to be updated. If it does, update the ->state to EBLOG_LANGUAGES_NEEDS_UPDATING
			// We need to check if the language updated time is greater than the local updated time
			if ($exists && $table->state == ED_LANGUAGES_INSTALLED) {
				$languageTime = strtotime($language->updated);
				$localLanguageTime = strtotime($table->updated);

				if ($languageTime > $localLanguageTime && $table->state == ED_LANGUAGES_INSTALLED) {
					$table->state = ED_LANGUAGES_NEEDS_UPDATING;
				}
			}

			// Set the title
			$table->title = $language->title;

			// Set the locale
			$table->locale = $language->locale;

			// Set the translator
			$table->translator = $language->translator;

			// Set the updated time
			$table->updated = $language->updated;

			// Update the progress
			$table->progress = $language->progress;

			// Update the table with the appropriate params
			$params = new JRegistry();

			$params->set('download' , $language->download);
			$params->set('md5', $language->md5);
			$table->params = $params->toString();

			$table->store();
		}

		return true;
	}
}
