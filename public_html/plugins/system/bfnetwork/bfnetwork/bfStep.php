<?php

/**
 * @package   Blue Flame Network (bfNetwork)
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license   GNU General Public License version 3 or later
 * @link      https://myJoomla.com/
 * @author    Phil Taylor / Blue Flame Digital Solutions Limited.
 *
 * bfNetwork is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * bfNetwork is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this package.  If not, see http://www.gnu.org/licenses/
 */
final class STEP
{

    /**
     * Our steps of the audit, broken down into sections mainly for
     * Reporting
     */
    const TESTCONNECTION         = 1;
    const REQUESTSCANNERCONFIG   = 2;
    const SCANNINGROOTDIRS       = 3;
    const INITIALSCANNINGFOLDERS = 4;
    const INITIALSCANNINGFILES   = 5;
    const LOOKINGUPMODIFIEDFILES = 6;
    const GETHASHFAILURECOUNT    = 7;
    const DBINFO                 = 8;
    const DEEPSCAN               = 9;
    const COMPILEEXTENSIONS      = 10;
    const VERIFYEXTENSIONS       = 11;
    const BESTPRACTICESECURITY   = 12;
    const COMPLETE               = 13;

    /**
     * @var int The current step we are running
     */
    private $_currentStep;

    /**
     *
     * @var array Inverse of our CONST's so that we can convert both ways
     */
    private $steps = array(
        '1'  => 'TESTCONNECTION',
        '2'  => 'REQUESTSCANNERCONFIG',
        '3'  => 'SCANNINGROOTDIRS',
        '4'  => 'INITIALSCANNINGFOLDERS',
        '5'  => 'INITIALSCANNINGFILES',
        '6'  => 'LOOKINGUPMODIFIEDFILES',
        '7'  => 'GETHASHFAILURECOUNT',
        '8'  => 'DBINFO',
        '9'  => 'DEEPSCAN',
        '10' => 'COMPILEEXTENSIONS',
        '11' => 'VERIFYEXTENSIONS',
        '12' => 'BESTPRACTICESECURITY',
        '13' => 'COMPLETE'
    );

    /**
     * Initialise the audit, setting the current step to run
     *
     * @param int $currentStep
     */
    public function __construct($currentStep = NULL)
    {
        if (!$currentStep) {
            $currentStep = STEP::TESTCONNECTION;
        }
        $this->_currentStep = $currentStep;
    }

    /**
     * Force the audit onto the next step in the audit process,
     * this is a STEP (Section) not a TICK!
     *
     * @return int The current step
     */
    public function nextStepPlease()
    {

        // If we are almost complete then mark it as so
        if ($this->_currentStep > count($this->steps)) {

            $this->_currentStep = STEP::COMPLETE;
        } else {

            // Increase the step by one
            $this->_currentStep++;
        }

        return $this->_currentStep;
    }

    /**
     * Get the current step
     * @return string The name of the step
     */
    public function __toString()
    {
        return $this->steps [$this->_currentStep];
    }

    /**
     * Get the method name for a step
     *
     * @param int $step
     *
     * @return string
     */
    public function getStepFunction($step)
    {
        return strtolower($this->steps[$step]) . 'Action';
    }
}
