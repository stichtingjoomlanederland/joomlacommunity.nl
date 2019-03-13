<?php
/**
 * @package RSForm!Pro
 * @copyright (C) 2007-2018 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.plugin.plugin' );

/**
 * RSForm! Pro Delete Submissions System Plugin
 */
class plgSystemRsformdeletesubmissions extends JPlugin
{
    public function onAfterInitialise()
    {
        if (!file_exists(JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsform.php'))
        {
            return false;
        }

        require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsform.php';

        $now        = JFactory::getDate()->toUnix();
        $config     = RSFormProConfig::getInstance();
        $last_run   = $config->get('deleteafter.last_run', 0);
        $interval   = $config->get('deleteafter.interval', 10);
        
        if ($last_run + ($interval * 60) > $now)
        {
            return false;
        }

        $config->set('deleteafter.last_run', $now);

		$db = JFactory::getDbo();
		
		$query = $db->getQuery(true)
			->select($db->qn('FormId'))
			->select($db->qn('DeleteSubmissionsAfter'))
			->from($db->qn('#__rsform_forms'))
			->where($db->qn('DeleteSubmissionsAfter') . ' > ' . $db->q(0));
		
		if ($forms = $db->setQuery($query)->loadObjectList())
		{
			foreach ($forms as $form)
			{
				$date = JFactory::getDate()->modify("-{$form->DeleteSubmissionsAfter} days")->toSql();
				// Find all Submission IDs that need to get removed
				$query->clear()
					->select($db->qn('SubmissionId'))
					->from($db->qn('#__rsform_submissions'))
					->where($db->qn('FormId') . ' = ' . $db->q($form->FormId))
					->where($db->qn('DateSubmitted') . ' < ' . $db->q($date));
				
				if ($submissions = $db->setQuery($query)->loadColumn())
				{
                    require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/submissions.php';

                    RSFormProSubmissionsHelper::deleteSubmissions($submissions);
				}
			}
		}
    }

    public function onPreprocessMenuItems($context, &$items, $params, $enabled)
    {
        $user = JFactory::getUser();

        foreach ($items as $i => $item)
        {
            if ($item->element == 'com_rsform')
            {
                if (
                    ($item->title == 'COM_RSFORM_MANAGE_FORMS' && !$user->authorise('forms.manage', 'com_rsform')) ||
                    ($item->title == 'COM_RSFORM_MANAGE_SUBMISSIONS' && !$user->authorise('submissions.manage', 'com_rsform')) ||
                    ($item->title == 'COM_RSFORM_MANAGE_DIRECTORY_SUBMISSIONS' && !$user->authorise('directory.manage', 'com_rsform')) ||
                    ($item->title == 'COM_RSFORM_CONFIGURATION' && !$user->authorise('core.admin', 'com_rsform')) ||
                    ($item->title == 'COM_RSFORM_BACKUP_RESTORE' && !$user->authorise('backuprestore.manage', 'com_rsform'))
                )
                {
                    unset($items[$i]);
                }
            }
        }
    }
}