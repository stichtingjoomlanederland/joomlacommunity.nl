<?php
/**
* @package RSForm! Pro
* @copyright (C) 2007-2014 www.rsjoomla.com
* @license GPL, http://www.gnu.org/copyleft/gpl.html
*/

defined('_JEXEC') or die('Restricted access');

class RsformModelDirectory extends JModelLegacy
{
    protected $fields;
    protected $_db;
    protected $_app;
    public $params;

    /**
     *    Main constructor
     */
    public function __construct($config = array())
    {
        $this->_app     = JFactory::getApplication();
        $this->_db      = JFactory::getDbo();
        $this->params   = $this->_app->getParams('com_rsform');
        $this->itemid   = $this->getItemid();
        $this->context  = 'com_rsform.directory' . $this->itemid;

        // Check for a valid form
        if (!$this->isValid()) {
            throw new Exception($this->getError(), 500);
        }

        $this->getFields();
        parent::__construct($config);
    }

    /**
     *    Check if we are allowed to show content
     */
    public function isValid()
    {
        if (!$this->params->get('enable_directory', 0)) {
            $this->setError(JText::_('RSFP_VIEW_DIRECTORY_NOT_ENABLED_FORGOT'));
            return false;
        }

        // Do we have a valid formId
        $formId = $this->params->get('formId', 0);
        if (empty($formId)) {
            $this->setError(JText::sprintf('RSFP_VIEW_DIRECTORY_NO_VALID_FORMID', $formId));
            return false;
        }

        // Check if the directory exists
        $this->_db->setQuery('SELECT COUNT(' . $this->_db->qn('formId') . ') FROM ' . $this->_db->qn('#__rsform_directory') . ' WHERE ' . $this->_db->qn('formId') . ' = ' . (int)$formId . '');
        if (!$this->_db->loadResult()) {
            $this->setError(JText::_('RSFP_VIEW_DIRECTORY_NOT_SAVED_YET'));
            return false;
        }

        return true;
    }

    /**
     *    Get directory fields
     */
    public function getFields()
    {
        if (!is_array($this->fields)) {
            $this->fields = RSFormProHelper::getDirectoryFields($this->params->get('formId'));
        }

        return $this->fields;
    }

