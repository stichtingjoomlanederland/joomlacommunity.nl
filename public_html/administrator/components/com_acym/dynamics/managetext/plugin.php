<?php

use AcyMailing\Libraries\acymPlugin;

class plgAcymManagetext extends acymPlugin
{
    public function replaceContent(&$email, $send = true)
    {
        $this->_replaceRandom($email);
        $this->_handleAnchors($email);
        $this->fixPicturesOutlook($email);
    }

    public function replaceUserInformation(&$email, &$user, $send = true)
    {
        $this->pluginHelper->replaceVideos($email->body);
        $this->pluginHelper->cleanHtml($email->body);

        $this->_removetext($email);
        $this->_ifstatement($email, $user);
        $this->_replaceConstant($email, $user);
    }

    private function _replaceConstant(&$email, $user)
    {
        $tags = $this->pluginHelper->extractTags($email, '(?:const|trans|config)');
        if (empty($tags)) {
            return;
        }

        $tagsReplaced = [];
        foreach ($tags as $i => $oneTag) {
            $val = '';
            $arrayVal = [];
            foreach ($oneTag as $valname => $oneValue) {
                if ($valname == 'id') {
                    $val = trim(strip_tags($oneValue));
                } elseif ($valname != 'default') {
                    $arrayVal[] = '{'.$valname.'}';
                }
            }

            if (empty($val)) {
                continue;
            }
            $tagValues = explode(':', $i);
            $type = ltrim($tagValues[0], '{');
            if ($type == 'const') {
                $tagsReplaced[$i] = defined($val) ? constant($val) : 'Constant not defined : '.$val;
            } elseif ($type == 'config') {
                if ($val == 'sitename') {
                    $tagsReplaced[$i] = acym_getCMSConfig($val);
                }
            } else {
                if (!empty($user->language)) {
                    $language = $user->language;
                } elseif (!empty($email->language)) {
                    $language = $email->language;
                } else {
                    $language = acym_getLanguageTag();
                }

                $previousLanguage = acym_setLanguage($language);
                acym_loadLanguage($language);

                static $done = false;
                if (!$done && strpos($val, 'COM_USERS') !== false) {
                    $done = true;

                    acym_loadLanguageFile('com_users');
                }
                if (!empty($arrayVal)) {
                    $translation = acym_translation($val);
                    $paramsIncluded = vsprintf($translation, $arrayVal);
                    if ($translation === $paramsIncluded) {
                        $translation = preg_replace(
                            '/\{[A-Z_]+\}/',
                            '%s',
                            $translation
                        );
                        $paramsIncluded = vsprintf($translation, $arrayVal);
                    }
                    $tagsReplaced[$i] = nl2br($paramsIncluded);
                } else {
                    $tagsReplaced[$i] = acym_translation($val);
                }

                if (empty($previousLanguage)) $previousLanguage = acym_getLanguageTag();
                acym_setLanguage($previousLanguage);
                acym_loadLanguage($previousLanguage);
            }
        }

        $this->pluginHelper->replaceTags($email, $tagsReplaced, true);
    }

    private function _replaceRandom(&$email)
    {
        $randTag = $this->pluginHelper->extractTags($email, "rand");
        if (empty($randTag)) {
            return;
        }
        foreach ($randTag as $oneRandTag) {
            $results[$oneRandTag->id] = explode(';', $oneRandTag->id);
            $randNumber = rand(0, count($results[$oneRandTag->id]) - 1);
            $results[$oneRandTag->id][count($results[$oneRandTag->id])] = $results[$oneRandTag->id][$randNumber];
        }

        $tags = [];
        foreach (array_keys($results) as $oneResult) {
            $tags['{rand:'.$oneResult.'}'] = end($results[$oneResult]);
        }

        if (empty($tags)) {
            return;
        }
        $this->pluginHelper->replaceTags($email, $tags, true);
    }


