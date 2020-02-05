<?php
defined('_JEXEC') or die('Restricted access');
?><?php

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

function acym_config($reload = false)
{
    static $configClass = null;
    if ($configClass === null || $reload) {
        $configClass = acym_get('class.configuration');
        $configClass->load();
    }

    return $configClass;
}

function acym_get($path)
{
    list($group, $class) = explode('.', $path);

    $className = $class.ucfirst(str_replace('_front', '', $group));
    if ($group == 'class' || ($group == 'helper' && strpos($className, 'acym') !== 0)) {
        $className = 'acym'.$className;
    }

    if (substr($group, 0, 4) == 'view') {
        $className = $className.ucfirst($class);
        $class .= DS.'view.html';
    }

    if (!class_exists($className)) {
        $classFile = constant(strtoupper('ACYM_'.$group)).$class.'.php';
        if (file_exists($classFile)) include_once $classFile;

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

function acym_listingActions($actions)
{
    $defaultAction = new stdClass();
    $defaultAction->value = 0;
    $defaultAction->text = acym_translation('ACYM_CHOOSE_ACTION');
    $defaultAction->disable = true;

    array_unshift($actions, $defaultAction);

    return acym_select($actions, '', null, 'class="medium-shrink cell margin-right-1"', 'value', 'text', 'listing_actions');
}

function acym_backToListing($listingName = null)
{
    if (empty($listingName)) $listingName = acym_getVar('cmd', 'ctrl');

    $returnLink = '<p class="acym__back_to_listing">';
    $returnLink .= '<a href="'.acym_completeLink($listingName).'" class="acym_vcenter">';
    $returnLink .= '<i class="acymicon-chevron-left"></i> '.acym_translation('ACYM_BACK_TO_LISTING');
    $returnLink .= '</a>';
    $returnLink .= '</p>';

    return $returnLink;
}

function acym_getSvg($svgPath)
{
    $xml = @simplexml_load_file($svgPath);

    if (class_exists('SimpleXMLElement') && $xml !== false) {
        $res = $xml->asXML();
        if (!empty($res)) return $res;
    }

    return acym_fileGetContent($svgPath);
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
        'ACYM_COULD_NOT_SUBMIT_FORM_CONTACT_ADMIN_WEBSITE',
    ];

    foreach ($keysToLoad as $oneKey) {
        $msg .= ',"'.$oneKey.'": "'.acym_translation($oneKey, true).'"';
    }

    $msg .= "}";

    return $msg;
}

