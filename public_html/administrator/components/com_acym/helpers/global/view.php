<?php
defined('_JEXEC') or die('Restricted access');
?><?php

function acym_getView($ctrl, $view, $forceBackend = false)
{
    $override = acym_getPageOverride($ctrl, $view, $forceBackend);

    if (!empty($override) && file_exists($override)) {
        return $override;
    } else {
        $viewsFolder = ($forceBackend || acym_isAdmin()) ? ACYM_VIEW : ACYM_VIEW_FRONT;

        return $viewsFolder.$ctrl.DS.'tmpl'.DS.$view.'.php';
    }
}

function acym_loadAssets($scope, $ctrl, $task)
{
    acym_loadCmsScripts();

    acym_addScript(
        true,
        'var AJAX_URL_UPDATEME = "'.ACYM_UPDATEMEURL.'";
        var MEDIA_URL_ACYM = "'.ACYM_MEDIA_URL.'";
        var CMS_ACYM = "'.ACYM_CMS.'";
        var FOUNDATION_FOR_EMAIL = "'.ACYM_CSS.'libraries/foundation_email.min.css?v='.filemtime(ACYM_MEDIA.'css'.DS.'libraries'.DS.'foundation_email.min.css').'";
        var ACYM_FIXES_FOR_EMAIL = "'.str_replace('"', '\"', acym_getEmailCssFixes()).'";
        var ACYM_REGEX_EMAIL = /^'.acym_getEmailRegex(true).'$/i;
        var ACYM_JS_TXT = '.acym_getJSMessages().';'
    );

    acym_addScript(false, ACYM_JS.'libraries/foundation.min.js?v='.filemtime(ACYM_MEDIA.'js'.DS.'libraries'.DS.'foundation.min.js'));

    if ('back' === $scope) {
        acym_addScript(false, ACYM_JS.'libraries/select2.min.js?v='.filemtime(ACYM_MEDIA.'js'.DS.'libraries'.DS.'select2.min.js'));
    }

    acym_addScript(false, ACYM_JS.'helpers.min.js?v='.filemtime(ACYM_MEDIA.'js'.DS.'helpers.min.js'), 'text/javascript', true);
    if ('back' == $scope) acym_addScript(false, ACYM_JS.$scope.'_helpers.min.js?v='.filemtime(ACYM_MEDIA.'js'.DS.'back_helpers.min.js'));
    acym_addScript(false, ACYM_JS.'global.min.js?v='.filemtime(ACYM_MEDIA.'js'.DS.'global.min.js'));
    acym_addScript(false, ACYM_JS.$scope.'_global.min.js?v='.filemtime(ACYM_MEDIA.'js'.DS.$scope.'_global.min.js'));

    if (file_exists(ACYM_MEDIA.'js'.DS.$scope.DS.$ctrl.'.min.js')) {
        acym_addScript(false, ACYM_JS.$scope.'/'.$ctrl.'.min.js?v='.filemtime(ACYM_MEDIA.'js'.DS.$scope.DS.$ctrl.'.min.js'), 'text/javascript', true);
    }

    acym_addStyle(false, ACYM_CSS.'global.min.css?v='.filemtime(ACYM_MEDIA.'css'.DS.'global.min.css'));

    if (!acym_isExcludedFrontView($ctrl, $task)) {
        acym_addStyle(false, ACYM_CSS.$scope.'_global.min.css?v='.filemtime(ACYM_MEDIA.'css'.DS.$scope.'_global.min.css'));
    }

    if (file_exists(ACYM_MEDIA.'css'.DS.$scope.DS.$ctrl.'.min.css')) {
        acym_addStyle(false, ACYM_CSS.$scope.'/'.$ctrl.'.min.css?v='.filemtime(ACYM_MEDIA.'css'.DS.$scope.DS.$ctrl.'.min.css'));
    }
}

function acym_isExcludedFrontView($ctrl, $task)
{
    if ('archive' === $ctrl && in_array($task, ['view', 'listing'])) return true;
    if ('frontusers' === $ctrl && 'profile' === $task) return true;

    return false;
}

