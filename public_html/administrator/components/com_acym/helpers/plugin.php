<?php

namespace AcyMailing\Helpers;

use AcyMailing\Libraries\acymObject;

class PluginHelper extends acymObject
{
    public $wraped = false;
    public $name = 'content';
    public $mailerHelper;
    public $wrappedText = '';

    public function getFormattedResult($elements, $parameter)
    {
        if (count($elements) < 2) {
            return implode('', $elements);
        }

        $beforeAll = [];
        $beforeAll['table'] = '<table cellspacing="0" cellpadding="0" border="0" width="100%" class="elementstable">'."\n";
        $beforeAll['ul'] = '<ul class="elementsul">'."\n";
        $beforeAll['br'] = '';

        $beforeBlock = [];
        $beforeBlock['table'] = '<tr class="elementstable_tr numrow{rownum}">'."\n";
        $beforeBlock['ul'] = '';
        $beforeBlock['br'] = '';

        $beforeOne = [];
        $beforeOne['table'] = '<td valign="top" width="{equalwidth}" style="{padding}" class="elementstable_td numcol{numcol}" >'."\n";
        $beforeOne['ul'] = '<li class="elementsul_li numrow{rownum}">'."\n";
        $beforeOne['br'] = '';

        $afterOne = [];
        $afterOne['table'] = '</td>'."\n";
        $afterOne['ul'] = '</li>'."\n";
        $afterOne['br'] = '<br />'."\n";

        $afterBlock = [];
        $afterBlock['table'] = '</tr>'."\n";
        $afterBlock['ul'] = '';
        $afterBlock['br'] = '';

        $afterAll = [];
        $afterAll['table'] = '</table>'."\n";
        $afterAll['ul'] = '</ul>'."\n";
        $afterAll['br'] = '';


        $type = 'table';
        $cols = 1;
        if (!empty($parameter->displaytype)) {
            $type = $parameter->displaytype;
        }
        if ($type == 'none') {
            return implode('', $elements);
        }
        if (!empty($parameter->cols)) {
            $cols = $parameter->cols;
        }

        $parameter->hpadding = is_null($parameter->hpadding) ? 10 : $parameter->hpadding;
        $parameter->vpadding = is_null($parameter->vpadding) ? 10 : $parameter->vpadding;

        $horizontalPadding = round($parameter->hpadding / 2);
        $verticalPadding = round($parameter->vpadding / 2);

        $string = $beforeAll[$type];
        $a = 0;
        $numrow = 1;
        foreach ($elements as $key => $oneElement) {
            $topPadding = $verticalPadding.'px';
            $rightPadding = $horizontalPadding.'px';
            $bottomPadding = $verticalPadding.'px';
            $leftPadding = $horizontalPadding.'px';

            if ($a == $cols) {
                $string .= $afterBlock[$type];
                $a = 0;
                $numrow++;
            }

            if ($a == 0) {
                $string .= str_replace('{rownum}', $numrow, $beforeBlock[$type]);
                $leftPadding = '0px';
            }

            if ($a + 1 == $cols) $rightPadding = '0px';
            if ($numrow == 1) $topPadding = '0px';
            if (round(count($elements) / $cols) == $numrow) $bottomPadding = '0px';

            $padding = 'padding: '.$topPadding.' '.$rightPadding.' '.$bottomPadding.' '.$leftPadding.' !important;';

            $string .= str_replace('{numcol}', $a + 1, $beforeOne[$type]).$oneElement.$afterOne[$type];
            $string = str_replace('{padding}', $padding, $string);
            $a++;
        }
        while ($cols > $a) {
            $string .= str_replace('{numcol}', $a + 1, $beforeOne[$type]).$afterOne[$type];
            $a++;
        }

        $string .= $afterBlock[$type];
        $string .= $afterAll[$type];

        $equalwidth = intval(100 / $cols).'%';

        $string = str_replace(['{equalwidth}'], [$equalwidth], $string);

        return $string;
    }

    public function formatString(&$replaceme, $mytag)
    {
        if (!empty($mytag->part)) {
            $parts = explode(' ', $replaceme);
            if ($mytag->part == 'last') {
                $replaceme = count($parts) > 1 ? end($parts) : '';
            } else {
                if (is_numeric($mytag->part) && count($parts) >= $mytag->part) {
                    $replaceme = $parts[$mytag->part - 1];
                } else {
                    $replaceme = reset($parts);
                }
            }
        }

        if (!empty($mytag->type)) {
            if (empty($mytag->format)) {
                $mytag->format = acym_translation('ACYM_DATE_FORMAT_LC3');
            }
            if ($mytag->type == 'date') {
                $replaceme = acym_getDate(acym_getTime($replaceme), $mytag->format);
            } elseif ($mytag->type == 'time') {
                $replaceme = acym_getDate($replaceme, $mytag->format);
            } elseif ($mytag->type == 'diff') {
                try {
                    $date = $replaceme;
                    if (is_numeric($date)) {
                        $date = acym_getDate($replaceme, '%Y-%m-%d %H:%M:%S');
                    }
                    $dateObj = new \DateTime($date);
                    $nowObj = new \DateTime();
                    $diff = $dateObj->diff($nowObj);
                    $replaceme = $diff->format($mytag->format);
                } catch (\Exception $e) {
                    $replaceme = 'Error using the "diff" parameter in your tag. Please make sure the DateTime() and diff() functions are available on your server.';
                }
            }
        }

        if (!empty($mytag->lower) || !empty($mytag->lowercase)) {
            $replaceme = function_exists('mb_strtolower') ? mb_strtolower($replaceme, 'UTF-8') : strtolower($replaceme);
        }
        if (!empty($mytag->upper) || !empty($mytag->uppercase)) {
            $replaceme = function_exists('mb_strtoupper') ? mb_strtoupper($replaceme, 'UTF-8') : strtoupper($replaceme);
        }
        if (!empty($mytag->ucwords)) {
            $replaceme = ucwords($replaceme);
        }
        if (!empty($mytag->ucfirst)) {
            $replaceme = ucfirst($replaceme);
        }
        if (isset($mytag->rtrim)) {
            $replaceme = empty($mytag->rtrim) ? rtrim($replaceme) : rtrim($replaceme, $mytag->rtrim);
        }
        if (!empty($mytag->urlencode)) {
            $replaceme = urlencode($replaceme);
        }
        if (!empty($mytag->substr)) {
            $args = explode(',', $mytag->substr);
            if (isset($args[1])) {
                $replaceme = substr($replaceme, intval($args[0]), intval($args[1]));
            } else {
                $replaceme = substr($replaceme, intval($args[0]));
            }
        }


        if (!empty($mytag->maxheight) || !empty($mytag->maxwidth)) {
            $imageHelper = new ImageHelper();
            $imageHelper->maxHeight = empty($mytag->maxheight) ? 999 : $mytag->maxheight;
            $imageHelper->maxWidth = empty($mytag->maxwidth) ? 999 : $mytag->maxwidth;
            $replaceme = $imageHelper->resizePictures($replaceme);
        }
    }

