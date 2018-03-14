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
/**
 * @package    AkeebaCMSUpdate
 * @copyright  Copyright (c)2010-2014 Nicholas K. Dionysopoulos
 * @license    GNU General Public License version 3, or later
 *
 *  This program is free software: you can redistribute it and/or modify
 *  it under the terms of the GNU General Public License as published by
 *  the Free Software Foundation, either version 3 of the License, or
 *  (at your option) any later version.
 *
 *  This program is distributed in the hope that it will be useful,
 *  but WITHOUT ANY WARRANTY; without even the implied warranty of
 *  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 *  GNU General Public License for more details.
 *
 *  You should have received a copy of the GNU General Public License
 *  along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

/**
 * Provides update information for the Joomla! CMS
 */
class AcuUpdateProviderJoomla
{
    /**
     * The source for LTS updates
     *
     * @var  string
     */
    protected static $lts_url = 'http://update.joomla.org/core/list.xml';

    /**
     * The source for STS updates
     *
     * @var  string
     */
    protected static $sts_url = 'http://update.joomla.org/core/sts/list_sts.xml';

    /**
     * The source for test release updates
     *
     * @var  string
     */
    protected static $test_url = 'http://update.joomla.org/core/test/list_test.xml';

    /**
     * Reads a "collection" XML update source and picks the correct source URL
     * for the extension update source.
     *
     * @param   string $url      The collection XML update source URL to read from
     * @param   string $jVersion Joomla! version to fetch updates for, or null to use JVERSION
     *
     * @return  string  The URL of the extension update source, or empty if no updates are provided / fetching failed
     */
    public function getUpdateSourceFromCollection($url, $jVersion = NULL)
    {
        $provider = new AcuUpdateProviderCollection;

        return $provider->getExtensionUpdateSource($url, 'file', 'joomla', $jVersion);
    }

    /**
     * Reads an "extension" XML update source and returns all listed update
     * entries.
     *
     * @param   string $url The extension XML update source URL to read from
     *
     * @return  array  An array of update entries
     */
    public function getUpdatesFromExtension($url)
    {
        // Initialise
        $ret = array();

        // Get and parse the XML source
        $donwloader = new AcuDownload();
        $xmlSource  = $donwloader->getFromURL($url);

        try {
            $xml = new SimpleXMLElement($xmlSource, LIBXML_NONET);
        } catch (Exception $e) {
            return $ret;
        }

        // Sanity check
        if (($xml->getName() != 'updates')) {
            unset($xml);

            return $ret;
        }

        // Let's populate the list of updates
        foreach ($xml->children() as $update) {
            // Sanity check
            if ($update->getName() != 'update') {
                continue;
            }

            $entry = array(
                'infourl'        => array('title' => '', 'url' => ''),
                'downloads'      => array(),
                'tags'           => array(),
                'targetplatform' => array(),
            );

            $properties = get_object_vars($update);

            foreach ($properties as $nodeName => $nodeContent) {
                switch ($nodeName) {
                    default:
                        $entry[$nodeName] = $nodeContent;
                        break;

                    case 'infourl':
                    case 'downloads':
                    case 'tags':
                    case 'targetplatform':
                        break;
                }
            }

            $infourlNode               = $update->xpath('infourl');
            $entry['infourl']['title'] = (string)$infourlNode[0]['title'];
            $entry['infourl']['url']   = (string)$infourlNode[0];

            $downloadNodes = $update->xpath('downloads/downloadurl');
            foreach ($downloadNodes as $downloadNode) {
                $entry['downloads'][] = array(
                    'type'   => (string)$downloadNode['type'],
                    'format' => (string)$downloadNode['format'],
                    'url'    => (string)$downloadNode,
                );
            }

            $tagNodes = $update->xpath('tags/tag');
            foreach ($tagNodes as $tagNode) {
                $entry['tags'][] = (string)$tagNode;
            }

            $targetPlatformNode                 = $update->xpath('targetplatform');
            $entry['targetplatform']['name']    = (string)$targetPlatformNode[0]['name'];
            $entry['targetplatform']['version'] = (string)$targetPlatformNode[0]['version'];

            $ret[] = $entry;
        }

        unset($xml);

        return $ret;
    }