    private function _ifstatement(&$email, $user, $loop = 1)
    {
        if (isset($this->noIfStatementTags[$email->id])) {
            return;
        }

        $isAdmin = acym_isAdmin();

        if ($loop > 3) {
            if ($isAdmin) {
                acym_display('You cannot have more than 3 nested {if} tags.', 'warning');
            }

            return;
        }

        $match = '#{if:(((?!{if).)*)}(((?!{if).)*){/if}#Uis';
        $variables = ['subject', 'body', 'altbody', 'From', 'FromName', 'ReplyTo'];
        $found = false;
        foreach ($variables as $var) {
            if (empty($email->$var)) continue;

            if (is_array($email->$var)) {
                foreach ($email->$var as $i => &$arrayField) {
                    if (empty($arrayField) || !is_array($arrayField)) continue;

                    foreach ($arrayField as $key => &$oneval) {
                        $found = preg_match_all($match, $oneval, $results[$var.$i.'-'.$key]) || $found;
                        if (empty($results[$var.$i.'-'.$key][0])) unset($results[$var.$i.'-'.$key]);
                    }
                }
            } else {
                $found = preg_match_all($match, $email->$var, $results[$var]) || $found;
                if (empty($results[$var][0])) unset($results[$var]);
            }
        }

        if (!$found) {
            if ($loop == 1) {
                $this->noIfStatementTags[$email->id] = true;
            }

            return;
        }

        static $a = false;

        $tags = [];
        foreach ($results as $var => $allresults) {
            foreach ($allresults[0] as $i => $oneTag) {
                if (isset($tags[$oneTag])) {
                    continue;
                }
                $allresults[1][$i] = html_entity_decode($allresults[1][$i]);
                if (!preg_match('#^(.+)(!=|<|>|&gt;|&lt;|!~)([^=!<>~]+)$#is', $allresults[1][$i], $operators) && !preg_match(
                        '#^(.+)(=|~)([^=!<>~]+)$#is',
                        $allresults[1][$i],
                        $operators
                    )) {
                    if ($isAdmin) {
                        acym_enqueueMessage(acym_translationSprintf('ACYM_OPERATION_NOT_FOUND', $allresults[1][$i]), 'error');
                    }
                    $tags[$oneTag] = $allresults[3][$i];
                    continue;
                };
                $field = trim($operators[1]);
                $prop = '';

                $operatorsParts = explode('.', $operators[1]);
                $operatorComp = 'acym';
                if (count($operatorsParts) > 1 && in_array($operatorsParts[0], ['acym', 'joomla', 'var'])) {
                    $operatorComp = $operatorsParts[0];
                    unset($operatorsParts[0]);
                    $field = implode('.', $operatorsParts);
                }

                if ($operatorComp == 'joomla') {
                    if (!empty($user->userid)) {
                        if ($field == 'gid') {
                            $prop = implode(';', acym_loadResultArray('SELECT group_id FROM #__user_usergroup_map WHERE user_id = '.intval($user->userid)));
                        } else {
                            $juser = acym_loadObject('SELECT * FROM #__users WHERE id = '.intval($user->userid));
                            if (isset($juser->{$field})) {
                                $prop = strtolower($juser->{$field});
                            } else {
                                if ($isAdmin && !$a) {
                                    acym_display('User variable not set : '.$field.' in '.$allresults[1][$i], 'error');
                                }
                                $a = true;
                            }
                        }
                    }
                } elseif ($operatorComp == 'var') {
                    $prop = strtolower($field);
                } else {
                    if (!isset($user->{$field})) {
                        if ($isAdmin && !$a) {
                            acym_display('User variable not set : '.$field.' in '.$allresults[1][$i], 'error');
                        }
                        $a = true;
                    } else {
                        $prop = strtolower($user->{$field});
                    }
                }

                $tags[$oneTag] = '';
                $val = trim(strtolower($operators[3]));
                if ($operators[2] == '=' && ($prop == $val || in_array($prop, explode(';', $val)) || in_array($val, explode(';', $prop)))) {
                    $tags[$oneTag] = $allresults[3][$i];
                } elseif ($operators[2] == '!=' && $prop != $val) {
                    $tags[$oneTag] = $allresults[3][$i];
                } elseif (($operators[2] == '>' || $operators[2] == '&gt;') && $prop > $val) {
                    $tags[$oneTag] = $allresults[3][$i];
                } elseif (($operators[2] == '<' || $operators[2] == '&lt;') && $prop < $val) {
                    $tags[$oneTag] = $allresults[3][$i];
                } elseif ($operators[2] == '~' && strpos($prop, $val) !== false) {
                    $tags[$oneTag] = $allresults[3][$i];
                } elseif ($operators[2] == '!~' && strpos($prop, $val) === false) {
                    $tags[$oneTag] = $allresults[3][$i];
                }
            }
        }

        $this->pluginHelper->replaceTags($email, $tags, true);

        $this->_ifstatement($email, $user, $loop + 1);
    }

    private function _removetext(&$email)
    {
        $removetext = '{reg},{/reg},{pub},{/pub}';
        if (!empty($removetext)) {
            $removeArray = explode(',', trim($removetext, ' ,'));
            if (!empty($email->body)) {
                $email->body = str_replace($removeArray, '', $email->body);
            }
        }


        $removetags = 'youtube';
        if (!empty($removetags)) {
            $regex = [];
            $removeArray = explode(',', trim($removetags, ' ,'));
            foreach ($removeArray as $oneTag) {
                if (empty($oneTag)) {
                    continue;
                }
                $regex[] = '#(?:{|%7B)'.preg_quote($oneTag, '#').'(?:}|%7D).*(?:{|%7B)/'.preg_quote($oneTag, '#').'(?:}|%7D)#Uis';
                $regex[] = '#(?:{|%7B)'.preg_quote($oneTag, '#').'[^}]*(?:}|%7D)#Uis';
            }

            if (!empty($email->body)) {
                $email->body = preg_replace($regex, '', $email->body);
            }
        }
    }

