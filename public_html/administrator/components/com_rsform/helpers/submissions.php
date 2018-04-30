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
    public static function deleteSubmissions($cid)
    {
        $db     = JFactory::getDbo();
        $cid    = array_map('intval', (array) $cid);

        // Delete files
        static::deleteSubmissionFiles($cid);

        // Delete submissions
        $query = $db->getQuery(true)
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