    public function replaceVideos(&$text)
    {
        $text = preg_replace(
            '#\[embed=videolink][^}]*youtube[^=]*=([^"/}]*)[^}]*}\[/embed]#i',
            '<a target="_blank" href="https://www.youtube.com/watch?v=$1"><img src="https://img.youtube.com/vi/$1/0.jpg"/></a>',
            $text
        );
        $text = preg_replace(
            '#<video[^>]*youtube\.com/embed/([^"/]*)[^>]*>[^>]*</video>#i',
            '<a target="_blank" href="https://www.youtube.com/watch?v=$1"><img src="https://img.youtube.com/vi/$1/0.jpg"/></a>',
            $text
        );
        $text = preg_replace(
            '#{JoooidContent[^}]*youtube[^}]*id"[^"]*"([^}"]*)"[^}]*}#i',
            '<a target="_blank" href="https://www.youtube.com/watch?v=$1"><img src="https://img.youtube.com/vi/$1/0.jpg"/></a>',
            $text
        );
        $text = preg_replace(
            '#<iframe[^>]*src="[^"]*youtube[^"]*embed/([^"?]*)(\?[^"]*)?"[^>]*>[^<]*</iframe>#Uis',
            '<a target="_blank" href="https://www.youtube.com/watch?v=$1"><img src="https://img.youtube.com/vi/$1/0.jpg"/></a>',
            $text
        );

        $text = preg_replace(
            '#{youtube}[^{]+v=([^{&]+)(&[^{]*)?{/youtube}#Uis',
            '<a target="_blank" href="https://www.youtube.com/watch?v=$1"><img src="https://img.youtube.com/vi/$1/0.jpg"/></a>',
            $text
        );

        $text = preg_replace('#{vimeo}(https://vimeo.com/[^{]+){/vimeo}#Uis', '<iframe src="$1"></iframe>', $text);
        $text = preg_replace('#{vimeo}([^{]+){/vimeo}#Uis', '<iframe src="https://player.vimeo.com/video/$1"></iframe>', $text);

        if (preg_match_all('#<iframe[^>]*src="[^"]*vimeo[^"]*/(\d+)([&/\?][^"]*)?"[^>]*>[^<]*</iframe>#Uis', $text, $matches)) {
            foreach ($matches[1] as $key => $match) {
                $hash = acym_fileGetContent('https://vimeo.com/api/v2/video/'.$match.'.php');
                $hash = @unserialize($hash);
                if (empty($hash)) continue;

                if (strpos($matches[0][$key], ' width="') !== false) {
                    $extension = substr($hash[0]['thumbnail_large'], strrpos($hash[0]['thumbnail_large'], '.'));
                    preg_match('#width="([^"]*)"#Uis', $matches[0][$key], $width);

                    $replace = strpos($hash[0]['thumbnail_large'], '_') === false ? '.' : '_';
                    $hash[0]['thumbnail_large'] = substr($hash[0]['thumbnail_large'], 0, strrpos($hash[0]['thumbnail_large'], $replace)).'_'.$width[1].$extension;
                }
                $thumbnail = 'https://i.vimeocdn.com/filter/overlay?src='.urlencode($hash[0]['thumbnail_large']);
                $thumbnail .= '&src='.urlencode('https://f.vimeocdn.com/p/images/crawler_play.png');

                $text = str_replace(
                    $matches[0][$key],
                    '<a target="_blank" href="'.acym_escape($hash[0]['url']).'"><img class="donotresize" alt="" src="'.acym_escape($thumbnail).'" /></a>',
                    $text
                );
            }
        }

        $text = preg_replace('#\[embed=videolink][^}]*video":"([^"]*)[^}]*}\[/embed]#i', '<a target="_blank" href="$1"><img src="'.ACYM_IMAGES.'/video.png"/></a>', $text);
        $text = preg_replace('#<video[^>]*src="([^"]*)"[^>]*>[^>]*</video>#i', '<a target="_blank" href="$1"><img src="'.ACYM_IMAGES.'/video.png"/></a>', $text);
    }

    private function _convertbase64pictures(&$html)
    {
        if (!preg_match_all('#<img[^>]*src=("data:image/([^;]{1,5});base64[^"]*")([^>]*)>#Uis', $html, $resultspictures)) {
            return;
        }



        $dest = ACYM_MEDIA.'resized'.DS;
        acym_createDir($dest);
        foreach ($resultspictures[2] as $i => $extension) {
            $pictname = md5($resultspictures[1][$i]).'.'.$extension;
            $picturl = ACYM_LIVE.ACYM_MEDIA_FOLDER.'/resized/'.$pictname;
            $pictPath = $dest.$pictname;
            $pictCode = trim($resultspictures[1][$i], '"');
            if (file_exists($pictPath)) {
                $html = str_replace($pictCode, $picturl, $html);
                continue;
            }

            $getfunction = '';
            switch ($extension) {
                case 'gif':
                    $getfunction = 'ImageCreateFromGIF';
                    break;
                case 'jpg':
                case 'jpeg':
                    $getfunction = 'ImageCreateFromJPEG';
                    break;
                case 'png':
                    $getfunction = 'ImageCreateFromPNG';
                    break;
            }

            if (empty($getfunction) || !function_exists($getfunction)) {
                continue;
            }

            $img = $getfunction($pictCode);

            if (in_array($extension, ['gif', 'png'])) {
                imagealphablending($img, false);
                imagesavealpha($img, false);
            }

            ob_start();
            switch ($extension) {
                case 'gif':
                    $status = imagegif($img);
                    break;
                case 'jpg':
                case 'jpeg':
                    $status = imagejpeg($img, null, 100);
                    break;
                case 'png':
                    $status = imagepng($img, null, 1);
                    break;
            }
            $imageContent = ob_get_clean();
            $status = $status && acym_writeFile($pictPath, $imageContent);

            if (!$status) {
                continue;
            }
            $html = str_replace($pictCode, $picturl, $html);
        }
    }

