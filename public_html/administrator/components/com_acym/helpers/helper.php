<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.5.2
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php

define('ACYM_NAME', 'AcyMailing');
define('ACYM_DBPREFIX', '#__acym_');
define('ACYM_LANGUAGE_FILE', 'com_acym');
define('ACYM_ACYWEBSITE', 'https://www.acyba.com/');
define('ACYM_UPDATEMEURL', ACYM_ACYWEBSITE.'index.php?option=com_updateme&ctrl=');
define('ACYM_SPAMURL', ACYM_UPDATEMEURL.'spamsystem&task=');
define('ACYM_HELPURL', ACYM_UPDATEMEURL.'doc&component='.ACYM_NAME.'&page=');
define('ACYM_REDIRECT', ACYM_UPDATEMEURL.'redirect&page=');
define('ACYM_UPDATEURL', ACYM_UPDATEMEURL.'update&task=');
define('ACYM_DOCUMENTATION', ACYM_UPDATEMEURL.'doc&task=getLink');

if (!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

include_once rtrim(dirname(__DIR__), DS).DS.'library'.DS.strtolower('Joomla.php');

define('ACYM_LIVE', rtrim(acym_rootURI(), '/').'/');

if (is_callable('date_default_timezone_set')) {
    date_default_timezone_set(@date_default_timezone_get());
}

function acym_replaceDateTags($value)
{
    $replace = ['{year}', '{month}', '{weekday}', '{day}'];
    $replaceBy = [date('Y'), date('m'), date('N'), date('d')];
    $value = str_replace($replace, $replaceBy, $value);

    $results = [];
    if (preg_match_all('#{(year|month|weekday|day)\|(add|remove):([^}]*)}#Uis', $value, $results)) {
        foreach ($results[0] as $i => $oneMatch) {
            $format = str_replace(['year', 'month', 'weekday', 'day'], ['Y', 'm', 'N', 'd'], $results[1][$i]);
            $delay = str_replace(['add', 'remove'], ['+', '-'], $results[2][$i]).intval($results[3][$i]).' '.str_replace('weekday', 'day', $results[1][$i]);
            $value = str_replace($oneMatch, date($format, strtotime($delay)), $value);
        }
    }

    return $value;
}

function acym_getMailThumbnail($thumbnail)
{
    $thumbnailSRC = $thumbnail;

    if (!file_exists(str_replace(acym_rootURI(), ACYM_ROOT, $thumbnailSRC))) {
        $thumbnailSRC = ACYM_TEMPLATE_THUMBNAILS.$thumbnail;
    }

    if (!file_exists(str_replace(acym_rootURI(), ACYM_ROOT, $thumbnailSRC))) {
        $thumbnailSRC = ACYM_IMAGES.'thumbnails/'.$thumbnail;
    }

    if (empty($thumbnail) || !file_exists(str_replace(acym_rootURI(), ACYM_ROOT, $thumbnailSRC))) {
        $thumbnailSRC = ACYM_IMAGES.'default_template_thumbnail.png';
    }

    return $thumbnailSRC;
}

function acym_isLocalWebsite()
{
    return strpos(ACYM_LIVE, 'localhost') !== false || strpos(ACYM_LIVE, '127.0.0.1') !== false;
}

function acym_translationExists($key)
{
    return $key !== acym_translation($key);
}

function acym_checkPluginsVersion()
{
    $pluginClass = acym_get('class.plugin');
    $pluginsInstalled = $pluginClass->getMatchingElements();
    $pluginsInstalled = $pluginsInstalled['elements'];
    if (empty($pluginsInstalled)) return true;

    $url = ACYM_UPDATEMEURL.'integrationv6&task=getAllPlugin&cms='.ACYM_CMS;

    $res = acym_fileGetContent($url);
    $pluginsAvailable = json_decode($res, true);

    foreach ($pluginsInstalled as $key => $pluginInstalled) {
        foreach ($pluginsAvailable as $pluginAvailable) {
            if (str_replace('.zip', '', $pluginAvailable['file_name']) == $pluginInstalled->folder_name && !version_compare($pluginInstalled->version, $pluginAvailable['version'], '>=')) {
                $pluginsInstalled[$key]->uptodate = 0;
                $pluginsInstalled[$key]->latest_version = $pluginAvailable['version'];
                $pluginClass->save($pluginsInstalled[$key]);
            }
        }
    }

    return true;
}

function acym_checkVersion($ajax = false)
{
    ob_start();
    $config = acym_config();
    $url = ACYM_UPDATEURL.'loadUserInformation';

    $paramsForLicenseCheck = [
        'component' => 'acymailing', // Know which product to look at
        'level' => strtolower($config->get('level', 'starter')), // Know which version to look at
        'domain' => rtrim(ACYM_LIVE, '/'), // Tell the user if the automatic features are available for the current installation
        'version' => $config->get('version'), // Tell the user if a newer version is available
        'cms' => ACYM_CMS, // We may delay some new Acy versions depending on the CMS
        'cmsv' => ACYM_CMSV, // Acy isn't available for some versions
        'php' => PHP_VERSION, // Return a warning if Acy cannot be installed with this version
    ];

    foreach ($paramsForLicenseCheck as $param => $value) {
        $url .= '&'.$param.'='.urlencode($value);
    }

    $userInformation = acym_fileGetContent($url, 30);
    $warnings = ob_get_clean();
    $result = (!empty($warnings) && acym_isDebug()) ? $warnings : '';

    if (empty($userInformation) || $userInformation === false) {
        if ($ajax) {
            echo json_encode(['content' => '<br/><span style="color:#C10000;">'.acym_translation('ACYM_ERROR_LOAD_FROM_ACYBA').'</span><br/>'.$result]);
            exit;
        } else {
            return '';
        }
    }

    $decodedInformation = json_decode($userInformation, true);

    $newConfig = new stdClass();

    $newConfig->latestversion = $decodedInformation['latestversion'];
    $newConfig->expirationdate = $decodedInformation['expiration'];
    $newConfig->lastlicensecheck = time();
    $config->save($newConfig);

    acym_checkPluginsVersion();

    return $newConfig->lastlicensecheck;
}

function acym_loaderLogo()
{
    return '<div class="cell shrink acym_loader_logo">'.acym_getSvg(ACYM_IMAGES.'loader.svg').'</div>';
}

function acym_dateField($name, $value = '', $class = '', $attributes = '', $relativeDefault = '-')
{
    $result = '<div class="date_rs_selection_popup">';

    $result .= '<div class="grid-x">';
    $result .= acym_switchFilter(
        [
            'relative' => acym_translation('ACYM_RELATIVE_DATE'),
            'specific' => acym_translation('ACYM_SPECIFIC_DATE'),
        ],
        'relative',
        'switch_'.$name,
        'date_rs_selection'
    );
    $result .= '</div>';

    $result .= '<div class="date_rs_selection_choice date_rs_selection_relative grid-x grid-margin-x">
                    <div class="cell small-2">
                        <input type="number" class="relativenumber" value="0">
                    </div>
                    <div class="cell small-5">';
    $result .= acym_select(
        [
            '60' => acym_translation('ACYM_MINUTES'),
            '3600' => acym_translation('ACYM_HOUR'),
            '86400' => acym_translation('ACYM_DAY'),
        ],
        'relative_'.$name,
        null,
        'class="acym__select relativetype"'
    );

    $result .= '</div>
                <div class="cell small-5">';

    $result .= acym_select(
        [
            '-' => acym_translation('ACYM_BEFORE'),
            '+' => acym_translation('ACYM_AFTER'),
        ],
        'relativewhen_'.$name,
        $relativeDefault,
        'class="acym__select relativewhen"'
    );
    $result .= '</div>
            </div>';

    $result .= '<div class="date_rs_selection_choice date_rs_selection_specific grid-x" style="display: none;">
                    <div class="cell auto"></div>
                    <div class="cell shrink">
                        <input type="text" name="specific_'.acym_escape($name).'" class="acy_date_picker" readonly>
                    </div>
                    <div class="cell auto"></div>
                </div>
                <div class="cell grid-x grid-margin-x">
                    <div class="cell auto"></div>
                    <button type="button" class="cell medium-4 button button-secondary acym__button__clear__time" data-close>'.acym_translation('ACYM_CLEAR').'</button>
                    <button type="button" class="cell medium-4 button acym__button__set__time" data-close>'.acym_translation('ACYM_APPLY').'</button>
                    <div class="cell auto"></div>
                </div>';

    $result .= '</div>';

    $id = preg_replace('#[^a-z0-9_]#i', '', $name);
    if (is_numeric($value)) {
        $months = ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'];

        $replaceValues = [];
        foreach ($months as $oneMonth) {
            $replaceValues[] = substr(acym_translation('ACYM_'.strtoupper($oneMonth)), 0, 3);
        }

        $shownValue = str_replace($months, $replaceValues, date('d F Y H:i', $value));
    } else {
        $shownValue = $value;
    }
    $result = '<input data-rs="'.acym_escape($id).'" type="hidden" name="'.acym_escape($name).'" value="'.acym_escape($value).'">'.acym_modal(
            '<input data-open="'.acym_escape($id).'" class="rs_date_field '.$class.'" '.$attributes.' type="text" value="'.acym_escape($shownValue).'" readonly>',
            $result,
            $id,
            '',
            '',
            false,
            false
        );

    return $result;
}

function acym_escape($text, $isURL = false)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function acydump($arg, $ajax = false, $indent = true)
{
    ob_start();
    var_dump($arg);
    $result = ob_get_clean();

    if ($ajax) {
        file_put_contents(ACYM_ROOT.'acydebug.txt', $result, FILE_APPEND);
    } else {
        $style = $indent ? 'margin-left: 220px;' : '';
        echo '<pre style="'.$style.'">'.$result.'</pre>';
    }
}

function acym_line_chart($id, $dataMonth, $dataDay, $dataHour)
{
    acym_initializeChart();

    $month = [];
    $openMonth = [];
    $clickMonth = [];

    foreach ($dataMonth as $key => $data) {
        $month[] = '"'.$key.'"';
        $openMonth[] = '"'.$data['open'].'"';
        $clickMonth[] = '"'.$data['click'].'"';
    }

    $day = [];
    $openDay = [];
    $clickDay = [];

    foreach ($dataDay as $key => $data) {
        $day[] = '"'.$key.'"';
        $openDay[] = '"'.$data['open'].'"';
        $clickDay[] = '"'.$data['click'].'"';
    }

    $hour = [];
    $openHour = [];
    $clickHour = [];

    foreach ($dataHour as $key => $data) {
        $hour[] = '"'.$key.'"';
        $openHour[] = '"'.$data['open'].'"';
        $clickHour[] = '"'.$data['click'].'"';
    }

    $idCanvas = 'acy_canvas_rand_id'.rand(1000, 9000);
    $idLegend = 'acy_legend_rand_id'.rand(1000, 9000);
    $return = '';

    $nbDataDay = count($dataDay);
    $nbDataHour = count($dataHour);
    $selectedChartHour = "";
    $selectedChartDay = "";
    $selectedChartMonth = "";

    if ($nbDataHour < 49) {
        $selectedChartHour = "selected__choose_by";
        $displayed = $hour;
        $clickDisplayed = $clickHour;
        $openDisplayed = $openHour;
    } elseif ($nbDataDay < 63) {
        $selectedChartDay = "selected__choose_by";
        $displayed = $day;
        $clickDisplayed = $clickDay;
        $openDisplayed = $openDay;
    } else {
        $selectedChartMonth = "selected__choose_by";
        $displayed = $month;
        $clickDisplayed = $clickMonth;
        $openDisplayed = $openMonth;
    }


    $return .= '<div class="acym__chart__line__container" id="'.$id.'">
                    <div class="acym__chart__line__choose__by">
                        <p class="acym__chart__line__choose__by__one '.$selectedChartMonth.'" onclick="acymChartLineUpdate(this, \'month\')">'.acym_translation('ACYM_BY_MONTH').'</p>
                        <p class="acym__chart__line__choose__by__one '.$selectedChartDay.'" onclick="acymChartLineUpdate(this, \'day\')">'.acym_translation('ACYM_BY_DAY').'</p>
                        <p class="acym__chart__line__choose__by__one '.$selectedChartHour.'" onclick="acymChartLineUpdate(this, \'hour\')">'.acym_translation('ACYM_BY_HOUR').'</p>
                    </div>
                    <div class="acym__chart__line__legend" id="'.$idLegend.'"></div>
                    <canvas id="'.$idCanvas.'" height="400" width="400"></canvas>
                </div>';

    $return .= '<script>
                    var ctx = document.getElementById("'.$idCanvas.'").getContext("2d");
                    
                    var gradientBlue = ctx.createLinearGradient(0, 0, 0, 400);
                    gradientBlue.addColorStop(0, "rgba(128,182,244,0.5)"); 
                    gradientBlue.addColorStop(0.5, "rgba(128,182,244,0.25)"); 
                    gradientBlue.addColorStop(1, "rgba(128,182,244,0)"); 
                    
                    var gradientRed = ctx.createLinearGradient(0, 0, 0, 400);
                    gradientRed.addColorStop(0., "rgba(255,82,89,0.5)"); 
                    gradientRed.addColorStop(0.5, "rgba(255,82,89,0.25)"); 
                    gradientRed.addColorStop(1, "rgba(255,82,89,0)"); 
                    
                    var config = {
                        type: "line",
                        data: {
                            labels: ["'.acym_translation('ACYM_SENT').'", '.implode(',', $displayed).'],
                            datasets: [{ //We place the open before, because there are less than the clicks
                                label: "'.acym_translation('ACYM_CLICK').'",
                                data: ["0", '.implode(',', $clickDisplayed).'],
                                borderColor: "#00a4ff",
                                fill: true,
                                backgroundColor: gradientBlue,
                                borderWidth: 3,
                                pointBackgroundColor: "#ffffff",
                                pointRadius: 5,
                            },{
                                label: "'.acym_translation('ACYM_OPEN').'",
                                data: ["0", '.implode(',', $openDisplayed).'],
                                borderColor: "#ff5259",
                                fill: true,
                                backgroundColor: gradientRed,
                                borderWidth: 3,
                                pointBackgroundColor: "#ffffff",
                                pointRadius: 5,
                            },]
                        }, options: {
                            responsive: true,
                             legend: { //We make custom legends
                                display: false,
                             }, 
                            tooltips: { //on hover the dot
                                backgroundColor: "#fff",
                                borderWidth: 2,
                                borderColor: "#303e46",
                                titleFontSize: 16,
                                titleFontColor: "#303e46",
                                bodyFontColor: "#303e46",
                                bodyFontSize: 14,
                                displayColors: false
                            },
                            maintainAspectRatio: false, //to fit in the div
                            scales: {
                                yAxes: [{
                                    gridLines: {
                                        display: false
                                    },
                                    ticks: { //label on the axesY
                                        display: true,
                                        fontColor: "#0a0a0a"
                                    }
                                }],
                                xAxes: [{
                                    gridLines: {
                                        display: false
                                    },
                                    ticks: { //label on the axesX
                                        display: true,
                                        fontSize: 14,
                                        fontColor: "#0a0a0a"
                                    }
                                }],
                            },
                            legendCallback: function(chart) { //custom legends
                                var text = [];
                                for (var i = 0; i < chart.data.datasets.length; i++) {
                                  if (chart.data.datasets[i].label) {
                                    text.push(\'<div onclick="updateDataset(event, \'+ chart.legend.legendItems[i].datasetIndex + \', this)" class="acym_chart_line_labels"><i class="fa fa-circle" style="color: \' + chart.data.datasets[i].borderColor + \'"></i><span>\' + chart.data.datasets[i].label+\'</span></div>\');

                                  }
                                }
                                return text.join("");
                            },
                        }
                    };
                    var chart = new Chart(ctx, config);
                    document.getElementById("'.$idLegend.'").innerHTML = (chart.generateLegend());
                    updateDataset = function(e, datasetIndex, element) { //hide and show dataset for the custom legends
                        element = element.children[1];
                        var index = datasetIndex;
                        var ci = e.view.chart;
                        var meta = ci.getDatasetMeta(index);
                        
                        meta.hidden = meta.hidden === null? !ci.data.datasets[index].hidden : null;
                        
                        if(element.style.textDecoration == "line-through"){
                            element.style.textDecoration = "none";
                        }else{
                            element.style.textDecoration = "line-through";
                        }
                        
                        ci.update();
                    };
                    acymChartLineUpdate = function(elem, by){
                    	var chartLineLabels = document.getElementsByClassName("acym_chart_line_labels");
                    	for	(var i = 0; i < chartLineLabels.length; i++){
                    		chartLineLabels[i].getElementsByTagName("span")[0].style.textDecoration = "none";
                    	}
                        if(by == "month"){
                            var labels = ["'.acym_translation('ACYM_SENT').'", '.implode(',', $month).'];
                            var dataOpen = ["0", '.implode(',', $openMonth).'];
                            var dataClick = ["0", '.implode(',', $clickMonth).'];
                        }else if(by == "day"){
                            var labels = ["'.acym_translation('ACYM_SENT').'", '.implode(',', $day).'];
                            var dataOpen = ["0", '.implode(',', $openDay).'];
                            var dataClick = ["0", '.implode(',', $clickDay).'];
                        }else if(by == "hour"){
                            var labels = ["'.acym_translation('ACYM_SENT').'", '.implode(',', $hour).'];
                            var dataOpen = ["0", '.implode(',', $openHour).'];
                            var dataClick = ["0", '.implode(',', $clickHour).'];
                        }
                        chart.config.data.labels = labels,
                        chart.config.data.datasets = [{ //We place the open before, because there are less than the clicks
                                label: "'.acym_translation('ACYM_CLICK').'",
                                data: dataClick,
                                borderColor: "#00a4ff",
                                fill: true,
                                backgroundColor: gradientBlue,
                                borderWidth: 3,
                                pointBackgroundColor: "#ffffff",
                                pointRadius: 5,
                            },{
                                label: "'.acym_translation('ACYM_OPEN').'",
                                data: dataOpen,
                                borderColor: "#ff5259",
                                fill: true,
                                backgroundColor: gradientRed,
                                borderWidth: 3,
                                pointBackgroundColor: "#ffffff",
                                pointRadius: 5,
                            }
                        ];
                        chart.update();
                        var allChooseBy = document.getElementsByClassName("acym__chart__line__choose__by__one");
                        for(var i = 0; i < allChooseBy.length;i++){
                            allChooseBy[i].classList.remove("selected__choose_by");
                        }
                        elem.classList.add("selected__choose_by");
                    }
                </script>';

    return $return;
}

function acym_initializeChart()
{
    static $loaded = false;

    if (!$loaded) {
        acym_addScript(false, ACYM_JS.'libraries/chart.min.js?v='.filemtime(ACYM_MEDIA.'js'.DS.'libraries'.DS.'chart.min.js'), 'text/javascript', false, false, true);
        $loaded = true;
    }
}

function acym_round_chart($id, $pourcentage, $type = '', $class = '', $topLabel = '', $bottomLabel = '', $colorChart = '')
{
    if ($pourcentage != 0 && empty($pourcentage)) {
        return;
    }

    acym_initializeChart();

    if (empty($id)) {
        $id = 'acy_round_chart_rand_id'.rand(1000, 9000);
    }

    $green = '#3dea91';
    $red = '#ff5259';
    $orange = '#ffab15';
    $defaultColor = '#00a4ff';

    $isFixColor = false;
    $isInverted = false;

    switch ($type) {
        case 'click':
            $valueHigh = 5;
            $valueLow = 1;
            break;
        case 'open':
            $valueHigh = 30;
            $valueLow = 18;
            break;
        case 'delivery':
            $valueHigh = 90;
            $valueLow = 70;
            break;
        case 'fail':
            $valueHigh = 30;
            $valueLow = 10;
            $isInverted = true;
            break;
        default:
            $isFixColor = true;
    }

    if ($isFixColor) {
        $color = !empty($colorChart) ? $colorChart : $defaultColor;
    } else {
        if ($pourcentage >= $valueHigh) {
            $color = $isInverted ? $red : $green;
        } elseif ($pourcentage < $valueHigh && $pourcentage >= $valueLow) {
            $color = $orange;
        } elseif ($pourcentage < $valueLow) {
            $color = $isInverted ? $green : $red;
        } else {
            $color = $defaultColor;
        }
    }

    $idCanvas = 'acy_canvas_rand_id'.rand(1000, 9000);

    $return = '<div class="'.$class.' acym__chart__doughnut text-center">
                        <p class="text-center acym__chart__doughnut__container__top-label">'.$topLabel.'</p>
                        <div class="acym__chart__doughnut__container" id="'.$id.'">
                            <canvas id="'.$idCanvas.'" width="200" height="200"></canvas>
                        </div>
                        <p class="acym__chart__doughnut__container__bottom-label text-center">'.$bottomLabel.'</p>
                </div>';
    $return .= '<script>
            Chart.pluginService.register({
                beforeDraw: function(chart){
                    if(chart.config.options.elements.center){
                        var ctx = chart.chart.ctx;
        
                        var centerConfig = chart.config.options.elements.center;
                        var fontStyle = centerConfig.fontStyle || "Arial";
                        var txt = centerConfig.text;
                        var color = centerConfig.color || "#000";
                        ctx.textAlign = "center";
                        ctx.textBaseline = "middle";
                        var centerX = ((chart.chartArea.left + chart.chartArea.right) / 2);
                        var centerY = ((chart.chartArea.top + chart.chartArea.bottom) / 2);
                        ctx.font = "15px " + fontStyle;
                        ctx.fillStyle = color;
        
                        ctx.fillText(txt, centerX, centerY);
                    }
                }
            });
            var ctx = document.getElementById("'.$idCanvas.'").getContext("2d");
            var config = {
                type: "doughnut", data: {
                    datasets: [{
                        data: ['.$pourcentage.', (100 - '.$pourcentage.')], //Data of chart
                         backgroundColor: ["'.$color.'", "#f1f1f1"], //Two color of chart
                         borderWidth: 0 //no border
                    }]
                }, options: {
                    responsive: true,
                     legend: {
                        display: false,
                     }, 
                    elements: {
                        center: {
                            text: "'.$pourcentage.'%", color: "#363636", 
                            fontStyle: "Poppins", 
                            sidePadding: 70 
                        }
                    }, 
                    cutoutPercentage: 90, //thickness donut
                    tooltips: {
                        enabled: false //disable the tooltips on hover
                    }
                }
            };
            var chart = new Chart(ctx, config);
        </script>';


    return $return;
}

function acym_getEmailRegex($secureJS = false, $forceRegex = false)
{
    $config = acym_config();
    if ($forceRegex || $config->get('special_chars', 0) == 0) {
        $regex = '[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+(?:\.[a-z0-9!#$%&\'*+\/=?^_`{|}~-]+)*\@([a-z0-9-]+\.)+[a-z0-9]{2,20}';
    } else {
        $regex = '.+\@(.+\.)+.{2,20}';
    }

    if ($secureJS) {
        $regex = str_replace(['"', "'"], ['\"', "\'"], $regex);
    }

    return $regex;
}

function acym_isValidEmail($email, $extended = false)
{
    if (empty($email) || !is_string($email)) {
        return false;
    }

    if (!preg_match('/^'.acym_getEmailRegex().'$/i', $email)) {
        return false;
    }

    if (!$extended) {
        return true;
    }


    $config = acym_config();

    if ($config->get('email_checkdomain', false) && function_exists('getmxrr')) {
        $domain = substr($email, strrpos($email, '@') + 1);
        $mxhosts = [];
        $checkDomain = getmxrr($domain, $mxhosts);
        if (!empty($mxhosts) && strpos($mxhosts[0], 'hostnamedoesnotexist')) {
            array_shift($mxhosts);
        }
        if (!$checkDomain || empty($mxhosts)) {
            $dns = @dns_get_record($domain, DNS_A);
            $domainChanged = true;
            foreach ($dns as $oneRes) {
                if (strtolower($oneRes['host']) == strtolower($domain)) {
                    $domainChanged = false;
                }
            }
            if (empty($dns) || $domainChanged) {
                return false;
            }
        }
    }

    $object = new stdClass();
    $object->IP = acym_getIP();
    $object->emailAddress = $email;

    if ($config->get('email_iptimecheck', 0)) {
        $lapseTime = time() - 7200;
        $nbUsers = acym_loadResult('SELECT COUNT(*) FROM #__acym_user WHERE creation_date > '.intval($lapseTime).' AND ip = '.acym_escapeDB($object->IP));
        if ($nbUsers >= 3) {
            return false;
        }
    }

    return true;
}

function acym_getIP()
{
    $ip = '';
    if (!empty($_SERVER['HTTP_X_FORWARDED_FOR']) && strlen($_SERVER['HTTP_X_FORWARDED_FOR']) > 6) {
        $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
    } elseif (!empty($_SERVER['HTTP_CLIENT_IP']) && strlen($_SERVER['HTTP_CLIENT_IP']) > 6) {
        $ip = $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['REMOTE_ADDR']) && strlen($_SERVER['REMOTE_ADDR']) > 6) {
        $ip = $_SERVER['REMOTE_ADDR'];
    }

    return strip_tags($ip);
}

function acym_radio($options, $name, $selected = null, $attributes = [], $params = [])
{
    $id = preg_replace(
        '#[^a-zA-Z0-9_]+#mi',
        '_',
        str_replace(
            ['[', ']'],
            ['_', ''],
            empty($params['id']) ? $name : $params['id']
        )
    );

    $objValue = empty($params['objectValue']) ? 'value' : $params['objectValue'];
    $objText = empty($params['objectText']) ? 'text' : $params['objectText'];

    $attributes['type'] = 'radio';
    $attributes['name'] = $name;

    $return = '<div class="acym_radio_group">';
    $k = 0;
    foreach ($options as $value => $label) {
        if (is_object($label)) {
            if (!empty($label->class)) {
                $attributes['class'] = $label->class;
            }

            $value = $label->$objValue;
            $label = $label->$objText;
        }

        $currentId = empty($params['useIncrement']) ? $id.$value : $id.$k;

        $attributes['value'] = $value;
        $attributes['id'] = $currentId;

        $checked = $value == $selected ? ' checked="checked"' : '';

        $formattedAttributes = '';
        foreach ($attributes as $attribute => $val) {
            $formattedAttributes .= ' '.$attribute.'="'.acym_escape($val).'"';
        }
        if (!empty($params['required'])) {
            $formattedAttributes .= ' required';
            unset($params['required']);
        }

        $return .= '<i data-radio="'.$currentId.'" class="acymicon-radio_button_checked acym_radio_checked"></i>';
        $return .= '<i data-radio="'.$currentId.'" class="acymicon-radio_button_unchecked acym_radio_unchecked"></i>';
        $return .= '<input'.$formattedAttributes.$checked.' />';
        $return .= '<label for="'.$currentId.'" id="'.$currentId.'-lbl">'.$label.'</label>';

        if (!empty($params['pluginMode'])) $return .= '<br />';
        $k++;
    }
    $return .= '</div>';

    return $return;
}

function acym_boolean($name, $selected = null, $id = null, $attributes = [], $yes = 'ACYM_YES', $no = 'ACYM_NO')
{
    $options = [
        '1' => acym_translation($yes),
        '0' => acym_translation($no),
    ];

    return acym_radio(
        $options,
        $name,
        $selected ? 1 : 0,
        $attributes,
        ['id' => $id]
    );
}

function acym_select($data, $name, $selected = null, $attribs = null, $optKey = 'value', $optText = 'text', $idtag = false, $translate = false)
{
    $idtag = str_replace(['[', ']', ' '], '', empty($idtag) ? $name : $idtag);
    $dropdown = '<select id="'.acym_escape($idtag).'" name="'.acym_escape($name).'" '.(empty($attribs) ? '' : $attribs).'>';

    foreach ($data as $key => $oneOption) {
        $disabled = false;
        if (is_object($oneOption)) {
            $value = $oneOption->$optKey;
            $text = $oneOption->$optText;
            if (isset($oneOption->disable)) {
                $disabled = $oneOption->disable;
            }
        } else {
            $value = $key;
            $text = $oneOption;
        }

        if ($translate) {
            $text = acym_translation($text);
        }

        if (strtolower($value) == '<optgroup>') {
            $dropdown .= '<optgroup label="'.acym_escape($text).'">';
        } elseif (strtolower($value) == '</optgroup>') {
            $dropdown .= '</optgroup>';
        } else {
            $cleanValue = acym_escape($value);
            $cleanText = acym_escape($text);
            $dropdown .= '<option value="'.$cleanValue.'"'.($value == $selected ? ' selected="selected"' : '').($disabled ? ' disabled="disabled"' : '').'>'.$cleanText.'</option>';
        }
    }

    $dropdown .= '</select>';

    return $dropdown;
}

function acym_modal($button, $data, $id = null, $attributesModal = '', $attributesButton = '', $isButton = true, $isLarge = true)
{
    if (empty($id)) {
        $id = 'acymodal_'.rand(1000, 9000);
    }

    $modal = $isButton ? '<button type="button" data-open="'.$id.'" '.$attributesButton.'>'.$button.'</button>' : $button;
    $modal .= '<div class="reveal" '.($isLarge ? 'data-reveal-larger' : '').' id="'.$id.'" '.$attributesModal.' data-reveal>';
    $modal .= $data;
    $modal .= '<button class="close-button" data-close aria-label="Close reveal" type="button">';
    $modal .= '<span aria-hidden="true">&times;</span>';
    $modal .= '</button ></div>';

    return $modal;
}

function acym_modal_include($button, $file, $id, $data, $attributes = '', $classModal = "")
{
    if (empty($id)) {
        $id = 'acymodal_'.rand(1000, 9000);
    }

    $modal = '<div data-open="'.acym_escape($id).'">'.$button;
    $modal .= '<div class="reveal '.$classModal.'" id="'.acym_escape($id).'" '.$attributes.' data-reveal>';
    ob_start();
    include($file);
    $modal .= ob_get_clean();
    $modal .= '<button type="button" class="close-button" data-close aria-label="Close reveal">';
    $modal .= '<span aria-hidden="true">&times;</span>';
    $modal .= '</button></div></div>';

    return $modal;
}

function acym_modal_pagination_lists($button, $class, $textButton = null, $id = null, $attributes = '', $isModal = true, $inputEventId = "", $checkedLists = "[]", $needDisplaySubscribers = false, $attributesModal = '')
{
    $searchField = acym_filterSearch('', 'modal_search_lists', 'ACYM_SEARCH');

    $data = "";

    if (!empty($inputEventId)) {
        $data .= '<input type="hidden" id="'.$inputEventId.'">';
    }
    if ($needDisplaySubscribers) {
        $data .= '<input type="hidden" id="modal__pagination__need__display__sub">';
    }

    $data .= '<div class="cell grid-x" '.$attributesModal.'>
            <input type="hidden" name="show_selected" value="false" id="modal__pagination__show-information">
            <input type="hidden" id="modal__pagination__search__lists">
            <input type="hidden" name="lists_selected" id="acym__modal__lists-selected" value="'.acym_escape($checkedLists).'">
            <div class="cell grid-x">
                <h4 class="cell text-center acym__modal__pagination__title">'.acym_translation('ACYM_CHOOSE_LISTS').'</h4>
            </div>
            <div class="cell grid-x modal__pagination__search">
                '.$searchField.'
            </div>
            <div class="cell text-center" id="modal__pagination__search__spinner" style="display: none">
                <i class="fa fa-circle-o-notch fa-spin"></i>
            </div>
            <div class="cell medium-6 modal__pagination__show">
                <a href="#" class="acym__color__blue modal__pagination__show-selected modal__pagination__show-button selected">'.acym_translation('ACYM_SHOW_SELECTED_LISTS').'</a>
                <a href="#" class="acym__color__blue modal__pagination__show-all modal__pagination__show-button">'.acym_translation('ACYM_SHOW_ALL_LISTS').'</a>
            </div>
            <div class="cell grid-x modal__pagination__listing__lists">
                <div class="cell modal__pagination__listing__lists__in-form"></div>
            </div>
            </div>';

    if ($isModal) {
        $data .= '<div class="cell grid-x"><div class="cell medium-auto"></div><div class="cell medium-shrink"><button type="button" text-empty="'.acym_translation('ACYM_PLEASE_SELECT_LIST').'" class="button primary" id="modal__pagination__confirm">'.$textButton.'</button></div><div class="cell medium-auto"></div></div>';
        $attributesButton = 'class="modal__pagination__button-open button '.$class.'" '.$attributes;

        return acym_modal($button, $data, $id, "", $attributesButton);
    } else {
        return $data;
    }
}

function acym_generateCountryNumber($name, $defaultvalue = '')
{
    $flagPosition = [];
    $flagPosition['93'] = ['x' => -48, 'y' => 0];
    $flagPosition['355'] = ['x' => -96, 'y' => 0];
    $flagPosition['213'] = ['x' => -160, 'y' => -33];
    $flagPosition['1684'] = ['x' => -176, 'y' => 0];
    $flagPosition['376'] = ['x' => -16, 'y' => 0];
    $flagPosition['244'] = ['x' => -144, 'y' => 0];
    $flagPosition['1264'] = ['x' => -80, 'y' => 0];
    $flagPosition['672'] = ['x' => 0, 'y' => -176]; //antartica
    $flagPosition['1268'] = ['x' => -64, 'y' => 0];
    $flagPosition['54'] = ['x' => -160, 'y' => 0];
    $flagPosition['374'] = ['x' => -112, 'y' => 0];
    $flagPosition['297'] = ['x' => -224, 'y' => 0];
    $flagPosition['247'] = ['x' => -16, 'y' => -176]; //ascenscion island
    $flagPosition['61'] = ['x' => -208, 'y' => 0];
    $flagPosition['43'] = ['x' => -192, 'y' => 0];
    $flagPosition['994'] = ['x' => -240, 'y' => 0];
    $flagPosition['1242'] = ['x' => -208, 'y' => -11];
    $flagPosition['973'] = ['x' => -96, 'y' => -11];
    $flagPosition['880'] = ['x' => -32, 'y' => -11];
    $flagPosition['1246'] = ['x' => -16, 'y' => -11];
    $flagPosition['375'] = ['x' => -16, 'y' => -22];
    $flagPosition['32'] = ['x' => -48, 'y' => -11];
    $flagPosition['501'] = ['x' => -32, 'y' => -22];
    $flagPosition['229'] = ['x' => -128, 'y' => -11];
    $flagPosition['1441'] = ['x' => -144, 'y' => -11];
    $flagPosition['975'] = ['x' => -224, 'y' => -11];
    $flagPosition['591'] = ['x' => -176, 'y' => -11];
    $flagPosition['387'] = ['x' => 0, 'y' => -11];
    $flagPosition['267'] = ['x' => 0, 'y' => -22];
    $flagPosition['55'] = ['x' => -192, 'y' => -11];
    $flagPosition['1284'] = ['x' => -240, 'y' => -154];
    $flagPosition['673'] = ['x' => -160, 'y' => -11];
    $flagPosition['359'] = ['x' => -80, 'y' => -11];
    $flagPosition['226'] = ['x' => -64, 'y' => -11];
    $flagPosition['257'] = ['x' => -112, 'y' => -11];
    $flagPosition['855'] = ['x' => -64, 'y' => -77];
    $flagPosition['237'] = ['x' => -192, 'y' => -22];
    $flagPosition['1'] = ['x' => -48, 'y' => -22];
    $flagPosition['238'] = ['x' => -16, 'y' => -33];
    $flagPosition['1345'] = ['x' => -192, 'y' => -77];
    $flagPosition['236'] = ['x' => -96, 'y' => -22];
    $flagPosition['235'] = ['x' => -112, 'y' => -143];
    $flagPosition['56'] = ['x' => -176, 'y' => -22];
    $flagPosition['86'] = ['x' => -208, 'y' => -22];
    $flagPosition['6724'] = ['x' => -32, 'y' => -176]; //christmas island
    $flagPosition['6722'] = ['x' => -48, 'y' => -176]; //coco keeling island
    $flagPosition['57'] = ['x' => -224, 'y' => -22];
    $flagPosition['269'] = ['x' => -96, 'y' => -77];
    $flagPosition['243'] = ['x' => -80, 'y' => -22];
    $flagPosition['242'] = ['x' => -112, 'y' => -22];
    $flagPosition['682'] = ['x' => -160, 'y' => -22];
    $flagPosition['506'] = ['x' => -240, 'y' => -22];
    $flagPosition['225'] = ['x' => -144, 'y' => -22];
    $flagPosition['385'] = ['x' => 0, 'y' => -66];
    $flagPosition['53'] = ['x' => 0, 'y' => -33];
    $flagPosition['357'] = ['x' => -48, 'y' => -33];
    $flagPosition['420'] = ['x' => -64, 'y' => -33];
    $flagPosition['45'] = ['x' => -112, 'y' => -33];
    $flagPosition['253'] = ['x' => -96, 'y' => -33];
    $flagPosition['1767'] = ['x' => -128, 'y' => -33];
    $flagPosition['1809'] = ['x' => -144, 'y' => -33];
    $flagPosition['593'] = ['x' => -176, 'y' => -33];
    $flagPosition['20'] = ['x' => -208, 'y' => -33];
    $flagPosition['503'] = ['x' => -32, 'y' => -143];
    $flagPosition['240'] = ['x' => -96, 'y' => -55];
    $flagPosition['291'] = ['x' => 0, 'y' => -44];
    $flagPosition['372'] = ['x' => -192, 'y' => -33];
    $flagPosition['251'] = ['x' => -32, 'y' => -44];
    $flagPosition['500'] = ['x' => -96, 'y' => -44];
    $flagPosition['298'] = ['x' => -128, 'y' => -44];
    $flagPosition['679'] = ['x' => -80, 'y' => -44];
    $flagPosition['358'] = ['x' => -64, 'y' => -44];
    $flagPosition['33'] = ['x' => -144, 'y' => -44];
    $flagPosition['596'] = ['x' => -80, 'y' => -99];
    $flagPosition['594'] = ['x' => -128, 'y' => -176]; //french guiana
    $flagPosition['689'] = ['x' => -224, 'y' => -110];
    $flagPosition['241'] = ['x' => -160, 'y' => -44];
    $flagPosition['220'] = ['x' => -48, 'y' => -55];
    $flagPosition['995'] = ['x' => -208, 'y' => -44];
    $flagPosition['49'] = ['x' => -80, 'y' => -33];
    $flagPosition['233'] = ['x' => 0, 'y' => -55];
    $flagPosition['350'] = ['x' => -16, 'y' => -55];
    $flagPosition['30'] = ['x' => -112, 'y' => -55];
    $flagPosition['299'] = ['x' => -32, 'y' => -55];
    $flagPosition['1473'] = ['x' => -192, 'y' => -44];
    $flagPosition['590'] = ['x' => -80, 'y' => -55];
    $flagPosition['1671'] = ['x' => -160, 'y' => -55];
    $flagPosition['502'] = ['x' => -144, 'y' => -55];
    $flagPosition['224'] = ['x' => -64, 'y' => -55];
    $flagPosition['245'] = ['x' => -176, 'y' => -55];
    $flagPosition['592'] = ['x' => -192, 'y' => -55];
    $flagPosition['509'] = ['x' => -16, 'y' => -66];
    $flagPosition['504'] = ['x' => -240, 'y' => -55];
    $flagPosition['852'] = ['x' => -208, 'y' => -55];
    $flagPosition['36'] = ['x' => -32, 'y' => -66];
    $flagPosition['354'] = ['x' => -192, 'y' => -66];
    $flagPosition['91'] = ['x' => -128, 'y' => -66];
    $flagPosition['62'] = ['x' => -64, 'y' => -66];
    $flagPosition['964'] = ['x' => -160, 'y' => -66];
    $flagPosition['98'] = ['x' => -176, 'y' => -66];
    $flagPosition['353'] = ['x' => -80, 'y' => -66];
    $flagPosition['972'] = ['x' => -96, 'y' => -66];
    $flagPosition['39'] = ['x' => -208, 'y' => -66];
    $flagPosition['1876'] = ['x' => -240, 'y' => -66];
    $flagPosition['81'] = ['x' => -16, 'y' => -77];
    $flagPosition['962'] = ['x' => 0, 'y' => -77];
    $flagPosition['254'] = ['x' => -32, 'y' => -77];
    $flagPosition['686'] = ['x' => -80, 'y' => -77];
    $flagPosition['3774'] = ['x' => -64, 'y' => -176]; //kosovo
    $flagPosition['965'] = ['x' => -176, 'y' => -77];
    $flagPosition['996'] = ['x' => -48, 'y' => -77];
    $flagPosition['856'] = ['x' => -224, 'y' => -77];
    $flagPosition['371'] = ['x' => -112, 'y' => -88];
    $flagPosition['961'] = ['x' => -240, 'y' => -77];
    $flagPosition['266'] = ['x' => -64, 'y' => -88];
    $flagPosition['231'] = ['x' => -48, 'y' => -88];
    $flagPosition['218'] = ['x' => -128, 'y' => -88];
    $flagPosition['423'] = ['x' => -16, 'y' => -88];
    $flagPosition['370'] = ['x' => -80, 'y' => -88];
    $flagPosition['352'] = ['x' => -96, 'y' => -88];
    $flagPosition['853'] = ['x' => -48, 'y' => -99];
    $flagPosition['389'] = ['x' => -240, 'y' => -88];
    $flagPosition['261'] = ['x' => -208, 'y' => -88];
    $flagPosition['265'] = ['x' => -176, 'y' => -99];
    $flagPosition['60'] = ['x' => -208, 'y' => -99];
    $flagPosition['960'] = ['x' => -160, 'y' => -99];
    $flagPosition['223'] = ['x' => 0, 'y' => -99];
    $flagPosition['356'] = ['x' => -128, 'y' => -99];
    $flagPosition['692'] = ['x' => -224, 'y' => -88];
    $flagPosition['222'] = ['x' => -96, 'y' => -99];
    $flagPosition['230'] = ['x' => -144, 'y' => -99];
    $flagPosition['52'] = ['x' => -192, 'y' => -99];
    $flagPosition['691'] = ['x' => -112, 'y' => -44];
    $flagPosition['373'] = ['x' => -176, 'y' => -88];
    $flagPosition['377'] = ['x' => -160, 'y' => -88];
    $flagPosition['976'] = ['x' => -32, 'y' => -99];
    $flagPosition['382'] = ['x' => -192, 'y' => -88];
    $flagPosition['1664'] = ['x' => -112, 'y' => -99];
    $flagPosition['212'] = ['x' => -144, 'y' => -88];
    $flagPosition['258'] = ['x' => -224, 'y' => -99];
    $flagPosition['95'] = ['x' => -16, 'y' => -99];
    $flagPosition['264'] = ['x' => -240, 'y' => -99];
    $flagPosition['674'] = ['x' => -128, 'y' => -110];
    $flagPosition['977'] = ['x' => -112, 'y' => -110];
    $flagPosition['31'] = ['x' => -80, 'y' => -110];
    $flagPosition['599'] = ['x' => -128, 'y' => 0];
    $flagPosition['687'] = ['x' => 0, 'y' => -110];
    $flagPosition['64'] = ['x' => -160, 'y' => -110];
    $flagPosition['505'] = ['x' => -64, 'y' => -110];
    $flagPosition['227'] = ['x' => -16, 'y' => -110];
    $flagPosition['234'] = ['x' => -48, 'y' => -110];
    $flagPosition['683'] = ['x' => -144, 'y' => -110];
    $flagPosition['6723'] = ['x' => -32, 'y' => -110];
    $flagPosition['850'] = ['x' => -128, 'y' => -77];
    $flagPosition['47'] = ['x' => -96, 'y' => -110];
    $flagPosition['968'] = ['x' => -176, 'y' => -110];
    $flagPosition['92'] = ['x' => -16, 'y' => -121];
    $flagPosition['680'] = ['x' => -80, 'y' => -176]; //palau
    $flagPosition['970'] = ['x' => -96, 'y' => -121];
    $flagPosition['507'] = ['x' => -192, 'y' => -110];
    $flagPosition['675'] = ['x' => -240, 'y' => -110];
    $flagPosition['595'] = ['x' => -144, 'y' => -121];
    $flagPosition['51'] = ['x' => -208, 'y' => -110];
    $flagPosition['63'] = ['x' => 0, 'y' => -121];
    $flagPosition['48'] = ['x' => -32, 'y' => -121];
    $flagPosition['351'] = ['x' => -112, 'y' => -121];
    $flagPosition['1787'] = ['x' => -80, 'y' => -121];
    $flagPosition['974'] = ['x' => -160, 'y' => -121];
    $flagPosition['262'] = ['x' => -144, 'y' => -176]; //reunion island
    $flagPosition['40'] = ['x' => -192, 'y' => -121];
    $flagPosition['7'] = ['x' => -224, 'y' => -121];
    $flagPosition['250'] = ['x' => -240, 'y' => -121];
    $flagPosition['1670'] = ['x' => -96, 'y' => -176]; //marianne
    $flagPosition['378'] = ['x' => -176, 'y' => -132];
    $flagPosition['239'] = ['x' => -16, 'y' => -143];
    $flagPosition['966'] = ['x' => 0, 'y' => -132];
    $flagPosition['221'] = ['x' => -192, 'y' => -132];
    $flagPosition['381'] = ['x' => -208, 'y' => -121];
    $flagPosition['248'] = ['x' => -32, 'y' => -132];
    $flagPosition['232'] = ['x' => -160, 'y' => -132];
    $flagPosition['65'] = ['x' => -96, 'y' => -132];
    $flagPosition['421'] = ['x' => -144, 'y' => -132];
    $flagPosition['386'] = ['x' => -128, 'y' => -132];
    $flagPosition['677'] = ['x' => -16, 'y' => -132];
    $flagPosition['252'] = ['x' => -208, 'y' => -132];
    $flagPosition['685'] = ['x' => -112, 'y' => -176]; //somoa
    $flagPosition['27'] = ['x' => -128, 'y' => -165];
    $flagPosition['82'] = ['x' => -144, 'y' => -77];
    $flagPosition['34'] = ['x' => -16, 'y' => -44];
    $flagPosition['94'] = ['x' => -32, 'y' => -88];
    $flagPosition['290'] = ['x' => -112, 'y' => -132];
    $flagPosition['1869'] = ['x' => -112, 'y' => -77];
    $flagPosition['1758'] = ['x' => 0, 'y' => -88];
    $flagPosition['508'] = ['x' => -48, 'y' => -121];
    $flagPosition['1784'] = ['x' => -208, 'y' => -154];
    $flagPosition['249'] = ['x' => -64, 'y' => -132];
    $flagPosition['597'] = ['x' => -240, 'y' => -132];
    $flagPosition['268'] = ['x' => -80, 'y' => -143];
    $flagPosition['46'] = ['x' => -80, 'y' => -132];
    $flagPosition['41'] = ['x' => -128, 'y' => -22];
    $flagPosition['963'] = ['x' => -64, 'y' => -143];
    $flagPosition['886'] = ['x' => -64, 'y' => -154];
    $flagPosition['992'] = ['x' => -176, 'y' => -143];
    $flagPosition['255'] = ['x' => -80, 'y' => -154];
    $flagPosition['66'] = ['x' => -160, 'y' => -143];
    $flagPosition['228'] = ['x' => -144, 'y' => -143];
    $flagPosition['690'] = ['x' => -192, 'y' => -143];
    $flagPosition['676'] = ['x' => 0, 'y' => -154];
    $flagPosition['1868'] = ['x' => -32, 'y' => -154];
    $flagPosition['216'] = ['x' => -240, 'y' => -143];
    $flagPosition['90'] = ['x' => -16, 'y' => -154];
    $flagPosition['993'] = ['x' => -224, 'y' => -143];
    $flagPosition['1649'] = ['x' => -96, 'y' => -143];
    $flagPosition['688'] = ['x' => -48, 'y' => -154];
    $flagPosition['256'] = ['x' => -112, 'y' => -154];
    $flagPosition['380'] = ['x' => -96, 'y' => -154];
    $flagPosition['971'] = ['x' => -32, 'y' => 0];
    $flagPosition['44'] = ['x' => -176, 'y' => -44];
    $flagPosition['598'] = ['x' => -160, 'y' => -154];
    $flagPosition['1 '] = ['x' => -144, 'y' => -154];
    $flagPosition['998'] = ['x' => -176, 'y' => -154];
    $flagPosition['678'] = ['x' => -32, 'y' => -165];
    $flagPosition['3966'] = ['x' => -192, 'y' => -154];
    $flagPosition['58'] = ['x' => -224, 'y' => -154];
    $flagPosition['84'] = ['x' => -16, 'y' => -165];
    $flagPosition['1340'] = ['x' => 0, 'y' => -165];
    $flagPosition['681'] = ['x' => -64, 'y' => -165];
    $flagPosition['967'] = ['x' => -96, 'y' => -165];
    $flagPosition['260'] = ['x' => -160, 'y' => -165];
    $flagPosition['263'] = ['x' => -176, 'y' => -165];
    $flagPosition[''] = ['x' => -160, 'y' => -176];


    $country = [];
    $country['93'] = 'Afghanistan';
    $country['355'] = 'Albania';
    $country['213'] = 'Algeria';
    $country['1684'] = 'American Samoa';
    $country['376'] = 'Andorra';
    $country['244'] = 'Angola';
    $country['1264'] = 'Anguilla';
    $country['672'] = 'Antarctica';
    $country['1268'] = 'Antigua & Barbuda';
    $country['54'] = 'Argentina';
    $country['374'] = 'Armenia';
    $country['297'] = 'Aruba';
    $country['247'] = 'Ascension Island';
    $country['61'] = 'Australia';
    $country['43'] = 'Austria';
    $country['994'] = 'Azerbaijan';
    $country['1242'] = 'Bahamas';
    $country['973'] = 'Bahrain';
    $country['880'] = 'Bangladesh';
    $country['1246'] = 'Barbados';
    $country['375'] = 'Belarus';
    $country['32'] = 'Belgium';
    $country['501'] = 'Belize';
    $country['229'] = 'Benin';
    $country['1441'] = 'Bermuda';
    $country['975'] = 'Bhutan';
    $country['591'] = 'Bolivia';
    $country['387'] = 'Bosnia/Herzegovina';
    $country['267'] = 'Botswana';
    $country['55'] = 'Brazil';
    $country['1284'] = 'British Virgin Islands';
    $country['673'] = 'Brunei';
    $country['359'] = 'Bulgaria';
    $country['226'] = 'Burkina Faso';
    $country['257'] = 'Burundi';
    $country['855'] = 'Cambodia';
    $country['237'] = 'Cameroon';
    $country['1'] = 'Canada/USA';
    $country['238'] = 'Cape Verde Islands';
    $country['1345'] = 'Cayman Islands';
    $country['236'] = 'Central African Republic';
    $country['235'] = 'Chad Republic';
    $country['56'] = 'Chile';
    $country['86'] = 'China';
    $country['6724'] = 'Christmas Island';
    $country['6722'] = 'Cocos Keeling Island';
    $country['57'] = 'Colombia';
    $country['269'] = 'Comoros';
    $country['243'] = 'Congo Democratic Republic';
    $country['242'] = 'Congo, Republic of';
    $country['682'] = 'Cook Islands';
    $country['506'] = 'Costa Rica';
    $country['225'] = 'Cote D\'Ivoire';
    $country['385'] = 'Croatia';
    $country['53'] = 'Cuba';
    $country['357'] = 'Cyprus';
    $country['420'] = 'Czech Republic';
    $country['45'] = 'Denmark';
    $country['253'] = 'Djibouti';
    $country['1767'] = 'Dominica';
    $country['1809'] = 'Dominican Republic';
    $country['593'] = 'Ecuador';
    $country['20'] = 'Egypt';
    $country['503'] = 'El Salvador';
    $country['240'] = 'Equatorial Guinea';
    $country['291'] = 'Eritrea';
    $country['372'] = 'Estonia';
    $country['251'] = 'Ethiopia';
    $country['500'] = 'Falkland Islands';
    $country['298'] = 'Faroe Island';
    $country['679'] = 'Fiji Islands';
    $country['358'] = 'Finland';
    $country['33'] = 'France';
    $country['596'] = 'French Antilles/Martinique';
    $country['594'] = 'French Guiana';
    $country['689'] = 'French Polynesia';
    $country['241'] = 'Gabon Republic';
    $country['220'] = 'Gambia';
    $country['995'] = 'Georgia';
    $country['49'] = 'Germany';
    $country['233'] = 'Ghana';
    $country['350'] = 'Gibraltar';
    $country['30'] = 'Greece';
    $country['299'] = 'Greenland';
    $country['1473'] = 'Grenada';
    $country['590'] = 'Guadeloupe';
    $country['1671'] = 'Guam';
    $country['502'] = 'Guatemala';
    $country['224'] = 'Guinea Republic';
    $country['245'] = 'Guinea-Bissau';
    $country['592'] = 'Guyana';
    $country['509'] = 'Haiti';
    $country['504'] = 'Honduras';
    $country['852'] = 'Hong Kong';
    $country['36'] = 'Hungary';
    $country['354'] = 'Iceland';
    $country['91'] = 'India';
    $country['62'] = 'Indonesia';
    $country['964'] = 'Iraq';
    $country['98'] = 'Iran';
    $country['353'] = 'Ireland';
    $country['972'] = 'Israel';
    $country['39'] = 'Italy';
    $country['1876'] = 'Jamaica';
    $country['81'] = 'Japan';
    $country['962'] = 'Jordan';
    $country['254'] = 'Kenya';
    $country['686'] = 'Kiribati';
    $country['3774'] = 'Kosovo';
    $country['965'] = 'Kuwait';
    $country['996'] = 'Kyrgyzstan';
    $country['856'] = 'Laos';
    $country['371'] = 'Latvia';
    $country['961'] = 'Lebanon';
    $country['266'] = 'Lesotho';
    $country['231'] = 'Liberia';
    $country['218'] = 'Libya';
    $country['423'] = 'Liechtenstein';
    $country['370'] = 'Lithuania';
    $country['352'] = 'Luxembourg';
    $country['853'] = 'Macau';
    $country['389'] = 'Macedonia';
    $country['261'] = 'Madagascar';
    $country['265'] = 'Malawi';
    $country['60'] = 'Malaysia';
    $country['960'] = 'Maldives';
    $country['223'] = 'Mali Republic';
    $country['356'] = 'Malta';
    $country['692'] = 'Marshall Islands';
    $country['222'] = 'Mauritania';
    $country['230'] = 'Mauritius';
    $country['52'] = 'Mexico';
    $country['691'] = 'Micronesia';
    $country['373'] = 'Moldova';
    $country['377'] = 'Monaco';
    $country['976'] = 'Mongolia';
    $country['382'] = 'Montenegro';
    $country['1664'] = 'Montserrat';
    $country['212'] = 'Morocco';
    $country['258'] = 'Mozambique';
    $country['95'] = 'Myanmar (Burma)';
    $country['264'] = 'Namibia';
    $country['674'] = 'Nauru';
    $country['977'] = 'Nepal';
    $country['31'] = 'Netherlands';
    $country['599'] = 'Netherlands Antilles';
    $country['687'] = 'New Caledonia';
    $country['64'] = 'New Zealand';
    $country['505'] = 'Nicaragua';
    $country['227'] = 'Niger Republic';
    $country['234'] = 'Nigeria';
    $country['683'] = 'Niue Island';
    $country['6723'] = 'Norfolk';
    $country['850'] = 'North Korea';
    $country['47'] = 'Norway';
    $country['968'] = 'Oman Dem Republic';
    $country['92'] = 'Pakistan';
    $country['680'] = 'Palau Republic';
    $country['970'] = 'Palestine';
    $country['507'] = 'Panama';
    $country['675'] = 'Papua New Guinea';
    $country['595'] = 'Paraguay';
    $country['51'] = 'Peru';
    $country['63'] = 'Philippines';
    $country['48'] = 'Poland';
    $country['351'] = 'Portugal';
    $country['1787'] = 'Puerto Rico';
    $country['974'] = 'Qatar';
    $country['262'] = 'Reunion Island';
    $country['40'] = 'Romania';
    $country['7'] = 'Russia';
    $country['250'] = 'Rwanda Republic';
    $country['1670'] = 'Saipan/Mariannas';
    $country['378'] = 'San Marino';
    $country['239'] = 'Sao Tome/Principe';
    $country['966'] = 'Saudi Arabia';
    $country['221'] = 'Senegal';
    $country['381'] = 'Serbia';
    $country['248'] = 'Seychelles Island';
    $country['232'] = 'Sierra Leone';
    $country['65'] = 'Singapore';
    $country['421'] = 'Slovakia';
    $country['386'] = 'Slovenia';
    $country['677'] = 'Solomon Islands';
    $country['252'] = 'Somalia Republic';
    $country['685'] = 'Somoa';
    $country['27'] = 'South Africa';
    $country['82'] = 'South Korea';
    $country['34'] = 'Spain';
    $country['94'] = 'Sri Lanka';
    $country['290'] = 'St. Helena';
    $country['1869'] = 'St. Kitts';
    $country['1758'] = 'St. Lucia';
    $country['508'] = 'St. Pierre';
    $country['1784'] = 'St. Vincent';
    $country['249'] = 'Sudan';
    $country['597'] = 'Suriname';
    $country['268'] = 'Swaziland';
    $country['46'] = 'Sweden';
    $country['41'] = 'Switzerland';
    $country['963'] = 'Syria';
    $country['886'] = 'Taiwan';
    $country['992'] = 'Tajikistan';
    $country['255'] = 'Tanzania';
    $country['66'] = 'Thailand';
    $country['228'] = 'Togo Republic';
    $country['690'] = 'Tokelau';
    $country['676'] = 'Tonga Islands';
    $country['1868'] = 'Trinidad & Tobago';
    $country['216'] = 'Tunisia';
    $country['90'] = 'Turkey';
    $country['993'] = 'Turkmenistan';
    $country['1649'] = 'Turks & Caicos Island';
    $country['688'] = 'Tuvalu';
    $country['256'] = 'Uganda';
    $country['380'] = 'Ukraine';
    $country['971'] = 'United Arab Emirates';
    $country['44'] = 'United Kingdom';
    $country['598'] = 'Uruguay';
    $country['1 '] = 'USA/Canada';
    $country['998'] = 'Uzbekistan';
    $country['678'] = 'Vanuatu';
    $country['3966'] = 'Vatican City';
    $country['58'] = 'Venezuela';
    $country['84'] = 'Vietnam';
    $country['1340'] = 'Virgin Islands (US)';
    $country['681'] = 'Wallis/Futuna Islands';
    $country['967'] = 'Yemen Arab Republic';
    $country['260'] = 'Zambia';
    $country['263'] = 'Zimbabwe';
    $country[''] = acym_translation('ACYM_PHONE_NOCOUNTRY');

    $countryCodeForSelect = [];

    foreach ($country as $key => $one) {
        $countryCodeForSelect[$key] = $one.' +'.$key;
    }

    return acym_select($countryCodeForSelect, $name, empty($defaultvalue) ? '' : $defaultvalue, 'class="acym__select__country"', 'value', 'text');
}

function acym_displayDateFormat($format, $name = 'date', $default = '', $attributes = '')
{
    $formatForDate = explode('%', $format);
    unset($formatForDate[0]);
    $formatForDate = implode('/', $formatForDate);
    $formatForDate = str_replace('y', 'Y', $formatForDate);

    $attributes = empty($attributes) ? 'class="acym__custom__fields__select__form "' : $attributes;
    $default = empty($default) ? acym_date('now', $formatForDate) : $default;
    $return = '<div class="cell grid-x grid-margin-x">';
    $days = [];
    for ($i = 1 ; $i <= 31 ; $i++) {
        $days[$i < 10 ? '0'.$i : $i] = $i < 10 ? '0'.$i : $i;
    }
    $month = [
        '01' => acym_translation('ACYM_JANUARY'),
        '02' => acym_translation('ACYM_FEBRUARY'),
        '03' => acym_translation('ACYM_MARCH'),
        '04' => acym_translation('ACYM_APRIL'),
        '05' => acym_translation('ACYM_MAY'),
        '06' => acym_translation('ACYM_JUNE'),
        '07' => acym_translation('ACYM_JULY'),
        '08' => acym_translation('ACYM_AUGUST'),
        '09' => acym_translation('ACYM_SEPTEMBER'),
        '10' => acym_translation('ACYM_OCTOBER'),
        '11' => acym_translation('ACYM_NOVEMBER'),
        '12' => acym_translation('ACYM_DECEMBER'),
    ];
    $year = [];
    for ($i = 1900 ; $i <= (acym_date('now', 'Y') + 10) ; $i++) {
        $year[$i] = $i;
    }
    $formatToDisplay = explode('%', $format);
    $defaultDate = explode('/', $default);

    $i = 0;
    unset($formatToDisplay[0]);
    foreach ($formatToDisplay as $one) {
        if ($one == 'd') {
            $return .= '<div class="medium-3 cell">'.acym_select($days, $name, $defaultDate[$i], $attributes, 'value', 'text').'</div>';
        }
        if ($one == 'm') {
            $return .= '<div class="medium-5 cell">'.acym_select($month, $name, $defaultDate[$i], $attributes, 'value', 'text').'</div>';
        }
        if ($one == 'y') {
            $return .= '<div class="medium-4 cell">'.acym_select($year, $name, $defaultDate[$i], $attributes, 'value', 'text').'</div>';
        }
        $i++;
    }

    $return .= '</div>';

    return $return;
}

function acym_selectMultiple($data, $name, $selected = [], $attribs = [], $optValue = "value", $optText = "text", $translate = false)
{
    if (substr($name, -2) !== '[]') {
        $name .= '[]';
    }

    $attribs['multiple'] = 'multiple';

    $dropdown = '<select name="'.acym_escape($name).'"';
    foreach ($attribs as $attribKey => $attribValue) {
        $dropdown .= ' '.$attribKey.'="'.addslashes($attribValue).'"';
    }
    $dropdown .= '>';

    foreach ($data as $oneDataKey => $oneDataValue) {
        $disabled = '';

        if (is_object($oneDataValue)) {
            $value = $oneDataValue->$optValue;
            $text = $oneDataValue->$optText;

            if (!empty($oneDataValue->disable)) {
                $disabled = ' disabled="disabled"';
            }
        } else {
            $value = $oneDataKey;
            $text = $oneDataValue;
        }

        if ($translate) {
            $text = acym_translation($text);
        }

        if (strtolower($value) == '<optgroup>') {
            $dropdown .= '<optgroup label="'.acym_escape($text).'">';
        } elseif (strtolower($value) == '</optgroup>') {
            $dropdown .= '</optgroup>';
        } else {
            $text = acym_escape($text);
            $dropdown .= '<option value="'.acym_escape($value).'"'.(in_array($value, $selected) ? ' selected="selected"' : '').$disabled.'>'.$text.'</option>';
        }
    }

    $dropdown .= '</select>';

    return $dropdown;
}

function acym_selectOption($value, $text = '', $optKey = 'value', $optText = 'text', $disable = false)
{
    $option = new stdClass();
    $option->$optKey = $value;
    $option->$optText = acym_translation($text);
    $option->disable = $disable;

    return $option;
}

function acym_level($level)
{
    $config = acym_config();
    if ($config->get($config->get('level'), 0) >= $level) {
        return true;
    }

    return false;
}

function acym_getDate($time = 0, $format = '%d %B %Y %H:%M')
{
    if (empty($time)) {
        return '';
    }

    if (is_numeric($format)) {
        $format = acym_translation('ACYM_DATE_FORMAT_LC'.$format);
    }

    $format = str_replace(
        ['%A', '%d', '%B', '%m', '%Y', '%y', '%H', '%M', '%S', '%a', '%I', '%p', '%w'],
        ['l', 'd', 'F', 'm', 'Y', 'y', 'H', 'i', 's', 'D', 'h', 'a', 'w'],
        $format
    );

    try {
        return acym_date($time, $format, false);
    } catch (Exception $e) {
        return date($format, $time);
    }
}

function acym_isRobot()
{
    if (empty($_SERVER)) {
        return false;
    }
    if (!empty($_SERVER['HTTP_USER_AGENT']) && strpos(strtolower($_SERVER['HTTP_USER_AGENT']), 'spambayes') !== false) {
        return true;
    }
    if (!empty($_SERVER['REMOTE_ADDR']) && version_compare($_SERVER['REMOTE_ADDR'], '64.235.144.0', '>=') && version_compare($_SERVER['REMOTE_ADDR'], '64.235.159.255', '<=')) {
        return true;
    }

    return false;
}

function acym_loadLanguage()
{
    acym_loadLanguageFile(ACYM_LANGUAGE_FILE, ACYM_ROOT, null, true);
    acym_loadLanguageFile(ACYM_LANGUAGE_FILE.'_custom', ACYM_ROOT, null, true);
}

function acym_createDir($dir, $report = true, $secured = false)
{
    if (is_dir($dir)) return true;

    $indexhtml = '<html><body bgcolor="#FFFFFF"></body></html>';

    try {
        $status = acym_createFolder($dir);
    } catch (Exception $e) {
        $status = false;
    }

    if (!$status) {
        if ($report) {
            acym_display('Could not create the directory '.$dir, 'error');
        }

        return false;
    }

    try {
        $status = acym_writeFile($dir.DS.'index.html', $indexhtml);
    } catch (Exception $e) {
        $status = false;
    }

    if (!$status) {
        if ($report) {
            acym_display('Could not create the file '.$dir.DS.'index.html', 'error');
        }
    }

    if ($secured) {
        try {
            $htaccess = 'Order deny,allow'."\r\n".'Deny from all';
            $status = acym_writeFile($dir.DS.'.htaccess', $htaccess);
        } catch (Exception $e) {
            $status = false;
        }

        if (!$status) {
            if ($report) {
                acym_display('Could not create the file '.$dir.DS.'.htaccess', 'error');
            }
        }
    }

    return $status;
}

function acym_replaceDate($mydate, $display = false)
{
    if (strpos($mydate, '[time]') === false) {
        if (is_numeric($mydate) && $display) return acym_date($mydate, 'Y-m-d H:i:s');

        return $mydate;
    }

    if ($mydate == '[time]' && $display) return acym_translation('ACYM_NOW');

    $mydate = str_replace('[time]', time(), $mydate);
    $operators = ['+', '-'];
    foreach ($operators as $oneOperator) {
        if (strpos($mydate, $oneOperator) === false) continue;

        $dateArray = explode($oneOperator, $mydate);
        if ($oneOperator == '+') {
            if ($display) {
                $mydate = acym_translation_sprintf('ACYM_AFTER_DATE', acym_secondsToTime(intval($dateArray[1])));
            } else {
                $mydate = intval($dateArray[0]) + intval($dateArray[1]);
            }
        } elseif ($oneOperator == '-') {
            if ($display) {
                $mydate = acym_translation_sprintf('ACYM_BEFORE_DATE', acym_secondsToTime(intval($dateArray[1])));
            } else {
                $mydate = intval($dateArray[0]) - intval($dateArray[1]);
            }
        }
    }

    return $mydate;
}

function acym_secondsToTime($seconds)
{
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");

    return $dtF->diff($dtT)->format('%a day(s) %h h, %i min');
}

function acym_generateKey($length)
{
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $randstring = '';
    $max = strlen($characters) - 1;
    for ($i = 0 ; $i < $length ; $i++) {
        $randstring .= $characters[mt_rand(0, $max)];
    }

    return $randstring;
}

function acym_absoluteURL($text)
{
    static $mainurl = '';
    if (empty($mainurl)) {
        $urls = parse_url(ACYM_LIVE);
        if (!empty($urls['path'])) {
            $mainurl = substr(ACYM_LIVE, 0, strrpos(ACYM_LIVE, $urls['path'])).'/';
        } else {
            $mainurl = ACYM_LIVE;
        }
    }

    $text = str_replace(
        [
            'href="../undefined/',
            'href="../../undefined/',
            'href="../../../undefined//',
            'href="undefined/',
            ACYM_LIVE.'http://',
            ACYM_LIVE.'https://',
        ],
        [
            'href="'.$mainurl,
            'href="'.$mainurl,
            'href="'.$mainurl,
            'href="'.ACYM_LIVE,
            'http://',
            'https://',
        ],
        $text
    );
    $text = preg_replace('#href="(/?administrator)?/({|%7B)#Ui', 'href="$2', $text);

    $text = preg_replace('#href="http:/([^/])#Ui', 'href="http://$1', $text);

    $text = preg_replace(
        '#href="'.preg_quote(str_replace(['http://', 'https://'], '', $mainurl), '#').'#Ui',
        'href="'.$mainurl,
        $text
    );

    $replace = [];
    $replaceBy = [];
    if ($mainurl !== ACYM_LIVE) {

        $replace[] = '#(href|src|action|background)[ ]*=[ ]*\"(?!(\{|%7B|\[|\#|\\\\|[a-z]{3,15}:|/))(?:\.\./)#i';
        $replaceBy[] = '$1="'.substr(ACYM_LIVE, 0, strrpos(rtrim(ACYM_LIVE, '/'), '/') + 1);


        $subfolder = substr(ACYM_LIVE, strrpos(rtrim(ACYM_LIVE, '/'), '/'));
        $replace[] = '#(href|src|action|background)[ ]*=[ ]*\"'.preg_quote($subfolder, '#').'(\{|%7B)#i';
        $replaceBy[] = '$1="$2';
    }

    $replace[] = '#(href|src|action|background)[ ]*=[ ]*\"(?!(\{|%7B|\[|\#|\\\\|[a-z]{3,15}:|/))(?:\.\./|\./)?#i';
    $replaceBy[] = '$1="'.ACYM_LIVE;
    $replace[] = '#(href|src|action|background)[ ]*=[ ]*\"(?!(\{|%7B|\[|\#|\\\\|[a-z]{3,15}:))/#i';
    $replaceBy[] = '$1="'.$mainurl;

    $replace[] = '#((?:background-image|background)[ ]*:[ ]*url\((?:\'|"|&quot;)?(?!(\\\\|[a-z]{3,15}:|/|\'|"|&quot;))(?:\.\./|\./)?)#i';
    $replaceBy[] = '$1'.ACYM_LIVE;

    return preg_replace($replace, $replaceBy, $text);
}

function acym_mainURL(&$link)
{
    static $mainurl = '';
    static $otherarguments = false;
    if (empty($mainurl)) {
        $urls = parse_url(ACYM_LIVE);
        if (isset($urls['path']) && strlen($urls['path']) > 0) {
            $mainurl = substr(ACYM_LIVE, 0, strrpos(ACYM_LIVE, $urls['path'])).'/';
            $otherarguments = trim(str_replace($mainurl, '', ACYM_LIVE), '/');
            if (strlen($otherarguments) > 0) {
                $otherarguments .= '/';
            }
        } else {
            $mainurl = ACYM_LIVE;
        }
    }

    if ($otherarguments && strpos($link, $otherarguments) === false) {
        $link = $otherarguments.$link;
    }

    return $mainurl;
}

function acym_bytes($val)
{
    $val = trim($val);
    if (empty($val)) {
        return 0;
    }
    $last = strtolower($val[strlen($val) - 1]);
    switch ($last) {
        case 'g':
            $val = intval($val) * 1073741824;
            break;
        case 'm':
            $val = intval($val) * 1048576;
            break;
        case 'k':
            $val = intval($val) * 1024;
            break;
    }

    return (int)$val;
}

function acym_getTables()
{
    return acym_loadResultArray('SHOW TABLES');
}

function acym_getColumns($table, $acyTable = true, $putPrefix = true)
{
    if ($putPrefix) {
        $prefix = $acyTable ? '#__acym_' : '#__';
        $table = $prefix.$table;
    }

    return acym_loadResultArray('SHOW COLUMNS FROM '.acym_secureDBColumn($table));
}

function acym_display($messages, $type = 'success', $close = true)
{
    if (empty($messages)) return;
    if (!is_array($messages)) $messages = [$messages];

    foreach ($messages as $id => $message) {
        echo '<div class="acym__message grid-x acym__message__'.$type.'">';

        if (is_array($message)) $message = implode('</p><p>', $message);

        echo '<div class="cell auto"><p>'.$message.'</p></div>';

        if ($close) {
            echo '<i data-id="'.acym_escape($id).'" class="cell shrink acym__message__close fa fa-close"></i>';
        }
        echo '</div>';
    }
}

function acym_secureDBColumn($fieldName)
{
    if (!is_string($fieldName) || preg_match('|[^a-z0-9#_.-]|i', $fieldName) !== 0) {
        die('field, table or database "'.acym_escape($fieldName).'" not secured');
    }

    return $fieldName;
}

function acym_displayErrors()
{
    error_reporting(E_ALL);
    @ini_set("display_errors", 1);
}

function acym_increasePerf()
{
    @ini_set('max_execution_time', 600);
    @ini_set('pcre.backtrack_limit', 1000000);
}

function acym_config($reload = false)
{
    static $configClass = null;
    if ($configClass === null || $reload) {
        $configClass = acym_get('class.configuration');
        $configClass->load();
    }

    return $configClass;
}

function acym_getModuleFormName()
{
    static $i = 1;

    return 'formAcym'.rand(1000, 9999).$i++;
}

function acym_initModule($params = null)
{
    static $loaded = false;
    if ($loaded) {
        return;
    }
    $loaded = true;

    if (method_exists($params, 'get')) {
        $nameCaption = $params->get('nametext');
        $emailCaption = $params->get('emailtext');
    }

    if (empty($nameCaption)) {
        $nameCaption = acym_translation('ACYM_NAME');
    }
    if (empty($emailCaption)) {
        $emailCaption = acym_translation('ACYM_EMAIL');
    }

    $js = " var acymModule = [];
			acymModule['emailRegex'] = /^".acym_getEmailRegex(true)."$/i;
			acymModule['NAMECAPTION'] = '".str_replace("'", "\'", $nameCaption)."';
			acymModule['NAME_MISSING'] = '".str_replace("'", "\'", acym_translation('ACYM_MISSING_NAME'))."';
			acymModule['EMAILCAPTION'] = '".str_replace("'", "\'", $emailCaption)."';
			acymModule['VALID_EMAIL'] = '".str_replace("'", "\'", acym_translation('ACYM_VALID_EMAIL'))."';
			acymModule['CAPTCHA_MISSING'] = '".str_replace("'", "\'", acym_translation('ACYM_WRONG_CAPTCHA'))."';
			acymModule['NO_LIST_SELECTED'] = '".str_replace("'", "\'", acym_translation('ACYM_SELECT_LIST'))."';
			acymModule['ACCEPT_TERMS'] = '".str_replace("'", "\'", acym_translation('ACYM_ACCEPT_TERMS'))."';
		";

    $config = acym_config();
    $version = str_replace('.', '', $config->get('version'));

    $scriptName = acym_addScript(false, ACYM_JS.'module.min.js?v='.$version);
    acym_addScript(true, $js, 'text/javascript', false, false, false, ['script_name' => $scriptName]);

    if ('wordpress' === ACYM_CMS) {
        wp_enqueue_style('style_acymailing_module', ACYM_CSS.'module.min.css?v='.$version);
    } else {
        acym_addStyle(false, ACYM_CSS.'module.min.css?v='.$version);
    }
}

function acym_get($path)
{
    list($group, $class) = explode('.', $path);

    $className = $class.ucfirst(str_replace('_front', '', $group));
    if ($group == 'helper' && strpos($className, 'acym') !== 0) {
        $className = 'acym'.$className;
    }
    if ($group == 'class') {
        $className = 'acym'.$className;
    }

    if (substr($group, 0, 4) == 'view') {
        $className = $className.ucfirst($class);
        $class .= DS.'view.html';
    }

    if (!class_exists($className)) {
        include(constant(strtoupper('ACYM_'.$group)).$class.'.php');
    }

    if (!class_exists($className)) {
        return null;
    }

    return new $className();
}

function acym_getCID($field = '')
{
    $oneResult = acym_getVar('array', 'cid', [], '');
    $oneResult = intval(reset($oneResult));
    if (!empty($oneResult) || empty($field)) {
        return $oneResult;
    }

    $oneResult = acym_getVar('int', $field, 0, '');

    return intval($oneResult);
}

function acym_checkRobots()
{
    if (preg_match('#(libwww-perl|python|googlebot)#i', @$_SERVER['HTTP_USER_AGENT'])) {
        die('Not allowed for robots. Please contact us if you are not a robot');
    }
}

function acym_importFile($file, $uploadPath, $onlyPict, $maxwidth = '')
{
    acym_checkToken();

    $config = acym_config();
    $additionalMsg = '';

    if ($file["error"] > 0) {
        $file["error"] = intval($file["error"]);
        if ($file["error"] > 8) {
            $file["error"] = 0;
        }

        $phpFileUploadErrors = [
            0 => 'Unknown error',
            1 => 'The uploaded file exceeds the upload_max_filesize directive in php.ini',
            2 => 'The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form',
            3 => 'The uploaded file was only partially uploaded',
            4 => 'No file was uploaded',
            6 => 'Missing a temporary folder',
            7 => 'Failed to write file to disk',
            8 => 'A PHP extension stopped the file upload',
        ];

        acym_enqueueMessage(acym_translation_sprintf('ACYM_ERROR_UPLOADING_FILE_X', $phpFileUploadErrors[$file["error"]]), 'error');

        return false;
    }

    acym_createDir($uploadPath, true);

    if (!is_writable($uploadPath)) {
        @chmod($uploadPath, '0755');
        if (!is_writable($uploadPath)) {
            acym_display(acym_translation_sprintf('ACYM_WRITABLE_FOLDER', $uploadPath), 'error');

            return false;
        }
    }

    if ($onlyPict) {
        $allowedExtensions = ['png', 'jpeg', 'jpg', 'gif', 'ico', 'bmp'];
    } else {
        $allowedExtensions = explode(',', $config->get('allowed_files'));
    }

    if (!preg_match('#\.('.implode('|', $allowedExtensions).')$#Ui', $file["name"], $extension)) {
        $ext = substr($file["name"], strrpos($file["name"], '.') + 1);
        acym_display(
            acym_translation_sprintf(
                'ACYM_ACCEPTED_TYPE',
                acym_escape($ext),
                implode(', ', $allowedExtensions)
            ),
            'error'
        );

        return false;
    }

    if (preg_match('#\.(php.?|.?htm.?|pl|py|jsp|asp|sh|cgi)#Ui', $file["name"])) {
        acym_display(
            'This extension name is blocked by the system regardless your configuration for security reasons',
            'error'
        );

        return false;
    }

    $file["name"] = preg_replace(
            '#[^a-z0-9]#i',
            '_',
            strtolower(substr($file["name"], 0, strrpos($file["name"], '.')))
        ).'.'.$extension[1];

    if ($onlyPict) {
        $imageSize = getimagesize($file['tmp_name']);
        if (empty($imageSize)) {
            acym_display('Invalid image', 'error');

            return false;
        }
    }

    if (file_exists($uploadPath.DS.$file["name"])) {
        $i = 1;
        $nameFile = preg_replace("/\\.[^.\\s]{3,4}$/", "", $file["name"]);
        $ext = substr($file["name"], strrpos($file["name"], '.') + 1);
        while (file_exists($uploadPath.DS.$nameFile.'_'.$i.'.'.$ext)) {
            $i++;
        }

        $file["name"] = $nameFile.'_'.$i.'.'.$ext;
        $additionalMsg = '<br />'.acym_translation_sprintf('ACYM_FILE_RENAMED', $file["name"]);
        if ($onlyPict) {
            $additionalMsg .= '<br /><a style="color: blue; cursor: pointer;" onclick="confirmBox(\'rename\', \''.$file['name'].'\', \''.$nameFile.'.'.$ext.'\')">'.acym_translation(
                    'ACYM_RENAME_OR_REPLACE'
                ).'</a>';
        }
    }

    if (!acym_uploadFile($file["tmp_name"], rtrim($uploadPath, DS).DS.$file["name"])) {
        if (!move_uploaded_file($file["tmp_name"], rtrim($uploadPath, DS).DS.$file["name"])) {
            acym_display(
                acym_translation_sprintf(
                    'ACYM_FAIL_UPLOAD',
                    '<b><i>'.acym_escape($file["tmp_name"]).'</i></b>',
                    '<b><i>'.acym_escape(rtrim($uploadPath, DS).DS.$file["name"]).'</i></b>'
                ),
                'error'
            );

            return false;
        }
    }

    if (!empty($maxwidth) || ($onlyPict && $imageSize[0] > 1000)) {
        $imageHelper = acym_get('helper.image');
        if ($imageHelper->available()) {
            $imageHelper->maxHeight = 9999;
            if (empty($maxwidth)) {
                $imageHelper->maxWidth = 700;
            } else {
                $imageHelper->maxWidth = $maxwidth;
            }
            $message = 'ACYM_IMAGE_RESIZED';
            $imageHelper->destination = $uploadPath;
            $thumb = $imageHelper->generateThumbnail(rtrim($uploadPath, DS).DS.$file["name"], $file["name"]);
            $resize = acym_moveFile($thumb['file'], $uploadPath.DS.$file["name"]);
            if ($thumb) {
                $additionalMsg .= '<br />'.acym_translation($message);
            }
        }
    }
    acym_enqueueMessage(acym_translation('ACYM_SUCCESS_FILE_UPLOAD').$additionalMsg, 'success');

    return $file["name"];
}

function acym_inputFile($name, $value = '', $id = '', $class = '', $attributes = '')
{
    $return = '<div class="cell acym__input__file '.$class.' grid-x"><input '.$attributes.' style="display: none" id="'.$id.'" type="file" name="'.$name.'"><button type="button" class="smaller-button acym__button__file button button-secondary cell shrink">'.acym_translation('ACYM_CHOOSE_FILE').'</button><span class="cell shrink margin-left-2">';
    $return .= empty($value) ? acym_translation('ACYM_NO_FILE_CHOSEN') : $value;
    $return .= '</span></div>';

    return $return;
}

function acym_getFilesFolder($folder = 'upload', $multipleFolders = false)
{
    $listClass = acym_get('class.list');
    if (acym_isAdmin()) {
        $allLists = $listClass->getAll();
    } else {
        $allLists = $listClass->getAll();
    }
    $newFolders = [];

    $config = acym_config();
    if ($folder == 'upload') {
        $uploadFolder = $config->get('uploadfolder', ACYM_UPLOAD_FOLDER);
    } else {
        $uploadFolder = $config->get('mediafolder', ACYM_UPLOAD_FOLDER);
    }

    $folders = explode(',', $uploadFolder);

    foreach ($folders as $k => $folder) {
        $folders[$k] = trim($folder, '/');
        if (strpos($folder, '{userid}') !== false) {
            $folders[$k] = str_replace('{userid}', acym_currentUserId(), $folders[$k]);
        }

        if (strpos($folder, '{listalias}') !== false) {
            if (empty($allLists)) {
                $noList = new stdClass();
                $noList->alias = 'none';
                $allLists = [$noList];
            }

            foreach ($allLists as $oneList) {
                $newFolders[] = str_replace(
                    '{listalias}',
                    strtolower(str_replace([' ', '-'], '_', $oneList->alias)),
                    $folders[$k]
                );
            }

            $folders[$k] = '';
            continue;
        }

        if (strpos($folder, '{groupid}') !== false || strpos($folder, '{groupname}') !== false) {
            $groups = acym_getGroupsByUser(acym_currentUserId(), false);
            acym_arrayToInteger($groups);
            if (empty($groups)) {
                $groups[] = 0;
            }

            $completeGroups = acym_loadObjectList('SELECT id, title FROM #__usergroups WHERE id IN ('.implode(',', $groups).')');

            foreach ($completeGroups as $group) {
                $newFolders[] = str_replace(
                    ['{groupid}', '{groupname}'],
                    [$group->id, strtolower(str_replace(' ', '_', $group->title))],
                    $folders[$k]
                );
            }

            $folders[$k] = '';
        }
    }

    $folders = array_merge($folders, $newFolders);
    $folders = array_filter($folders);
    sort($folders);
    if ($multipleFolders) {
        return $folders;
    } else {
        return array_shift($folders);
    }
}

function acym_generateArborescence($folders)
{
    $folderList = [];
    foreach ($folders as $folder) {
        $folderPath = acym_cleanPath(ACYM_ROOT.trim(str_replace('/', DS, trim($folder)), DS));
        if (!file_exists($folderPath)) {
            acym_createDir($folderPath);
        }
        $subFolders = acym_listFolderTree($folderPath, '', 15);
        $folderList[$folder] = [];
        foreach ($subFolders as $oneFolder) {
            $subFolder = str_replace(ACYM_ROOT, '', $oneFolder['relname']);
            $subFolder = str_replace(DS, '/', $subFolder);
            $folderList[$folder][$subFolder] = ltrim($subFolder, '/');
        }
        $folderList[$folder] = array_unique($folderList[$folder]);
    }

    return $folderList;
}

function acym_arrayToInteger(&$array)
{
    if (is_array($array)) {
        $array = array_map('intval', $array);
    } else {
        $array = [];
    }
}

function acym_makeSafeFile($file)
{
    $file = rtrim($file, '.');
    $regex = ['#(\.){2,}#', '#[^A-Za-z0-9\.\_\- ]#', '#^\.#'];

    return trim(preg_replace($regex, '', $file));
}

function acym_tooltip($text, $tooltipText, $classContainer = '', $title = '', $link = '')
{
    if (!empty($link)) {
        $text = '<a href="'.$link.'" title="'.acym_escape($title).'">'.$text.'</a>';
    }

    if (!empty($title)) {
        $title = '<span class="acym__tooltip__title">'.$title.'</span>';
    }

    return '<span class="acym__tooltip '.$classContainer.'"><span class="acym__tooltip__text">'.$title.$tooltipText.'</span>'.$text.'</span>';
}

function acym_info($tooltipText)
{
    return acym_tooltip('<span class="acym__tooltip__info__container"><i class="acym__tooltip__info__icon fa fa-info-circle"></i></span>', $tooltipText, 'acym__tooltip__info');
}

function acym_deleteFolder($path)
{
    $path = acym_cleanPath($path);
    if (!is_dir($path)) {
        acym_enqueueMessage(acym_translation_sprintf('ACYM_IS_NOT_A_FOLDER', $path), 'error');

        return false;
    }
    $files = acym_getFiles($path);
    if (!empty($files)) {
        foreach ($files as $oneFile) {
            if (!acym_deleteFile($path.DS.$oneFile)) {
                return false;
            }
        }
    }

    $folders = acym_getFolders($path, '.', false, false, []);
    if (!empty($folders)) {
        foreach ($folders as $oneFolder) {
            if (!acym_deleteFolder($path.DS.$oneFolder)) {
                return false;
            }
        }
    }

    if (@rmdir($path)) {
        $ret = true;
    } else {
        acym_enqueueMessage(acym_translation_sprintf('ACYM_COULD_NOT_DELETE_FOLDER', $path), 'error');
        $ret = false;
    }

    return $ret;
}

function acym_createFolder($path = '', $mode = 0755)
{
    $path = acym_cleanPath($path);
    if (file_exists($path)) {
        return true;
    }

    $origmask = @umask(0);
    $ret = @mkdir($path, $mode, true);
    @umask($origmask);

    return $ret;
}

function acym_getFolders($path, $filter = '.', $recurse = false, $full = false, $exclude = ['.svn', 'CVS', '.DS_Store', '__MACOSX'], $excludefilter = ['^\..*'])
{
    $path = acym_cleanPath($path);

    if (!is_dir($path)) {
        acym_enqueueMessage(acym_translation_sprintf('ACYM_IS_NOT_A_FOLDER', $path), 'error');

        return [];
    }

    if (count($excludefilter)) {
        $excludefilter_string = '/('.implode('|', $excludefilter).')/';
    } else {
        $excludefilter_string = '';
    }

    $arr = acym_getItems($path, $filter, $recurse, $full, $exclude, $excludefilter_string, false);
    asort($arr);

    return array_values($arr);
}

function acym_getFiles($path, $filter = '.', $recurse = false, $full = false, $exclude = ['.svn', 'CVS', '.DS_Store', '__MACOSX'], $excludefilter = ['^\..*', '.*~'], $naturalSort = false)
{
    $path = acym_cleanPath($path);

    if (!is_dir($path)) {
        acym_enqueueMessage(acym_translation_sprintf('ACYM_IS_NOT_A_FOLDER', $path), 'error');

        return false;
    }

    if (count($excludefilter)) {
        $excludefilter_string = '/('.implode('|', $excludefilter).')/';
    } else {
        $excludefilter_string = '';
    }

    $arr = acym_getItems($path, $filter, $recurse, $full, $exclude, $excludefilter_string, true);

    if ($naturalSort) {
        natsort($arr);
    } else {
        asort($arr);
    }

    return array_values($arr);
}

function acym_getItems($path, $filter, $recurse, $full, $exclude, $excludefilter_string, $findfiles)
{
    $arr = [];

    if (!($handle = @opendir($path))) {
        return $arr;
    }

    while (($file = readdir($handle)) !== false) {
        if ($file == '.' || $file == '..' || in_array($file, $exclude) || (!empty($excludefilter_string) && preg_match(
                    $excludefilter_string,
                    $file
                ))) {
            continue;
        }
        $fullpath = $path.'/'.$file;

        $isDir = is_dir($fullpath);

        if (($isDir xor $findfiles) && preg_match("/$filter/", $file)) {
            if ($full) {
                $arr[] = $fullpath;
            } else {
                $arr[] = $file;
            }
        }

        if ($isDir && $recurse) {
            if (is_int($recurse)) {
                $arr = array_merge(
                    $arr,
                    acym_getItems(
                        $fullpath,
                        $filter,
                        $recurse - 1,
                        $full,
                        $exclude,
                        $excludefilter_string,
                        $findfiles
                    )
                );
            } else {
                $arr = array_merge(
                    $arr,
                    acym_getItems(
                        $fullpath,
                        $filter,
                        $recurse,
                        $full,
                        $exclude,
                        $excludefilter_string,
                        $findfiles
                    )
                );
            }
        }
    }

    closedir($handle);

    return $arr;
}

function acym_copyFolder($src, $dest, $path = '', $force = false, $use_streams = false)
{

    if ($path) {
        $src = acym_cleanPath($path.'/'.$src);
        $dest = acym_cleanPath($path.'/'.$dest);
    }

    $src = rtrim($src, DIRECTORY_SEPARATOR);
    $dest = rtrim($dest, DIRECTORY_SEPARATOR);

    if (!file_exists($src)) {
        acym_enqueueMessage(acym_translation_sprintf('ACYM_FOLDER_DOES_NOT_EXIST', $src), 'error');

        return false;
    }

    if (file_exists($dest) && !$force) {
        acym_enqueueMessage(acym_translation_sprintf('ACYM_FOLDER_ALREADY_EXIST', $dest), 'error');

        return true;
    }

    if (!acym_createFolder($dest)) {
        acym_enqueueMessage(acym_translation('ACYM_CANNOT_CREATE_DESTINATION_FOLDER'), 'error');

        return false;
    }

    if (!($dh = @opendir($src))) {
        acym_enqueueMessage(acym_translation('ACYM_CANNOT_OPEN_SOURCE_FOLDER'), 'error');

        return false;
    }

    while (($file = readdir($dh)) !== false) {
        $sfid = $src.'/'.$file;
        $dfid = $dest.'/'.$file;

        switch (filetype($sfid)) {
            case 'dir':
                if ($file != '.' && $file != '..') {
                    $ret = acym_copyFolder($sfid, $dfid, null, $force, $use_streams);

                    if ($ret !== true) {
                        return $ret;
                    }
                }
                break;

            case 'file':
                if (!@copy($sfid, $dfid)) {
                    acym_enqueueMessage(acym_translation_sprintf('ACYM_COPY_FILE_FAILED_PERMISSION', $sfid), 'error');

                    return false;
                }
                break;
        }
    }

    return true;
}

function acym_listFolderTree($path, $filter, $maxLevel = 3, $level = 0, $parent = 0)
{
    $dirs = [];

    if ($level == 0) {
        $GLOBALS['acym_folder_tree_index'] = 0;
    }

    if ($level < $maxLevel) {
        $folders = acym_getFolders($path, $filter);

        foreach ($folders as $name) {
            $id = ++$GLOBALS['acym_folder_tree_index'];
            $fullName = acym_cleanPath($path.'/'.$name);
            $dirs[] = [
                'id' => $id,
                'parent' => $parent,
                'name' => $name,
                'fullname' => $fullName,
                'relname' => str_replace(ACYM_ROOT, '', $fullName),
            ];
            $dirs2 = acym_listFolderTree($fullName, $filter, $maxLevel, $level + 1, $id);
            $dirs = array_merge($dirs, $dirs2);
        }
    }

    return $dirs;
}

function acym_deleteFile($file)
{
    $file = acym_cleanPath($file);
    if (!is_file($file)) {
        acym_enqueueMessage(acym_translation_sprintf('ACYM_IS_NOT_A_FILE', $file), 'error');

        return false;
    }

    @chmod($file, 0777);

    if (!@unlink($file)) {
        $filename = basename($file);
        acym_enqueueMessage(acym_translation_sprintf('ACYM_FAILED_DELETE', $filename), 'error');

        return false;
    }

    return true;
}

function acym_writeFile($file, $buffer, $use_streams = false)
{
    if (!file_exists(dirname($file)) && acym_createFolder(dirname($file)) == false) {
        return false;
    }

    $file = acym_cleanPath($file);
    $ret = is_int(file_put_contents($file, $buffer));

    return $ret;
}

function acym_moveFile($src, $dest, $path = '', $use_streams = false)
{
    if (!empty($path)) {
        $src = acym_cleanPath($path.'/'.$src);
        $dest = acym_cleanPath($path.'/'.$dest);
    }

    if (!is_readable($src)) {
        acym_enqueueMessage(acym_translation_sprintf('ACYM_COULD_NOT_FIND_FILE_SOURCE_PERMISSION', $src), 'error');

        return false;
    }

    if (!@rename($src, $dest)) {
        acym_enqueueMessage(acym_translation('ACYM_COULD_NOT_MOVE_FILE'), 'error');

        return false;
    }

    return true;
}

function acym_uploadFile($src, $dest)
{
    $dest = acym_cleanPath($dest);

    $baseDir = dirname($dest);
    if (!file_exists($baseDir)) {
        acym_createFolder($baseDir);
    }

    if (is_writeable($baseDir) && move_uploaded_file($src, $dest)) {
        if (@chmod($dest, octdec('0644'))) {
            return true;
        } else {
            acym_enqueueMessage(acym_translation('ACYM_FILE_REJECTED_SAFETY_REASON'), 'error');
        }
    } else {
        acym_enqueueMessage(acym_translation_sprintf('ACYM_COULD_NOT_UPLOAD_FILE_PERMISSION', $baseDir), 'error');
    }

    return false;
}

function acym_copyFile($src, $dest, $path = null, $use_streams = false)
{
    if ($path) {
        $src = acym_cleanPath($path.'/'.$src);
        $dest = acym_cleanPath($path.'/'.$dest);
    }

    if (!is_readable($src)) {
        acym_enqueueMessage(acym_translation_sprintf('ACYM_COULD_NOT_FIND_FILE_SOURCE_PERMISSION', $src), 'error');

        return false;
    }

    if (!@copy($src, $dest)) {
        acym_enqueueMessage(acym_translation_sprintf('ACYM_COULD_NOT_COPY_FILE_X_TO_X', $src, $dest), 'error');

        return false;
    }

    return true;
}

function acym_fileGetExt($file)
{
    $endPos = strpos($file, '?');
    if (false !== $endPos) {
        $file = substr($file, 0, $endPos);
    }

    $dot = strrpos($file, '.');
    if (false === $dot) return '';
    $extension = substr($file, $dot + 1);

    return $extension;
}

function acym_cleanPath($path, $ds = DIRECTORY_SEPARATOR)
{
    $path = trim($path);

    if (empty($path)) {
        $path = ACYM_ROOT;
    } elseif (($ds == '\\') && substr($path, 0, 2) == '\\\\') {
        $path = "\\".preg_replace('#[/\\\\]+#', $ds, $path);
    } else {
        $path = preg_replace('#[/\\\\]+#', $ds, $path);
    }

    return $path;
}

function acym_createArchive($name, $files)
{
    $contents = [];
    $ctrldir = [];

    $timearray = getdate();
    $dostime = (($timearray['year'] - 1980) << 25) | ($timearray['mon'] << 21) | ($timearray['mday'] << 16) | ($timearray['hours'] << 11) | ($timearray['minutes'] << 5) | ($timearray['seconds'] >> 1);
    $dtime = dechex($dostime);
    $hexdtime = chr(hexdec($dtime[6].$dtime[7])).chr(hexdec($dtime[4].$dtime[5])).chr(hexdec($dtime[2].$dtime[3])).chr(
            hexdec($dtime[0].$dtime[1])
        );

    foreach ($files as $file) {
        $data = $file['data'];
        $filename = str_replace('\\', '/', $file['name']);

        $fr = "\x50\x4b\x03\x04\x14\x00\x00\x00\x08\x00".$hexdtime;

        $unc_len = strlen($data);
        $crc = crc32($data);
        $zdata = gzcompress($data);
        $zdata = substr(substr($zdata, 0, strlen($zdata) - 4), 2);
        $c_len = strlen($zdata);

        $fr .= pack('V', $crc).pack('V', $c_len).pack('V', $unc_len).pack('v', strlen($filename)).pack(
                'v',
                0
            ).$filename.$zdata;

        $old_offset = strlen(implode('', $contents));
        $contents[] = $fr;

        $cdrec = "\x50\x4b\x01\x02\x00\x00\x14\x00\x00\x00\x08\x00".$hexdtime;
        $cdrec .= pack('V', $crc).pack('V', $c_len).pack('V', $unc_len).pack('v', strlen($filename)).pack('v', 0).pack(
                'v',
                0
            ).pack('v', 0).pack('v', 0).pack('V', 32).pack('V', $old_offset).$filename;

        $ctrldir[] = $cdrec;
    }

    $data = implode('', $contents);
    $dir = implode('', $ctrldir);
    $buffer = $data.$dir."\x50\x4b\x05\x06\x00\x00\x00\x00".pack('v', count($ctrldir)).pack('v', count($ctrldir)).pack(
            'V',
            strlen($dir)
        ).pack('V', strlen($data))."\x00\x00";

    return acym_writeFile($name.'.zip', $buffer);
}

function acym_currentURL()
{
    $url = isset($_SERVER['HTTPS']) ? 'https' : 'http';
    $url .= '://'.$_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];

    return $url;
}

