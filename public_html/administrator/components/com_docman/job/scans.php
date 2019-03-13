<?php
/**
 * @package    DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanJobScans extends ComSchedulerJobAbstract
{
    /**
     * The number of halt retries (all scans) before halting the job
     *
     * @var int
     */
    protected $_halt_retries_limit = 5;

    /**
     * The number of failed scans for triggering a halt
     *
     * @var int
     */
    protected $_halt_fail_trigger = 5;

    /**
     * The number of retries per scan before abandoning
     *
     * @var int
     */
    protected $_scan_retries_limit = 3;

    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'frequency' => ComSchedulerJobInterface::FREQUENCY_EVERY_FIVE_MINUTES
        ));

        parent::_initialize($config);
    }

    public function run(ComSchedulerJobContextInterface $context)
    {
        try
        {
            $behavior = $this->getObject('com://admin/docman.controller.behavior.scannable');

            if (!$behavior->isSupported()) {
                $context->log('Joomlatools Connect credentials are missing');

                return $this->skip();
            }

            $behavior->purgeStaleScans();

            if (!$this->_isHalted($context))
            {
                $i = 0;
                $has_error = false;

                $this->_handleFailedScans($context);

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
        }
        catch (Exception $e) {
            $context->log($e->getMessage());

            return $this->complete();
        }
    }

    protected function _isHalted(ComSchedulerJobContextInterface $context)
    {
        $result = true;

        $state = $context->getState();

        if ($state->halt)
        {
            $halted_on = new DateTime($state->halted_on, new DateTimeZone('UTC'));

            // Reset the retry count next month

            if ($halted_on->format('Y') != gmdate('Y', time())) {
                $state->halt_count = 0;
            } elseif ($halted_on->format('n') < gmdate('n', time())) {
                $state->halt_count = 0;
            }

            if ($state->halt_count < $this->_halt_retries_limit)
            {
                $modifier = sprintf('+%d hours', pow(2, $state->halt_count) - 1);

                $halted_on->modify($modifier);

                if ($halted_on->getTimestamp() < time())
                {
                    // Un-halt the job so that scans are sent

                    $state->halt = false;

                    // Reset failed scans

                    $query = $this->getObject('lib:database.query.update');

                    $query->table('docman_scans')
                          ->values(array('status = :pending'))
                          ->where('status = :failed')
                          ->where('retries < :retries')
                          ->bind(array(
                              'failed'  => ComDocmanControllerBehaviorScannable::STATUS_FAILED,
                              'pending' => ComDocmanControllerBehaviorScannable::STATUS_PENDING,
                              'retries' => $this->_scan_retries_limit
                          ));

                    $this->getObject('lib:database.adapter.mysqli')->update($query);

                    $context->log(sprintf('DOCman scans job has been un-halted with a halt count of %s', $state->halt_count));

                    $result = false;
                }
            }
        }
        else
        {
            // See if the job should be halted

            $query = $this->getObject('lib:database.query.select');

            $query->table('docman_scans')
                  ->columns(array('COUNT(*)'))
                  ->where('status = :status')
                  ->bind(array('status' => ComDocmanControllerBehaviorScannable::STATUS_FAILED));

            $retries = (int) $this->getObject('lib:database.adapter.mysqli')->select($query, KDatabase::FETCH_FIELD);

            if ($retries >= $this->_halt_fail_trigger)
            {
                $state->halt       = true;
                $state->halt_count = $state->halt_count ? $state->halt_count + 1 : 1;
                $state->halted_on  = gmdate('Y-m-d H:i:s', time());

                $context->log(sprintf('DOCman scan jobs has been halted with a halt count of %s', $state->halt_count));
            }
            else $result = false;
        }

        return $result;
    }

    protected function _handleFailedScans(ComSchedulerJobContextInterface $context)
    {
        $query = $this->getObject('lib:database.query.update');

        $query->table('docman_scans')
              ->values('status = :pending')
              ->bind(array('pending' => ComDocmanControllerBehaviorScannable::STATUS_PENDING));

        for ($i = 0; $i < $this->_scan_retries_limit; $i++)
        {
            $condition = sprintf('(retries = :retries_%1$s AND status = :failed AND DATE_ADD(sent_on, INTERVAL %2$s HOUR) < UTC_TIMESTAMP())', $i, pow(2, $i + 1) - 1);

            $query->where($condition, 'OR')->bind(array(
                sprintf('retries_%1$s', $i) => $i
            ));
        }

        $query->bind(array('failed' => ComDocmanControllerBehaviorScannable::STATUS_FAILED));

        $adapter = $this->getObject('lib:database.adapter.mysqli');

        if ($result = $adapter->update($query)) {
            $context->log(sprintf('%s failed DOCman scans have been reset to pending', $result));
        }

        $query = $this->getObject('lib:database.query.update');

        $query->table('docman_scans')->values('status = :abandoned')->where('retries >= :retries')
              ->where('status = :failed')->bind(array(
                'abandoned' => ComDocmanControllerBehaviorScannable::STATUS_ABANDONED,
                'retries'   => $this->_scan_retries_limit,
                'failed'    => ComDocmanControllerBehaviorScannable::STATUS_FAILED
            ));

        if ($result = $adapter->update($query)) {
            $context->log(sprintf('%s failed DOCman scans have been abandoned', $result));
        }
    }
}