    public function cleanHtml(&$html)
    {
        $this->_convertbase64pictures($html);

        $pregreplace = [];
        $pregreplace['#<tr([^>"]*>([^<]*<td[^>]*>[ \n\s]*<img[^>]*>[ \n\s]*</ *td[^>]*>[ \n\s]*)*</ *tr)#Uis'] = '<tr style="line-height: 0px;" $1';
        $pregreplace['#<td(((?!style|>).)*>[ \n\s]*(<a[^>]*>)?[ \n\s]*<img[^>]*>[ \n\s]*(</a[^>]*>)?[ \n\s]*</ *td)#Uis'] = '<td style="line-height: 0px;" $1';

        $pregreplace['#{tab[ =][^}]*}#is'] = '';
        $pregreplace['#{/tabs}#is'] = '';
        $pregreplace['#{jcomments\s+(on|off|lock)}#is'] = '';

        $pregreplace["#(onmouseout|onmouseover|onclick|onfocus|onload|onblur) *= *\"(?:(?!\").)*\"#Ui"] = '';
        $pregreplace["#< *script(?:(?!< */ *script *>).)*< */ *script *>#Uis"] = '';
        $pregreplace["#< *iframe(?:(?!< */ *iframe *>).)*< */ *iframe *>#Uis"] = '';

        $pregreplace['#(<p style=")([^>]*>\s*<img *[^>]*margin-left: auto; margin-right: auto;[^>]*>\s*</p>)#Uis'] = '$1text-align: center;$2';

        $newbody = preg_replace(array_keys($pregreplace), $pregreplace, $html);
        if (!empty($newbody)) {
            $html = $newbody;
        }

        $body = preg_replace_callback('/src="([^"]* [^"]*)"/Ui', [$this, '_convertSpaces'], $html);
        if (!empty($body)) $html = $body;

        $html = acym_cmsCleanHtml($html);
    }

    public function _convertSpaces($matches)
    {
        return "src='".str_replace(' ', '%20', $matches[1])."'";
    }

    public function replaceTags(&$email, $tags, $html = false)
    {
        if (empty($tags)) return;

        $htmlVars = ['body'];
        $textVars = [
            'subject',
            'AltBody',
            'From',
            'FromName',
            'ReplyTo',
            'ReplyName',
            'bcc',
            'cc',
            'fromname',
            'fromemail',
            'replyname',
            'replyemail',
            'params',
            'preheader',
            'Preheader',
        ];

        $variables = array_merge($htmlVars, $textVars);

        if ($html) {
            if (empty($this->mailerHelper)) {
                $this->mailerHelper = new MailerHelper();
            }

            $textreplace = [];
            foreach ($tags as $i => $replacement) {
                if (isset($textreplace[$i])) continue;
                $textreplace[$i] = $this->mailerHelper->textVersion($replacement, true);
            }
        } else {
            $textreplace = $tags;
        }

        foreach ($variables as $var) {
            if (empty($email->$var)) continue;
            $email->$var = $this->replaceDText($email->$var, in_array($var, $htmlVars) ? $tags : $textreplace);
        }
    }

    public function replaceDText($text, $replacement)
    {
        if (is_array($text)) {
            foreach ($text as $i => &$oneCell) {
                if (empty($oneCell)) continue;
                $oneCell = $this->replaceDText($oneCell, $replacement);
            }
        } elseif (is_string($text) && !empty($text)) {
            foreach ($replacement as $code => $value) {
                $codes = [$code, urlencode($code)];
                $safePregValue = str_replace('$', '\$', $value);

                foreach ($codes as $oneCode) {
                    $text = preg_replace(
                        '#<span[^>]+'.preg_quote($oneCode, '#').'.+</em>[^<]*</span>#Uis',
                        $safePregValue,
                        $text
                    );

                    $text = preg_replace(
                        '#(<tr[^>]+)data-dynamic="'.preg_quote($oneCode, '#').'"([^>]+>[^<]*<td[^>]*>).+</i>[^<]*</td>[^<]*</tr>#Uis',
                        '$1$2'.$safePregValue.'</td></tr>',
                        $text
                    );

                    $text = str_replace($oneCode, $value, $text);
                }
            }
        }

        return $text;
    }

    public function extractTags($email, $tagfamily)
    {
        $results = [];

        $match = '#(?:{|%7B)'.$tagfamily.'(?:%3A|\\:)(.*)(?:}|%7D)#Ui';
        $variables = [
            'subject',
            'AltBody',
            'body',
            'From',
            'FromName',
            'ReplyTo',
            'ReplyName',
            'bcc',
            'cc',
            'fromname',
            'fromemail',
            'replyname',
            'replyemail',
            'params',
            'preheader',
            'Preheader',
        ];
        $found = false;
        foreach ($variables as $var) {
            if (empty($email->$var)) continue;

            if (is_array($email->$var)) {
                foreach ($email->$var as $i => $arrayField) {
                    if (empty($arrayField)) continue;

                    if (is_array($arrayField)) {
                        foreach ($arrayField as $a => $oneval) {
                            $found = preg_match_all($match, $oneval, $results[$var.$i.'-'.$a]) || $found;
                            if (empty($results[$var.$i.'-'.$a][0])) unset($results[$var.$i.'-'.$a]);
                        }
                    } else {
                        $found = preg_match_all($match, $arrayField, $results[$var.$i]) || $found;
                        if (empty($results[$var.$i][0])) unset($results[$var.$i]);
                    }
                }
            } else {
                $found = preg_match_all($match, $email->$var, $results[$var]) || $found;
                if (empty($results[$var][0])) unset($results[$var]);
            }
        }

        if (!$found) {
            return [];
        }

        $tags = [];
        foreach ($results as $var => $allresults) {
            foreach ($allresults[0] as $i => $oneTag) {
                if (isset($tags[$oneTag])) {
                    continue;
                }
                $tags[$oneTag] = $this->extractTag($allresults[1][$i]);
            }
        }

        return $tags;
    }

    public function extractTag($oneTag)
    {
        $oneTag = str_replace(['[time]+', '[time]-'], [urlencode('[time]+'), urlencode('[time]-')], $oneTag);
        $arguments = explode('|', strip_tags(urldecode($oneTag)));
        $tag = new \stdClass();
        $tag->id = $arguments[0];
        $tag->default = '';
        for ($i = 1, $a = count($arguments) ; $i < $a ; $i++) {
            $args = explode(':', $arguments[$i], 2);
            $arg0 = trim($args[0]);
            if (empty($arg0)) continue;

            if (isset($args[1])) {
                $tag->$arg0 = $args[1];
                if (isset($args[2])) {
                    $tag->{$args[0]} .= ':'.$args[2];
                }
            } else {
                $tag->$arg0 = true;
            }
        }

        return $tag;
    }