function acym_session()
{
    $sessionID = session_id();
    if (empty($sessionID)) {
        @session_start();
    }
}

function acym_listingActions($actions)
{
    $defaultAction = new stdClass();
    $defaultAction->value = 0;
    $defaultAction->text = acym_translation('ACYM_CHOOSE_ACTION');
    $defaultAction->disable = true;

    array_unshift($actions, $defaultAction);

    return acym_select($actions, '', null, 'class="medium-shrink cell margin-right-1"', 'value', 'text', 'listing_actions');
}

function acym_switchFilter($switchOptions, $selected, $name, $addClass = '')
{
    $return = '<input type="hidden" id="acym__type-template" name="'.$name.'" value="'.$selected.'">';
    foreach ($switchOptions as $value => $text) {
        $class = 'button hollow acym__type__choosen cell small-6 xlarge-auto large-shrink';
        if ($value == $selected) {
            $class .= ' is-active';
        }
        $class .= ' '.$addClass;
        $return .= '<button class="'.acym_escape($class).'" type="button" data-type="'.acym_escape($value).'">'.acym_translation($text).'</button>';
    }

    return $return;
}

function acym_filterStatus($options, $selected, $name)
{
    $filterStatus = '<input type="hidden" id="acym_filter_status" name="'.acym_escape($name).'" value="'.acym_escape($selected).'"/>';

    foreach ($options as $value => $text) {
        $class = 'acym__filter__status clear button secondary';
        if ($value == $selected) {
            $class .= ' font-bold acym__status__select';
        }
        $disabled = empty($text[1]) ? ' disabled' : '';
        $extraIcon = '';
        if (!empty($text[2]) && 'pending' == $text[2]) {
            $extraIcon = ' <i class="fa fa-exclamation-triangle acym__color__orange" style="font-size: 15px;"></i>';
        }
        $filterStatus .= '<button type="button" status="'.acym_escape($value).'" class="'.acym_escape($class).'"'.$disabled.'>'.acym_translation($text[0]).$extraIcon.' ('.$text[1].')</button>';
    }

    return $filterStatus;
}

