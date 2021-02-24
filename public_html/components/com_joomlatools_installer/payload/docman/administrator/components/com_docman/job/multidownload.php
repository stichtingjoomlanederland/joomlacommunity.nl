<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanJobMultidownload extends ComSchedulerJobAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'frequency' => ComSchedulerJobInterface::FREQUENCY_HOURLY
        ));

        parent::_initialize($config);
    }

    public function run(ComSchedulerJobContextInterface $context)
    {
        /**
         * @var $behavior ComDocmanControllerBehaviorCompressible
         */
        $behavior = $this->getObject('com://site/docman.controller.behavior.compressible');

        if (!$behavior->isSupported()) {
            return $this->skip();
        }

        $behavior->purgeExpiredFiles();

        return $this->complete();
    }
}