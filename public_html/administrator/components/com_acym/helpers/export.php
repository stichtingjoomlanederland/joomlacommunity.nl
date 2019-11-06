<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php

class acymexportHelper
{
    public function setDownloadHeaders($filename = 'export', $extension = 'csv')
    {
        header('Pragma: public');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        header('Content-Type: application/force-download');
        header('Content-Type: application/octet-stream');
        header('Content-Type: application/download');

        header('Content-Disposition: attachment; filename='.$filename.'.'.$extension);
        header('Content-Transfer-Encoding: binary');
    }

    public function exportCSV($query, $fieldsToExport, $customFieldsToExport, $separator = ',', $charset = 'UTF-8', $exportFile = null)
    {
        $nbExport = $this->getExportLimit();
        acym_displayErrors();
        $encodingClass = acym_get('helper.encoding');
        $config = acym_config();
        $excelSecure = $config->get('export_excelsecurity', 0);

        $eol = "\r\n";
        $before = '"';
        if (!in_array($separator, [',', ';'])) $separator = ',';
        $separator = '"'.$separator.'"';
        $after = '"';

        $firstLine = $before.implode($separator, array_merge($fieldsToExport, $customFieldsToExport)).$after.$eol;

        if (empty($exportFile)) {
            @ob_clean();
            $filename = 'export_'.date('Y-m-d');
            $this->setDownloadHeaders($filename);
            echo $firstLine;
        } else {
            preg_match('#^(.+/)[^/]+$#', $exportFile, $folder);
            if (!empty($folder[1]) && !file_exists($folder[1])) acym_createDir($folder[1]);

            $fp = fopen($exportFile, 'w');
            if (false === $fp) return acym_translation_sprintf('ACYM_FAIL_SAVE_FILE', $exportFile);

            $error = fwrite($fp, $firstLine);
            if (false === $error) return acym_translation_sprintf('ACYM_UNWRITABLE_FILE', $exportFile);
        }

        $start = 0;
        do {
            $users = acym_loadObjectList($query.' LIMIT '.intval($start).', '.intval($nbExport), 'id');
            $start += $nbExport;

            if ($users === false) {
                $errorLine = $eol.$eol.'Error: '.acym_getDBError();

                if (empty($exportFile)) {
                    echo $errorLine;
                } else {
                    $error = fwrite($fp, $errorLine);
                    if (false === $error) return acym_translation_sprintf('ACYM_UNWRITABLE_FILE', $exportFile);
                }
            }

            if (empty($users)) break;

            foreach ($users as $userID => $oneUser) {
                unset($oneUser->id);

                $data = get_object_vars($oneUser);

                if (!empty($customFieldsToExport)) {
                    $fieldIDs = array_keys($customFieldsToExport);
                    acym_arrayToInteger($fieldIDs);

                    $userCustomFields = acym_loadObjectList(
                        'SELECT `field_id`, `value` 
                        FROM #__acym_user_has_field 
                        WHERE user_id = '.intval($userID).' AND field_id IN ('.implode(',', $fieldIDs).')',
                        'field_id'
                    );

                    foreach ($customFieldsToExport as $fieldID => $fieldName) {
                        $data[] = empty($userCustomFields[$fieldID]) ? '' : $userCustomFields[$fieldID]->value;
                    }
                    unset($userCustomFields);
                }

                foreach ($data as &$oneData) {
                    if ($excelSecure == 1) {
                        $firstcharacter = substr($oneData, 0, 1);
                        if (in_array($firstcharacter, ['=', '+', '-', '@'])) {
                            $oneData = '	'.$oneData;
                        }
                    }

                    $oneData = acym_escape($oneData);
                }

                $dataexport = implode($separator, $data);
                unset($data);

                $oneLine = $before.$encodingClass->change($dataexport, 'UTF-8', $charset).$after.$eol;
                if (empty($exportFile)) {
                    echo $oneLine;
                } else {
                    $error = fwrite($fp, $oneLine);
                    if (false === $error) return acym_translation_sprintf('ACYM_UNWRITABLE_FILE', $exportFile);
                }
            }

            unset($users);
        } while (true);

        if (!empty($exportFile)) fclose($fp);

        return '';
    }

    private function getExportLimit()
    {
        $serverLimit = acym_bytes(ini_get('memory_limit'));
        if ($serverLimit > 150000000) {
            return 50000;
        } elseif ($serverLimit > 80000000) {
            return 15000;
        } else {
            return 5000;
        }
    }
}