function acym_filterSearch($search, $name, $placeholder = 'ACYM_SEARCH', $showClearBtn = true)
{
    $searchField = '<div class="input-group acym__search-area">
        <div class="input-group-button">
            <button class="button acym__search__button hide-for-small-only"><i class="acymicon-search"></i></button>
        </div>
        <input class="input-group-field acym__search-field" type="text" name="'.acym_escape($name).'" placeholder="'.acym_escape(acym_translation($placeholder)).'" value="'.acym_escape($search).'">';
    if ($showClearBtn) {
        $searchField .= '<span class="acym__search-clear"><i class="fa fa-close"></i></span>';
    }
    $searchField .= '</div>';

    return $searchField;
}

function acym_switch($name, $value, $label = null, $attrInput = [], $labelClass = 'medium-6 small-9', $switchContainerClass = 'auto', $switchClass = 'tiny', $toggle = null, $toggleOpen = true)
{
    static $occurrence = 100;
    $occurrence++;

    $id = acym_escape('switch_'.$occurrence);
    $checked = $value == 1 ? 'checked="checked"' : '';

    $switch = '
    <div class="switch '.acym_escape($switchClass).'">
        <input type="hidden" name="'.acym_escape($name).'" data-switch="'.$id.'" value="'.acym_escape($value).'"';

    if (!empty($toggle)) {
        $switch .= ' data-toggle-switch="'.acym_escape($toggle).'" data-toggle-switch-open="'.($toggleOpen ? 'show' : 'hide').'"';
    }

    foreach ($attrInput as $oneAttributeName => $oneAttributeValue) {
        $switch .= ' '.$oneAttributeName.'="'.acym_escape($oneAttributeValue).'"';
    }
    $switch .= '>';
    $switch .= '
        <input class="switch-input" type="checkbox" id="'.$id.'" value="1" '.$checked.'>
        <label class="switch-paddle switch-label" for="'.$id.'">
            <span class="switch-active" aria-hidden="true">1</span>
            <span class="switch-inactive" aria-hidden="true">0</span>
        </label>
    </div>';

    if (!empty($label)) {
        $switch = '<label for="'.$id.'" class="cell '.$labelClass.' switch-label">'.$label.'</label><div class="cell '.$switchContainerClass.'">'.$switch.'</div>';
    }

    return $switch;
}

