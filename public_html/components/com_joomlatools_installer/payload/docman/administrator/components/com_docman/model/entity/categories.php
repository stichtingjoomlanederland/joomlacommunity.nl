<?php
/**
 * @package     DOCman
 * @copyright   Copyright (C) 2011 Timble CVBA. (http://www.timble.net)
 * @license     GNU GPLv3 <http://www.gnu.org/licenses/gpl.html>
 * @link        http://www.joomlatools.com
 */

class ComDocmanModelEntityCategories extends KModelEntityRowset
{
    /**
     * Used in mapping total document-counts of parent categories
     *
     * @var array
     */
    static $_parents = array();

    protected function _getCountQuery()
    {
        $ids = array();

        foreach ($this as $entity) {
            $ids[] = $entity->id;
        }

        return $this->getObject('lib:database.query.select')
            ->columns(array('count' => 'COUNT(*)'))
            ->table('docman_documents')
            ->where('docman_category_id IN :id')
            ->bind(array('id' => $ids));
    }

    public function setDocumentCount($hierarchy = true)
    {
        $map = count($this) ? $this->getDocumentCountMap() : array();

        foreach ($this as $category)
        {
            $category->document_count = isset($map[$category->id]) ? $map[$category->id]->count : 0;

            if ($hierarchy)
            {
                // Keep track of document count for each sub-category
                self::$_parents[$category->id] = $category;

                // Track infinite level of sub-categories.
                $paths = explode('/', $category->path);
                array_pop($paths);

                if ($paths)
                {
                    foreach($paths as $id) {
                        // Increment document count in the parent category with each of its sub-categories
                        self::$_parents[$id]->document_count += $category->document_count;
                    }
                }
            }
        }

        return $this;
    }

    public function getDocumentCountMap()
    {
        $table = $this->getTable();
        $query = $this->_getCountQuery()->columns(array('docman_category_id'))->group('docman_category_id');

        return $table->getAdapter()->select($query, KDatabase::FETCH_OBJECT_LIST, 'docman_category_id');
    }

    /**
     * Returns the total number of documents in all categories and their children
     *
     * @return int Document count
     */
    public function countDocuments()
    {
        $ids = array();

        foreach ($this as $entity)
        {
            $descendants = $entity->getDescendants();
            $ids[] = $entity->id;

            foreach ($descendants as $descendant) {
                $ids[] = $descendant->id;
            }
        }

        $ids = array_unique($ids);

        if ($ids)
        {
            $table = $this->getTable();
            $query = $this->_getCountQuery()->bind(array('id' => $ids));
            $count = $table->getAdapter()->select($query, KDatabase::FETCH_FIELD);
        }
        else $count = 0;

        return $count;
    }
}
