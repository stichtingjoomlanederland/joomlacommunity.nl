<?php
defined('_JEXEC') or die('Restricted access');
?><?php

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