function acym_backToListing($listingName)
{
    return '<p class="acym__back_to_listing"><a href="'.acym_completeLink($listingName).'"><i class="fa fa-chevron-left"></i> '.acym_translation('ACYM_BACK_TO_LISTING').'</a></p>';
}

function acym_sortBy($options = [], $listing, $default = "")
{
    $default = empty($default) ? reset($options) : $default;

    $selected = acym_getVar('string', $listing.'_ordering', $default);
    $orderingSortOrder = acym_getVar('string', $listing.'_ordering_sort_order', 'desc');
    $classSortOrder = $orderingSortOrder == 'asc' ? 'fa-sort-amount-asc' : 'fa-sort-amount-desc';

    $display = '<span class="acym__color__dark-gray">'.acym_translation('ACYM_SORT_BY').'</span>
				<select name="'.$listing.'_ordering" id="acym__listing__ordering">';

    foreach ($options as $oneOptionValue => $oneOptionText) {
        $display .= '<option value="'.$oneOptionValue.'"';
        if ($selected == $oneOptionValue) {
            $display .= ' selected';
        }
        $display .= '>'.$oneOptionText.'</option>';
    }

    $display .= '</select>';

    $tooltipText = $orderingSortOrder == 'asc' ? acym_translation('ACYM_SORT_ASC') : acym_translation('ACYM_SORT_DESC');
    $display .= acym_tooltip('<i class="fa '.$classSortOrder.' acym__listing__ordering__sort-order" aria-hidden="true"></i>', $tooltipText);

    $display .= '<input type="hidden" id="acym__listing__ordering__sort-order--input" name="'.$listing.'_ordering_sort_order" value="'.$orderingSortOrder.'">';

    return $display;
}

