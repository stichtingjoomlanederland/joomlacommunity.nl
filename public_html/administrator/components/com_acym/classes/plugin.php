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

class acympluginClass extends acymClass
{
    var $table = 'plugin';
    var $pkey = 'id';

    public function getNotUptoDatePlugins()
    {
        $testPluginTable = 'SHOW TABLES LIKE "%_acym_plugin"';
        $result = acym_loadResult($testPluginTable);
        if (empty($result)) return 0;

        $query = 'SELECT count(id) FROM #__acym_plugin WHERE uptodate = 0';

        return acym_loadResult($query);
    }
}

