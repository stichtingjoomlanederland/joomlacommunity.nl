<?php
/**
 * @package AkeebaBackup
 *
 * @license GNU General Public License, version 2 or later
 * @author Nicholas K. Dionysopoulos
 * @copyright Copyright 2006-2014 Nicholas K. Dionysopoulos
 * @since 1.3
 */
defined('_JEXEC') or die();

use Akeeba\Engine\Factory;
use Akeeba\Engine\Platform;

/**
 * Akeeba statistics model class
 * used for all requirements of backup statistics in JP
 *
 */
class AkeebaModelStatistics extends F0FModel
{
	/** @var JPagination The JPagination object, used in the GUI */
	private $_pagination;

	/**
	 * Constructor.
	 */
	public function __construct($config = array())
	{
		parent::__construct($config);

		// Get the pagination request variables (we only have them if we're not in CLI
        if(JFactory::$application)
        {
            $app = JFactory::getApplication();
        }
        else
        {
            $app = new stdClass();
        }


		if(!($app instanceof JApplication || $app instanceof JApplicationAdministrator)) {
			$limit = 0;
			$limitstart = 0;
		} else {
			$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'));
			$limitstart = $app->getUserStateFromRequest('com_akeebaprofileslimitstart','limitstart',0);
		}

		// Set the page pagination variables
		$this->setState('limit',$limit);
		$this->setState('limitstart',$limitstart);

		$this->table = 'stat';
	}


	/**
	 * Returns the same list as getStatisticsList(), but includes an extra field
	 * named 'meta' which categorises attempts based on their backup archive status
	 *
	 * @return array An object array of backup attempts
	 */
	public function &getStatisticsListWithMeta($overrideLimits = false, $filters = null, $order = null)
	{
		$limitstart = $this->getState('limitstart');
		$limit = $this->getState('limit');

		if($overrideLimits)
		{
			$limitstart = 0;
			$limit = 0;
			$filters = null;
		}
		$allStats = Platform::getInstance()->get_statistics_list(array(
			'limitstart'	=> $limitstart,
			'limit'			=> $limit,
			'filters'		=> $filters,
			'order'			=> $order
		));
		$valid = Platform::getInstance()->get_valid_backup_records();
		if(empty($valid)) $valid = array();

		// This will hold the entries whose files are no longer present and are
		// not already marked as such in the database
		$updateNonExistent = array();

		if(!empty($allStats))
		{
			$new_stats = array();

			foreach($allStats as $stat)
			{
				$total_size = 0;
				if(in_array($stat['id'], $valid))
				{
					$archives = Factory::getStatistics()->get_all_filenames($stat);
					$stat['meta'] = (count($archives) > 0) ? 'ok' : 'obsolete';

					if($stat['meta'] == 'ok')
					{
						if($stat['total_size']) {
							$total_size = $stat['total_size'];
						} else {
							$total_size = 0;
							foreach($archives as $filename)
							{
								$total_size += @filesize($filename);
							}
						}

					}
					else
					{
						if($stat['total_size']) {
							$total_size = $stat['total_size'];
						}
						if($stat['filesexist']) {
							$updateNonExistent[] = $stat['id'];
						}

						// If there is a "remote_filename", the record is "remote", not "obsolete"
						if($stat['remote_filename']) {
							$stat['meta'] = 'remote';
						}
					}
					$stat['size'] = $total_size;
				}
				else
				{
					switch($stat['status'])
					{
						case 'run':
							$stat['meta'] = 'pending';
							break;

						case 'fail':
							$stat['meta'] = 'fail';
							break;

						default:
							if($stat['remote_filename']) {
								// If there is a "remote_filename", the record is "remote", not "obsolete"
								$stat['meta'] = 'remote';
							} else {
								// Else, it's "obsolete"
								$stat['meta'] = 'obsolete';
							}
							break;
					}
				}
				$new_stats[] = $stat;
			}
		}

		// Update records found as not having files any more
		if(count($updateNonExistent))
		{
			Platform::getInstance()->invalidate_backup_records($updateNonExistent);
		}

		unset($valid);
		return $new_stats;
	}

