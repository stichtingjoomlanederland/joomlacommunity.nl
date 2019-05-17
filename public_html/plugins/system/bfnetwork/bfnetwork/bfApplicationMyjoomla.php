<?php

/*
 * @package   bfNetwork
 * @copyright Copyright (C) 2011, 2012, 2013, 2014, 2015, 2016, 2017, 2018, 2019 Blue Flame Digital Solutions Ltd. All rights reserved.
 * @license   GNU General Public License version 3 or later
 *
 * @see       https://myJoomla.guru/
 * @see       https://myWP.guru/
 * @see       https://mySites.guru/
 * @see       https://www.phil-taylor.com/
 *
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
 *
 * If you have any questions regarding this code, please contact phil@phil-taylor.com
 */

defined('_JEXEC') or die();

/*
 * Our very own myJoomla implementation of some core Joomla features
 * We need this so that we can overwrite troublesome methods in the class
 *
 * Class JApplicationMyjoomla
 */
if (class_exists('JApplicationCms')) {
    /**
     * Joomla 3.0.0 +.
     *
     * Class JApplicationMyjoomla
     */
    class JApplicationMyjoomla extends JApplicationCms
    {
        /**
         * @var array The details of the redirect
         */
        private $_redirectDetails = array();

        /**
         * Override the redirect method
         * We need this so we can stop Joomla from running exit(0) and killing us!
         * This is useful when an extension update aborts and redirects to com_installer
         * It will probably be even more use as we blur the edges of what Joomla can achieve.
         *
         * @param string $url
         * @param bool   $moved
         */
        public function redirect($url, $moved = false)
        {
            $this->setRedirect($url, $moved);

            // Note we DO NOT exit(0) or die here - yes this *could* cause issues, but at the moment we have seen none.
        }

        private function setRedirect($url, $moved = false)
        {
            $this->_redirectDetails = array(
                'headers'      => $this->getHeaders(),
                'messagequeue' => $this->getMessageQueue(),
                'url'          => $url,
            );
        }

        /**
         * Return the details of any set redirect.
         *
         * @return array
         */
        public function getRedirectDetails()
        {
            return $this->_redirectDetails;
        }

        /**
         * Return the current state of the language filter.
         *
         * @return bool
         *
         * @since	3.2
         */
        public function getLanguageFilter()
        {
            return false;
        }
    }
} elseif (class_exists('JApplication')) {
    /**
     * Joomla 1.5.0 - 1.5.26
     * Joomla 2.5.0 - 2.5.28.
     *
     * Class JApplicationMyjoomla
     */
    class JApplicationMyjoomla extends JApplication
    {
        /**
         * @var array The details of the redirect
         */
        private $_redirectDetails = array();

        /**
         * Override the redirect method
         * We need this so we can stop Joomla from running exit(0) and killing us!
         * This is useful when an extension update aborts and redirects to com_installer
         * It will probably be even more use as we blur the edges of what Joomla can achieve.
         *
         * @param string $url
         * @param bool   $moved
         */
        public function redirect($url, $moved = false)
        {
            $this->setRedirect($url, $moved);

            // Note we DO NOT exit(0) or die here - yes this *could* cause issues, but at the moment we have seen none.
        }

        private function setRedirect($url, $moved = false)
        {
            $this->_redirectDetails = array(
                'headers'      => $this->getHeaders(),
                'messagequeue' => $this->getMessageQueue(),
                'url'          => $url,
            );
        }

        /**
         * Return the details of any set redirect.
         *
         * @return array
         */
        public function getRedirectDetails()
        {
            return $this->_redirectDetails;
        }
    }
}