    /**
     *    Submissions query
     */
    public function getListQuery()
    {
        // Get query
        $db = &$this->_db;
        $query = $db->getQuery(true);

        // Get headers
        $fields = $this->getFields();
        $headers = RSFormProHelper::getDirectoryStaticHeaders();

        // Check if it's a search.
        $search = $this->getSearch();

        // Get the SubmissionId
        $query->select($db->qn('s.SubmissionId'))
            ->from($db->qn('#__rsform_submission_values', 'sv'))
            ->join('left', $db->qn('#__rsform_submissions', 's') . ' ON (' . $db->qn('sv.SubmissionId') . '=' . $db->qn('s.SubmissionId') . ')')
            ->where($db->qn('s.FormId') . '=' . $db->q($this->params->get('formId')))
            ->group($db->qn('s.SubmissionId'))
            ->order($db->qn($this->getListOrder()) . ' ' . $db->escape($this->getListDirn()));

        // Show only confirmed submissions?
        if ($this->params->get('show_confirmed', 0)) {
            $query->where($db->qn('s.confirmed') . '=' . $db->q(1));
        }

        // Show only submissions for selected language
        if ($lang = $this->params->get('lang', '')) {
            $query->where($db->qn('s.Lang') . '=' . $db->q($lang));
        }

        // Check if we need to show only submissions related to UserId.
        $userId = $this->params->def('userId', 0);
        if ($userId == 'login') {
            // Get only logged in user's submissions
            $user = JFactory::getUser();

            // Do not continue if he's a guest.
            if ($user->guest) {
                return false;
            }

            $query->where($db->qn('s.UserId') . '=' . $db->q($user->get('id')));
        } elseif ($userId) {
            // Show only the submissions of these users
            $userIds = explode(',', $userId);
            $userIds = array_map('intval', $userIds);

            $query->where($db->qn('s.UserId') . ' IN (' . implode(',', $userIds) . ')');
        }

        // Iterate through fields to build the query
        foreach ($fields as $field) {
            // If the field is viewable or searchable, we need to select() it.
            if ($field->viewable || $field->searchable) {
                if ($field->componentId < 0 && isset($headers[$field->componentId])) {
                    // Static headers.
                    // Select the value.
                    if ($field->FieldName == 'confirmed') {
                        // Make sure we display a text instead of 0 and 1.
                        $query->select('IF(' . $db->qn('s.confirmed') . ' = ' . $db->q(1) . ', ' . $db->q(JText::_('RSFP_YES')) . ', ' . $db->q(JText::_('RSFP_NO')) . ') AS ' . $db->qn('confirmed'));
                    } else {
                        $query->select($db->qn('s.' . $field->FieldName));
                    }
                } else {
                    // Dynamic headers.
                    // Select the value.
                    $query->select('GROUP_CONCAT(IF(' . $db->qn('sv.FieldName') . '=' . $db->q($field->FieldName) . ', ' . $db->qn('sv.FieldValue') . ', NULL)) AS ' . $db->qn($field->FieldName));
                }

                // If we're searching, add the field to the having() query.
                if ($search && $field->searchable) {
                    // DateSubmitted doesn't play well with LIKE
                    if ($field->FieldId == '-1' && preg_match('#([^0-9\-: ])#', $search)) {
                        continue;
                    }
                    $query->having($db->qn($field->FieldName) . ' LIKE ' . $db->q('%' . $db->escape($search, true) . '%', false), 'OR');
                }
            }
        }

        return $query;
    }

    public function setGroupConcatLimit()
    {
        $this->_db->setQuery("SET SESSION `group_concat_max_len` = 1000000");
        $this->_db->execute();
    }

    /**
     *    Get Submissions
     */
    public function getItems()
    {
        $mainframe = JFactory::getApplication();

        $this->setGroupConcatLimit();
        if ($query = $this->getListQuery()) {
            $this->_db->setQuery($query, $this->getStart(), $this->getLimit());
            $items = $this->_db->loadObjectList();
        } else {
            $items = array();
        }

        // small workaround - we need to have only string keys for the items
        foreach ($items as $i => $item) {
            $newItem = new stdClass();
            foreach ($item as $key => $value) {
                $newItem->{((string)$key)} = $value;
            }
            $items[$i] = $newItem;
        }

        $mainframe->triggerEvent('rsfp_onAfterManageDirectoriesQuery', array(&$items, $this->params->get('formId')));
        jimport('joomla.filesystem.file');

        list($multipleSeparator, $uploadFields, $multipleFields, $secret) = RSFormProHelper::getDirectoryFormProperties($this->params->get('formId'));
        $this->uploadFields = $uploadFields;
        $this->multipleFields = $multipleFields;

        if ($items) {
            foreach ($items as $i => $item) {
                foreach ($uploadFields as $field) {
                    if (isset($item->$field)) {
                        $item->$field = '<a href="' . JRoute::_('index.php?option=com_rsform&task=submissions.view.file&hash=' . md5($item->SubmissionId . $secret . $field)) . '">' . RSFormProHelper::htmlEscape(JFile::getName($item->$field)) . '</a>';
                    }
                }
                foreach ($multipleFields as $field) {
                    if (isset($item->$field)) {
                        $item->$field = str_replace("\n", $multipleSeparator, RSFormProHelper::htmlEscape($item->$field));
                    }
                }
                $items[$i] = $item;
            }
        }

        return $items;
    }

    public function getAdditionalUnescaped()
    {
        $unescapedFields = array();

        JFactory::getApplication()->triggerEvent('rsfp_b_onManageDirectoriesCreateUnescapedFields', array(array('fields' => & $unescapedFields, 'formId' => $this->params->get('formId'))));

        return $unescapedFields;

    }