    public function wrapText($text, $tag)
    {
        $this->wraped = false;

        if (!empty($tag->wrap)) $tag->wrap = intval($tag->wrap);
        if (empty($tag->wrap)) return $text;

        $allowedTags = [
            'b',
            'strong',
            'i',
            'em',
            'a',
            'p',
            'div',
            'h2',
            'h3',
            'h4',
            'h5',
            'h6',
        ];

        $aloneAllowedTags = [
            'br',
            'img',
        ];

        $newText = strip_tags($text, '<'.implode('><', array_merge($allowedTags, $aloneAllowedTags)).'>');

        $newText = preg_replace('/^(\s|\n|(<br[^>]*>))+/i', '', trim($newText));
        $newText = preg_replace('/(\s|\n|(<br[^>]*>))+$/i', '', trim($newText));

        $newText = str_replace(['&lt', '&gt'], ['<', '>'], $newText);

        $numChar = strlen($newText);

        $numCharStrip = strlen(strip_tags($newText));

        if ($numCharStrip <= $tag->wrap) {
            return $newText;
        }

        $this->wraped = true;

        $open = [];

        $write = true;

        $countStripChar = 0;

        for ($i = 0 ; $i < $numChar ; $i++) {
            if ($newText[$i] == '<') {
                foreach ($allowedTags as $oneAllowedTag) {
                    if ($numChar >= ($i + strlen($oneAllowedTag) + 1) && substr($newText, $i, strlen($oneAllowedTag) + 1) == '<'.$oneAllowedTag && (in_array(
                            $newText[$i + strlen($oneAllowedTag) + 1],
                            [' ', '>']
                        ))) {
                        $write = false;
                        $open[] = '</'.$oneAllowedTag.'>';
                    }

                    if ($numChar >= ($i + strlen($oneAllowedTag) + 2) && substr($newText, $i, strlen($oneAllowedTag) + 2) == '</'.$oneAllowedTag) {
                        if (end($open) == '</'.$oneAllowedTag.'>') {
                            array_pop($open);
                        }
                    }
                }

                foreach ($aloneAllowedTags as $oneAllowedTag) {
                    if ($numChar >= ($i + strlen($oneAllowedTag) + 1) && substr($newText, $i, strlen($oneAllowedTag) + 1) == '<'.$oneAllowedTag && (in_array(
                            $newText[$i + strlen($oneAllowedTag) + 1],
                            [' ', '/', '>']
                        ))) {
                        $write = false;
                    }
                }
            }

            if ($write) {
                $countStripChar++;
            }

            if ($newText[$i] == ">") {
                $write = true;
            }

            if ($newText[$i] == " " && $countStripChar >= $tag->wrap && $write) {
                $newText = substr($newText, 0, $i).'...';

                $open = array_reverse($open);
                $newText = $newText.implode('', $open);

                break;
            }
        }

        $newText = preg_replace('/^(\s|\n|(<br[^>]*>))+/i', '', trim($newText));
        $newText = preg_replace('/(\s|\n|(<br[^>]*>))+$/i', '', trim($newText));

        return $newText;
    }

