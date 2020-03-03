<?php
defined('_JEXEC') or die('Restricted access');
?><?php

class acymexportHelper extends acymObject
{
    var $eol = "\r\n";
    var $before = '"';
    var $after = '"';

    public function setDownloadHeaders($filename = 'export', $extension = 'csv')
    {
        acym_header('Pragma: public');
        acym_header('Expires: 0');
        acym_header('Cache-Control: must-revalidate, post-check=0, pre-check=0');

        acym_header('Content-Type: application/force-download');
        acym_header('Content-Type: application/octet-stream');
        acym_header('Content-Type: application/download');

        acym_header('Content-Disposition: attachment; filename='.$filename.'.'.$extension);
        acym_header('Content-Transfer-Encoding: binary');
    }

    public function exportStatsFormattedCSV($mailName, $globalDonutsData, $globaline, $timeLinechart)
    {
        $nbExport = $this->getExportLimit();
        acym_displayErrors();

        if ($timeLinechart == 'month') {
            $timeLinechart = acym_translation('ACYM_MONTHLY_STATS');
        } elseif ($timeLinechart == 'week') {
            $timeLinechart = acym_translation('ACYM_WEEKLY_STATS');
        } else {
            $timeLinechart = acym_translation('ACYM_DAILY_STATS');
        }

        $separator = '","';

        $csvLines = [];
        $csvLines[] = $this->before.$mailName.$this->after;

        $csvLines[] = $this->eol;

        $globalDonutsTitle = [
            acym_translation('ACYM_SUCCESSFULLY_SENT'),
            acym_translation('ACYM_OPEN_RATE'),
            acym_translation('ACYM_CLICK_RATE'),
            acym_translation('ACYM_BOUNCE_RATE'),
        ];

        $csvLines[] = $this->before.implode($separator, $globalDonutsTitle).$this->after;
        $csvLines[] = $this->before.implode($separator, $globalDonutsData).$this->after;

        $csvLines[] = $this->eol;

        $csvLines[] = $this->before.$timeLinechart.$separator.acym_translation('ACYM_OPEN').$separator.acym_translation('ACYM_CLICK').$this->after;

        $i = 0;
        foreach ($globaline as $date => $value) {
            if ($i > $nbExport) break;
            $csvLines[] = $this->before.$date.$separator.$value['open'].$separator.$value['click'].$this->after;
            $i++;
        }

        $this->finishStatsExport($csvLines);
    }

    public function exportStatsFullCSV($query, $columns, $type = 'global')
    {
        $mailsStats = acym_loadObjectList($query);
        $nbExport = $this->getExportLimit();
        acym_displayErrors();

        $separator = '","';
        $csvLines = [];

        $csvLines[] = $this->before.implode($separator, $columns).$this->after;

        $i = 0;
        foreach ($mailsStats as $mailStat) {
            if ($i > $nbExport) break;
            $oneLine = [];
            foreach ($columns as $key => $trad) {
                $key = explode('.', $key);
                $oneLine[] = $mailStat->{$key[1]};
            }
            $csvLines[] = $this->before.implode($separator, $oneLine).$this->after;
            $i++;
        }

        $this->finishStatsExport($csvLines, $type);
    }

    private function finishStatsExport($csvLines, $type = 'global')
    {
        $final = implode($this->eol, $csvLines);

        @ob_clean();
        $filename = 'export_stats_'.$type.'_'.date('Y-m-d');
        $this->setDownloadHeaders($filename);
        echo $final;

        return '';
    }

    public function exportCSV($query, $fieldsToExport, $customFieldsToExport, $separator = ',', $charset = 'UTF-8', $exportFile = null)
    {
        $nbExport = $this->getExportLimit();
        acym_displayErrors();
        $encodingClass = acym_get('helper.encoding');
        $excelSecure = $this->config->get('export_excelsecurity', 0);

        if (!in_array($separator, [',', ';'])) $separator = ',';
        $separator = '"'.$separator.'"';

        $firstLine = $this->before.implode($separator, array_merge($fieldsToExport, $customFieldsToExport)).$this->after.$this->eol;

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
                $errorLine = $this->eol.$this->eol.'Error: '.acym_getDBError();

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

                $oneLine = $this->before.$encodingClass->change($dataexport, 'UTF-8', $charset).$this->after.$this->eol;
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