function acym_getJSMessages()
{
    $msg = "{";
    $msg .= '"email": "'.acym_translation('ACYM_VALID_EMAIL', true).'",';
    $msg .= '"number": "'.acym_translation('ACYM_VALID_NUMBER', true).'",';
    $msg .= '"requiredMsg": "'.acym_translation('ACYM_REQUIRED_FIELD', true).'",';
    $msg .= '"defaultMsg": "'.acym_translation('ACYM_DEFAULT_VALIDATION_ERROR', true).'"';

    $keysToLoad = [
        'ACYM_ARE_YOU_SURE',
        'ACYM_INSERT_IMG_BAD_NAME',
        'ACYM_NON_VALID_URL',
        'ACYM_DYNAMIC_TEXT',
        'ACYM_ARE_YOU_SURE_DELETE',
        'ACYM_ARE_YOU_SURE_ACTIVE',
        'ACYM_ARE_YOU_SURE_INACTIVE',
        'ACYM_SEARCH',
        'ACYM_SEARCH_ENCODING',
        'ACYM_CANCEL',
        'ACYM_CONFIRM',
        'ACYM_TEMPLATE_CHANGED_CLICK_ON_SAVE',
        'ACYM_SURE_SEND_TRANSALTION',
        'ACYM_TESTS_SPAM_SENT',
        'ACYM_CONFIRMATION_CANCEL_CAMPAIGN_QUEUE',
        'ACYM_EXPORT_SELECT_LIST',
        'ACYM_YES',
        'ACYM_NO',
        'ACYM_NEXT',
        'ACYM_BACK',
        'ACYM_SKIP',
        'ACYM_INTRO_ADD_DTEXT',
        'ACYM_INTRO_TEMPLATE',
        'ACYM_INTRO_DRAG_BLOCKS',
        'ACYM_INTRO_DRAG_CONTENT',
        'ACYM_INTRO_SETTINGS',
        'ACYM_INTRO_CUSTOMIZE_FONT',
        'ACYM_INTRO_IMPORT_CSS',
        'ACYM_INTRO_SAFE_CHECK',
        'ACYM_INTRO_MAIL_SETTINGS',
        'ACYM_INTRO_ADVANCED',
        'ACYM_INTRO_DKIM',
        'ACYM_INTRO_CRON',
        'ACYM_INTRO_SUBSCRIPTION',
        'ACYM_INTRO_CHECK_DATABASE',
        'ACYM_SEND_TEST_SUCCESS',
        'ACYM_SEND_TEST_ERROR',
        'ACYM_COPY_DEFAULT_TRANSLATIONS_CONFIRM',
        'ACYM_BECARFUL_BACKGROUND_IMG',
        'ACYM_CANT_DELETE_AND_SAVE',
        'ACYM_AND',
        'ACYM_OR',
        'ACYM_ERROR',
        'ACYM_EDIT_MAIL',
        'ACYM_CREATE_MAIL',
        'ACYM_NO_RAND_FOR_MULTQUEUE',
        'ACYM_DELETE_MY_DATA_CONFIRM',
        'ACYM_CHOOSE_COLUMN',
        'ACYM_AUTOSAVE_USE',
        'ACYM_SELECT_NEW_ICON',
        'ACYM_ICON_IMPORTED',
        'ACYM_SESSION_IS_GOING_TO_END',
        'ACYM_CLICKS_OUT_OF',
        'ACYM_OF_CLICKS',
        'ACYM_ARE_SURE_DUPLICATE_TEMPLATE',
        'ACYM_NOT_FOUND',
        'ACYM_EMAIL',
        'ACYM_CAMPAIGN_NAME',
        'ACYM_EMAIL_SUBJECT',
        'ACYM_TEMPLATE_NAME',
        'ACYM_ERROR_SAVING',
        'ACYM_LOADING_ERROR',
        'ACYM_AT_LEAST_ONE_USER',
        'ACYM_ERROR_SAVING',
        'ACYM_NO_DCONTENT_TEXT',
        'ACYM_PREVIEW',
        'ACYM_PREVIEW_DESC',
        'ACYM_CONTENT_TYPE',
        'ACYM_TEMPLATE_EMPTY',
        'ACYM_DRAG_BLOCK_AND_DROP_HERE',
        'ACYM_WELL_DONE_DROP_HERE',
        'ACYM_REPLACE_CONFIRM',
        'ACYM_STATS_START_DATE_LOWER',
        'ACYM_ARE_YOU_SURE_DELETE_ADD_ON',
    ];

    foreach ($keysToLoad as $oneKey) {
        $msg .= ',"'.$oneKey.'": "'.acym_translation($oneKey, true).'"';
    }

    $msg .= "}";

    return $msg;
}