    public function getStandardDisplay($format)
    {
        if (empty($format->tag->format)) $format->tag->format = 'TOP_LEFT';
        if (!in_array($format->tag->format, ['TOP_LEFT', 'TOP_RIGHT', 'TITLE_IMG', 'TITLE_IMG_RIGHT', 'CENTER_IMG', 'TOP_IMG', 'COL_LEFT', 'COL_RIGHT'])) {
            $format->tag->format = 'TOP_LEFT';
        }

        $invertValues = [
            'TOP_LEFT' => 'TOP_RIGHT',
            'TITLE_IMG' => 'TITLE_IMG_RIGHT',
            'COL_LEFT' => 'COL_RIGHT',
            'TOP_RIGHT' => 'TOP_LEFT',
            'TITLE_IMG_RIGHT' => 'TITLE_IMG',
            'COL_RIGHT' => 'COL_LEFT',
        ];
        if (!empty($format->tag->invert) && !empty($invertValues[$format->tag->format])) {
            $format->tag->format = $invertValues[$format->tag->format];
        }

        $image = '';
        if (!empty($format->imagePath)) {
            $style = '';
            $linkStyle = '';

            if (in_array($format->tag->format, ['TOP_LEFT', 'TITLE_IMG'])) {
                $style = 'left';
            } elseif (in_array($format->tag->format, ['TOP_RIGHT', 'TITLE_IMG_RIGHT'])) {
                $style = 'right';
            }

            if (!empty($style)) {
                $linkStyle = 'style="float:'.$style.';"';

                if ($style === 'left') {
                    $style = 'style="float:left; margin-right: 7px; margin-bottom: 7px;"';
                } else {
                    $style = 'style="float:right; margin-left: 7px; margin-bottom: 7px;"';
                }
            }

            preg_match('#src="([^"]+)"#Uis', $format->imagePath, $matches);
            if (!empty($matches[1])) $format->imagePath = $matches[1];
            $image = '<img class="content_main_image" alt="" src="'.$format->imagePath.'" '.$style.' />';

            if(!empty($format->imageCaption) && !in_array($format->tag->format, ['TITLE_IMG', 'TITLE_IMG_RIGHT'])){
                $image .= '<p class="content_main_image_caption">'.acym_escape($format->imageCaption).'</p>';
            }
        }

        $result = '';
        if ($format->tag->format == 'TITLE_IMG' || $format->tag->format == 'TITLE_IMG_RIGHT') {
            $format->title = $image.$format->title;
            $image = '';
        }

        if (!empty($format->link) && !empty($image)) {
            $image = '<a target="_blank" href="'.$format->link.'" '.$linkStyle.'>'.$image.'</a>';
        }

        if ($format->tag->format == 'TOP_IMG' && !empty($image)) {
            $result = $image;
            $image = '';
        }

        if (in_array($format->tag->format, ['COL_LEFT', 'COL_RIGHT'])) {
            if (empty($image)) {
                $format->tag->format = 'TOP_LEFT';
            } else {
                $result = '<table><tr><td valign="middle" style="vertical-align: middle; padding-right: 7px;" class="acyleftcol">';
                if ($format->tag->format == 'COL_LEFT') {
                    $result .= $image.'</td><td valign="top" class="acyrightcol">';
                }
            }
        }

        if (!empty($format->title)) {
            if (!empty($format->link)) {
                if (empty($format->tag->type) || $format->tag->type !== 'title') {
                    $format->title = '<h2 class="acym_title">'.$format->title.'</h2>';
                }

                $title = '<a';
                if (!empty($format->tag->type) && $format->tag->type === 'title') $title .= ' class="acym_title"';
                $title .= ' href="'.$format->link.'" target="_blank" name="'.$this->name.'-'.$format->tag->id.'">';
                $title .= $format->title;
                $title .= '</a>';
                $format->title = $title;
            } else {
                if (empty($format->tag->type) || $format->tag->type != 'title') {
                    $format->title = '<h2 class="acym_title">'.$format->title.'</h2>';
                }
            }

            $result .= $format->title;
        }

        if (!empty($format->afterTitle)) $result .= $format->afterTitle;

        if (!empty($format->description)) {
            $format->description = $this->wrapText($format->description, $format->tag);
        }
        $this->wrappedText = $format->description;


        $rowText = '<div class="acydescription">';
        $endRow = '</div><br />';
        if (in_array($format->tag->format, ['TOP_LEFT', 'TOP_RIGHT', 'TITLE_IMG', 'TITLE_IMG_RIGHT', 'TOP_IMG'])) {
            if (!empty($image) || !empty($format->description)) {
                $result .= $rowText.$image.$format->description.$endRow;
            }
        } elseif ($format->tag->format == 'CENTER_IMG') {
            if (!empty($image)) {
                $result .= '<div class="acymainimage">'.$image.$endRow;
            }

            if (!empty($format->description)) {
                $result .= $rowText.$format->description.$endRow;
            }
        } elseif (in_array($format->tag->format, ['COL_LEFT', 'COL_RIGHT'])) {
            if (!empty($format->description)) {
                $result .= $rowText.$format->description.$endRow;
            }

            if ($format->tag->format == 'COL_RIGHT') {
                $result .= '</td><td valign="middle" style="vertical-align: middle; padding-left: 7px;" class="acyrightcol">'.$image;
            }
            $result .= '</td></tr></table>';
        }

        if (!empty($format->customFields)) {
            $result .= '<table style="width:100%;" class="customfieldsarea"><tr>';

            $format->cols = empty($format->tag->nbcols) ? 1 : intval($format->tag->nbcols);
            if (empty($format->cols)) $format->cols = 1;

            $i = 0;
            foreach ($format->customFields as $oneField) {
                if ($i != 0 && $i % $format->cols == 0) $result .= '</tr><tr>';

                $result .= '<td nowrap="nowrap" class="cf';

                if (empty($oneField[1])) {
                    $result .= 'value" colspan="2">';
                } else {
                    $result .= 'label">'.$oneField[1].'</td><td class="cfvalue">';
                }

                $result .= $oneField[0].'</td>';
                $i++;
            }

            while ($i % $format->cols != 0) {
                $result .= '<td colspan="2"></td>';
                $i++;
            }

            $result .= '</tr></table>';
        }

        if (!empty($format->afterArticle)) {
            $result .= $format->afterArticle;
        }

        return $result;
    }

    public function managePicts($tag, $result)
    {
        if (!isset($tag->pict)) {
            return $result;
        }

        $imageHelper = new ImageHelper();
        if ($tag->pict === 'resized') {
            $imageHelper->maxHeight = empty($tag->maxheight) ? 150 : $tag->maxheight;
            $imageHelper->maxWidth = empty($tag->maxwidth) ? 150 : $tag->maxwidth;
            if ($imageHelper->available()) {
                $result = $imageHelper->resizePictures($result);
            } elseif (acym_isAdmin()) {
                acym_enqueueMessage($imageHelper->error, 'notice');
            }
        } elseif ($tag->pict == '0') {
            $result = $imageHelper->removePictures($result);
        }

        return acym_absoluteURL($result);
    }

