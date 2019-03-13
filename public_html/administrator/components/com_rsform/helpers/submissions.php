<?php
/**
 * @package RSForm! Pro
 * @copyright (C) 2007-2014 www.rsjoomla.com
 * @license GPL, http://www.gnu.org/copyleft/gpl.html
 */

defined('_JEXEC') or die('Restricted access');

require_once JPATH_ADMINISTRATOR . '/components/com_rsform/helpers/rsform.php';

abstract class RSFormProSubmissionsHelper
{
    public static function getSubmission($cid, $withValues = true)
    {
        $db = JFactory::getDbo();

        // Load submission
        $query = $db->getQuery(true);
        $query->select('*')
            ->from($db->qn('#__rsform_submissions'))
            ->where($db->qn('SubmissionId').'='.$db->q($cid));
        $submission = $db->setQuery($query)->loadObject();
        if (empty($submission)) {
            return false;
        }

        // Get submission values
        if ($withValues)
        {
            $submission->values = array();
            $query->clear()
                ->select($db->qn('FieldName'))
                ->select($db->qn('FieldValue'))
                ->from($db->qn('#__rsform_submission_values'))
                ->where($db->qn('SubmissionId').'='.$db->q($cid));
            if ($submissionValues = $db->setQuery($query)->loadObjectList()) {
                foreach ($submissionValues as $value) {
                    $submission->values[$value->FieldName] = $value->FieldValue;
                }
            }
        }

        return $submission;
    }

    public static function deleteSubmissions($cid, $sendDeletionEmail = false)
    {
        $db     = JFactory::getDbo();
        $query  = $db->getQuery(true);
        $cid    = array_map('intval', (array) $cid);

        if ($sendDeletionEmail && count($cid) == 1)
        {
            $SubmissionId = $cid[0];
            // Get replacements
            list($placeholders, $values) = RSFormProHelper::getReplacements($SubmissionId);

            // Get form ID from placeholders
            $formId = $values[array_search('{global:formid}', $placeholders)];

            // Get language
            $lang = $values[array_search('{global:language}', $placeholders)];

            // Load form
            $query
                ->select(array('DeletionEmailTo', 'DeletionEmailCC', 'DeletionEmailBCC', 'DeletionEmailFrom', 'DeletionEmailReplyTo', 'DeletionEmailFromName', 'DeletionEmailText', 'DeletionEmailSubject', 'DeletionEmailMode'))
                ->from($db->qn('#__rsform_forms'))
                ->where($db->qn('FormId') . ' = ' . $db->q($formId));
            $form = $db->setQuery($query)->loadObject();

            // Get translation
            if (empty($lang))
            {
                $lang = RSFormProHelper::getCurrentLanguage($formId);
            }

            if ($translations = RSFormProHelper::getTranslations('forms', $formId, $lang)) {
                foreach ($translations as $field => $value) {
                    if (isset($form->{$field})) {
                        $form->{$field} = $value;
                    }
                }
            }

            // RSForm! Pro Scripting - Deletion Email Text
            if (strpos($form->DeletionEmailText, '{/if}') !== false)
            {
                require_once dirname(__FILE__).'/scripting.php';
                RSFormProScripting::compile($form->DeletionEmailText, $placeholders, $values);
            }

            // Create email
            $deletionEmail = array(
                'to' => str_replace($placeholders, $values, $form->DeletionEmailTo),
                'cc' => str_replace($placeholders, $values, $form->DeletionEmailCC),
                'bcc' => str_replace($placeholders, $values, $form->DeletionEmailBCC),
                'from' => str_replace($placeholders, $values, $form->DeletionEmailFrom),
                'replyto' => str_replace($placeholders, $values, $form->DeletionEmailReplyTo),
                'fromName' => str_replace($placeholders, $values, $form->DeletionEmailFromName),
                'text' => str_replace($placeholders, $values, $form->DeletionEmailText),
                'subject' => str_replace($placeholders, $values, $form->DeletionEmailSubject),
                'mode' => $form->DeletionEmailMode
            );

            if (strpos($deletionEmail['cc'], ',') !== false)
            {
                $deletionEmail['cc'] = explode(',', $deletionEmail['cc']);
            }
            if (strpos($deletionEmail['bcc'], ',') !== false)
            {
                $deletionEmail['bcc'] = explode(',', $deletionEmail['bcc']);
            }

            JFactory::getApplication()->triggerEvent('rsfp_beforeDeletionEmail', array(array('form' => &$form, 'placeholders' => &$placeholders, 'values' => &$values, 'submissionId' => $SubmissionId, 'userEmail'=> &$deletionEmail)));

            if ($deletionEmail['to'])
            {
                $recipients = explode(',', $deletionEmail['to']);

                RSFormProHelper::sendMail($deletionEmail['from'], $deletionEmail['fromName'], $recipients, $deletionEmail['subject'], $deletionEmail['text'], $deletionEmail['mode'], !empty($deletionEmail['cc']) ? $deletionEmail['cc'] : null, !empty($deletionEmail['bcc']) ? $deletionEmail['bcc'] : null, null, !empty($deletionEmail['replyto']) ? $deletionEmail['replyto'] : '');
            }
        }

        // Delete files
        static::deleteSubmissionFiles($cid);

        // Delete submissions
        $query->clear()
            ->delete($db->qn('#__rsform_submissions'))
            ->where($db->qn('SubmissionId') . ' IN (' . implode(',', $cid) . ')');
        $db->setQuery($query)
            ->execute();

        $total = $db->getAffectedRows();

        // Delete values
        $query->clear()
            ->delete($db->qn('#__rsform_submission_values'))
            ->where($db->qn('SubmissionId') . ' IN (' . implode(',', $cid) . ')');
        $db->setQuery($query)
            ->execute();

        return $total;
    }