    /**
     *    Get directory details
     */
    public function getDirectory()
    {
        static $table;

        if (is_null($table)) {
            $formId = $this->params->get('formId', 0);
            $table = JTable::getInstance('RSForm_Directory', 'Table');
            $table->load($formId);
        }

        return $table;
    }

    public function getTemplate()
    {
        $cid = $this->_app->input->getInt('id', 0);
        $format = $this->_app->input->get('format');
        $user = JFactory::getUser();
        $userId = $this->params->def('userId', 0);
        $directory = $this->getDirectory();
        $template = $directory->ViewLayout;

        if ($userId != 'login' && $userId != 0) {
            $userId = explode(',', $userId);
            $userId = array_map('intval', $userId);
        }

        // Grab submission
        require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';
        $submission = RSFormProSubmissionsHelper::getSubmission($cid, false);

        // Submission doesn't exist
        if (!$submission) {
            $this->_app->enqueueMessage(JText::sprintf('RSFP_SUBMISSION_DOES_NOT_EXIST', $cid), 'warning');
            return $this->_app->redirect(JUri::root());
        }

        // Submission doesn't belong to the configured form ID OR
        // can view only own submissions and not his own OR
        // can view only specified user IDs and this doesn't belong to any of the IDs
        if (($submission->FormId != $this->params->get('formId')) || ($userId == 'login' && $submission->UserId != $user->get('id')) || (is_array($userId) && !in_array($user->get('id'), $userId))) {
            $this->_app->enqueueMessage(JText::sprintf('RSFP_SUBMISSION_NOT_ALLOWED', $cid), 'warning');
            return $this->_app->redirect(JUri::root());
        }

        if ($this->params->get('show_confirmed', 0) && !$submission->confirmed) {
            $this->_app->enqueueMessage(JText::sprintf('RSFP_SUBMISSION_NOT_CONFIRMED', $cid), 'warning');
            return $this->_app->redirect(JUri::root());
        }

        $confirmed = $submission->confirmed ? JText::_('RSFP_YES') : JText::_('RSFP_NO');
        list($replace, $with) = RSFormProHelper::getReplacements($cid, true);
        list($replace2, $with2) = $this->getReplacements($submission->UserId);
        $replace = array_merge($replace, $replace2, array('{global:userip}', '{global:date_added}', '{global:submissionid}', '{global:submission_id}', '{global:confirmed}', '{global:lang}', '{global:formid}'));
        $with = array_merge($with, $with2, array($submission->UserIp, RSFormProHelper::getDate($submission->DateSubmitted), $cid, $cid, $confirmed, $submission->Lang, $submission->FormId));

        if ($format == 'pdf') {
            if (strpos($template, ':path}') !== false) {
                $template = str_replace(':path}', ':localpath}', $template);
            }

            $template = str_replace('{sitepath}', JPATH_SITE, $template);
        } else {
            $template = str_replace('{sitepath}', JUri::root(), $template);
        }

        if (strpos($template, '{/if}') !== false) {
            require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/scripting.php';
            RSFormProScripting::compile($template, $replace, $with);
        }

        $detailsLayout = str_replace($replace, $with, $template);
        eval($directory->DetailsScript);

        // Set filename
        $directory->filename = str_replace($replace, $with, $directory->filename);

        return $detailsLayout;
    }

    public function getReplacements($user_id)
    {
        static $sitename, $siteurl, $mailfrom, $fromname;

        if (is_null($siteurl)) {
            $config = JFactory::getConfig();
            $sitename = $config->get('sitename');
            $siteurl = JUri::root();
            $mailfrom = $config->get('mailfrom');
            $fromname = $config->get('fromname');
        }

        $user = JFactory::getUser((int)$user_id);
        $replace = array('{global:sitename}', '{global:siteurl}', '{global:userid}', '{global:username}', '{global:email}', '{global:useremail}', '{global:fullname}', '{global:mailfrom}', '{global:fromname}');
        $with = array($sitename, $siteurl, $user->id, $user->username, $user->email, $user->email, $user->name, $mailfrom, $fromname);

        return array($replace, $with);
    }