    private function _handleAnchors(&$email)
    {
        if (empty($email->body)) return;

        $newBody = preg_replace('/(<a +href="#[^"]*"[^>]*) target="_blank"([^>]*>)/Uis', '$1 $2', $email->body);

        if (!empty($newBody)) $email->body = $newBody;
    }

    public function fixPicturesOutlook(&$email)
    {
        $this->addPictureDimensions($email->body);
        $this->addPictureAlign($email->body);
    }

    public function addPictureDimensions(&$html)
    {
        if (!preg_match_all('#(<img)([^>]*>)#i', $html, $results)) {
            return;
        }

        $replace = [];
        $widthheight = ['width', 'height'];
        foreach ($results[0] as $num => $oneResult) {
            $add = [];
            foreach ($widthheight as $whword) {
                if (preg_match('#'.$whword.' *=#i', $oneResult) || !preg_match('#[^a-z_\-]'.$whword.' *:([0-9 ]{1,8})px#i', $oneResult, $resultWH)) continue;

                if (empty($resultWH[1])) continue;
                $add[] = $whword.'="'.trim($resultWH[1]).'" ';
            }

            if (!empty($add)) {
                $replace[$oneResult] = '<img '.implode(' ', $add).$results[2][$num];
            }
        }

        if (!empty($replace)) {
            $html = str_replace(array_keys($replace), $replace, $html);
            preg_match_all('#(<img)([^>]*>)#i', $html, $results);
        }

        static $replace = [];
        foreach ($results[0] as $num => $oneResult) {
            if (isset($replace[$oneResult])) continue;
            if (strpos($oneResult, 'width=') || strpos($oneResult, 'height=')) continue;
            if (preg_match('#[^a-z_\-]width *:([0-9 ]{1,8})#i', $oneResult, $res)) continue;
            if (preg_match('#[^a-z_\-]height *:([0-9 ]{1,8})#i', $oneResult, $res)) continue;
            if (!preg_match('#src="([^"]*)"#i', $oneResult, $url)) continue;

            $imageUrl = $url[1];

            $replace[$oneResult] = $oneResult;

            $base = str_replace(['http://www.', 'https://www.', 'http://', 'https://'], '', ACYM_LIVE);
            $replacements = ['https://www.'.$base, 'http://www.'.$base, 'https://'.$base, 'http://'.$base];
            $localpict = false;
            foreach ($replacements as $oneReplacement) {
                if (strpos($imageUrl, $oneReplacement) === false) continue;

                $imageUrl = str_replace(
                    [$oneReplacement, '/'],
                    [ACYM_ROOT, DS],
                    urldecode($imageUrl)
                );
                $localpict = true;
                break;
            }

            if (!$localpict) continue;

            $dim = @getimagesize($imageUrl);
            if (!$dim) continue;
            if (empty($dim[0]) || empty($dim[1])) continue;

            $replace[$oneResult] = str_replace('<img', '<img width="'.$dim[0].'" height="'.$dim[1].'"', $oneResult);
        }

        if (!empty($replace)) {
            $html = str_replace(array_keys($replace), $replace, $html);
        }
    }

    public function addPictureAlign(&$html)
    {
        if (preg_match_all('#< *img([^>]*)>#Ui', $html, $allPictures)) ;

        foreach ($allPictures[0] as $i => $onePict) {
            if (strpos($onePict, 'align=') !== false) continue;
            if (!preg_match('#(style="[^"]*)(float *: *)(right|left|top|bottom|middle)#Ui', $onePict, $pictParams)) continue;

            $newPict = str_replace('<img', '<img align="'.$pictParams[3].'" ', $onePict);
            $html = str_replace($onePict, $newPict, $html);


            if (strpos($onePict, 'hspace=') !== false) continue;

            $hspace = 5;
            if (preg_match('#margin(-right|-left)? *:([^";]*)#i', $onePict, $margins)) {
                $currentMargins = explode(' ', trim($margins[2]));
                $myMargin = (count($currentMargins) > 1) ? $currentMargins[1] : $currentMargins[0];
                if (strpos($myMargin, 'px') !== false) $hspace = preg_replace('#[^0-9]#i', '', $myMargin);
            }

            $lastPict = str_replace('<img', '<img hspace="'.$hspace.'" ', $newPict);

            $html = str_replace($newPict, $lastPict, $html);
        }
    }
}
