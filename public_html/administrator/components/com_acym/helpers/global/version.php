<?php
defined('_JEXEC') or die('Restricted access');
?><?php

function acym_level($level)
{
    $config = acym_config();
    if ($config->get($config->get('level'), 0) >= $level) {
        return true;
    }

    return false;
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
    ];

    if (acym_level(1)) {
        $paramsForLicenseCheck['php'] = PHP_VERSION; // Return a warning if Acy cannot be installed with this version
    }

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

function acym_upgradeTo($version)
{
    $link = ACYM_ACYWEBSITE.'acymailing/'.($version == 'essential' ? 'essential' : 'enterprise').'.html';
    $text = $version == 'essential' ? 'AcyMailing Essential' : 'AcyMailing Enterprise';
    echo '<div class="acym__upgrade cell grid-x text-center align-center">
            <h2 class="acym__listing__empty__title cell">'.acym_translation_sprintf('ACYM_USE_THIS_FEATURE', '<span class="acym__color__blue">'.$text.'</span>').'</h2>
            <a target="_blank" href="'.$link.'" class="button  cell shrink">'.acym_translation('ACYM_UPGRADE_NOW').'</a>
          </div>';
}

function acym_existsAcyMailing59()
{
    $allTables = acym_getTables();
    if (!in_array(acym_getPrefix().'acymailing_config', $allTables)) return false;

    $version = acym_loadResult('SELECT `value` FROM #__acymailing_config WHERE `namekey` LIKE "version"');

    return version_compare($version, '5.9.0', '>=');
}

function acym_buttonGetProVersion($class = 'cell shrink')
{
    return '<a href="https://www.acyba.com/acymailing.html#customwhichpack" target="_blank" class="button '.$class.'">'.acym_translation('ACYM_GET_PRO_VERSION').'</a>';
}