    public function delete($id)
    {
        require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';
        RSFormProSubmissionsHelper::deleteSubmissions($id, true);
    }

	public function save() {
		jimport('joomla.filesystem.file');
		jimport('joomla.filesystem.folder');

		$app        = JFactory::getApplication();
		$db         = JFactory::getDbo();
		$cid    	= $app->input->getInt('id');
        $formId 	= $app->input->getInt('formId');
        $form       = $app->input->post->get('form', array(), 'array');
        $static     = $app->input->post->get('formStatic', array(), 'array');
        $files      = $app->input->files->get('form', array(), 'array');
		$validation = RSFormProHelper::validateForm($formId, 'directory', $cid);

		if (!empty($validation)) {
			return false;
		}

		$formFields 	= RSFormProHelper::getDirectoryFields($formId);
		$headers 		= RSFormProHelper::getDirectoryStaticHeaders();
		$staticFields   = array();
		$allowed		= array();
		foreach ($formFields as $field) {
			if ($field->editable) {
				if ($field->componentId < 0 && isset($headers[$field->componentId])) {
					$staticFields[] = $field->FieldName;
				} else {
					$allowed[] = $field->FieldName;
				}
			}
		}

		//Trigger Event - onBeforeDirectorySave
		$this->_app->triggerEvent('rsfp_f_onBeforeDirectorySave', array(array('SubmissionId'=>&$cid,'formId'=>$formId,'post'=>&$form)));

		// Handle file uploads first
		if (!empty($files))
		{
            foreach ($files as $field => $file)
            {
                if (!in_array($field, $allowed) || $file['error'])
                {
                    continue;
                }

                // The above $validation should suffice
                $query = $db->getQuery(true)
                    ->select($db->qn('FieldValue'))
                    ->from($db->qn('#__rsform_submission_values'))
                    ->where($db->qn('FieldName') . ' = ' . $db->q($field))
                    ->where($db->qn('SubmissionId') . ' = ' . $db->q($cid));

                $original = $db->setQuery($query)->loadResult();

                // Prefix
                $componentId = RSFormProHelper::getComponentId($field, $formId);
                $data = RSFormProHelper::getComponentProperties($componentId);
                $prefix = uniqid('') . '-';
                if (isset($data['PREFIX']) && strlen(trim($data['PREFIX'])) > 0)
                    $prefix = RSFormProHelper::isCode($data['PREFIX']);

                // Path
                $realpath = realpath($data['DESTINATION'] . DIRECTORY_SEPARATOR);
                if (substr($realpath, -1) != DIRECTORY_SEPARATOR)
                    $realpath .= DIRECTORY_SEPARATOR;

                // Filename
                $path = $realpath . $prefix . $file['name'];

                // Upload File
                if ($file != $original && JFile::upload($file['tmp_name'], $path, false, (bool) RSFormProHelper::getConfig('allow_unsafe')))
                {
                    // Remove the original file to save up space
                    if (file_exists($original) && is_file($original))
                    {
                        JFile::delete($original);
                    }

                    // Add to db (submission value)
                    $form[$field] = $path;
                }
            }
        }

		// Update fields
		foreach ($form as $field => $value)
		{
			if (!in_array($field, $allowed)) {
				continue;
			}

			if (is_array($value)) {
				$value = implode("\n", $value);
			}

			// Dynamic field - update value.
			$this->_db->setQuery("SELECT SubmissionValueId, FieldValue FROM #__rsform_submission_values WHERE FieldName='".$this->_db->escape($field)."' AND SubmissionId='".$cid."' LIMIT 1");
			$original = $this->_db->loadObject();
			if (!$original)
			{
				$this->_db->setQuery("INSERT INTO #__rsform_submission_values SET FormId='".$formId."', SubmissionId='".$cid."', FieldName='".$this->_db->escape($field)."', FieldValue='".$this->_db->escape($value)."'");
				$this->_db->execute();
			}
			else
			{
				// Update only if we've changed something
				if ($original->FieldValue !== $value)
				{
					$this->_db->setQuery("UPDATE #__rsform_submission_values SET FieldValue='".$this->_db->escape($value)."' WHERE SubmissionValueId='".$original->SubmissionValueId."' LIMIT 1");
					$this->_db->execute();
				}
			}
		}

		$offset = JFactory::getConfig()->get('offset');

		if ($static && $staticFields) {
			// Static, update submission
			$query = $this->_db->getQuery(true);
			$query->update('#__rsform_submissions')
				  ->where($this->_db->qn('SubmissionId').'='.$this->_db->q($cid));

			foreach ($staticFields as $field) {
				if (!isset($static[$field])) {
					$static[$field] = '';
				}

				if ($field == 'DateSubmitted') {
					$static[$field] = JFactory::getDate($static[$field], $offset)->toSql();
				}

				$query->set($this->_db->qn($field).'='.$this->_db->q($static[$field]));
			}

			$this->_db->setQuery($query);
			$this->_db->execute();
		}

		// Checkboxes don't send a value if nothing is checked
		$checkboxesWhere = '';
		if ($editFields = $this->getEditFields()) {
			$allowedFields = array();
			foreach ($editFields as $field) {
				$allowedFields[] = $this->_db->q($field[3]);
			}

			if (!empty($allowedFields)) {
				$checkboxesWhere = "AND p.PropertyValue IN (".implode(',',$allowedFields).")";
			}
		}

		$this->_db->setQuery("SELECT p.PropertyValue FROM #__rsform_components c LEFT JOIN #__rsform_properties p ON (c.ComponentId=p.ComponentId) WHERE c.ComponentTypeId='4' AND p.PropertyName='NAME' AND c.FormId='".$formId."' ".$checkboxesWhere);
		$checkboxes = $this->_db->loadColumn();

		foreach ($checkboxes as $checkbox) {
			$value = isset($form[$checkbox]) ? $form[$checkbox] : '';
			if (is_array($value))
				$value = implode("\n", $value);

			$this->_db->setQuery("UPDATE #__rsform_submission_values SET FieldValue='".$this->_db->escape($value)."' WHERE FieldName='".$this->_db->escape($checkbox)."' AND FormId='".$formId."' AND SubmissionId='".$cid."' LIMIT 1");
			$this->_db->execute();
		}

		// Send emails
		$this->sendEmails($formId, $cid);
		return true;
	}

