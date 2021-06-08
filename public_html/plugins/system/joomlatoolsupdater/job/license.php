<?php
/**
 * @package     JoomlatoolsUpdater
 * @copyright   Copyright (C) 2021 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class PlgSystemJoomlatoolsupdaterJobLicense extends ComSchedulerJobAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $secret = JFactory::getConfig()->get('secret');

        if (!$secret || strlen($secret) < 2) {
            $secret = str_replace('www.', '', $this->getObject('request')->getHost());
        }

        // Joomla secrets are normally a-zA-Z0-9 which is 48-57, 65-90, 97-122
        // We run the requests between 21:00UTC and 06:59UTC
        $hour = (ord($secret[0]) % 10) - 3;
        $minute = ord($secret[1]) % 60;

        if ($hour < 0) {
            $hour += 24;
        }

        $config->append([
            'frequency'   => "$minute $hour * * *"
        ]);

        parent::_initialize($config);
    }

    public function run(ComSchedulerJobContextInterface $context)
    {
        try {
            /** @var \PlgSystemJoomlatoolsupdaterLicense $license */
            $license = $this->getObject('license');
            $license->refresh();
        }
        catch (\Exception $e) {
            $context->log('exception: '.$e->getMessage());
        }

        return $this->complete();
    }
}