    /**
     * Determines the properties of a version: STS/LTS, normal or testing
     *
     * @param   string $jVersion       The version number to check
     * @param   string $currentVersion The current Joomla! version number
     *
     * @return  array  The properties analysis
     */
    public function getVersionProperties($jVersion, $currentVersion = NULL)
    {
        // Initialise
        $ret = array(
            'lts'     => TRUE,
            // Is this an LTS release? False means STS.
            'current' => FALSE,
            // Is this a release in the $currentVersion branch?
            'upgrade' => 'none',
            // Upgrade relation of $jVersion to $currentVersion: 'none' (can't upgrade), 'lts' (next or current LTS), 'sts' (next or current STS) or 'current' (same release, no upgrade available)
            'testing' => FALSE,
            // Is this a testing (alpha, beta, RC) release?
        );

        // Get the current version if none is defined
        if (is_null($currentVersion)) {
            $currentVersion = JVERSION;
        }

        // Sanitise version numbers
        $jVersion       = $this->sanitiseVersion($jVersion);
        $currentVersion = $this->sanitiseVersion($currentVersion);

        // Get the base version
        $baseVersion = substr($jVersion, 0, 3);

        // Get the minimum and maximum current version numbers
        $current_minimum = substr($currentVersion, 0, 3);
        $current_maximum = $current_minimum . '.9999';

        // Initialise STS/LTS version numbers
        $sts_minimum = FALSE;
        $sts_maximum = FALSE;
        $lts_minimum = FALSE;

        // Is it an LTS or STS release?
        switch ($baseVersion) {
            case '1.5':
                $ret['lts'] = TRUE;
                break;

            case '1.6':
                $ret['lts']  = FALSE;
                $sts_minimum = '1.7';
                $sts_maximum = '1.7.999';
                $lts_minimum = '2.5';
                break;

            case '1.7':
                $ret['lts']  = FALSE;
                $sts_minimum = FALSE;
                $lts_minimum = '2.5';
                break;

            default:
                $majorVersion = substr($jVersion, 0, 1);
                $minorVersion = substr($jVersion, 2, 1);

                if ($minorVersion == '5') {
                    $ret['lts'] = TRUE;
                    // This is an LTS release, it can be superseded by .0 through .4 STS releases on the next branch...
                    $sts_minimum = ($majorVersion + 1) . '.0';
                    $sts_maximum = ($majorVersion + 1) . '.4.9999';
                    // ...or a .5 LTS on the next branch
                    $lts_minimum = ($majorVersion + 1) . '.5';
                } else {
                    $ret['lts'] = FALSE;
                    // This is an STS release, it can be superseded by a .1/.2/.3/.4 STS release on the same branch...
                    $sts_minimum = $majorVersion . '.1';
                    $sts_maximum = $majorVersion . '.4.9999';
                    // ...or a .5 LTS on the same branch
                    $lts_minimum = $majorVersion . '.5';
                }
                break;
        }

        // Is it a current release?
        if (version_compare($jVersion, $current_minimum, 'ge') && version_compare($jVersion, $current_maximum, 'le')) {
            $ret['current'] = TRUE;
        }

        // Is this a testing release?
        $versionParts    = explode('.', $jVersion);
        $lastVersionPart = array_pop($versionParts);

        if (in_array(substr($lastVersionPart, 0, 1), array('a', 'b'))) {
            $ret['testing'] = TRUE;
        } elseif (substr($lastVersionPart, 0, 2) == 'rc') {
            $ret['testing'] = TRUE;
        } elseif (substr($lastVersionPart, 0, 3) == 'dev') {
            $ret['testing'] = TRUE;
        }

        // Find the upgrade relation of $jVersion to $currentVersion
        if (version_compare($jVersion, $currentVersion, 'eq')) {
            $ret['upgrade'] = 'current';
        } elseif (($sts_minimum !== FALSE) && version_compare($jVersion, $sts_minimum, 'ge') && version_compare($jVersion, $sts_maximum, 'le')) {
            $ret['upgrade'] = 'sts';
        } elseif (($lts_minimum !== FALSE) && version_compare($jVersion, $lts_minimum, 'ge')) {
            $ret['upgrade'] = 'lts';
        } elseif ($baseVersion == $current_minimum) {
            $ret['upgrade'] = $ret['lts'] ? 'lts' : 'sts';
        } else {
            $ret['upgrade'] = 'none';
        }

        return $ret;
    }