global $acymPlugins;
function acym_loadPlugins()
{
    $dynamics = acym_getFolders(ACYM_BACK.'dynamics');

    $pluginClass = acym_get('class.plugin');
    $plugins = $pluginClass->getAll('folder_name');

    foreach ($dynamics as $key => $oneDynamic) {
        if (!empty($plugins[$oneDynamic]) && '0' === $plugins[$oneDynamic]->active) unset($dynamics[$key]);
        if ('managetext' === $oneDynamic) unset($dynamics[$key]);
    }

    foreach ($plugins as $pluginFolder => $onePlugin) {
        if (in_array($pluginFolder, $dynamics) || '0' === $onePlugin->active) continue;
        $dynamics[] = $pluginFolder;
    }

    $dynamics[] = 'managetext';

    global $acymPlugins;
    foreach ($dynamics as $oneDynamic) {
        $dynamicFile = acym_getPluginPath($oneDynamic);
        $className = 'plgAcym'.ucfirst($oneDynamic);

        if (isset($acymPlugins[$className]) || !file_exists($dynamicFile) || !include_once $dynamicFile) continue;
        if (!class_exists($className)) continue;

        $plugin = new $className();
        if (!in_array($plugin->cms, ['all', 'Joomla']) || !$plugin->installed) continue;

        $acymPlugins[$className] = $plugin;
    }
}

