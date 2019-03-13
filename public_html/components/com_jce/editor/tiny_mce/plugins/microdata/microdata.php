<?php

/**
 * @copyright     Copyright (c) 2009-2019 Ryan Demmer. All rights reserved
 * @license       GNU/GPL 2 or later - http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * JCE is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses
 */
defined('JPATH_PLATFORM') or die;

// Load class dependencies

// Link Plugin Controller
class WFMicrodataPlugin extends WFEditorPlugin
{
    protected static $_url = 'https://schema.org/docs/schema_org_rdfa.html';
    protected static $_schema = null;

    /**
     * Constructor activating the default information of the class.
     */
    public function __construct()
    {
        parent::__construct();

        $request = WFRequest::getInstance();

        $request->setRequest(array($this, 'getSchema'));
        $request->setRequest(array($this, 'getTypeList'));
        $request->setRequest(array($this, 'getPropertyList'));
    }

    public function display()
    {
        parent::display();

        $document = WFDocument::getInstance();
        $settings = $this->getSettings();

        $document->addScriptDeclaration('MicrodataDialog.settings=' . json_encode($settings) . ';');

        $tabs = WFTabs::getInstance(array(
            'base_path' => WF_EDITOR_PLUGIN,
        ));

        // Add tabs
        $tabs->addTab('microdata', 1);

        // add link stylesheet
        $document->addStyleSheet(array('microdata'), 'plugins');
        // add link scripts last
        $document->addScript(array('microdata'), 'plugins');
    }

    public function getSettings($settings = array())
    {
        return parent::getSettings($settings);
    }

    public function getPropertyList($type)
    {
        $schema = $this->getSchema('types');

        if (isset($schema->types->$type)) {
            return $schema->types->$type;
        }

        return false;
    }

    /**
     * array_merge_recursive does indeed merge arrays, but it converts values with duplicate
     * keys to arrays rather than overwriting the value in the first array with the duplicate
     * value in the second array, as array_merge does. I.e., with array_merge_recursive,
     * this happens (documented behavior):.
     *
     * array_merge_recursive(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('org value', 'new value'));
     *
     * array_merge_recursive_distinct does not change the datatypes of the values in the arrays.
     * Matching keys' values in the second array overwrite those in the first array, as is the
     * case with array_merge, i.e.:
     *
     * array_merge_recursive_distinct(array('key' => 'org value'), array('key' => 'new value'));
     *     => array('key' => array('new value'));
     *
     * Parameters are passed by reference, though only for performance reasons. They're not
     * altered by this function.
     *
     * @param array $array1
     * @param array $array2
     *
     * @return array
     *
     * @author Daniel <daniel (at) danielsmedegaardbuus (dot) dk>
     * @author Gabriel Sobrinho <gabriel (dot) sobrinho (at) gmail (dot) com>
     */
    private static function array_merge_recursive_distinct(array &$array1, array &$array2)
    {
        $merged = $array1;

        foreach ($array2 as $key => &$value) {
            if (is_array($value) && isset($merged[$key]) && is_array($merged[$key])) {
                $merged[$key] = self::array_merge_recursive_distinct($merged[$key], $value);
            } else {
                $merged[$key] = $value;
            }
        }

        return $merged;
    }

    private static function buildList($nodes)
    {
        $data = array();

        foreach ($nodes as $node) {
            $value = str_replace('http://schema.org/', '', $node->getAttribute('resource'));

            if ($node->getAttribute('typeof') === 'rdfs:Class') {
                $subclass = array();
                $comment = '';

                foreach ($node->getElementsByTagName('a') as $item) {
                    if ($item->getAttribute('property') === 'rdfs:subClassOf') {
                        $subclass[] = str_replace('http://schema.org/', '', $item->nodeValue);
                    }
                }

                foreach ($node->getElementsByTagName('span') as $item) {
                    $prop = $item->getAttribute('property');

                    if ($prop === 'rdfs:comment') {
                        $comment = str_replace('http://schema.org/', '', $item->nodeValue);
                    }
                }

                $arr = array($value => array('resource' => $value, 'comment' => $comment, 'subClassOf' => $subclass, 'domainIncludes' => array(), 'rangeIncludes' => array()));
                $data = self::array_merge_recursive_distinct($data, $arr);
            }

            if ($node->getAttribute('typeof') === 'rdf:Property') {
                $comment = '';

                foreach ($node->getElementsByTagName('span') as $item) {
                    $prop = str_replace('http://schema.org/', '', $item->getAttribute('property'));

                    if ($prop === 'rdfs:comment') {
                        $comment = str_replace('http://schema.org/', '', $item->nodeValue);
                    }
                }

                foreach ($node->getElementsByTagName('a') as $item) {
                    $prop = str_replace('http://schema.org/', '', $item->getAttribute('property'));

                    if ($prop === 'domainIncludes' || $prop === 'rangeIncludes') {
                        $subclass = str_replace('http://schema.org/', '', $item->nodeValue);

                        if ($value) {
                            $entry = array('label' => $value, 'comment' => $comment);

                            $arr = array($subclass => array($prop => array($entry)));
                            $data = array_merge_recursive($data, $arr);
                        }
                    }
                }
            }
        }

        return $data;
    }

    private function getData()
    {
        $http = JHttpFactory::getHttp();

        try {
            $response = $http->get(self::$_url);
        } catch (\RuntimeException $e) {
            $response = null;
        }

        if ($response === null || $response->code !== 200) {
            return array('error' => JText::_('Unable to load schema - Invalid response from ' . self::$_url));
        }

        if (!empty($response->headers['Content-Encoding']) && $response->headers['Content-Encoding'] === 'gzip') {
            return gzdecode($response->body);
        }

        return $response->body;
    }

    public function getSchema()
    {
        jimport('joomla.filesystem.file');

        if (empty(self::$_schema)) {
            // create cache file path
            $cache = JPATH_SITE . '/cache/com_jce/' . md5(self::$_url) . '.json';

            // get refresh time
            $ttl = (int) $this->getParam('cache_ttl', 7);

            // load data from cache file
            if (JFile::exists($cache)) {
                $data = JFile::read($cache);
                self::$_schema = json_decode($data);
            }

            // cehck for valid, existing file
            if (empty(self::$_schema) || !$ttl || (JFile::exists($cache) && filemtime($cache) >= strtotime($ttl . ' days ago'))) {
                $html = $this->getData();

                // result should be string, otherwise an error
                if (is_array($html)) {
                    return $html;
                }

                // error getting data, return cache
                if (empty($html)) {
                    return self::$_schema;
                }

                $dom = new \DOMDocument();
                $dom->loadHTML($html);

                // dom is empty, return cache
                if (empty($dom)) {
                    return empty(self::$_schema) ? 'Schema data is empty' : self::$_schema;
                }

                $nodes = $dom->getElementsByTagName('div');

                // no div nodes, return cache
                if ($nodes->length === 0) {
                    return empty(self::$_schema) ? 'Invalid Schema data' : self::$_schema;
                }

                $data = self::buildList($nodes);

                // no new list created, return cache
                if (empty($data)) {
                    return empty(self::$_schema) ? 'Unable to build schema list' : self::$_schema;
                }

                // set schema and write to cache
                self::$_schema = $data;

                JFile::write($cache, json_encode($data));
            }
        }

        return self::$_schema;
    }

    public function getTypeList()
    {
        $schema = $this->getSchema();

        $options = array();

        foreach ($schema as $key => $value) {
            $options[] = array('key' => $key, 'value' => $value->resource);
        }

        return $options;
    }
}
