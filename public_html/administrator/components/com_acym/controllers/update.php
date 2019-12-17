<?php
/**
 * @package	AcyMailing for Joomla
 * @version	6.6.1
 * @author	acyba.com
 * @copyright	(C) 2009-2019 ACYBA SAS - All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */

defined('_JEXEC') or die('Restricted access');
?>
<?php

class updateController extends acymController
{
    public function checkForNewVersion()
    {
        $lastlicensecheck = acym_checkVersion(true);

        $headerHelper = acym_get('helper.header');
        $myAcyArea = $headerHelper->checkVersionArea();

        echo json_encode(['content' => $myAcyArea, 'lastcheck' => acym_date($lastlicensecheck, 'Y/m/d H:i')]);
        exit;
    }
}