function acym_trigger($method, $args = [], $plugin = null)
{
    global $acymPlugins;
    if (empty($acymPlugins)) acym_loadPlugins();

    $result = [];
    foreach ($acymPlugins as $class => $onePlugin) {
        if (!method_exists($onePlugin, $method)) continue;
        if (!empty($plugin) && $class != $plugin) continue;
        $value = call_user_func_array([$onePlugin, $method], $args);

        if (isset($value)) $result[] = $value;
    }

    return $result;
}

function acym_displayParam($type, $value, $name, $params = [])
{
    if (!include_once(ACYM_FRONT.'params'.DS.$type.'.php')) return '';

    $class = 'JFormField'.ucfirst($type);

    $field = new $class();
    $field->value = $value;
    $field->name = $name;

    if (!empty($params)) {
        foreach ($params as $param => $val) {
            $field->$param = $val;
        }
    }

    return $field->getInput();
}

function acym_upgradeTo($version)
{
    $link = ACYM_ACYWEBSITE.'acymailing/'.($version == 'essential' ? 'essential' : 'enterprise').'.html';
    $text = $version == 'essential' ? 'AcyMailing Essential' : 'AcyMailing Enterprise';
    echo '<div class="acym__upgrade cell grid-x text-center align-center">
            <h1 class="acym__listing__empty__title cell">'.acym_translation_sprintf('ACYM_USE_THIS_FEATURE', '<span class="acym__color__blue">'.$text.'</span>').'</h1>
            <a target="_blank" href="'.$link.'" class="button smaller-button cell shrink">'.acym_translation('ACYM_UPGRADE_NOW').'</a>
          </div>';
}

