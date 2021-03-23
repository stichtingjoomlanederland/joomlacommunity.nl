<?php

use AcyMailing\Classes\ConfigurationClass;

function acydump($arg, $ajax = false, $indent = true)
{
    ob_start();
    var_dump($arg);
    $result = ob_get_clean();

    if ($ajax) {
        file_put_contents(ACYM_ROOT.'acydebug.txt', $result, FILE_APPEND);
    } else {
        $style = $indent ? 'margin-left: 220px;' : '';
        echo '<pre style="'.$style.'">'.htmlentities($result).'</pre>';
    }
}

function acym_debug($file = false, $indent = true)
{
    $debug = debug_backtrace();
    $takenPath = [];
    foreach ($debug as $step) {
        if (empty($step['file']) || empty($step['line'])) continue;
        $takenPath[] = $step['file'].' => '.$step['line'];
    }
    acydump(implode($file ? "\n" : '<br/>', $takenPath), $file, $indent);
}

function acym_config($reload = false)
{
    static $configClass = null;
    if ($configClass === null || $reload) {
        $configClass = new ConfigurationClass();
        $configClass->load();
    }

    return $configClass;
}

function acym_get($path)
{
    list($group, $class) = explode('.', $path);

    $className = ucfirst($class).ucfirst(str_replace('_front', '', $group));

    if (substr($group, 0, 4) == 'view') {
        $className .= ucfirst($class);
        $class .= DS.'view.html';
    }

    if ($group === 'class') {
        $className = 'AcyMailing\\Classes\\'.$className;
    } elseif ($group === 'controller') {
        $className = 'AcyMailing\\Controllers\\'.$className;
    } elseif ($group === 'view') {
        $className = 'AcyMailing\\Views\\'.$className;
    } elseif ($group === 'helper') {
        $className = 'AcyMailing\\Helpers\\'.$className;
    } elseif ($group === 'controller_front') {
        $className = 'AcyMailing\\FrontControllers\\'.$className;
    } elseif ($group === 'type') {
        $className = 'AcyMailing\\Types\\'.$className;
    } elseif ($group === 'view_front') {
        $className = 'AcyMailing\\FrontViews\\'.$className;
    }

    if (!class_exists($className)) {
        $classFile = constant(strtoupper('ACYM_'.$group)).$class.'.php';
        if (file_exists($classFile)) require_once $classFile;

        if (!class_exists($className)) return null;
    }

    return new $className();
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
            echo '<i data-id="'.acym_escape($id).'" class="cell shrink acym__message__close acymicon-remove"></i>';
        }
        echo '</div>';
    }
}

function acym_increasePerf()
{
    @ini_set('max_execution_time', 600);
    @ini_set('pcre.backtrack_limit', 1000000);
}

function acym_session()
{
    $sessionID = session_id();
    if (empty($sessionID)) {
        @session_start();
    }
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

function acym_header($header, $replace = true)
{
    if (headers_sent()) return;
    header($header, $replace);
}

function acym_getSocialMedias()
{
    return json_decode(ACYM_SOCIAL_MEDIA, true);
}
