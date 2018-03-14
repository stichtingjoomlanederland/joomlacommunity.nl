<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
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

        parent::_initialize($config);
    }

    public function run(ComSchedulerJobContextInterface $context)
    {
        try {
            $behavior = $this->getObject('com://admin/docman.controller.behavior.scannable');

            if (!$behavior->isSupported()) {
                $context->log('Joomlatools Connect credentials are missing');

                return $this->skip();
            }

            $i = 0;
            $has_error = false;

            while ($context->hasTimeLeft() && $behavior->canSendScan() && $i < 4)
            {
                $scan = $behavior->sendPendingScan();

                if (!$scan->isNew() && $scan->status == \ComDocmanControllerBehaviorScannable::STATUS_SENT) {
                    $context->log('Sent request to scan '.$scan->identifier);
                } else {
                    $has_error = true;
                }

                $i++;
            }

            if ($behavior->needsThrottling()) {
                $context->log('Waiting for active scans to complete before sending new ones');
            }

            return $behavior->canSendScan() && !$has_error ? $this->suspend() : $this->complete();
        }
        catch (Exception $e) {
            $context->log($e->getMessage());

            return $this->complete();
        }
    }
}