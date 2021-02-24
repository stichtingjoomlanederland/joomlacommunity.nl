<?php
/**
 * @package     JoomlatoolsUpdater
 * @copyright   Copyright (C) 2021 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class PlgSystemJoomlatoolsupdaterInstallerScript
{
    public function postflight($type, $installer)
    {
        $query = sprintf("SELECT extension_id FROM #__extensions WHERE type = '%s' AND element = '%s' AND folder = '%s'",
            'plugin', 'joomlatoolsupdater', 'system'
        );

        $extension_id = JFactory::getDbo()->setQuery($query)->loadResult();

        if ($extension_id) {
            // Enable plugin
            $query = sprintf("UPDATE #__extensions SET enabled = 1 WHERE extension_id = %d", $extension_id);
            JFactory::getDbo()->setQuery($query)->execute();

            $params_query = sprintf("SELECT params FROM #__extensions WHERE extension_id = %d", $extension_id);

            $parameters = JFactory::getDbo()->setQuery($params_query)->loadResult();
            $parameters = @json_decode($parameters, true);

            if (!$parameters) {
                $parameters = [];
            }

            // Save parameters if supplied
            $source = $installer->getParent()->getPath('source');

            if (file_exists($source.'/.api.key')) {
                $parameters = array_merge($parameters, array(
                    'api_key'    => trim(file_get_contents($source.'/.api.key')),
                ));
            }

            if (file_exists($source.'/.public.key')) {
                $parameters = array_merge($parameters, array(
                    'public_key' => trim(file_get_contents($source.'/.public.key')),
                ));
            }

            $query = sprintf("UPDATE #__extensions SET params = '%s' WHERE extension_id = %d", json_encode($parameters), $extension_id);

            JFactory::getDbo()->setQuery($query)->execute();

            try {
                if (class_exists('Koowa')) {
                    KObjectManager::getInstance()->getObject('plg:system.joomlatoolsupdater.license')->refresh();
                }
            } catch (Exception $e) {
                JFactory::getApplication()->enqueueMessage($e->getMessage(), 'error');
            }


        }

        return true;
    }

}