	public function sendEmails($formId, $SubmissionId) {
		$directory = $this->getDirectory();

		$this->_db->setQuery("SELECT Lang FROM #__rsform_submissions WHERE FormId='".$formId."' AND SubmissionId='".$SubmissionId."'");
		$lang = $this->_db->loadResult();

		list($placeholders,$values) = RSFormProHelper::getReplacements($SubmissionId);

		$this->_db->setQuery("SELECT * FROM #__rsform_emails WHERE `type` = 'directory' AND `formId` = ".$formId." AND `from` != ''");
		if ($emails = $this->_db->loadObjectList()) {
			$etranslations = RSFormProHelper::getTranslations('emails', $formId, $lang);
			foreach ($emails as $email) {
				if (isset($etranslations[$email->id.'.fromname'])) {
					$email->fromname = $etranslations[$email->id.'.fromname'];
				}
				if (isset($etranslations[$email->id.'.subject'])) {
					$email->subject = $etranslations[$email->id.'.subject'];
				}
				if (isset($etranslations[$email->id.'.message'])) {
					$email->message = $etranslations[$email->id.'.message'];
				}

				if (empty($email->fromname) || empty($email->subject) || empty($email->message)) {
					continue;
				}

				$directoryEmail = array(
					'to' 		=> $email->to,
					'cc' 		=> $email->cc,
					'bcc' 		=> $email->bcc,
					'from' 		=> $email->from,
					'replyto' 	=> $email->replyto,
					'fromName' 	=> $email->fromname,
					'text' 		=> $email->message,
					'subject' 	=> $email->subject,
					'mode' 		=> $email->mode,
					'files' 	=> array()
				);
				
				eval($directory->EmailsCreatedScript);
				
				// RSForm! Pro Scripting
				// performance check
				if (strpos($directoryEmail['text'], '{/if}') !== false) {
					require_once JPATH_ADMINISTRATOR.'/components/com_rsform/helpers/scripting.php';
					RSFormProScripting::compile($directoryEmail['text'], $placeholders, $values);
				}
				
				// Replace placeholders
				$directoryEmail = str_replace($placeholders, $values, $directoryEmail);

				// additional cc
				if (strpos($directoryEmail['cc'], ',') !== false)
					$directoryEmail['cc'] = explode(',', $directoryEmail['cc']);
				// additional bcc
				if (strpos($directoryEmail['bcc'], ',') !== false)
					$directoryEmail['bcc'] = explode(',', $directoryEmail['bcc']);

				//Trigger Event - beforeDirectoryEmail
				$this->_app->triggerEvent('rsfp_beforeDirectoryEmail', array(array('directory' => &$directory, 'placeholders' => &$placeholders, 'values' => &$values, 'submissionId' => $SubmissionId, 'directoryEmail'=>&$directoryEmail)));

				eval($directory->EmailsScript);

				// mail users
				$recipients = explode(',',$directoryEmail['to']);
				if(!empty($recipients))
					foreach($recipients as $recipient)
						if(!empty($recipient))
							RSFormProHelper::sendMail($directoryEmail['from'], $directoryEmail['fromName'], $recipient, $directoryEmail['subject'], $directoryEmail['text'], $directoryEmail['mode'], !empty($directoryEmail['cc']) ? $directoryEmail['cc'] : null, !empty($directoryEmail['bcc']) ? $directoryEmail['bcc'] : null, $directoryEmail['files'], !empty($directoryEmail['replyto']) ? $directoryEmail['replyto'] : '');

			}
		}
	}

