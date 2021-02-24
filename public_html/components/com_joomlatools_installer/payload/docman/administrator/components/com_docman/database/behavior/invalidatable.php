<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

/**
 * Creates a cache namespace per table and automatically invalidates it after every non-safe operation
 */
class ComDocmanDatabaseBehaviorInvalidatable extends KDatabaseBehaviorAbstract
{
    protected $_namespace;

    public function __construct(KObjectConfig $config)
    {
        parent::__construct($config);

        $this->setNamespace($config->namespace);
    }


    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'namespace' => null
        ));

        parent::_initialize($config);
    }

    /**
     * Returns a cache object specific for the table
     *
     * @param array $options
     * @return JCache
     */
    public function getCache(array $options = array())
    {
        if (empty($this->_namespace)) {
            $identifier = $this->getMixer()->getIdentifier();
            $name       = KStringInflector::pluralize($identifier->name);
            $this->setNamespace(sprintf('com_%s.%s', $identifier->package, $name));
        }

        $options = array_merge(array(
            'type'          => 'output',
            'caching' 		=> JDEBUG ? defined('DOCMAN_FORCE_CACHE') : true,
            'defaultgroup'  => $this->getNamespace(),
            'lifetime' 		=> 60*24*7,
            // force the path to site in both applications
            'cachebase' 	=> strpos(JPATH_CACHE, 'administrator/') === false ? JPATH_CACHE : JPATH_SITE.'/cache',
            'storage'		=> JFactory::getApplication()->getCfg('cache_handler', 'file')
        ), $options);

        $type = $options['type'];

        unset($options['type']);

        return JCache::getInstance($type, $options);
    }

    public function cleanCache()
    {
        $this->getCache()->clean($this->getNamespace());
    }

    protected function _afterDelete(KDatabaseContextInterface $context)
    {
        $this->cleanCache();
    }

    protected function _afterInsert(KDatabaseContextInterface $context)
    {
        $this->cleanCache();
    }

    protected function _afterUpdate(KDatabaseContextInterface $context)
    {
        $this->cleanCache();
    }

    /**
     * @return mixed
     */
    public function getNamespace()
    {
        return $this->_namespace;
    }

    /**
     * @param string $namespace
     * @return ComDocmanDatabaseBehaviorInvalidatable
     */
    public function setNamespace($namespace)
    {
        $this->_namespace = $namespace;

        return $this;
    }
}