function acym_checkbox($values, $name, $selected = [], $label = '', $parentClass = '', $labelClass = '')
{
    echo '<div class="'.$parentClass.'"><div class="cell acym__label '.$labelClass.'">'.$label.'</div><div class="cell auto grid-x">';
    foreach ($values as $key => $value) {
        echo '<label class="cell grid-x margin-top-1"><input type="checkbox" name="'.$name.'" value="'.$key.'" '.(in_array($key, $selected) ? 'checked' : '').' >'.$value.'</label>';
    }
    echo '</div></div>';
}

function acym_existsAcyMailing59()
{
    $allTables = acym_getTables();
    if (!in_array(acym_getPrefix().'acymailing_config', $allTables)) return false;

    $version = acym_loadResult('SELECT `value` FROM #__acymailing_config WHERE `namekey` LIKE "version"');

    return version_compare($version, '5.9.0', '>=');
}

function acym_noCache()
{
    header('Cache-Control: no-store, no-cache, must-revalidate');
    header('Cache-Control: post-check=0, pre-check=0', false);
    header('Pragma: no-cache');
    header('Expires: Wed, 17 Sep 1975 21:32:10 GMT');
}

function acym_getDatabases()
{
    try {
        $allDatabases = acym_loadResultArray('SHOW DATABASES');
    } catch (Exception $exception) {
        $allDatabases = [];
        $allDatabases[] = acym_loadResult('SELECT DATABASE();');
    }

    $databases = [];
    foreach ($allDatabases as $database) {
        $databases[$database] = $database;
    }

    return $databases;
}

function acym_getSvg($svgPath)
{
    if (class_exists('SimpleXMLElement') && $xml = simplexml_load_file($svgPath)) {
        $res = $xml->asXML();
        if (!empty($res)) return $res;
    }

    return acym_fileGetContent($svgPath);
}


include_once ACYM_LIBRARY.'object.php';
include_once ACYM_LIBRARY.'class.php';
include_once ACYM_LIBRARY.'parameter.php';
include_once ACYM_LIBRARY.'controller.php';
include_once ACYM_LIBRARY.'view.php';
include_once ACYM_LIBRARY.'plugin.php';

acym_loadLanguage();

