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

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/date/date.php');

class EasyDiscussControllerRules extends EasyDiscussController
{
    public function __construct()
    {
        parent::__construct();

        $this->checkAccess('discuss.manage.rules');
        $this->jConfig = ED::jConfig();
    }

	public function remove()
	{
		// Request forgeries check
		ED::checkToken();

		$ids = $this->input->get('cid', '', 'var');

		$rule = ED::table('Rules');

		// @task: Sanitize the id's to integer.
		foreach ($ids as $id) {
			$rule->load((int) $id);
			$rule->delete();
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_RULE_IS_NOW_DELETED') , 'success');
		ED::redirect('index.php?option=com_easydiscuss&view=rules');
	}

	public function newrule()
	{
		return ED::redirect('index.php?option=com_easydiscuss&view=rules&layout=install');
	}

	public function install()
	{
		// Request forgeries check
		ED::checkToken();

		// $file = $this->input->get('rule', '', 'FILES');
		$file = $this->input->files->get('rule', '');
		$files = array();
		$redirection = 'index.php?option=com_easydiscuss&view=rules&layout=install';

		// @task: If there's no tmp_name in the $file, we assume that the data sent is corrupted.
		if (!isset($file['tmp_name'])) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_RULE_FILE'), ED_MSG_ERROR);
			return ED::redirect($redirection);
		}

		// There are various MIME type for compressed file. So let's check the file extension instead.
		if ($file['name'] && JFile::getExt($file['name']) == 'xml') {
			$files = array($file['tmp_name']);
		} else {
			$path = rtrim($this->jConfig->get('tmp_path'), '/') . '/' . $file['name'];

			// @rule: Copy zip file to temporary location
			if( !JFile::copy($file['tmp_name'], $path)) {
				ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_RULE_FILE'), ED_MSG_ERROR);
				return ED::redirect($redirection);
			}

			jimport('joomla.filesystem.archive');
			$tmp = md5(ED::date()->toSql());
			$dest = rtrim($this->jConfig->get('tmp_path'), '/') . '/' . $tmp;

			if (!EDArchive::extract($path, $dest)) {
				ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_RULE_FILE'), ED_MSG_ERROR);
				return ED::redirect($redirection);
			}

			$files = JFolder::files($dest, '.', true, true);

			if (empty($files)) {
				// Try to do a level deeper in case the zip is on the outer.
				$folder	= JFolder::folders($dest);

				if (!empty($folder)) {
					$files = JFolder::files($dest . '/' . $folder[0] , true);
					$dest = $dest . '/' . $folder[0];
				}
			}

			if (empty($files)) {
				ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_RULE_FILE'), ED_MSG_ERROR);
				return ED::redirect($redirection);
			}
		}

		if (empty($files)) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_RULE_INSTALL_FAILED'), ED_MSG_ERROR);
			return ED::redirect($redirection);
		}

		foreach ($files as $file) {
			$this->installXML($file);
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_RULE_INSTALL_SUCCESS'), 'success');

		return ED::redirect($redirection);
	}

	private function installXML($path)
	{
		// @task: Try to read the temporary file.
		$contents = file_get_contents($path);
		$parser = ED::getXML($contents, false);

		// @task: Bind appropriate values from the xml file into the database table.
		$rule = ED::table('Rules');

		$rule->command = (string) $parser->command;
		$rule->title = (string) $parser->title;
		$rule->description = (string) $parser->description;

		$rule->set('published', 1);
		$rule->set('created', ED::date()->toSql());

		if ($rule->exists($rule->command)) {
			return;
		}

		$state = $rule->store();

		if ($state) {
			$actionlog = ED::actionlog();
			$actionlog->log('COM_ED_ACTIONLOGS_CREATED_RULES', 'rules', array(
				'ruleTitle' => $rule->title
			));
		}

		return $state;
	}
}