    /**
     * Filters a list of updates, making sure they apply to the specifed CMS
     * release.
     *
     * @param   array  $updates  A list of update records returned by the getUpdatesFromExtension method
     * @param   string $jVersion The current Joomla! version number
     *
     * @return  array  A filtered list of updates. Each update record also includes version relevance information.
     */
    public function filterApplicableUpdates($updates, $jVersion = NULL)
    {
        if (empty($jVersion)) {
            $jVersion = JVERSION;
        }

        $versionParts          = explode('.', $jVersion, 4);
        $platformVersionMajor  = $versionParts[0];
        $platformVersionMinor  = $platformVersionMajor . '.' . $versionParts[1];
        $platformVersionNormal = $platformVersionMinor . '.' . $versionParts[2];
        $platformVersionFull   = (count($versionParts) > 3) ? $platformVersionNormal . '.' . $versionParts[3] : $platformVersionNormal;

        $ret = array();

        foreach ($updates as $update) {
            // Check each update for platform match
            if (strtolower($update['targetplatform']['name']) != 'joomla') {
                continue;
            }

            $targetPlatformVersion = $update['targetplatform']['version'];

            if (($targetPlatformVersion !== $platformVersionMajor) && ($targetPlatformVersion !== $platformVersionMinor) && ($targetPlatformVersion !== $platformVersionNormal) && ($targetPlatformVersion !== $platformVersionFull)) {
                continue;
            }

            // Get some information from the version number
            $updateVersion     = $update['version'];
            $versionProperties = $this->getVersionProperties($updateVersion, $jVersion);

            if ($versionProperties['upgrade'] == 'none') {
                continue;
            }

            // The XML files are ill-maintained. Maybe we already have this update?
            if (!array_key_exists($updateVersion, $ret)) {
                $ret[$updateVersion] = array_merge($update, $versionProperties);
            }
        }

        return $ret;
    }

    /**
     * Joomla! has a lousy track record in naming its alpha, beta and release
     * candidate releases. The convention used seems to be "what the hell the
     * current package maintainer thinks looks better". This method tries to
     * figure out what was in the mind of the maintainer and translate the
     * funky version number to an actual PHP-format version string.
     *
     * @param   string $version The whatever-format version number
     *
     * @return  string  A standard formatted version number
     */
    public function sanitiseVersion($version)
    {
        $test                   = strtolower($version);
        $alphaQualifierPosition = strpos($test, 'alpha-');
        $betaQualifierPosition  = strpos($test, 'beta-');
        $rcQualifierPosition    = strpos($test, 'rc-');
        $rcQualifierPosition2   = strpos($test, 'rc');
        $devQualifiedPosition   = strpos($test, 'dev');

        if ($alphaQualifierPosition !== FALSE) {
            $betaRevision = substr($test, $alphaQualifierPosition + 6);
            if (!$betaRevision) {
                $betaRevision = 1;
            }
            $test = substr($test, 0, $alphaQualifierPosition) . '.a' . $betaRevision;
        } elseif ($betaQualifierPosition !== FALSE) {
            $betaRevision = substr($test, $betaQualifierPosition + 5);
            if (!$betaRevision) {
                $betaRevision = 1;
            }
            $test = substr($test, 0, $betaQualifierPosition) . '.b' . $betaRevision;
        } elseif ($rcQualifierPosition !== FALSE) {
            $betaRevision = substr($test, $rcQualifierPosition + 5);
            if (!$betaRevision) {
                $betaRevision = 1;
            }
            $test = substr($test, 0, $rcQualifierPosition) . '.rc' . $betaRevision;
        } elseif ($rcQualifierPosition2 !== FALSE) {
            $betaRevision = substr($test, $rcQualifierPosition2 + 5);
            if (!$betaRevision) {
                $betaRevision = 1;
            }
            $test = substr($test, 0, $rcQualifierPosition2) . '.rc' . $betaRevision;
        } elseif ($devQualifiedPosition !== FALSE) {
            $betaRevision = substr($test, $devQualifiedPosition + 6);
            if (!$betaRevision) {
                $betaRevision = '';
            }
            $test = substr($test, 0, $devQualifiedPosition) . '.dev' . $betaRevision;
        }

        return $test;
    }