	public function getUploadFields() {
		return $this->uploadFields;
	}

	public function getMultipleFields() {
		return $this->multipleFields;
	}

	public function getTotal() {
		if ($query = $this->getListQuery()) {
			$this->_db->setQuery($query);
			$this->_db->execute();

			return $this->_db->getNumRows();
		} else {
			return 0;
		}
	}

	public function getPagination() {
		jimport('joomla.html.pagination');
		return new JPagination($this->getTotal(), $this->getStart(), $this->getLimit());
	}

	public function getStart() {
		static $limitstart;
		if (is_null($limitstart)) {
			$limitstart	= JFactory::getApplication()->input->get('limitstart', 0, '', 'int');
		}
		return $limitstart;
	}

	public function getLimit() {
		static $limit;
		if (is_null($limit)) {
			$limit = JFactory::getApplication()->input->get('limit', $this->params->get('display_num'), '', 'int');
		}

		return $limit;
	}

	public function getSearch() {
		return $this->_app->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', '', 'string');
	}

	public function getListOrder() {
		return $this->_app->getUserStateFromRequest($this->context.'.filter.filter_order', 'filter_order', 'SubmissionId', '');
	}

	public function getListDirn() {
		return $this->_app->getUserStateFromRequest($this->context.'.filter.filter_order_Dir', 'filter_order_Dir', 'desc', 'word');
	}

	public function getEditFields() {
		$id	= $this->_app->input->getInt('id',0);
		return RSFormProHelper::getEditFields($id);
	}

	// Get current Itemid
	public function getItemid() {
		if ($menu = $this->_app->getMenu()) {
			$active = $menu->getActive();
			return isset($active->id) ? $active->id : 0;
		} else {
			return 0;
		}
	}
}