    public function displayOptions($options, $dynamicIdentifier, $type = 'individual', $defaultValues = null)
    {
        $suffix = preg_replace('[^a-zA-Z0-9]', '_', $dynamicIdentifier);
        $updateFunction = 'updateDynamic'.$suffix;

        $outputStructure = [
            'topOptions' => [],
            'options' => [],
        ];
        $jsOptionsMerge = [];

        foreach ($options as $option) {
            $currentLabel = $option['title'];
            $currentOption = '';

            if (isset($defaultValues->{$option['name']})) $option['default'] = $defaultValues->{$option['name']};

            if ($option['type'] == 'pictures') {
                $displayedPictures = isset($option['default']) ? $option['default'] : 'resized';
                if (isset($defaultValues->pict)) $displayedPictures = $defaultValues->pict;
                $resizeDisplay = 'resized' === $displayedPictures ? '' : 'style="display: none;"';
                $maxWidth = isset($defaultValues->maxwidth) ? $defaultValues->maxwidth : 150;
                $maxHeight = isset($defaultValues->maxheight) ? $defaultValues->maxheight : 150;

                $valImages = [];
                $valImages[] = acym_selectOption('1', 'ACYM_YES');
                $valImages[] = acym_selectOption('resized', 'ACYM_RESIZED');
                $valImages[] = acym_selectOption('0', 'ACYM_NO');
                $currentOption .= '<div class="cell large-5 acym_plugin_field">'.acym_translation('ACYM_DISPLAY').'</div>';
                $currentOption .= '<div class="cell large-7">'.acym_radio(
                        $valImages,
                        'pict'.$suffix,
                        $displayedPictures,
                        ['onclick' => $updateFunction.'();'],
                        ['containerClass' => 'dcontent_pictures'],
                        !acym_isAdmin()
                    ).'</div>';
                $currentOption .= '<div id="pictsize'.$suffix.'" class="cell grid-x margin-y margin-top-1" '.$resizeDisplay.'>
                                <div class="cell large-5 acym_plugin_field">'.acym_translation('ACYM_MAX_WIDTH').'</div>
                                <div class="cell large-7">
                                	<input class="intext_input" name="pictwidth'.$suffix.'" type="number" onchange="'.$updateFunction.'();" value="'.intval($maxWidth).'"/>
                            	</div>
                                <div class="cell large-5 acym_plugin_field">'.acym_translation('ACYM_MAX_HEIGHT').'</div>
                                <div class="cell large-7">
                    				<input class="intext_input" name="pictheight'.$suffix.'" type="number" onchange="'.$updateFunction.'();" value="'.intval($maxHeight).'"/>
                            	</div>
                            </div>';
                if (!empty($option['caption'])) {
                    $currentOption .= '<div class="cell grid-x margin-top-1">';
                    $currentOption .= '<label class="cell large-5 acym_plugin_field">'.acym_translation('ACYM_CAPTION').'</label>';
                    $currentOption .= acym_radio(
                        [
                            acym_selectOption('1', 'ACYM_YES'),
                            acym_selectOption('0', 'ACYM_NO'),
                        ],
                        'caption'.$suffix,
                        isset($defaultValues->caption) ? $defaultValues->caption : '0',
                        ['onchange' => $updateFunction.'();'],
                        ['containerClass' => 'cell large-7']
                    );
                    $currentOption .= '</div>';

                    $jsOptionsMerge[] = 'otherinfo += "| caption:" + jQuery(\'input[name="caption'.$suffix.'"]:checked\').val();';
                }

                $jsOptionsMerge[] = '
                    var _pictVal'.$suffix.' = jQuery(\'input[name="pict'.$suffix.'"]:checked\').val();
                    otherinfo += "| pict:" + _pictVal'.$suffix.';
    
                    if(_pictVal'.$suffix.' == "resized"){
                        jQuery("#pictsize'.$suffix.'").show();
                        otherinfo += "| maxwidth:" + jQuery(\'input[name="pictwidth'.$suffix.'"]\').val();
                        otherinfo += "| maxheight:" + jQuery(\'input[name="pictheight'.$suffix.'"]\').val();
                    }else{
                        jQuery("#pictsize'.$suffix.'").hide();
                    }';
            } elseif ($option['type'] == 'checkbox') {
                if (!empty($option['default'])) {
                    $checkedValues = explode(',', $option['default']);
                    foreach ($option['options'] as $key => $oneOption) {
                        $oneOption[1] = in_array($key, $checkedValues);
                        $option['options'][$key] = $oneOption;
                    }
                }

                $currentOption .= '<div class="cell grid-x">';
                foreach ($option['options'] as $value => $title) {
                    $currentOption .= '<div class="cell medium-6">
                                <input type="checkbox" name="'.acym_escape($option['name'].$suffix).'" value="'.acym_escape($value).'" id="'.acym_escape(
                            $value.$suffix
                        ).'" onclick="'.$updateFunction.'();" '.($title[1] ? 'checked="checked"' : '').'/>
                                <label style="margin-left:5px" for="'.acym_escape($value.$suffix).'">'.acym_translation($title[0]).'</label>
                            </div>';
                }
                $currentOption .= '</div>';

                if (empty($option['separator'])) $option['separator'] = ',';

                $jsOptionsMerge[] = 'var _checked'.$option['name'].$suffix.' = [];
                    jQuery("input:checkbox[name='.$option['name'].$suffix.']:checked").each(function(){
                        _checked'.$option['name'].$suffix.'.push(jQuery(this).val());
                    });
                    if(_checked'.$option['name'].$suffix.'.length) otherinfo += "| '.$option['name'].':" + _checked'.$option['name'].$suffix.'.join("'.$option['separator'].'");';
            } elseif ($option['type'] == 'boolean') {
                $currentOption .= acym_boolean(
                    $option['name'].$suffix,
                    $option['default'],
                    $option['name'].$suffix,
                    ['onclick' => $updateFunction.'();']
                );

                $jsOptionsMerge[] = 'otherinfo += "| '.$option['name'].':" + jQuery(\'input[name="'.$option['name'].$suffix.'"]:checked\').val();';
            } elseif ($option['type'] == 'radio') {
                $radioOptions = [];
                foreach ($option['options'] as $value => $title) {
                    $radioOptions[] = acym_selectOption($value, $title);
                }

                $currentOption .= acym_radio(
                    $radioOptions,
                    $option['name'].$suffix,
                    $option['default'],
                    ['onclick' => $updateFunction.'();'],
                    ['pluginMode' => true],
                    !acym_isAdmin()
                );
                $jsOptionsMerge[] = 'otherinfo += "| '.$option['name'].':" + jQuery(\'input[name="'.$option['name'].$suffix.'"]:checked\').val();';
            } elseif ($option['type'] == 'select') {
                $selectOptions = [];
                foreach ($option['options'] as $value => $title) {
                    if (is_object($title)) {
                        $selectOptions[] = acym_selectOption($title->value, $title->text);
                    } else {
                        $selectOptions[] = acym_selectOption($value, $title);
                    }
                }

                $default = empty($option['default']) ? null : $option['default'];
                if (!empty($default) && strpos($default, ',')) list($default, $defaultOrder) = explode(',', $default);

                $attributes = [
                    'onchange' => $updateFunction.'();',
                    'id' => $option['name'].$suffix,
                ];
                if ($option['name'] === 'order') {
                    $attributes['class'] = 'acym__dynamics__ordering__select';
                }
                $currentOption .= acym_select(
                    $selectOptions,
                    $option['name'].$suffix,
                    $default,
                    $attributes
                );

                if ($option['name'] === 'order') {

                    $dirs = [
                        'desc' => acym_translation('ACYM_DESC'),
                        'asc' => acym_translation('ACYM_ASC'),
                    ];
                    if (empty($defaultOrder)) $defaultOrder = empty($option['defaultdir']) ? null : $option['defaultdir'];
                    $currentOption .= ' '.acym_select(
                            $dirs,
                            'orderdir',
                            $defaultOrder,
                            [
                                'onchange' => $updateFunction.'();',
                                'style' => 'width: 115px;',
                                'class' => 'acym__dynamics__ordering__select',
                            ]
                        );

                    $jsOptionsMerge[] = 'otherinfo += "| '.$option['name'].':" + jQuery(\'[name="'.$option['name'].$suffix.'"]\').val() + "," + jQuery(\'[name="orderdir"]\').val();';
                } else {
                    $jsOptionsMerge[] = 'otherinfo += "| '.$option['name'].':" + jQuery(\'[name="'.$option['name'].$suffix.'"]\').val();';
                }
            } elseif ($option['type'] == 'multiselect') {
                $selectOptions = [];
                foreach ($option['options'] as $value => $title) {
                    $selectOptions[] = acym_selectOption($value, $title);
                }


                if (!isset($option['default'])) $option['default'] = [];
                if (!is_array($option['default'])) $option['default'] = explode(',', $option['default']);

                $currentOption .= acym_selectMultiple(
                    $selectOptions,
                    $option['name'].$suffix,
                    $option['default'],
                    ['onchange' => $updateFunction.'();', 'id' => $option['name'].$suffix]
                );

                $jsOptionsMerge[] = '
                var theMultiSelect = document.querySelector(\'[name="'.$option['name'].$suffix.'[]"]\');
                var selectedOptions = [];
                for(var i = 0 ; i < theMultiSelect.length ; i++){
                	if(theMultiSelect[i].selected){
                		selectedOptions.push(theMultiSelect[i].value);
                	}
                }
                otherinfo += "| '.$option['name'].':" + selectedOptions.join(",");';
            } elseif ($option['type'] == 'text') {
                if (!isset($option['default'])) $option['default'] = '';
                $class = empty($option['class']) ? 'acym_plugin_text_field' : $option['class'];
                $placeholder = empty($option['placeholder']) ? '' : ' placeholder="'.acym_escape($option['placeholder']).'"';
                $currentOption .= '<input type="text" name="'.$option['name'].$suffix.'" id="'.$option['name'].$suffix.'" onchange="'.$updateFunction.'();" value="'.acym_escape(
                        $option['default']
                    ).'" class="'.acym_escape($class).'" '.$placeholder.'/>';
                $jsOptionsMerge[] = 'otherinfo += "| '.$option['name'].':" + jQuery(\'input[name="'.$option['name'].$suffix.'"]\').val();';
            } elseif ($option['type'] == 'number') {
                $min = empty($option['min']) ? '' : ' min="'.$option['min'].'"';
                $max = empty($option['max']) ? '' : ' max="'.$option['max'].'"';
                $class = empty($option['class']) ? 'acym_plugin_text_field' : $option['class'];
                $currentOption .= '<input type="number"'.$min.$max.' name="'.$option['name'].$suffix.'" id="'.$option['name'].$suffix.'" onchange="'.$updateFunction.'();" value="'.intval(
                        $option['default']
                    ).'" class="'.acym_escape($class).'" />';
                $jsOptionsMerge[] = 'otherinfo += "| '.$option['name'].':" + jQuery(\'input[name="'.$option['name'].$suffix.'"]\').val();';
            } elseif ($option['type'] == 'intextfield') {
                $inputType = 'text';
                if (!empty($option['isNumber']) && $option['isNumber'] === 1) $inputType = 'number';
                $currentOption .= acym_translationSprintf(
                    $option['text'],
                    '<input type="'.$inputType.'" name="'.$option['name'].$suffix.'" id="'.$option['name'].$suffix.'" class="intext_input" value="'.acym_escape(
                        $option['default']
                    ).'" onchange="'.$updateFunction.'();"/>'
                );
                $jsOptionsMerge[] = 'otherinfo += "| '.$option['name'].':" + jQuery(\'input[name="'.$option['name'].$suffix.'"]\').val();';
            } elseif ($option['type'] == 'date') {
                $relativeTime = '-';
                if (!empty($option['relativeDate'])) $relativeTime = $option['relativeDate'];
                $currentOption .= acym_dateField($option['name'].$suffix, $option['default'], '', ' onchange="'.$updateFunction.'();"', $relativeTime);
                $jsOptionsMerge[] = 'otherinfo += "| '.$option['name'].':" + jQuery(\'input[name="'.$option['name'].$suffix.'"]\').val();';
            } elseif ($option['type'] == 'custom') {
                $currentOption .= $option['output'];
                $jsOptionsMerge[] = $option['js'];
            }

            if (!empty($option['main']) || in_array($option['type'], ['pictures', 'checkbox'])) {
                $outputStructure['topOptions'][$currentLabel] = $currentOption;

                if ($option['type'] === 'checkbox' && $currentLabel === 'ACYM_DISPLAY') {
                    $formatOption = '<div class="grid-x">';
                    $formatOption .= '<div class="cell large-3">'.acym_translation('ACYM_FORMAT').'</div>';
                    $formatOption .= '<div class="cell large-9 dcontentFormatContainer">';

                    $default = empty($defaultValues->format) ? 'TOP_LEFT' : $defaultValues->format;
                    $formats = ['TOP_LEFT', 'TOP_RIGHT', 'TITLE_IMG', 'TITLE_IMG_RIGHT', 'CENTER_IMG', 'TOP_IMG', 'COL_LEFT', 'COL_RIGHT'];
                    foreach ($formats as $oneFormat) {
                        $class = 'button-radio';
                        if ($default === $oneFormat) $class .= ' button-radio-selected';

                        $formatOption .= '<button 
											class="'.$class.'" 
											acym-button-radio-group="dcontentFormat'.$suffix.'" 
											acym-data-type="'.$oneFormat.'"
											acym-callback="'.$updateFunction.'">
											<img alt="'.$oneFormat.'" src="'.ACYM_IMAGES.'dynamics/'.strtolower($oneFormat).'.png"/>
										</button>';
                    }
                    $formatOption .= '</div>';

                    if ($type === 'grouped') {
                        $formatOption .= '<div class="cell large-3">'.acym_translation('ACYM_ALTERNATE').acym_info('ACYM_ALTERNATE_DESC').'</div>';
                        $formatOption .= '<div class="cell large-9">';
                        $formatOption .= acym_boolean(
                            'alternate'.$suffix,
                            !empty($defaultValues->alternate),
                            'alternate'.$suffix,
                            ['onclick' => $updateFunction.'();']
                        );
                        $formatOption .= '</div>';

                        $jsOptionsMerge[] = 'var alternate = jQuery(\'input[name="alternate'.$suffix.'"]:checked\').val();';
                        $jsOptionsMerge[] = 'if (!acym_helper.empty(alternate)) otherinfo += "| alternate";';
                    }

                    $formatOption .= '</div>';

                    $jsOptionsMerge[] = 'var selectedFormatOption = jQuery(\'.button-radio-selected[acym-button-radio-group="dcontentFormat'.$suffix.'"]\')';
                    $jsOptionsMerge[] = 'if (!acym_helper.empty(selectedFormatOption)) otherinfo += "| format:" + selectedFormatOption.attr("acym-data-type");';

                    $outputStructure['topOptions']['ACYM_FORMAT'] = $formatOption;
                }
                continue;
            }

            if (empty($option['section'])) $option['section'] = 'ACYM_OTHER_OPTIONS';

            $currentLabel = acym_translation($currentLabel);
            if (!empty($option['tooltip'])) {
                $currentLabel .= '&nbsp;'.acym_info($option['tooltip'], 'acym_plugin_field_'.$option['name']);
            }
            $currentLabel = '<label class="cell large-5 acym_plugin_field acym_plugin_field_'.$option['type'].'" for="'.acym_escape(
                    $option['name'].$suffix
                ).'">'.$currentLabel.'</label>';

            $outputStructure['options'][$option['section']][$currentLabel] = $currentOption;
        }

        if (!empty($outputStructure['options'])) {
            foreach ($outputStructure['options'] as $section => $options) {
                $formattedOptions = '';
                foreach ($options as $label => $option) {
                    $formattedOptions .= '<div class="cell grid-x margin-bottom-1">'.$label;
                    $formattedOptions .= '<div class="cell large-7">'.$option.'</div>';
                    $formattedOptions .= '</div>';
                }
                $outputStructure['topOptions'][$section] = $formattedOptions;
            }
        }

        $output = '';
        if (!empty($outputStructure['topOptions'])) {
            foreach ($outputStructure['topOptions'] as $label => $oneOption) {
                $output .= '<p class="acym__wysid__right__toolbar__p acym__wysid__right__toolbar__p__open acym__title">';
                $output .= acym_translation($label).'<i class="acymicon-keyboard_arrow_up"></i>';
                $output .= '</p>';
                $output .= '<div class="acym__wysid__right__toolbar__design--show acym__wysid__right__toolbar__design acym__wysid__context__modal__container grid-x">';
                $output .= $oneOption;
                $output .= '</div>';
            }
        }

        $output .= '
            <script language="javascript" type="text/javascript">
                <!--
                var _selectedRows'.$suffix.' = [];
                var _selectedRows = [];
                if("undefined" === typeof _additionalInfo'.$suffix.') {
                	var _additionalInfo'.$suffix.' = {};
                 }
                ';
        if (!empty($defaultValues->id) && (empty($defaultValues->defaultPluginTab) || $dynamicIdentifier === $defaultValues->defaultPluginTab)) {
            $delimiter = strpos($defaultValues->id, '-') ? '-' : ',';
            $selected = explode($delimiter, $defaultValues->id);

            foreach ($selected as $key => $value) {
                if (empty($value)) continue;
                $output .= '_selectedRows'.$suffix.'['.intval($value).'] = true;
                ';
            }
        }

        $output .= '
                function applyContent'.$suffix.'(contentid, row){
                    if(_selectedRows'.$suffix.'[contentid]){
                        jQuery(row).removeClass("selected_row");
                        delete _selectedRows'.$suffix.'[contentid];
                    }else{
                    ';

        if ('individual' === $type) {
            $output .= '
						for(let elementKey in _selectedRows'.$suffix.') {
							if(!_selectedRows'.$suffix.'.hasOwnProperty(elementKey)) continue;
							
							jQuery(\'[data-id="\' + elementKey + \'"]\').removeClass("selected_row");
                        	delete _selectedRows'.$suffix.'[elementKey];
						}
				';
        }

        $output .= '
                        jQuery(row).addClass("selected_row");
                        _selectedRows'.$suffix.'[contentid] = true;
                    }
                    '.$updateFunction.'();
                    
                    if(typeof _selectedRows !== "undefined"){
                        _selectedRows = _selectedRows'.$suffix.';
                    }
                }
    
                function '.$updateFunction.'(){
                    var tag = "";
                    var otherinfo = "";
    
                    '.implode("\r\n\r\n", $jsOptionsMerge).'
    
    				for (let [index, info] of Object.entries(_additionalInfo'.$suffix.')){
    					otherinfo += "| "+index+":"+info;
    				}
                    ';

        if ($type == 'individual') {
            $output .= '
                    for(var i in _selectedRows'.$suffix.'){
                        if(!_selectedRows'.$suffix.'.hasOwnProperty(i)) continue;
                        
                        tag = tag + "{'.$dynamicIdentifier.':" + i + otherinfo + "}";
                    }';
        } elseif ($type == 'grouped') {
            $output .= '
                    tag = "{'.$dynamicIdentifier.':";
                    for(var icat in _selectedRows'.$suffix.'){
                        if(!_selectedRows'.$suffix.'.hasOwnProperty(icat)) continue;
                        tag += icat + "-";
                    }
                    tag += otherinfo + "}";';
        } elseif ($type == 'simple') {
            $output .= '
                    tag = "{'.$dynamicIdentifier.':" + otherinfo + "}";';
        }

        $output .= '
                    acym_editorWysidDynamic.insertDContent(tag);
                }
               
                function addAdditionalInfo'.$suffix.'(index, value){
                	_additionalInfo'.$suffix.'[index] = value;
                	'.$updateFunction.'();
                }
                //-->
            </script>';

        if ($type == 'individual') {
            acym_trigger('displayCustomViewEditor', [&$output], 'plgAcym'.ucfirst($dynamicIdentifier));
            $output .= '<input type="hidden" id="acym__dynamic__update__function" value="'.$updateFunction.'">';
        }

        return $output;
    }

    public function translateItem(&$item, &$tag, $referenceTable, $referenceId = 0)
    {
        if (empty($tag->lang) || (!file_exists(ACYM_ROOT.'components'.DS.'com_falang') && !file_exists(ACYM_ROOT.'components'.DS.'com_joomfish'))) return;
        $langid = intval(substr($tag->lang, strpos($tag->lang, ',') + 1));

        if (empty($langid)) return;

        if (empty($referenceId)) $referenceId = $tag->id;

        $table = file_exists(ACYM_ROOT.'components'.DS.'com_falang') ? '`#__falang_content`' : '`#__jf_content`';

        $query = 'SELECT `reference_field`, `value` 
					FROM '.$table.' 
					WHERE `published` = 1 
						AND `reference_table` = '.acym_escapeDB($referenceTable).' 
						AND `language_id` = '.intval($langid).' 
						AND `reference_id` = '.intval($referenceId);
        $translations = acym_loadObjectList($query);

        if (empty($translations)) return;

        foreach ($translations as $oneTranslation) {
            if (empty($oneTranslation->value)) continue;

            $translatedfield = $oneTranslation->reference_field;
            $item->$translatedfield = $oneTranslation->value;
        }
    }
}