    /**
     * Reloads the list of all updates available for the specified Joomla! version
     * from the network.
     *
     * @param    array  $sources  The enabled sources to look into
     * @param    string $jVersion The Joomla! version we are checking updates for
     *
     * @return   array  A list of updates for the installed, current, lts and sts versions
     */
    public function getUpdates($sources = array(), $jVersion = NULL)
    {
        // Make sure we have a valid list of sources
        if (empty($sources) || !is_array($sources)) {
            $sources = array();
        }

        $defaultSources = array('lts' => TRUE, 'sts' => TRUE, 'test' => TRUE, 'custom' => '');

        $sources = array_merge($defaultSources, $sources);

        // Use the current JVERSION if none is specified
        if (empty($jVersion)) {
            $jVersion = JVERSION;
        }

        // Get the current branch' min/max versions
        $versionParts      = explode('.', $jVersion, 4);
        $currentMinVersion = $versionParts[0] . '.' . $versionParts[1];
        $currentMaxVersion = $versionParts[0] . '.' . $versionParts[1] . '.9999';


        // Retrieve all updates
        $allUpdates = array();
        foreach ($sources as $source => $value) {
            if (($value === FALSE) || empty($value)) {
                continue;
            }

            switch ($source) {
                case 'lts':
                    $url = self::$lts_url;
                    break;

                case 'sts':
                    $url = self::$sts_url;
                    break;

                case 'test':
                    $url = self::$test_url;
                    break;

                case 'custom':
                    $url = $value;
                    break;
            }

            $url = $this->getUpdateSourceFromCollection($url, $jVersion);

            if (!empty($url)) {
                $updates = $this->getUpdatesFromExtension($url);

                if (!empty($updates)) {
                    $applicableUpdates = $this->filterApplicableUpdates($updates, $jVersion);

                    if (!empty($applicableUpdates)) {
                        $allUpdates = array_merge($allUpdates, $applicableUpdates);
                    }
                }
            }
        }

        $ret = array(
            // Currently installed version (used to reinstall, if available)
            'installed' => array(
                'version' => '',
                'package' => '',
                'infourl' => '',
            ),
            // Current branch
            'current'   => array(
                'version' => '',
                'package' => '',
                'infourl' => '',
            ),
            // Upgrade to STS release
            'sts'       => array(
                'version' => '',
                'package' => '',
                'infourl' => '',
            ),
            // Upgrade to LTS release
            'lts'       => array(
                'version' => '',
                'package' => '',
                'infourl' => '',
            ),
            // Upgrade to LTS release
            'test'      => array(
                'version' => '',
                'package' => '',
                'infourl' => '',
            ),
        );

        foreach ($allUpdates as $update) {
            $sections = array();

            if ($update['upgrade'] == 'current') {
                $sections[0] = 'installed';
            } elseif (version_compare($update['version'], $currentMinVersion, 'ge') && version_compare($update['version'], $currentMaxVersion, 'le')) {
                $sections[0] = 'current';
            } else {
                $sections[0] = '';
            }

            $sections[1] = $update['lts'] ? 'lts' : 'sts';

            if ($update['testing']) {
                $sections = array('test');
            }

            foreach ($sections as $section) {
                if (empty($section)) {
                    continue;
                }

                $existingVersionForSection = $ret[$section]['version'];

                if (empty($existingVersionForSection)) {
                    $existingVersionForSection = '0.0.0';
                }

                if (version_compare($update['version'], $existingVersionForSection, 'ge')) {
                    $ret[$section]['version'] = $update['version'];
                    $ret[$section]['package'] = $update['downloads'][0]['url'];
                    $ret[$section]['infourl'] = $update['infourl']['url'];
                }
            }
        }

        // Catch the case when the latest current branch version is the installed version (up to date site)
        if (empty($ret['current']['version']) && !empty($ret['installed']['version'])) {
            $ret['current'] = $ret['installed'];
        }

        return $ret;
    }
}
