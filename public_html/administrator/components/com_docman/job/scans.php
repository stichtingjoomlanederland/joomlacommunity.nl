<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 - 2014 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanJobScans extends ComSchedulerJobAbstract
{
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'frequency' => ComSchedulerJobInterface::FREQUENCY_EVERY_FIVE_MINUTES
        ));
    }

    public function run(ComSchedulerJobContextInterface $context)
    {
        try {
            $behavior = $this->getObject('com://admin/docman.controller.behavior.scannable');

            $i = 0;
            while ($context->hasTimeLeft() && $behavior->canSendScan() && $i < 5)
            {
                $scan = $behavior->sendPendingScan();

                if (!$scan->isNew() && $scan->status == \ComDocmanControllerBehaviorScannable::STATUS_SENT) {
                    $context->log('Sent request to scan '.$scan->identifier);
                }

                $i++;
            }

            if (!$behavior->isSupported()) {
                $context->log('Joomlatools Connect credentials are missing');
            }

            if ($behavior->needsThrottling()) {
                $context->log('Waiting for active scans to complete before sending new ones');
            }
        }
        catch (Exception $e) {
            $context->log($e->getMessage());
        }

        return $this->complete();
    }
}