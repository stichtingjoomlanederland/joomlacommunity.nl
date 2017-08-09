<?php
/**
* @package		EasyDiscuss
* @copyright	Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

require_once(JPATH_ADMINISTRATOR . '/components/com_easydiscuss/includes/date/date.php');

class EasyDiscussControllerRules extends EasyDiscussController
{
    public function __construct()
    {
        parent::__construct();
        $this->jConfig = ED::jConfig();

        $this->checkAccess('discuss.manage.rules');
    }

	public function remove()
	{
		// Request forgeries check
		ED::checkToken();
		$ids = $this->input->get('cid', '', 'var');

		// @task: Sanitize the id's to integer.
		foreach ($ids as $id) {
			
			$id	= (int) $id;
			$rule = ED::table('BadgesRules');
			$rule->load($id);
			$rule->delete();
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_RULE_IS_NOW_DELETED') , 'success');
		$this->app->redirect('index.php?option=com_easydiscuss&view=rules');
	}

	public function newrule()
	{
		return $this->app->redirect('index.php?option=com_easydiscuss&view=rules&layout=install');
	}

	public function install()
	{
		// Request forgeries check
		ED::checkToken();

		// $file = $this->input->get('rule', '', 'FILES');
		$file = $this->input->files->get('rule', '');
		$files = array();

		// @task: If there's no tmp_name in the $file, we assume that the data sent is corrupted.
		if (!isset($file['tmp_name'])) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_RULE_FILE'), 'error');
			return $this->app->redirect('index.php?option=com_easydiscuss&view=rules&layout=install');
		}

		// There are various MIME type for compressed file. So let's check the file extension instead.
		if ($file['name'] && JFile::getExt($file['name']) == 'xml') {
			$files = array($file['tmp_name']);
		} else {
			$path = rtrim($this->jConfig->get('tmp_path'), '/') . '/' . $file['name'];

			// @rule: Copy zip file to temporary location
			if( !JFile::copy($file['tmp_name'], $path)) {
				ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_RULE_FILE'), 'error');
				return $this->app->redirect('index.php?option=com_easydiscuss&view=rules&layout=install');
			}

			jimport('joomla.filesystem.archive');
			$tmp = md5(ED::date()->toSql());
			$dest = rtrim($this->jConfig->get('tmp_path'), '/') . '/' . $tmp;

			if (!JArchive::extract($path, $dest)) {
				ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_RULE_FILE'), 'error');
				return $this->app->redirect('index.php?option=com_easydiscuss&view=rules&layout=install');
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
				ED::setMessage(JText::_('COM_EASYDISCUSS_INVALID_RULE_FILE'), 'error');
				return $this->app->redirect('index.php?option=com_easydiscuss&view=rules&layout=install');
			}
		}

		if (empty($files)) {
			ED::setMessage(JText::_('COM_EASYDISCUSS_RULE_INSTALL_FAILED'), 'error');
			return $this->app->redirect('index.php?option=com_easydiscuss&view=rules&layout=install');
		}

		foreach ($files as $file) {
			$this->installXML($file);
		}

		ED::setMessage(JText::_('COM_EASYDISCUSS_RULE_INSTALL_SUCCESS'), 'success');

		return $this->app->redirect('index.php?option=com_easydiscuss&view=rules&layout=install');
	}

	private function installXML($path)
	{
		// @task: Try to read the temporary file.
		$contents = JFile::read($path);
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

		return $rule->store();
	}
}