    protected static function deleteSubmissionFiles($cid)
    {
        $db     = JFactory::getDbo();
        $cid    = array_map('intval', (array) $cid);

        $query = $db->getQuery(true)
            ->select($db->qn('FormId'))
            ->from($db->qn('#__rsform_submissions'))
            ->where($db->qn('SubmissionId') . ' IN (' . implode(',', $cid) . ')');
        if ($formIds = $db->setQuery($query)->loadColumn())
        {
            $formIds = array_unique($formIds);

            foreach ($formIds as $formId)
            {
                if ($fields = RSFormProHelper::componentExists($formId, RSFORM_FIELD_FILEUPLOAD, false))
                {
                    $allData = RSFormProHelper::getComponentProperties($fields);
                    foreach ($fields as $field)
                    {
                        if (!isset($allData[$field]))
                        {
                            continue;
                        }

                        $data = $allData[$field];

                        $query->clear()
                            ->select($db->qn('FieldValue'))
                            ->from($db->qn('#__rsform_submission_values'))
                            ->where($db->qn('SubmissionId') . ' IN (' . implode(',', $cid) . ')')
                            ->where($db->qn('FieldName') . ' = ' . $db->q($data['NAME']))
                            ->where($db->qn('FieldValue') . ' != ' . $db->q(''));
                        if ($files = $db->setQuery($query)->loadColumn())
                        {
                            jimport('joomla.filesystem.file');

                            foreach ($files as $file)
                            {
                                if (file_exists($file) && is_file($file))
                                {
                                    JFile::delete($file);
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    public static function deleteAllSubmissions($formId)
    {
        $db = JFactory::getDbo();

        // Delete files
        static::deleteAllSubmissionFiles($formId);

        // Delete submissions
        $query = $db->getQuery(true);
        $query->delete('#__rsform_submissions')
            ->where($db->qn('FormId').' = '.$db->q($formId));
        $db->setQuery($query)->execute();

        // Remember how many submissions we've removed.
        $total = $db->getAffectedRows();

        // Delete submission values
        $query = $db->getQuery(true);
        $query->delete('#__rsform_submission_values')
            ->where($db->qn('FormId').' = '.$db->q($formId));
        $db->setQuery($query)->execute();

        // Delete submission columns
        $query = $db->getQuery(true);
        $query->delete('#__rsform_submission_columns')
            ->where($db->qn('FormId').' = '.$db->q($formId));
        $db->setQuery($query)->execute();

        return $total;
    }

    protected static function deleteAllSubmissionFiles($formId)
    {
        $db     = JFactory::getDbo();
        $query = $db->getQuery(true);

        if ($fields = RSFormProHelper::componentExists($formId, RSFORM_FIELD_FILEUPLOAD, false))
        {
            $allData = RSFormProHelper::getComponentProperties($fields);
            foreach ($fields as $field)
            {
                if (!isset($allData[$field]))
                {
                    continue;
                }

                $data = $allData[$field];

                $query->clear()
                    ->select($db->qn('FieldValue'))
                    ->from($db->qn('#__rsform_submission_values'))
                    ->where($db->qn('FormId') . ' = ' . $db->q($formId))
                    ->where($db->qn('FieldName') . ' = ' . $db->q($data['NAME']))
                    ->where($db->qn('FieldValue') . ' != ' . $db->q(''));
                if ($files = $db->setQuery($query)->loadColumn())
                {
                    jimport('joomla.filesystem.file');

                    foreach ($files as $file)
                    {
                        if (file_exists($file) && is_file($file))
                        {
                            JFile::delete($file);
                        }
                    }
                }
            }
        }
    }
}