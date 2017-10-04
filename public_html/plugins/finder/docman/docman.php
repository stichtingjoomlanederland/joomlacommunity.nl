<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

defined('_JEXEC') or die;

JLoader::register('PlgKoowaFinder', JPATH_ROOT.'/libraries/joomlatools/plugins/koowa/finder.php');

/**
 * Joomla smart search plugin for DOCman
 */
class plgFinderDocman extends PlgKoowaFinder
{
    /**
     * Initializes the default configuration for the object
     *
     * Called from {@link __construct()} as a first step of object instantiation.
     *
     * @param   KObjectConfig $config Configuration options
     * @return  void
     */
    protected function _initialize(KObjectConfig $config)
    {
        $config->append(array(
            'context'  => 'DOCman',
            'entity'   => 'document',
            'instructions' => array(
                FinderIndexer::TEXT_CONTEXT => array('contents'),
                FinderIndexer::META_CONTEXT => array('storage_path')
            )
        ));

        parent::_initialize($config);
    }

    public function onFinderAfterSave($context, $entity, $isNew)
    {
        // Some hosts cannot handle the default value of 30000 during index
        FinderIndexer::getState()->set('memory_table_limit', 5000);

        if ($context === $this->extension.'.category') {
            return $this->reindexCategory($entity);
        }

        return parent::onFinderAfterSave($context, $entity, $isNew);
    }

    protected function reindexCategory($category)
    {
        $db    = JFactory::getDbo();
        $query = 'SELECT docman_document_id, access, enabled FROM #__docman_documents WHERE docman_category_id = %d';

        $db->setQuery(sprintf($query, $category->id));
        $items = $db->loadObjectList();

        $access_changed = $category->old_access !== $category->access;
        $state_changed  = $category->old_enabled !== $category->enabled;

        $limit = ini_get('memory_limit');
        if ($limit != '-1') {
            $limit = $this->convertToBytes($limit);
        }

        foreach ($items as $item)
        {
            // Leave at least 5 MBs of memory
            if ($limit != '-1' && ($limit - memory_get_usage() < 5*1048576)) {
                break;
            }

            if ($access_changed) {
                $this->change($item->docman_document_id, 'access', max($item->access, $category->access));
            }

            if ($state_changed) {
                $this->change($item->docman_document_id, 'state', min($item->enabled, $category->enabled));
            }
        }

        return true;
    }

    /**
     * Returns the model
     *
     * @return KModelAbstract
     */
    protected function getModel()
    {
        $model = parent::getModel();

        $model->page('all');

        return $model;
    }

    /**
     * Turns an entity into a finder item
     *
     * @param KModelEntityInterface $entity
     * @return object
     */
    protected function getFinderItem(KModelEntityInterface $entity)
    {
        if (!$entity->itemid) {
            $this->getModel()->setPage($entity);
        }

        $item = parent::getFinderItem($entity);

        // Add language
        if ($entity->itemid)
        {
            $menu = KObjectManager::getInstance()->getObject('com://admin/docman.model.pages')
                ->language('all')->id($entity->itemid)->fetch();

            if ($menu->language) {
                $item->language = $menu->language;
            }
        }

        // Add the category taxonomy data.
        if (!empty($item->category_title))
        {
            $category_state  = isset($item->category_enabled) ? $item->category_enabled : 1;
            $category_access = isset($item->category_access) ? $item->category_access   : 1;

            $item->state  = min($item->enabled, $category_state);
            $item->access = max($item->access,  $category_access);

            $item->addTaxonomy('Category', $item->category_title, $category_state, $category_access);
        }

        // Tokenizing 40000 characters seem to take around 10 seconds in Finder
        if (strlen($entity->contents) > 40000) {
            $item->contents = substr($entity->contents, 0, 40000);
        } else {
            $item->contents = $entity->contents;
        }


        return $item;
	}

    /**
     * Returns a link to a row
     *
     * @param KModelEntityInterface $entity
     * @return string
     */
    protected function getLink(KModelEntityInterface $entity)
    {
        $template = 'index.php?option=com_docman&view=document&alias=%s&category_slug=%s&Itemid=%d';

        return sprintf($template, $entity->alias, $entity->category_slug, $entity->itemid);
    }

    protected function convertToBytes($value)
    {
        $keys = array('k', 'm', 'g');
        $last_char = strtolower(substr($value, -1));
        $value = (int) $value;

        if (in_array($last_char, $keys)) {
            $value *= pow(1024, array_search($last_char, $keys)+1);
        }

        return $value;
    }
}
