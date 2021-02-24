<?php

/**
 * @package     JoomlatoolsUpdater
 * @copyright   Copyright (C) 2021 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class PlgSystemJoomlatoolsupdaterInstaller extends \JInstaller
{
    public $error = 'An error occurred during installation';

    public function abort($msg = null, $type = null)
    {
        if ($msg) {
            $this->error = $msg;
        }

        return parent::abort($msg, $type);
    }
}
