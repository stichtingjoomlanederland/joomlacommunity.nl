<?php
/**
* @package      EasyDiscuss
* @copyright    Copyright (C) 2010 - 2015 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasyDiscuss is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

require_once(DISCUSS_ADMIN_ROOT . '/includes/maintenance/dependencies.php');

class EasyDiscussMaintenanceScriptMigrateCaptchaSettings extends EasyDiscussMaintenanceScript
{
    public static $title = "Migrate old captcha settings from 3.x to 4.x";
    public static $description = "This script is to migrate captcha settings from 3.x to 4.x";

    public function main()
    {
        $config = ED::config();

        $data = array();

        // If user previously enabled built in captcha, always use that
        if ($config->get('antispam_easydiscuss_captcha')) {
            $data['antispam_captcha'] = 'default';
        } else if ($config->get('antispam_recaptcha')) {
            $data['antispam_captcha'] = 'recaptcha';
        }

        if ($config->get('antispam_easydiscuss_captcha_registered')) {
            $data['antispam_captcha_registered'] = $config->get('antispam_easydiscuss_captcha_registered');
        }

        if ($config->get('antispam_recaptcha_registered_members') && !isset($data['antispam_captcha_registered'])) {
            $data['antispam_captcha_registered'] = $config->get('antispam_recaptcha_registered_members');
        }

        // Change antispam_skip_recaptcha to antispam_skip_captcha
        if ($config->get('antispam_skip_recaptcha')) {
            $data['antispam_skip_recaptcha'] = $config->get('antispam_skip_recaptcha');
        }

        $model = ED::model('Settings');
        $model->save($data);

        return true;
    }

}
