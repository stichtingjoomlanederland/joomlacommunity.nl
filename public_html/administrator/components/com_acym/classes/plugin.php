<?php
defined('_JEXEC') or die('Restricted access');
?><?php

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

