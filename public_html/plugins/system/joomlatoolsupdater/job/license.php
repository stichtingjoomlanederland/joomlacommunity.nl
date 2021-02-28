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
        $config->append([
            'frequency'   => '0 */12 * * *' //every 12 hours
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