	/**
	 * Returns the details of the latest backup as HTML
	 *
	 * @return string HTML
	 *
	 * @todo Move this into a helper class
	 */
	public function getLatestBackupDetails()
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true)
			->select('MAX('.$db->qn('id').')')
			->from($db->qn('#__ak_stats'))
			->where($db->qn('origin') .' != '.$db->q('restorepoint'));
		$db->setQuery($query);
		$id = $db->loadResult();

		$backup_types = Factory::getEngineParamsProvider()->loadScripting();

		if(empty($id)) return '<p class="label">'.JText::_('BACKUP_STATUS_NONE').'</p>';

		$record = Platform::getInstance()->get_statistics($id);

		JLoader::import('joomla.utilities.date');

		$statusClass="";
		switch($record['status'])
		{
			case 'run':
				$status = JText::_('STATS_LABEL_STATUS_PENDING');
				$statusClass="label-warning";
				break;

			case 'fail':
				$status = JText::_('STATS_LABEL_STATUS_FAIL');
				$statusClass="label-important";
				break;

			case 'complete':
				$status = JText::_('STATS_LABEL_STATUS_OK');
				$statusClass="label-success";
				break;

			default:
				$status = '';
				$statusClass='';
		}

		switch($record['origin'])
		{
			case 'frontend':
				$origin = JText::_('STATS_LABEL_ORIGIN_FRONTEND');
				break;

			case 'backend':
				$origin = JText::_('STATS_LABEL_ORIGIN_BACKEND');
				break;

			case 'cli':
				$origin = JText::_('STATS_LABEL_ORIGIN_CLI');
				break;

			default:
				$origin = '&ndash;';
				break;
		}

		if(array_key_exists($record['type'],$backup_types['scripts']))
		{
			$type = Platform::getInstance()->translate($backup_types['scripts'][ $record['type'] ]['text']);
		}
		else
		{
			$type = '';
		}

		$startTime = new JDate($record['backupstart']);

		$html = '<table class="table table-striped">';
		$html .= '<tr><td>'.JText::_('STATS_LABEL_START').'</td><td>'.$startTime->format(JText::_('DATE_FORMAT_LC4'), true).'</td></tr>';
		$html .= '<tr><td>'.JText::_('STATS_LABEL_DESCRIPTION').'</td><td>'.$record['description'].'</td></tr>';
		$html .= '<tr><td>'.JText::_('STATS_LABEL_STATUS').'</td><td><span class="label '.$statusClass.'">'.$status.'</span></td></tr>';
		$html .= '<tr><td>'.JText::_('STATS_LABEL_ORIGIN').'</td><td>'.$origin.'</td></tr>';
		$html .= '<tr><td>'.JText::_('STATS_LABEL_TYPE').'</td><td>'.$type.'</td></tr>';
		$html .= '</table>';

		return $html;
	}

    public function notifyFailed()
    {
        $params = JComponentHelper::getParams('com_akeeba');

        // Invalidate stale backups
        Factory::resetState(array(
			'global' => true,
			'log' => false,
			'maxrun' => $params->get('failure_timeout', 180)
		));

        // Get the last execution and search for failed backups AFTER that date
        $last = $this->getLastCheck();

        // Get failed backups
        $filters[] = array('field' => 'status'     , 'operand' => '=' , 'value'   => 'fail');
        $filters[] = array('field' => 'origin'     , 'operand' => '<>', 'value'   => 'restorepoint');
        $filters[] = array('field' => 'backupstart', 'operand' => '>' , 'value'   => $last);

        $failed = Platform::getInstance()->get_statistics_list(array('filters' => $filters));

        // Well, everything went ok.
        if(!$failed)
        {
            return array(
                'message' => array("No need to run: no failed backups or notifications were already sent."),
                'result'  => true
            );
        }

        // Whops! Something went wrong, let's start notifing
        $superAdmins     = array();
        $superAdminEmail = $params->get('failure_email_address', '');

        if (!empty($superAdminEmail))
        {
            $superAdmins = $this->getSuperAdministrators($superAdminEmail);
        }

        if (empty($superAdmins))
        {
            $superAdmins = $this->getSuperAdministrators();
        }

        if(empty($superAdmins))
        {
            return array(
                'message' => array("WARNING! Failed backup(s) detected, but there are no configured Super Administrators to receive notifications"),
                'result'  => false
            );
        }

        $failedReport = array();

        foreach($failed as $fail)
        {
            $string  = "Description : ".$fail['description']."\n";
            $string .= "Start time  : ".$fail['backupstart']."\n";
            $string .= "Origin      : ".$fail['origin']."\n";
            $string .= "Type        : ".$fail['type']."\n";
            $string .= "Profile ID  : ".$fail['profile_id'];

            $failedReport[] = $string;
        }

        $failedReport = implode("\n#-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+-+#\n", $failedReport);

        $email_subject = $params->get('failure_email_subject', '');

        if(!$email_subject)
        {
            $email_subject	= <<<ENDSUBJECT
THIS EMAIL IS SENT FROM YOUR SITE "[SITENAME]" - Failed backup(s) detected
ENDSUBJECT;
        }

        $email_body = $params->get('failure_email_body', '');

        if(!$email_body)
        {
            $email_body = <<<ENDBODY
================================================================================
FAILED BACKUP ALERT
================================================================================

Your site has determined that there are failed backups.

The following backups are found to be failing:

[FAILEDLIST]

================================================================================
WHY AM I RECEIVING THIS EMAIL?
================================================================================

This email has been automatically sent by scritp you, or the person who built
or manages your site, has installed and explicitly configured. This script looks
for failed backups and sends an email notification to all Super Users.

If you do not understand what this means, please do not contact the authors of
the software. They are NOT sending you this email and they cannot help you.
Instead, please contact the person who built or manages your site.

================================================================================
WHO SENT ME THIS EMAIL?
================================================================================

This email is sent to you by your own site, [SITENAME]

ENDBODY;
        }

        $jconfig  = JFactory::getConfig();

        $mailfrom = $jconfig->get('mailfrom');
        $fromname = $jconfig->get('fromname');

        $email_subject = Factory::getFilesystemTools()->replace_archive_name_variables($email_subject);
        $email_body    = Factory::getFilesystemTools()->replace_archive_name_variables($email_body);
        $email_body    = str_replace('[FAILEDLIST]', $failedReport, $email_body);

        foreach($superAdmins as $sa)
        {
            $mailer = JFactory::getMailer();

            $mailer->setSender(array( $mailfrom, $fromname ));
            $mailer->addRecipient($sa->email);
            $mailer->setSubject($email_subject);
            $mailer->setBody($email_body);
            $mailer->Send();
        }

        // Let's update the last time we check, so we will avoid to send
        // the same notification several times
        $this->updateLastCheck(intval($last));

        return array(
            'message' => array(
                "WARNING! Found ".count($failed)." failed backup(s)",
                "Sent ".count($superAdmins)." notifications"
            ),
            'result'  => true
        );
    }

	/**
	 * Delete the stats record whose ID is set in the model
	 * @param	int		$id		Backup record whose files we have to delete
	 * @return bool True on success
	 */
	public function delete()
	{
		$db = $this->getDBO();

		$id = $this->getState('id', 0);

		if( (!is_numeric($id)) || ($id <= 0) )
		{
			$this->setError(JText::_('STATS_ERROR_INVALIDID'));
			return false;
		}

		// Try to delete files
		$this->deleteFile($id);
		if(!Platform::getInstance()->delete_statistics($id))
		{
			$this->setError($db->getError());
			return false;
		}

		return true;
	}

	/**
	 * Delete the backup file of the stats record whose ID is set in the model
	 * @return bool True on success
	 */
	public function deleteFile()
	{
		JLoader::import('joomla.filesystem.file');

		$id = $this->getState('id', 0);

		if( (!is_numeric($id)) || ($id <= 0) )
		{
			$this->setError(JText::_('STATS_ERROR_INVALIDID'));
			return false;
		}

		// Get the backup statistics record and the files to delete
		$stat = Platform::getInstance()->get_statistics($id);
		$allFiles = Factory::getStatistics()->get_all_filenames($stat, false);

		// Remove the custom log file if necessary
		$this->_deleteLogs($stat);

		// No files? Nothing to do.
		if (empty($allFiles))
		{
			return true;
		}

		$status = true;

		foreach($allFiles as $filename)
		{
			if (!@file_exists($filename))
			{
				continue;
			}

			$new_status = @unlink($filename);

			if (!$new_status)
			{
				$new_status = JFile::delete($filename);
			}

			$status = $status ? $new_status : false;
		}

		return $status;
	}

	/**
	 * Deletes the backup-specific log files of a stats record
	 *
	 * @param   array   $stat  The array holding the backup stats record
	 *
	 * @return  void
	 */
	protected function _deleteLogs(array $stat)
	{
		// We can't delete logs if there is no backup ID in the record
		if (!isset($stat['backupid']) || empty($stat['backupid']))
		{
			return;
		}

		$logFileName = 'akeeba.' . $stat['tag'] . '.' . $stat['backupid'] . '.log';

		$logPath = dirname($stat['absolute_path']) . '/' . $logFileName;

		if (@file_exists($logPath))
		{
			if (!@unlink($logPath))
			{
				JFile::delete($logPath);
			}
		}
	}

	/**
	 * Get a pagination object
	 *
	 * @access public
	 * @return JPagination
	 *
	 */
	public function &getPagination($filters = null)
	{
		if( empty($this->_pagination) )
		{
			// Import the pagination library
			JLoader::import('joomla.html.pagination');

			// Prepare pagination values
			$total = Platform::getInstance()->get_statistics_count($filters);
			$limitstart = $this->getState('limitstart');
			$limit = $this->getState('limit');

			// Create the pagination object
			$this->_pagination = new JPagination($total, $limitstart, $limit);
		}

		return $this->_pagination;
	}

    private function getSuperAdministrators($email = null)
    {
        $db = F0FPlatform::getInstance()->getDbo();
        $sql = $db->getQuery(true)
            ->select(array(
                $db->qn('u').'.'.$db->qn('id'),
                $db->qn('u').'.'.$db->qn('email')
            ))
            ->from($db->qn('#__user_usergroup_map').' AS '.$db->qn('g'))
            ->join(
                'INNER',
                $db->qn('#__users').' AS '.$db->qn('u').' ON ('.
                $db->qn('g').'.'.$db->qn('user_id').' = '.$db->qn('u').'.'.$db->qn('id').')'
            )->where($db->qn('g').'.'.$db->qn('group_id').' = '.$db->q('8'))
            ->where($db->qn('u').'.'.$db->qn('sendEmail').' = '.$db->q('1'))
        ;
        if (!empty($email))
        {
            $sql->where($db->qn('u').'.'.$db->qn('email').' = '.$db->q($email));
        }
        $db->setQuery($sql);

        return $db->loadObjectList();
    }

    private function updateLastCheck($exists)
    {
        $db = F0FPlatform::getInstance()->getDbo();

        $now = new JDate();

		if (method_exists($now, 'toSql'))
		{
			$nowToSql = $now->toSql();
		}
		else
		{
			$nowToSql = $now->toMySQL();
		}

		if($exists)
        {
            $query = $db->getQuery(true)
                        ->update($db->qn('#__ak_storage'))
                        ->set($db->qn('lastupdate').' = '.$db->q($nowToSql))
                        ->where($db->qn('tag').' = '.$db->q('akeeba_checkfailed'));
        }
        else
        {
            $query = $db->getQuery(true)
                        ->insert($db->qn('#__ak_storage'))
                        ->columns(array($db->qn('tag'), $db->qn('lastupdate')))
                        ->values($db->q('akeeba_checkfailed').', '.$db->q($nowToSql));
        }

        try
        {
            $db->setQuery($query)->execute();
        }
        catch (Exception $exc)
        {

        }
    }

    private function getLastCheck()
    {
        $db = F0FPlatform::getInstance()->getDbo();

        $query = $db->getQuery(true)
                    ->select($db->qn('lastupdate'))
                    ->from($db->qn('#__ak_storage'))
                    ->where($db->qn('tag').' = '.$db->q('akeeba_checkfailed'));

        $datetime = $db->setQuery($query)->loadResult();

        if(!intval($datetime))
        {
            $datetime = $db->getNullDate();
        }

        return $datetime;
    }

	/**
	 * Set the flag to hide the restoration instructions modal from the Manage Backups page
	 *
	 * @return  void
	 */
	public function hideRestorationInstructionsModal()
	{
		$component = JComponentHelper::getComponent('com_akeeba');

		if (is_object($component->params) && ($component->params instanceof JRegistry))
		{
			$params = $component->params;
		}
		else
		{
			$params = new JRegistry($component->params);
		}

		$params->set('show_howtorestoremodal', 0);

		$params->set('jversion', '1.6');
		$db   = F0FPlatform::getInstance()->getDbo();
		$data = $params->toString();
		$sql  = $db->getQuery(true)
				   ->update($db->qn('#__extensions'))
				   ->set($db->qn('params') . ' = ' . $db->q($data))
				   ->where($db->qn('element') . ' = ' . $db->q('com_akeeba'))
				   ->where($db->qn('type') . ' = ' . $db->q('component'));
		$db->setQuery($sql);
		$db->execute();
	}
}